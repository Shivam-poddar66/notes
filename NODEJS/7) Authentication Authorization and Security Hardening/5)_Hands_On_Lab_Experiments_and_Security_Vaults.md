# 5) Hands-On Lab Experiments and Security Vaults

This chapter provides four production-grade security implementations demonstrating dual-token JWT authentication with reuse detection, SSRF-safe outbound fetchers, fine-grained ABAC authorization, and hardened edge gateways.

---

## Lab 1: JWT Dual-Token Authentication with Family Rotation & Reuse Detection

```typescript
import jwt from 'jsonwebtoken';
import crypto from 'node:crypto';
import { redis } from './redis';

const JWT_SECRET = 'SUPER_SECRET_ACCESS_KEY';
const ACCESS_TOKEN_EXPIRY = '15m';

export interface UserPayload {
  userId: string;
  email: string;
  role: string;
}

export class AuthService {
  /**
   * Generates a new access token and a hashed refresh token stored in Redis.
   */
  public static async generateAuthTokens(user: UserPayload) {
    // 1. Generate short-lived JWT access token
    const accessToken = jwt.sign(
      { sub: user.userId, email: user.email, role: user.role },
      JWT_SECRET,
      { expiresIn: ACCESS_TOKEN_EXPIRY }
    );

    // 2. Generate opaque 64-character refresh token
    const rawRefreshToken = crypto.randomBytes(32).toString('hex');
    const tokenHash = crypto.createHash('sha256').update(rawRefreshToken).digest('hex');

    // 3. Store in user's active token set in Redis
    const userTokensKey = `auth:user:${user.userId}:tokens`;
    await redis.sadd(userTokensKey, tokenHash);
    await redis.expire(userTokensKey, 30 * 24 * 60 * 60); // 30 Days

    return { accessToken, refreshToken: rawRefreshToken };
  }

  /**
   * Rotates refresh token and invalidates all sessions on reuse detection.
   */
  public static async refreshSession(userId: string, presentedRefreshToken: string, user: UserPayload) {
    const tokenHash = crypto.createHash('sha256').update(presentedRefreshToken).digest('hex');
    const userTokensKey = `auth:user:${userId}:tokens`;

    const isValid = await redis.sismember(userTokensKey, tokenHash);

    if (!isValid) {
      // SECURITY INCIDENT: Token was already rotated or revoked!
      console.error(`[SECURITY BREACH] Token reuse detected for user ${userId}. Revoking ALL tokens.`);
      await redis.del(userTokensKey); // Invalidate all active user sessions
      throw new Error('Unauthorized: Session compromised');
    }

    // Invalidate the presented token
    await redis.srem(userTokensKey, tokenHash);

    // Issue a brand new token pair
    return await this.generateAuthTokens(user);
  }
}
```

---

## Lab 2: Bulletproof SSRF-Safe Webhook Dispatcher

```typescript
import dns from 'node:dns/promises';
import ipaddr from 'ipaddr.js';

export async function executeSsrfSafeWebhook(
  targetUrl: string,
  payload: Record<string, any>
): Promise<Response> {
  const parsed = new URL(targetUrl);

  if (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') {
    throw new Error('Forbidden protocol');
  }

  // 1. Resolve hostname to all associated IPs
  const resolvedIps = await dns.lookup(parsed.hostname, { all: true });

  for (const { address } of resolvedIps) {
    const ip = ipaddr.parse(address);
    const range = ip.range();

    const blockedRanges = [
      'loopback',
      'private',
      'linkLocal',
      'carrierGradeNat',
      'uniqueLocal',
    ];

    if (blockedRanges.includes(range)) {
      throw new Error(`SSRF Blocked: Destination IP ${address} is in private range ${range}`);
    }
  }

  // 2. Perform outbound request with strict timeout and redirect restrictions
  return await fetch(targetUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
    redirect: 'error', // Block automatic redirects to prevent DNS rebinding attacks
    signal: AbortSignal.timeout(5000), // 5-second timeout
  });
}
```

---

## Lab 3: CASL Attribute-Based Access Control (ABAC) Middleware

```typescript
import { Request, Response, NextFunction } from 'express';
import { defineAbilityForUser, Article } from './casl-definitions';

// Middleware generator for checking abilities
export function checkAbility(action: 'read' | 'create' | 'update' | 'delete', subjectName: string) {
  return async (req: Request, res: Response, next: NextFunction) => {
    const user = req.user;
    if (!user) return res.status(401).json({ error: 'Unauthenticated' });

    const ability = defineAbilityForUser(user);

    // If request contains an ID, fetch target resource
    if (req.params.id) {
      const resource = await db.findResource(subjectName, req.params.id);
      if (!resource) return res.status(404).json({ error: 'Resource not found' });

      if (ability.cannot(action, resource)) {
        return res.status(403).json({ error: 'Forbidden: Insufficient permissions on resource' });
      }

      req.resource = resource;
    } else {
      // General entity check
      if (ability.cannot(action, subjectName as any)) {
        return res.status(403).json({ error: 'Forbidden: Insufficient permissions' });
      }
    }

    next();
  };
}
```

---

## Lab 4: Production Hardened Gateway with Rate Limiting & Helmet

```typescript
import express from 'express';
import helmet from 'helmet';
import cors from 'cors';
import { RateLimiterRedis } from 'rate-limiter-flexible';
import { redis } from './redis';

const app = express();

// 1. Security Headers
app.use(helmet());

// 2. Strict CORS
app.use(
  cors({
    origin: ['https://dashboard.example.com'],
    credentials: true,
  })
);

// 3. Body Parsing Limit (Mitigates payload flood DoS)
app.use(express.json({ limit: '100kb' }));

// 4. Redis-Backed Rate Limiting (100 requests per 15 min per IP)
const gatewayLimiter = new RateLimiterRedis({
  storeClient: redis,
  keyPrefix: 'gw_limit',
  points: 100,
  duration: 900,
});

app.use(async (req, res, next) => {
  try {
    await gatewayLimiter.consume(req.ip || '127.0.0.1');
    next();
  } catch (err: any) {
    res.status(429).json({ error: 'Rate limit exceeded. Please try again later.' });
  }
});

// 5. Health Check Endpoint
app.get('/health', (req, res) => res.json({ status: 'HEALTHY' }));

export default app;
```

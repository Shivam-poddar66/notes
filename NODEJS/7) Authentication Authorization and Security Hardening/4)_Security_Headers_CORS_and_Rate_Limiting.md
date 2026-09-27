# 4) Security Headers, CORS, and Rate Limiting

## Executive Overview
Web security does not stop at application code. A defense-in-depth architecture must enforce **HTTP Security Headers** to instruct browser security sandboxes, configure **CORS policies** to prevent unauthorized cross-origin API abuse, and enforce **Distributed Rate Limiting** to mitigate brute-force attacks and Denial of Service (DoS).

---

## 1. HTTP Security Headers with Helmet

The `helmet` middleware suite sets 15+ standardized HTTP headers to harden browser-side protections:

```typescript
import helmet from 'helmet';
import express from 'express';

const app = express();

app.use(
  helmet({
    // 1. Content Security Policy (CSP): Restricts origins for scripts, styles, and images
    contentSecurityPolicy: {
      directives: {
        defaultSrc: ["'self'"],
        scriptSrc: ["'self'", 'https://trusted-cdn.com'],
        objectSrc: ["'none'"],
        upgradeInsecureRequests: [],
      },
    },
    // 2. HTTP Strict Transport Security (HSTS): Forces HTTPS for 1 year, including subdomains
    hsts: {
      maxAge: 31536000,
      includeSubDomains: true,
      preload: true,
    },
    // 3. Clickjacking Protection: Disallows embedding inside <iframe>
    frameguard: { action: 'deny' },
    // 4. MIME-type Sniffing Protection
    noSniff: true,
    // 5. Referrer Leakage Prevention
    referrerPolicy: { policy: 'strict-origin-when-cross-origin' },
  })
);
```

---

## 2. CORS (Cross-Origin Resource Sharing) Security

CORS is an agreement between browsers and servers to allow cross-origin XMLHttpRequests/fetch calls.

```text
Browser Client (https://app.example.com)           API Server (https://api.example.com)
            │                                                      │
            ├── 1. Preflight OPTIONS /api/v1/data ────────────────>│
            │      Origin: https://app.example.com                 │
            │      Access-Control-Request-Method: POST             │
            │                                                      │
            │<── 2. Preflight Response ────────────────────────────┤
            │      Access-Control-Allow-Origin: https://app.example.com
            │      Access-Control-Allow-Methods: POST, GET         │
            │      Access-Control-Allow-Credentials: true          │
            │                                                      │
            ├── 3. Actual POST /api/v1/data ──────────────────────>│
```

> [!CAUTION]
> **The Wildcard Credential Vulnerability**:
> Never set `Access-Control-Allow-Origin: *` while simultaneously setting `Access-Control-Allow-Credentials: true`. Browsers will block this, and dynamically reflecting the requesting `Origin` without strict domain whitelisting allows any malicious website to steal authenticated user sessions!

### Production-Hardened CORS Configuration

```typescript
import cors from 'cors';

const ALLOWED_ORIGINS = new Set([
  'https://app.example.com',
  'https://admin.example.com',
]);

export const corsMiddleware = cors({
  origin: (requestOrigin, callback) => {
    // Allow non-browser requests (e.g. mobile apps, curl, server-to-server) where origin is undefined
    if (!requestOrigin || ALLOWED_ORIGINS.has(requestOrigin)) {
      callback(null, true);
    } else {
      callback(new Error(`CORS Error: Origin ${requestOrigin} not permitted by policy`));
    }
  },
  credentials: true, // Allow cookies and Authorization headers
  methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With', 'X-Request-Id'],
  maxAge: 86400, // Cache preflight response for 24 hours
});
```

---

## 3. Distributed Sliding-Window Rate Limiting

To prevent brute-force login attacks and API scraping, rate limiting must be shared across all Node.js cluster instances using Redis:

```typescript
import { RateLimiterRedis } from 'rate-limiter-flexible';
import { redis } from './redis';
import { Request, Response, NextFunction } from 'express';

// Configure Sliding Window Rate Limiter (e.g. Max 10 requests per 1-minute window per IP)
const rateLimiter = new RateLimiterRedis({
  storeClient: redis,
  keyPrefix: 'rate_limit',
  points: 10,        // Number of requests
  duration: 60,      // Per 60 seconds
  blockDuration: 300 // Block for 5 minutes if limit exceeded
});

export async function rateLimitMiddleware(req: Request, res: Response, next: NextFunction) {
  const clientIp = req.ip || req.socket.remoteAddress || 'unknown-ip';

  try {
    const rateLimitRes = await rateLimiter.consume(clientIp);

    // Set standard rate limit headers
    res.setHeader('X-RateLimit-Limit', 10);
    res.setHeader('X-RateLimit-Remaining', rateLimitRes.remainingPoints);
    res.setHeader('X-RateLimit-Reset', new Date(Date.now() + rateLimitRes.msBeforeNext).toISOString());

    next();
  } catch (rejRes: any) {
    const retryAfterSec = Math.round(rejRes.msBeforeNext / 1000) || 1;
    res.setHeader('Retry-After', retryAfterSec);
    res.status(429).json({
      error: 'Too Many Requests',
      message: `Rate limit exceeded. Please retry after ${retryAfterSec} seconds.`,
    });
  }
}
```

---

## 4. Software Supply Chain & Dependency Hardening

Vulnerabilities often enter Node.js codebases through third-party npm packages.

### Production Hardening Rules:
1. **Enforce Lockfile Integrity**: Always install in CI using `npm ci` or `pnpm install --frozen-lockfile`.
2. **Automated Vulnerability Scanning**: Run `npm audit --audit-level=high` in CI pipelines.
3. **Disable Install Scripts**: Prevent malicious packages from running install-time shell scripts:
   ```bash
   npm config set ignore-scripts true
   ```

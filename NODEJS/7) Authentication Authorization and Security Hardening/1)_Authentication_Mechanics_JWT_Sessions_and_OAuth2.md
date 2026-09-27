# 1) Authentication Mechanics: JWT, Sessions, and OAuth 2.0

## Executive Overview
Authentication verifies the identity of an incoming client or user. In enterprise Node.js architectures, systems choose between **Stateless Token Authentication (JWT)**, **Stateful Distributed Sessions (Redis)**, or delegated identity through **OAuth 2.0 / OpenID Connect (OIDC)**.

A secure authentication system requires enforcing short access token lifetimes, refresh token rotation with reuse detection, cryptographically secure password hashing via **`Argon2id`**, and defense against token theft.

---

## 1. Stateless JWT vs Stateful Session Architecture

```text
┌────────────────────────┬────────────────────────────────┬────────────────────────────────┐
│ Dimension              │ Stateless JWTs                 │ Stateful Sessions (Redis)      │
├────────────────────────┼────────────────────────────────┼────────────────────────────────┤
│ State Location         │ Client-side (Cryptographic Sig)│ Server-side (Redis In-Memory)  │
│ Database Lookups       │ Zero DB queries on verify      │ 1 Redis query per HTTP request │
│ Revocation / Logout    │ Difficult without blacklist DB │ Instant (Delete session key)   │
│ Payload Size           │ Large (Sent in every header)   │ Minimal (32-byte Session ID)   │
│ Scalability            │ Massively horizontally scalable│ Requires clustered Redis       │
└────────────────────────┴────────────────────────────────┴────────────────────────────────┘
```

---

## 2. Secure JWT Dual-Token Architecture

A production JWT architecture separates credentials into two distinct token types:
1. **Access Token**: Short-lived (5–15 minutes). Carries user identity and permissions. Signed with asymmetric cryptography (`RS256` or `ES256`).
2. **Refresh Token**: Long-lived (7–30 days). Opaque random 64-byte hex string stored hashed in Redis/DB. Used exclusively to obtain a new Access Token.

```text
Client                          Node.js Backend                      Redis / DB
  │                                    │                                  │
  ├─── 1. Login (User + Pass) ────────>│                                  │
  │<── 2. Return Access + Refresh ─────┼── Store Hashed Refresh Token ───>│
  │                                    │                                  │
  ├─── 3. API Call (Access Token) ────>│ (Verifies Signature Locally)     │
  │<── 4. API Response ────────────────│ (Zero Database Queries!)        │
  │                                    │                                  │
  │  [ Access Token Expires (10m) ]    │                                  │
  │                                    │                                  │
  ├─── 5. /auth/refresh (Refresh Token)>│                                  │
  │                                    ├── Validate Token & Rotate ──────>│
  │<── 6. New Access + New Refresh ────┼── Invalidate Old Refresh Token ──>│
```

### 2.1 Refresh Token Rotation & Reuse Detection (Theft Defense)
If an attacker steals a Refresh Token and uses it:
1. The server detects that an **already-used refresh token** was presented.
2. The server treats this as an active breach and **immediately invalidates the entire refresh token family** for that user, terminating all active sessions.

```typescript
import crypto from 'node:crypto';
import { redis } from './redis';

export class RefreshTokenManager {
  private static readonly FAMILY_TTL_SECONDS = 30 * 24 * 60 * 60; // 30 Days

  public static async rotateRefreshToken(
    userId: string,
    presentedToken: string
  ): Promise<{ newAccessToken: string; newRefreshToken: string }> {
    const tokenHash = crypto.createHash('sha256').update(presentedToken).digest('hex');
    const userTokensKey = `auth:refresh:${userId}`;

    // 1. Check if token exists in active set
    const isActive = await redis.sismember(userTokensKey, tokenHash);

    if (!isActive) {
      // SECURITY INCIDENT: Token was already rotated or stolen!
      console.error(`[SECURITY ALERT] Refresh token reuse detected for user ${userId}. Revoking all sessions!`);
      await redis.del(userTokensKey); // Invalidate entire family
      throw new Error('Security violation: Session compromised');
    }

    // 2. Invalidate used token
    await redis.srem(userTokensKey, tokenHash);

    // 3. Issue new token pair
    const newRefreshToken = crypto.randomBytes(32).toString('hex');
    const newHash = crypto.createHash('sha256').update(newRefreshToken).digest('hex');

    await redis.sadd(userTokensKey, newHash);
    await redis.expire(userTokensKey, this.FAMILY_TTL_SECONDS);

    const newAccessToken = 'JWT_GENERATED_ACCESS_TOKEN';

    return { newAccessToken, newRefreshToken };
  }
}
```

---

## 3. Stateful Cookie Security & Flags

When storing session identifiers or refresh tokens in browser cookies, all four security flags must be strictly enforced:

```typescript
import { Response } from 'express';

export function setSecureSessionCookie(res: Response, sessionId: string) {
  res.cookie('__Host-session', sessionId, {
    httpOnly: true,              // Blocks client-side JavaScript (document.cookie) to prevent XSS theft
    secure: true,                // Enforces transmission exclusively over HTTPS
    sameSite: 'strict',          // Blocks cookie from being sent on cross-site requests (mitigates CSRF)
    path: '/',                   // Cookie scoped to entire domain
    maxAge: 7 * 24 * 60 * 60 * 1000, // 7 Days in milliseconds
  });
}
```

---

## 4. Modern Password Hashing with Argon2id

> [!CAUTION]
> The OWASP foundation recommends **`Argon2id`** as the primary password hashing algorithm. Unlike SHA-256 or MD5, Argon2id is mathematically designed to be memory-hard and GPU-resistant.

```typescript
import argon2 from 'argon2';

export class PasswordSecurity {
  /**
   * Hashes a password using Argon2id with memory-hard parameters.
   */
  public static async hashPassword(password: string): Promise<string> {
    return await argon2.hash(password, {
      type: argon2.argon2id, // Hybrid memory-hard / side-channel resistant
      memoryCost: 65536,     // 64 MB RAM per hash operation
      timeCost: 3,           // 3 Iteration passes
      parallelism: 4,        // 4 Threads
    });
  }

  /**
   * Verifies password against stored Argon2id hash in constant time.
   */
  public static async verifyPassword(password: string, hash: string): Promise<boolean> {
    try {
      return await argon2.verify(hash, password);
    } catch {
      return false;
    }
  }
}
```

---

## 5. OAuth 2.0 with PKCE (Proof Key for Code Exchange)

In OAuth 2.0, the **Authorization Code Flow with PKCE** protects single-page apps and mobile clients against authorization code interception attacks:

```text
Client                              Auth Server (Google/GitHub)
  │                                              │
  ├── 1. Generate code_verifier (Random 64B)     │
  ├── 2. Compute code_challenge = SHA256(verifier)
  │                                              │
  ├── 3. Redirect to /authorize?code_challenge ──>
  │<── 4. User Authenticates & Returns Code ─────┤
  │                                              │
  ├── 5. POST /token (Code + code_verifier) ─────>
  │      (Auth server computes SHA256(verifier)  │
  │       and matches against code_challenge)    │
  │<── 6. Returns Access & ID Tokens ────────────┤
```

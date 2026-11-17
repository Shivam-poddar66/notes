# 2) Stateless Application Tier and Session Decoupling

## Executive Overview
A tier is stateless when it treats every incoming request as an independent transaction without retaining context from past interactions. Decoupling ephemeral user state from application compute is what makes elasticity and autoscaling possible.

---

## 1. Architectural Evolution: From Stateful to Decoupled

```text
EVOLUTION STAGE 1: Stateful Monolith (Local In-Memory Sessions)
[ Client ] ── Sticky Session ──▶ [ App Node 1 (Session HashMap in RAM) ]
* If Node 1 restarts or crashes, user is logged out and in-memory cart vanishes.

EVOLUTION STAGE 2: Database-Backed Sessions
[ Client ] ── Any Node ──▶ [ App Node 1/2 ] ──▶ [ SQL DB `sessions` table ]
* Eliminates sticky sessions, but floods the relational database with 10k read/write queries per second just to validate session tokens!

EVOLUTION STAGE 3: Distributed In-Memory Cache (Production Standard)
[ Client ] ── Any Node ──▶ [ Stateless App Node ] ── <1ms ──▶ [ Redis Sentinel / Cluster ]
* Blazing sub-millisecond session validation; handles millions of concurrent active users with zero load on relational DB.
```

---

## 2. Stateless Token Architecture: Signed & Encrypted JWTs

```text
┌────────────────────────────────────────────────────────┐
│                   JWT Structure                        │
├───────────────────┬────────────────────────────────────┤
│ Header            │ { "alg": "RS256", "typ": "JWT" }   │
├───────────────────┼────────────────────────────────────┤
│ Payload (Claims)  │ { "sub": "123", "role": "admin",   │
│                   │   "exp": 1773000000 }              │
├───────────────────┼────────────────────────────────────┤
│ Signature         │ RSASHA256(Base64(H) + "." +        │
│                   │           Base64(P), private_key)  │
└───────────────────┴────────────────────────────────────┘
```

### The Invalidation Dilemma of JWTs:
- **Advantage**: Purely stateless. Any backend server verifies the token signature using the public key in microseconds without touching Redis or the database.
- **Drawback**: **JWTs cannot be revoked immediately**. If a user's phone is stolen or an account is compromised, the token remains valid until `exp`.
- **Hybrid Solution**:
  1. Use short-lived Access Tokens (e.g., 10-minute expiry).
  2. Use long-lived Refresh Tokens stored in Redis.
  3. When revoking, delete the Refresh Token in Redis; within 10 minutes, the user is permanently locked out.

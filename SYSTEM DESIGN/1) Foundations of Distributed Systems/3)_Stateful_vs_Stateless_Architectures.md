# 3) Stateful vs. Stateless Architectures

## Executive Overview
The cornerstone of modern horizontal cloud autoscaling is **Statelessness** in the compute layer. Decoupling computational logic from ephemeral application state allows any machine to service any incoming request interchangeably.

---

## 1. Architectural Comparison

```text
STATEFUL TIER (Scaling Bottleneck):
[ User Alice ] ──────▶ [ Server 1 (Holds Alice's Session in RAM) ]
[ User Bob ]   ──────▶ [ Server 2 (Holds Bob's Session in RAM) ]

* Problem: If Server 1 crashes, Alice is logged out and her shopping cart is lost!
* Problem: Load Balancer must use "Sticky Sessions", creating uneven load distribution.

STATELESS TIER (Horizontally Scalable):
[ User Alice ] ───▶ [ Load Balancer ]
[ User Bob ]   ───▶ (Any algorithm: Round Robin / Least Conn)
                             │
            ┌────────────────┼────────────────┐
            ▼                ▼                ▼
     [ App Node 1 ]   [ App Node 2 ]   [ App Node 3 ]
            │                │                │
            └────────────────┼────────────────┘
                             │
            ┌────────────────┴────────────────┐
            ▼                                 ▼
   [ Distributed Cache ]              [ Database Cluster ]
      (Redis Cluster)                 (Postgres / MySQL)
```

| Dimension | Stateful Architecture | Stateless Architecture |
| :--- | :--- | :--- |
| **Session Storage** | Local process RAM or local file system | External shared store (Redis, DynamoDB) or JWT |
| **Routing Requirement**| Sticky sessions (IP Hash or Session Cookie affinity) | Any server can handle any request |
| **Horizontal Autoscaling**| Extremely hard; draining connections takes hours | Instantaneous; spin up/down containers on demand |
| **Failure Blast Radius** | Crashing server loses active user data | Crashing server drops zero state; request is retried |
| **Deployment Simplicity**| Risky deployments; requires state migration | Rolling updates / blue-green deployments trivial |

---

## 2. Converting a Stateful System into a Stateless System

### Step 1: Externalize User Sessions
- Move sessions from process memory (`express-session` memory store) to **Redis Sentinel / Cluster**.
- Alternative: Use self-contained, stateless **JWTs (JSON Web Tokens)** signed with asymmetric keys (RSA / EdDSA).
  - *Caveat*: JWTs cannot be revoked instantly without maintaining a shared blacklist (which reintroduces a Redis lookup).

### Step 2: Offload File Uploads & Ephemeral Disk Storage
- Never save uploaded user profile pictures, documents, or videos to local server paths (`/var/app/uploads/`).
- Use **Presigned URLs**:
  1. Client requests upload authorization: `POST /api/uploads/presign`.
  2. Server generates a signed S3 upload URL valid for 5 minutes.
  3. Client uploads binary payload directly to **Amazon S3 / Google Cloud Storage**.
  4. Server remains 100% free of binary file I/O.

### Step 3: Decouple Scheduled & Background Tasks
- In stateful apps, `node-cron` or background timers run inside the app process. If you scale to 5 instances, jobs run 5 times simultaneously!
- Move background jobs to dedicated worker processes backed by **RabbitMQ, Kafka, or Redis BullMQ**.

---

## 3. When is Stateful Architecture Legitimate?
Not all systems can be purely stateless:
1. **Real-Time Multiplayer Gaming**: Game state (physics, entity positions) must be calculated in-memory at 60–120 FPS.
2. **WebSocket & Collab Engines (Figma, Google Docs)**: Operational Transformation (OT) / CRDT engines maintain active document state in memory per room.
3. **Storage & Database Nodes**: The storage layer itself (Kafka brokers, Cassandra nodes, Redis instances) is fundamentally stateful and requires specialized replication management.

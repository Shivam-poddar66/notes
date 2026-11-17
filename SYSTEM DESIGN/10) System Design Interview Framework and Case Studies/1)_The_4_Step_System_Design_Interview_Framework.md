# 1) The 4-Step System Design Interview Framework

## Executive Overview
A System Design interview is a 45-minute open-ended collaborative architecture session. The interviewer is not evaluating if you memorized an architecture diagram; they are evaluating your structured problem-solving, trade-off communication, scope management, and distributed systems maturity.

---

## 1. Time Management Breakdown (45 Minutes)

```text
┌────────────────────────────────────────────────────────┬────────────┐
│ Step                                                   │ Duration   │
├────────────────────────────────────────────────────────┼────────────┤
│ Step 1: Understand the Problem & Scope the System      │ 3 – 5 min  │
│ Step 2: High-Level Architecture & API Design           │ 10 – 12 min│
│ Step 3: Detailed Component Deep Dive                   │ 15 – 20 min│
│ Step 4: Failure Scenarios, Bottlenecks & Wrap-Up       │ 5 – 8 min  │
└────────────────────────────────────────────────────────┴────────────┘
```

---

## 2. Step-by-Step Execution Protocol

### Step 1: Clarify Requirements & Scope (3–5 min)
- **Functional Requirements**: Limit the problem to the core 2 to 3 user-facing features (e.g., "User posts tweet; User sees timeline; User searches tweets"). Explicitly de-scope peripheral features (e.g., "We will assume direct messaging and ads are out of scope for today").
- **Non-Functional Requirements**:
  - High availability ($99.99\%$) vs. strong consistency?
  - Read-heavy or write-heavy workload?
  - Acceptable latency SLAs (e.g., < 100 ms for reads, < 500 ms for writes).
- **Back-of-the-Envelope Estimates**:
  - Compute DAU, Read/Write QPS, Peak QPS, 5-Year Storage, Network Bandwidth, and Cache RAM (80/20 rule).

### Step 2: High-Level Architecture & API Design (10–12 min)
- **Define API Contracts**:
  - `POST /api/v1/tweets` with parameters and responses.
  - `GET /api/v1/users/{id}/timeline` with pagination parameters.
- **Data Model & Database Selection**:
  - Draw database schema (entities, primary keys, relationships).
  - Justify SQL (relational ACID) vs. NoSQL (key-value, document, wide-column) based on access patterns.
- **Draw the High-Level Block Diagram**:
  - Client $\to$ CDN $\to$ Load Balancer $\to$ Stateless App Servers $\to$ Primary DB / Read Replicas + Redis Cache.

### Step 3: Detailed Component Deep Dive (15–20 min)
- Deep dive into the hardest technical problem of the specific question:
  - If URL Shortener $\to$ Dive into Base62 key generation, collisions, and KGS.
  - If Chat System $\to$ Dive into WebSockets, Redis Pub/Sub, and Cassandra partition keys.
  - If Flash Sale $\to$ Dive into Redis distributed locks, atomic Lua scripts, and Kafka queueing.
  - If Twitter Feed $\to$ Contrast Fan-out on Write vs. Fan-out on Read for celebrity accounts.

### Step 4: Failure Scenarios & Edge Cases (5–8 min)
- Demonstrate Senior/Staff engineering maturity by breaking your own system before the interviewer does:
  - Single Points of Failure (SPOF): What happens if the Primary DB crashes? How does failover work?
  - Network partitions and replication lag: What if replica reads are stale?
  - Thundering herd / cache stampede: What happens when the homepage cache key expires?
  - Rate limiting and DDoS protection.

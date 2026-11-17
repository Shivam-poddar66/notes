# 4) Fault Tolerance: Circuit Breakers and Bulkheads

## Executive Overview
In microservice architectures, failures are inevitable. Designing for fault tolerance means ensuring that a single failing service or third-party dependency cannot exhaust system resources or cause cascading failures across the entire ecosystem.

---

## 1. The Circuit Breaker Pattern (Michael Nygard)

When a remote service is failing or under severe load, repeatedly hammering it with requests guarantees its complete collapse while blocking caller threads. A circuit breaker intercepts outgoing network calls and fails fast.

```text
                  Incoming Remote RPC Calls
                              │
                              ▼
                       ┌──────────────┐
                       │    CLOSED    │ ◄─── (Normal operations: calls pass through)
                       └──────┬───────┘
                              │
                    [ Failure Rate > Threshold ]
                    (e.g., > 50% failures over 10s)
                              │
                              ▼
                       ┌──────────────┐
                       │     OPEN     │ ───▶ (Fail fast immediately; execute fallback)
                       └──────┬───────┘
                              │
                    [ Cooldown Sleep Window Expires ]
                    (e.g., after 30 seconds)
                              │
                              ▼
                       ┌──────────────┐
                       │  HALF-OPEN   │ ───▶ (Send limited canary trial requests)
                       └──────────────┘
                         │          │
                 [ Canaries Pass ]  [ Canaries Fail ]
                         │          │
                         ▼          ▼
                      [CLOSED]   [OPEN]
```

### Fallback Strategies when Breaker is Open:
1. **Stale Cache Fallback**: Return cached product information or catalog results from 10 minutes ago.
2. **Default / Mock Fallback**: Return an empty array of recommendations or a generic trending list.
3. **Queue for Later**: Push the task (e.g., email notification) into an asynchronous Dead Letter Queue for background retry.

---

## 2. The Bulkhead Pattern

Named after the watertight compartments in a ship's hull: If one compartment is breached and fills with water, the ship remains afloat because the flooding is contained.

```text
UNISOLATED (THREAD STARVATION):
[ All Requests ] ──▶ [ Shared 100-Thread Pool ] ──▶ Hangs waiting on Payment API!
                      * 100% of threads block; Catalog and User Profile requests also stall!

BULKHEAD ISOLATION:
[ Catalog Requests ] ──▶ [ Pool A: 50 Threads ] ──▶ Catalog DB (Fast, Healthy)
[ Payment Requests ] ──▶ [ Pool B: 20 Threads ] ──▶ Payment API (Fails isolated; max 20 threads blocked)
[ User Profile Req ] ──▶ [ Pool C: 30 Threads ] ──▶ User DB (Fast, Healthy)
```

- **Thread Pool Bulkheads**: Dedicate independent thread pools per outbound downstream service (e.g., Netflix Hystrix / Resilience4j).
- **Semaphore Bulkheads**: Limit concurrent in-flight calls via counting semaphores without creating extra threads.
- **Connection Pool Bulkheads**: Separate database connection pools for read queries vs write transactions.

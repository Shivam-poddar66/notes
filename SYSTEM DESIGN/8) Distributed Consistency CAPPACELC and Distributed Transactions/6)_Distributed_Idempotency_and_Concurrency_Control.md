# 6) Distributed Idempotency and Concurrency Control

## Executive Overview
In distributed networks, communication timeouts are fundamentally ambiguous: Did the remote server fail to process the request, or did it process successfully and the response packet was lost? **Idempotency Keys** and **Optimistic Concurrency Control (OCC)** guarantee safe retries and race-free state transitions.

---

## 1. Idempotency Key Architecture (Stripe Model)

An API call is **idempotent** if making the call multiple times produces the exact same side-effects as making it once.

```text
[ Client ]
   │
   │ POST /v1/charges { amount: $100 }
   │ Header: Idempotency-Key: "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d"
   ▼
[ Payment Gateway Server ]
   │
   ├── 1. Check Redis: `GET idemp:9b1deb4d...`
   │
   ├── CASE A: Key already COMPLETED
   │   └── Return cached response immediately! (Zero extra charges)
   │
   ├── CASE B: Key is currently PROCESSING
   │   └── Return HTTP 409 Conflict ("Transaction in flight; retry later")
   │
   └── CASE C: Key does NOT exist
       ├── Set lock: `SET idemp:9b1deb4d... "PROCESSING" NX PX 60000`
       ├── Execute payment with card network
       ├── Save to Redis & DB: `SET idemp:9b1deb4d... "{status: 'SUCCESS', charge_id: 'ch_101'}"`
       └── Return HTTP 200 OK to client
```

---

## 2. Concurrency Control: Optimistic (OCC) vs. Pessimistic (2PL)

```text
PESSIMISTIC LOCKING (Two-Phase Locking - 2PL):
SELECT * FROM inventory WHERE item_id = 42 FOR UPDATE;
* Locks the row against all other readers and writers until transaction commits.
* Eliminates race conditions, but destroys throughput and creates deadlocks.

OPTIMISTIC CONCURRENCY CONTROL (OCC):
1. Read current stock and version:
   SELECT stock, version FROM inventory WHERE item_id = 42;
   -- Returns stock = 10, version = 5

2. Attempt update using version check:
   UPDATE inventory 
   SET stock = stock - 1, version = version + 1 
   WHERE item_id = 42 AND version = 5;

3. Check affected rows:
   - If rows_affected == 1: SUCCESS!
   - If rows_affected == 0: Another thread updated first! Roll back and RETRY.
```

- **When to use OCC**: High-read, low-contention workloads (standard e-commerce, user profiles).
- **When to use Pessimistic**: Ultra-high contention where retries would fail 90% of the time (e.g., flash sales of 10 items among 100,000 users; better yet, use Redis in-memory reservation).

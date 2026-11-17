# 4) Distributed Transactions: Two-Phase Commit and Its Flaws

## Executive Overview
When an atomic transaction must span multiple physically separated databases or microservices, traditional single-node ACID guarantees break. The classical distributed protocol is the **Two-Phase Commit (2PC)**.

---

## 1. Mechanics of Two-Phase Commit (2PC)

```text
PHASE 1: PREPARE PHASE (Can we commit?)
              [ Coordinator ]
                 │          │
   "Prepare" RPC │          │ "Prepare" RPC
                 ▼          ▼
           [ Shard A ]  [ Shard B ]
           (Locks rows,  (Locks rows,
            writes WAL,   writes WAL,
            votes YES)    votes YES)
                 │          │
                 └────┬─────┘
                      │ (All voted YES!)
                      ▼
PHASE 2: COMMIT PHASE (Do the commit!)
              [ Coordinator ]
                 │          │
    "Commit" RPC │          │ "Commit" RPC
                 ▼          ▼
           [ Shard A ]  [ Shard B ]
           (Commits &    (Commits &
            releases      releases
            row locks)    row locks)
```

---

## 2. Why 2PC Fails at High Scale

1. **Blocking Protocol (Lock Contention)**:
   - Rows remain locked throughout both Phase 1 and Phase 2 across all participating databases.
   - Any other transaction attempting to read or write those rows blocks and queues up, causing severe throughput collapse.
2. **Coordinator Single Point of Failure (SPOF)**:
   - If the Coordinator crashes *after* Shard A and Shard B vote "YES" but *before* sending the "Commit" message:
   - **The participant shards are left in limbo indefinitely**, holding database locks forever because they cannot decide on their own whether to commit or abort!
3. **High Latency Multiplication**:
   - Incurs multiple synchronous sequential network round-trips. If any participating database has a 200 ms latency spike, the entire transaction stalls.

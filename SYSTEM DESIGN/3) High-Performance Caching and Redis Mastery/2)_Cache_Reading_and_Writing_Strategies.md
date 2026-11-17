# 2) Cache Reading and Writing Strategies

## Executive Overview
Choosing how the application reads from and writes to the cache determines system consistency, write latency, and failure behavior.

---

## 1. Architectural Strategy Comparison

```text
CACHE-ASIDE (Lazy Loading):
[ App ] ── 1. Read Cache ──▶ [ Cache ]
   │                              │ (Miss)
   ├── 2. Query DB ──▶ [ DB ]     │
   └── 3. Update Cache ───────────┘

WRITE-THROUGH:
[ App ] ── 1. Write Data ──▶ [ Cache ] ── 2. Sync Write ──▶ [ DB ]
                                ▲                            │
                                └─── Confirms write only ────┘
                                     after both succeed

WRITE-BACK (Write-Behind):
[ App ] ── 1. Write Data ──▶ [ Cache ] ──▶ Returns Success Immediately!
                                │
                                ▼ (Async Batch Flush in background)
                             [ DB ]
```

| Strategy | Read Path | Write Path | Consistency | Use Cases |
| :--- | :--- | :--- | :--- | :--- |
| **Cache-Aside (Lazy)** | App checks Cache; on miss reads DB, populates Cache. | App writes to DB; invalidates or updates Cache. | Eventual consistency; risk of stale cache. | General web workloads, read-heavy APIs. |
| **Read-Through** | App requests Cache. Cache library fetches from DB on miss. | App delegates writes elsewhere. | Moderate; automated loading logic. | Standard ORMs with integrated caching layer. |
| **Write-Through** | Direct from Cache. | App writes to Cache; Cache synchronously writes to DB. | High consistency; no stale reads. | High data integrity systems (financial balances). |
| **Write-Back (Behind)** | Direct from Cache. | App writes to Cache only; Cache batches writes to DB asynchronously. | Low (Data loss if Cache crashes before DB flush). | Write-heavy telemetry, view counters, IoT. |
| **Refresh-Ahead** | Direct from Cache. | Background worker refreshes key before TTL expiration. | High hit rate; near-zero cache miss penalty. | Extremely hot keys, homepage feeds. |

---

## 2. Invalidation: Update Cache vs. Delete Key?
When data in the database changes, should you update the cache with the new value, or simply delete the key?

> **Senior Best Practice**: **DELETE the key from the cache (Cache Invalidation)**.
> - **Why?**
>   1. **Concurrency Race Conditions**: If Thread 1 and Thread 2 update the DB concurrently, network latency might cause Thread 1's cache update to arrive *after* Thread 2's cache update, leaving stale data permanently in the cache.
>   2. **Wasted Compute**: Updating the cache consumes CPU/memory immediately for data that might not be read again for hours. Deleting lets the next reader lazily recompute only if needed.

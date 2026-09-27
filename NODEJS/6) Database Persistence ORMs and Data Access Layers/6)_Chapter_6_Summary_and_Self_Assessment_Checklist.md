# 6) Chapter 6 Summary and Self-Assessment Checklist

## Chapter 6 Quick Revision Summary

### 1. Relational Databases & Pools
- **Connection Pools**: Maintain warm TCP connections to avoid expensive 3-way handshake and authentication costs. Sizing formula: `(Core Count * 2) + Spindles`.
- **Transactions & ACID**: Always wrap atomic operations in `BEGIN` / `COMMIT` / `ROLLBACK` blocks; always release clients back to the pool in a `finally` block.
- **Isolation Levels**: PostgreSQL defaults to `Read Committed`. Use `Repeatable Read` or `Serializable` when preventing non-repeatable reads or phantom reads is required.
- **Concurrency Control**: Use Optimistic Locking (`version` column) for low contention; use Pessimistic Locking (`SELECT ... FOR UPDATE`) for high contention financial / inventory operations.

### 2. Modern ORMs (Drizzle vs Prisma)
- **Drizzle ORM**: Pure TypeScript schema definitions, zero runtime engine overhead, SQL-like query builder that compiles to optimized native SQL.
- **Prisma**: Schema DSL with automatic TypeScript client generation, powered by a native Rust query engine.
- **Expand & Contract Pattern**: 4-phase zero-downtime database migration strategy for continuous deployment pipelines.

### 3. NoSQL & MongoDB
- **Aggregation Framework**: Declarative multi-stage pipelines (`$match` -> `$unwind` -> `$group` -> `$lookup` -> `$project` -> `$sort`).
- **ESR Indexing Rule**: Order compound index keys by Equality (`E`), then Sort (`S`), then Range (`R`).
- **Write Concerns & Read Preferences**: Use `w: 'majority'` and `j: true` for strong durability; use `secondaryPreferred` for read-heavy offloading.

### 4. Redis Caching & Queues
- **Cache Invalidation**: Cache-Aside is standard; prevent Cache Stampedes with Mutex locks or XFetch probabilistic early expiration; prevent Avalanches with TTL jitter.
- **Distributed Mutex**: Implement single-key Redis lock with atomic Lua script release.
- **Job Queues**: Use `BullMQ` for Redis-backed distributed jobs, concurrency management, and exponential backoff retries.

---

## Self-Assessment Challenge Questions

### Q1: Why does setting a database connection pool size to 500 often degrade PostgreSQL performance under load?
**Answer**: PostgreSQL uses a process-per-connection model. When 500 connections run queries concurrently on a 4-core CPU, the operating system spends more CPU cycles performing kernel context switches between processes and competing for disk I/O bandwidth than executing actual query plans. A smaller pool (e.g. 15–20) queues excess requests in Node.js memory and allows PostgreSQL to process queries sequentially at maximum disk/CPU efficiency.

### Q2: What is the difference between Optimistic Locking and Pessimistic Locking in database transactions?
**Answer**:
- **Optimistic Locking**: Reads rows without locking them. On update, it checks whether a `version` or `updated_at` column has changed (`WHERE id = $1 AND version = $2`). If 0 rows were updated, the application detects concurrent modification and retries. Best for read-heavy, low-conflict workloads.
- **Pessimistic Locking**: Immediately locks target rows using `SELECT ... FOR UPDATE` inside a transaction, blocking all other transactions from reading or modifying those rows until the transaction finishes. Best for high-contention operations like bank transfers and flash sales.

### Q3: How do you prevent deadlocks when locking multiple database rows concurrently?
**Answer**: Always acquire row locks in a **strict, deterministic alphabetical or numerical order** across all code paths. For example, if transferring funds between Account A and Account B, sort their IDs (`[idA, idB].sort()`) and lock the smaller ID first, followed by the larger ID. This ensures two concurrent transfers in opposite directions never block each other in a circular wait condition.

### Q4: Why is an atomic Lua script required when releasing a distributed lock in Redis?
**Answer**: Without Lua, releasing a lock requires two steps: `GET lockKey` to check the owner token, and `DEL lockKey` to delete it. If the lock's TTL expires between the `GET` and `DEL` calls and another worker acquires the lock, the first worker's `DEL` will delete the *second worker's* newly acquired lock! A Lua script runs atomically inside Redis's single thread, ensuring the key is deleted only if the token still matches.

### Q5: What is the ESR rule in MongoDB indexing, and why is the order critical?
**Answer**: The **ESR (Equality, Sort, Range)** rule defines the optimal order of fields in a compound index:
1. **Equality (`E`)**: Exact match fields filter the index tree down to the smallest subset.
2. **Sort (`S`)**: Ordering the matched keys directly from the index avoids an in-memory sort operation.
3. **Range (`R`)**: Range filters (`$gte`, `$lt`) scan the remaining keys without breaking index ordering for the sort.

---

## Chapter 6 Mastery Verification Checklist

Check off each item once you can explain or implement it with confidence:

- [ ] Configure and size a PostgreSQL connection pool (`node-postgres`) defensively.
- [ ] Execute multi-statement ACID transactions with automatic rollback on errors.
- [ ] Prevent SQL injection using parameterized queries and prepared statements.
- [ ] Differentiate between the 4 SQL Transaction Isolation Levels.
- [ ] Implement Optimistic Locking with version columns and Pessimistic Locking with `SELECT ... FOR UPDATE`.
- [ ] Prevent transaction deadlocks using deterministic ID sorting before acquiring row locks.
- [ ] Build type-safe schemas and relational queries in Drizzle ORM.
- [ ] Execute zero-downtime database migrations using the Expand-and-Contract pattern.
- [ ] Construct multi-stage MongoDB Aggregation Pipelines (`$match`, `$group`, `$lookup`, `$unwind`).
- [ ] Design high-performance compound MongoDB indexes according to the ESR rule.
- [ ] Configure MongoDB Write Concerns (`w: 'majority'`) and Read Preferences.
- [ ] Implement the Cache-Aside pattern in Redis with TTL jitter to prevent cache avalanches.
- [ ] Prevent Cache Stampedes using distributed mutex locking or probabilistic early expiration.
- [ ] Implement an atomic distributed lock in Redis using `SET NX PX` and an atomic Lua release script.
- [ ] Build high-concurrency background worker queues in Node.js using `BullMQ`.

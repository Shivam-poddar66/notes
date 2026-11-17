# 7) Cross-Shard Joins and Distributed Transactions

## Executive Overview
Once data is partitioned horizontally across network-isolated database instances, native SQL relational operations like `JOIN` and ACID multi-table transactions break.

---

## 1. The Cross-Shard JOIN Dilemma

Suppose `users` table is sharded on Server A and `orders` table is sharded on Server B:
```sql
SELECT u.name, o.total 
FROM users u 
JOIN orders o ON u.id = o.user_id 
WHERE u.country = 'USA';
```
The database engine cannot execute this join in memory because the data resides on physically separate physical machines separated by a network switch.

### Production Solutions:
1. **Co-Location (Table Affinity)**:
   - Shard related entities by the **exact same shard key**.
   - Example: Shard both `users` and `orders` by `user_id`. Alice's user profile and all of Alice's orders will physically reside on the exact same database shard node! The join runs locally in memory.
2. **Denormalization**:
   - Duplicate reference attributes directly into the child table:
     `orders: [ id, user_id, user_name, total ]`
   - Eliminates the need to join with `users` just to display the customer name.
3. **Application-Layer Scatter-Gather Joins**:
   - Query all shards in parallel from the application tier, fetch intermediate ID sets, and merge results in application heap memory.

---

## 2. Distributed SQL Middleware

Rather than managing shard routing manually in application code, modern architectures utilize distributed SQL layers:
- **Vitess** (MySQL): Handles query routing, connection pooling, and resharding (used by YouTube, Slack, GitHub).
- **Citus** (PostgreSQL): Distributed extension transforming Postgres into a sharded distributed database.
- **NewSQL Distributed Engines**: **CockroachDB, Google Cloud Spanner, TiDB, YugabyteDB**. Provide automatic range-based sharding, distributed transactions, and Raft consensus natively.

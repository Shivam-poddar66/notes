# 4) Sharding Strategies: Range, Hash, and Directory-Based

## Executive Overview
The **Shard Key** is the partition routing attribute. The strategy chosen to map shard keys to physical servers determines whether your cluster achieves uniform load distribution or suffers from devastating hot spots.

---

## 1. The Three Primary Sharding Strategies

```text
┌─────────────────┬─────────────────────────────┬─────────────────────────────┐
│ Strategy        │ Mechanism                   │ Major Trade-off             │
├─────────────────┼─────────────────────────────┼─────────────────────────────┤
│ 1. Range-Based  │ Split by contiguous ranges  │ Easy range queries;         │
│                 │ (e.g. IDs 1–1M, Dates)      │ SEVERE WRITE HOTSPOTS       │
├─────────────────┼─────────────────────────────┼─────────────────────────────┤
│ 2. Hash-Based   │ Shard = hash(Key) % N       │ Uniform write distribution; │
│                 │                             │ Range queries scatter-gather│
├─────────────────┼─────────────────────────────┼─────────────────────────────┤
│ 3. Directory /  │ Central lookup service maps │ Extreme flexibility;        │
│    Lookup       │ ID -> physical shard        │ Lookup service is SPOF/bottl│
└─────────────────┴─────────────────────────────┴─────────────────────────────┘
```

---

## 2. Detailed Strategy Analysis

### A. Range-Based Sharding
- Shard 1: Customer IDs 1 to 1,000,000.
- Shard 2: Customer IDs 1,000,001 to 2,000,000.
- **The Catastrophic Hotspot Trap**: If sharding by auto-incrementing ID or transaction date (`created_at`), **100% of all write operations hit the highest shard (current date)** while older shards sit completely idle!

### B. Hash-Based Sharding
- Hashes the shard key using a uniform hashing function (MurmurHash3):
  $$\text{Shard ID} = \text{MurmurHash3}(\text{user\_id}) \pmod{\text{Total Shards}}$$
- **Pros**: Spreads write traffic uniformly across all physical nodes.
- **Cons**: A query like `WHERE user_id BETWEEN 100 AND 200` cannot target a single shard. It must broadcast to **every shard in the cluster (Scatter-Gather)** and merge results in application memory.

### C. Directory-Based (Lookup Service)
- A separate database or distributed cache (Redis) stores a lookup mapping: `user_101 -> Shard_4`.
- **Pros**: Easy to rebalance individual heavy enterprise tenants to dedicated hardware without re-sharding the entire table.
- **Cons**: Every database query requires a preliminary network lookup round-trip to the directory service.

---

## 3. Shard Key Selection Criteria
1. **High Cardinality**: The attribute must have millions of distinct values (`user_id`, `account_id`, NOT `country` or `gender`).
2. **Even Write Distribution**: Avoid monotonically increasing keys.
3. **Query Pattern Alignment**: If 90% of your queries filter by `user_id`, make `user_id` the shard key so queries hit a single physical server.

# 8) Chapter 7 Summary & Self-Assessment Checklist

## Chapter Summary
1. **Replication Models**: Single-Leader routes writes to the Primary and scales reads via replicas. Semi-synchronous replication balances durability and latency. Leaderless systems rely on Quorum Consensus ($W + R > N$).
2. **Replication Lag**: Mitigated by pinning a user's reads to the Primary for a 5–10 second cooldown window following any write.
3. **Partitioning Vectors**: Vertical partitioning isolates heavy blob columns to optimize Buffer Pool efficiency. Horizontal sharding divides rows across nodes.
4. **Sharding Schemes**: Range sharding suffers from write hotspots; Hash sharding provides uniform writes but demands scatter-gather for range scans.
5. **Distributed IDs**: Twitter Snowflake packs timestamp, worker ID, and sequence counter into a 64-bit integer, generating millions of IDs/sec per node with zero coordination. UUIDv7 prevents B+ tree page splits by maintaining time ordering.
6. **Cross-Shard Complexities**: Mitigated through co-location by the same shard key, denormalization, or NewSQL distributed database engines.

---

## 🎯 Chapter 7 Self-Assessment Checklist

- [ ] Explain how Quorum Consensus ($W + R > N$) guarantees strong consistency using the Pigeonhole Principle.
- [ ] What is Read-Your-Own-Writes consistency and how do you implement it with an asynchronous read replica pool?
- [ ] Why does Range-Based sharding by date cause a write hotspot on the latest shard?
- [ ] How does Consistent Hashing minimize the amount of data moved when a new shard node is added?
- [ ] Detail the 4 components of a 64-bit Twitter Snowflake ID.
- [ ] Why does inserting random UUIDv4 primary keys degrade B+ tree index write throughput?
- [ ] What is Table Co-Location and how does it allow local joins in a sharded database?
- [ ] What are the 5 phases of a zero-downtime database live resharding migration?

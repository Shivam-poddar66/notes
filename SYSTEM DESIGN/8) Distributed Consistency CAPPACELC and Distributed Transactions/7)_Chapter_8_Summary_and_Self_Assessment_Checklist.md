# 7) Chapter 8 Summary & Self-Assessment Checklist

## Chapter Summary
1. **The CAP Reality**: Network partitions are unavoidable in physical hardware. Systems cannot choose "CA"; they must choose between **CP** (Consistency) and **AP** (Availability) under network failure.
2. **PACELC Theorem**: Extends CAP to normal operations: If Partitioned $\to$ A or C; Else $\to$ Latency (L) or Consistency (C).
3. **Consistency Spectrum**: Ranges from expensive Linearizable consistency (global real-time order) down to low-latency Eventual consistency.
4. **2PC Limitations**: Two-Phase Commit is blocking, holds row locks across network round-trips, and suffers from Coordinator single points of failure.
5. **The Saga Pattern**: Replaces 2PC in microservices by executing local ACID transactions linked by events, with compensating transactions to undo partial state changes on failure.
6. **Distributed Idempotency & OCC**: Idempotency keys protect against network retry duplicates in financial APIs; Optimistic Concurrency Control (OCC) uses version columns to eliminate pessimistic row locks.

---

## 🎯 Chapter 8 Self-Assessment Checklist

- [ ] Why is "CA" physically impossible under real-world network partitions?
- [ ] How does Cassandra classify under PACELC, and what trade-off does it make during normal operations?
- [ ] What is the difference between Linearizable consistency and Sequential consistency?
- [ ] Detail the failure scenario where a 2PC coordinator crashes and leaves participant nodes locked indefinitely.
- [ ] Explain how a Compensating Transaction works in the Saga Pattern.
- [ ] Compare Saga Orchestration versus Choreography: Which is preferred for complex multi-step workflows and why?
- [ ] How does Stripe implement Idempotency Keys using Redis to prevent double billing?
- [ ] Write the SQL query for Optimistic Concurrency Control using a `version` column and explain how conflicts are detected.

# 1) CAP Theorem: Consistency vs. Availability under Partitions

## Executive Overview
Formulated by Eric Brewer and proven by Gilbert & Lynch, the **CAP Theorem** proves that a distributed data store can guarantee at most **two out of three** properties simultaneously.

---

## 1. The Three CAP Properties

```text
┌─────────────────────────┬────────────────────────────────────────────────────────┐
│ Property                │ Formal Definition                                      │
├─────────────────────────┼────────────────────────────────────────────────────────┤
│ C: Consistency          │ Every read receives the most recent write or an error. │
│                         │ (All nodes see the identical data simultaneously).     │
├─────────────────────────┼────────────────────────────────────────────────────────┤
│ A: Availability         │ Every non-failing node returns a non-error response    │
│                         │ for every request (no guarantee it's the latest write).│
├─────────────────────────┼────────────────────────────────────────────────────────┤
│ P: Partition Tolerance  │ The system continues to operate despite arbitrary      │
│                         │ packet drops or network partitions between nodes.      │
└─────────────────────────┴────────────────────────────────────────────────────────┘
```

---

## 2. Why "CA" Does Not Exist in Real-World Distributed Systems

In physical computer networks, fiber cables are accidentally cut, routers reboot, and Wi-Fi frames drop. **Network Partitions ($P$) are physically unavoidable**.

```text
               NETWORK PARTITION OCCURS!
Node 1 (Region East)   <<<< [ NETWORK CUT ] >>>>   Node 2 (Region West)
Current Value: X=5                                 Current Value: X=5

Client A writes: X=10 ──▶ Node 1                    Client B reads ──▶ Node 2
```

When the network partition splits Node 1 and Node 2, you have only **two possible choices**:

### Choice 1: CP (Consistency over Availability):
- Node 2 knows it cannot contact Node 1. To prevent returning stale data ($X=5$ instead of $X=10$), Node 2 **refuses the request and returns an error**.
- The system preserves Consistency, but sacrifices Availability.
- Examples: **HBase, ZooKeeper, etcd, CockroachDB**.

### Choice 2: AP (Availability over Consistency):
- Node 2 accepts the read and returns $X=5$. Both nodes remain fully available, but Node 2 returns stale data.
- The system preserves Availability, but sacrifices Consistency.
- Examples: **Apache Cassandra, Amazon DynamoDB, CouchDB**.

> **Conclusion**: You never choose "between C, A, and P". You **must choose between CP and AP when a partition occurs**.

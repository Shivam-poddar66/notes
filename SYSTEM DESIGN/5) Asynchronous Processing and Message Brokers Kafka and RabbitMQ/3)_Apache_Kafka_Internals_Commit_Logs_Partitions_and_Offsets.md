# 3) Apache Kafka Internals: Commit Logs, Partitions, and Offsets

## Executive Overview
Apache Kafka is a horizontally scalable, fault-tolerant, append-only **distributed event streaming log**. Unlike traditional message brokers that track message state and delete acknowledged messages, Kafka acts as an immutable database of events.

---

## 1. Core Kafka Topology: Topics, Partitions, and Segments

```text
TOPIC: "payment-events" (Configured with 3 Partitions)

Partition 0: [ 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | ... ] ──▶ Broker 1 (Leader)
Partition 1: [ 0 | 1 | 2 | 3 | 4 | 5 | ... ]             ──▶ Broker 2 (Leader)
Partition 2: [ 0 | 1 | 2 | 3 | 4 | 5 | 6 | ... ]         ──▶ Broker 3 (Leader)
```

### Key Kafka Concepts:
1. **Topic**: A logical category or feed name to which records are published.
2. **Partition**:
   - The fundamental unit of **scalability and parallelism** in Kafka.
   - Each partition is an ordered, immutable sequence of records stored across physical disk segment files.
   - Partitions can be spread across different physical server brokers.
3. **Offset**:
   - A sequential, monotonically increasing 64-bit integer assigned to every message within a partition.
   - Messages are identified uniquely by: `(Topic, Partition, Offset)`.
4. **Partition Key & Ordering Rule**:
   - **Kafka guarantees strict ordering ONLY within a single partition**, never across the entire topic!
   - If a producer provides a key:
     $$\text{Partition} = \text{MurmurHash2}(\text{Key}) \pmod{\text{Total Partitions}}$$
   - All events with the same key (e.g., `user_id = 42`) land in the exact same partition, ensuring their processing order is strictly preserved.

---

## 2. Replication & In-Sync Replicas (ISR)

Every partition has **1 Leader** and $N-1$ **Followers** (where $N$ is the replication factor, typically 3):
- All producer writes and consumer reads go exclusively to the **Leader**.
- Followers replicate data from the Leader asynchronously.
- **ISR (In-Sync Replicas)**: The subset of followers that are caught up with the leader within `replica.lag.time.max.ms`.

### Producer Acknowledgment Configurations (`acks`):
- `acks=0`: Producer returns immediately without waiting for any confirmation. (Fastest, high risk of data loss).
- `acks=1`: Producer waits for Leader to write to its local log. (Moderate durability; data loss if Leader crashes before followers replicate).
- `acks=all` (or `-1`): Producer waits for the Leader AND all in-sync replicas (`min.insync.replicas=2`) to commit. **Guarantees zero data loss**.

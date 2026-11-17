# 5) Consumer Groups, Rebalancing, and Partition Assignment

## Executive Overview
Consumer Groups are the mechanism that allows a pool of distributed worker processes to divide and read a high-throughput Kafka topic in parallel.

---

## 1. The Fundamental Partition Rule

> **The Golden Law of Kafka Consumer Groups**: Each partition within a topic is assigned to **at most one consumer instance** within the same consumer group.

```text
TOPIC: 4 Partitions [P0, P1, P2, P3]

CASE 1: Equal Consumers (Ideal Parallelism)
Consumer Group A (4 Workers):
Worker 1 ──▶ P0
Worker 2 ──▶ P1
Worker 3 ──▶ P2
Worker 4 ──▶ P3

CASE 2: Fewer Consumers than Partitions (Load Shared)
Consumer Group B (2 Workers):
Worker 1 ──▶ P0, P1
Worker 2 ──▶ P2, P3

CASE 3: More Consumers than Partitions (Idle Workers!)
Consumer Group C (5 Workers):
Worker 1 ──▶ P0
Worker 2 ──▶ P1
Worker 3 ──▶ P2
Worker 4 ──▶ P3
Worker 5 ──▶ [ IDLE! ZERO WORKLOAD! ]
```

- **Scaling Rule**: To increase consumer parallelism from 4 to 8, you **must first increase the number of topic partitions to at least 8**.

---

## 2. Independent Consumer Groups (Pub/Sub Fan-Out)
Different Consumer Groups read from the same topic completely independently, maintaining their own separate offsets in the internal `__consumer_offsets` topic:

```text
                               [ Topic: "user-orders" ]
                                          │
                   ┌──────────────────────┴──────────────────────┐
                   ▼                                             ▼
        [ Consumer Group: "Billing" ]                 [ Consumer Group: "Analytics" ]
        - Tracks offset: 50,000                       - Tracks offset: 42,000 (Processing batch)
```

---

## 3. Consumer Rebalancing Protocols

A **Rebalance** occurs when a consumer joins the group, crashes, or partitions change.
1. **Eager Rebalancing (Stop-The-World)**:
   - All consumers stop reading, revoke all partition assignments, and wait for the Group Coordinator to reassign partitions from scratch. Causes noticeable latency spikes.
2. **Incremental Cooperative Rebalancing (Kafka 2.4+)**:
   - Consumers only revoke the specific partitions that need to be migrated to a new worker, while continuing to process their remaining partitions without interruption.

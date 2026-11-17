# 6) Delivery Semantics: At-Least-Once vs. Exactly-Once

## Executive Overview
When moving messages over unreliable physical networks with server crashes and retries, distributed message systems must define clear delivery guarantees.

---

## 1. The Three Delivery Semantics

```text
┌─────────────────────────┬───────────────────────────────┬─────────────────────────────┐
│ Semantic Level          │ Processing Flow               │ Trade-off / Risk            │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 1. At-Most-Once         │ Commit Offset ──▶ Process Msg │ Fast, but messages can be   │
│                         │                               │ lost if worker crashes      │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 2. At-Least-Once        │ Process Msg ──▶ Commit Offset │ Zero message loss, but can  │
│    (Industry Standard)  │                               │ duplicate if crash occurs   │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 3. Exactly-Once (EOS)   │ Idempotent Producer + Kafka   │ True exactly-once, but adds │
│                         │ Transactional API Coordination│ small coordination overhead │
└─────────────────────────┴───────────────────────────────┴─────────────────────────────┘
```

---

## 2. Why At-Least-Once Requires Idempotent Consumers

Under At-Least-Once processing:
1. Consumer reads message `Offset: 1045` ("Charge User \$50").
2. Consumer charges the user's credit card successfully.
3. Before the consumer can commit `Offset: 1045` back to Kafka, the worker's power cable is pulled!
4. Kafka detects the crash, triggers a rebalance, and assigns the partition to a new worker.
5. The new worker resumes reading from the last committed offset: `Offset: 1045`!
6. **Result**: If the consumer is not idempotent, the user is charged twice (\$100).

### Designing an Idempotent Consumer:
Use a unique message identifier (e.g., `event_id` or transaction UUID) stored with a unique constraint in the consumer's database:

```sql
INSERT INTO processed_events (event_id, processed_at) VALUES ('evt_abc123', NOW());
-- If a duplicate event arrives, the INSERT fails with a unique constraint violation,
-- and the consumer simply ignores the message and commits the offset.
```

---

## 3. Kafka's Native Exactly-Once Semantics (EOS)

Kafka achieved end-to-end Exactly-Once processing through two core features:

### A. Idempotent Producer (`enable.idempotence = true`):
- The Kafka broker assigns each producer instance a 64-bit **Producer ID (PID)**.
- Each message sent by the producer carries a monotonic sequence number ($0, 1, 2, \dots$).
- The broker records the highest sequence number per partition. If a network retry re-sends sequence number $5$, the broker acknowledges the write but **silently discards the duplicate record** from the disk log.

### B. Kafka Transactional API (Read-Process-Write Loop):
- Coordinates atomic multi-partition writes:
  ```java
  producer.initTransactions();
  producer.beginTransaction();
  // Send processed records to output topic
  producer.send(new ProducerRecord<>("output-topic", processedData));
  // Atomically commit consumer offsets in the same transaction!
  producer.sendOffsetsToTransaction(currentOffsets, consumerGroupId);
  producer.commitTransaction();
  ```
- Ensures that downstream topics and consumer offsets are committed **atomically together** or aborted entirely.

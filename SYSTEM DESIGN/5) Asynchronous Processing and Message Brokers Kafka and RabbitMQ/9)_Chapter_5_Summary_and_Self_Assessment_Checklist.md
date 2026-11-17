# 9) Chapter 5 Summary & Self-Assessment Checklist

## Chapter Summary
1. **Asynchronous Advantages**: Message brokers eliminate cascading latency, isolate service crashes, and level out traffic spikes through peak shaving.
2. **RabbitMQ Architecture**: AMQP exchanges (Direct, Fanout, Topic, Headers) provide flexible routing. Prefetch limits prevent consumer OOMs, and Dead Letter Queues isolate poison pills.
3. **Kafka Performance Physics**: Millions of messages per second are achieved through sequential disk appends, Linux page cache utilization, and Zero-Copy `sendfile()` DMA transfers.
4. **Partition Parallelism**: Partitions are Kafka's unit of scalability. Within a consumer group, each partition is consumed by at most one worker. Ordering is guaranteed strictly within a single partition.
5. **Delivery Guarantees**: At-least-once delivery is the industry standard and requires consumers to be idempotent. Exactly-once semantics are supported natively via idempotent producers and transactional APIs.
6. **Dual-Write Mitigation**: The Transactional Outbox pattern combined with Debezium Change Data Capture (CDC) eliminates dual-write race conditions.

---

## 🎯 Chapter 5 Self-Assessment Checklist

- [ ] Explain how asynchronous message queues provide "traffic peak shaving" during flash sales.
- [ ] What is the difference between RabbitMQ Direct, Fanout, and Topic exchanges?
- [ ] Why is Prefetch Count critical when consuming from RabbitMQ?
- [ ] How does Kafka achieve massive throughput on persistent disks without being CPU-bound?
- [ ] Explain how Linux OS `sendfile()` accomplishes Zero-Copy data transfers.
- [ ] What happens if a Kafka Consumer Group has 6 worker instances but the topic only has 4 partitions?
- [ ] Why does At-Least-Once message delivery demand that consumers be idempotent?
- [ ] How does Kafka's Idempotent Producer prevent duplicate messages during network retries?
- [ ] Detail the failure scenario of the Dual-Write problem and explain how the Transactional Outbox pattern fixes it.
- [ ] When would you architect a system with RabbitMQ instead of Apache Kafka?

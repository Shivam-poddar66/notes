# 8) Kafka vs. RabbitMQ: Architectural Decision Matrix

## Executive Overview
Choosing between Apache Kafka and RabbitMQ is one of the most common dilemmas in distributed system design. They were engineered for fundamentally different access patterns.

---

## 1. Comprehensive Architectural Comparison

```text
┌─────────────────────────┬───────────────────────────────┬─────────────────────────────┐
│ Architectural Dimension │ RabbitMQ                      │ Apache Kafka                │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ Core Paradigm           │ Smart Broker, Dumb Consumer   │ Dumb Broker, Smart Consumer │
│ Storage Engine          │ In-memory queues; transient   │ Append-only commit log on   │
│                         │ disk paging                   │ persistent disk segments    │
│ Consumption Model       │ Push (Broker pushes to worker)│ Pull (Consumer polls log)   │
│ Message Deletion        │ Deleted immediately upon ACK  │ Retained by time policy     │
│                         │                               │ (e.g. 7 days or forever)    │
│ Replayability           │ IMPOSSIBLE (once ACKed, gone) │ TRIVIAL (rewind offset)     │
│ Max Throughput          │ ~20,000 – 50,000 msgs/sec     │ 1,000,000+ msgs/sec         │
│ Message Ordering        │ FIFO in single queue; broken  │ Strict FIFO guaranteed      │
│                         │ by parallel consumer workers  │ within each partition       │
│ Routing Capabilities    │ Complex (Direct, Fanout,      │ Simple key-based partition  │
│                         │ Topic wildcards, Headers)     │ hashing                     │
│ Primary Protocol        │ AMQP 0-9-1, MQTT, STOMP       │ Custom binary TCP protocol  │
└─────────────────────────┴───────────────────────────────┴─────────────────────────────┘
```

---

## 2. Decision Tree: Which Broker Should You Choose?

```text
                               What is your primary workload?
                                              │
              ┌───────────────────────────────┴───────────────────────────────┐
              ▼                                                               ▼
   [ Complex Task Routing / RPC ]                                 [ High-Throughput Event Stream ]
   - Need priority queues                                         - Need to retain events for days/months
   - Granular routing (wildcards)                                 - Need to replay historical event streams
   - Ephemeral job execution                                      - Clickstream, analytics, CDC pipelines
   - Throughput < 50k msgs/sec                                    - Throughput > 100k msgs/sec
              │                                                               │
              ▼                                                               ▼
     [ CHOOSE RABBITMQ ]                                             [ CHOOSE APACHE KAFKA ]
```

### When to Pick RabbitMQ:
- Microservice background task processing (e.g., resize an image, convert video, send PDF report).
- Priority message handling (urgent customer transactions processed ahead of batch jobs).
- Complex routing trees where different services receive specific subsets of notifications.

### When to Pick Apache Kafka:
- Event-driven architecture with event sourcing and CQRS read models.
- Real-time stream processing with Apache Flink, Spark Streaming, or Kafka Streams.
- Audit logs, metrics ingestion, and Change Data Capture (CDC) from primary databases.
- Scenarios where new microservices deployed months later need to replay historical data from day one.

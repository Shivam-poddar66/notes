# 2) RabbitMQ Internals: AMQP, Exchanges, Queues, and Bindings

## Executive Overview
RabbitMQ is a feature-rich, high-reliability message broker implementing the **AMQP 0-9-1 (Advanced Message Queuing Protocol)** standard. It acts as a "Smart Broker" that handles complex routing logic and message lifecycle tracking.

---

## 1. Core AMQP Architectural Entities

```text
[ Producer ] ── (Publish with Routing Key) ──▶ [ Exchange ]
                                                    │
                               ┌────────────────────┴────────────────────┐
                        Binding Key: `order.*`            Binding Key: `audit.#`
                               ▼                                         ▼
                         [ Queue A ]                                [ Queue B ]
                               │                                         │
                             (Push)                                    (Push)
                               ▼                                         ▼
                         [ Consumer 1 ]                            [ Consumer 2 ]
```

### The 4 Standard Exchange Types:
1. **Direct Exchange**:
   - Compares the message **Routing Key** directly against queue **Binding Keys** for an exact match.
   - Example: Message with routing key `pdf` routes exclusively to queues bound with `pdf`.
2. **Fanout Exchange**:
   - Completely ignores routing keys. Duplicates and broadcasts incoming messages to **every queue bound to it**.
   - Ideal for pub/sub broadcast notifications.
3. **Topic Exchange (Pattern Matching)**:
   - Evaluates routing keys containing dot-delimited words against binding patterns:
     - `*` (asterisk): Matches **exactly one word**.
     - `#` (hash): Matches **zero or more words**.
   - Example: A message with routing key `eu.orders.created` matches `*.orders.*` and `eu.#`.
4. **Headers Exchange**:
   - Routes messages based on arbitrary key-value pairs in the message headers table rather than the routing key string.

---

## 2. Consumer Flow Control & Dead Letter Exchanges (DLX)

### A. Consumer Acknowledgments & Prefetch Count (QoS):
- By default, RabbitMQ aggressively pushes all messages in a queue to connected consumers.
- If a consumer takes 2 seconds to process a job, receiving 10,000 messages instantly causes Out-Of-Memory (OOM) crashes.
- **Prefetch Count**: Setting `channel.basicQos(prefetch = 10)` instructs RabbitMQ to send at most 10 unacknowledged messages to a worker at any given time.

### B. Dead Letter Exchanges (DLX):
A message is forwarded to a Dead Letter Exchange when:
1. The message is rejected (`basic.reject` or `basic.nack`) with `requeue = false`.
2. The message expires because its **TTL (Time-To-Live)** was exceeded.
3. The queue reaches its **Maximum Length** limit.
- **Use Case**: Isolating poisoned messages (malformed JSON payloads that crash workers) into a Dead Letter Queue (DLQ) for human triage.

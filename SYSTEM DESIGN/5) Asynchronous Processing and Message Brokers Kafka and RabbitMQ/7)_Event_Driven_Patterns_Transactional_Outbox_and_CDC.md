# 7) Event-Driven Patterns: Transactional Outbox and CDC

## Executive Overview
When a service needs to update its local database AND publish an event to a message broker (e.g., save an order and publish `OrderPlaced` to Kafka), doing both sequentially without distributed transactions leads to catastrophic data inconsistencies.

---

## 1. The Dual-Write Problem

```text
SCENARIO A (DB first, Broker fails):
1. DB.save(order);      // SUCCESS!
2. Kafka.send(event);   // CRASH / TIMEOUT / NETWORK FAILURE!
* Result: Order exists in DB, but billing and shipping services never receive the event!

SCENARIO B (Broker first, DB fails):
1. Kafka.send(event);   // SUCCESS!
2. DB.save(order);      // SQL CONSTRAINT VIOLATION!
* Result: Billing service charges customer for an order that does not exist in the database!
```

---

## 2. The Transactional Outbox Pattern

The Transactional Outbox pattern guarantees that the database state change and the event publication are bound atomically within a **single local ACID transaction**.

```text
[ Client Request ]
       │
       ▼
[ Application Service ]
       │
       ├── 1. BEGIN DB Transaction
       │   ├── INSERT INTO orders (id, user_id, amount) VALUES (101, 42, 99.99);
       │   └── INSERT INTO outbox (id, aggregate_type, payload) VALUES (uuid, 'Order', '{...}');
       ├── 2. COMMIT DB Transaction (Atomically Guaranteed by Relational DB!)
       │
       ▼
[ Outbox Message Relay / Poller ] (e.g., Debezium CDC)
       │ - Reads outbox table or tails database transaction log
       ▼
[ Apache Kafka ]
       │ - Publishes `OrderPlaced` event
       ▼
[ Mark outbox entry as PROCESSED / Delete ]
```

---

## 3. Change Data Capture (CDC) with Debezium

Instead of having a custom background thread poll the outbox table (`SELECT * FROM outbox WHERE processed = false`), enterprise architectures use **Change Data Capture (CDC)**:

```text
[ MySQL / PostgreSQL ] ──▶ Writes to [ Write-Ahead Log (WAL / Binlog) ]
                                                │
                                                ▼ (Tails transaction log at byte-level)
                                    [ Debezium CDC Connector ]
                                                │
                                                ▼ (Streams directly with zero polling)
                                    [ Apache Kafka Topics ]
```

### Advantages of CDC:
1. **Zero Database Query Overhead**: Does not execute polling SQL queries against table rows.
2. **Sub-Millisecond Event Streaming**: Events are published the moment the database appends to its WAL on disk.
3. **Capture All Mutations**: Captures `INSERT`, `UPDATE`, and `DELETE` operations with before and after state snapshots.

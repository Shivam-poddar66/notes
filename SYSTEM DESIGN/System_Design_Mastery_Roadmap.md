# System Design Mastery Roadmap (Complete 2026 Edition)

A structured, battle-tested, end-to-end roadmap to master **System Design & Distributed Architectures** from foundational networking and single-node scaling to Principal / Staff Distributed Systems Architect level.

---

## Table of Contents
1. [How to Use This Roadmap & Study System](#how-to-use-this-roadmap--study-system)
2. [Mastery Definition & Senior Staff Benchmarks](#mastery-definition--senior-staff-benchmarks)
3. [Visual Architecture Roadmap Flowchart](#visual-architecture-roadmap-flowchart)
4. [Phase 1: Foundations of Distributed Systems](#phase-1-foundations-of-distributed-systems)
5. [Phase 2: Scalability & High Availability Architecture](#phase-2-scalability--high-availability-architecture)
6. [Phase 3: High-Performance Caching & Redis Mastery](#phase-3-high-performance-caching--redis-mastery)
7. [Phase 4: Traffic Routing, Load Balancing & Reverse Proxies](#phase-4-traffic-routing-load-balancing--reverse-proxies)
8. [Phase 5: Asynchronous Processing & Message Brokers (Kafka & RabbitMQ)](#phase-5-asynchronous-processing--message-brokers-kafka--rabbitmq)
9. [Phase 6: Database Indexing, Query Optimization & Storage Engines](#phase-6-database-indexing-query-optimization--storage-engines)
10. [Phase 7: Database Partitioning, Sharding & Replication](#phase-7-database-partitioning-sharding--replication)
11. [Phase 8: Distributed Consistency, CAP/PACELC & Distributed Transactions](#phase-8-distributed-consistency-cappacelc--distributed-transactions)
12. [Phase 9: Observability, Resilience & Fault Tolerance](#phase-9-observability-resilience--fault-tolerance)
13. [Phase 10: The 4-Step System Design Interview Framework](#phase-10-the-4-step-system-design-interview-framework)
14. [Phase 11: Production Real-World Case Studies Portfolio](#phase-11-production-real-world-case-studies-portfolio)
15. [Phase 12: 100-Point Senior Distributed Architect Evaluation Checklist](#phase-12-100-point-senior-distributed-architect-evaluation-checklist)

---

## How to Use This Roadmap & Study System

- **Pacing**: Dedicate 10–14 hours per week across 16–24 weeks.
- **Dual-Track Approach**:
  - **Theory & Mechanics**: Understand underlying data structures (B+ trees, LSM trees, Hash Rings, Redis skip lists, Kafka commit logs).
  - **Trade-off Analysis**: System design is never about the "perfect" solution; it is always about choosing the right trade-off (Latency vs Consistency, Throughput vs Durability, Cost vs Complexity).
- **Note-Taking Structure**:
  - Every core topic in this directory corresponds to a detailed deep-dive markdown note file.
  - Review the companion notes in this directory as you progress through each phase.
- **Numbers Every Engineer Must Know**:
  - L1 cache reference: ~0.5–1 ns
  - Main memory (RAM) reference: ~100 ns
  - SSD random read: ~10–100 μs (microseconds)
  - NVMe / Fast SSD sequential read: ~1 GB/sec (~1 μs per 1 KB)
  - Magnetic disk seek / HDD read: ~10 ms (milliseconds)
  - Round trip within same datacenter: ~500 μs
  - Round trip across continental US (NYC to SF): ~40–60 ms
  - Transatlantic cable (London to NYC): ~70–80 ms

---

## Mastery Definition & Senior Staff Benchmarks

You have achieved true mastery of System Design when you can:

1. **Calculate Back-of-the-Envelope Numbers in 5 Minutes**:
   - Accurately estimate QPS (Queries Per Second), peak write/read ratios, bandwidth requirements, memory cache size (80/20 rule), and storage requirements over 5 years.
2. **Deconstruct Single Points of Failure (SPOF)**:
   - Identify every bottleneck across DNS, Edge, Ingress, Application, Cache, Broker, and Database tiers, specifying failover and degradation strategies.
3. **Select Storage & Messaging Engines by Access Pattern**:
   - Justify choosing Postgres vs Cassandra vs DynamoDB vs Redis vs Kafka vs RabbitMQ based on read-heavy vs write-heavy workloads, atomicity needs, query flexibility, and access latency.
4. **Architect Zero-Downtime Data Migrations & Re-sharding**:
   - Plan live horizontal database sharding, online index creation, schema migrations, and dual-writing without dropping a single write.
5. **Formulate Bulletproof Disaster Recovery & Resiliency**:
   - Implement graceful degradation, Circuit Breakers, Bulkheads, Dead Letter Queues, Rate Limiting, Idempotency keys, and Multi-Region Active-Active topologies.

---

## Visual Architecture Roadmap Flowchart

```
                            [ User Client ]
                                   │
                           DNS (Geo-DNS / Anycast)
                                   │
                                   ▼
                            [ CDN (Edge) ] ── (Static Assets / Edge Caching)
                                   │
                                   ▼
                      [ L4 / L7 Load Balancer ]
                        (NGINX / ALB / HAProxy)
                                   │
                                   ▼
                     [ API Gateway / Ingress ]
             (Rate Limiting, Auth, TLS Offload, Routing)
                                   │
        ┌──────────────────────────┴──────────────────────────┐
        ▼                                                     ▼
[ Stateless App Cluster A ]                           [ Stateless App Cluster B ]
(Auto-scaled Pods/Instances)                          (Auto-scaled Pods/Instances)
        │                                                     │
        ├──────────────────────────┬──────────────────────────┤
        ▼                          ▼                          ▼
 [ Distributed Cache ]    [ Message Broker ]          [ Primary DB ]
   (Redis Cluster)       (Kafka / RabbitMQ)       (Write Master / Sharded)
   - Cache-Aside          - Event Streaming            │
   - Write-Behind         - Task Dequeuing             ├── Replication (Async/Semi-Sync)
   - Lock / Rate Limit    - Outbox Pattern             ▼
        │                          │              [ Read Replicas ]
        │                          ▼              (Horizontal Read Scale)
        │                 [ Async Consumers /          │
        │                   Workers Cluster ]          ▼
        └──────────────────────────┼────────────── [ Search / Analytics ]
                                   ▼               (Elasticsearch / OLAP)
                            [ Object Store ]
                             (S3 / Blob / CDN)
```

---

## Phase 1: Foundations of Distributed Systems

### 1.1 Core Principles
- **Monolith vs Microservices vs Service-Oriented Architecture (SOA)**:
  - When to stick with a modular monolith; when microservices make sense.
  - Organizational scaling (Conway's Law) vs operational overhead.
- **Latency vs Throughput**:
  - Latency: Time taken to process a single request (p50, p95, p99, p99.9 tail latencies).
  - Throughput: Total requests or data units processed per second (RPS / QPS / MBps).
- **Availability vs Reliability vs Fault Tolerance**:
  - High Availability (HA): Measured in 9s (99.9% = ~8.7 hrs downtime/yr, 99.99% = ~52 mins/yr, 99.999% = ~5 mins/yr).
  - MTBF (Mean Time Between Failures) and MTTR (Mean Time To Recovery).
- **Stateful vs Stateless Services**:
  - Moving all session state, tokens, and ephemeral storage out of application instances into external distributed stores (Redis, DB, JWTs).

### 1.2 Back-of-the-Envelope Estimation Framework
- Daily Active Users (DAU) & Monthly Active Users (MAU).
- Read-to-Write Ratio (e.g., Twitter ~100:1 read-heavy; IoT telemetry ~1:10 write-heavy).
- Average QPS = $\frac{\text{Total Requests per Day}}{86,400\text{ seconds}}$.
- Peak QPS = $\text{Average QPS} \times \text{Peak Factor (typically } 2\times \text{ to } 5\times)$.
- Storage estimation over 5 years + replication factor ($\times 3$) + index overhead ($+20\%$).
- Cache memory estimation: 20% of hot data generates 80% of daily read traffic ($80/20$ Pareto principle).

---

## Phase 2: Scalability & High Availability Architecture

### 2.1 Scaling Paradigms
- **Vertical Scaling (Scale-Up)**:
  - Adding CPU, RAM, NVMe storage to a single machine.
  - Limits: Hardware ceiling, exponential cost, single point of failure (SPOF).
- **Horizontal Scaling (Scale-Out)**:
  - Adding more commodity machines/containers behind a load balancer.
  - Requires: Stateless architecture, distributed coordination, dynamic service discovery.

### 2.2 Elasticity & Autoscaling
- Metric-based scaling: CPU utilization, Memory pressure, Request queue depth, Target response time.
- Cool-down periods and hysteresis: Preventing autoscaling flapping (rapid thrashing of scale-up/scale-down).
- Predictive scaling for known diurnal peaks and scheduled campaigns.

### 2.3 Resiliency & Fault-Tolerant Patterns
- **Circuit Breaker Pattern**:
  - Closed (normal traffic) $\to$ Open (failures trip threshold, fast-fail without calling dependency) $\to$ Half-Open (canary trial probes).
- **Bulkhead Pattern**:
  - Isolating resources (thread pools, connection pools, memory quotas) so a single misbehaving component cannot starve the entire system.
- **Retry with Exponential Backoff & Full Jitter**:
  - $T_{\text{wait}} = \text{random}(0, \min(T_{\text{max}}, T_{\text{base}} \times 2^{\text{attempt}}))$. Avoids thundering herd synchronization.
- **Graceful Degradation & Fallbacks**:
  - Returning stale cache, default mocks, or disabling non-critical UI features (e.g., product recommendations) during severe traffic spikes.

---

## Phase 3: High-Performance Caching & Redis Mastery

### 3.1 Caching Layers Across the Full Stack
- **Client / Browser Cache**: HTTP headers (`Cache-Control: public, max-age=31536000, immutable`, `ETag`, `Last-Modified`).
- **CDN (Content Delivery Network)**: Cloudflare, Akamai, CloudFront. Edge caching, Geo-routing, DDoS mitigation, dynamic content acceleration.
- **API Gateway / Reverse Proxy Cache**: Micro-caching frequent GET responses (1–5 seconds) to survive traffic bursts.
- **Application In-Memory Cache**: Local heap caches (Guava, Caffeine, Node in-memory LRU) - ultra-fast (< 1 μs) but per-instance and unshared.
- **Distributed Cache Tier**: Dedicated remote cluster (Redis / Memcached) shared across all instances.
- **Database Buffer Pool**: InnoDB Buffer Pool, Postgres Shared Buffers.

### 3.2 Cache Access & Writing Strategies
- **Cache-Aside (Lazy Loading)**:
  - App checks cache. If hit, return. If miss, query DB, write to cache, return.
  - Pros: Only requested data cached; node failure is non-fatal. Cons: Cache miss penalty; possible stale data.
- **Read-Through**:
  - App treats cache as primary data source; cache library coordinates fetching from DB automatically on miss.
- **Write-Through**:
  - App writes to cache; cache writes synchronously to DB before returning success.
  - Pros: High consistency, no stale cache. Cons: Higher write latency.
- **Write-Back (Write-Behind)**:
  - App writes to cache; cache acknowledges immediately and batches writes asynchronously to DB.
  - Pros: Extremely high write throughput, low latency. Cons: Potential data loss if cache node crashes before flushing.
- **Refresh-Ahead**:
  - Cache predicts hot keys expiring soon and reloads them from DB before eviction.

### 3.3 Cache Invalidation & Eviction Policies
- **Eviction Algorithms**:
  - **LRU (Least Recently Used)**: Evicts keys not accessed for the longest time (Doubly Linked List + Hash Map, $O(1)$).
  - **LFU (Least Frequently Used)**: Tracks access frequency; evicts lowest access count.
  - **FIFO (First In, First Out)**: Evicts oldest inserted keys.
  - **Random / Volatile-TTL**: Evicts keys expiring soonest.
- **The 3 Classic Cache Disasters & Production Defenses**:
  - **Cache Stampede / Thundering Herd**:
    - Trigger: A super hot key expires, and thousands of concurrent requests miss and query DB simultaneously.
    - Solution: Distributed Mutex locking (`SETNX`), Probabilistic early expiration (XFetch algorithm), or background cron refresh.
  - **Cache Penetration**:
    - Trigger: Queries for non-existent IDs (e.g., ID: -99999) bypass cache and continuously hit DB.
    - Solution: Cache null/empty values with short TTL, or use **Bloom Filters** at the cache gateway.
  - **Cache Avalanche**:
    - Trigger: Massive number of keys expire at the exact same second (e.g., midnight batch), crushing the DB.
    - Solution: Add random jitter to TTL values (`TTL = 3600 + rand(0, 300)`).

### 3.4 Redis Deep Dive & Internals
- **Why is Redis so fast?**
  - In-memory execution, non-blocking asynchronous event loop (I/O multiplexing via `epoll`/`kqueue`), zero context switching for command execution, optimized C data structures.
- **Core Data Structures & Use Cases**:
  - **String**: Key-value, counters (`INCRBY`), distributed locks (`SET key value NX PX 30000`).
  - **Hash**: User sessions, entity profiles ($O(1)$ field get/set).
  - **List**: Message queues (`LPUSH` / `RPOP` / `BRPOP`), activity feeds.
  - **Set**: Unique tags, social graphs (`SADD`, `SINTER`, `SUNION`).
  - **Sorted Set (ZSET)**: Leaderboards, priority queues, sliding-window rate limiters ($O(\log N)$ skiplist + hash map).
  - **Bitmaps & HyperLogLog**: Massive cardinality counting (e.g., 100M unique visitors using only 12 KB memory via HLL).
  - **Streams**: Persistent pub/sub event logs with consumer groups.
- **Redis Persistence**:
  - **RDB (Snapshotting)**: Point-in-time binary dump (`bgsave` using fork). Fast recovery, potential data loss since last snapshot.
  - **AOF (Append Only File)**: Logs every write command (`appendfsync always / everysec / no`). High durability, larger file size.
  - **Hybrid Persistence**: RDB snapshot preamble + incremental AOF log.
- **High Availability & Scaling**:
  - **Redis Sentinel**: Master-Replica monitoring, automatic failover, cluster discovery.
  - **Redis Cluster**: Multi-master horizontal sharding across 16,384 Hash Slots ($CRC16(\text{key}) \pmod{16384}$), master-replica pairs per shard, zero-downtime resharding.

---

## Phase 4: Traffic Routing, Load Balancing & Reverse Proxies

### 4.1 Load Balancing Tiers & OSI Layers
- **DNS Load Balancing**:
  - Geo-DNS (directs users to closest datacenter), Round Robin DNS, Anycast BGP routing (same IP advertised from multiple geographic locations).
- **Layer 4 Load Balancing (Transport Layer - TCP/UDP)**:
  - Operates on IP addresses and TCP/UDP ports without decrypting or inspecting application payload.
  - Extreme throughput, low CPU overhead (e.g., AWS NLB, Linux Virtual Server / IPVS, HAProxy in TCP mode).
- **Layer 7 Load Balancing (Application Layer - HTTP/HTTPS/gRPC)**:
  - Inspects HTTP headers, cookies, URL paths, content types.
  - Supports path-based routing (`/api/v1/users` $\to$ User Service), header routing, cookie-based session stickiness, SSL/TLS termination, gRPC multiplexing (e.g., AWS ALB, NGINX, Envoy, Traefik).

### 4.2 Load Balancing Algorithms & Mechanics
- **Static Algorithms**:
  - Round Robin: Distributes sequentially.
  - Weighted Round Robin: Distributes proportionally to server capacity/spec.
  - IP Hash: Deterministic hashing of client IP ($hash(\text{IP}) \pmod N$).
- **Dynamic Algorithms**:
  - Least Connections: Routes to server with fewest active TCP connections.
  - Weighted Least Connections: Combines server capacity with active connection count.
  - Least Response Time / Latency: Pings or measures TTFB (Time to First Byte).
- **Consistent Hashing**:
  - Essential for distributed caches and stateful routing.
  - Hash ring $[0, 2^{32}-1]$ with virtual nodes to balance distribution and avoid re-mapping when nodes are added or removed.

### 4.3 Health Checking, Failover & TLS Offloading
- Active Health Checks: Periodic synthetic probes (HTTP `GET /healthz`, TCP SYN).
- Passive Health Checks: Detecting error thresholds (e.g., 3 consecutive 5xx errors) on live traffic and marking nodes down.
- SSL/TLS Termination: Decrypting HTTPS at the load balancer to relieve CPU load from backend application servers; backend communicates over private VPC network.

---

## Phase 5: Asynchronous Processing & Message Brokers (Kafka & RabbitMQ)

### 5.1 Synchronous vs Asynchronous Architecture
- Drawbacks of pure synchronous REST/RPC:
  - Cascading failures, thread blocking, tight coupling, vulnerable to latency spikes and outages.
- Benefits of Asynchronous Message-Driven Architecture:
  - Temporal decoupling (producer and consumer don't need to be online at the same time), traffic smoothing / peak shaving, guaranteed delivery, parallel fan-out processing.

### 5.2 RabbitMQ Deep Dive (AMQP Message Broker)
- **Architecture**:
  - **Producer $\to$ Exchange $\to$ Bindings $\to$ Queues $\to$ Consumer**.
- **Exchange Types**:
  - **Direct**: Routes based on exact matching of routing key to binding key.
  - **Fanout**: Broadcasts every message to all bound queues unconditionally.
  - **Topic**: Pattern matching with wildcards (`*` matches exactly 1 word; `#` matches 0 or more words).
  - **Headers**: Routes based on message header attributes instead of routing keys.
- **Consumer Mechanics**:
  - Consumer acknowledgments (`ack`, `nack`, `reject` with requeue flag).
  - **Prefetch Count (QoS)**: Prevents overwhelming a single worker by limiting unacknowledged messages in flight.
  - **Dead Letter Exchanges (DLX)**: Routing rejected, failed, or expired (TTL) messages to a dedicated inspection queue.
- **Best Suited For**: Complex routing rules, task queues, transactional job processing, priority queues, low-latency microservice RPC.

### 5.3 Apache Kafka Deep Dive (Distributed Event Streaming Platform)
- **Architecture**:
  - Append-only distributed commit log.
  - **Topics $\to$ Partitions $\to$ Segments on Disk**.
- **Partitions & Horizontal Scalability**:
  - The partition is the unit of parallelism in Kafka.
  - Messages with the same key go to the same partition ($hash(\text{key}) \pmod{\text{partitions}}$) and are guaranteed strict order.
  - If ordering across the entire topic is needed, only 1 partition can be used (bottleneck).
- **Brokers, Controller & Storage Engine**:
  - Zero-Copy optimization via Linux `sendfile` system call (disk $\to$ OS page cache $\to$ network socket without copying to user-space memory).
  - Sequential disk writes (faster than random memory writes on modern storage).
  - Metadata management via KRaft (Kafka Raft consensus, replacing legacy ZooKeeper).
- **Replication & High Availability**:
  - Leader and Follower replicas.
  - **ISR (In-Sync Replicas)**: Replicas caught up with the leader.
  - `min.insync.replicas` and `acks=all` (or `acks=-1`) for maximum durability guarantee.
- **Consumer Groups & Offsets**:
  - Each partition is consumed by exactly one consumer within a Consumer Group.
  - Offset tracking in the internal `__consumer_offsets` topic.
  - Group Rebalancing protocols (Eager vs Incremental Cooperative rebalancing).
- **Delivery Guarantees**:
  - **At-most-once**: Consumer commits offset before processing. If process crashes, message is lost.
  - **At-least-once**: Consumer commits offset after processing. If crash occurs midway, message is reprocessed (requires idempotent consumers).
  - **Exactly-once Semantics (EOS)**: Idempotent producer (`enable.idempotence=true`) with Sequence IDs + Kafka transactional API across multiple topics/partitions.

### 5.4 Kafka vs RabbitMQ: The Architectural Decision Matrix

| Dimension | RabbitMQ | Apache Kafka |
| :--- | :--- | :--- |
| **Model** | Traditional Message Broker (Smart broker, dumb consumer) | Distributed Append-Only Commit Log (Dumb broker, smart consumer) |
| **Message Consumption** | Push model (broker pushes to consumer) | Pull model (consumers poll partitions at their own pace) |
| **Message Retention** | Deleted upon consumer ACK (ephemeral) | Retained on disk according to retention policy (hours, days, or forever) |
| **Replayability** | Cannot replay processed messages | Rewind consumer offset to re-process historical stream |
| **Throughput** | High (~10k–50k msgs/sec per node) | Ultra-Massive (~1M+ msgs/sec with batching & zero-copy) |
| **Ordering** | FIFO within queue, but parallel workers break ordering | Strict ordering within each individual partition |
| **Routing Flexibility** | Complex routing (Topic, Fanout, Header matching) | Simple key-based partition hash routing |
| **Primary Use Cases** | Background jobs, worker queues, complex workflow routing | Event streaming, activity tracking, audit logs, CDC, real-time analytics |

### 5.5 Event-Driven Design Patterns
- **Transactional Outbox Pattern**:
  - Writing business state change and message event in a single atomic local DB transaction; an async relay (Debezium / polling publisher) reads the outbox table and publishes to Kafka.
- **Change Data Capture (CDC)**:
  - Reading database transaction logs (MySQL binlog, Postgres WAL) directly into Kafka streams.
- **Event Sourcing & CQRS (Command Query Responsibility Segregation)**:
  - State is stored as an immutable sequence of events; read models are projected asynchronously into optimized read datastores.

---

## Phase 6: Database Indexing, Query Optimization & Storage Engines

### 6.1 Database Storage Engine Architecture
- **Page-Based Storage**:
  - Databases read and write data in fixed-size blocks/pages (typically 4 KB, 8 KB in Postgres, 16 KB in MySQL InnoDB).
  - Row pointers, page directory, slot arrays.
- **Buffer Pool / Page Cache**:
  - In-memory cache of disk pages. Dirty pages flushed via WAL (Write-Ahead Logging).

### 6.2 Index Data Structures
- **B-Tree & B+ Tree (Relational Standard - Postgres, MySQL, Oracle)**:
  - Balanced multi-way search tree.
  - Why B+ Tree over binary search tree or standard B-tree?
    - High branching factor (fan-out of 100–1000) keeps tree depth shallow (3–4 levels can index hundreds of millions of rows $\to$ 3–4 I/O operations max).
    - Internal nodes only store keys and pointers (maximizing keys per page).
    - Leaf nodes contain all data/pointers and are linked as a doubly-linked list for lightning-fast range scans (`BETWEEN`, `>`, `<`).
- **LSM-Tree (Log-Structured Merge-Tree - Cassandra, RocksDB, ScyllaDB)**:
  - Writes go sequentially to an in-memory **MemTable** (SkipList / Red-Black tree) + appended to WAL for durability.
  - When MemTable reaches threshold, flushed to disk as an immutable **SSTable (Sorted String Table)**.
  - Multi-tier background **Compaction** merges and purges old/tombstoned data.
  - **Bloom Filters** check if a key definitely does not exist before searching disk SSTables.
  - Optimized for ultra-fast write throughput (zero random disk writes).
- **Hash Index**:
  - $O(1)$ exact lookup (`WHERE id = 5`), but completely incapable of range queries or sorting.
- **Inverted Index (Elasticsearch, Solr, Lucene)**:
  - Maps terms/words to document lists; foundation of full-text search and analytical querying.

### 6.3 Clustered vs Secondary (Non-Clustered) Indexes
- **Clustered Index**:
  - Determines the physical on-disk ordering of rows.
  - The leaf node contains the complete data row itself.
  - Only **one** clustered index per table (typically the Primary Key in MySQL InnoDB).
- **Secondary (Non-Clustered) Index**:
  - A separate B+ tree structure.
  - Leaf nodes store the indexed column(s) plus the **clustering key (primary key)** or row ID.
  - **Index Lookup Penalty (Bookmark Lookup)**: Secondary index lookup first traverses the secondary B+ tree to find the Primary Key, then traverses the Clustered B+ tree to fetch the full row.
- **Covering Index (Index-Only Scan)**:
  - When all columns requested in `SELECT`, `WHERE`, and `ORDER BY` exist within the secondary index itself.
  - The database completely skips touching the clustered table pages, slashing I/O to near-zero.

### 6.4 Composite Indexes & The Leftmost Prefix Rule
- Index on `(status, created_at, user_id)`:
  - Efficient for:
    - `WHERE status = 'active'`
    - `WHERE status = 'active' AND created_at > '2026-01-01'`
    - `WHERE status = 'active' AND created_at = '2026-01-01' AND user_id = 42`
  - Completely unusable for:
    - `WHERE created_at > '2026-01-01'` (skips leading column `status`)
    - `WHERE user_id = 42`
- **Range Condition Trap**: In a composite index, indexing stops after the first range column.
  - If query is `WHERE status = 'active' AND created_at > '2026-01-01' AND user_id = 42`, `user_id` cannot be used for index filtering if placed after `created_at`. Order columns: **Equality first, Range second**.

### 6.5 Cardinality, Selectivity & EXPLAIN Execution Plans
- **Selectivity** = $\frac{\text{Distinct Values}}{\text{Total Rows}}$.
  - High selectivity (close to 1.0, e.g., email, UUID): Perfect for indexing.
  - Low selectivity (e.g., boolean `is_active`, `gender`): Database query planner will usually abandon the index and perform a full table scan.
- Reading `EXPLAIN` / `EXPLAIN ANALYZE`:
  - Scan types: `ALL` (Table scan - red flag), `index` (Full index scan), `range` (Range scan on index), `ref` (Non-unique index lookup), `eq_ref` (Unique/PK join lookup), `const` (Single row PK lookup).
  - Checking `rows_examined` vs `rows_returned`.

---

## Phase 7: Database Partitioning, Sharding & Replication

### 7.1 Database Replication Architectures
- **Single-Leader (Master-Slave / Primary-Replica)**:
  - All writes go to Single Primary; reads distributed across multiple Read Replicas.
  - **Synchronous Replication**: Leader waits for replica confirmation before committing. Guaranteed zero data loss, but write latency tied to slowest replica; write halts if replica fails.
  - **Asynchronous Replication**: Leader commits immediately, sends binlog/WAL asynchronously. Ultra-low latency, but risk of data loss during leader failover.
  - **Semi-Synchronous Replication**: Leader waits for at least 1 replica to acknowledge.
- **Replication Lag & Read-Your-Own-Writes Consistency**:
  - Issue: User updates profile, immediately refreshes, reads from a lagging replica, and sees old data.
  - Solutions: Route user's reads to Primary for 5–10 seconds after any write; use client-tracked version stamps / Monotonic Read guarantees.
- **Multi-Leader (Master-Master)**:
  - Writes accepted in multiple geographically dispersed datacenters.
  - Requires automated conflict resolution (Last-Write-Wins, CRDTs - Conflict-free Replicated Data Types).
- **Leaderless Replication (Dynamo-Style - Cassandra, ScyllaDB, Amazon DynamoDB)**:
  - Client writes directly to multiple replica nodes.
  - **Quorum Consensus**:
    - Let $N$ = total replicas, $W$ = write quorum, $R$ = read quorum.
    - If $W + R > N$, strong consistency is guaranteed (at least one node in read quorum has the latest write).
    - Example: $N=3, W=2, R=2 \implies 2 + 2 = 4 > 3$ (Quorum achieved).
  - Read Repair & Anti-Entropy (Merkle trees) in the background.

### 7.2 Database Sharding (Horizontal Partitioning)
- **Why Shard?** When data volume, write throughput, or working-set memory exceeds the limits of the largest single database instance.
- **Vertical Partitioning**:
  - Splitting tables by domain (e.g., User DB, Billing DB, Product DB) or moving large blobs/text columns into separate tables.
- **Horizontal Sharding Strategies**:
  1. **Range-Based Sharding**:
     - Shard 1: IDs 1–1,000,000; Shard 2: IDs 1,000,001–2,000,000.
     - Pro: Simple, easy range queries. Con: Severe write hotspots (all new writes hit the newest shard).
  2. **Hash-Based Sharding**:
     - $\text{Shard ID} = hash(\text{Shard Key}) \pmod N$.
     - Pro: Uniformly distributes writes across all nodes. Con: Adding/removing nodes requires massive data re-shuffling; range queries must scatter-gather to all shards.
  3. **Directory-Based / Lookup Service**:
     - Central lookup table maps entity IDs to specific physical database shards.
     - Pro: Extreme flexibility; easy tenant re-balancing. Con: Lookup service becomes single point of failure and extra latency hop (must be cached).

### 7.3 Consistent Hashing with Virtual Nodes
- Solves the catastrophic re-balancing problem of naive modulo hashing ($N \to N+1$).
- Map shards and keys onto a circular hash ring of size $2^{32}$.
- Keys traverse clockwise until they encounter the first shard node.
- **Virtual Nodes (Tokens)**: Each physical machine is mapped to 100–300 points on the ring.
  - Prevents non-uniform data skew.
  - When a node is added or fails, only $\frac{1}{N}$ fraction of keys are moved, gracefully distributed across all remaining nodes.

### 7.4 Sharding Challenges & Trade-offs
- **Cross-Shard Joins**: Almost impossible to perform efficiently. Application must perform in-memory joins or duplicate/denormalize reference data.
- **Cross-Shard Transactions**: Demands heavy distributed transactions (2PC / Saga), destroying performance.
- **Distributed ID Generation**: Auto-incrementing IDs fail across multiple shards.
  - Solutions: Twitter Snowflake (64-bit: Timestamp + Datacenter ID + Machine ID + Sequence number), UUIDv7 (time-ordered 128-bit), or centralized Ticket Server.

---

## Phase 8: Distributed Consistency, CAP/PACELC & Distributed Transactions

### 8.1 CAP Theorem & PACELC Theorem
- **CAP Theorem**: In a distributed system with network partitions ($P$ is unavoidable in physical networks), you must choose between:
  - **Consistency (CP)**: Every read receives the most recent write or an error. If nodes cannot communicate, refuse requests to protect consistency. (e.g., HBase, Zookeeper, etcd).
  - **Availability (AP)**: Every non-failing node returns a response, but it may be stale. (e.g., Cassandra, CouchDB).
  - *Note*: CA does not exist in real-world distributed networks because network partitions cannot be prevented.
- **PACELC Theorem (Extended CAP)**:
  - **If Partition ($P$)**: Choose between Availability ($A$) and Consistency ($C$);
  - **Else ($E$)**: Choose between Latency ($L$) and Consistency ($C$).
  - Example: MongoDB is $PC/EC$ (consistent during normal ops, consistent during partition); Cassandra is $PA/EL$ (available during partition, prioritizes low latency over consistency normally).

### 8.2 Consistency Hierarchy
- **Strict / Linearizable Consistency**: Global real-time ordering across all nodes.
- **Sequential Consistency**: Operations executed in some sequential order agreed on by all nodes, preserving per-process order.
- **Causal Consistency**: Causally related operations are seen by every node in the same order; concurrent operations may be seen in different order.
- **Read-Your-Writes Consistency**: A user always sees their own updates.
- **Eventual Consistency**: Given no new updates, all replicas eventually converge to the identical state.

### 8.3 Distributed Transactions: 2PC vs Saga Pattern
- **Two-Phase Commit (2PC)**:
  - Phase 1 (Prepare): Coordinator asks all participants to prepare and lock resources.
  - Phase 2 (Commit): If all vote yes, coordinator sends commit; otherwise abort.
  - Disasters: Coordinator is SPOF; blocking locks held indefinitely if coordinator dies midway; poor latency and availability.
- **Saga Pattern (The Modern Standard for Microservices)**:
  - Breaks a distributed transaction into a sequence of local transactions.
  - Each service updates its own database and publishes an event/message.
  - If a step fails, the Saga executes **Compensating Transactions** backward to reverse previous steps.
  - **Choreography**: Services listen to events and decide next action (no central controller; good for simple flows, hard to trace at scale).
  - **Orchestration**: A dedicated Saga Orchestrator tells each participant what transaction to execute (centralized state machine, clean visibility, e.g., Temporal, AWS Step Functions).

---

## Phase 9: Observability, Resilience & Fault Tolerance

### 9.1 The Three Pillars of Observability
- **Metrics**: Numeric aggregations over time intervals (Counter, Gauge, Histogram - Prometheus, Datadog).
- **Logs**: Structured JSON log events indexed with correlation IDs (ELK Stack, OpenSearch, Grafana Loki).
- **Distributed Tracing**: Tracking a single request through microservices via W3C Trace Context / OpenTelemetry (`trace_id`, `span_id`, parent spans, Jaeger).

### 9.2 Rate Limiting & Traffic Shaping Algorithms
- **Token Bucket**: Tokens added at constant rate up to bucket capacity. Supports bursts while maintaining average rate. (Nginx, Stripe API).
- **Leaky Bucket**: Requests enter queue and leak out at constant, smoothed rate. Eliminates bursts.
- **Fixed Window Counter**: Counts requests in discrete time blocks (e.g., 00:00–00:01). Flaw: $2\times$ burst at window boundaries.
- **Sliding Window Log**: Stores exact timestamp of every request in Redis Sorted Set (`ZREMRANGEBYSCORE`, `ZCARD`). Accurate, but high memory footprint.
- **Sliding Window Counter**: Hybrid combining previous window count and current window count using weighted percentage. Low memory, highly accurate.

---

## Phase 10: The 4-Step System Design Interview Framework

### Step 1: Clarify Requirements & Scope (3–5 minutes)
- **Functional Requirements**: Core 3–4 features that must be designed (e.g., "User pastes URL $\to$ gets short link; User clicks short link $\to$ redirects"). Explicitly de-scope secondary features.
- **Non-Functional Requirements**: High availability, low read latency (< 50 ms), write latency, consistency model, durability guarantees.
- **Scale & Traffic Estimates**: DAU, Read:Write ratio, QPS, Storage per year, Bandwidth.

### Step 2: High-Level Architecture & API Design (10–12 minutes)
- Define API contracts (REST / gRPC endpoints, parameters, responses).
- Data Schema & Database selection (SQL vs NoSQL).
- Draw the high-level block diagram: Client $\to$ DNS/CDN $\to$ Load Balancer $\to$ Web Application Servers $\to$ Primary/Replica DB + Cache.
- Walk through the happy path data flow.

### Step 3: Detailed Component Deep-Dive (15–20 minutes)
- Address the core technical complexities:
  - How does hashing / ID generation work?
  - How is caching maintained without stale reads?
  - How are database tables partitioned and indexed?
  - How are message brokers handling consumer lag and retries?

### Step 4: Bottlenecks, Edge Cases & Failure Scenarios (5–8 minutes)
- What happens when a Redis node dies?
- What happens if the primary DB crashes during write?
- How to handle hot keys / celebrity problem (e.g., Elon Musk tweet)?
- Disaster recovery, data replication lag, security & rate limiting.

---

## Phase 11: Production Real-World Case Studies Portfolio

| Case Study | Key Architecture Challenges | Core Technologies |
| :--- | :--- | :--- |
| **1. TinyURL / URL Shortener** | High read:write ratio (100:1), base62 encoding vs pre-generated keys, low latency redirection | Redis, Postgres/Cassandra, Base62, KGS (Key Generation Service) |
| **2. Distributed Rate Limiter** | Microsecond latency, race conditions, distributed atomicity, cluster coordination | Redis Lua scripts, Token Bucket, Sliding Window, Envoy Filter |
| **3. Scalable Notification System** | Fan-out to millions, multiple notification channels (Push, SMS, Email), rate limiting, delivery receipts | RabbitMQ / Kafka, Redis, Worker Pools, APNS, FCM, Twilio |
| **4. Real-Time Chat App (WhatsApp/Slack)** | Persistent bidirectional connections, message ordering, online presence, group fan-out | WebSockets, TCP, Kafka, Cassandra/ScyllaDB, Redis Pub/Sub, Zookeeper |
| **5. E-Commerce Flash Sale System** | Extreme concurrency bursts, preventing overselling, zero race conditions, checkout queues | Redis Distributed Lock (Redlock), Lua scripts, Kafka Order Queue, DB Optimistic Locking |
| **6. Distributed File Storage (Google Drive / S3)** | Chunking large files, deduplication, resumable uploads, metadata sync across devices | S3/Blob, Chunking engine, Metadata DB (Postgres), Kafka event sync |

---

## Phase 12: 100-Point Senior Distributed Architect Evaluation Checklist

Track your readiness across each dimension (Target: 85+ points for Senior / Staff level):

- [ ] **1–10: Distributed System Fundamentals**
  - [ ] Can calculate QPS, bandwidth, and 5-year storage estimates under 5 minutes without hesitation.
  - [ ] Can clearly articulate latency vs throughput trade-offs across network and storage layers.
  - [ ] Understands the difference between synchronous RPC and asynchronous event streaming.
  - [ ] Can accurately distinguish between CP and AP systems under network partitions.
  - [ ] Understands PACELC and can apply it to modern distributed databases.
  - [ ] Knows single-digit latency numbers for L1 cache, RAM, NVMe SSD, and inter-datacenter networks.
  - [ ] Can architect stateless application layers with zero session stickiness dependencies.
  - [ ] Can explain how to design an idempotent REST API using Idempotency Keys.
  - [ ] Can design a graceful degradation strategy during 10x traffic spikes.
  - [ ] Knows how to compute SLA uptime percentages (99.9% vs 99.999%).

- [ ] **11–30: Caching & Redis Mastery**
  - [ ] Can implement Cache-Aside, Write-Through, and Write-Behind patterns with error handling.
  - [ ] Can explain the internal implementation of LRU using a Doubly Linked List and Hash Map.
  - [ ] Knows the difference between Redis RDB and AOF persistence and their performance impacts.
  - [ ] Can explain why Redis is single-threaded for command execution yet handles 100k+ QPS.
  - [ ] Can design defenses against Cache Stampede using distributed locks or XFetch algorithm.
  - [ ] Can prevent Cache Penetration using Bloom Filters.
  - [ ] Can prevent Cache Avalanche using TTL jittering.
  - [ ] Mastered Redis data structures: Strings, Hashes, Lists, Sets, Sorted Sets, HyperLogLogs, and Streams.
  - [ ] Can write atomic Redis Lua scripts to execute multi-step operations without race conditions.
  - [ ] Understands Redis Cluster slot allocation ($16,384$ slots) and cross-slot multi-key limitations.

- [ ] **31–50: Load Balancing & Networking**
  - [ ] Can explain L4 vs L7 load balancing mechanisms and when to use each.
  - [ ] Knows how Consistent Hashing with virtual nodes distributes load and minimizes key movement.
  - [ ] Understands DNS Anycast routing and Geo-DNS load balancing.
  - [ ] Can configure SSL/TLS termination and explain performance benefits to the backend.
  - [ ] Can design active and passive health checks that avoid false-positive flapping.
  - [ ] Understands HTTP/1.1 vs HTTP/2 multiplexing vs HTTP/3 (QUIC) UDP fundamentals.
  - [ ] Can explain connection pooling, keep-alive, and head-of-line blocking.
  - [ ] Understands reverse proxy vs forward proxy architectural placement.

- [ ] **51–70: Message Queues & Event Streaming**
  - [ ] Can articulate the structural differences between Kafka commit logs and RabbitMQ AMQP queues.
  - [ ] Understands Kafka partition assignment, consumer groups, and rebalance triggers.
  - [ ] Knows how Kafka leverages zero-copy (`sendfile`) and sequential disk I/O.
  - [ ] Can design end-to-end Exactly-Once Semantics (EOS) using idempotent producers and transactions.
  - [ ] Can implement the Transactional Outbox pattern with Change Data Capture (Debezium).
  - [ ] Knows how RabbitMQ Direct, Fanout, and Topic exchanges route messages.
  - [ ] Can implement Dead Letter Queues (DLQ) and exponential backoff retry topics.
  - [ ] Knows how to handle message poison pills and schema evolution (Avro / Protobuf).

- [ ] **71–85: Database Indexing, Partitioning & Sharding**
  - [ ] Can draw and explain the internal mechanics of a B+ Tree leaf and internal node structure.
  - [ ] Can contrast B+ Trees with LSM-Trees and know which to pick for write-heavy workloads.
  - [ ] Understands the performance cost of secondary index bookmark lookups vs covering indexes.
  - [ ] Can diagnose slow queries using `EXPLAIN ANALYZE` and identify missing composite indexes.
  - [ ] Understands the Leftmost Prefix Rule and range column ordering in composite indexes.
  - [ ] Can design horizontal database sharding using consistent hashing and distributed IDs.
  - [ ] Knows how to mitigate single-shard hot spots (e.g., celebrity user write/read traffic).
  - [ ] Understands Single-Leader, Multi-Leader, and Leaderless quorum replication ($W + R > N$).

- [ ] **86–100: Distributed Systems Architecture & Interview Mastery**
  - [ ] Can lead a full 45-minute System Design interview smoothly using the 4-step framework.
  - [ ] Can design an end-to-end URL shortener with collision-free Base62 encoding.
  - [ ] Can design a real-time distributed rate limiter using Redis Token Bucket and sliding window.
  - [ ] Can design a scalable notification engine with fan-out queues and channel throttling.
  - [ ] Can design a real-time chat application with WebSockets and message history storage.
  - [ ] Can design a flash sale reservation system preventing double-booking and overselling.
  - [ ] Understands distributed consensus protocols (Raft / Paxos) at a conceptual level.
  - [ ] Can architect multi-region active-active setups with automated disaster recovery.

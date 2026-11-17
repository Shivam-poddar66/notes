# System Design Master Interview Questions & Answers (Top 50)

Comprehensive, battle-tested interview questions and answers covering **Scalability, Caching (Redis), Load Balancing, Message Queues (Kafka & RabbitMQ), Database Indexing & Sharding, and Distributed Consistency**.

---

## Section 1: Scalability & Distributed Architecture

### Q1: What is the fundamental difference between Vertical and Horizontal Scaling?
- **Vertical Scaling (Scale-Up)**: Upgrading existing hardware with more CPU cores, RAM, and faster NVMe disks.
  - *Pros*: Simplest architecture; no distributed code required.
  - *Cons*: Rigid hardware ceiling, exponential cost curve, single point of failure (requires downtime to upgrade).
- **Horizontal Scaling (Scale-Out)**: Adding more commodity servers behind a load balancer.
  - *Pros*: Elastic, virtually infinite scaling capacity, high availability, cost-effective.
  - *Cons*: Requires completely stateless application design, distributed caching, session externalization, and database partitioning.

### Q2: What makes an application tier "stateless", and why is it necessary for horizontal autoscaling?
An application tier is stateless when any server instance can handle any incoming client request without needing historical context or local session data from previous requests.
To achieve this:
1. User sessions and tokens are moved to an external store (Redis) or encoded into cryptographically signed JWTs.
2. File uploads bypass local disk and go directly to cloud object storage (S3/GCS).
3. Background tasks are dispatched to external brokers (Kafka/RabbitMQ).
Without statelessness, adding or removing instances during traffic spikes results in broken user sessions or stuck in-flight jobs.

### Q3: Explain the Circuit Breaker pattern and its three states.
The Circuit Breaker pattern prevents a failing downstream service from cascading and crashing upstream callers.
- **Closed**: Normal state. Requests pass through to the dependency. The breaker monitors error rates.
- **Open**: When errors exceed a threshold (e.g., 50% failures over 10 seconds), the breaker trips to Open. All incoming requests fail immediately or execute a local fallback without touching the failing dependency, preventing thread pool exhaustion.
- **Half-Open**: After a configurable cooldown timeout (e.g., 30 seconds), the breaker allows a small percentage of trial canary requests through. If they succeed, it transitions back to **Closed**; if they fail, it reverts to **Open**.

### Q4: What is the difference between SLA, SLO, and SLI?
- **SLI (Service Level Indicator)**: A measurable metric indicating real-time performance (e.g., "p99 HTTP latency is 140 ms").
- **SLO (Service Level Objective)**: An internal target set by the engineering team (e.g., "99.9% of requests must complete under 200 ms").
- **SLA (Service Level Agreement)**: A formal agreement with paying customers specifying penalties or financial refunds if the SLO is breached.

---

## Section 2: Caching & Redis

### Q5: Compare Cache-Aside, Write-Through, and Write-Back (Write-Behind).
- **Cache-Aside (Lazy Loading)**: Application reads cache. On miss, it queries the database and writes the result to cache.
  - *Trade-off*: Memory efficient (only requested data is cached), but incurs latency penalty on misses and potential stale reads if DB is updated out-of-band.
- **Write-Through**: Application writes to cache; cache writes synchronously to DB before acknowledging.
  - *Trade-off*: High consistency; no stale cache. However, higher write latency due to two round-trips.
- **Write-Back (Write-Behind)**: Application writes to cache, which acknowledges immediately. Cache asynchronously flushes batched writes to DB in the background.
  - *Trade-off*: Extremely high write throughput, but high risk of data loss if cache crashes before flushing.

### Q6: What is a Cache Stampede (Thundering Herd) and how do you prevent it?
When a high-traffic cached key expires, thousands of concurrent requests miss simultaneously and flood the database to recompute it.
- **Prevention**:
  1. **Distributed Mutex Lock (`SETNX`)**: Only the first worker to miss acquires a short-lived lock in Redis, computes the query, and populates the cache. Other requests wait or retry.
  2. **Probabilistic Early Expiration (XFetch)**: Background workers evaluate a probability formula based on remaining TTL and proactively refresh the cache before expiration.
  3. **Permanent Keys with Async Refresh**: Set key TTL to infinity; use a background worker or event to update it.

### Q7: What is Cache Penetration and how does a Bloom Filter solve it?
Cache Penetration occurs when queries for non-existent keys (e.g., `id = -99999`) bypass the cache and continuously hit the database.
- **Solution: Bloom Filter**:
  - A space-efficient probabilistic data structure that tests set membership.
  - A Bloom filter can return **False Positives** (might say an element exists when it doesn't), but **NEVER False Negatives** (if it says an element is absent, it is 100% absent).
  - Placed in front of the cache: If the Bloom filter says the ID does not exist, reject immediately without touching Redis or DB.

### Q8: What is Cache Avalanche and how is it mitigated?
Cache Avalanche occurs when a large batch of cached keys share the exact same expiration time (e.g., set to expire at midnight), causing an overwhelming wave of DB queries all at once.
- **Mitigation**: Add a randomized jitter to the TTL (e.g., `TTL = 3600 + random(0, 300)`).

### Q9: Why is Redis single-threaded for command execution yet capable of over 100k+ QPS?
1. **In-Memory Operations**: All data resides in RAM with sub-microsecond access times.
2. **I/O Multiplexing (Reactor Pattern)**: Uses non-blocking OS system calls (`epoll`/`kqueue`) to handle tens of thousands of active socket connections concurrently.
3. **No Thread Context Switching**: Zero locking, mutex contention, or race conditions during command processing.

### Q10: How does an LRU Cache work internally?
Implemented using a **Hash Map + Doubly Linked List**:
- The Hash Map maps keys to nodes in the linked list for $O(1)$ lookup.
- When an item is accessed or updated, the node is moved to the head of the Doubly Linked List in $O(1)$.
- When cache memory is full, the tail node (the least recently used) is evicted in $O(1)$.

### Q11: Explain Redis Persistence: RDB vs. AOF vs. Hybrid.
- **RDB (Snapshotting)**: Point-in-time binary snapshot of memory (`bgsave` via copy-on-write `fork()`). Fast startup recovery, but data between snapshots is lost on crash.
- **AOF (Append Only File)**: Logs every write command. Offers high durability (`fsync everysec`), but larger file size and slower startup.
- **Hybrid**: Starts with an RDB base for fast recovery, followed by an incremental AOF log for recent writes.

### Q12: How does Redis Cluster partition data across nodes?
Redis Cluster uses **16,384 Hash Slots**.
- Every key is assigned a slot: $\text{slot} = CRC16(\text{key}) \pmod{16384}$.
- The 16,384 slots are divided among the master nodes.
- **Hash Tags**: Wrapping part of a key in curly braces (e.g., `{user_100}:orders` and `{user_100}:profile`) forces both keys to evaluate hash on `user_100`, guaranteeing they land on the same node for multi-key operations.

---

## Section 3: Load Balancing & Reverse Proxies

### Q13: What is the difference between Layer 4 (L4) and Layer 7 (L7) Load Balancing?
- **L4 (Transport Layer)**:
  - Operates on IP addresses and TCP/UDP ports.
  - Does not decrypt or inspect packet payloads.
  - Blazing throughput, low CPU overhead (e.g., AWS NLB, Linux IPVS).
- **L7 (Application Layer)**:
  - Inspects HTTP headers, cookies, URL paths, and JSON payloads.
  - Performs intelligent routing (`/api/v1/auth` vs `/static`), SSL/TLS termination, and cookie-based sticky sessions.
  - Higher CPU overhead (e.g., AWS ALB, NGINX, Envoy).

### Q14: How does Consistent Hashing work and why is it superior to Modulo Hashing?
- Modulo hashing ($\text{hash}(\text{key}) \pmod N$) breaks completely when a node is added or removed, causing almost 100% of keys to re-map.
- **Consistent Hashing**:
  - Maps servers and keys onto a circular hash ring of size $2^{32}$.
  - A key is stored on the first server encountered moving clockwise.
  - Adding or removing a server only affects keys between that server and its counter-clockwise neighbor. **Only $\frac{1}{N}$ of keys move.**
  - **Virtual Nodes**: Physical nodes are assigned multiple positions on the ring to ensure statistically even key distribution and eliminate hot spots.

### Q15: What is Anycast BGP routing and how is it used in CDNs?
Anycast assigns the **identical IP address** to multiple servers in datacenters around the globe. Internet BGP routers automatically direct user packets to the topologically closest server. If a datacenter goes down, BGP withdraws the route, redirecting traffic to the next closest datacenter with zero DNS propagation latency.

### Q16: What is the purpose of TLS/SSL Termination at the Load Balancer?
Decryption of HTTPS traffic is performed at the load balancer instead of individual backend instances:
1. Offloads cryptographic compute from backend application servers.
2. Centralizes certificate renewal and management.
3. Allows the load balancer to inspect Layer 7 HTTP traffic for WAF security and intelligent routing.

---

## Section 4: Message Queues & Event Streaming (Kafka vs. RabbitMQ)

### Q17: What is the fundamental architectural difference between Apache Kafka and RabbitMQ?
- **RabbitMQ**: Traditional message broker (Smart broker, dumb consumer). Messages are queued, pushed to consumers, and deleted once acknowledged.
- **Kafka**: Distributed append-only commit log (Dumb broker, smart consumer). Messages are written sequentially to disk, retained for a configured duration, and consumers independently poll messages by tracking their own offsets.

### Q18: Explain RabbitMQ's 4 Exchange Types.
1. **Direct**: Exact match between routing key and binding key.
2. **Fanout**: Broadcasts every message to all bound queues unconditionally.
3. **Topic**: Pattern-matching using wildcards (`*` matches one word, `#` matches zero or more words).
4. **Headers**: Routes based on message header key-value attributes instead of routing keys.

### Q19: Why can Kafka achieve millions of messages per second?
1. **Sequential Disk I/O**: Kafka writes to append-only disk segments, matching sequential memory speeds.
2. **Zero-Copy Optimization**: Uses Linux OS `sendfile()` system call to transfer data directly from OS Page Cache to the network NIC, bypassing user-space CPU memory copying.
3. **OS Page Cache**: Offloads caching to the OS kernel rather than JVM heap, eliminating garbage collection pauses.
4. **End-to-End Batching**: Producers and consumers batch records, reducing network round-trips.

### Q20: How does Kafka guarantee message ordering?
Kafka guarantees strict ordering **only within a single partition**, NEVER across an entire topic.
All messages sharing the same key are routed to the same partition:
$$\text{Partition} = \text{MurmurHash2}(\text{Key}) \pmod{\text{Total Partitions}}$$
If global ordering across an entire topic is mandatory, that topic must have exactly **1 partition** (which limits write throughput to a single broker).

### Q21: What is a Kafka Consumer Group and what happens if consumers outnumber partitions?
A Consumer Group allows a pool of consumer instances to read from a topic in parallel. Each partition is assigned to exactly one consumer within the group.
If there are 4 partitions and 6 consumers in a group, **2 consumers will sit idle** with zero workload.

### Q22: Explain Kafka Delivery Guarantees: At-Most-Once, At-Least-Once, and Exactly-Once.
- **At-Most-Once**: Commit offset before processing. If worker crashes during processing, message is lost.
- **At-Least-Once**: Commit offset only after successful processing. If worker crashes, replacement consumer re-processes message (requires idempotent consumers).
- **Exactly-Once Semantics (EOS)**: Uses idempotent producers (`enable.idempotence=true`) with unique Producer IDs (PIDs) and sequence numbers to de-duplicate retries, combined with Kafka's transactional API across partitions.

### Q23: What is the Transactional Outbox Pattern and why is it needed?
Writing to a database and publishing an event to a message broker cannot be done in a single atomic transaction without 2PC.
- **The Outbox Solution**:
  1. The business update and an `outbox` event record are written to the database in a **single local ACID transaction**.
  2. A message relay (e.g., Debezium CDC or background poller) reads the `outbox` table and publishes the event to Kafka.
  3. Guarantees that an event is published if and only if the database write succeeded.

---

## Section 5: Database Indexing & Query Optimization

### Q24: Why do relational databases use B+ Trees instead of Binary Search Trees for indexes?
1. **High Branching Factor (Fan-out 100–1000)**: A B+ tree node fits an entire disk page (16 KB), keeping the tree height to only 3–4 levels even for billions of rows $\implies$ maximum 3–4 disk reads.
2. **Linked Leaf Nodes**: Leaf nodes form a doubly linked list, making range queries (`BETWEEN`, `>`, `<`) extremely fast without re-traversing the tree.
3. **Internal Nodes are Lightweight**: Internal nodes only store search keys and child pointers, packing more keys per page.

### Q25: What is the difference between a Clustered and a Secondary (Non-Clustered) Index?
- **Clustered Index**: Determines the physical on-disk order of table rows. The leaf nodes store the complete row data. There can be only **one** clustered index per table (typically the Primary Key).
- **Secondary Index**: A separate B+ tree whose leaf nodes store the indexed column(s) and the **clustering key (Primary Key)**.
  - To retrieve non-indexed columns, the database must perform a **Bookmark Lookup** (reading secondary index first, then traversing the clustered index to fetch the full row).

### Q26: What is a Covering Index (Index-Only Scan)?
A covering index contains all columns requested in `SELECT`, `WHERE`, and `ORDER BY`. The database engine retrieves all required data directly from the secondary index leaf node, skipping the clustered index table lookup completely, resulting in dramatic performance gains.

### Q27: Explain the Leftmost Prefix Rule for composite indexes.
An index on `(A, B, C)` is physically sorted by `A`, then `B`, then `C`.
- Queries filtering on `(A)`, `(A, B)`, or `(A, B, C)` use the index.
- Queries filtering on `(B)` or `(C)` **CANNOT** use the index because the leading prefix `A` is missing.
- In addition, index usage stops after the first range column (`>`, `<`, `BETWEEN`).

### Q28: How does an LSM-Tree work in write-heavy databases like Cassandra or RocksDB?
1. Writes append to an in-memory **MemTable** (Red-Black tree or SkipList) and an on-disk Write-Ahead Log (WAL) for durability.
2. When the MemTable fills up, it is flushed to disk as an immutable **SSTable (Sorted String Table)**.
3. Background **Compaction** merges overlapping SSTables and deletes tombstoned records.
4. Reads check MemTable, Bloom Filters, and SSTables sequentially. Writes are blazing fast because they require zero random disk seeks.

---

## Section 6: Database Sharding & Partitioning

### Q29: What is the difference between Horizontal Sharding and Vertical Partitioning?
- **Vertical Partitioning**: Splitting a database by domain/table (e.g., User DB vs Order DB) or moving large blob columns into separate tables.
- **Horizontal Sharding**: Splitting rows of the same table across multiple physical database instances based on a **Shard Key**.

### Q30: What are the main trade-offs of Range-Based vs. Hash-Based Sharding?
- **Range-Based**: Easy range scans; intuitive data organization. However, causes severe write hot spots when using auto-incrementing IDs or dates (all writes hit the newest shard).
- **Hash-Based**: Distributes writes evenly across all shards. However, range queries require querying every single shard (Scatter-Gather).

### Q31: How do you handle Distributed Primary Key Generation in a sharded database?
1. **Twitter Snowflake**: 64-bit ID combining timestamp (41 bits) + worker ID (10 bits) + sequence counter (12 bits). Naturally ordered, fits in `BIGINT`, generates millions of IDs/sec per node without coordination.
2. **UUIDv7**: 128-bit time-ordered UUID. Avoids B+ tree fragmentation issues of random UUIDv4.
3. **Ticket Server**: Centralized DB instance with auto-increment step offsets.

### Q32: What are the biggest operational challenges of Sharding?
1. **Cross-Shard Joins**: Joining tables across network-separated shards is prohibitively expensive; requires denormalization or application-layer joins.
2. **Cross-Shard Transactions**: Demands 2PC or Saga patterns, hurting latency and throughput.
3. **Re-sharding**: Adding shards requires live data migration without downtime.

---

## Section 7: Distributed Consistency & Transactions

### Q33: Explain the CAP Theorem and why "CA" is not possible in real-world networks.
Network partitions ($P$) are physically unavoidable due to cable cuts, switch failures, and packet delays.
When a network partition occurs, you must choose:
- **CP**: Refuse requests to maintain strong consistency across isolated nodes.
- **AP**: Accept writes/reads on both sides of the partition, allowing data to temporarily diverge.
Since $P$ is a physical fact of networking, a system cannot choose "CA" under network failure.

### Q34: What is the PACELC Theorem?
An extension of CAP describing normal operations:
- **If Partition ($P$)**: Choose between Availability ($A$) and Consistency ($C$);
- **Else ($E$)**: Choose between Latency ($L$) and Consistency ($C$).
Example: Cassandra is $PA/EL$ (Available on partition; prioritizes Latency over Consistency normally).

### Q35: What is Quorum Consensus ($W + R > N$) in Dynamo-style databases?
- $N$ = Replication factor, $W$ = Write acknowledgment count, $R$ = Read replica count.
- If $W + R > N$, the read quorum and write quorum must overlap by at least 1 node (Pigeonhole Principle).
- That overlapping node holds the latest write timestamp, guaranteeing strong consistency.

### Q36: Why is Two-Phase Commit (2PC) rarely used in high-scale microservices?
2PC is a blocking protocol. During Phase 1 (Prepare) and Phase 2 (Commit), participant databases hold locks on rows. If the Coordinator crashes midway, locks remain held indefinitely, causing massive queueing, thread starvation, and system-wide timeouts.

### Q37: How does the Saga Pattern handle failures in distributed transactions?
Instead of holding locks across services, a Saga executes a chain of independent local database transactions.
If any step fails, the Saga executes **Compensating Transactions** backward to undo previous successful steps (e.g., issuing a credit card refund if inventory reservation fails).

### Q38: What is the difference between Optimistic Concurrency Control (OCC) and Pessimistic Locking?
- **Pessimistic Locking**: Uses `SELECT ... FOR UPDATE` to lock database rows before reading/updating. Guarantees no conflicts, but limits concurrency and risks deadlocks.
- **Optimistic Concurrency Control (OCC)**: Does not lock during read. When writing, checks a `version` column (`WHERE id = 42 AND version = 3`). If version changed, write fails and application retries. Best for read-heavy, low-contention workloads.

---

## Section 8: System Design Scenarios & High-Concurrency Patterns

### Q39: How do you prevent inventory overselling in a flash sale with 100k concurrent requests?
1. Pre-load inventory counts into **Redis**.
2. Execute an atomic **Redis Lua script** to check and decrement stock in memory (`DECRBY`).
3. If stock drops below 0, reject immediately with HTTP 200 "Sold Out" (< 5 ms).
4. If successful, push the order event into a **Kafka** queue for asynchronous database persistence.
5. Workers process orders at a controlled rate and commit to the database using OCC.

### Q40: How would you design a Distributed Rate Limiter for an API gateway?
Use the **Token Bucket** or **Sliding Window Counter** algorithm implemented in Redis via Lua scripts:
- Key: `rate_limit:{user_id}:{minute_timestamp}`.
- Check current count and increment atomically.
- Set key TTL to window duration (e.g., 60 seconds).
- Return HTTP 429 Too Many Requests with `Retry-After` header when limit is exceeded.

### Q41: How do you design a system to handle the "Celebrity Problem" (Hot Key Problem)?
When an account with 100M followers posts an update, fan-out write approaches crash:
- **Hybrid Fan-Out**:
  - Regular users: **Fan-out on Write** (push to all followers' feeds).
  - Celebrity users: **Fan-out on Read** (do not push to 100M inboxes; instead, fetch celebrity's latest post on-demand when a follower opens their feed and merge in memory).
- **Cache Sharding**: Add random salt suffixes to the celebrity key in Redis (`celebrity_post:100:shard_1` through `shard_10`) and load balance reads across shards.

### Q42: What is the difference between WebSockets, Long Polling, and Server-Sent Events (SSE)?
- **Long Polling**: Client opens HTTP connection; server holds connection until new data is available, then closes. High HTTP header overhead for frequent updates.
- **SSE (Server-Sent Events)**: Unidirectional persistent connection over HTTP. Server pushes text/JSON streams to client. Best for live score tickers, stock quotes.
- **WebSockets**: Full-duplex, bidirectional TCP connection. Minimal frame overhead (2–10 bytes). Essential for interactive real-time chat, multiplayer gaming, collaborative editing.

### Q43: How do you implement Idempotency in payment processing APIs?
Client sends a unique `Idempotency-Key` (UUIDv4) in the HTTP header.
1. Backend checks Redis: `GET idemp:{key}`.
2. If found, returns the cached previous response.
3. If not found, sets `SET idemp:{key} "PROCESSING" NX PX 30000`.
4. Executes the payment with the gateway.
5. Updates Redis with final response: `SET idemp:{key} "{status: 'SUCCESS', charge_id: '...'}"`.

### Q44: What is Read-After-Write Consistency and how do you achieve it with Read Replicas?
When a user updates data on the Primary DB and immediately refreshes, replication lag to Read Replicas can cause them to see stale data.
- **Solutions**:
  1. Pin the user's read requests to the Primary DB for a 5–10 second cooldown window following any write.
  2. Track client write Log Sequence Number (LSN); if a replica hasn't reached that LSN, route to Primary.

### Q45: How would you design a high-throughput clickstream tracking pipeline?
`Clients` $\to$ `Edge CDN` $\to$ `API Gateway` $\to$ `Kafka Cluster` (partitioned by `session_id`) $\to$ `Flink / Spark Streaming` (real-time aggregation) $\to$ `ClickHouse / Snowflake` (OLAP analytical queries) + `S3 Data Lake` (raw Parquet archive).

### Q46: What is the difference between OLTP and OLAP databases?
- **OLTP (Online Transaction Processing)**: Row-oriented (PostgreSQL, MySQL). Optimized for frequent, low-latency CRUD operations on individual records using B+ trees.
- **OLAP (Online Analytical Processing)**: Columnar-oriented (ClickHouse, Snowflake, BigQuery, Redshift). Optimized for scanning billions of rows across a few columns to perform aggregations (`SUM`, `AVG`, `GROUP BY`).

### Q47: How does Snowflake ID generation prevent timestamp collisions within the same millisecond?
Snowflake IDs reserve 12 bits for a **Sequence Counter** ($2^{12} = 4,096$).
If multiple IDs are requested within the exact same millisecond on the same worker node, the sequence counter increments from 0 to 4,095. If the sequence counter exhausts 4,095 in a single millisecond, the generator spins and waits until the next millisecond.

### Q48: How do you design zero-downtime database schema migrations on large tables?
Never run raw `ALTER TABLE ... ADD COLUMN ...` on tables with millions of rows because it acquires an exclusive table lock:
- **Solution (Online Schema Change / gh-ost / pt-online-schema-change)**:
  1. Create a shadow table with the new schema.
  2. Attach triggers or read binlog to stream ongoing writes to both tables (dual-write).
  3. Backfill historical data in small, throttled batches.
  4. Once caught up, perform an atomic table rename (`RENAME TABLE original TO old, shadow TO original`).

### Q49: What is Backpressure and how do streaming systems handle it?
Backpressure occurs when a producer generates data faster than a downstream consumer can process it.
- **Mitigations**:
  1. **Buffering**: Queue messages in Kafka/RabbitMQ up to memory/disk limits.
  2. **Prefetch Limits**: Set consumer prefetch count in RabbitMQ.
  3. **Load Shedding / Rate Limiting**: Reject non-essential requests with HTTP 429 / 503.
  4. **Dynamic Flow Control**: Consumer signals producer to pause or reduce stream rate (Reactive Streams protocol).

### Q50: How do you perform Disaster Recovery in Multi-Region Distributed Systems?
- **Active-Passive (Warm/Cold Standby)**: Primary region serves 100% of traffic; database replicates asynchronously to secondary region. If primary fails, DNS/Anycast switches traffic to secondary. (Low cost, minor failover downtime).
- **Active-Active**: Both regions serve live traffic simultaneously.
  - Requires distributed databases (CockroachDB, Spanner) or multi-leader replication with conflict resolution.
  - Near-zero downtime failover during regional datacenter outages.

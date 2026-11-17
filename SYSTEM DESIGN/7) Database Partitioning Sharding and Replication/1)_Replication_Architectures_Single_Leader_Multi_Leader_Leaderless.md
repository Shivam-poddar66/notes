# 1) Replication Architectures: Single-Leader, Multi-Leader, Leaderless

## Executive Overview
When read traffic, high-availability demands, or geographical proximity exceed what a single database server can provide, **Replication** duplicates identical copies of the dataset across multiple network nodes.

---

## 1. Single-Leader (Primary-Replica / Master-Slave)

```text
       [ Write Requests ] ──────────▶ [ Primary Database (Leader) ]
                                                │
                        ┌───────────────────────┴───────────────────────┐
                        ▼ (Replication Stream: WAL / Binlog)            ▼
              [ Read Replica 1 ]                              [ Read Replica 2 ]
                        ▲                                               ▲
                        └───────────────── [ Read Requests ] ───────────┘
```

- **Write Path**: 100% of write traffic (`INSERT`, `UPDATE`, `DELETE`) goes exclusively to the **Primary**.
- **Read Path**: Read queries are distributed across multiple **Read Replicas** to scale read capacity horizontally.
- **Failover**: If the Primary dies, monitoring software (Orchestrator / Patroni) promotes a Replica to become the new Primary.

---

## 2. Synchronous vs. Asynchronous Replication Modes

```text
┌─────────────────────────┬───────────────────────────────┬─────────────────────────────┐
│ Mode                    │ Commit Behavior               │ Trade-off                   │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ Asynchronous            │ Primary commits locally and   │ Ultra-low write latency.    │
│                         │ returns immediately; replicates│ Risk of data loss if Primary│
│                         │ in background                 │ crashes before sync         │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ Synchronous             │ Primary waits for ALL replicas│ Zero data loss; guaranteed  │
│                         │ to confirm before committing  │ durability. But write pauses│
│                         │                               │ if any replica is slow/down │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ Semi-Synchronous        │ Primary waits for AT LEAST ONE│ Standard compromise (MySQL).│
│                         │ replica to acknowledge        │ High durability, bounded lat│
└─────────────────────────┴───────────────────────────────┴─────────────────────────────┘
```

---

## 3. Multi-Leader (Active-Active / Master-Master)
- Multiple databases in different geographic regions act as leaders, accepting both reads and writes locally.
- Leaders replicate writes to each other asynchronously across datacenters.
- **The Conflict Problem**: If User Alice updates her profile in US-East while User Bob updates Alice's profile in EU-Central simultaneously, writes conflict!
- **Conflict Resolution**:
  - **Last-Write-Wins (LWW)**: Uses wall-clock timestamps (risks data loss due to NTP clock drift).
  - **CRDTs (Conflict-free Replicated Data Types)**: Mathematically mergeable data types.

---

## 4. Leaderless Replication (Dynamo-Style: Cassandra, ScyllaDB)
- There is no designated master. Clients write to and read from multiple peer nodes concurrently.
- **Quorum Consensus Formula**:
  $$\mathbf{W + R > N}$$
  - $N$ = Total replicas.
  - $W$ = Number of write acknowledgments required.
  - $R$ = Number of read responses required.
- **Pigeonhole Principle**: The write node set and read node set must overlap by at least 1 node holding the latest write timestamp!
- **Anti-Entropy**: Uses background **Merkle Trees** and **Read Repair** to heal lagging replicas.

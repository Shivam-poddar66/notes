# 6) Redis Persistence (RDB/AOF) and High Availability

## Executive Overview
Although Redis is primarily an in-memory datastore, it provides persistence mechanisms to survive machine reboots and high-availability topologies for automatic failover.

---

## 1. Redis Persistence Mechanisms: RDB vs. AOF

```text
┌───────────────────────┬───────────────────────────┬───────────────────────────┐
│ Feature               │ RDB (Snapshotting)        │ AOF (Append Only File)    │
├───────────────────────┼───────────────────────────┼───────────────────────────┤
│ Mechanism             │ Point-in-time binary dump │ Append-only log of every  │
│                       │ to `dump.rdb` via fork()  │ write command received    │
├───────────────────────┼───────────────────────────┼───────────────────────────┤
│ Performance Impact    │ Child process handles I/O;│ Small write overhead;     │
│                       │ zero main-thread block    │ fsync policy controls I/O │
├───────────────────────┼───────────────────────────┼───────────────────────────┤
│ Recovery Speed        │ Ultra-fast (direct memory │ Slower (must replay every │
│                       │ binary restoration)       │ command sequentially)     │
├───────────────────────┼───────────────────────────┼───────────────────────────┤
│ Data Loss Risk        │ Loses all writes since    │ Max 1 second data loss    │
│                       │ last snapshot (e.g. 5 min)│ (with `appendfsync everysec`)
├───────────────────────┼───────────────────────────┼───────────────────────────┤
│ File Size             │ Highly compressed binary  │ Larger; requires periodic │
│                       │ file                      │ background rewriting      │
└───────────────────────┴───────────────────────────┴───────────────────────────┘
```

> **Recommended Production Setting**: **Hybrid Persistence (`aof-use-rdb-preamble yes`)**.
> When rewriting the AOF file, Redis writes an RDB binary snapshot as the base, and appends only recent incremental commands in AOF format. This combines the ultra-fast recovery of RDB with the near-zero data loss of AOF.

---

## 2. High Availability Topologies

### A. Redis Sentinel (Master-Replica with Failover)
- Best for systems that fit within a single master's RAM.
- A quorum of **Sentinel processes** monitor the Master and Replicas.
- If Master dies, Sentinels vote via Raft-like consensus, elect a Replica as the new Master, and reconfigure clients automatically.

### B. Redis Cluster (Horizontal Sharding)
- When data exceeds a single server's RAM (e.g., > 128 GB) or write throughput exceeds 100k QPS.
- Distributes data across **16,384 Hash Slots**:
  $$\text{Slot} = CRC16(\text{Key}) \pmod{16384}$$
- Each master node owns a subset of slots (e.g., Master 1: 0–5460, Master 2: 5461–10922, Master 3: 10923–16383).
- **Hash Tags**: Putting braces around keys (`{user_101}:cart` and `{user_101}:profile`) guarantees they map to the same slot, allowing multi-key commands and atomic Lua scripts.

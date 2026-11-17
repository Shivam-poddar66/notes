# 3) LSM-Trees: MemTable, SSTables, and Compaction

## Executive Overview
While B+ Trees are optimized for read-heavy relational workloads, **Log-Structured Merge-Trees (LSM-Trees)** are engineered for ultra-high write throughput. They form the core storage engine of NoSQL systems including **Apache Cassandra, RocksDB, ScyllaDB, and Google Bigtable**.

---

## 1. The LSM-Tree Write Path

```text
[ Client Write: PUT (Key, Value) ]
               │
               ├── 1. Append sequentially to WAL (Write-Ahead Log on disk)
               │      (Guarantees durability with zero random seeks)
               ▼
       [ 2. MemTable (In RAM) ]
       (Sorted in-memory data structure: SkipList / Red-Black Tree)
               │
               ▼ (When MemTable reaches 64 MB threshold, flush to disk)
       [ 3. SSTable Level 0 (Immutable file on disk) ]
       [ SSTable Level 1 ]
       [ SSTable Level 2 ]
```

### Components:
1. **MemTable**: An in-memory write buffer maintaining keys in sorted order. Writes complete in microseconds.
2. **WAL (Write-Ahead Log)**: Sequential disk append log used to restore the MemTable if the machine loses power.
3. **SSTable (Sorted String Table)**: An immutable on-disk file containing sorted key-value pairs. Once written, an SSTable is never modified in-place! Deletes are written as special **Tombstone markers**.

---

## 2. Background Compaction & Bloom Filters

### Compaction:
Over time, multiple SSTables accumulate duplicate keys and tombstones. A background process called **Compaction** reads multiple SSTables, performs an external merge sort (like mergesort), discards old overwritten values and deleted tombstones, and writes a newly compacted SSTable file.

### Bloom Filters (Read Optimization):
- Because an immutable SSTable cannot be updated, a read query for a key might have to search the MemTable and dozens of SSTables on disk (**Read Amplification**).
- To solve this, every SSTable has an accompanying **Bloom Filter** in RAM:
  - If the Bloom filter says a key is **absent**, the engine completely skips reading that SSTable file from disk!

---

## 3. B+ Tree vs. LSM-Tree Comparison Matrix

```text
┌─────────────────────────┬───────────────────────────────┬─────────────────────────────┐
│ Dimension               │ B+ Tree (MySQL / Postgres)    │ LSM-Tree (Cassandra/RocksDB)│
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ Primary Workload        │ Read-Heavy (OLTP)             │ Write-Heavy (High Ingestion)│
│ Write Mechanism         │ In-place random page updates  │ Sequential appends to log   │
│ Read Latency            │ Predictable (3–4 page reads)  │ Slower (checks multiple SST)│
│ Space Amplification     │ Fragmentation in page splits  │ Low (Compacted sequentially)│
│ Write Amplification     │ High (writes 16KB for 1 row)  │ Moderate (Compaction runs)  │
└─────────────────────────┴───────────────────────────────┴─────────────────────────────┘
```

# 5) Consistent Hashing and Dynamic Resharding

## Executive Overview
When an existing sharded database reaches capacity, adding new shards requires re-distributing data without taking the production database offline. Using naive modulo hashing forces a total cluster rewrite; **Consistent Hashing with Virtual Nodes** enables smooth incremental scaling.

---

## 1. Minimal Data Movement via Consistent Hashing

When using Consistent Hashing on a $2^{32}$ circular ring:
- Shards are assigned hundreds of virtual token positions across the ring.
- Data rows map to the next physical shard token clockwise.

```text
[ Existing Shards: A, B, C ]
Adding New Shard D:
* Shard D claims virtual tokens evenly across the ring.
* Only 1 / (N + 1) of the total dataset must be migrated!
* With 4 shards becoming 5 shards: Only 20% of data moves; 80% remains untouched!
```

---

## 2. Zero-Downtime Live Resharding Architecture (Dual-Write Pattern)

How do you migrate 50 Terabytes of database rows to new shards while processing 20,000 live writes/sec without dropping a single write?

```text
PHASE 1: Dual-Writing
[ Application ] ──▶ Write to OLD Shards (Source of Truth)
               └──▶ Write to NEW Shards (Silently ignore errors)

PHASE 2: Historical Backfill
[ Backfill Worker ]
└── Copies historical records from Old Shards to New Shards in small, throttled batches.

PHASE 3: Consistency Verification
[ Reconciliation Tool ]
└── Compares data between Old and New shards, fixing missing or mismatched rows.

PHASE 4: Cutover (Atomic Flip)
[ Application ] ──▶ Switch Reads and Writes to NEW Shards as Source of Truth!

PHASE 5: Decommission Old Shards
```

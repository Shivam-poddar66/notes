# 6) Distributed Primary Key Generation: Snowflake and UUIDv7

## Executive Overview
In a single database, auto-incrementing integer IDs (`AUTO_INCREMENT` or `SERIAL`) generate unique primary keys trivially. Across 50 sharded databases, two independent shards will both generate `id = 1`, causing catastrophic primary key collisions.

---

## 1. Comparing Distributed ID Approaches

```text
┌─────────────────────────┬──────────────┬───────────────┬─────────────────────────────┐
│ Strategy                │ Size (Bits)  │ Sortable?     │ Main Limitation / Trade-off │
├─────────────────────────┼──────────────┼───────────────┼─────────────────────────────┤
│ Auto-Increment Offset   │ 64-bit       │ Yes           │ Hardcoded step size; brittle│
│ UUIDv4 (Random)         │ 128-bit      │ NO (Random!)  │ B+ Tree page fragmentation! │
│ UUIDv7 (Time-Ordered)   │ 128-bit      │ Yes (Time)    │ 16-byte storage overhead    │
│ Twitter Snowflake       │ 64-bit       │ Yes (Time)    │ Requires worker coordination│
└─────────────────────────┴──────────────┴───────────────┴─────────────────────────────┘
```

---

## 2. Twitter Snowflake (64-Bit Time-Ordered ID)

Snowflake generates unique 64-bit integers that fit into a standard SQL `BIGINT`.

```text
 1 Bit       41 Bits (Timestamp in ms)        10 Bits (Worker)  12 Bits (Sequence)
┌─────┬──────────────────────────────────────┬────────────────┬──────────────────┐
│  0  │ Milliseconds since custom epoch (~69y│ Datacenter/Node│ 0 to 4095 per ms │
└─────┴──────────────────────────────────────┴────────────────┴──────────────────┘
```

### Snowflake Bit Breakdown:
1. **1 Bit (Sign)**: Always 0 (keeps integers positive).
2. **41 Bits (Timestamp)**: Milliseconds elapsed since a custom epoch (e.g., Jan 1, 2026). Provides $2^{41} \approx \mathbf{69.7\text{ years}}$ of IDs.
3. **10 Bits (Worker Node ID)**: Allows $2^{10} = \mathbf{1,024\text{ distinct worker machines}}$ or containers.
4. **12 Bits (Sequence Counter)**: Increments from 0 to $2^{12} - 1 = \mathbf{4,095}$ within the same millisecond on a single worker node.
   - **Throughput**: $1,024\text{ workers} \times 4,096\text{ IDs/ms} = \mathbf{4.19\text{ Million IDs per second}}$ with **zero network communication** between nodes!

---

## 3. The UUIDv4 Disaster in B+ Trees vs. UUIDv7

### Why UUIDv4 Destroys B+ Tree Performance:
UUIDv4 is 128 bits of pure pseudorandom data. When inserted into a B+ tree index:
- Rows are inserted into random leaf pages across disk.
- Causes constant **B+ Tree Page Splits** and severe page fragmentation.
- Buffer pool cache hit rate plunges to near zero because every insert reads a random page from disk.

### The Solution: UUIDv7:
- UUIDv7 combines a **48-bit UNIX millisecond timestamp** with 74 bits of randomness.
- Because the leading 48 bits are monotonically increasing, new rows are always appended to the **rightmost leaf page** of the B+ tree, eliminating page splits!

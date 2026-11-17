# 1) Storage Engine Internals: Pages, Blocks, and Buffer Pool

## Executive Overview
Relational database management systems (RDBMS) like MySQL InnoDB and PostgreSQL do not manipulate individual bytes or single rows directly on disk. All disk I/O, cache buffering, and locking occurs at the granularity of fixed-size storage units called **Pages** or **Blocks**.

---

## 1. Page Anatomy & Layout

- **MySQL InnoDB**: Default page size is **16 KB**.
- **PostgreSQL**: Default page size is **8 KB**.

```text
┌────────────────────────────────────────────────────────┐
│                   Database Page (16 KB)                │
├───────────────────┬────────────────────────────────────┤
│ Page Header       │ Page ID, LSN (Log Sequence Number),│
│ (38–56 Bytes)     │ Free space offset, Checksum        │
├───────────────────┼────────────────────────────────────┤
│ User Records      │ Row 1: [Col1 | Col2 | Col3...]     │
│ (Grows Downward)  │ Row 2: [Col1 | Col2 | Col3...]     │
│                   │ Row 3: [Col1 | Col2 | Col3...]     │
├───────────────────┼────────────────────────────────────┤
│ Free Space        │ Unallocated gap between records    │
│                   │ and slot directory                 │
├───────────────────┼────────────────────────────────────┤
│ Slot Directory    │ Array of 2-byte pointers pointing  │
│ (Grows Upward)    │ to row offsets (enables binary s.) │
├───────────────────┼────────────────────────────────────┤
│ Page Trailer      │ Checksum for corruption validation │
└───────────────────┴────────────────────────────────────┘
```

---

## 2. The Buffer Pool (Database RAM Cache)

The **Buffer Pool** (PostgreSQL Shared Buffers) is a dedicated region in physical RAM where disk pages are cached.

```text
[ SQL Read Query ] ──▶ Check Buffer Pool in RAM ──▶ Hit? Return row in < 10 µs!
                              │
                              ▼ Miss (Page not in RAM)
                      [ Load 16 KB Page from Disk into Buffer Pool ]
```

### Dirty Pages & Write-Ahead Logging (WAL):
When an `UPDATE` or `INSERT` query executes:
1. The database modifies the page in RAM (**Dirty Page**).
2. It does **NOT** write the modified 16 KB page to disk immediately (that would require slow random disk I/O).
3. Instead, it appends a small binary delta to the sequential **Write-Ahead Log (WAL / Redo Log)** on disk.
4. The transaction commits safely.
5. In the background, a checkpoint thread flushes dirty pages in bulk to disk sequentially.

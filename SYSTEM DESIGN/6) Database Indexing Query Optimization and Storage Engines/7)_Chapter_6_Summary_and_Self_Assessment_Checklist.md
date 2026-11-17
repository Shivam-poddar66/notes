# 7) Chapter 6 Summary & Self-Assessment Checklist

## Chapter Summary
1. **Database Page Model**: Disk I/O operates on fixed pages (16 KB in InnoDB). In-place updates modify pages in the Buffer Pool and append changes to the Write-Ahead Log (WAL).
2. **B+ Tree Strengths**: High fan-out (branching factor 100–1,000) keeps tree height to 3–4 levels for billions of records. Doubly linked leaf nodes allow linear range scans without tree re-traversal.
3. **LSM-Tree Strengths**: Engineered for high write ingestion (Cassandra, RocksDB). Writes append sequentially to MemTable and immutable SSTables; background compaction merges files.
4. **Clustered vs. Secondary**: The Clustered index stores full row data in leaf pages. Secondary indexes store indexed columns plus the Primary Key, incurring a bookmark lookup unless designed as a Covering Index.
5. **Leftmost Prefix Rule**: In composite index `(A, B, C)`, queries must filter by `A` to use the index, and index filtering halts after the first range condition.
6. **Query Optimization**: High selectivity justifies index usage. Avoid functions on indexed columns, implicit type conversions, and leading wildcards.

---

## 🎯 Chapter 6 Self-Assessment Checklist

- [ ] What is the default page size of MySQL InnoDB, and why doesn't it write modified rows directly to disk immediately?
- [ ] Calculate the maximum height of a B+ tree indexing 1 billion rows with a fan-out of 500.
- [ ] Why do leaf nodes in a B+ tree form a doubly linked list?
- [ ] Explain how an LSM-Tree handles database deletes using Tombstones.
- [ ] What is a Bookmark Lookup and how does a Covering Index eliminate it?
- [ ] Given an index on `(status, created_at, user_id)`, will `WHERE created_at = '2026-01-01'` use the index? Why or why not?
- [ ] In the query `WHERE status = 'PAID' AND age > 21 AND city = 'NYC'`, why can't `city` be filtered via the index if ordered `(status, age, city)`?
- [ ] What is Selectivity, and why will a database query optimizer perform a full table scan on a boolean column index?
- [ ] Explain the difference between access types `range`, `ref`, and `ALL` in `EXPLAIN`.

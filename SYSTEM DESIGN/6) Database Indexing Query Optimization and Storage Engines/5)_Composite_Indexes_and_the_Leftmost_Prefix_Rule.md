# 5) Composite Indexes and the Leftmost Prefix Rule

## Executive Overview
A composite index (multi-column index) indexes multiple columns in a single B+ tree. Incorrect column ordering in a composite index makes the index completely unusable by the query planner.

---

## 1. Physical Sorting Mechanics of a Composite Index

Suppose we create a composite index on 3 columns:
```sql
CREATE INDEX idx_status_created_user ON orders(status, created_at, user_id);
```

The B+ tree physically orders entries:
1. First, strictly by **`status`**.
2. Within rows having the identical `status`, sorted by **`created_at`**.
3. Within rows having the identical `status` AND `created_at`, sorted by **`user_id`**.

```text
INDEX LEAF ORDERING:
('CANCELLED', '2026-01-01', 10)
('CANCELLED', '2026-01-02', 15)
('PAID',      '2026-01-01', 5)
('PAID',      '2026-01-01', 9)
('PAID',      '2026-01-03', 42)
```

---

## 2. The Leftmost Prefix Rule

A composite index on `(A, B, C)` can only be used by queries that filter on a contiguous prefix starting from the leftmost column:

```text
┌────────────────────────────────────────────────────────┬────────────────┐
│ Query Filter Condition                                │ Index Hit?     │
├────────────────────────────────────────────────────────┼────────────────┤
│ WHERE status = 'PAID'                                  │ YES (Uses Col 1│
│ WHERE status = 'PAID' AND created_at = '2026-01-01'    │ YES (Cols 1 & 2│
│ WHERE status = 'PAID' AND created_at = '...' AND user=5│ YES (All 3)    │
│ WHERE created_at = '2026-01-01'                        │ NO! (Col 1 miss│
│ WHERE user_id = 5                                      │ NO! (Col 1 miss│
│ WHERE status = 'PAID' AND user_id = 5                  │ PARTIAL (Uses 1│
└────────────────────────────────────────────────────────┴────────────────┘
```

> **Why?** Just like a telephone phonebook sorted by `(LastName, FirstName)`: You cannot search for people named "John" without knowing their last name!

---

## 3. The Range Condition Stopping Rule

In a composite index, **index filtering stops after the FIRST range condition (`>`, `<`, `BETWEEN`, `LIKE 'abc%'`)**:

### Example:
Query:
```sql
SELECT * FROM orders 
WHERE status = 'PAID' 
  AND created_at > '2026-01-01' 
  AND user_id = 42;
```

```text
1. `status = 'PAID'`: Uses index (equality lookup).
2. `created_at > '2026-01-01'`: Uses index (range scan along leaf linked list).
3. `user_id = 42`: CANNOT use the index!
```
- Because rows where `created_at > '2026-01-01'` have different dates, their `user_id` values are scattered randomly! The database engine must examine every row in the date range to check `user_id`.

### Golden Rule of Column Ordering in Composite Indexes:
1. **Place strict equality columns first** (`col = value`).
2. **Place sort columns second** (`ORDER BY col`).
3. **Place range columns last** (`col > value`).

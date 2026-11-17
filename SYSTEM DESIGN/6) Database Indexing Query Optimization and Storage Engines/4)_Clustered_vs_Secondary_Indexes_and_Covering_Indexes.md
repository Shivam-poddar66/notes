# 4) Clustered vs. Secondary Indexes and Covering Indexes

## Executive Overview
Understanding the physical layout of indexes is the key to diagnosing query latency. A query that requires secondary index lookups can be 10x slower than an index-only covering query.

---

## 1. Clustered Index (Primary Key Index)

- A **Clustered Index** dictates the **physical on-disk ordering** of table rows.
- The leaf node of the clustered index B+ tree **contains the complete data row**.
- A table can have **only ONE clustered index**.
- In MySQL InnoDB, the Primary Key is always the clustered index. If no primary key is declared, InnoDB selects the first non-null unique index or synthesizes an internal 6-byte row ID.

```text
CLUSTERED INDEX LEAF PAGE:
[ Key: 101 | Data: { id: 101, name: 'Alice', email: 'alice@x.com', balance: 500.00 } ]
[ Key: 102 | Data: { id: 102, name: 'Bob',   email: 'bob@x.com',   balance: 750.00 } ]
```

---

## 2. Secondary (Non-Clustered) Index & Bookmark Lookups

A **Secondary Index** is a separate B+ tree built on non-primary columns (e.g., `CREATE INDEX idx_email ON users(email);`).
- The leaf node of a secondary index does **NOT** contain the full row data.
- It stores only: `[ Indexed Column Value | Primary Key (Clustering Key) ]`.

### The Bookmark Lookup Penalty (Double B+ Tree Traversal):
When querying: `SELECT balance FROM users WHERE email = 'alice@x.com';`

```text
Step 1: Traverse Secondary Index B+ Tree on `email`
        └── Leaf found: { email: 'alice@x.com', id: 101 }

Step 2: BOOKMARK LOOKUP (Traverse Clustered Index B+ Tree!)
        └── Using Primary Key `id = 101`, traverse Clustered B+ Tree
            └── Leaf found: Full row { id: 101, ..., balance: 500.00 }
```
*Result: The engine performs two full B+ tree traversals and double the disk page reads to fetch a single row!*

---

## 3. Covering Indexes (Index-Only Scans)

A **Covering Index** occurs when all columns requested in `SELECT`, `WHERE`, and `ORDER BY` exist within the secondary index itself.

### Example Optimization:
```sql
-- Create composite index covering both search and return columns
CREATE INDEX idx_email_balance ON users(email, balance);

-- Execute query
SELECT balance FROM users WHERE email = 'alice@x.com';
```

```text
EXECUTION PATH (Index-Only Scan):
Step 1: Traverse Secondary Index B+ Tree on `(email, balance)`
        └── Leaf found: { email: 'alice@x.com', balance: 500.00, id: 101 }
Step 2: RETURN IMMEDIATELY! (Bookmark lookup is SKIPPED!)
```

> **Performance Impact**: Index-Only scans eliminate all random disk I/O to the main table pages, cutting query execution latency from 50 ms to sub-millisecond speeds.

# 3) Vertical Partitioning vs. Horizontal Sharding

## Executive Overview
While Replication duplicates data across multiple servers, **Partitioning** and **Sharding** divide large datasets into smaller, manageable subsets distributed across distinct hardware instances.

---

## 1. Comparing Partitioning Dimensions

```text
VERTICAL PARTITIONING (Split by Columns / Tables):
Table: Users [ id | username | email | password_hash | bio_text | avatar_blob ]
                                   │
               ┌───────────────────┴───────────────────┐
               ▼                                       ▼
    [ Fast Core Table (RAM) ]                [ Heavy Auxiliary Table (Disk) ]
    [ id | username | email | pass ]         [ user_id | bio_text | avatar_blob ]

HORIZONTAL SHARDING (Split by Rows across Servers):
Table: Orders (100 Million Rows)
├── Shard 1 (Server A): Orders 1 to 25 Million
├── Shard 2 (Server B): Orders 25,000,001 to 50 Million
├── Shard 3 (Server C): Orders 50,000,001 to 75 Million
└── Shard 4 (Server D): Orders 75,000,001 to 100 Million
```

---

## 2. When to Use Which?

| Dimension | Vertical Partitioning | Horizontal Sharding |
| :--- | :--- | :--- |
| **Separation Axis** | Columns or Tables | Rows of the same table |
| **Primary Driver** | Wide tables; mixed access patterns | Massive row volume exceeding disk or write limits |
| **Cross-Node Joins** | Usually straightforward via foreign keys | Extremely slow and difficult across network shards |
| **Application Impact**| Minimal; often handled by ORM schema | High; application or proxy must route via Shard Key |
| **Scale Ceiling** | Limited to number of columns/domains | Near-infinite horizontal scale |

### Vertical Partitioning Best Practice:
Move large `TEXT`, `BLOB`, or `JSON` columns into a separate table or object store (S3). This allows the primary table's 16 KB pages to hold hundreds of rows rather than 2 or 3, dramatically increasing Buffer Pool cache efficiency in RAM.

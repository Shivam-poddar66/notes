# 11) SQL vs NoSQL vs Analytics (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Correct boundary between relational, NoSQL, and analytics services.
- How to avoid common model mismatch mistakes.

## 1. SQL model
Typical services: Cloud SQL, Spanner.

Characteristics:
- Structured schema.
- Relational joins.
- ACID transactions.
- Strong transactional semantics.

Best fit:
- Order systems,
- financial records,
- inventory,
- transactional app backends.

## 2. Document NoSQL model
Typical service: Firestore.

Characteristics:
- Flexible schema.
- Document-centric reads/writes.
- Fast app iteration and serverless development patterns.

Best fit:
- mobile and web app profiles,
- content metadata,
- app state and user-centric data models.

## 3. Wide-column NoSQL model
Typical service: Bigtable.

Characteristics:
- Very high throughput.
- Key-range and time-series style access patterns.
- Large-scale serving workloads.

Best fit:
- telemetry streams,
- clickstream,
- very large key-based data serving.

## 4. Analytics warehouse model
Typical service: BigQuery.

Characteristics:
- Large analytical scans and aggregations.
- SQL for reporting and BI.
- Not intended as OLTP app database.

Best fit:
- dashboards,
- historical analysis,
- ad-hoc analytical SQL.

## 5. Practical selection rules
1. Need relational joins and transactions? -> SQL services.
2. Need flexible document model for app backend? -> Firestore.
3. Need very high key-range throughput? -> Bigtable.
4. Need large-scale analytics queries? -> BigQuery.

## 6. Hybrid pattern (common in production)
- OLTP in Cloud SQL/Spanner/Firestore/Bigtable.
- Raw files and exports in Cloud Storage.
- Analytics in BigQuery.
- Cache in Memorystore.

## 7. Common mistakes
- Running transactional workload directly on BigQuery.
- Forcing relational joins into document model without redesign.
- Choosing Bigtable without key design discipline.
- Treating one service as solution for all data workloads.

## 8. Exam cues
- "transactional" -> SQL.
- "document" -> Firestore.
- "massive key-based throughput" -> Bigtable.
- "analytics SQL warehouse" -> BigQuery.

## 9. One-line memory hook
SQL serves transactional relationships, NoSQL serves scale and flexibility patterns, analytics warehouse serves large query and reporting workloads.

# 10) Service Selection Matrix (Exam Quick Reference)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Fast comparison framework to pick the correct storage/database service in scenario questions.
- Constraint-first matrix and elimination method.

## 1. Primary matrix

| Requirement | Best-fit service | Why |
|---|---|---|
| Object files, backups, static assets | Cloud Storage | Durable object storage with class/lifecycle controls |
| VM durable boot/data block volume | Persistent Disk | Managed block storage for Compute Engine |
| Shared NFS across VM clients | Filestore | Managed shared file service |
| Managed relational OLTP database | Cloud SQL | SQL transactions with managed operations |
| Global-scale relational strong consistency | Spanner | Relational + strong consistency at very high scale |
| Serverless document app data | Firestore | Flexible document model with auto scaling |
| Massive key-range low-latency throughput | Bigtable | Wide-column high-scale serving model |
| Large SQL analytics and BI | BigQuery | Serverless analytical warehouse |
| Reduce repeated DB read latency | Memorystore | In-memory caching layer |

## 2. Tie-breaker rules
1. Data model fit (object, relational, document, wide-column, analytics, cache).
2. Transaction vs analytics boundary.
3. Scale and latency constraints.
4. Reliability and compliance constraints.
5. Operational complexity and cost.

## 3. Elimination pattern
- Remove options that do not match data model.
- Remove options that violate transactional/analytical intent.
- Remove options that exceed required complexity.
- Select simplest valid option meeting hard constraints.

## 4. Common pairwise comparisons
- Cloud SQL vs Spanner:
  - Cloud SQL for common managed relational workloads.
  - Spanner for global-scale strong-consistency relational requirements.
- Firestore vs Bigtable:
  - Firestore for document app patterns.
  - Bigtable for very high throughput key-range patterns.
- Cloud Storage vs Filestore:
  - Cloud Storage for object data.
  - Filestore for shared filesystem semantics.
- BigQuery vs Cloud SQL:
  - BigQuery for analytics.
  - Cloud SQL for transactional app backend.

## 5. Quick exam cues
- "minimum ops + files" -> Cloud Storage.
- "SQL app transactions" -> Cloud SQL.
- "global relational consistency" -> Spanner.
- "event/mobile app document data" -> Firestore.
- "PB analytics SQL" -> BigQuery.

## 6. One-line memory hook
Pick by access pattern and data model first, then validate reliability, security, and cost.

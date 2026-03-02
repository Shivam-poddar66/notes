# 1) Core Mental Model (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- A simple way to choose the correct storage or database service in GCP.
- Core tradeoffs: data model, consistency, latency, scale, and operations.
- A practical decision workflow for exam and real architecture scenarios.

## 1. Why this model matters
Most wrong service choices happen because people start with product names instead of workload shape.  
Start with data and access pattern first, then choose the managed service that matches those constraints.

## 2. Storage and database categories

### Storage models
- Object storage: Cloud Storage.
- Block storage: Persistent Disk, Local SSD.
- File storage: Filestore.

### Database models
- Relational OLTP: Cloud SQL, Spanner.
- Document NoSQL: Firestore.
- Wide-column NoSQL: Bigtable.
- Analytics warehouse: BigQuery.
- Cache layer: Memorystore.

## 3. The 7-question decision framework
1. What is the primary access pattern?
   - File/object read-write, transactional row operations, document reads, key-range reads, or analytical scans.
2. Is the workload transactional or analytical?
   - OLTP and analytics should usually be separated.
3. What consistency model is required?
   - Strong relational transactions vs eventually consistent patterns where acceptable.
4. What scale is expected?
   - Small/medium app scale, very high throughput, or global-scale relational requirements.
5. What latency target is required?
   - Millisecond online serving vs batch/interactive analytics.
6. What operational model is preferred?
   - Lowest ops serverless model or managed-but-configurable database model.
7. What reliability, security, and cost limits apply?
   - RTO/RPO, compliance boundaries, private access, and budget controls.

## 4. Fast mapping cheatsheet
- "Files, backups, static assets" -> Cloud Storage.
- "VM boot or block volume" -> Persistent Disk.
- "Shared filesystem for multiple VMs" -> Filestore.
- "Managed SQL app DB" -> Cloud SQL.
- "Global-scale relational consistency" -> Spanner.
- "Flexible document app data" -> Firestore.
- "Massive key-based throughput" -> Bigtable.
- "Large SQL analytics" -> BigQuery.
- "Reduce DB read pressure" -> Memorystore.

## 5. Architecture layering pattern
Use layered data architecture:
- Online transactional tier: Cloud SQL / Spanner / Firestore / Bigtable.
- Cache tier: Memorystore.
- Object data and backup tier: Cloud Storage.
- Analytics tier: BigQuery.

This separation avoids overloading a transactional database with analytics traffic.

## 6. Constraint precedence rule
When constraints conflict, prioritize in this order:
1. Compliance and data residency.
2. Correct data model and consistency.
3. Reliability targets (RTO/RPO).
4. Performance/SLO.
5. Cost and operational simplicity.

## 7. Exam elimination method
1. Remove options that do not match data model.
2. Remove options that fail transaction vs analytics requirement.
3. Remove options violating scale or latency constraints.
4. Remove options that add unnecessary operational complexity.
5. Choose the simplest remaining option that meets all hard requirements.

## 8. Common mistakes
- Picking BigQuery for OLTP transactions.
- Picking Cloud SQL for very high scale key-range telemetry workloads.
- Treating Cloud Storage as relational database.
- Ignoring restore testing in reliability planning.
- Ignoring data transfer cost in cross-region designs.

## 9. Quick checklist
- Data model identified.
- Access pattern identified.
- Transaction vs analytics separated.
- Security and network posture defined.
- Backup and restore plan tested.
- Cost and scale assumptions validated.

## 10. One-line memory hook
Choose by data shape and query shape first, then optimize security, reliability, performance, and cost.

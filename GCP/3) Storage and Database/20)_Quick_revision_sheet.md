# 20) Quick Revision Sheet (Detailed)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Service one-liners
- Cloud Storage: object files, backups, static data.
- Persistent Disk: durable VM block storage.
- Filestore: shared NFS file storage.
- Cloud SQL: managed relational OLTP.
- Spanner: global-scale relational strong consistency.
- Firestore: serverless document NoSQL.
- Bigtable: massive wide-column key-range serving.
- BigQuery: serverless analytical SQL warehouse.
- Memorystore: in-memory cache layer.

## 2. Fast keyword map
- "files/backups" -> Cloud Storage.
- "SQL transactions" -> Cloud SQL.
- "global relational consistency" -> Spanner.
- "document app data" -> Firestore.
- "high-throughput key-range" -> Bigtable.
- "analytics SQL" -> BigQuery.
- "shared VM filesystem" -> Filestore.
- "reduce DB latency/load" -> Memorystore.

## 3. Decision order
1. Data model fit.
2. Access/query pattern.
3. Transaction vs analytics boundary.
4. Reliability and security constraints.
5. Cost and operations simplicity.

## 4. Security reminders
- Least privilege IAM everywhere.
- Secret Manager for credentials.
- Private connectivity preferred for databases.
- Audit and alerting for access/policy changes.

## 5. Reliability reminders
- Define RTO/RPO.
- Backups plus restore testing.
- Region strategy aligned to continuity requirement.

## 6. Cost reminders
- Right storage class and lifecycle.
- Query optimization to reduce scan waste.
- Co-locate services to reduce egress.

## 7. Exam trap reminders
- Do not pick BigQuery for OLTP.
- Do not ignore data model mismatch.
- Do not assume backup alone means DR ready.

## 8. Final rule
Choose the simplest service that fully satisfies data model, scale, security, reliability, and cost requirements.

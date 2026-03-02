# 17) Common Mistakes And Trap Options (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- High-frequency mistakes in storage/database architecture and exam answers.
- Correction patterns for each trap.

## 1. Model mismatch mistakes
- Mistake: BigQuery for OLTP.
  - Fix: use Cloud SQL/Spanner/Firestore/Bigtable based on serving model.

- Mistake: Cloud Storage as transactional row store.
  - Fix: use database service for transactional/query requirements.

- Mistake: Firestore for relational join-heavy design without redesign.
  - Fix: choose relational service when relational behavior is essential.

## 2. Scale mismatch mistakes
- Mistake: Cloud SQL for very high-scale globally distributed relational need.
  - Fix: evaluate Spanner.

- Mistake: Firestore for extreme key-range telemetry throughput.
  - Fix: evaluate Bigtable.

## 3. Reliability mistakes
- Mistake: backup configured but never restored in test.
  - Fix: enforce restore drills.

- Mistake: no explicit RTO/RPO targets.
  - Fix: define targets before architecture.

- Mistake: single-region assumption for critical workloads.
  - Fix: add regional/multi-region continuity design if required.

## 4. Security mistakes
- Mistake: broad IAM roles for data workloads.
  - Fix: least privilege and role separation.

- Mistake: public exposure by default.
  - Fix: private connectivity unless public access is explicit requirement.

- Mistake: secrets in code/config.
  - Fix: Secret Manager and rotation process.

## 5. Performance mistakes
- Mistake: scale resources before tuning query/data model.
  - Fix: bottleneck-first analysis.

- Mistake: ignore index/key design.
  - Fix: design around real query and access paths.

- Mistake: run analytics against OLTP system.
  - Fix: separate analytics into BigQuery.

## 6. Cost mistakes
- Mistake: wrong storage class for access pattern.
  - Fix: class and lifecycle alignment.

- Mistake: no query scan governance in BigQuery.
  - Fix: partitioning, clustering, and query hygiene.

- Mistake: ignoring egress costs.
  - Fix: co-locate services and monitor transfer.

## 7. Exam trap language cues
Watch for words that usually decide answer:
- "transactional", "joins", "ACID" -> relational.
- "document", "mobile backend" -> Firestore.
- "analytics at scale" -> BigQuery.
- "backup/static files" -> Cloud Storage.
- "global relational consistency" -> Spanner.

## 8. One-line memory hook
Most wrong answers are over-complex or model-mismatched; correct answers align directly to data shape and access shape.

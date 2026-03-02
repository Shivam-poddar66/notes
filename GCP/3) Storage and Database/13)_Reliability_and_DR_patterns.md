# 13) Reliability And DR Patterns (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Reliability architecture and disaster recovery patterns for storage/database services.
- RTO/RPO-based decision mapping.

## 1. Reliability fundamentals
- Define RTO (time to recover) and RPO (acceptable data loss window).
- Align backup, replication, and failover patterns to business impact.
- Test recovery regularly.

## 2. Cloud Storage reliability patterns
- Use object versioning for accidental overwrite/delete recovery.
- Use retention policies for compliance and safety.
- Select location strategy (regional/dual-region/multi-region) based on continuity needs.

## 3. Cloud SQL reliability patterns
- Enable HA where needed.
- Configure automated backups and point-in-time recovery.
- Use replicas and failover design for read scale and resilience.

## 4. Spanner reliability patterns
- Design for multi-zone and optionally multi-region continuity.
- Validate failover behavior with operational runbooks.

## 5. Firestore and Bigtable reliability patterns
- Plan backup/export and restore strategy.
- Design for retry-safe application behavior.
- Avoid single-region assumptions for critical global workloads.

## 6. BigQuery reliability patterns
- Build robust ingestion pipelines with retries.
- Monitor load failures and late data.
- Use lifecycle/governance controls on critical datasets.

## 7. DR workflow template
1. Identify failure domain (instance, zone, region, data corruption, accidental delete).
2. Activate runbook.
3. Recover from backup/replica.
4. Validate application correctness.
5. Execute post-incident review.

## 8. Testing and validation
- Backup restore drills.
- Failover simulations.
- Data integrity checks after recovery.
- Runbook tabletop exercises.

## 9. Common mistakes
- Assuming backup equals recovery readiness.
- No documented failover plan.
- RPO/RTO not explicitly defined.
- Not testing regional outage assumptions.

## 10. One-line memory hook
Reliability is proven by tested recovery and failover, not by architecture diagrams alone.

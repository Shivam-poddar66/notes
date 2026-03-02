# 4) Cloud SQL (Deep Dive)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Cloud SQL architecture, operations, reliability, security, performance, and cost.
- When Cloud SQL is correct and when to consider alternatives.

## 1. Service overview
Cloud SQL is managed relational database service for:
- MySQL,
- PostgreSQL,
- SQL Server.

It is commonly used for transactional application workloads requiring SQL semantics.

## 2. Best-fit workload profile
Use Cloud SQL when you need:
- relational schema,
- joins and SQL querying,
- transactional consistency,
- managed operations with lower complexity than self-managed DB on VMs.

## 3. Core architecture patterns
- Primary instance for writes.
- Read replicas for read-heavy scaling patterns.
- High availability configuration for zonal resilience.
- Separate dev/stage/prod environments with controlled change workflows.

## 4. Reliability and DR patterns
- Enable automated backups.
- Define and test restore process.
- Use point-in-time recovery strategy where required.
- Use replicas and failover design as per RTO/RPO targets.

Exam rule:
- Backups without restore testing are incomplete reliability design.

## 5. Security patterns
Identity and access:
- Least-privilege IAM around instance access.
- Least-privilege database users/roles.

Connectivity:
- Prefer private network path for app-to-DB connectivity.
- Restrict public exposure unless explicitly required.

Secrets:
- Store credentials in Secret Manager.
- Rotate credentials and verify app compatibility during rotation.

## 6. Performance tuning
- Right-size compute and memory.
- Monitor CPU, memory, storage, and connection usage.
- Add and tune indexes using query patterns.
- Investigate slow queries and optimize application-side query behavior.
- Use connection pooling to avoid connection storms.

## 7. Cost optimization
- Avoid oversized instance classes.
- Clean up unused replicas.
- Set backup retention to policy needs, not unlimited growth.
- Place compute and database close to reduce network costs and latency.

## 8. Cloud SQL vs alternatives
- Cloud SQL vs Spanner:
  - Cloud SQL for most regional managed relational app workloads.
  - Spanner when global scale and strong consistency at very high scale are hard requirements.
- Cloud SQL vs Firestore:
  - Cloud SQL for relational schema and joins.
  - Firestore for flexible document model and app-scale serverless patterns.

## 9. Common exam scenarios
1. Managed SQL backend for web app -> Cloud SQL.
2. Need relational transactions and simple managed operations -> Cloud SQL.
3. Need read scaling for heavy reads -> Cloud SQL with read replicas.

## 10. Common mistakes
- Leaving database publicly exposed without requirement.
- Ignoring connection pool tuning under burst traffic.
- Overusing Cloud SQL where non-relational model is better fit.
- Not defining clear backup and restore objectives.

## 11. Quick checklist
- Engine selected correctly.
- HA requirement evaluated.
- Backup/PITR and restore tests defined.
- Private connectivity configured.
- Query/index and pooling tuned.
- Cost and capacity reviewed regularly.

## 12. One-line memory hook
Cloud SQL is the default managed relational OLTP answer unless global-scale constraints force Spanner.

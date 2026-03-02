# 5) Cloud Spanner (Deep Dive)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Spanner use cases, architecture thinking, and exam selection boundaries.
- Tradeoffs compared with Cloud SQL and NoSQL options.

## 1. Service overview
Cloud Spanner is a relational database designed for:
- horizontal scalability,
- strong consistency,
- high availability patterns,
- globally distributed architectures.

## 2. When to use Spanner
Choose Spanner when requirements include:
- very high scale relational data,
- strong consistency requirements across large distributed workloads,
- global or multi-region relational continuity goals.

If these constraints are not present, Cloud SQL is often simpler.

## 3. Data model and query model
- Relational schema and SQL model.
- Transactional behavior suitable for high-value consistency-sensitive workloads.
- Designed for large scale while retaining relational capabilities.

## 4. Reliability design patterns
- Multi-zone and multi-region architecture options aligned to business continuity needs.
- Explicit RTO/RPO design decisions and failover testing.
- Operational runbooks for incident and failover events.

## 5. Security patterns
- Least-privilege IAM and database roles.
- Private network access pattern for application connectivity.
- Credential handling through managed secrets.
- Audit visibility for privileged changes.

## 6. Performance patterns
- Data model and primary key strategy impact throughput and latency.
- Keep application access patterns aligned with schema design.
- Monitor hotspot risk and query distribution.

## 7. Cost considerations
- Spanner provides powerful scale and consistency but with higher complexity and cost profile.
- Use only when scale/consistency/reliability requirements justify it.

## 8. Spanner vs Cloud SQL quick boundary
- Cloud SQL:
  - regional, managed relational workloads at common app scale.
- Spanner:
  - global-scale, strongly consistent relational workloads where Cloud SQL limits become a risk.

## 9. Common exam cues
- "Global relational consistency at scale" -> Spanner.
- "Simple managed SQL app database" -> usually Cloud SQL, not Spanner.

## 10. Common mistakes
- Choosing Spanner for normal workloads without scale justification.
- Ignoring schema and key design impact on performance.
- Treating Spanner as a drop-in choice for every relational workload.

## 11. One-line memory hook
Spanner is for high-scale, high-consistency relational requirements; Cloud SQL is for simpler managed relational needs.

# 12) Security Patterns Across Storage And Databases (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Identity, network, encryption, and audit best practices for storage and data services.
- Exam-ready secure architecture patterns.

## 1. Identity and access baseline
- Use dedicated service accounts per workload boundary.
- Grant least-privilege IAM roles.
- Avoid broad project-wide primitive roles for data access.
- Review and remove unused permissions regularly.

## 2. Secrets and credentials
- Store DB credentials, API keys, and secrets in Secret Manager.
- Do not hardcode credentials in source or config files.
- Rotate credentials and verify application compatibility.

## 3. Network security
- Prefer private connectivity for databases.
- Restrict public exposure unless explicitly required.
- Segment environments (dev/stage/prod) to reduce blast radius.

## 4. Data protection
- Encryption at rest is default across core managed services.
- Use CMEK where compliance or policy requires key control.
- Apply retention and governance controls for sensitive datasets.

## 5. Cloud Storage security patterns
- Use Uniform bucket-level access.
- Avoid public buckets unless business requirement is explicit.
- Use signed URLs for controlled temporary sharing.

## 6. Relational/NoSQL security patterns
- Cloud SQL/Spanner/Firestore/Bigtable:
  - least-privilege instance/database access,
  - private network path,
  - strict operational role separation.

## 7. BigQuery security patterns
- Dataset and table access boundaries.
- Separate analyst, engineer, and admin privileges.
- Monitor privileged query and data export behavior.

## 8. Logging, monitoring, and audit
- Enable audit visibility for access and admin operations.
- Alert on high-risk changes:
  - IAM policy changes,
  - unexpected public exposure,
  - abnormal data export activity.

## 9. Incident response pattern
1. Detect and scope.
2. Contain identity and endpoint exposure.
3. Rotate affected secrets/credentials.
4. Restore normal access with controlled rollout.
5. Perform post-incident hardening improvements.

## 10. Common mistakes
- Shared high-privilege identity across many apps.
- Public DB/bucket exposure by default.
- Missing audit and alerting for sensitive services.
- No tested key/credential rotation process.

## 11. One-line memory hook
Secure data services by default private access, least privilege, managed secrets, and auditable controls.

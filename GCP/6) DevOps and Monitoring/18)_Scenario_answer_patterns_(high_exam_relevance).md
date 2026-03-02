# 18) Scenario answer patterns (high exam relevance)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

1. Requirement: automate build-test-deploy on code push.
   - Typical answer: Cloud Build trigger-based pipeline.

2. Requirement: centralize logs and query operational issues.
   - Typical answer: Cloud Logging.

3. Requirement: send logs to analytics warehouse.
   - Typical answer: Logging sink to BigQuery.

4. Requirement: private long-term log archive.
   - Typical answer: Logging sink to Cloud Storage with lifecycle.

5. Requirement: track uptime and latency alerts.
   - Typical answer: Cloud Monitoring + uptime checks + alert policies.

6. Requirement: controlled promotion from stage to production.
   - Typical answer: Cloud Deploy style release promotion with approvals.

7. Requirement: keep infra changes repeatable and auditable.
   - Typical answer: IaC workflow in source control.

8. Requirement: reduce production incidents after releases.
   - Typical answer: canary/blue-green + rollback + SLO monitoring.

9. Requirement: avoid hardcoded secrets in pipeline/app.
   - Typical answer: Secret Manager integration.

10. Requirement: secure container artifact lifecycle.
    - Typical answer: Artifact Registry + vulnerability/security checks.

Exam elimination method:
1. Remove manual and non-repeatable options first.
2. Remove options with poor security posture (broad roles/plain secrets).
3. Remove options without observability/rollback support.
4. Choose simplest managed pipeline that meets constraints.


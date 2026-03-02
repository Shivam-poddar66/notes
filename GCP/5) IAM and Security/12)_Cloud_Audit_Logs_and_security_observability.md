# 12) Cloud Audit Logs and security observability

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Logging categories (high-level)
- Admin activity logging.
- Data access visibility (as applicable by service and configuration).
- System and policy-related events.

### Why logs matter
- Forensics.
- Compliance evidence.
- Detection of privilege abuse or misconfiguration.

### Alerting patterns
- Alert on:
  - privileged role grants,
  - service account key creation,
  - policy changes in sensitive projects,
  - repeated authorization failures.

Exam cues:
- "Need governance and compliance visibility" -> Cloud Audit Logs.


## Practical example
- Requirement: Detect unauthorized privileged role grants quickly.
- Design: Send audit log events to alerting pipeline for IAM policy changes.
- Why: Near-real-time detection supports faster incident response.


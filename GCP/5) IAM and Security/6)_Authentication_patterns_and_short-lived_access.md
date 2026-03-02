# 6) Authentication patterns and short-lived access

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Preferred patterns
- Application Default Credentials in managed runtimes.
- Service account impersonation for admin operations.
- Workforce/workload federation where enterprise integration requires it.

### Why short-lived access is preferred
- Reduces credential theft impact.
- Simplifies rotation and governance.

### Human access patterns
- Strong MFA and identity policy controls.
- Separate high-risk privileged activities from daily accounts.

Exam cues:
- "Avoid long-lived keys" -> impersonation/short-lived credential pattern.


## Practical example
- Requirement: Ops engineer needs temporary admin access for migration weekend.
- Design: Use short-lived impersonation access instead of permanent key.
- Why: Time-bound credentials reduce credential theft impact.


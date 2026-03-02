# 5) Service accounts and workload identity

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Core principle
- Service account is identity for workloads, not for humans.

### Best practices
- One service account per workload boundary.
- Grant least privilege at narrow scope.
- Avoid sharing one high-privilege account across many apps.

### Service account lifecycle
1. Create dedicated SA.
2. Grant minimal roles.
3. Attach SA to compute workload.
4. Monitor usage and rotate/remove unused bindings.

### Anti-pattern
- Downloading and distributing static SA keys widely.

Exam cues:
- "Application or VM needs API access" -> attach service account with minimal role.


## Practical example
- Requirement: VM-based app must write logs and read one secret.
- Design: Attach dedicated service account with only logging + secret accessor permissions.
- Why: Per-workload identity limits blast radius and avoids shared high-privilege accounts.


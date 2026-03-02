# 17) Security architecture patterns across services

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Pattern 1: Project baseline
- Organization policy guardrails.
- Logging and alerts enabled.
- Restricted IAM administrators.

### Pattern 2: Workload identity model
- Dedicated SA per service.
- Least privilege resource access.
- Secret Manager for all secrets.

### Pattern 3: Deployment pipeline security
- Separate CI/CD identity with limited deploy permissions.
- No broad owner/editor roles for pipelines.
- Audit deployment actions.

### Pattern 4: Cross-project access
- Explicit grants only.
- Avoid hidden implicit assumptions.
- Monitor inter-project privileged access closely.

Exam cues:
- "Secure by default at scale" -> baseline guardrails + workload-specific least privilege.


## Practical example
- Requirement: Multi-team platform needs secure default project template.
- Design: Baseline guardrails + per-service identity + audited CI/CD roles.
- Why: Reusable secure architecture reduces repeated misconfiguration.


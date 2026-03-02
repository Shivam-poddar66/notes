# 7) Infrastructure as Code and environment consistency

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### IaC principles
- Define infrastructure declaratively.
- Version control infrastructure changes.
- Apply through reviewed pipeline, not manual console drift.

### Benefits
- Reproducible environments.
- Change auditability.
- Easier rollback and disaster rebuild.

### Common tools/patterns
- Terraform-centric workflows in many teams.
- Plan/review/apply model with approvals for prod.

### Anti-patterns
- Manual changes in prod without IaC update.
- One shared state/process without environment separation.

Exam cues:
- "Consistent, repeatable infra provisioning" -> IaC approach.


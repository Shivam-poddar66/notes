# 19) Common mistakes and exam traps

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Delivery mistakes
- Deploying directly from developer workstation.
- No staging validation before production.
- No rollback mechanism.

### Observability mistakes
- Monitoring infra only, not user-impact SLIs.
- High-noise alerts with no runbooks.
- No correlation across logs/metrics/traces.

### Security mistakes
- Overprivileged build/deploy service accounts.
- Secrets in source repositories.
- No artifact trust or scanning policy.

### Governance mistakes
- Untracked manual changes in production.
- Inconsistent environment config.
- No incident review feedback loop.

Exam traps:
- Choosing tools that solve only one part (for example build only) when scenario asks end-to-end delivery + monitoring.


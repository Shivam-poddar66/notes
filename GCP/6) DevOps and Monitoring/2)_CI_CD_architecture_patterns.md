# 2) CI/CD architecture patterns

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Pipeline stages
- Source -> build -> test -> security checks -> artifact -> deploy -> verify -> promote.

### Environment strategy
- Separate dev, stage, and prod.
- Promote immutable artifacts across environments.
- Keep environment-specific config externalized.

### Quality gates
- Unit/integration tests before deploy.
- Policy and security checks before promotion.
- Manual approval on high-risk production releases where needed.

### Anti-patterns
- Building directly on production hosts.
- Deploying unversioned artifacts.
- Skipping rollback plan.

Exam cues:
- "Automated build-test-deploy with managed service" -> Cloud Build centric pattern.


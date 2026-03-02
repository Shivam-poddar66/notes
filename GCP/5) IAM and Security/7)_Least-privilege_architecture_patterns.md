# 7) Least-privilege architecture patterns

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Role grant strategy
- Grant only required roles.
- Grant at lowest feasible scope.
- Use conditions for time/resource restrictions when needed.

### Separation patterns
- Separate roles for:
  - deployment,
  - runtime operation,
  - security auditing.

### Periodic review workflow
1. Export and review IAM bindings.
2. Remove stale grants.
3. Tighten broad roles.
4. Validate no production outage from cleanup.

Exam cues:
- "Security-first with minimum access" -> least-privilege and narrow scope grant.


## Practical example
- Requirement: CI pipeline deploys app but must not manage IAM.
- Design: Grant deploy-specific role only; keep IAM admin role separate.
- Why: Separation of duties limits privilege abuse and mistakes.


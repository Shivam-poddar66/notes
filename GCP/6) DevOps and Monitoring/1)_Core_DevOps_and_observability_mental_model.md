# 1) Core DevOps and observability mental model

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

Use this 5-layer model:
1. Plan and change:
   - version control, branching, review gates.
2. Build and package:
   - reproducible builds and artifact integrity.
3. Release and deploy:
   - progressive rollout, rollback, environment promotion.
4. Observe and operate:
   - metrics, logs, tracing, alerts, runbooks.
5. Improve continuously:
   - postmortems, reliability targets, automation hardening.

Primary rule:
- Fast delivery without observability and rollback is operational risk.

Exam rule:
- Choose managed services and the simplest reliable delivery path that meets security and availability constraints.


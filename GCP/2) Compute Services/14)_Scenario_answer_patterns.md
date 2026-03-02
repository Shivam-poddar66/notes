# Scenario Answer Patterns (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- A repeatable method to solve compute scenario questions quickly.
- Keyword-to-service mapping and tie-breaker rules.
- High-frequency patterns with answer logic.
- Elimination strategy for close options.

## 1) 6-step method for scenario questions

1. Extract hard constraints.
   - Examples: custom OS, minimum ops, event trigger, strict latency, compliance region.
2. Classify workload type.
   - HTTP service, event handler, batch job, legacy VM app.
3. Match service candidates.
   - Compute Engine, App Engine, Cloud Run, Cloud Run functions.
4. Apply reliability and security overlay.
   - Multi-zone, IAM least privilege, private ingress, rollout/rollback.
5. Apply cost and operations overlay.
   - Idle profile, scaling behavior, team operational bandwidth.
6. Choose the least-complex architecture that satisfies all constraints.

## 2) Keyword decoder (fast exam mapping)

- "minimum server management" -> Cloud Run or App Engine direction.
- "custom OS/kernel/agent dependency" -> Compute Engine direction.
- "trigger on upload/message/event" -> Cloud Run functions direction.
- "containerized HTTP API" -> Cloud Run direction.
- "autohealing VM fleet" -> MIG direction.
- "version traffic split" -> App Engine or Cloud Run revision strategy.

## 3) Tie-breaker logic when multiple options look valid

Use this order:
1. Hard constraints.
2. Workload model fit.
3. Reliability/security fit.
4. Lowest operational burden.
5. Cost efficiency for traffic pattern.

## 4) High-frequency scenario patterns

1. Requirement: scalable HTTP API + low ops.
   - Typical answer: Cloud Run.
   - Why: managed scaling and container-based deployment.

2. Requirement: strict host-level dependency.
   - Typical answer: Compute Engine.
   - Why: OS/runtime control requirement dominates.

3. Requirement: process object upload event.
   - Typical answer: Cloud Run functions.
   - Why: trigger-driven code path is primary fit.

4. Requirement: VM fleet must autoscale and autoheal.
   - Typical answer: MIG + load balancing + health checks.

5. Requirement: managed app platform with fast deploy.
   - Typical answer: App Engine.

6. Requirement: bursty containerized microservice.
   - Typical answer: Cloud Run with tuned concurrency/min instances.

7. Requirement: legacy app, urgent migration, low refactor window.
   - Typical answer: Compute Engine first, modernize later.

8. Requirement: public API but private backend dependencies.
   - Typical answer: Cloud Run or App Engine with controlled ingress + private service connectivity.

9. Requirement: survive zonal failure for VM app.
   - Typical answer: regional MIG across zones.

10. Requirement: strict low-latency endpoint with occasional spikes.
    - Typical answer: Cloud Run with selective min instances and autoscaling guardrails.

11. Requirement: workload mainly scheduled batch container task.
    - Typical answer: Cloud Run jobs (or VM batch depending control needs).

12. Requirement: simple webhook/event automation with minimal ops.
    - Typical answer: Cloud Run functions.

13. Requirement: security says no broad project roles.
    - Typical answer pattern: dedicated least-privilege service account per workload.

14. Requirement: rollout safety and quick rollback.
    - Typical answer: revision/version traffic splitting pattern.

15. Requirement: single project hosting many apps with different permissions.
    - Typical answer pattern: separate identities and narrow IAM scope.

16. Requirement: minimize idle compute spend for intermittent API.
    - Typical answer: Cloud Run over always-on VM.

17. Requirement: strict compliance needs host control and custom hardening.
    - Typical answer: Compute Engine with explicit hardening controls.

18. Requirement: queue-triggered asynchronous processing.
    - Typical answer: Cloud Run functions or Cloud Run worker service depending complexity.

19. Requirement: "best reliability" for VM-based web tier.
    - Typical answer: multi-zone MIG + LB + health checks + rollout plan.

20. Requirement: "best overall" with low ops, secure, scalable API.
    - Typical answer: Cloud Run + least-privilege IAM + Secret Manager + observability.

## 5) Answer template you can use in exam reasoning

Use this mental sentence:
- "Because the workload is [type] and requires [hard constraints], the best fit is [service]. Then add [reliability/security overlay] with minimal extra complexity."

Example:
- "Because this is a bursty stateless HTTP API with minimum management requirement, choose Cloud Run; add least-privilege service account, Secret Manager, and controlled ingress."

## 6) Elimination patterns for wrong options

- Remove options that violate explicit requirement words.
- Remove options that require unnecessary infrastructure operations.
- Remove options that mismatch workload model.
- Remove options that ignore required resilience domain (zone/region).
- Remove options with unclear identity/secret handling.

## 7) Common trap patterns and fixes

- Trap: choosing Compute Engine for every migration question.
  - Fix: only choose VM when control or migration constraints demand it.

- Trap: choosing Cloud Run functions for heavy, long business workflows.
  - Fix: use Cloud Run service/job or queue-driven worker architecture.

- Trap: choosing public ingress by default.
  - Fix: match ingress to actual requirement.

- Trap: selecting architecture without rollback method.
  - Fix: include version/revision rollback pattern.

## 8) Quick decision tree

1. Need OS-level control?
   - Yes -> Compute Engine.
   - No -> continue.
2. Trigger/event-first workload?
   - Yes -> Cloud Run functions (or Cloud Run worker if heavy).
   - No -> continue.
3. Containerized HTTP service with low ops?
   - Yes -> Cloud Run.
   - No -> continue.
4. Managed app platform preference with supported runtime?
   - Yes -> App Engine.
5. Add reliability, security, and cost overlays.

## 9) Final exam rule

- Select by constraints, not by personal preference.
- Simpler valid architecture usually wins over complex "powerful" design.

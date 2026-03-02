# Service Model Comparison (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- IaaS vs PaaS vs serverless compute in GCP.
- Responsibility split and operational impact.
- Scaling, security, cost, and reliability differences.
- Decision matrix for exam scenarios.

## 1) Model comparison overview

### IaaS (Compute Engine)
- Infrastructure control is high.
- Operations burden is high.

### PaaS (App Engine)
- Platform manages much infrastructure complexity.
- Control is moderate.

### Serverless containers (Cloud Run)
- Infrastructure management is minimal.
- Best for stateless containerized services.

### Event-driven functions (Cloud Run functions)
- Minimal infrastructure and function-level code focus.
- Best for trigger-based workloads.

## 2) Responsibility model comparison

### Compute Engine
You manage:
- guest OS patching,
- runtime dependencies,
- host-level tuning choices,
- workload security hardening.

### App Engine
You manage mostly:
- application code,
- app-level config,
- access and data controls.

### Cloud Run
You manage mostly:
- container code/image,
- service config,
- identity and downstream permissions.

### Cloud Run functions
You manage mostly:
- function code,
- trigger behavior,
- retries/idempotency logic,
- IAM and secrets.

## 3) Deployment unit comparison

- Compute Engine: VM instances.
- App Engine: app services/versions.
- Cloud Run: services/revisions/jobs.
- Cloud Run functions: functions and triggers.

## 4) Scaling behavior comparison

- Compute Engine: manual scaling or MIG-driven autoscaling.
- App Engine: managed scaling based on config and demand.
- Cloud Run: request-driven autoscaling with service configuration.
- Cloud Run functions: event/request-driven scaling.

## 5) Reliability characteristics

- Compute Engine:
  - reliability depends heavily on your architecture (MIG, zones, LB).
- App Engine:
  - strong platform-managed scaling behavior.
- Cloud Run:
  - managed scaling, but dependency architecture still critical.
- Cloud Run functions:
  - reliability requires idempotent handlers and safe retry design.

## 6) Security characteristics

All models require customer ownership of:
- IAM least privilege,
- service account scope,
- secret management,
- data access governance.

Difference:
- Compute Engine adds OS hardening and patching responsibility.
- Managed models reduce infrastructure security work but not app/data identity controls.

## 7) Cost behavior comparison

- Compute Engine:
  - efficient when tuned,
  - wasteful if idle or oversized.
- App Engine:
  - productivity-first managed cost profile.
- Cloud Run:
  - strong fit for variable traffic and low idle.
- Cloud Run functions:
  - strong fit for event bursts and small units of execution.

## 8) Performance and latency considerations

- Compute Engine:
  - strongest low-level tuning control.
- App Engine:
  - good platform behavior, less host tuning control.
- Cloud Run:
  - good for stateless APIs; concurrency/cold behavior tuning matters.
- Cloud Run functions:
  - ideal for short event logic, not heavy long-running workflows.

## 9) Decision matrix (exam-focused)

Choose Compute Engine when:
- custom OS/runtime control is required.

Choose App Engine when:
- managed app platform and rapid delivery are priority.

Choose Cloud Run when:
- scalable containerized HTTP service with minimal ops is required.

Choose Cloud Run functions when:
- trigger-driven event logic is required.

## 10) Common model-selection mistakes

- Picking highest-control option without need.
- Ignoring workload statefulness requirements.
- Assuming managed services remove security responsibilities.
- Ignoring cost and reliability behavior under real traffic pattern.

## 11) Quick revision sheet

- IaaS = control first.
- PaaS = platform productivity.
- Serverless containers = low-ops services.
- Event functions = trigger-first code.
- Service choice should satisfy constraints, not preferences.

## 12) Practice questions with answers

1. Which model gives maximum infrastructure control?
   - IaaS (Compute Engine).

2. Which model is best aligned with event-driven small handlers?
   - Cloud Run functions.

3. Which model is strongest for containerized API with low ops?
   - Cloud Run.

4. Which model is PaaS in GCP compute?
   - App Engine.

5. Which model usually needs most patch management effort?
   - Compute Engine.

6. Which model usually has lowest infrastructure management burden?
   - Cloud Run functions/Cloud Run.

7. If a workload needs kernel-level customization, best model?
   - Compute Engine.

8. If requirement says "quick app deployment, less infra work," likely model?
   - App Engine.

9. Is IAM design still required in managed/serverless models?
   - Yes.

10. What is the best exam strategy for model comparison questions?
    - Match workload constraints to the minimum-complexity suitable model.

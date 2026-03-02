# Quick Revision Sheet (Compute Chapter)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1) Service one-liners

- Compute Engine: highest host/runtime control, highest infrastructure operations.
- MIG: VM autoscaling + autohealing pattern for resilient fleets.
- App Engine: managed application platform with version-based deployment flow.
- Cloud Run: serverless containers for stateless HTTP services and jobs.
- Cloud Run functions: event-driven function model for lightweight triggers.

## 2) Keyword-to-service map

- "minimum operational overhead" -> Cloud Run or App Engine.
- "custom OS/kernel/agent" -> Compute Engine.
- "event trigger on upload/message" -> Cloud Run functions.
- "containerized API/microservice" -> Cloud Run.
- "autoheal failed VM instances" -> MIG + health checks.

## 3) Control vs operations memory hook

- More control -> more ops (Compute Engine).
- More managed -> less ops (App Engine/Cloud Run/functions).
- Exam default: choose least-complex service that still satisfies hard constraints.

## 4) Reliability quick rules

- VM production baseline: multi-zone MIG + load balancing + health checks.
- Serverless reliability: stateless design + safe rollout/rollback + dependency resilience.
- Event reliability: idempotent handlers for retry/duplicate delivery.
- DR: multi-zone handles zone failures; multi-region handles regional continuity.

## 5) Security quick rules

- Dedicated least-privilege service account per workload.
- Secret Manager for credentials/secrets.
- Private ingress by default; public only when required.
- VM patching/hardening is customer responsibility.
- Enable logging and alerts for IAM/deployment anomalies.

## 6) Performance quick rules

- Track `p95/p99` latency, error rate, throughput, saturation.
- Cloud Run tuning levers: concurrency, min instances, max instances.
- VM tuning levers: right-size machine + autoscaling policy + dependency bottleneck checks.
- Never scale compute tier without checking database/downstream limits.

## 7) Cost quick rules

- Compute Engine wins when utilization is steady and controlled well.
- Cloud Run often wins for bursty/intermittent HTTP workloads.
- Functions fit event-sized automation.
- App Engine can reduce total ownership cost for small teams.
- Hidden costs: inter-region egress, idle baseline, retries, noisy logging.

## 8) Common exam traps

- Choosing VM despite explicit low-ops requirement.
- Choosing Cloud Run for strict OS customization requirements.
- Choosing functions for long heavy workflows.
- Ignoring reliability overlays after compute selection.
- Assuming serverless is always cheapest.

## 9) 30-second decision tree

1. Need OS-level control?
   - Yes -> Compute Engine.
2. Is workload trigger/event-first?
   - Yes -> Cloud Run functions (or Cloud Run worker if heavy).
3. Is it stateless containerized HTTP service?
   - Yes -> Cloud Run.
4. Need managed app platform semantics?
   - Yes -> App Engine.
5. Add reliability, security, and cost overlays before final answer.

## 10) Final exam rule

- Parse constraints first, eliminate invalid options fast, then pick simplest valid architecture.

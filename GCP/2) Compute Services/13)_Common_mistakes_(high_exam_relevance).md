# Common Mistakes (High Exam Relevance) - Detailed Notes

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- The most frequent compute mistakes made in ACE scenario questions.
- Why each mistake is wrong and what the corrected answer pattern looks like.
- Elimination shortcuts to avoid trap options quickly.

## 1) Requirement parsing mistakes

### Mistake 1
- Reading "minimum operational overhead" but still choosing raw VMs.
- Correct pattern: start with Cloud Run or App Engine unless a hard control requirement blocks them.

### Mistake 2
- Ignoring exact words like "custom OS/kernel/agent."
- Correct pattern: these words almost always push toward Compute Engine.

### Mistake 3
- Missing workload shape (HTTP service vs event-driven function).
- Correct pattern: HTTP/container service usually Cloud Run; trigger-driven lightweight code usually Cloud Run functions.

### Mistake 4
- Choosing based on familiarity instead of explicit constraints.
- Correct pattern: parse constraints first, then choose service.

## 2) Service-fit mistakes

### Mistake 5
- Choosing Cloud Run functions for long, complex orchestrations.
- Correct pattern: use Cloud Run service/job or queue-worker architecture for heavier logic.

### Mistake 6
- Choosing App Engine when deep host-level customization is required.
- Correct pattern: Compute Engine for low-level OS/runtime control.

### Mistake 7
- Choosing Compute Engine for simple stateless API with bursty traffic and no custom host needs.
- Correct pattern: Cloud Run is usually lower-ops and better fit.

### Mistake 8
- Assuming App Engine and Cloud Run are interchangeable in every scenario.
- Correct pattern: evaluate runtime fit, deployment model, and control needs.

## 3) Reliability design mistakes

### Mistake 9
- Single VM in one zone for critical workload.
- Correct pattern: MIG + load balancing + multi-zone architecture.

### Mistake 10
- No rollback strategy for new deployments.
- Correct pattern: use versions/revisions with controlled traffic shift and rollback plan.

### Mistake 11
- Treating backup as proof of DR readiness.
- Correct pattern: restore testing and failover runbook validation are required.

### Mistake 12
- Ignoring downstream dependency failure modes.
- Correct pattern: retries with backoff, timeouts, and fallback behavior.

## 4) Security mistakes

### Mistake 13
- Using one shared high-privilege service account for many workloads.
- Correct pattern: dedicated least-privilege service account per workload boundary.

### Mistake 14
- Storing secrets in environment variables or source code.
- Correct pattern: Secret Manager with controlled access.

### Mistake 15
- Exposing services publicly by default without requirement.
- Correct pattern: private/internal ingress unless public endpoint is explicitly needed.

### Mistake 16
- Forgetting VM patching responsibility in Compute Engine.
- Correct pattern: include patch and hardening operations in VM answers.

## 5) Performance and scaling mistakes

### Mistake 17
- Scaling compute tier only while database remains bottleneck.
- Correct pattern: profile end-to-end path before scaling changes.

### Mistake 18
- High concurrency on CPU-heavy Cloud Run handlers causing tail latency spikes.
- Correct pattern: tune concurrency based on workload behavior.

### Mistake 19
- No min-instance strategy for strict low-latency endpoints.
- Correct pattern: use min instances selectively for cold-start-sensitive paths.

### Mistake 20
- Treating autoscaling as substitute for load testing.
- Correct pattern: test spike and failure modes before production.

## 6) Cost mistakes

### Mistake 21
- Assuming serverless is always cheapest.
- Correct pattern: match service to traffic profile and operational needs.

### Mistake 22
- Leaving non-prod VMs always on.
- Correct pattern: scheduling and automation for idle shutdown.

### Mistake 23
- Ignoring inter-region traffic costs.
- Correct pattern: co-locate compute and data where possible.

### Mistake 24
- Overusing min instances in low-traffic Cloud Run services.
- Correct pattern: use only when latency SLO requires it.

## 7) Architecture and migration mistakes

### Mistake 25
- Full refactor to serverless during urgent migration timeline.
- Correct pattern: lift-and-shift to Compute Engine first when time/risk constraints are tight.

### Mistake 26
- Mixing stateful assumptions into stateless service platforms.
- Correct pattern: externalize state to managed storage/data services.

### Mistake 27
- Designing event handlers without idempotency.
- Correct pattern: safe duplicate handling for retries.

### Mistake 28
- Skipping environment parity (dev/stage/prod) for performance/reliability tests.
- Correct pattern: validate key behaviors in production-like environments.

## 8) Exam elimination method for trap options

1. Remove options that violate explicit constraint words.
2. Remove options that increase ops burden when low-ops is required.
3. Remove options that mismatch workload type (service vs event).
4. Remove options without clear reliability/security path.
5. Choose simplest remaining option that satisfies all constraints.

## 9) Fast correction map (memorization)

- "Minimum ops HTTP API" -> Cloud Run, not VM-first.
- "Custom OS dependency" -> Compute Engine, not managed runtime.
- "Event trigger automation" -> Cloud Run functions, not full VM.
- "Autoheal VM fleet" -> MIG + health checks.
- "Critical production VM app" -> multi-zone architecture, not single instance.

## 10) Quick revision sheet

- Parse hard constraints before reading answer choices.
- Match service to workload shape.
- Apply reliability and security overlays after compute selection.
- Validate cost only after functional fit is confirmed.
- Avoid "always/never" assumptions in cloud architecture questions.

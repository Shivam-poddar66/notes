# Performance and Scaling Guidance (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- A practical performance model for all GCP compute choices.
- Scaling patterns for Compute Engine, MIG, App Engine, Cloud Run, and Cloud Run functions.
- Tuning levers that affect latency, throughput, and cost.
- Exam-focused decision and troubleshooting patterns.

## 1) Baseline performance model

Use user-impact metrics first:
- Latency percentiles (`p50`, `p95`, `p99`).
- Throughput (requests/events per second).
- Error rate and timeout rate.
- Saturation (CPU, memory, queue depth, connection pools).

Then map metrics to SLOs:
- Define acceptable latency/error targets.
- Set alert thresholds tied to SLO breach risk, not only host metrics.
- Track deployment events to correlate regressions quickly.

## 2) Capacity planning workflow

1. Classify traffic shape:
   - steady,
   - bursty,
   - event-driven,
   - scheduled spikes.
2. Run baseline load test for current architecture.
3. Identify bottleneck layer:
   - compute,
   - database,
   - network,
   - downstream API.
4. Choose scaling model (horizontal, vertical, queue buffering, or mixed).
5. Validate with stress and failure tests before production rollout.

## 3) Compute Engine and MIG performance patterns

### Right-sizing and machine selection
- CPU-bound services: favor compute-heavy profiles.
- Memory-bound services: favor higher-memory profiles.
- Avoid chronic overprovisioning; tune from measured utilization trends.

### Disk and I/O considerations
- Use higher-performance disk tiers for IOPS-sensitive workloads.
- Keep hot paths out of slow boot-time package installs.
- Separate application and data concerns to avoid noisy-neighbor effects inside one VM.

### MIG scaling strategy
- Use autoscaler signals that reflect real load:
  - CPU utilization,
  - load balancer serving capacity,
  - custom metrics (queue depth, request backlog).
- Configure minimum instances for baseline traffic to reduce startup latency.
- Configure maximum instances to protect downstream systems.

### VM startup performance
- Prefer baked images and deterministic startup scripts.
- Keep boot path short and idempotent.
- Preload critical dependencies where possible.

## 4) Cloud Run performance patterns

### Core tuning levers
- Concurrency:
  - higher concurrency can reduce cost but may increase tail latency for CPU-heavy handlers.
  - lower concurrency can improve predictable latency for heavy request workloads.
- Minimum instances:
  - useful for cold-start-sensitive endpoints.
  - increases baseline cost.
- Maximum instances:
  - protects databases and external APIs from overload during spikes.

### Application design patterns
- Keep services stateless.
- Optimize startup path (lazy-load noncritical dependencies).
- Reuse outbound connections and clients.
- Keep request handlers fast; offload long work to async path (Pub/Sub, jobs, queue worker).

### Deployment patterns
- Use revision traffic splitting for safe rollout.
- Benchmark before and after runtime or dependency changes.

## 5) App Engine performance patterns

### Scaling modes
- Automatic scaling for dynamic traffic.
- Basic/manual modes for specific workload control patterns.

### Performance levers
- Choose appropriate instance class.
- Tune request handling and background work separation.
- Use version rollouts to detect regressions before full traffic shift.
- Keep dependencies near compute region to reduce latency.

### Operational pattern
- Measure real latency under production-like traffic before locking scaling configuration.

## 6) Cloud Run functions performance patterns

- Keep each handler focused and short-running.
- Make handlers idempotent for safe retry behavior.
- Minimize synchronous dependencies inside trigger path.
- Use event filtering and precise trigger scopes to reduce unnecessary invocations.
- Move heavy transformations to Cloud Run services/jobs or queue-based workers.

## 7) Cross-service scaling patterns

### Queue and buffer pattern
- Absorb spikes via Pub/Sub or task queues.
- Smooth demand before hitting CPU-heavy worker layers.

### Backpressure and protection
- Apply timeouts and retry-with-backoff.
- Add circuit-breaker behavior for fragile dependencies.
- Limit concurrency where downstream systems are strict bottlenecks.

### Data-path optimization
- Keep compute close to database/storage region.
- Reduce cross-region chatty calls.
- Add caching for frequently repeated read paths.

## 8) Load testing and validation strategy

Test in stages:
1. Baseline: current expected peak.
2. Step load: gradual increase to locate knee point.
3. Stress: short burst above expected peak.
4. Soak: long-duration run to catch memory leaks and connection churn.
5. Failure mode: downstream slowdown/outage simulation.

Record for each stage:
- latency percentiles,
- error types,
- autoscaling reaction time,
- instance counts,
- dependency saturation.

## 9) Observability requirements for performance

Track a dashboard per service with:
- request latency,
- request count/event throughput,
- error rates,
- instance count and scaling events,
- CPU and memory saturation indicators,
- deployment markers.

Alert on:
- sustained `p95/p99` degradation,
- error spikes,
- backlog growth,
- saturation persistence beyond recovery window.

## 10) Performance troubleshooting playbook

1. Confirm impact scope (single endpoint, all requests, one region, one revision).
2. Compare with last known good deployment.
3. Isolate bottleneck layer (compute, DB, network, third-party API).
4. Apply targeted mitigation:
   - rollback revision/version,
   - adjust concurrency/min instances,
   - scale MIG/service bounds,
   - shed noncritical load.
5. Capture root cause and update runbook.

## 11) Exam-focused decision patterns

- Requirement says "low ops + bursty HTTP API":
  - usually Cloud Run with concurrency and min-instance tuning.
- Requirement says "strict OS/runtime tuning required":
  - Compute Engine (often with MIG for scale).
- Requirement says "event-driven lightweight processing":
  - Cloud Run functions.
- Requirement says "managed app platform + version controls":
  - App Engine.

Tie-breaker:
- Choose the least operationally complex option that still meets SLO and control constraints.

## 12) High-frequency anti-patterns

- Scaling compute without checking database bottleneck.
- Setting very high concurrency for CPU-heavy handlers and then blaming platform latency.
- Running long business workflows synchronously in request path.
- Ignoring cold-start impact for strict tail-latency endpoints.
- Treating autoscaling as substitute for performance testing.

## 13) Quick performance checklist

- SLOs defined (latency + error).
- Autoscaling bounds configured and justified.
- Downstream dependency protection in place.
- Load tests include spike and failure behavior.
- Rollback path tested for bad performance deployments.

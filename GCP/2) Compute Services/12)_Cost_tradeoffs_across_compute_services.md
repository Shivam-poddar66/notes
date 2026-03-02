# Cost Tradeoffs Across Compute Services (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Cost behavior differences between Compute Engine, App Engine, Cloud Run, and Cloud Run functions.
- Practical optimization levers for each model.
- Hidden cost drivers frequently missed in exams and real projects.
- Decision patterns that balance cost, reliability, and operational effort.

## 1) Core cost model: beyond list price

Total cost is not only compute billing. Include:
- Runtime compute charges.
- Storage and network transfer.
- Logging/monitoring usage.
- Security and compliance overhead.
- Engineering and operations effort.

Exam reminder:
- The "cheapest" answer is usually the one that meets requirements with least necessary complexity and minimal idle waste.

## 2) Primary cost drivers by compute model

### Compute Engine
- VM uptime (including idle time when instance is running).
- Machine size overprovisioning.
- Disk tier/size and snapshots.
- Network egress and external IP patterns.
- License and support constraints (when applicable).

### App Engine
- Instance class and scaling behavior.
- Idle baseline due to scaling settings.
- Runtime-specific efficiency differences.
- Outbound data and dependent service costs.

### Cloud Run
- Request-driven CPU/memory billing profile.
- Configuration choices:
  - minimum instances (reduces cold start, increases baseline spend),
  - concurrency (affects both performance and instance count),
  - max instances (controls burst cost and downstream risk).
- Outbound network transfer and supporting service calls.

### Cloud Run functions
- Invocation count and compute time profile.
- Retry/duplicate event patterns (can increase bill when handlers are not idempotent).
- Trigger design and filtering quality.

## 3) Compute Engine cost tradeoffs

### When Compute Engine is cost-effective
- Steady, predictable, high-utilization workloads.
- Legacy workloads that would require expensive refactoring for serverless fit.
- Scenarios needing host-level tuning for strong performance-per-dollar.

### Main risks
- Idle always-on VMs.
- Oversized machine types.
- Underused non-production environments left running.

### Optimization levers
- Rightsize continuously from utilization data.
- Use autoscaling MIGs for elastic tiers.
- Use committed use discounts where usage is predictable.
- Use Spot VMs for fault-tolerant batch workloads.
- Schedule start/stop for non-prod environments.

## 4) App Engine cost tradeoffs

### Strengths
- Low platform ops effort can reduce people-cost for small teams.
- Managed scaling reduces custom infrastructure work.

### Risks
- Misconfigured scaling can create unnecessary idle baseline.
- Runtime inefficiencies can amplify per-request cost.

### Optimization levers
- Choose appropriate environment and instance class.
- Tune scaling settings with real traffic patterns.
- Remove stale versions/services.
- Keep dependencies region-local where possible.

## 5) Cloud Run cost tradeoffs

### Strengths
- Strong alignment with variable or bursty demand.
- Scale-to-near-zero style behavior for intermittent services.
- Fast deployment and low ops overhead.

### Risks
- High minimum instances can erase serverless cost advantage.
- Poor concurrency tuning can increase instance count and cost.
- Chatty cross-region dependencies can dominate total spend.

### Optimization levers
- Tune concurrency per workload type (CPU-heavy vs IO-heavy handlers).
- Set min instances only where latency requires it.
- Protect downstream systems with max instances to avoid expensive failure storms.
- Trim image startup overhead to reduce request-time compute usage.

## 6) Cloud Run functions cost tradeoffs

### Strengths
- Efficient for event spikes and lightweight automation.
- Minimal operational footprint for trigger-driven tasks.

### Risks
- Not ideal for long orchestration-heavy flows.
- Retry storms from non-idempotent handlers can multiply invocations.
- Broad triggers create unnecessary executions.

### Optimization levers
- Keep function logic small and deterministic.
- Filter events aggressively.
- Use queue/job/service patterns for heavy work.
- Monitor retry counts and dead-letter behavior.

## 7) Hidden cost drivers across all services

- Inter-region data transfer.
- Cloud NAT, load balancing, and egress architecture choices.
- Logging volume growth from noisy debug logs.
- Build/deploy pipeline inefficiencies (frequent heavy builds).
- Operational toil from over-complex architecture.

## 8) Workload-shape decision matrix (cost perspective)

### Spiky HTTP traffic + low ops requirement
- Usually Cloud Run is cost-efficient.

### Steady high-utilization workload with deep tuning
- Compute Engine can be more efficient if sized and governed well.

### Managed web app with small ops team
- App Engine can reduce total cost of ownership even if raw compute is not the absolute cheapest.

### Event-triggered lightweight automation
- Cloud Run functions often fit best.

## 9) Cost optimization playbook

1. Baseline current monthly spend by service and environment.
2. Find top 3 drivers (compute idle, network egress, logging, etc.).
3. Apply targeted optimization:
   - rightsize compute,
   - tune scaling bounds,
   - remove idle baseline,
   - regionalize dependencies.
4. Validate no reliability/security regression.
5. Repeat monthly with budget and forecast checks.

## 10) Governance controls

- Set budgets and alerts per project/environment.
- Use labels and cost allocation tags consistently.
- Review anomalous spend after deployments.
- Enforce architecture review for high-egress and always-on services.

## 11) High-frequency exam traps

- Assuming "serverless is always cheapest."
- Ignoring traffic pattern when selecting compute service.
- Choosing VM for low-traffic intermittent service without strong control requirement.
- Ignoring operational cost in "best overall" architecture decisions.
- Missing network egress as major total-cost contributor.

## 12) Exam answer pattern

When question asks for "cost-optimized":
1. Respect hard constraints first (security, reliability, compliance).
2. Match service to traffic shape.
3. Minimize idle compute.
4. Reduce operational burden unless explicit control is required.
5. Avoid unnecessary cross-region communication.

## 13) Quick revision sheet

- Compute Engine: best when predictable utilization and control justify it.
- App Engine: can win on total ownership cost for small teams needing fast delivery.
- Cloud Run: strong for variable demand and low ops.
- Cloud Run functions: best for event-sized automation.
- Biggest hidden bill multipliers: egress, idle baseline, retries, noisy logging.

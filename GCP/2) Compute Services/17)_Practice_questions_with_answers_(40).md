# Practice Questions With Answers (40, Detailed)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## How to use this set
- Try to answer each question first in 20 to 30 seconds.
- Then verify using the "Why" lines.
- Focus on constraint-based elimination, not memorization only.

1. Which GCP compute service gives full OS control?
   - Correct answer: Compute Engine.
   - Why: It is the IaaS option where you control machine image, OS packages, and host runtime settings.

2. Which service is usually best for a stateless containerized HTTP API with minimum ops?
   - Correct answer: Cloud Run.
   - Why: It is designed for managed container serving with automatic scaling and low infrastructure overhead.

3. Which service is best for event-triggered lightweight logic (for example, on file upload)?
   - Correct answer: Cloud Run functions.
   - Why: Function-style event handlers are the primary fit for trigger-driven small units of work.

4. Which option is a managed app platform with strong version deployment workflow?
   - Correct answer: App Engine.
   - Why: It is PaaS-oriented and supports managed runtime behavior with version traffic controls.

5. What is a Managed Instance Group (MIG) primarily used for?
   - Correct answer: Autoscaling and autohealing VM fleets.
   - Why: MIG provides template-based fleet management and unhealthy instance replacement.

6. A workload needs custom kernel-level software. Which service is most appropriate?
   - Correct answer: Compute Engine.
   - Why: Managed serverless/PaaS options do not provide deep host-level customization.

7. A bursty public API has low traffic most of the day. Which option usually aligns with cost and ops goals?
   - Correct answer: Cloud Run.
   - Why: Demand-driven scaling avoids paying for idle always-on VMs in many intermittent traffic patterns.

8. A legacy monolith must migrate quickly with minimal code change. What is common first step?
   - Correct answer: Compute Engine.
   - Why: Lift-and-shift is often fastest on VMs; modernization can follow later.

9. What does multi-zone MIG deployment improve?
   - Correct answer: Resilience against zonal failure.
   - Why: Workload instances remain available across separate zones in a region.

10. What should usually be paired with MIG for resilient inbound traffic routing?
    - Correct answer: Load balancing with health checks.
    - Why: Health checks route traffic only to healthy backends.

11. In Cloud Run, what is a revision?
    - Correct answer: Immutable deployed version of a service.
    - Why: Revisions support controlled rollout and rollback.

12. In Cloud Run, what is a job?
    - Correct answer: Finite-run container execution for batch/task workflows.
    - Why: Jobs are not long-lived request-serving endpoints.

13. Why should compute workloads use dedicated service accounts?
    - Correct answer: To enforce least-privilege workload identity.
    - Why: Shared high-privilege identities increase blast radius.

14. For Compute Engine guest OS patching, whose responsibility is it?
    - Correct answer: Customer responsibility.
    - Why: IaaS gives control and patching duty to the customer side.

15. What IAM principle should be applied first across all compute services?
    - Correct answer: Least privilege.
    - Why: Security posture depends heavily on tightly scoped identities.

16. Public HTTP endpoint is required for a service. What additional pattern is commonly recommended?
    - Correct answer: Controlled ingress with edge protection (for example, load balancing and WAF patterns as needed).
    - Why: Public exposure should include threat mitigation and access controls.

17. A latency-sensitive Cloud Run API shows cold-start impact. Which lever is commonly used?
    - Correct answer: Configure minimum instances for critical paths.
    - Why: Warm baseline instances reduce startup latency at the cost of some idle spend.

18. Cloud Run tail latency worsens under load for CPU-heavy handlers. What is likely adjustment?
    - Correct answer: Lower concurrency and retest.
    - Why: Too much per-instance parallelism can saturate CPU and hurt `p95/p99`.

19. Why is idempotency important in Cloud Run functions?
    - Correct answer: Retries and duplicate event delivery can occur.
    - Why: Idempotency prevents duplicate side effects.

20. Two answers both work technically. What tie-breaker is usually best in ACE scenarios?
    - Correct answer: Least operational complexity that meets all hard constraints.
    - Why: Exam answers typically favor simpler managed designs unless control requirements force complexity.

21. Which option can be cost-effective for steady high-utilization workload with host tuning needs?
    - Correct answer: Compute Engine (often with rightsizing and discount strategy).
    - Why: Stable utilization can justify VM model economics.

22. Which model usually fits simple intermittent event automation?
    - Correct answer: Cloud Run functions.
    - Why: Event-driven execution aligns with sporadic trigger workloads.

23. Why should compute and data be co-located regionally when possible?
    - Correct answer: Lower latency and lower cross-region transfer cost.
    - Why: Network distance affects both performance and spending.

24. Which App Engine feature helps safe rollout?
    - Correct answer: Version traffic splitting and rollback.
    - Why: Controlled traffic migration reduces deployment risk.

25. Requirement says "strict security baseline + no long-lived credentials." What identity pattern fits?
    - Correct answer: Use service accounts from managed runtime identity and avoid static keys.
    - Why: Keyless identity reduces secret sprawl and key-rotation risk.

26. Event workflow became heavy and long-running. What is common correction?
    - Correct answer: Move heavy logic to Cloud Run service/job or queue-worker pattern.
    - Why: Function handlers should remain focused and bounded.

27. Application still slows down after scaling compute instances. What is likely issue?
    - Correct answer: Downstream bottleneck (database/API/network) was not addressed.
    - Why: End-to-end profiling is required; compute may not be limiting factor.

28. Which metrics should be monitored across compute models?
    - Correct answer: Latency, errors, throughput, and saturation.
    - Why: These "golden signals" reflect user-impact performance.

29. What is a poor security design pattern across environments?
    - Correct answer: One shared high-privilege service account for many apps.
    - Why: It violates least privilege and increases impact of compromise.

30. What is a common migration strategy when deadline is tight and refactor risk is high?
    - Correct answer: Migrate first to Compute Engine, modernize incrementally.
    - Why: Reduces immediate migration friction.

31. What scaling pattern helps absorb sudden request spikes safely?
    - Correct answer: Queue/buffer based decoupling with async workers.
    - Why: It smooths load and protects backends.

32. Why set max instances in Cloud Run?
    - Correct answer: To cap burst fan-out and protect downstream systems/cost.
    - Why: Unbounded scaling can overload dependencies.

33. Requirement: private internal HTTP service between workloads only. What ingress pattern fits?
    - Correct answer: Internal-only ingress configuration with private networking.
    - Why: Public exposure is unnecessary and riskier.

34. For VM workloads, when should you avoid assigning external IP by default?
    - Correct answer: When inbound internet access is not required.
    - Why: Private IP + controlled egress reduces attack surface.

35. Requirement: survive full regional outage for critical service. What design domain changes?
    - Correct answer: Add cross-region DR pattern.
    - Why: Multi-zone alone does not cover regional failure.

36. Is serverless always cheapest?
    - Correct answer: No.
    - Why: Cost depends on traffic profile, tuning choices, and dependency architecture.

37. A workload requires deep host daemon control and persistent local tuning. Best compute fit?
    - Correct answer: Compute Engine.
    - Why: Managed runtimes abstract host control away.

38. Security team asks for better detection after deployment changes. What should be added?
    - Correct answer: Audit/log-based alerts tied to IAM and deployment events.
    - Why: Fast anomaly detection shortens incident response time.

39. What is the first step before selecting any compute service in scenario questions?
    - Correct answer: Clarify hard constraints (ops, control, scale, compliance, cost).
    - Why: Service choice is a consequence of constraints.

40. Final compute decision rule for ACE questions?
    - Correct answer: Choose the simplest service that satisfies required control, reliability, security, and cost goals.
    - Why: This rule consistently avoids over-engineered wrong answers.

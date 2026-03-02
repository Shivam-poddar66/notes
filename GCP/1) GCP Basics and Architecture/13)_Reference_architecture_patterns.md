# Reference Architecture Patterns (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Reusable architecture patterns you can map to exam scenarios.
- Pattern components, when-to-use, strengths, and tradeoffs.
- How to choose the right pattern quickly under time pressure.
- Hands-on pattern practice and scenario Q&A.

## 1) How to use reference patterns in exams

When a scenario question appears:
1. identify workload type (web/API, batch, data analytics, internal system),
2. identify constraints (latency, compliance, budget, ops skill, RTO/RPO),
3. map to closest proven pattern,
4. pick services matching least operational overhead that still meet constraints.

## 2) Pattern A: Startup web application baseline

### Architecture
- single region,
- multi-zone stateless app tier,
- managed relational DB with backup,
- load balancer and autoscaling.

### Best for
- growing business applications,
- moderate uptime requirements,
- limited platform team size.

### Strengths
- balanced cost and reliability,
- simple operations,
- easy future evolution.

### Watchouts
- no full region-outage resilience by default,
- DB performance tuning still required as scale grows.

## 3) Pattern B: Event-driven serverless API platform

### Architecture
- API entry on managed HTTP endpoint,
- serverless compute for request handling,
- event bus/queue for async workloads,
- managed data services for persistence.

### Best for
- variable/spiky traffic,
- rapid delivery teams,
- workloads needing low ops burden.

### Strengths
- scales automatically,
- reduced idle infrastructure cost,
- fast developer iteration.

### Watchouts
- cold-start/runtime behavior tuning,
- dependency design needed to avoid latency spikes.

## 4) Pattern C: Compliance-focused enterprise platform

### Architecture
- approved regional placement,
- strict hierarchy and IAM boundaries,
- private networking,
- centralized audit and policy controls,
- DR aligned to documented RTO/RPO.

### Best for
- regulated industries,
- strict audit and governance needs,
- data residency constraints.

### Strengths
- strong control and compliance posture,
- consistent governance at scale.

### Watchouts
- higher policy/process overhead,
- slower change velocity if automation is weak.

## 5) Pattern D: Global consumer API

### Architecture
- multi-region user-facing endpoints,
- global traffic steering/load balancing,
- regional service deployments near user clusters,
- consistent CI/CD and observability.

### Best for
- global user base with strict latency targets.

### Strengths
- improved user experience by geography,
- stronger regional resilience options.

### Watchouts
- complex release and incident operations,
- higher network and replication cost.

## 6) Pattern E: Data analytics and reporting platform

### Architecture
- object storage/data lake ingestion,
- managed data warehouse for analytics,
- scheduled pipeline/orchestration,
- BI/reporting integration.

### Best for
- business intelligence,
- large-scale analytical SQL workloads.

### Strengths
- scalable analytics,
- clear separation from transactional workloads.

### Watchouts
- uncontrolled query patterns can increase cost rapidly,
- data governance and quality controls are essential.

## 7) Pattern F: Legacy migration landing zone

### Architecture
- VM-based rehost starting point,
- centralized IAM/network guardrails,
- phased modernization to managed services over time.

### Best for
- fast migration from on-prem systems,
- workloads with OS-level dependencies.

### Strengths
- lower migration friction,
- preserves compatibility during transition.

### Watchouts
- potential long-term ops overhead if modernization stalls.

## 8) Pattern G: Business-critical DR architecture

### Architecture
- primary region production,
- secondary region standby (pilot light or warm standby),
- tested failover runbook,
- replicated backups/data paths aligned to RTO/RPO.

### Best for
- mission-critical services needing regional outage resilience.

### Strengths
- stronger continuity posture.

### Watchouts
- extra operational complexity and cost,
- requires routine failover testing.

## 9) Pattern selection matrix (quick guide)

- Minimum ops + scalable API -> serverless API pattern.
- Strict OS/runtime dependency -> legacy migration/VM pattern.
- High compliance/data residency -> compliance-focused enterprise pattern.
- Global low-latency users -> global consumer API pattern.
- Analytics-heavy use case -> data analytics platform pattern.
- Strong regional DR requirement -> business-critical DR pattern.

## 10) Architecture quality checklist

For any selected pattern, validate:
1. reliability (zone/region failure handling),
2. security (IAM, network boundaries, secrets, audit),
3. performance (latency targets and scaling behavior),
4. cost (steady and peak operating profile),
5. operability (monitoring, runbooks, team readiness).

## 11) Common pattern misuse mistakes

- applying global multi-region design without business need,
- using VM-heavy pattern for highly spiky stateless API,
- mixing transactional and analytical workloads on same data path,
- ignoring governance requirements in regulated workloads,
- choosing architecture before defining recovery targets.

## 12) Exam pattern traps

1. Trap: selecting the most complex architecture by default.
2. Trap: ignoring stated operational skill constraints.
3. Trap: ignoring phrase "minimum administration overhead."
4. Trap: choosing non-compliant region strategy for regulated scenario.
5. Trap: missing DR requirement hidden in RTO/RPO wording.

## 13) Hands-on pattern exercises

1. Build startup baseline pattern in one region and test zonal failure behavior.
2. Deploy one serverless API and move one background task to async processing.
3. Build simple analytics flow: ingest file -> transform -> query.
4. Draft DR runbook for primary/secondary region pattern.
5. Compare estimated cost and ops complexity across two patterns for same use case.

## 14) Quick revision sheet

- Reference patterns accelerate scenario decision-making.
- Always match pattern to constraints, not preference.
- Start simple and scale architecture only when requirements demand.
- Reliability, security, performance, cost, and operations must be evaluated together.
- In ACE exam, "minimum ops" is often a strong clue toward managed/serverless patterns.

## 15) Practice questions with answers

1. Which pattern is a good baseline for many production web apps?
   - Single-region multi-zone startup web app pattern.

2. Which pattern best fits highly variable traffic with low ops overhead?
   - Event-driven serverless API platform.

3. Which pattern is most suitable for strict compliance and audit requirements?
   - Compliance-focused enterprise platform.

4. Which pattern is best for global low-latency consumer traffic?
   - Global consumer API pattern.

5. Which pattern is best for large analytical SQL workloads?
   - Data analytics and reporting platform.

6. Which pattern helps when migrating legacy OS-dependent workloads quickly?
   - Legacy migration landing zone pattern.

7. Which pattern targets region outage continuity?
   - Business-critical DR architecture.

8. What is a key drawback of global multi-region patterns?
   - Higher complexity and cost.

9. What is a key drawback of VM-first migration if unchanged long term?
   - Ongoing operational overhead.

10. Which phrase in exam usually indicates managed/serverless preference?
    - "Minimum operational overhead."

11. Why should pattern selection include team skill consideration?
    - Operational complexity must match execution capability.

12. What should be defined before selecting DR pattern?
    - RTO and RPO requirements.

13. Why avoid applying most advanced pattern by default?
    - It may add unnecessary cost and complexity.

14. What is a common analytics architecture mistake?
    - Running heavy analytics on transactional path.

15. Why compare patterns for same use case?
    - To select best tradeoff among reliability, cost, and complexity.

16. If compliance requires specific geography, what must pattern include?
    - Region-restricted deployment and governance enforcement.

17. What check confirms pattern operational readiness?
    - Monitoring, alerting, and tested runbooks.

18. If users are global and latency-sensitive, what pattern signal appears?
    - Multi-region user-near deployment with global routing.

19. In exam scenario answers, what is often rewarded?
    - Right-size architecture that exactly meets constraints.

20. Top exam takeaway for pattern questions?
    - Map requirements to proven architecture patterns and avoid over-engineering.

---

Use this chapter with scenario-based decision guide to answer service-selection questions quickly and accurately.

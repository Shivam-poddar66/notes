# Common Mistakes (High Exam Relevance) - Detailed Notes

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- High-frequency mistakes candidates make in architecture and exam questions.
- Why each mistake is wrong and what correct approach looks like.
- Fast correction checklist for each domain.
- Elimination strategy for tricky scenario options.

## 1) Foundation mistakes

### Mistake: confusing region and zone
- Why wrong:
  - region is geography,
  - zone is failure domain inside region.
- Correct approach:
  - map requirement to failure scope first.

### Mistake: ignoring resource scope (global/regional/zonal)
- Why wrong:
  - scope determines blast radius and placement constraints.
- Correct approach:
  - identify resource scope before architecture decision.

### Mistake: choosing nearest region only
- Why wrong:
  - compliance and service availability can override proximity.
- Correct approach:
  - evaluate compliance -> latency -> features -> cost.

## 2) Reliability mistakes

### Mistake: critical workload in single zone
- Why wrong:
  - single point of failure.
- Correct approach:
  - multi-zone baseline for production.

### Mistake: no clear RTO/RPO before DR design
- Why wrong:
  - leads to wrong DR architecture and cost mismatch.
- Correct approach:
  - define recovery targets first.

### Mistake: backup configured but restore never tested
- Why wrong:
  - unverified recovery path.
- Correct approach:
  - schedule and document restore drills.

### Mistake: only infrastructure monitoring, no user SLO monitoring
- Why wrong:
  - outages can happen with healthy infra metrics.
- Correct approach:
  - monitor latency/error/availability at user impact layer.

## 3) Security and IAM mistakes

### Mistake: broad Editor/Owner grants for convenience
- Why wrong:
  - excessive privilege and blast radius.
- Correct approach:
  - least privilege with granular roles.

### Mistake: shared service account across many workloads
- Why wrong:
  - poor isolation and weak audit clarity.
- Correct approach:
  - dedicated service accounts by workload boundary.

### Mistake: storing secrets in code or plaintext config
- Why wrong:
  - high credential exposure risk.
- Correct approach:
  - use Secret Manager and controlled access.

### Mistake: "fully managed service means no security responsibility"
- Why wrong:
  - customer still owns IAM, data handling, and secure config.
- Correct approach:
  - apply shared-responsibility model correctly.

## 4) Networking mistakes

### Mistake: open firewall rules for quick troubleshooting
- Why wrong:
  - broad attack surface.
- Correct approach:
  - least-access network policy and temporary controlled exceptions.

### Mistake: ignoring cross-region traffic path
- Why wrong:
  - hidden latency and egress cost.
- Correct approach:
  - keep tightly coupled services local when possible.

### Mistake: no private connectivity pattern for internal systems
- Why wrong:
  - unnecessary internet exposure.
- Correct approach:
  - prefer private network/service access options.

## 5) Cost mistakes

### Mistake: no budget alerts and no ownership tags
- Why wrong:
  - delayed spend detection and weak accountability.
- Correct approach:
  - set budgets early and enforce labels.

### Mistake: overprovisioning as default strategy
- Why wrong:
  - chronic waste.
- Correct approach:
  - right-size with measured demand and autoscaling.

### Mistake: choosing cheapest design without reliability/security fit
- Why wrong:
  - may fail business requirements.
- Correct approach:
  - satisfy mandatory targets first, then optimize cost.

## 6) Performance mistakes

### Mistake: optimizing average latency only
- Why wrong:
  - tail latency (p95/p99) drives user pain.
- Correct approach:
  - monitor and optimize percentile latency.

### Mistake: scaling compute before checking DB/query bottleneck
- Why wrong:
  - root cause remains unresolved.
- Correct approach:
  - fix actual bottleneck first.

### Mistake: deep synchronous dependency chains
- Why wrong:
  - compounded latency and failure propagation.
- Correct approach:
  - shorten critical path and offload async work.

## 7) Governance mistakes

### Mistake: one project for all environments
- Why wrong:
  - poor isolation for IAM, quota, billing, and incident blast radius.
- Correct approach:
  - separate projects by environment/risk boundary.

### Mistake: no folder strategy in growing organization
- Why wrong:
  - governance becomes fragmented and inconsistent.
- Correct approach:
  - structure folders by business unit and environment.

### Mistake: policy only documented, not enforced
- Why wrong:
  - drift and non-compliant deployments.
- Correct approach:
  - implement org policy guardrails and continuous audit checks.

## 8) Exam-taking mistakes

### Mistake: answering from memorized service names only
- Why wrong:
  - ACE is scenario/tradeoff oriented.
- Correct approach:
  - map requirement -> constraints -> service choice.

### Mistake: ignoring words like "minimum management" or "strict compliance"
- Why wrong:
  - these are key decision signals.
- Correct approach:
  - highlight requirement keywords before selecting answer.

### Mistake: picking most complex architecture
- Why wrong:
  - exam often rewards right-sized solution.
- Correct approach:
  - choose simplest architecture that meets all requirements.

### Mistake: not eliminating obviously wrong options first
- Why wrong:
  - increases confusion in close questions.
- Correct approach:
  - remove options that violate core constraints quickly.

## 9) Correction checklist by domain

### Reliability correction
- multi-zone for critical apps,
- defined RTO/RPO,
- tested backups and failover runbooks.

### Security correction
- least privilege,
- secure secret handling,
- strong audit visibility,
- no unnecessary public exposure.

### Cost correction
- labels + budgets + ownership,
- right-size and autoscale,
- reduce egress-heavy patterns.

### Operations correction
- SLO-based monitoring,
- deployment safety with rollback,
- periodic governance and IAM reviews.

## 10) Fast elimination rules for exam questions

1. Reject options violating compliance/location requirements.
2. Reject options that fail explicit uptime/DR requirement.
3. Reject options requiring high ops when question asks low ops.
4. Reject options with obvious over-privileged security posture.
5. Prefer option that meets requirements with least complexity.

## 11) Hands-on anti-pattern drills

1. Deploy small app in single zone, then redesign to multi-zone.
2. Replace broad role with least-privilege role and verify function still works.
3. Add budget + labels and inspect cost attribution.
4. Move one cross-region dependency to same region and observe latency impact.
5. Create restore test from backup and document recovery steps.

## 12) Quick revision sheet

- Most exam mistakes come from ignoring constraints, not lack of service names.
- Architecture answers must satisfy reliability, security, compliance, and cost together.
- Prefer right-sized solutions over over-engineered designs.
- Always check blast radius, privilege scope, and ops burden.

## 13) Practice questions with answers

1. What is a frequent cause of wrong exam answers?
   - Ignoring scenario constraints and picking service by memory only.

2. If question says "minimum management," what type of answer is often favored?
   - Managed/serverless approach.

3. Why is single-zone production risky?
   - It creates single point of failure.

4. Why is broad Editor access dangerous?
   - Excess privilege and larger blast radius.

5. What should be set early for cost control?
   - Budget alerts and labels.

6. Why must restore be tested, not just backup configured?
   - Recovery capability must be proven.

7. What is wrong with one project for all environments?
   - Weak isolation and governance.

8. If an option violates compliance location requirement, should it be considered?
   - No.

9. What is the risk of deep synchronous dependency chains?
   - Higher latency and cascading failures.

10. What is a key indicator in question text for service choice?
    - Explicit operational and reliability constraints.

11. Why not choose most complex option by default?
    - Complexity without requirement is a design mistake.

12. What is a common hidden cost source?
    - Cross-region and internet egress traffic.

13. What should you inspect before scaling compute for performance?
    - Actual bottleneck, often DB or network path.

14. What is the best IAM principle for exam answers?
    - Least privilege.

15. Why enforce policies rather than only document them?
    - Enforcement prevents drift.

16. What does "right-sized architecture" mean in exam context?
    - Meets requirements with minimal necessary complexity.

17. Why is keyword scanning useful in scenario questions?
    - It quickly reveals mandatory constraints.

18. What is a top governance anti-pattern?
    - No folder/project strategy as organization grows.

19. If reliability requirement is zone-failure only, what is often enough?
    - Multi-zone single-region design.

20. Top exam takeaway from this chapter?
    - Avoid common traps by validating every answer against explicit scenario constraints.

---

Use this chapter before mock tests to reduce avoidable mistakes and improve scenario selection accuracy.

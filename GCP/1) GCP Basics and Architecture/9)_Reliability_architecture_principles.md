# Reliability Architecture Principles (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Reliability fundamentals and design mindset.
- SLI, SLO, SLA and failure-domain thinking.
- High availability and disaster recovery patterns in GCP.
- Data reliability, observability, incident operations.
- Reliability vs cost tradeoffs for exam scenarios.

## 1) Reliability fundamentals

Reliability means a system consistently delivers correct service at expected quality under normal and failure conditions.

Reliability outcomes:
- stable uptime,
- predictable performance,
- controlled failures with fast recovery,
- minimal data loss during incidents.

## 2) Key reliability terms (must know)

### Availability
- Percentage of time system is usable.

### Durability
- Probability data is retained without loss.

### SLI (Service Level Indicator)
- Measurable signal of service behavior (error rate, latency, uptime).

### SLO (Service Level Objective)
- Target value for an SLI (for example 99.9% availability).

### SLA (Service Level Agreement)
- Contractual guarantee, usually external and business-facing.

### Error budget
- Allowed unreliability within SLO target.
- If error budget is spent, reduce release risk and prioritize stability work.

## 3) Design for failure mindset

Core principle:
- Assume failures will happen at zone, service, dependency, network, and deployment levels.

Design actions:
- remove single points of failure,
- spread workloads across failure domains,
- automate health checks and failover,
- keep rollback and recovery paths tested.

## 4) Failure domains in GCP

- Zonal failure: affects zonal resources in one zone.
- Regional failure: affects services/resources in one region.
- Dependency failure: upstream API, DB, queue, auth component issue.
- Human/configuration failure: bad deploy, bad policy, incorrect IAM/firewall.

Reliability strategy must explicitly map each critical component to a failure domain.

## 5) High availability architecture patterns

### Pattern A: Single region, multi-zone
- baseline production reliability for many workloads,
- protects against zone-level outages.

### Pattern B: Regional managed service plus zonal stateless compute
- app tier across zones,
- data service with managed backup/HA options.

### Pattern C: Multi-region active-passive
- primary region serves traffic,
- secondary region warm standby or pilot light,
- failover based on runbook and business target.

### Pattern D: Multi-region active-active
- traffic served from multiple regions,
- strongest continuity, highest complexity.

## 6) Disaster recovery principles

### RTO and RPO mapping
- strict RTO/RPO requires stronger automation and replication design.
- weaker RTO/RPO can use backup-and-restore with lower cost.

### DR models
- Backup and restore:
  - lowest cost,
  - slowest recovery.
- Pilot light:
  - minimal always-on footprint in secondary region.
- Warm standby:
  - partially running standby environment.
- Active-active:
  - full multi-region serving model.

### Practical exam logic
- "survive zone outage" -> multi-zone.
- "survive region outage with low RTO" -> warm standby or active-active patterns.

## 7) Data reliability and consistency

Data reliability controls:
- backups with retention and restore tests,
- replication aligned with RPO,
- point-in-time recovery where supported,
- schema change safety and rollback strategy.

Data consistency tradeoff:
- stronger consistency may increase latency/complexity in distributed systems.
- eventual consistency patterns may improve scale but need application-level handling.

## 8) Reliability in deployment and change management

Most outages are change-induced.  
Reliability requires safe release strategy:

1. test in lower environments,
2. deploy gradually (canary/rolling),
3. monitor SLO impact,
4. auto or manual rollback if health degrades.

Operational controls:
- change windows for high-risk systems,
- automated policy checks in CI/CD,
- feature flags to reduce blast radius.

## 9) Observability and reliability operations

### Essential signals
- availability,
- latency,
- error rate,
- saturation/resource pressure.

### Reliability telemetry stack
- metrics and dashboards,
- centralized logs,
- distributed tracing,
- health checks and alert policies.

### Alerting principles
- alert on user impact and SLO burn rate,
- reduce noisy alerts,
- route alerts by service ownership.

## 10) Incident response and recovery readiness

### Response model
1. detect and triage severity,
2. contain impact,
3. restore service quickly,
4. perform root cause analysis,
5. implement preventive actions.

### Runbook quality checklist
- clear trigger conditions,
- owner and escalation chain,
- exact recovery steps,
- validation steps after recovery.

### Post-incident review
- no-blame analysis,
- timeline and contributing factors,
- remediation owners and deadlines.

## 11) Reliability testing

Test plans should include:
- zonal fail simulation,
- dependency outage simulation,
- backup restore drills,
- load/stress behavior under peak traffic,
- failover drills for regional scenarios.

Untested DR is not real DR.

## 12) Reliability vs cost tradeoffs

Higher reliability usually increases:
- replication spend,
- standby infrastructure cost,
- operational complexity.

Balance approach:
- map business criticality tier,
- assign target SLO/RTO/RPO per tier,
- choose minimum architecture that meets target.

## 13) Common reliability mistakes

- single-zone production for critical service,
- no backup restore testing,
- alerting on infrastructure only (no user impact),
- no rollout safety or rollback process,
- undefined SLOs,
- cross-region design without clear RTO/RPO need.

## 14) Exam traps and answer patterns

1. Trap: recommending multi-region when only zone failure is required.
2. Trap: ignoring RTO/RPO in DR answer.
3. Trap: assuming backups are enough for low-RTO requirement.
4. Trap: no mention of health checks/load balancing in HA questions.
5. Trap: focusing only on compute, ignoring data recovery path.

## 15) Hands-on checklist

1. Deploy a stateless app across two zones.
2. Add load balancer and health checks.
3. Simulate one backend failure and verify continuity.
4. Configure backup policy for data layer.
5. Perform one restore test and document duration.
6. Define SLO and basic alerts for availability and latency.
7. Run one controlled canary deployment with rollback verification.

## 16) Quick revision sheet

- Reliability = consistent service under normal and failure conditions.
- SLO drives reliability target; error budget drives release risk.
- Multi-zone handles zone failures.
- Multi-region improves regional outage resilience.
- RTO = recovery speed, RPO = data loss tolerance.
- Observability + tested runbooks are mandatory.
- Backup without restore testing is incomplete reliability design.

## 17) Practice questions with answers

1. What is the first reliability principle?
   - Design for failure.

2. What does SLO represent?
   - Target service objective for an SLI metric.

3. Which metric directly reflects user-facing speed?
   - Latency.

4. What architecture usually protects against zone outage?
   - Multi-zone in one region.

5. Which architecture is stronger for regional outage resilience?
   - Multi-region DR design.

6. What does RTO measure?
   - Time to recover service.

7. What does RPO measure?
   - Acceptable data loss window.

8. Why are health checks important?
   - They route traffic away from unhealthy backends.

9. What is a common low-cost DR model?
   - Backup and restore.

10. What is the strongest but most complex DR model?
    - Active-active multi-region.

11. If requirements demand low RTO, are backups alone usually enough?
    - Usually no.

12. Why use canary deployment for reliability?
    - To reduce blast radius of release risk.

13. What is error budget used for?
    - Balancing feature velocity and reliability work.

14. Why test restore regularly?
    - To confirm recovery process and timing actually work.

15. Which outage source is very common in real systems?
    - Change/configuration errors.

16. What should alerts prioritize?
    - User-impact and SLO-risk signals.

17. Is single-zone acceptable for critical production?
    - Generally no.

18. What is a reliability anti-pattern in monitoring?
    - Noisy alerts without clear actionability.

19. What should every critical system have besides architecture?
    - Documented and tested incident runbooks.

20. Top exam takeaway for reliability questions?
    - Map requirement to failure domain, then choose the minimum architecture that meets SLO/RTO/RPO.

---

Use this chapter with region strategy and global infrastructure notes to answer reliability scenarios correctly in the exam.

# Quick Exam Revision Sheet (High-Yield)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1) 60-second architecture map

- Cloud model: on-demand, elastic, metered.
- Core hierarchy: Organization -> Folder -> Project -> Resource.
- Project is the operational boundary for API, IAM, quota, billing.
- Region = geography, zone = failure domain inside region.
- Multi-zone = zone outage protection.
- Multi-region = stronger regional DR posture.
- Always design with tradeoffs: latency, resilience, compliance, cost.

## 2) Core definitions to memorize

- IaaS: VM-level control (Compute Engine).
- PaaS: managed app platform (App Engine).
- Serverless containers: Cloud Run.
- Event-driven functions: Cloud Run functions.
- Object storage: Cloud Storage.
- Relational managed DB: Cloud SQL.
- NoSQL document DB: Firestore.
- Analytics warehouse: BigQuery.

## 3) Must-know exam differences

- Region vs zone:
  - region is geographic area,
  - zone is isolated failure domain.
- Role vs permission:
  - role is a set of permissions,
  - permission is one action.
- Control plane vs data plane:
  - control plane configures resources,
  - data plane serves real traffic.
- Multi-zone vs multi-region:
  - multi-zone handles zone failures,
  - multi-region handles region-level continuity.

## 4) Service selection cheat sheet

- Need full OS/runtime control -> Compute Engine.
- Need scalable HTTP service with minimum ops -> Cloud Run.
- Need event trigger on file/message -> Cloud Run functions.
- Need managed web app platform style -> App Engine.
- Need SQL transactions -> Cloud SQL.
- Need flexible document model -> Firestore.
- Need huge analytical SQL workloads -> BigQuery.
- Need static files/backups/object store -> Cloud Storage.

## 5) Reliability quick rules

- Critical production should not be single-zone.
- Set RTO/RPO before DR design.
- Backups without restore testing are incomplete.
- Use health checks and load balancing for failover behavior.
- Monitor user impact (latency/error/availability), not only infrastructure.

## 6) Security quick rules

- Identity first: least privilege IAM.
- Use service accounts for workloads.
- Avoid broad basic roles in production.
- Store secrets in Secret Manager.
- Limit public exposure; prefer private paths when possible.
- Enable audit logging and alert on privileged changes.

## 7) Cost quick rules

- Budget alerts are alerts only, not automatic hard stops.
- Label everything: owner, app, env, cost_center.
- Watch network egress and cross-region traffic.
- Right-size and autoscale instead of fixed overprovisioning.
- Start with architecture that meets requirements, then optimize.

## 8) Governance quick rules

- Use folders for policy delegation at scale.
- Separate projects by environment and risk boundary.
- Apply controls centrally with org policies where needed.
- Review inherited IAM to catch unintended access.

## 9) Performance quick rules

- Place workloads near users and near dependencies.
- Optimize p95/p99 latency, not only average.
- Avoid deep synchronous dependency chains.
- Use caching and async offload for non-critical sync work.
- Measure before and after each optimization.

## 10) Common traps to avoid

- Confusing region and zone.
- Recommending multi-region when only zone resilience is required.
- Ignoring compliance/location keywords in question.
- Picking cheapest option that fails reliability/security.
- Choosing complex architecture when simple valid option exists.
- Assuming "fully managed" means no customer security duties.

## 11) Scenario keyword decoder

- "minimum operational overhead" -> managed/serverless answer likely.
- "strict OS dependency" -> Compute Engine likely.
- "event on upload/message" -> function/event-driven likely.
- "survive zone outage" -> multi-zone likely.
- "survive region outage" -> multi-region DR likely.
- "transactional SQL" -> Cloud SQL likely.
- "massive analytical SQL" -> BigQuery likely.

## 12) 10-point answer validation before submit

1. Does answer meet compliance/location requirements?
2. Does it meet uptime/DR requirement?
3. Does it match ops burden requirement?
4. Is security posture least-privilege and auditable?
5. Does data store type match workload pattern?
6. Does network design avoid unnecessary exposure?
7. Are latency constraints addressed?
8. Is cost reasonable for requirement?
9. Is there a simpler valid option?
10. Is any key phrase in question ignored?

## 13) Last 7-day revision plan

### Day -7 to -5
- Re-read chapters 1-8.
- Practice 40 scenario questions/day.
- Run 2 hands-on labs focused on IAM + networking.

### Day -4 to -3
- Re-read chapters 9-15.
- Practice 50 mixed questions/day.
- Do one timed mock (2 hours).

### Day -2
- Review mistakes notebook only.
- Rehearse service chooser and scenario decoder.
- Do one short 25-question timed set.

### Day -1
- No heavy new topics.
- Revise this sheet and your weak-area notes.
- Sleep properly.

## 14) Exam-day execution

- First pass: answer straightforward questions quickly.
- Flag long scenarios and return in second pass.
- Use elimination:
  - remove non-compliant options first,
  - remove over-engineered options if requirement is simple.
- Do final review for marked questions only.

## 15) Rapid recall (20 one-liners)

1. Project boundary: IAM/API/quota/billing.
2. VPC is global, subnets are regional.
3. VM instances are typically zonal.
4. Multi-zone for HA, multi-region for stronger DR.
5. Cloud Run for scalable API with low ops.
6. Cloud SQL for relational transactional apps.
7. Firestore for flexible document model.
8. BigQuery for analytics at scale.
9. Cloud Storage for objects/files/backups.
10. Least privilege is mandatory.
11. Service accounts are workload identities.
12. Budgets alert; they do not auto-stop by default.
13. Egress is a major hidden cost.
14. Audit logs support investigation/compliance.
15. Control plane and data plane must both be monitored.
16. p95/p99 matter for real user experience.
17. Backup restore testing is required.
18. Start simple, scale architecture when requirement demands.
19. Use labels for cost ownership and reporting.
20. Read constraints first, then choose service.

---

Use this as your final pre-exam sheet after completing all chapter notes and practice sets.

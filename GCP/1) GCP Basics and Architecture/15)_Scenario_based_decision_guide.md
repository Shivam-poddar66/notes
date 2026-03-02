# Scenario-Based Decision Guide (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- A repeatable method to solve ACE scenario questions quickly.
- Requirement decoding and elimination strategy.
- Decision trees for compute, storage, networking, IAM, and operations.
- Large scenario bank with answer logic.

## 1) Why scenario questions feel tricky

ACE questions usually have:
- multiple technically possible answers,
- one best answer that matches constraints most precisely,
- distractors that are valid in general but wrong for the specific requirement.

You pass by choosing the best-fit architecture, not by choosing most advanced service.

## 2) 6-step decision method

1. Identify workload type
- web/API, batch, event-driven, analytics, internal tool, migration.

2. Extract hard constraints
- compliance/location,
- uptime/DR,
- latency/performance,
- budget,
- operations effort.

3. Identify data shape
- relational transactional,
- document/noSQL,
- object files,
- analytics warehouse.

4. Map operations preference
- minimum management vs full control.

5. Eliminate invalid options
- violates compliance,
- violates reliability target,
- violates ops constraint.

6. Choose simplest architecture that satisfies all constraints.

## 3) Fast keyword mapping

- "minimum operational overhead" -> managed/serverless direction.
- "strict OS dependency/custom kernel/agent" -> Compute Engine direction.
- "event on file upload/message" -> function/event-driven direction.
- "transactional relational DB" -> Cloud SQL direction.
- "massive analytical SQL" -> BigQuery direction.
- "must survive zone outage" -> multi-zone direction.
- "must survive region outage" -> multi-region DR direction.
- "sensitive data in specific geography" -> location-constrained deployment.

## 4) Compute decision mini-tree

If question asks:
- Full VM/OS control -> Compute Engine.
- Deploy containerized HTTP service with low ops -> Cloud Run.
- Event-triggered small function logic -> Cloud Run functions.
- Traditional managed app platform style -> App Engine.

Then apply:
- HA requirement (single-zone, multi-zone, multi-region),
- cost sensitivity and traffic pattern,
- security and identity controls.

## 5) Data decision mini-tree

- Need relational transactions and SQL -> Cloud SQL.
- Need flexible document model at scale -> Firestore.
- Need object/file storage -> Cloud Storage.
- Need petabyte analytics with SQL -> BigQuery.

Then validate:
- latency and region proximity,
- backup/retention and DR needs,
- access/security requirements.

## 6) Network decision mini-tree

- Need private internal networking baseline -> VPC/subnet/firewall.
- Need public HTTP/HTTPS entry with advanced routing -> application load balancing.
- Need private outbound internet without public IP on instances -> Cloud NAT pattern.
- Need on-prem secure connectivity -> VPN/Interconnect patterns.

## 7) IAM/security decision mini-tree

- Human access -> IAM user/group roles, least privilege.
- Workload access -> service accounts with minimal scope.
- Governance constraints -> org/folder policy controls.
- Sensitive workloads -> strict logging, key/secret handling, and audit.

## 8) Reliability decision mini-tree

- Zone failure tolerance only -> multi-zone same region.
- Region failure tolerance -> cross-region DR (pilot light/warm standby/active-active).
- Strict low RTO/RPO -> stronger standby/active design with tested failover.
- Moderate resilience and budget focus -> single-region multi-zone baseline.

## 9) Cost-aware answer filter

After picking technically valid answer, ask:
- Does it over-engineer beyond requirement?
- Is there a simpler lower-cost option that still satisfies constraints?
- Does it introduce unnecessary operational burden?

Best exam answers are usually:
- compliant,
- reliable enough for requirement,
- operationally aligned,
- cost-conscious without under-design.

## 10) Common elimination mistakes

- Choosing complex multi-region architecture when only zone resilience is asked.
- Selecting VM-heavy option when question says minimum management.
- Ignoring phrase indicating compliance/location restriction.
- Picking cheap but non-compliant/non-reliable option.
- Missing that question asks for workload identity security best practice.

## 11) Scenario bank with model answers

1. Requirement: scalable HTTP API with minimum server management.
   - Best answer: Cloud Run.

2. Requirement: legacy app needs custom OS package and kernel tuning.
   - Best answer: Compute Engine.

3. Requirement: run code when object is uploaded to storage bucket.
   - Best answer: Cloud Run functions (event-driven).

4. Requirement: transactional ecommerce database with SQL semantics.
   - Best answer: Cloud SQL.

5. Requirement: global analytics over very large datasets with SQL queries.
   - Best answer: BigQuery.

6. Requirement: flexible schema mobile app backend with document model.
   - Best answer: Firestore.

7. Requirement: production app must survive a single zone outage.
   - Best answer: Multi-zone architecture in one region.

8. Requirement: app must continue during region outage with strict continuity goals.
   - Best answer: Multi-region DR architecture.

9. Requirement: strict requirement to keep data in approved geography only.
   - Best answer: choose compliant region and enforce location policy constraints.

10. Requirement: private VMs need outbound internet updates without public IP.
    - Best answer: Cloud NAT design.

11. Requirement: separate dev and prod permissions and billing visibility.
    - Best answer: separate projects (and often folder-level governance).

12. Requirement: reduce blast radius of IAM permissions.
    - Best answer: least privilege at lowest practical resource scope.

13. Requirement: detect unauthorized admin activity quickly.
    - Best answer: audit logs plus alerting on privileged changes.

14. Requirement: serve global users with lower latency.
    - Best answer: deploy closer to users and use global traffic routing pattern.

15. Requirement: batch jobs can tolerate interruptions and cost must be minimized.
    - Best answer: lower-cost interruptible compute pattern.

16. Requirement: high query latency caused by distant DB region.
    - Best answer: colocate latency-sensitive app and DB in same region when possible.

17. Requirement: organization wants central policy across many projects.
    - Best answer: use folder/org-level governance policies.

18. Requirement: low ops CI/CD pipeline for container builds.
    - Best answer: Cloud Build pipeline with triggers.

19. Requirement: need central place for runtime metrics and alerting.
    - Best answer: Cloud Monitoring dashboards and alert policies.

20. Requirement: need centralized logs and route to analytics destination.
    - Best answer: Cloud Logging with sink to BigQuery/Storage/PubSub as required.

21. Requirement: choose storage for large static files and backups.
    - Best answer: Cloud Storage.

22. Requirement: API traffic spikes unpredictably and idle periods are long.
    - Best answer: serverless compute model.

23. Requirement: maintain strict control over machine images and patch cycle.
    - Best answer: Compute Engine with hardened image process.

24. Requirement: migrate quickly from on-prem with minimal refactor first.
    - Best answer: rehost on Compute Engine, then modernize incrementally.

25. Requirement: exam asks "best first step" before selecting architecture.
    - Best answer: clarify constraints (compliance, RTO/RPO, ops, cost, latency).

## 12) Time-management strategy for exam

### First pass
- answer direct-confidence questions quickly,
- flag complex scenario questions.

### Second pass
- apply 6-step decision method to flagged questions,
- eliminate two clearly wrong options first.

### Final pass
- review marked answers for compliance/reliability conflicts,
- avoid changing answers without clear reason.

## 13) Tie-breaker rules for close options

If two options seem valid, prefer the one that:
1. directly satisfies explicit constraints,
2. has lower operational burden when requirement mentions minimal management,
3. avoids unnecessary complexity,
4. aligns with best-practice security and least privilege.

## 14) Hands-on decision drills

1. Create 10 custom scenarios and classify workload type + constraints.
2. For each, select primary and backup service choice with reasoning.
3. Map each answer to reliability, cost, and security impacts.
4. Time-box each scenario to 2-3 minutes.
5. Review wrong choices and identify which constraint was missed.

## 15) Quick revision sheet

- Read scenario constraints before service names.
- Compliance and reliability constraints override convenience.
- Minimum ops usually points toward managed/serverless.
- Compute/storage/network/IAM choices must remain consistent as one architecture.
- Simplest valid solution usually wins over over-engineered alternatives.

## 16) Practice scenario questions with answers

1. App needs low-latency global read access and high availability. First design direction?
   - Multi-region serving strategy with global traffic routing.

2. Team asks for fastest deployment with no server maintenance for API.
   - Cloud Run.

3. App requires PostgreSQL with ACID transactions.
   - Cloud SQL for PostgreSQL.

4. Need to process image after upload event.
   - Event-driven function pattern.

5. Need private communication between internal services.
   - VPC-based private networking with controlled firewall rules.

6. Requirement says survive one zone failure only.
   - Multi-zone in one region.

7. Requirement says survive full region outage.
   - Cross-region DR architecture.

8. Need strict control over OS and third-party security agent.
   - Compute Engine.

9. Need data analytics over huge datasets with SQL.
   - BigQuery.

10. Need flexible NoSQL doc schema for mobile app.
    - Firestore.

11. Need cost transparency by department.
    - Project/folder structure plus labels and billing reporting.

12. Requirement includes "minimum operational overhead" and "auto scale."
    - Managed/serverless solution is preferred.

13. Need to prevent deployment outside approved regions.
    - Organization policy location constraints.

14. Need secure workload identity without user credentials.
    - Service account based access.

15. Need to reduce internet exposure for internal app tier.
    - Private networking and restricted ingress.

16. Logs must be centrally retained and queried later.
    - Cloud Logging with appropriate sink destination.

17. System must recover quickly from failed deployment.
    - Progressive rollout plus rollback strategy.

18. Budget is tight, reliability requirement is moderate.
    - Single-region multi-zone right-sized architecture.

19. Exam option offers complex multi-region design but requirement does not mention regional DR.
    - Prefer simpler multi-zone answer.

20. One option is cheaper but violates compliance location requirement.
    - Reject it; choose compliant option.

---

Use this guide daily with mock tests until scenario decoding becomes automatic.

# DevOps And Monitoring (Complete Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Practical DevOps architecture on Google Cloud.
- CI/CD, artifact management, release strategies, and IaC workflow.
- Observability stack: metrics, logs, alerts, tracing, and incident response.
- Reliability and security overlays for production operations.
- Full roadmap for exam readiness and implementation.

## 1) Core DevOps and observability mental model

Use this 5-layer model:
1. Plan and change:
   - version control, branching, review gates.
2. Build and package:
   - reproducible builds and artifact integrity.
3. Release and deploy:
   - progressive rollout, rollback, environment promotion.
4. Observe and operate:
   - metrics, logs, tracing, alerts, runbooks.
5. Improve continuously:
   - postmortems, reliability targets, automation hardening.

Primary rule:
- Fast delivery without observability and rollback is operational risk.

Exam rule:
- Choose managed services and the simplest reliable delivery path that meets security and availability constraints.

## 2) CI/CD architecture patterns

### Pipeline stages
- Source -> build -> test -> security checks -> artifact -> deploy -> verify -> promote.

### Environment strategy
- Separate dev, stage, and prod.
- Promote immutable artifacts across environments.
- Keep environment-specific config externalized.

### Quality gates
- Unit/integration tests before deploy.
- Policy and security checks before promotion.
- Manual approval on high-risk production releases where needed.

### Anti-patterns
- Building directly on production hosts.
- Deploying unversioned artifacts.
- Skipping rollback plan.

Exam cues:
- "Automated build-test-deploy with managed service" -> Cloud Build centric pattern.

## 3) Source control and branching strategy

### Core practices
- Trunk-based or disciplined branch model with pull request reviews.
- Enforce code owners for critical paths.
- Require passing CI before merge.

### Release tagging
- Tag release versions.
- Align tags with deployment records and change approvals.

### Operational value
- Clear audit trail of who changed what and when.
- Faster incident triage and rollback.

Exam cues:
- "Need controlled release history and collaboration" -> strong VCS workflow with CI gates.

## 4) Cloud Build (deep dive)

### What Cloud Build provides
- Managed build and pipeline execution.
- Trigger-based automation from source changes.
- Supports build, test, scan, and deployment steps.

### Design patterns
- Reusable build configs/templates.
- Separate triggers by branch/environment.
- Use substitutions and parameterization for portability.

### Security patterns
- Least-privilege build service account.
- Avoid broad permissions in build identity.
- Keep secrets externalized.

### Reliability patterns
- Deterministic build steps.
- Retry-safe scripts where possible.
- Version and pin critical build dependencies.

Exam cues:
- "Managed CI/CD build pipeline" -> Cloud Build.

## 5) Artifact Registry and software supply chain

### Role in DevOps
- Store container images and build artifacts.
- Promote immutable artifact versions between environments.

### Best practices
- Enforce version tagging and retention policy.
- Scan artifacts for vulnerabilities.
- Restrict artifact pull/push by least privilege.

### Supply chain controls
- Build provenance awareness and trusted source pipeline.
- Block deployment from untrusted or unscanned artifacts when policy requires.

Exam cues:
- "Store and manage container images securely" -> Artifact Registry.

## 6) Cloud Deploy and release strategies

### Why release orchestration matters
- Separates build from release promotion.
- Supports stage-to-prod promotion controls.

### Progressive delivery patterns
- Canary rollout.
- Blue/green rollout where architecture supports.
- Automated verification and rollback criteria.

### Governance
- Approval steps for production.
- Environment-specific policies.

Exam cues:
- "Need controlled multi-environment release promotion" -> Cloud Deploy pattern.

## 7) Infrastructure as Code and environment consistency

### IaC principles
- Define infrastructure declaratively.
- Version control infrastructure changes.
- Apply through reviewed pipeline, not manual console drift.

### Benefits
- Reproducible environments.
- Change auditability.
- Easier rollback and disaster rebuild.

### Common tools/patterns
- Terraform-centric workflows in many teams.
- Plan/review/apply model with approvals for prod.

### Anti-patterns
- Manual changes in prod without IaC update.
- One shared state/process without environment separation.

Exam cues:
- "Consistent, repeatable infra provisioning" -> IaC approach.

## 8) Configuration and secret management in delivery

### Configuration strategy
- Keep environment config separate from code.
- Use parameterization per environment.

### Secret strategy
- Use Secret Manager for credentials/tokens.
- Reference secrets at runtime/build securely.
- Rotate secrets and validate safe rollout.

### Security pattern
- Build/deploy identities should access only required secrets.

Exam cues:
- "Store deploy/runtime secrets securely" -> Secret Manager integration.

## 9) Cloud Monitoring fundamentals

### What it provides
- Metrics collection, dashboards, alert policies, uptime checks.

### Monitoring design basics
- Start with user-impact indicators first.
- Build service-level dashboards per critical workload.

### Golden signals
- Latency.
- Traffic/throughput.
- Errors.
- Saturation.

Exam cues:
- "Need centralized metrics dashboards and alerting" -> Cloud Monitoring.

## 10) SLI/SLO thinking and dashboard design

### SLI and SLO basics
- SLI: measured behavior (for example success rate/latency).
- SLO: target objective for SLI.

### Dashboard strategy
- Executive reliability view:
  - availability,
  - latency,
  - error rate.
- Service deep-dive view:
  - resource saturation,
  - dependency health,
  - release markers.

### Error budget concept
- Reliability target creates an allowable failure budget.
- Release velocity should respect error budget consumption.

Exam cues:
- "Balance release speed and reliability" -> SLO/error budget approach.

## 11) Alerting and on-call design

### Alert quality principles
- Alert on user impact and actionable signals.
- Avoid noisy low-value alerts.
- Use severity tiers and escalation paths.

### Alert policy patterns
- Multi-condition policies for confidence.
- Burn-rate style SLO alerts for early risk detection.
- Distinct policies for availability, latency, and deployment failures.

### On-call operations
- Runbooks linked from alerts.
- Fast ownership routing and escalation.
- Track MTTA/MTTR trends.

Exam cues:
- "Reduce alert fatigue while improving response" -> actionable alert design.

## 12) Cloud Logging (deep dive)

### What it provides
- Centralized ingestion, search, analysis, and operational diagnostics.

### Logging strategy
- Structured logs where possible.
- Correlation IDs/request IDs for traceability.
- Severity and service labels for filtering.

### Operational value
- Faster troubleshooting.
- Better security and compliance visibility.
- Deployment impact analysis.

Exam cues:
- "Centralized log analysis and troubleshooting" -> Cloud Logging.

## 13) Log routing, sinks, and retention strategy

### Log sinks
- Route logs to:
  - BigQuery for analytics,
  - Cloud Storage for archival/compliance,
  - Pub/Sub for streaming workflows.

### Design patterns
- Keep default operational logs in logging backend.
- Export specific high-value streams to downstream systems.
- Set retention and lifecycle intentionally.

### Cost and governance
- Prevent unbounded logging cost growth.
- Define retention by compliance and operational value.

Exam cues:
- "Route logs to analytics data warehouse" -> sink to BigQuery.
- "Archive logs long-term" -> sink to Cloud Storage.

## 14) Tracing and performance diagnostics

### Why tracing matters
- Metrics tell you "something is wrong."
- Traces help show "where in request path it is wrong."

### Patterns
- Distributed tracing across service boundaries.
- Link traces, logs, and metrics with correlation IDs.
- Use profiling and performance tools for CPU/memory hotspots where needed.

### Operational use
- Identify slow dependencies.
- Reduce tail latency (`p95/p99`) and timeout chains.

Exam cues:
- "Need request-level latency breakdown" -> tracing approach.

## 15) Error reporting and incident correlation

### Error workflow
1. Capture exceptions with context.
2. Group and prioritize recurring errors.
3. Link to deployment/version metadata.
4. Assign owners and remediation timeline.

### Incident correlation pattern
- Correlate error spikes with:
  - deploy events,
  - infrastructure changes,
  - dependency outages.

### Reliability benefit
- Faster root cause detection.
- Better rollback decisions.

Exam cues:
- "Find and group app exceptions quickly" -> error reporting pattern.

## 16) Reliability engineering and operations

### Reliability controls
- SLO-based operation model.
- Progressive rollout and rollback.
- Capacity planning and load testing.
- Disaster and recovery runbooks.

### Postmortem culture
- Blameless analysis.
- Action items with owners and deadlines.
- Follow-up verification that fixes work.

### Operational KPIs
- MTTR.
- Change failure rate.
- Deployment frequency.
- Reliability objective attainment.

Exam cues:
- "Improve reliability without freezing releases" -> SRE principles with SLO/error budget.

## 17) DevSecOps and pipeline security

### Core controls
- Least-privilege pipeline identities.
- Dependency and artifact vulnerability checks.
- Secret scanning and policy checks.
- Environment separation and approval controls.

### Deployment hardening
- Promote only trusted artifacts.
- Restrict production deploy capability.
- Keep audit trail for every deployment action.

### Common risks
- Overprivileged CI account.
- Inline secrets in build scripts.
- Manual production changes bypassing pipeline.

Exam cues:
- "Secure software delivery lifecycle" -> integrate security gates into CI/CD.

## 18) Scenario answer patterns (high exam relevance)

1. Requirement: automate build-test-deploy on code push.
   - Typical answer: Cloud Build trigger-based pipeline.

2. Requirement: centralize logs and query operational issues.
   - Typical answer: Cloud Logging.

3. Requirement: send logs to analytics warehouse.
   - Typical answer: Logging sink to BigQuery.

4. Requirement: private long-term log archive.
   - Typical answer: Logging sink to Cloud Storage with lifecycle.

5. Requirement: track uptime and latency alerts.
   - Typical answer: Cloud Monitoring + uptime checks + alert policies.

6. Requirement: controlled promotion from stage to production.
   - Typical answer: Cloud Deploy style release promotion with approvals.

7. Requirement: keep infra changes repeatable and auditable.
   - Typical answer: IaC workflow in source control.

8. Requirement: reduce production incidents after releases.
   - Typical answer: canary/blue-green + rollback + SLO monitoring.

9. Requirement: avoid hardcoded secrets in pipeline/app.
   - Typical answer: Secret Manager integration.

10. Requirement: secure container artifact lifecycle.
    - Typical answer: Artifact Registry + vulnerability/security checks.

Exam elimination method:
1. Remove manual and non-repeatable options first.
2. Remove options with poor security posture (broad roles/plain secrets).
3. Remove options without observability/rollback support.
4. Choose simplest managed pipeline that meets constraints.

## 19) Common mistakes and exam traps

### Delivery mistakes
- Deploying directly from developer workstation.
- No staging validation before production.
- No rollback mechanism.

### Observability mistakes
- Monitoring infra only, not user-impact SLIs.
- High-noise alerts with no runbooks.
- No correlation across logs/metrics/traces.

### Security mistakes
- Overprivileged build/deploy service accounts.
- Secrets in source repositories.
- No artifact trust or scanning policy.

### Governance mistakes
- Untracked manual changes in production.
- Inconsistent environment config.
- No incident review feedback loop.

Exam traps:
- Choosing tools that solve only one part (for example build only) when scenario asks end-to-end delivery + monitoring.

## 20) Full roadmap (study and implementation)

### Objective
Build practical DevOps and monitoring capability with exam-ready decision speed.

### Phase 1: Fundamentals (Day 1 to Day 3)
1. Learn CI/CD pipeline stages.
2. Learn core monitoring and logging concepts.
3. Build one-page DevOps flow map.

Deliverable:
- CI/CD + observability concept sheet.

### Phase 2: Tooling deep dive (Day 4 to Day 8)
1. Day 4: Cloud Build triggers and pipeline basics.
2. Day 5: Artifact Registry and release versioning.
3. Day 6: Cloud Deploy and rollout strategies.
4. Day 7: Cloud Monitoring dashboards/alerts.
5. Day 8: Cloud Logging and sink routing.

Deliverable:
- Service-by-service implementation notes.

### Phase 3: Reliability and security integration (Day 9 to Day 12)
1. Build SLO/error budget model for one service.
2. Add secret manager and least-privilege pipeline identities.
3. Add incident response and rollback runbook.

Deliverable:
- Production readiness checklist.

### Phase 4: Scenario mastery (Day 13 to Day 15)
1. Solve timed DevOps/monitoring scenarios.
2. Categorize wrong answers by root cause.
3. Practice elimination rules repeatedly.

Deliverable:
- Personal trap list with corrected patterns.

### Phase 5: Hands-on lab sprint (Day 16 to Day 19)
1. Lab A: build and test trigger pipeline.
2. Lab B: artifact promotion to stage/prod.
3. Lab C: canary rollout and rollback simulation.
4. Lab D: dashboards, SLO alerting, uptime checks.
5. Lab E: log sink to BigQuery and Storage.
6. Lab F: incident drill with alert-to-runbook workflow.

Deliverable:
- Lab evidence and architecture notes.

### Phase 6: Final revision (Day 20 to Day 21)
1. Review quick revision sheet daily.
2. Run one full timed mock set.
3. Close weak areas and finalize decision templates.

Deliverable:
- Final revision checklist and confidence score by topic.

## 21) Quick revision sheet

### Must-remember facts
- Cloud Build = managed build and pipeline execution.
- Artifact Registry = artifact/image storage and lifecycle.
- Cloud Deploy = controlled release promotion strategy.
- Cloud Monitoring = metrics, dashboards, alerts, uptime checks.
- Cloud Logging = centralized logs and analysis.
- Log sinks route to BigQuery, Cloud Storage, Pub/Sub.

### Fast keyword map
- "automate build/deploy" -> Cloud Build.
- "artifact repository" -> Artifact Registry.
- "stage-to-prod release control" -> Cloud Deploy.
- "metrics and alerting" -> Cloud Monitoring.
- "centralized logs + routing" -> Cloud Logging + sinks.
- "secure secrets in pipeline" -> Secret Manager.

### Final chapter rule
Reliable DevOps means automated delivery, strong observability, secure identities/secrets, and tested rollback paths.


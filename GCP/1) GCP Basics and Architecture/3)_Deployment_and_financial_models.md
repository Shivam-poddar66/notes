# Deployment and Financial Models (Detailed Notes)

Last updated: February 24, 2026
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this note covers
- GCP deployment models and when to use each.
- Cloud financial model and billing structure.
- Cost drivers and optimization patterns.
- FinOps governance practices.
- Exam traps and scenario-based answer logic.

## 1) Deployment models in Google Cloud

### Public cloud model
- Core GCP model: resources run in Google-managed infrastructure.
- Benefits:
  - Rapid provisioning.
  - Elastic scale.
  - Global reach.
  - Managed services reduce ops overhead.
- Tradeoffs:
  - Ongoing operating expense.
  - Need strong governance for cost/security.

### Hybrid cloud model
- Workloads split between on-premises and Google Cloud.
- Common reasons:
  - Legacy systems cannot move immediately.
  - Regulatory constraints.
  - Low-latency integration with on-prem systems.
- Typical connectivity options:
  - Cloud VPN (encrypted over internet).
  - Cloud Interconnect (dedicated high-throughput private link).

### Multi-cloud model
- Workloads/services spread across more than one cloud provider.
- Common reasons:
  - Provider diversity.
  - Specialized services in different clouds.
  - Mergers/acquisitions with existing cloud footprint.
- Tradeoffs:
  - Higher operational complexity.
  - IAM/networking/compliance policy consistency becomes harder.

### Migration patterns (exam useful)
- Rehost: lift-and-shift VM migration.
- Replatform: limited modernization (managed DB, improved runtime).
- Refactor: redesign app to cloud-native services (Cloud Run, Pub/Sub, managed DB).
- Retain/retire: keep or decommission based on business value.

## 2) Service model choice and cost impact

### IaaS (Compute Engine)
- High control, higher management responsibility.
- Costs can include VM runtime, disks, snapshots, and network egress.

### PaaS (App Engine)
- Faster deployment and less infra management.
- Cost tied to instance usage, scaling, and related services.

### Serverless (Cloud Run, Cloud Run functions)
- Pay mainly for actual execution/request usage.
- Strong choice for spiky workloads because idle cost can be near zero.

### Managed data/analytics
- Cloud SQL, Firestore, BigQuery, Cloud Storage each have different billing dimensions.
- Architecture should match workload pattern to avoid mismatch costs.

## 3) Financial model in GCP

### Billing account basics
- Billing account pays for linked project usage.
- One billing account can pay for multiple projects.
- One project links to one billing account at a time.

### Core pricing model
- Predominantly pay-as-you-go.
- Billing is usage-based and service-specific.
- Typical dimensions:
  - Compute time.
  - Memory allocation/runtime.
  - Storage capacity and operation class.
  - Network egress.
  - API/request volume.

### Cost visibility structure
- Project-level cost view is foundational.
- Labels help split costs by app/team/environment/cost center.
- Export billing data to BigQuery for deeper analysis.

## 4) Common cost drivers to remember

1. Compute
- Always-on VM usage.
- Oversized machine types.
- Non-production resources left running 24x7.

2. Storage
- Wrong storage class for access pattern.
- Snapshot/backup growth without retention control.
- Excess object versioning without lifecycle policies.

3. Network
- Internet egress.
- Cross-region traffic.
- Architecture patterns with unnecessary data movement.

4. Data/analytics
- Inefficient query design (for example scanning too much data).
- Poor partitioning/clustering strategy in analytics workloads.

## 5) Discounts and commitment concepts

### Committed use style savings
- Useful for predictable baseline workloads.
- Lower unit cost in exchange for longer commitment behavior.
- Risk: overcommit if workload forecast is wrong.

### Spot/preemptible style usage
- Lower cost, but workload can be interrupted.
- Suitable for batch/fault-tolerant workloads.
- Not suitable for strict always-on critical components.

### Autoscaling economics
- Aligns capacity with demand.
- Reduces waste from overprovisioning.
- Needs proper limits and monitoring to prevent instability or unexpected spikes.

## 6) Budgeting, alerts, and guardrails

### Budgets and alerts
- Set budget thresholds early (for example 50%, 75%, 90%, 100%).
- Send notifications to email/chat/on-call channels.
- Important: budgets alert; they do not hard-stop charges by default.

### Quotas
- Limit resource/API consumption per project.
- Useful to control accidental over-provisioning.

### Governance guardrails
- Organization policies for allowed locations/services.
- IAM least privilege to reduce accidental expensive changes.
- Standard templates for approved architectures.

## 7) FinOps operating model for GCP teams

### FinOps principles
- Visibility: everyone sees cost impact.
- Accountability: each workload has clear owner.
- Optimization: continuous, not one-time.
- Value focus: optimize for business outcomes, not just lower spend.

### Practical team process
- Weekly cost review by app/team.
- Monthly rightsizing and cleanup cycle.
- Pre-production cost estimate for major architecture changes.
- Post-incident cost review for runaway resource events.

### Tagging and ownership minimums
- Required labels:
  - `owner`
  - `environment`
  - `application`
  - `cost_center`
- Enforce labeling in CI/CD or policy checks when possible.

## 8) Architecture decisions and cost tradeoffs

### Example tradeoffs
- Multi-region improves resilience, increases replication and network spend.
- Managed services reduce ops burden, may have premium pricing versus raw VMs.
- Serverless saves idle cost, but very high steady traffic may require comparison against reserved compute options.

### Decision framework
1. Define SLO and compliance requirements.
2. Estimate baseline and peak demand.
3. Choose architecture candidate options.
4. Estimate cost for each option.
5. Select design with best reliability-security-cost balance.

## 9) Exam scenario patterns (high value)

1. Requirement: "minimum ops + variable traffic API."
- Typical best answer: Cloud Run.

2. Requirement: "legacy app with OS-level dependency."
- Typical best answer: Compute Engine (possibly migration/rehost first).

3. Requirement: "predictable steady workload."
- Typical optimization: evaluate committed usage savings.

4. Requirement: "batch jobs can be interrupted."
- Typical optimization: spot/preemptible compute usage.

5. Requirement: "cost visibility by team and app."
- Typical solution: separate projects + consistent labels + billing export/reporting.

## 10) Common mistakes
- Treating cloud as fixed-capacity datacenter and overprovisioning.
- Not separating dev/stage/prod projects.
- Missing budget alerts and quota controls.
- Ignoring egress charges in design.
- Choosing premium resilience patterns without business need.
- Assuming serverless is always cheapest in every traffic profile.

## 11) Hands-on checklist

1. Create separate `dev` and `prod` projects.
2. Link both to billing account.
3. Add budget with threshold alerts.
4. Apply labels to all test resources.
5. Deploy one workload on VM and one on Cloud Run.
6. Compare monthly estimated cost behavior under low vs high traffic assumptions.
7. Review network egress in billing reports.
8. Shut down idle resources and verify cost impact trend.

## 12) Quick revision sheet

- Public cloud = default GCP consumption model.
- Hybrid = on-prem + cloud integration (VPN/Interconnect).
- Multi-cloud increases flexibility but also complexity.
- Cloud billing is usage-based and service-specific.
- Top cost drivers: compute, storage, network egress, analytics/query patterns.
- Budgets alert only; combine with governance automation.
- FinOps = visibility + ownership + continuous optimization.

## 13) Practice questions with answers

1. What is the default deployment model for GCP workloads?
   - Public cloud.

2. What is a common reason to choose hybrid cloud?
   - Legacy or compliance constraints requiring on-prem integration.

3. One billing account can be linked to how many projects?
   - Multiple projects.

4. Can a single project use multiple billing accounts at the same time?
   - No.

5. Which cost area is often underestimated in architecture design?
   - Network egress and cross-region transfer.

6. What is the key benefit of serverless for spiky traffic?
   - Cost tracks usage and can reduce idle cost.

7. What is a major risk of commitment discounts?
   - Overcommitting beyond actual usage needs.

8. Are budgets hard limits that automatically stop spending?
   - No.

9. Why are labels important for FinOps?
   - They enable ownership-based cost tracking and reporting.

10. Which is usually better for interruptible batch workloads: on-demand only or spot/preemptible?
    - Spot/preemptible usage is often better for cost.

11. What does rightsizing mean?
    - Adjusting resource capacity to actual workload demand.

12. Why split environments into separate projects?
    - Better isolation for IAM, quota, operations, and billing visibility.

13. In exam questions, if the requirement says "minimum operational overhead," what service pattern is usually favored?
    - Managed/serverless services.

14. What should you evaluate before choosing multi-region architecture?
    - RTO/RPO/compliance requirements and added cost impact.

15. What is the FinOps goal?
    - Maximize business value from cloud spend through continuous cost optimization.

16. Which control helps prevent accidental API/resource overuse?
    - Quotas.

17. Which reporting enhancement is common for advanced cost analytics?
    - Billing export to BigQuery.

18. Is lowest cost always the best architecture decision?
    - No, decision must balance reliability, security, compliance, and cost.

19. What is a typical early warning setup for spend risk?
    - Budget alerts at progressive thresholds.

20. What is the best first step before optimization?
    - Establish clear cost visibility and ownership.

---

Use this chapter with regions/zones and cloud model notes to build strong exam foundations for architecture and cost questions.

# Cloud Model and Global Architecture (Detailed Notes)

Last updated: February 24, 2026
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this note covers
- Cloud computing model in GCP context.
- Service models (IaaS, PaaS, serverless) and when to choose each.
- Shared responsibility and security ownership.
- Google global infrastructure (regions, zones, edge network).
- Architecture design tradeoffs: latency, resilience, compliance, and cost.
- Exam-focused scenarios and traps.

## 1) Cloud model in Google Cloud

### Cloud computing in one line
Cloud computing means consuming compute, storage, networking, and managed services on demand over the internet, with usage-based billing and elastic scaling.

### Why organizations use cloud
- Faster delivery: provision infrastructure in minutes, not weeks.
- Elasticity: scale up/down with demand.
- Managed operations: reduce undifferentiated infrastructure work.
- Global reach: deploy near users worldwide.
- Cost control: pay for usage instead of large upfront hardware investment.

### Key cloud characteristics (exam relevant)
- On-demand self-service.
- Broad network access.
- Resource pooling and multi-tenancy.
- Rapid elasticity.
- Measured service (metered billing).

## 2) Service models in GCP

### IaaS (Infrastructure as a Service)
- Primary GCP example: Compute Engine.
- You manage: OS, runtime, patches, app stack.
- Use when: you need OS-level control, custom images, legacy/lift-and-shift workloads.

### PaaS (Platform as a Service)
- Primary example: App Engine.
- You manage: application code and settings.
- Google manages: most platform/runtime scaling and infrastructure.
- Use when: fast app deployment matters more than low-level control.

### Serverless
- Primary examples: Cloud Run, Cloud Run functions.
- You manage: container/function code and configuration.
- Google manages: scaling, capacity, most runtime infrastructure.
- Use when: event-driven or HTTP workloads need fast scaling with minimal ops.

### Managed data/analytics services
- Cloud SQL, Firestore, BigQuery, Cloud Storage.
- You focus on data model, permissions, performance tuning, and governance.

## 3) Deployment and financial models

### Deployment model
- Public cloud with optional hybrid and multi-cloud connectivity.
- Hybrid patterns supported with VPN, Interconnect, Anthos-related approaches.

### Financial model
- Most services are pay-as-you-go.
- Discounts often available for predictable usage (for example commitments).
- Cost can scale quickly without governance; budgets and alerts are mandatory.

### FinOps basics for GCP
- Separate projects by environment/team for cost visibility.
- Use labels for cost center, owner, app, environment.
- Set budgets and alert thresholds early.
- Monitor egress costs and idle resources.

## 4) Shared responsibility model (practical)

### Google responsibility
- Physical facilities, hardware, global network backbone.
- Base infrastructure operations.
- Managed service platform operations at service level.

### Customer responsibility
- IAM design and least-privilege access.
- Data classification and encryption/key strategy choices.
- Network segmentation and firewall policy.
- Application security, code vulnerabilities, and secret management.
- Compliance mapping for your business requirements.

### Exam trap
- "Fully managed service" does not mean "no security responsibility."
- You still control access, data handling, and secure configuration.

## 5) Google global infrastructure fundamentals

### Core building blocks
- Region: geographic area (example `us-central1`).
- Zone: isolated failure domain inside region (example `us-central1-a`).
- Edge/PoP locations: help deliver traffic closer to users.

### Scope model you must remember
- Global resources: some networking constructs (for example VPC network).
- Regional resources: subnets and many managed service placements.
- Zonal resources: many VM-related deployments.

### Why global backbone matters
- Google routes traffic over its private backbone for performance and reliability.
- This improves consistency versus unpredictable public internet segments.

## 6) Region and zone strategy

### Choosing a region
- User proximity (latency).
- Data residency and regulatory constraints.
- Service availability for required products/features.
- Cost differences by region.

### Choosing zone layout
- Production baseline: at least two zones for critical workloads.
- Use zonal spreading with managed instance groups/load balancing.

### Multi-zone vs multi-region
- Multi-zone (same region):
  - Handles zone-level failures.
  - Lower complexity and often lower latency within region.
- Multi-region:
  - Improves regional disaster recovery posture.
  - More complex and possibly costlier.
  - Use when business requires region-outage resilience or geographic distribution.

### Exam pattern
- Requirement: "survive zone failure with minimal complexity" -> multi-zone.
- Requirement: "survive region outage / strict DR target" -> multi-region strategy.

## 7) Control plane and data plane mindset

### Control plane
- APIs and management operations used to create/configure resources.

### Data plane
- Actual workload traffic and data processing path.

### Why this matters
- During incidents, management API issues and workload traffic issues may differ.
- Good architecture includes monitoring for both provisioning and runtime behavior.

## 8) GCP resource hierarchy and architecture governance

### Hierarchy
- Organization -> Folder -> Project -> Resource

### Architecture meaning
- Organization: enterprise governance root.
- Folder: policy delegation by team/business/environment.
- Project: deployment and security boundary for workloads.
- Resource: actual service objects (VM, bucket, DB, etc.).

### Policy inheritance
- IAM and org policies typically inherit from parent to child.
- Granting broad roles at high hierarchy levels can unintentionally overexpose resources.

### Recommended governance pattern
- Folder split by business unit or environment.
- Project split by workload lifecycle and trust boundary.
- Least-privilege IAM at lowest practical scope.

## 9) Reliability architecture principles

### Design for failure
- Assume components can fail.
- Distribute workloads across zones.
- Use managed services with built-in durability where possible.
- Use health checks and load balancing for failover.

### Recovery objectives
- RTO (Recovery Time Objective): how fast service must recover.
- RPO (Recovery Point Objective): how much data loss is acceptable.
- Higher resilience targets increase architecture complexity and cost.

### Typical resilience progression
- Stage 1: single-zone workload (not production-grade for critical apps).
- Stage 2: multi-zone HA in one region.
- Stage 3: cross-region disaster recovery with replication/backups.

## 10) Security architecture basics tied to global design

### Identity-first security
- IAM is the first control plane.
- Use service accounts for workloads.
- Avoid broad primitive roles in production.

### Network controls
- Use VPC segmentation, firewall rules, private service connectivity patterns.
- Limit exposure of public IPs where not required.

### Data controls
- Classify sensitive data.
- Use encryption defaults and customer-managed keys when policy requires.
- Use Secret Manager for credentials and tokens.

### Observability and audit
- Enable logging and monitoring.
- Use audit logs for compliance and incident investigation.

## 11) Performance and latency design

### Latency drivers
- Distance between users and workload region.
- Cross-region calls and external dependencies.
- Inefficient architecture patterns (chatty services over long distances).

### Performance practices
- Place user-facing services closer to users.
- Keep high-frequency service-to-service traffic within low-latency boundaries.
- Use caching/CDN strategies for global content delivery when appropriate.

## 12) Cost architecture tradeoffs

### Major cost drivers
- Compute runtime.
- Storage volume/class and operation frequency.
- Network egress (especially inter-region/internet).
- Premium managed features for high resilience.

### Tradeoff examples
- Multi-region improves DR but can raise replication and network cost.
- Overprovisioning reduces risk but increases spend.
- Aggressive autoscaling saves cost but needs good SLO-aware tuning.

## 13) Reference architecture patterns (exam style)

### Pattern A: Startup web app
- Single region, multi-zone application tier.
- Managed database with backups.
- Load balancer + autoscaling.
- Good cost/resilience balance for early stage production.

### Pattern B: Compliance-heavy enterprise workload
- Approved region selection based on policy.
- Strong IAM boundaries and organization policies.
- Private networking and strict audit controls.
- Backup/DR mapped to documented RTO/RPO.

### Pattern C: Global consumer API
- Region placement near user clusters.
- Global traffic steering and caching strategy.
- Consistent CI/CD and observability across regions.

## 14) Common mistakes (high exam relevance)
- Confusing region and zone.
- Running production critical app in one zone only.
- Using one project for every environment and team.
- Granting `Editor` broadly to speed up delivery.
- Assuming budget alerts automatically stop spend.
- Ignoring egress and cross-region traffic cost impact.
- Choosing a region without checking compliance/service availability.

## 15) Scenario-based decision guide

1. Requirement: lowest ops burden for scalable HTTP service.
   - Common answer: Cloud Run.

2. Requirement: strict OS control for legacy app.
   - Common answer: Compute Engine.

3. Requirement: app must keep running if one zone fails.
   - Common answer: multi-zone architecture in one region.

4. Requirement: app must recover from regional outage.
   - Common answer: cross-region DR or active/active multi-region, based on RTO/RPO.

5. Requirement: separate billing/IAM/quota between dev and prod.
   - Common answer: separate projects.

## 16) Quick exam revision sheet
- Cloud model: on-demand, elastic, metered.
- Project is core boundary: API, IAM, quota, billing.
- Region = geography, Zone = failure domain.
- Multi-zone for HA; multi-region for stronger DR.
- You always retain responsibility for IAM, data handling, and secure configuration.
- Governance uses Org -> Folder -> Project inheritance.
- Architecture decisions are tradeoffs between latency, resilience, compliance, and cost.

## 17) Hands-on mini lab plan for this topic

### Lab 1: Region/zone understanding
- Create two VMs in two zones of the same region.
- Validate workload availability conceptually if one zone is unavailable.

### Lab 2: Project and governance
- Create separate `dev` and `prod` projects.
- Apply different IAM roles and compare access behavior.

### Lab 3: Cost controls
- Attach billing to projects.
- Create budget alerts at 50%, 90%, and 100%.
- Apply labels and inspect billing reports by label.

### Lab 4: Architecture decision practice
- Pick three use cases and justify:
  - service model (IaaS/PaaS/serverless),
  - region strategy,
  - HA/DR target,
  - cost control method.

## 18) Practice questions with answers

1. What is the difference between region and zone?
   - Region is a geographic location; zone is an isolated deployment/failure domain within a region.

2. Why is a project considered a critical architecture boundary?
   - It defines IAM, API enablement, quota, and billing scope.

3. Multi-zone architecture mainly protects against what?
   - Zone-level failures.

4. What is the customer responsibility in shared responsibility for managed services?
   - Access control, secure configuration, data governance, and application security.

5. When should you choose multi-region over multi-zone?
   - When business requires regional outage resilience or strong geographic distribution.

6. Which requirement mostly drives region selection first in regulated industries?
   - Compliance and data residency.

7. Which architecture choice can unexpectedly increase costs the most?
   - Unplanned cross-region and internet egress-heavy traffic patterns.

8. Is "fully managed" equivalent to "no IAM/security work needed"?
   - No.

9. Why split dev and prod into separate projects?
   - Better isolation for security, billing, quota, and operational blast radius.

10. What are the four major architecture tradeoff dimensions in GCP design questions?
    - Latency, resilience, compliance, and cost.

---

Use this note as the base chapter before compute, networking, storage/database, IAM/security, and operations topics.

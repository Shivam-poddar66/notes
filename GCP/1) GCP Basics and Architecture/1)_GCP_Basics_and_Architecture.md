# GCP Basics and Architecture (Complete Notes)

Last updated: February 24, 2026

## What this chapter covers
- Core GCP architecture concepts for Associate Cloud Engineer level.
- Regions and zones.
- Projects and project boundaries.
- Billing model and cost control basics.
- Resource hierarchy and policy inheritance.
- Common exam traps and scenario logic.

## 1) Cloud model and global architecture
- Google Cloud is a public cloud platform with managed services across compute, storage, database, networking, AI, and operations.
- You consume resources through a project and are billed by usage.
- Most services are managed by Google, but you still own configuration, access control, and data protection choices.

### Shared responsibility (practical view)
- Google manages physical datacenters, hardware, and many managed service internals.
- You manage IAM, network controls, data classification, workload configuration, and secure usage patterns.

### Global infrastructure design impact
- Google Cloud has a global network backbone.
- You place resources close to users for low latency and in required geographies for compliance.
- Architecture choices often balance latency, resilience, data residency, and cost.

## 2) Regions and zones

### Definitions
- Region: geographic area with multiple independent zones (example: `us-central1`).
- Zone: isolated deployment area/failure domain within a region (example: `us-central1-a`).

### Why this matters
- A zonal outage should not take down a multi-zone architecture.
- Region choice affects  latency,service availability, compliance, and price.
- Some services are global, some regional, some zonal. Scope affects design and failure behavior.

### High availability guidance
- For production, spread instances across at least two zones in one region.
- For disaster recovery, plan cross-region backups/replication where required.
- Use managed services with built-in replication options when possible.

### Region selection criteria
- User latency: pick a region close to primary users.
- Compliance/data residency: keep data in required countries/regions.
- Service availability: verify required service and feature availability in region.
- Cost: compare regional pricing if architecture allows flexibility.

### Common exam trap
- Question asks for high availability but low complexity:
  - Correct pattern is often multi-zone in one region first.
  - Multi-region is for stronger disaster recovery or residency/performance needs.

## 3) Projects

### What a project is
- A project is the primary administrative container for resources.
- Project is the boundary for:
  - API enablement
  - IAM policy attachment
  - Quotas
  - Billing linkage
  - Logging and monitoring scoping patterns

### Project identifiers
- Project ID: globally unique, immutable after creation.
- Project name: human-readable, can be changed.
- Project number: system-generated numeric ID.

### Project lifecycle basics
- Create project.
- Link billing account.
- Enable required APIs.
- Create/configure resources.
- Shutdown/delete when no longer needed.

### Good project strategy
- Separate projects by environment and risk domain:
  - `prod`, `staging`, `dev`, `sandbox`
- Use labels/tags for ownership, cost center, environment, and application.
- Keep least privilege boundaries clear between teams and workloads.

## 4) Billing fundamentals

### Core billing model
- Pay-as-you-go for most services.
- Costs depend on consumed resources (compute time, storage, network egress, API calls, etc.).
- Discounts can exist through committed use for predictable workloads.

### Billing account relationship
- One billing account can be linked to multiple projects.
- A project can be linked to one billing account at a time.

### Cost controls you must know
- Budgets and alerts:
  - Notify at thresholds (for example 50%, 90%, 100%).
  - Do not automatically stop spending unless you build automation.
- Quotas:
  - Limit resource/API usage by project.
  - Useful for blast-radius and accidental over-provisioning control.
- Labels and cost reports:
  - Improve chargeback/showback visibility.

### Cost optimization basics
- Right-size compute and storage.
- Shut down idle non-production resources.
- Use autoscaling where possible.
- Avoid unnecessary data egress across regions or to internet.
- Choose storage class aligned with access pattern.

## 5) Resource hierarchy and governance

### Hierarchy
- Organization -> Folder -> Project -> Resource

### Purpose of each layer
- Organization:
  - Top-level container for a company domain.
  - Central governance anchor.
- Folder:
  - Logical grouping for departments, environments, or business units.
  - Enables policy delegation.
- Project:
  - Workload boundary and operational unit.
- Resource:
  - Actual service objects (VM, bucket, SQL instance, etc.).

### Inheritance model
- IAM and some policies inherit from parent to child.
- Example:
  - Role granted at folder applies to all projects/resources under that folder (unless constrained).

### Governance controls
- IAM policies: who can do what.
- Organization policies: what is allowed/disallowed (for example allowed locations, external IP restrictions).
- Audit logs: who did what and when.

## 6) IAM basics in architecture context

### Key terms
- Principal: identity (user, group, service account).
- Permission: single allowed action.
- Role: collection of permissions.
- Policy: binding role to principal at a resource scope.

### Architecture best practices
- Follow least privilege.
- Avoid broad basic roles for production where possible.
- Use service accounts for workloads, not user accounts.
- Keep admin and runtime identities separated.

## 7) Resource scope awareness (important for exam)
- Global examples:
  - VPC network (global)
- Regional examples:
  - Subnet, Cloud SQL instance region placement
- Zonal examples:
  - Compute Engine VM instances

Exam questions often test whether you understand the resource scope and failure impact.

## 8) Availability and resiliency basics

### Terminology
- HA (high availability): keep service running during component failures.
- RTO: target recovery time.
- RPO: acceptable data loss window.

### Simple architecture progression
- Level 1: single VM in one zone (low resilience).
- Level 2: managed instance group across multiple zones (better HA).
- Level 3: cross-region DR with backups/replication (stronger resilience).

### Practical exam logic
- If requirement says "survive zone failure": multi-zone.
- If requirement says "survive region outage": multi-region DR.

## 9) Networking architecture starter points
- VPC is global; subnets are regional.
- Firewall rules are stateful and priority-based.
- Prefer private internal communication where possible.
- Use load balancers for scalability and health-based traffic routing.

## 10) Hands-on checklist for this chapter
- Create a new project and link billing.
- Enable Compute Engine API.
- Create one VM in `zone-a` and another in `zone-b` (same region).
- Verify IAM role assignment for a teammate or test user.
- Set a budget and alert threshold.
- Add labels for environment and owner.

## 11) Common mistakes
- Confusing region and zone.
- Putting production in a single zone with no failover.
- Mixing all environments in one project.
- Granting `Editor` to everyone.
- Assuming budget alerts stop resources automatically.
- Ignoring data residency requirements when selecting region.

## 12) Fast revision sheet
- Region = geography. Zone = failure domain.
- Project = IAM/API/quota/billing boundary.
- Billing account pays for one or more projects.
- Hierarchy = Org -> Folder -> Project -> Resource.
- Policies/roles can inherit downward.
- Multi-zone for HA, multi-region for DR-level resilience.

## 13) Practice questions (Basics and Architecture)

1. What is the main difference between a region and a zone?
   - Region is a geographic area; zone is an isolated deployment domain within it.

2. Where do IAM permissions commonly get applied for workload administration?
   - At project level (or inherited from folder/org as needed).

3. If you want stronger separation between dev and prod, what should you do?
   - Use separate projects.

4. Can one billing account pay for multiple projects?
   - Yes.

5. Do budget alerts automatically shut down resources?
   - No.

6. What is the correct hierarchy order?
   - Organization -> Folder -> Project -> Resource.

7. If a question asks to survive a single-zone failure with minimal complexity, what is a common answer?
   - Multi-zone deployment in one region.

8. What is a project ID?
   - Globally unique project identifier.

9. Which is better for centralized policy management across teams: many unrelated standalone projects or folders under one organization?
   - Folders under one organization.

10. What is the unit that generally owns API enablement and quotas?
    - Project.

11. Why are labels useful?
    - Cost tracking, ownership mapping, and operations filtering.

12. What is least privilege?
    - Grant only required access, not more.

13. Why should service accounts be used for applications?
    - They provide workload identity separate from human users.

14. If a company must keep data in a specific country, what affects architecture most?
    - Region selection and storage/database placement.

15. If users complain of high latency, what is one likely improvement?
    - Deploy closer to users in a nearer region.

## 14) One-page exam answer patterns
- "Minimum ops + scalable HTTP API" -> Cloud Run (compute topic, but often appears in architecture questions).
- "Need strict isolation between environments" -> separate projects and controlled IAM.
- "Need policy applied to many projects" -> folder/org-level governance.
- "Need HA against zone failure" -> multi-zone architecture.
- "Need stronger disaster recovery against region outage" -> cross-region design.

---

Use this note as your base chapter before moving to compute, storage/database, networking, IAM/security, and operations.

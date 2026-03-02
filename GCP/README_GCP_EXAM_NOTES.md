# GCP Exam Notes (Associate Cloud Engineer) - Full Study Pack

Last updated: February 24, 2026

## Exam Snapshot
- Exam: Google Cloud Associate Cloud Engineer (ACE)
- Questions: 50-60
- Time: 2 hours
- Format: Multiple choice + multiple select
- Strongly recommended: hands-on practice

## 1) GCP Basics and Architecture
D:\applications\xampp\htdocs\notes\GCP\GCP Basics and Architecture\GCP_Basics_and_Architecture.md
- Region: geographic area (for example `us-central1`).
- Zone: isolated failure domain inside a region (for example `us-central1-a`).
- High availability best practice: deploy across multiple zones.
- Resource hierarchy: Organization -> Folder -> Project -> Resource.
- Project is the boundary for billing, APIs, IAM, and quota.
- Billing account can be linked to multiple projects.
- IAM and Org Policies inherit down the hierarchy.
- Budgets/alerts notify on spend; they do not automatically stop usage.

### Quick exam concept
- Region vs Zone:
  - Region = location grouping of zones.
  - Zone = deployment unit/failure domain inside a region.

## 2) Compute Services
- Compute Engine: virtual machines with full OS control.
- App Engine: PaaS for quick app deployment with reduced infra management.
- Cloud Run: serverless containers for HTTP services and jobs; scales to zero.
- Cloud Run functions: event-driven functions model (Cloud Functions direction).
- Managed Instance Group (MIG): autoscaling + autohealing fleet of VMs.

### Service chooser
- Need OS-level control: Compute Engine
- Need managed app platform: App Engine
- Need scalable API with minimal server management: Cloud Run
- Need event-based execution: Cloud Run functions

## 3) Storage and Database
- Cloud Storage: object storage for files, backups, static content.
- Cloud SQL: managed MySQL/PostgreSQL/SQL Server.
- Firestore: serverless NoSQL document database.
- BigQuery: serverless analytics data warehouse.

### SQL vs NoSQL
- SQL (Cloud SQL): structured schema, joins, ACID transactions.
- NoSQL (Firestore): flexible schema, document model, high app-scale patterns.

### Cloud Storage classes
- Standard: frequently accessed data
- Nearline: infrequent, 30-day minimum storage duration
- Coldline: rare access, 90-day minimum storage duration
- Archive: long-term archive, 365-day minimum storage duration

## 4) Networking (Very Important)
- VPC is global.
- Subnets are regional.
- Firewall rules are stateful, direction-based (ingress or egress), and priority-based.
- Cloud NAT gives outbound internet to private VMs without external IP.
- Load balancing:
  - Application Load Balancer (L7): HTTP/HTTPS
  - Network Load Balancer (L4): TCP/UDP
- Shared VPC: centralized network control across multiple projects.
- VPC Peering: private connectivity between VPCs.
- Cloud VPN: encrypted tunnel between on-prem and GCP.

## 5) IAM and Security
- Permission: one specific action (example: `compute.instances.start`).
- Role: collection of permissions.
- Role types: Basic, Predefined, Custom.
- Service Account: identity for workloads, not humans.
- Best practice: least privilege.
- Prefer short-lived credentials/impersonation over long-lived keys.
- Use Secret Manager for secrets.
- Use Cloud Audit Logs for governance and compliance visibility.

### Common exam concept
- Role vs Permission:
  - Permission = one action.
  - Role = set of permissions.

## 6) DevOps and Monitoring
- Cloud Monitoring: metrics, dashboards, alerting, uptime checks.
- Cloud Logging: centralized logs, search, analysis, routing.
- Cloud Build: CI/CD build and deploy pipelines.
- Log sinks can route logs to BigQuery, Cloud Storage, and Pub/Sub.

## 30-Day Study Plan (Pass Strategy)

### Week 1 - Foundation
- Topics: GCP basics, regions/zones, IAM, billing
- Hands-on:
  - Create project
  - Create VM
  - Set IAM roles
  - Set budget alerts

### Week 2 - Core Services
- Topics: Compute Engine, Cloud Run, App Engine, Cloud Storage
- Hands-on:
  - Deploy simple app to Cloud Run
  - Upload and manage objects in Cloud Storage

### Week 3 - Advanced
- Topics: VPC, subnets, firewall, load balancing, Cloud SQL
- Hands-on:
  - Build custom VPC and firewall rules
  - Create Cloud SQL DB
  - Connect app/VM to DB

### Week 4 - Exam Mode
- Topics: scenario-based revision and mocks
- Practice:
  - Daily timed mock tests
  - Focus on service-selection questions

## Biggest Mistakes to Avoid
- Only watching videos without practice
- Skipping hands-on labs
- Ignoring networking topics

## Must-Do Hands-On
- Create VM (Compute Engine)
- Deploy app
- Create Cloud SQL database
- Connect app to DB
- Upload file to Cloud Storage

---

## 100 Practice Questions and Answers

1. Q: What is a region in GCP? A: A geographic area containing multiple zones.
2. Q: What is a zone in GCP? A: A deployment/failure domain inside a region.
3. Q: Best practice for high availability in one region? A: Deploy across multiple zones.
4. Q: What is the resource hierarchy order? A: Organization -> Folder -> Project -> Resource.
5. Q: What is the main administrative boundary for APIs and billing? A: Project.
6. Q: Can one billing account link to multiple projects? A: Yes.
7. Q: Do budgets stop spend automatically? A: No, they alert only unless you automate controls.
8. Q: What is a project ID? A: A globally unique identifier for a project.
9. Q: Why use folders? A: To group projects and apply policies by team/environment.
10. Q: What inherits down hierarchy? A: IAM policies and organization policies.
11. Q: What is Compute Engine? A: Google Cloud VM compute service.
12. Q: When choose Compute Engine over Cloud Run? A: When you need OS/runtime-level control.
13. Q: What is a managed instance group (MIG)? A: Managed VM fleet with autoscaling and autohealing.
14. Q: What is an unmanaged instance group? A: VM group managed manually.
15. Q: What is App Engine? A: PaaS for running apps with minimal infra ops.
16. Q: App Engine Standard vs Flexible main difference? A: Standard is more managed/sandboxed; Flexible offers broader runtime flexibility.
17. Q: What is Cloud Run? A: Fully managed serverless platform for containers/services/jobs.
18. Q: What is Cloud Run Jobs used for? A: Batch/finite-run container workloads.
19. Q: Which services scale to zero? A: Cloud Run and Cloud Run functions.
20. Q: Cloud Run functions are best for? A: Event-driven workloads.
21. Q: Cloud Functions current direction in GCP? A: Cloud Run functions model.
22. Q: What is a Spot VM? A: Lower-cost VM that can be interrupted.
23. Q: Why use custom machine types? A: Better cost-performance right-sizing.
24. Q: What is OS Login? A: IAM-integrated VM login control.
25. Q: Startup scripts are used for? A: Boot-time instance configuration automation.
26. Q: What is Cloud Storage? A: Managed object storage for unstructured data.
27. Q: Cloud Storage object mutability? A: Objects are immutable; replace to update.
28. Q: What is a bucket? A: Container for Cloud Storage objects.
29. Q: Standard storage class best for? A: Frequently accessed data.
30. Q: Nearline minimum duration? A: 30 days.
31. Q: Coldline minimum duration? A: 90 days.
32. Q: Archive minimum duration? A: 365 days.
33. Q: What are lifecycle rules? A: Automated object transition/deletion policies.
34. Q: What is Cloud SQL? A: Managed relational DB service.
35. Q: Cloud SQL best for? A: Transactional relational applications.
36. Q: What is Firestore? A: Serverless NoSQL document database.
37. Q: Firestore best use case? A: Flexible-schema apps, especially web/mobile.
38. Q: What is BigQuery? A: Serverless analytics data warehouse.
39. Q: Best for petabyte-scale SQL analytics? A: BigQuery.
40. Q: Is BigQuery for OLTP transactions? A: No, it is optimized for analytics.
41. Q: SQL vs NoSQL core decision? A: Structured transactional schema vs flexible document model.
42. Q: Best relational managed service in GCP for app DB? A: Cloud SQL.
43. Q: Is VPC global or regional? A: Global.
44. Q: Are subnets global or regional? A: Regional.
45. Q: Auto mode VPC means? A: Subnets are auto-created.
46. Q: Custom mode VPC means? A: You define subnets manually.
47. Q: Firewall rules are stateful or stateless? A: Stateful.
48. Q: Firewall rules direction types? A: Ingress and egress.
49. Q: One firewall rule can be both ingress and egress? A: No.
50. Q: Firewall actions? A: Allow or deny.
51. Q: What controls firewall processing order? A: Priority (lower number = higher priority).
52. Q: What is Cloud NAT? A: Outbound internet for private instances without external IP.
53. Q: What is Cloud Router used for? A: Dynamic routing (for VPN/Interconnect/NAT scenarios).
54. Q: What is Cloud VPN? A: Secure encrypted tunnel over internet.
55. Q: What is VPC Peering? A: Private connectivity between VPC networks.
56. Q: Shared VPC main benefit? A: Centralized network management.
57. Q: Application Load Balancer handles? A: HTTP/HTTPS (Layer 7).
58. Q: Network Load Balancer handles? A: TCP/UDP (Layer 4).
59. Q: Why use load balancer health checks? A: Send traffic only to healthy backends.
60. Q: Global Anycast frontend benefit? A: Single global IP with smart traffic routing.
61. Q: Internal load balancer used for? A: Private internal traffic.
62. Q: What is Cloud DNS? A: Managed authoritative DNS service.
63. Q: What is Private Google Access? A: Private VMs can access Google APIs without external IP.
64. Q: What is Private Service Connect? A: Private endpoint connectivity to producer/managed services.
65. Q: Route matching principle? A: Longest prefix match first.
66. Q: Difference between role and permission? A: Role is a set of permissions; permission is one action.
67. Q: What is a principal in IAM? A: Identity granted access.
68. Q: Basic IAM roles are? A: Owner, Editor, Viewer.
69. Q: Why avoid basic roles in production? A: Too broad and risky permissions.
70. Q: What are predefined roles? A: Google-managed granular roles.
71. Q: What are custom roles? A: User-defined permission bundles.
72. Q: What is a service account? A: Workload identity for non-human access.
73. Q: Are service account keys preferred? A: No, avoid long-lived keys when possible.
74. Q: What is service account impersonation? A: Temporary access to act as service account.
75. Q: Principle of least privilege means? A: Grant minimum required access.
76. Q: IAM inheritance behavior? A: Parent grants are inherited by children.
77. Q: Organization Policy is for? A: Governance constraints (for example allowed regions).
78. Q: Cloud Audit Logs purpose? A: Security/compliance visibility of actions/events.
79. Q: Secret Manager purpose? A: Secure secret storage and access.
80. Q: CMEK means? A: Customer-managed encryption keys.
81. Q: Service agent means? A: Google-managed service account for managed services.
82. Q: Role commonly needed to attach service account to VM? A: roles/iam.serviceAccountUser.
83. Q: IAM Conditions allow? A: Context-based conditional access.
84. Q: Deny policies do what? A: Explicitly block permissions.
85. Q: Cloud Monitoring provides? A: Metrics, dashboards, uptime, alerts.
86. Q: Log-based metric means? A: Metric generated from matching log patterns.
87. Q: Cloud Logging sinks can route to? A: BigQuery, Cloud Storage, Pub/Sub.
88. Q: What is Cloud Build? A: Serverless CI/CD build service.
89. Q: Cloud Build job steps run as? A: Containerized build steps.
90. Q: Common Cloud Build config file? A: cloudbuild.yaml.
91. Q: What is a Cloud Build trigger? A: Event-based automatic build start.
92. Q: Artifact Registry role? A: Store container/images/packages.
93. Q: What is an uptime check? A: Endpoint availability test from configured locations.
94. Q: Alerting policy needs at least? A: Condition and notification channel.
95. Q: Ops Agent is used for? A: Collect VM logs and metrics.
96. Q: First production incident triage step? A: Check alerts, logs, metrics, and recent deployments.
97. Q: Scenario: scalable API with minimum server management. Service? A: Cloud Run.
98. Q: Scenario: trigger processing when file uploaded to bucket. Service? A: Cloud Run functions.
99. Q: Scenario: transactional relational app database. Service? A: Cloud SQL.
100. Q: Scenario: massive SQL analytics workload. Service? A: BigQuery.

---

## Final Revision Tips
- Practice service-selection scenarios every day.
- Do at least 2 timed mock tests before exam day.
- Repeat networking + IAM questions more than other topics.
- Spend at least 60% time on hands-on labs, 40% on theory.

## Suggested Free Resources
- Google Cloud Skills Boost (official labs)
- Google Cloud documentation
- Scenario-based practice tests

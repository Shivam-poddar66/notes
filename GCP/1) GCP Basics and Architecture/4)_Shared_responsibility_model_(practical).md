# Shared Responsibility Model (Practical Detailed Notes)

Last updated: February 24, 2026
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this note covers
- Exact meaning of shared responsibility in Google Cloud.
- Responsibility split across IaaS, PaaS, serverless, and managed data services.
- Practical controls you must configure as a customer.
- Real-world operational checklist for exam and production.
- Common exam traps and scenario-based Q&A.

## 1) Shared responsibility in one line

Google secures the cloud infrastructure, while you secure how your workloads, identities, networks, and data are configured and used in that cloud.

## 2) Why this model matters

- Security incidents often happen due to misconfiguration, not cloud provider datacenter failure.
- Using managed services reduces operational burden but does not remove access and data security duties.
- Compliance accountability for your data still remains with your organization.

## 3) Responsibility split at a high level

### Google responsibility ("security of the cloud")
- Physical datacenter security.
- Hardware lifecycle and host infrastructure.
- Global network backbone operations.
- Managed service platform operations at provider layer.
- Base service reliability engineering and infrastructure patching at provider-managed layers.

### Customer responsibility ("security in the cloud")
- Identity and access control (IAM roles, least privilege, account lifecycle).
- Network security configuration (VPC design, firewall rules, private access patterns).
- Data protection (classification, retention, backup strategy, encryption choices).
- Application and workload security (secure code, dependency patching where applicable).
- Logging, monitoring, and incident response readiness.
- Compliance mapping and audit readiness for business/regulatory needs.

## 4) Responsibility by service model

The more managed the service, the less infrastructure you manage.  
Identity, data, and secure configuration always remain your responsibility.

### A) Compute Engine (IaaS VM)

Google manages:
- Physical infrastructure and hypervisor platform.
- Underlying facilities and core platform operations.

You manage:
- Guest OS hardening and patching.
- Installed runtime and application packages.
- VM service accounts and IAM permissions.
- Network exposure (firewall, public/private IP decisions).
- Disk encryption policy choices, backup, and recovery posture.
- Workload-level monitoring, logging, and alerting.

### B) Google Kubernetes Engine (managed control plane model)

Google manages:
- Managed Kubernetes control plane components (in managed modes).
- Underlying platform operations for managed layers.

You manage:
- Cluster/node security posture (especially node pools where applicable).
- Kubernetes RBAC, namespace isolation, network policies.
- Container image security and patch lifecycle.
- Secrets handling and workload identity configuration.

### C) App Engine (PaaS)

Google manages:
- Most runtime platform and scaling infrastructure.

You manage:
- App code security.
- IAM/service account design.
- Data access policies and secrets management.
- App-level validation, auth, and logging practices.

### D) Cloud Run and Cloud Run functions (serverless)

Google manages:
- Server/runtime infrastructure, autoscaling mechanics, platform availability.

You manage:
- Container/function code and dependencies.
- Request authN/authZ decisions.
- Service account permissions.
- Secrets, environment configuration, and data access boundaries.

### E) Managed data services (Cloud SQL, Firestore, BigQuery)

Google manages:
- Service platform operations and underlying infrastructure.

You manage:
- Data schema and access control model.
- Data classification and retention.
- Query permissions and role design.
- Backup/restore policy, data export controls, and governance.

## 5) Security control areas you must own

### Identity and access management
- Use least privilege.
- Prefer predefined/custom roles over overly broad basic roles in production.
- Use service accounts for workloads, not user credentials.
- Avoid long-lived service account keys when possible.
- Rotate and review access regularly.

### Network security
- Use custom VPC/subnet architecture for control.
- Restrict inbound exposure with explicit firewall rules.
- Prefer private connectivity patterns where possible.
- Minimize publicly exposed resources.

### Data security
- Classify data by sensitivity.
- Enforce encryption and key management requirements.
- Use Secret Manager for credentials and tokens.
- Apply lifecycle/retention rules and secure deletion policies.

### Logging and monitoring
- Enable and review audit logs.
- Monitor security events and unusual access patterns.
- Create alerts for privilege changes, denied access spikes, or critical config drift.

### Patch and vulnerability management
- VM workloads: you patch guest OS and app stack.
- Containers/functions: you patch dependencies and vulnerable code.
- Managed services reduce platform patching burden but not app vulnerability responsibility.

## 6) Practical control mapping (exam-focused)

1. "Who can access?" -> IAM roles, groups, service accounts, conditions.
2. "What can they access?" -> least privilege at correct resource scope.
3. "From where?" -> network segmentation, firewall, private endpoints.
4. "How is data protected?" -> encryption, secrets, backup, retention.
5. "How do we detect abuse?" -> audit logs, monitoring, alerts.
6. "How do we recover?" -> tested backup/restore and incident runbooks.

## 7) Common misconceptions and corrections

- Misconception: "Managed service means Google handles all security."
  - Correction: Google handles platform security; you still control identity, data access, and secure use.

- Misconception: "If IAM role works, it is secure enough."
  - Correction: Working access is not least privilege. Rights must be minimal and reviewed.

- Misconception: "Budget alerts are security controls."
  - Correction: Budgets are financial controls, not access controls.

- Misconception: "Default settings are always production safe."
  - Correction: Default may be functional, not compliant with your risk requirements.

## 8) Day-to-day operational checklist

Daily:
- Review critical security alerts and failed authentication patterns.
- Check unusual privilege escalations or high-risk changes.

Weekly:
- Audit IAM changes and unused high-privilege roles.
- Review public endpoints and firewall rules for drift.

Monthly:
- Access recertification for privileged identities.
- Secret rotation where applicable.
- Backup restore test sampling.
- Compliance evidence collection check.

Quarterly:
- Threat modeling refresh for major systems.
- DR exercise against defined RTO/RPO.
- Policy and control baseline review.

## 9) Incident response under shared responsibility

When an incident occurs, customer teams are typically responsible for:
- Identifying compromised identity or workload.
- Revoking/rotating credentials and tightening IAM.
- Isolating affected workloads/network paths.
- Forensic review using logs and telemetry.
- Recovery and hardening actions.

Google responsibility remains platform-level operations and service reliability at provider scope.

## 10) Compliance and audit perspective

- Cloud provider certifications help, but your implementation must still pass your own compliance requirements.
- You must produce evidence for:
  - Access control policies and review records.
  - Audit logs and retention.
  - Encryption and key management approach.
  - Data location and lifecycle controls.
  - Incident response and recovery testing.

## 11) Architecture decision examples

Example 1: Startup API on Cloud Run  
- Google handles runtime infrastructure.  
- You must enforce IAM/service auth, protect secrets, secure DB access, and monitor suspicious behavior.

Example 2: Legacy VM workload on Compute Engine  
- You must patch OS, harden SSH/access paths, control firewall exposure, and manage backup/restore.

Example 3: Managed database workload  
- Google runs DB platform infrastructure.  
- You still own schema permissions, sensitive data controls, backup policy, and access audit.

## 12) Exam traps and answer logic

1. Trap: "Fully managed" interpreted as "no customer security actions."
   - Correct logic: customer still owns IAM, data, and app-layer security.

2. Trap: broad Editor/Owner access for convenience.
   - Correct logic: least privilege with scoped roles.

3. Trap: security thought limited to firewall only.
   - Correct logic: identity + network + data + monitoring controls together.

4. Trap: assuming provider handles compliance automatically.
   - Correct logic: provider supports, customer remains accountable for implementation.

## 13) Quick revision sheet

- Google secures the cloud; customer secures usage in the cloud.
- More managed services reduce infra patch work, not identity/data ownership.
- IAM least privilege is always customer duty.
- Data classification, encryption strategy, secrets, and retention are customer duties.
- Monitoring, audit, and incident response readiness are customer duties.
- "Fully managed" never means "fully outsourced security accountability."

## 14) Practice questions with answers

1. What does shared responsibility mean in GCP?
   - Google secures platform infrastructure; customer secures workload configuration, identity, and data usage.

2. In Compute Engine, who patches guest OS?
   - Customer.

3. In Cloud Run, who secures container code and dependencies?
   - Customer.

4. Who is responsible for IAM role design in your project?
   - Customer.

5. Who controls data classification and retention policy?
   - Customer.

6. Does managed DB remove customer responsibility for access control?
   - No.

7. Which is a provider responsibility: firewall rule design or physical datacenter security?
   - Physical datacenter security.

8. Which is a customer responsibility: service reliability SRE at provider layer or secret management for application credentials?
   - Secret management for application credentials.

9. Is least privilege optional in managed services?
   - No.

10. If an IAM misconfiguration causes data exposure, who is accountable for the misconfiguration?
    - Customer.

11. Does "fully managed service" mean no need for logging/monitoring setup?
    - No.

12. In GKE, who typically manages Kubernetes RBAC and workload policies?
    - Customer.

13. Which control directly answers "who can do what"?
    - IAM roles and permissions.

14. Which control helps detect suspicious admin actions?
    - Audit logs and alerting.

15. Is compliance automatically inherited just because provider has certifications?
    - No, customer implementation still must meet requirements.

16. What is the biggest exam misunderstanding in this topic?
    - Believing managed services eliminate customer security responsibilities.

17. Name three customer duties that always remain.
    - IAM control, data protection, and application security configuration.

18. If workload uses service account keys, what is best practice direction?
    - Prefer short-lived/keyless patterns and minimize long-lived keys.

19. Why review IAM periodically?
    - To remove excessive or stale permissions and reduce risk.

20. What is the correct high-level rule for ACE exam questions on this topic?
    - Provider handles infrastructure security; you handle secure configuration and data/identity governance.

---

Use this note with deployment, regions/zones, and architecture notes for a complete GCP foundations preparation set.

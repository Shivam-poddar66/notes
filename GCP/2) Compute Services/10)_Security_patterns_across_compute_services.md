# Security Patterns Across Compute Services (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Shared responsibility and security baseline for GCP compute.
- Service-specific patterns for Compute Engine, App Engine, Cloud Run, and Cloud Run functions.
- IAM, networking, secrets, logging, and incident response patterns.
- Exam-focused decision rules and anti-patterns.

## 1) Shared responsibility model by service

Security responsibility shifts with service model:
- Compute Engine: you manage guest OS hardening, patching, host-level runtime controls, and app security.
- App Engine: Google manages more platform layers; you still own app code, IAM, data access, and secrets.
- Cloud Run and Cloud Run functions: Google manages infrastructure/runtime layers; you own identity, data access, input validation, and secure configuration.

Exam rule:
- "Managed service" does not mean "no security work." It means a smaller infrastructure surface area to secure.

## 2) Identity and access design patterns

### Core IAM principles
- Use a dedicated service account per workload (or per bounded environment).
- Grant least privilege roles at the narrowest scope (resource or project when possible).
- Prefer predefined roles over broad project-wide primitive roles.
- Review and rotate human access patterns regularly.

### Workload identity patterns
- Attach workload-specific service accounts to VM instances, App Engine services, Cloud Run services/jobs, and Cloud Run functions.
- Avoid sharing one high-privilege service account across unrelated services.
- Avoid long-lived service account keys; prefer keyless auth patterns from Google-managed runtimes.

### Guardrails
- Use organization policies to block risky defaults (for example, unmanaged service account key creation where possible).
- Keep break-glass admin accounts limited and auditable.

## 3) Network security patterns

### Exposure minimization
- Default to private connectivity for east-west traffic.
- Expose only required ingress points.
- For internet-facing apps, place services behind load balancing and add Cloud Armor where threat profile justifies it.

### Compute Engine patterns
- Restrict firewall rules to explicit source ranges and target tags/service accounts.
- Remove unnecessary external IPs.
- Use Cloud NAT for controlled egress when internet access is needed without inbound exposure.

### Serverless patterns (Cloud Run and functions)
- Use ingress settings that match requirement:
  - internal only for private services,
  - internal and load balancing for controlled external exposure,
  - all only when public access is required.
- Route private egress through Serverless VPC Access connectors when internal systems must be reached.

### App Engine patterns
- Limit service exposure and enforce auth at app and IAM boundaries.
- Keep dependency paths private where possible.

## 4) Secret and key management patterns

- Store application secrets in Secret Manager, not in source code or instance metadata.
- Reference secrets at runtime with least-privilege access.
- Separate secret access by environment (dev/stage/prod).
- Rotate secrets and verify rollback procedures for credential changes.
- Use CMEK only when regulatory or control requirements justify operational complexity.

## 5) Host and runtime hardening patterns

### Compute Engine
- Use hardened base images and keep patch cadence documented.
- Enable Shielded VM features when compatible.
- Restrict SSH access pathways and prefer centralized access controls.
- Detect config drift (startup scripts, package changes, firewall drifts).

### Cloud Run and functions
- Keep container/function dependencies minimal.
- Pin dependency versions and rebuild images regularly.
- Use Artifact Registry and vulnerability scanning in CI/CD.
- Enforce deploy policies (for example, trusted image sources).

### App Engine
- Keep runtime versions current and remove unused service versions.
- Restrict service-to-service communication and IAM bindings.

## 6) Data access and application-layer protection

- Enforce service-to-database least privilege (separate read/write identities where possible).
- Validate all inputs at API boundaries.
- Use mTLS or trusted private channel patterns for internal traffic where required.
- Add rate limiting and abuse controls on public endpoints.
- Design for idempotency to reduce replay risk in event-driven handlers.

## 7) Logging, monitoring, and detection patterns

### Required telemetry
- Admin Activity and Data Access logs where needed.
- Application logs with request context and principal identity where possible.
- Security-relevant metric alerts:
  - IAM policy changes,
  - failed auth spikes,
  - unusual egress patterns,
  - error-rate jumps after deployments.

### Detection workflow
- Centralize logs in Cloud Logging.
- Create log-based metrics for high-risk events.
- Route critical alerts to on-call channel with severity mapping.
- Integrate Security Command Center findings into triage workflow.

## 8) Incident response playbook for compute workloads

1. Detect and classify incident scope.
2. Contain affected identity, endpoint, or workload (disable account/revision/VM path).
3. Preserve evidence (logs, deployment metadata, IAM diffs).
4. Eradicate root cause (credential rotation, patch, policy correction).
5. Recover with controlled rollout and heightened monitoring.
6. Run post-incident review and implement preventive controls.

## 9) Service-by-service security checklist

### Compute Engine
- No unnecessary external IP.
- Strict firewall tags/service-account targeting.
- OS patching and hardening policy exists.
- Least-privilege workload service account.

### App Engine
- Service identity scoped to required APIs only.
- Version rollout controls and rollback plan.
- Secrets fetched from Secret Manager.
- Logging and alerting enabled.

### Cloud Run
- Correct ingress setting for exposure intent.
- Invoker IAM and auth mode explicitly configured.
- Runtime service account least-privilege.
- Revision rollout and audit logs enabled.

### Cloud Run functions
- Trigger permissions tightly scoped.
- Handler idempotent and input-validated.
- Secrets not embedded in env or source.
- Retry behavior understood and monitored.

## 10) High-frequency exam traps

- Assuming "serverless" removes IAM design responsibility.
- Using broad `Editor` role for workload service accounts.
- Choosing public endpoint exposure when private ingress satisfies requirement.
- Forgetting VM patching responsibility in Compute Engine scenarios.
- Treating secret storage as app config variable instead of managed secret.

## 11) Exam answer pattern

When a question asks for "most secure" design:
1. Minimize exposed surface area.
2. Use least privilege identities.
3. Keep secrets in Secret Manager.
4. Enable auditable logs and alerts.
5. Choose the simplest service model that satisfies control/compliance constraints.

## 12) Quick revision sheet

- Least privilege first, service choice second.
- Private by default, public by explicit requirement.
- Secret Manager over plaintext secrets.
- VM security needs active patch/hardening operations.
- Managed services reduce infra attack surface, not app/data risk.

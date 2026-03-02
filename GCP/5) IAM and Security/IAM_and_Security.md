# IAM And Security (Complete Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Identity and access design in Google Cloud.
- Policy hierarchy, role strategy, and least-privilege implementation.
- Service accounts, impersonation, and key management.
- Security controls across secrets, logging, governance, and incident response.
- Full study roadmap for exam and practical implementation.

## 1) Core IAM and security mental model

Think in four layers:
1. Identity:
   - who is requesting access (user, group, service account, workload identity).
2. Authorization:
   - what is allowed (roles, permissions, conditions).
3. Resource scope:
   - where access applies (org, folder, project, resource).
4. Security operations:
   - how access is audited, monitored, and corrected.

Primary rule:
- Authentication answers "who are you."
- Authorization answers "what can you do."

Exam rule:
- Least complexity plus least privilege usually wins unless stricter constraints are stated.

## 2) Principals and identity types

### Common principal types
- Google identities (users).
- Google Groups (recommended for team-based grants).
- Service accounts (workload identities, not human logins).
- External identities (federated enterprise identities where configured).

### Why groups matter
- Assigning roles to groups simplifies lifecycle management.
- Reduces direct user-level policy sprawl.

### Identity hygiene
- Avoid assigning broad roles directly to many individuals.
- Separate admin and non-admin identities.
- Use break-glass accounts sparingly and monitor heavily.

Exam cues:
- "Many users need same access" -> use groups.
- "Workload identity" -> service account pattern.

## 3) Resource hierarchy and policy inheritance

### Hierarchy levels
- Organization.
- Folders.
- Projects.
- Individual resources.

### Inheritance behavior
- Policies granted at higher levels can flow down.
- Lower levels can add bindings; total effective permissions are cumulative unless constrained by deny/policy controls.

### Design patterns
- Put broad guardrails at org/folder level.
- Put workload-specific grants at project/resource level.
- Keep production and non-production in separate governance boundaries.

### Common mistake
- Over-granting at project level when narrower scope would work.

Exam cues:
- "Central governance across many projects" -> org/folder policy strategy.

## 4) Roles and permissions (deep dive)

### Permission
- One granular action (example style: `service.resource.verb`).

### Role
- Collection of permissions.

### Role types
- Basic roles:
  - broad and often too permissive for production.
- Predefined roles:
  - service-specific, safer default for most cases.
- Custom roles:
  - used when predefined roles are still broader than requirement.

### Practical selection order
1. Try predefined role with least privilege.
2. If still too broad, use custom role.
3. Avoid basic roles in production unless explicitly justified.

Exam cues:
- "Need minimal required permissions only" -> predefined/custom over basic roles.

## 5) Service accounts and workload identity

### Core principle
- Service account is identity for workloads, not for humans.

### Best practices
- One service account per workload boundary.
- Grant least privilege at narrow scope.
- Avoid sharing one high-privilege account across many apps.

### Service account lifecycle
1. Create dedicated SA.
2. Grant minimal roles.
3. Attach SA to compute workload.
4. Monitor usage and rotate/remove unused bindings.

### Anti-pattern
- Downloading and distributing static SA keys widely.

Exam cues:
- "Application or VM needs API access" -> attach service account with minimal role.

## 6) Authentication patterns and short-lived access

### Preferred patterns
- Application Default Credentials in managed runtimes.
- Service account impersonation for admin operations.
- Workforce/workload federation where enterprise integration requires it.

### Why short-lived access is preferred
- Reduces credential theft impact.
- Simplifies rotation and governance.

### Human access patterns
- Strong MFA and identity policy controls.
- Separate high-risk privileged activities from daily accounts.

Exam cues:
- "Avoid long-lived keys" -> impersonation/short-lived credential pattern.

## 7) Least-privilege architecture patterns

### Role grant strategy
- Grant only required roles.
- Grant at lowest feasible scope.
- Use conditions for time/resource restrictions when needed.

### Separation patterns
- Separate roles for:
  - deployment,
  - runtime operation,
  - security auditing.

### Periodic review workflow
1. Export and review IAM bindings.
2. Remove stale grants.
3. Tighten broad roles.
4. Validate no production outage from cleanup.

Exam cues:
- "Security-first with minimum access" -> least-privilege and narrow scope grant.

## 8) IAM Conditions and context-based access

### What conditions provide
- Conditional grants based on context (for example resource attributes, time windows, request context patterns where supported).

### Use cases
- Time-bound elevated access.
- Restricting role usage to specific resource names.
- Controlled temporary access for migration windows.

### Design advice
- Keep condition logic readable and documented.
- Test policy outcomes before critical rollout.

Exam cues:
- "Temporary or context-specific access" -> IAM Conditions pattern.

## 9) Organization Policy and governance controls

### Purpose
- Enforce security and compliance guardrails centrally.

### Common controls (conceptual)
- Restrict risky defaults.
- Control allowed regions/services.
- Restrict external sharing and key creation patterns where required.

### Governance pattern
- Baseline controls at org/folder.
- Exception workflow documented and audited.

Exam cues:
- "Enforce policy across many projects" -> Organization Policy.

## 10) Service account key management and impersonation

### Key risks
- Long-lived keys increase breach blast radius.
- Keys copied into CI/CD or developer laptops create unmanaged risk.

### Better pattern
- Prefer keyless auth and impersonation.
- If keys must exist:
  - strict rotation policy,
  - storage in secure systems only,
  - monitor and phase out quickly.

### Operational checklist
- Inventory all keys.
- Disable unused keys.
- Alert on new key creation in sensitive projects.

Exam cues:
- "Most secure SA auth approach" -> avoid long-lived keys, use impersonation.

## 11) Secret management and encryption controls

### Secret Manager patterns
- Store app credentials, API tokens, and sensitive config.
- Grant secret access only to required workload identities.
- Rotate secrets with tested rollout/rollback procedures.

### Encryption controls
- Default encryption at rest is provided.
- Use customer-managed keys (CMEK) when compliance/control requirements demand it.

### Common mistakes
- Putting secrets in source code or plain environment files.
- Giving broad secret accessor roles to unrelated workloads.

Exam cues:
- "Secure secret storage" -> Secret Manager.
- "Customer-controlled key requirement" -> CMEK direction.

## 12) Cloud Audit Logs and security observability

### Logging categories (high-level)
- Admin activity logging.
- Data access visibility (as applicable by service and configuration).
- System and policy-related events.

### Why logs matter
- Forensics.
- Compliance evidence.
- Detection of privilege abuse or misconfiguration.

### Alerting patterns
- Alert on:
  - privileged role grants,
  - service account key creation,
  - policy changes in sensitive projects,
  - repeated authorization failures.

Exam cues:
- "Need governance and compliance visibility" -> Cloud Audit Logs.

## 13) IAM troubleshooting and policy analysis

### Common troubleshooting flow
1. Confirm principal identity.
2. Confirm resource scope.
3. Check effective IAM bindings/inheritance.
4. Check deny/condition/policy constraints.
5. Validate API enablement and service-specific preconditions.

### Frequent issues
- Role granted at wrong project.
- Missing required permission inside otherwise correct role set.
- Condition blocks request unexpectedly.
- Cross-project access assumption without explicit binding.

### Good operational practice
- Keep access request templates tied to business function.
- Record least-privilege justification.

Exam cues:
- "User can list but cannot modify" often indicates missing specific permission or overly narrow role.

## 14) Security Command Center and posture management

### Role in security operations
- Central visibility of security findings and posture signals.
- Helps identify misconfigurations and exposure risks.

### Workflow
1. Triage critical findings.
2. Assign owner and remediation SLA.
3. Confirm fix and close with evidence.
4. Track recurring root causes.

### Governance integration
- Combine SCC findings with IAM/log alerts for broader detection coverage.

Exam cues:
- "Centralized security posture and findings" -> Security Command Center.

## 15) Data and perimeter protection patterns

### Data protection layers
- IAM least privilege.
- Encryption controls.
- Private network access patterns for sensitive services.

### Perimeter strategy
- Restrict data exfiltration paths where compliance demands strict boundaries.
- Combine identity controls with network controls and audit logs.

### Architecture rule
- Sensitive data systems should not rely on one control type only.

Exam cues:
- "Prevent unauthorized data movement" -> perimeter-aware and least-privilege design.

## 16) Incident response and access recovery

### Incident response phases
1. Detect suspicious access pattern.
2. Contain:
   - disable risky keys/tokens,
   - suspend compromised accounts if needed.
3. Eradicate root cause:
   - remove bad bindings,
   - rotate secrets,
   - patch policy gaps.
4. Recover safely:
   - re-enable with controlled privileges.
5. Post-incident review and control hardening.

### Break-glass access
- Maintain minimal emergency path with strong audit and approval workflow.
- Regularly test break-glass procedure.

Exam cues:
- "Immediate containment for compromised credentials" -> disable/rotate, then re-grant safely.

## 17) Security architecture patterns across services

### Pattern 1: Project baseline
- Organization policy guardrails.
- Logging and alerts enabled.
- Restricted IAM administrators.

### Pattern 2: Workload identity model
- Dedicated SA per service.
- Least privilege resource access.
- Secret Manager for all secrets.

### Pattern 3: Deployment pipeline security
- Separate CI/CD identity with limited deploy permissions.
- No broad owner/editor roles for pipelines.
- Audit deployment actions.

### Pattern 4: Cross-project access
- Explicit grants only.
- Avoid hidden implicit assumptions.
- Monitor inter-project privileged access closely.

Exam cues:
- "Secure by default at scale" -> baseline guardrails + workload-specific least privilege.

## 18) Scenario answer patterns (high exam relevance)

1. Requirement: give team read access to many projects.
   - Typical answer: grant viewer-like role to Google Group at folder/org scope as appropriate.

2. Requirement: workload needs storage read only.
   - Typical answer: dedicated SA with minimal storage read role.

3. Requirement: temporary admin access for migration window.
   - Typical answer: time-bounded conditional access.

4. Requirement: avoid long-lived service account keys.
   - Typical answer: impersonation or keyless managed identity pattern.

5. Requirement: store API credentials securely.
   - Typical answer: Secret Manager with least-privilege access.

6. Requirement: audit all privileged changes.
   - Typical answer: Cloud Audit Logs + alerting.

7. Requirement: centralized guardrails across projects.
   - Typical answer: Organization Policy and hierarchical governance pattern.

8. Requirement: detect security misconfiguration findings centrally.
   - Typical answer: Security Command Center.

9. Requirement: strict least privilege beyond predefined roles.
   - Typical answer: custom role (carefully scoped).

10. Requirement: emergency access during outage.
    - Typical answer: break-glass process with strong audit and post-incident cleanup.

Exam elimination method:
1. Remove options with broad permanent access where temporary/narrow access is required.
2. Remove options using static keys when short-lived alternatives satisfy requirement.
3. Remove options that skip audit/compliance requirement.
4. Choose simplest secure option meeting all constraints.

## 19) Common mistakes and exam traps

### Identity and role traps
- Granting basic `Editor` or `Owner` where predefined minimal role works.
- Assigning roles directly to many users instead of groups.
- Using one service account across unrelated workloads.

### Credential traps
- Long-lived keys in CI/CD.
- Secrets in code repos.
- No key rotation process.

### Governance traps
- No org-level baseline constraints.
- Missing audit logging visibility for sensitive changes.
- Cleanup not done after temporary elevated access.

### Operational traps
- Breaking production by removing roles without validation.
- No separation of duties between deployer and security admin.

Exam traps:
- Confusing authentication mechanisms with authorization grants.
- Choosing complex custom policy when predefined role + narrow scope already satisfies requirement.

## 20) Full roadmap (study and implementation)

### Objective
Build strong IAM and security decision-making with practical implementation discipline.

### Phase 1: Fundamentals (Day 1 to Day 3)
1. Learn principals, roles, permissions, hierarchy.
2. Understand authentication vs authorization.
3. Build IAM concept map.

Deliverable:
- One-page IAM glossary and role-selection flow.

### Phase 2: Access design deep dive (Day 4 to Day 8)
1. Day 4: role types and least-privilege mapping.
2. Day 5: service account patterns and keyless auth.
3. Day 6: IAM Conditions and temporary access design.
4. Day 7: org policy and governance controls.
5. Day 8: audit logs and alerting strategy.

Deliverable:
- Access architecture template for new projects.

### Phase 3: Security operations (Day 9 to Day 12)
1. Build secret management workflow.
2. Build incident response checklist for credential compromise.
3. Integrate posture findings and audit triage workflow.

Deliverable:
- Security operations runbook and escalation matrix.

### Phase 4: Scenario mastery (Day 13 to Day 15)
1. Solve timed IAM/security scenario sets.
2. Categorize wrong answers by root cause.
3. Practice elimination method.

Deliverable:
- Personal trap list with corrected answer patterns.

### Phase 5: Hands-on lab sprint (Day 16 to Day 19)
1. Lab A: group-based access architecture.
2. Lab B: service account per workload + least privilege.
3. Lab C: secret manager integration with workloads.
4. Lab D: audit log alert for privileged role grant.
5. Lab E: temporary conditional access workflow.
6. Lab F: credential compromise simulation and recovery.

Deliverable:
- Lab records, commands, screenshots, and lessons learned.

### Phase 6: Final revision (Day 20 to Day 21)
1. Review quick sheet and top traps.
2. Retake mixed IAM/security mock set.
3. Final weak-area closure.

Deliverable:
- Final exam-day quick notes and checklist.

## 21) Quick revision sheet

### Must-remember facts
- Permission = one action.
- Role = set of permissions.
- Service account = workload identity.
- Least privilege = default rule.
- Prefer short-lived auth/impersonation over long-lived keys.
- Secret Manager stores secrets securely.
- Audit logs provide governance visibility.

### Role strategy quick map
- Basic roles: broad, often too risky for production.
- Predefined roles: standard default choice.
- Custom roles: when predefined roles are still too broad.

### Fast keyword map
- "Many users same access" -> groups.
- "Workload API identity" -> service account.
- "Temporary elevated access" -> conditional/time-bounded grant.
- "Avoid static credentials" -> impersonation/short-lived access.
- "Compliance visibility" -> audit logs.
- "Central guardrails" -> organization policy.

### Final chapter rule
Authorize the minimum required access at the narrowest scope, monitor continuously, and remove unused privilege fast.

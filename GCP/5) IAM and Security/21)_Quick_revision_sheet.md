# 21) Quick revision sheet

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- 60-second exam flow:
1. Identify principal type.
2. Choose narrowest role and scope.
3. Prefer temporary/short-lived access for elevated tasks.
4. Ensure logging/audit requirement is covered.
- Why: Fast structured steps reduce IAM trap answers.


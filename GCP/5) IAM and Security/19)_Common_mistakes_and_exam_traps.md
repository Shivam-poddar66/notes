# 19) Common mistakes and exam traps

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Trap: Team grants `Owner` role for quick troubleshooting.
- Correct approach: narrow predefined role plus temporary condition if needed.
- Why: Broad permanent roles violate least-privilege and increase risk.


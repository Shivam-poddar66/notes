# 1) Core IAM and security mental model

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Requirement: Analyst should view billing reports, but only security team can change IAM.
- Design: Separate viewer and admin identities with narrow role grants.
- Why: Identity and authorization are split clearly, reducing accidental privilege.


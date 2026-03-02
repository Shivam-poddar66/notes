# 2) Principals and identity types

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Requirement: 40 developers need the same BigQuery read access.
- Design: Add users to one Google Group and grant role to the group.
- Why: Group-based grants simplify onboarding/offboarding and reduce policy sprawl.


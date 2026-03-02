# 3) Resource hierarchy and policy inheritance

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Requirement: Block risky behavior across all projects, but allow project-specific app roles.
- Design: Apply baseline guardrails at org/folder, app roles at project level.
- Why: Central governance with local flexibility follows hierarchy best practice.


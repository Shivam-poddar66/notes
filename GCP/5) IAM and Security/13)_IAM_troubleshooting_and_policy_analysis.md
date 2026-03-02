# 13) IAM troubleshooting and policy analysis

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Requirement: User can view VM list but cannot stop VM instances.
- Design: Check effective role bindings and missing `compute.instances.stop` permission.
- Why: Action-level permission gap explains partial access behavior.


# 4) Roles and permissions (deep dive)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Requirement: App needs only object read from one storage bucket.
- Design: Start with predefined storage read role at bucket scope, avoid basic roles.
- Why: Predefined narrow role gives least privilege with low maintenance.


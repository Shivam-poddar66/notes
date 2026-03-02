# 15) Data and perimeter protection patterns

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Requirement: Sensitive data must not be exfiltrated to untrusted locations.
- Design: Combine least-privilege IAM, private service access, and perimeter controls.
- Why: Layered controls reduce data movement risk better than single control.


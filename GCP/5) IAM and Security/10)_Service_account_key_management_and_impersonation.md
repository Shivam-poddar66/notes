# 10) Service account key management and impersonation

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

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


## Practical example
- Requirement: Legacy script uses long-lived service account key in CI.
- Design: Replace key with service account impersonation, then disable old key.
- Why: Eliminates static credential risk and improves auditability.


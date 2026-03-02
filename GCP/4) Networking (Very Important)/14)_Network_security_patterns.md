# 14) Network Security Patterns (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Security design principles
- Private by default.
- Least-privilege connectivity.
- Segmentation by trust boundary.
- Defense in depth (identity + network + monitoring).

## 2. Perimeter and ingress patterns
- Minimize public endpoints.
- Use controlled ingress points for internet-facing services.
- Restrict admin access to narrow source ranges and paths.

## 3. East-west segmentation
- Separate app, data, and management paths.
- Use service account-based targeting where possible.
- Enforce explicit allow rules rather than broad trust.

## 4. Encryption and transport security
- Use encrypted transport for sensitive communications.
- Manage certificates and endpoint identity lifecycle.

## 5. Governance controls
- Apply org/folder policy baselines.
- Standardize firewall rule naming and ownership.
- Run periodic policy review for stale broad rules.

## 6. Detection and response
- Log critical allow/deny decisions.
- Alert on suspicious traffic changes.
- Correlate network security events with IAM changes.

## 7. Anti-patterns
- Public management ports.
- Shared flat networks for all environments.
- No logging on sensitive firewall rules.

## 8. Exam cues
- "Most secure design" usually includes:
  - private paths,
  - least-privilege rules,
  - segmented network,
  - monitoring and audit visibility.

## One-line memory hook
Strong network security is controlled exposure plus continuous policy verification.

## Practical example
- Requirement: Secure production environment with minimal attack surface.
- Design: No public admin ports, segmented tiers, strict firewall by service identity, logging enabled.
- Why: Reduces exposure and improves auditability for sensitive paths.


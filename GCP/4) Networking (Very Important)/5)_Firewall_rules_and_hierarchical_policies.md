# 5) Firewall Rules And Hierarchical Policies (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Firewall rule basics
- Rules are stateful.
- Rules are direction-based: ingress or egress.
- Rules are priority-based.
- Match criteria can include source/destination ranges, protocol/port, and target selection.

## 2. Priority strategy
- Lower numeric priority value has higher precedence.
- Explicit deny and allow strategy should be documented.
- Avoid ambiguous overlapping rules.

## 3. Targeting strategy
- Prefer identity-driven targeting (service account) when possible.
- Use network tags with clear ownership and lifecycle controls.

## 4. Hierarchical policies
- Organization/folder-level policies enforce global guardrails.
- Project-level rules handle workload-specific needs.
- Strong model for centralized security governance.

## 5. Logging and audit
- Enable firewall logging on critical rules.
- Monitor deny spikes and unexpected allow events.
- Correlate policy changes with incident timelines.

## 6. Secure design patterns
- Default-deny posture where practical.
- Open only required ports and ranges.
- Restrict admin access paths and source ranges tightly.

## 7. Anti-patterns
- Broad `0.0.0.0/0` access to management ports.
- Rule sprawl without naming standards.
- Unused stale rules left active.

## 8. Troubleshooting checklist
1. Confirm target resource and tag/identity.
2. Check rule direction and priority.
3. Validate protocol/port match.
4. Validate source/destination range.

## 9. Exam cues
- "stateful firewall" and "priority-based" are key clues.
- "Centralized policy across many projects" -> hierarchical policy pattern.

## One-line memory hook
Least-privilege network policy means smallest scope, lowest exposure, and clear precedence.

## Practical example
- Requirement: Allow admin SSH only from office network across many projects.
- Design: Org-level deny for broad SSH, then explicit allow from office CIDR to admin targets.
- Why: Central baseline plus narrow exception gives least privilege at scale.


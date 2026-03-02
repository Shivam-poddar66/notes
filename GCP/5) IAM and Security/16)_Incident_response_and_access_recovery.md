# 16) Incident response and access recovery

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Incident response phases
1. Detect suspicious access pattern.
2. Contain:
   - disable risky keys/tokens,
   - suspend compromised accounts if needed.
3. Eradicate root cause:
   - remove bad bindings,
   - rotate secrets,
   - patch policy gaps.
4. Recover safely:
   - re-enable with controlled privileges.
5. Post-incident review and control hardening.

### Break-glass access
- Maintain minimal emergency path with strong audit and approval workflow.
- Regularly test break-glass procedure.

Exam cues:
- "Immediate containment for compromised credentials" -> disable/rotate, then re-grant safely.


## Practical example
- Requirement: Suspicious token use detected on production service account.
- Design: Immediately disable credentials, remove risky bindings, rotate secrets, restore minimal access.
- Why: Fast containment first, then safe recovery with audit trail.


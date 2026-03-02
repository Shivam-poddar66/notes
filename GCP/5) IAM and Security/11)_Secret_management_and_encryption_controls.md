# 11) Secret management and encryption controls

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Secret Manager patterns
- Store app credentials, API tokens, and sensitive config.
- Grant secret access only to required workload identities.
- Rotate secrets with tested rollout/rollback procedures.

### Encryption controls
- Default encryption at rest is provided.
- Use customer-managed keys (CMEK) when compliance/control requirements demand it.

### Common mistakes
- Putting secrets in source code or plain environment files.
- Giving broad secret accessor roles to unrelated workloads.

Exam cues:
- "Secure secret storage" -> Secret Manager.
- "Customer-controlled key requirement" -> CMEK direction.


## Practical example
- Requirement: App needs DB password rotation without code redeploy risk.
- Design: Store secret in Secret Manager and rotate versions with staged rollout.
- Why: Secure storage + controlled rotation reduces outage and leakage risk.


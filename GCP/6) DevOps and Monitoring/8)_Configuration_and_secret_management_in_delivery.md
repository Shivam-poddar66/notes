# 8) Configuration and secret management in delivery

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Configuration strategy
- Keep environment config separate from code.
- Use parameterization per environment.

### Secret strategy
- Use Secret Manager for credentials/tokens.
- Reference secrets at runtime/build securely.
- Rotate secrets and validate safe rollout.

### Security pattern
- Build/deploy identities should access only required secrets.

Exam cues:
- "Store deploy/runtime secrets securely" -> Secret Manager integration.


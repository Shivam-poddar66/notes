# 4) Cloud Build (deep dive)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### What Cloud Build provides
- Managed build and pipeline execution.
- Trigger-based automation from source changes.
- Supports build, test, scan, and deployment steps.

### Design patterns
- Reusable build configs/templates.
- Separate triggers by branch/environment.
- Use substitutions and parameterization for portability.

### Security patterns
- Least-privilege build service account.
- Avoid broad permissions in build identity.
- Keep secrets externalized.

### Reliability patterns
- Deterministic build steps.
- Retry-safe scripts where possible.
- Version and pin critical build dependencies.

Exam cues:
- "Managed CI/CD build pipeline" -> Cloud Build.


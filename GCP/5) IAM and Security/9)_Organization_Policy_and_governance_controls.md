# 9) Organization Policy and governance controls

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Purpose
- Enforce security and compliance guardrails centrally.

### Common controls (conceptual)
- Restrict risky defaults.
- Control allowed regions/services.
- Restrict external sharing and key creation patterns where required.

### Governance pattern
- Baseline controls at org/folder.
- Exception workflow documented and audited.

Exam cues:
- "Enforce policy across many projects" -> Organization Policy.


## Practical example
- Requirement: Security team wants one rule set across all projects.
- Design: Configure Organization Policy constraints at org/folder level.
- Why: Central enforcement prevents drift and inconsistent project settings.


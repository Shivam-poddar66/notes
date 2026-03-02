# 11) Alerting and on-call design

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Alert quality principles
- Alert on user impact and actionable signals.
- Avoid noisy low-value alerts.
- Use severity tiers and escalation paths.

### Alert policy patterns
- Multi-condition policies for confidence.
- Burn-rate style SLO alerts for early risk detection.
- Distinct policies for availability, latency, and deployment failures.

### On-call operations
- Runbooks linked from alerts.
- Fast ownership routing and escalation.
- Track MTTA/MTTR trends.

Exam cues:
- "Reduce alert fatigue while improving response" -> actionable alert design.


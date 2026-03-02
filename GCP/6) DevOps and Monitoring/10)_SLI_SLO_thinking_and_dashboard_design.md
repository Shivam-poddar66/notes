# 10) SLI/SLO thinking and dashboard design

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### SLI and SLO basics
- SLI: measured behavior (for example success rate/latency).
- SLO: target objective for SLI.

### Dashboard strategy
- Executive reliability view:
  - availability,
  - latency,
  - error rate.
- Service deep-dive view:
  - resource saturation,
  - dependency health,
  - release markers.

### Error budget concept
- Reliability target creates an allowable failure budget.
- Release velocity should respect error budget consumption.

Exam cues:
- "Balance release speed and reliability" -> SLO/error budget approach.


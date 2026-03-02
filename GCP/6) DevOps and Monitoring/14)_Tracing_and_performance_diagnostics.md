# 14) Tracing and performance diagnostics

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Why tracing matters
- Metrics tell you "something is wrong."
- Traces help show "where in request path it is wrong."

### Patterns
- Distributed tracing across service boundaries.
- Link traces, logs, and metrics with correlation IDs.
- Use profiling and performance tools for CPU/memory hotspots where needed.

### Operational use
- Identify slow dependencies.
- Reduce tail latency (`p95/p99`) and timeout chains.

Exam cues:
- "Need request-level latency breakdown" -> tracing approach.


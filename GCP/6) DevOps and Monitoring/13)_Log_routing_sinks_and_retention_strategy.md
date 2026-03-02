# 13) Log routing, sinks, and retention strategy

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### Log sinks
- Route logs to:
  - BigQuery for analytics,
  - Cloud Storage for archival/compliance,
  - Pub/Sub for streaming workflows.

### Design patterns
- Keep default operational logs in logging backend.
- Export specific high-value streams to downstream systems.
- Set retention and lifecycle intentionally.

### Cost and governance
- Prevent unbounded logging cost growth.
- Define retention by compliance and operational value.

Exam cues:
- "Route logs to analytics data warehouse" -> sink to BigQuery.
- "Archive logs long-term" -> sink to Cloud Storage.


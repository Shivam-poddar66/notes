# 1) Incident Commander Protocol for Production Outages

## Executive Overview
When a P1/P0 production outage strikes—the database is locked, the checkout endpoint is failing, or customer data is compromised—the biggest danger is rarely the bug itself; it is **organizational panic**. If 15 engineers jump on a call and simultaneously run debug scripts, reboot servers, and argue over root cause while executives scream for updates, the outage is prolonged by hours. High-reliability organizations (Google, PagerDuty, AWS) employ the **Incident Commander (IC) System** adapted from the US Federal Emergency Management Agency (FEMA).

---

## 1. The Incident Command Structure

```text
               ┌──────────────────────────────────────────────┐
               │           INCIDENT COMMANDER (IC)            │
               │   • Absolute tactical authority in outage    │
               │   • DOES NOT WRITE CODE OR DEBUG DIRECTLY    │
               │   • Coordinates, delegates, and maintains calm│
               └──────────────────────┬───────────────────────┘
                                      │
            ┌─────────────────────────┼─────────────────────────┐
            ▼                         ▼                         ▼
   [ OPERATIONS LEAD ]       [ COMMUNICATIONS LEAD ]     [ SCRIBE / SCRIBE BOT ]
   • Investigates telemetry  • Handles executive Slack   • Logs exact timeline:
   • Tests rollback/mitigate • Updates public status page  who did what, when,
   • Executes DB patches     • Broadcasts every 15 mins    and what output was
```

---

## 2. The Cardinal Rules of Incident Command

### Rule 1: The IC Never Writes Code
The moment the Incident Commander opens their terminal and starts debugging, they develop tunnel vision and lose situational awareness of the broader system. The IC must remain detached to direct traffic, evaluate risks, and make high-level calls.

### Rule 2: Mitigation Precedes Root Cause
- **Amateur Mentality**: *"Let's spend 3 hours figuring out why this subtle race condition occurred."*
- **Incident Commander Mentality**: *"Mitigate customer impact right now! Roll back to the previous deployment, enable traffic shedding, or flip the feature flag to false. We will debug the root cause tomorrow during the post-mortem."*

### Rule 3: Clear Voice Protocol
- In the incident bridge, only one person speaks at a time.
- The IC directs investigations explicitly: *"Dave, spend 5 minutes checking the Redis memory logs and report back. Sarah, prepare the rollback pipeline in staging. Scribe, log that we scaled the cluster at 14:22 UTC."*

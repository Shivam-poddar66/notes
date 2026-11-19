# 4) The Incident Commander Checklist

## Executive Overview
During a live P0/P1 production incident, the cognitive load on responders is extreme. Stress causes tunnel vision, skipped safety protocols, and fragmented communication. This operational checklist provides an Incident Commander (IC) with an unflinching, step-by-step protocol to command the room, stabilize the system, shield engineers, and communicate with leadership.

---

## 1. Live Outage Emergency Protocol Checklist

```text
================================================================================
                    INCIDENT COMMANDER (IC) RUNBOOK
================================================================================
INCIDENT SEVERITY: [ ] P0 (Site Down / Data Loss)   [ ] P1 (Critical Service Degraded)
INCIDENT COMMANDER: ____________________    WAR ROOM LINK: _____________________

--------------------------------------------------------------------------------
PHASE 1: TAKING COMMAND (FIRST 5 MINUTES)
[ ] Announce Command: "I am taking Incident Commander for this outage.
    Do not run uncoordinated commands in production."
[ ] Appoint Roles:
    • Operations Lead: ____________________ (Directs technical investigation)
    • Communications Lead: ________________ (Drafts status page & executive pings)
    • Scribe: _____________________________ (Logs timestamped actions)
[ ] Enforce Single Voice: Remind war room that only one person speaks at a time.
[ ] Quarantine the War Room: Mute non-essential observers; direct executives to
    the `#incident-status` broadcast channel.

--------------------------------------------------------------------------------
PHASE 2: MITIGATION OVER ROOT CAUSE (MINUTES 5 TO 30)
[ ] Focus on Customer Relief First:
    • Can we roll back to the last known good commit? [ Yes / No ]
    • Can we shed non-essential background traffic?   [ Yes / No ]
    • Can we flip feature flags to fallback mode?     [ Yes / No ]
    • Can we failover to our secondary read replica?  [ Yes / No ]
[ ] Ban Deep Root-Cause Rabbit Holes: Stop engineers from debugging obscure race
    conditions while the customer checkout is actively failing!

--------------------------------------------------------------------------------
PHASE 3: EXECUTIVE & CUSTOMER COMMUNICATIONS (EVERY 15 MINUTES)
[ ] Broadcast Update #1 (T+15m) using What / So What / Now What format:
    • WHAT: The objective failure (e.g. database CPU at 98%).
    • SO WHAT: Customer impact (e.g. 15% of European checkouts failing).
    • NOW WHAT: Action underway (e.g. scaling read replicas, next update at 14:30).
[ ] Update public status page (e.g. Statuspage.io) with customer-facing language.

--------------------------------------------------------------------------------
PHASE 4: RESOLUTION & DE-ESCALATION
[ ] Verify System Stability: Monitor p99 latency and error rates for 15 minutes.
[ ] Stand Down the War Room: "System has stabilized; incident is downgraded to P3."
[ ] Schedule Blameless Post-Mortem within 48 hours.
[ ] Assign Post-Mortem Author: ____________________
================================================================================
```

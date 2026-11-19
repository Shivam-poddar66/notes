# 2) The RACI Matrix & The Single Accountability Principle

## Executive Overview
When projects stall, deadlocks persist, or production systems fail without anyone taking ownership, the culprit is almost always **shared or ambiguous accountability**. As the leadership adage states: *"When everyone is responsible, no one is responsible."* The RACI Matrix eliminates organizational ambiguity by establishing clear roles for every major initiative, enforced by the non-negotiable **Single Accountability Principle**.

---

## 1. The RACI Role Definitions

```text
                  ┌──────────────────────────────────────────────┐
                  │                 RACI MATRIX                  │
                  └──────────────────────┬───────────────────────┘
                                         │
        ┌────────────────────────────────┼────────────────────────────────┐
        ▼                                ▼                                ▼
  [ R: RESPONSIBLE ]            [ A: ACCOUNTABLE ]              [ C: CONSULTED ]
  • The doer(s) of the work     • The SINGLE decision-maker     • Two-way communication
  • Multiple people allowed     • "The buck stops here"         • Subject matter experts
  • Writes the code/RFC         • EXACTLY ONE PERSON ALLOWED    • Provide critical input
                                         │
                                         ▼
                                [ I: INFORMED ]
                                • One-way updates
                                • Stakeholders notified of outcomes
```

---

## 2. The Non-Negotiable Single 'A' Rule

The most common failure mode in applying RACI is assigning multiple people as "Accountable" (e.g. *Product Manager AND Tech Lead*).  
- If two people are accountable, they will either engage in continuous political turf wars, or assume the other person is handling critical details, leading to dropped balls.
- **Rule**: There can be **only one single 'A'** for any row in a RACI matrix.

---

## 3. Practical Engineering RACI Matrix: Cloud Database Migration

| Activity / Deliverable | Tech Lead | DBA / SRE | Product Manager | VP of Eng | Security Lead |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **RFC & Architecture Spec** | **A / R** | C | I | I | C |
| **Schema Migration Script** | R | **A** | I | I | C |
| **Maintenance Window Timing** | C | C | **A** | I | I |
| **SOC2 Security Compliance** | C | C | I | I | **A / R** |
| **Final Budget Approval** | C | I | C | **A** | I |

*(Legend: A = Accountable, R = Responsible, C = Consulted, I = Informed)*

# 4) Team Interaction Modes: Collaboration, X-as-a-Service, Facilitating

## Executive Overview
Defining team topologies is insufficient if you do not define **how teams interact**. Uncontrolled communication between teams creates chaotic overhead, cross-team dependencies, and decision paralysis. *Team Topologies* defines exactly **three well-defined interaction modes** that govern how teams work together.

---

## 1. The 3 Interaction Modes

```text
                  ┌──────────────────────────────────────────────┐
                  │          THE 3 INTERACTION MODES             │
                  └──────────────────────┬───────────────────────┘
                                         │
        ┌────────────────────────────────┼────────────────────────────────┐
        ▼                                ▼                                ▼
  [ COLLABORATION ]             [ X-AS-A-SERVICE ]              [ FACILITATING ]
  • Two teams work closely      • One team consumes API/tool    • One team coaches and
    together for discovery        from another with zero          clears blockers for
  • High bandwidth communication  synchronous meetings            another team
  • Temporary (2-6 weeks)       • Stable, permanent mode        • Temporary (1-4 weeks)
```

---

## 2. Interaction Mode Specifications

### 1. Collaboration (Discovery & Co-Creation)
- **When to Use**: During high uncertainty—e.g. two teams co-designing a new event schema or integrating two previously siloed domains.
- **Duration**: Strictly temporary (2 to 6 weeks).
- **Rule**: If two teams must collaborate continuously for 6 months, their team boundaries are flawed and they should either be merged or redrawn. Continuous collaboration indicates unresolved coupling.

### 2. X-as-a-Service (Autonomous Consumption)
- **When to Use**: The ideal, default long-term operating state between Platform teams and Stream-Aligned teams, or between two mature domain squads.
- **Mechanics**: Team A provides an API, CLI tool, or self-service component with comprehensive documentation and SLAs. Team B consumes it without needing a meeting, Slack ping, or Jira ticket.
- **Rule**: High clarity, minimal synchronous communication overhead.

### 3. Facilitating (Coaching & Upskilling)
- **When to Use**: The primary mode of **Enabling Teams**.
- **Mechanics**: Specialists work alongside a Stream-Aligned team to upskill them on Kubernetes, automated performance testing, or security threat modeling.
- **Rule**: The facilitating team does not write the code for the host team; they coach the host team so they become self-sufficient.

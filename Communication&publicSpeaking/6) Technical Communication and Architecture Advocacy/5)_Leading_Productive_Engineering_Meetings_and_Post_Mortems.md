# 5) Leading Productive Engineering Meetings and Post-Mortems

## Executive Overview
Poorly run engineering meetings are multi-thousand-dollar productivity sinkholes. When 8 senior engineers sit in an aimless 60-minute meeting where two people debate tabs-versus-spaces or edge-case syntax, the company burns hundreds of engineering hours. Leading crisp, disciplined meetings, sprint retrospectives, and blameless post-mortems is a core leadership capability.

---

## 1. The Anatomy of a High-Velocity Meeting

```text
               ┌──────────────────────────────────────────────┐
               │        THE 3 PRE-CONDITIONS FOR A MEETING    │
               │  1. An explicit written objective            │
               │  2. A pre-circulated document or RFC         │
               │  3. An explicit decision-maker in the room   │
               └──────────────────────┬───────────────────────┘
                                      │
  If the goal is merely to share information ──► SEND AN ASYNC DOC / LOOM
  If the goal is to resolve a conflict / make a decision ──► HOLD THE MEETING
```

### The "No Agenda, No Meeting" Rule
If an invite lacks an agenda with clear outcomes, cancel or decline the meeting.
- **Good Objective**: *"Decide whether to adopt Postgres sharding or migrate to Citus for our Q4 scale-up."*
- **Bad Objective**: *"Discuss database stuff."*

---

## 2. Facilitating Technical Consensus Without Infinite Debates

### The 10-Minute Debate Cap & Disagree and Commit
When two senior developers enter an intractable deadlock over architecture:
1. Intervene: *"We have heard strong arguments for both Option A and Option B. We have debated this for 10 minutes, and we are not uncovering new data."*
2. Summarize the trade-offs on a shared board.
3. Call on the designated Architecture Owner: *"Sarah, as the tech lead, what is your call?"*
4. Invoke **Disagree and Commit (Andy Grove, Intel)**: *"We are committing 100% of our energy to Option A. If our metrics show performance degradation after 30 days, we re-evaluate."*

---

## 3. Running a Blameless Post-Mortem (Etsy Protocol)

The goal of a post-mortem is **systemic resilience**, not assigning personal culpability. Punishing human error guarantees that future engineers will hide mistakes, cover up outages, and slow down deployments.

```text
       BLAMEFUL POST-MORTEM (Toxic)               BLAMELESS POST-MORTEM (Resilient)
  ┌──────────────────────────────────────┐     ┌──────────────────────────────────────┐
  │ "Who pushed the bad code?"           │     │ "What systemic safeguards failed?"   │
  │ "Why didn't Dave test this?"         │     │ "Why did our CI/CD pipeline allow    │
  │ "Who approved this PR?"              │     │  untested migrations to production?" │
  │ Result: Fear, cover-ups, slower dev. │     │ Result: Automated safety, resilience │
  └──────────────────────────────────────┘     └──────────────────────────────────────┘
```

### The 5 Whys Root-Cause Method
- **Incident**: Production database dropped all foreign keys.
  - *Why 1*: A developer ran an unreviewed schema script.
  - *Why 2*: The staging test environment did not mirror production foreign key constraints.
  - *Why 3*: Staging sync scripts were disabled 3 months ago due to cloud storage limits.
  - *Why 4*: Nobody owned maintaining staging infrastructure parity.
  - *Root Cause*: Lack of automated infrastructure-as-code parity validation, not the individual developer's keyboard press!
- **Action Items**: Concrete, assigned to owners with Jira ticket IDs, due within 2 sprints.

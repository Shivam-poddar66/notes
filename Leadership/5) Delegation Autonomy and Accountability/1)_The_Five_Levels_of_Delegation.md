# 1) The Five Levels of Delegation

## Executive Overview
Delegation is the primary mechanism of managerial leverage. Yet, most managers view delegation as a binary switch: either they hoard the task completely, or they dump the entire project onto an engineer and walk away (abdication). When the project fails, the manager claims: *"I can't trust anyone; if you want something done right, you have to do it yourself."* True delegation is a **calibrated spectrum of five distinct levels** matched to the engineer's skill and the task's criticality.

---

## 1. The 5 Levels of Delegation (Michael Hyatt)

```text
  ┌────────────────────────────────────────────────────────┐
  │ LEVEL 1: WAIT TO BE TOLD                               │
  │ "Research this issue, do not take action, wait for me  │
  │  to tell you exactly what to do next."                 │
  ├────────────────────────────────────────────────────────┤
  │ LEVEL 2: LOOK INTO IT & REPORT BACK                    │
  │ "Investigate options and report all the facts;         │
  │  I will evaluate the data and make the decision."      │
  ├────────────────────────────────────────────────────────┤
  │ LEVEL 3: RECOMMEND A PATH                              │
  │ "Analyze the problem and bring me your recommended      │
  │  solution. I will review and approve before execution."│
  ├────────────────────────────────────────────────────────┤
  │ LEVEL 4: ACT & INFORM IMMEDIATELY                      │
  │ "Make the decision and execute immediately, but notify │
  │  me of your actions and outcome right away."           │
  ├────────────────────────────────────────────────────────┤
  │ LEVEL 5: FULL AUTONOMY                                 │
  │ "Own the entire domain end-to-end. No need to inform me│
  │  unless you hit a strategic or organizational blocker."│
  └────────────────────────────────────────────────────────┘
```

---

## 2. Calibrating the Level to the Task

| Scenario | Optimal Delegation Level | Rationale |
| :--- | :---: | :--- |
| **P1 Production Security Breach** | **Level 1 or 2** | Critical company risk; tight command and control required. |
| **Evaluating a New CI/CD Linter** | **Level 3** | Mid-level engineer can evaluate tools and present recommendation to team. |
| **Refactoring Internal Helper Library** | **Level 4** | Experienced engineer can execute and notify manager in standup. |
| **Staff Engineer Leading Cloud Migration** | **Level 5** | Staff engineer has full domain mastery and high task maturity. |

---

## 3. The Explicit Handshake
Never assign a project without explicitly agreeing on the delegation level:
- *"Sarah, for this database sharding investigation, I want you operating at **Level 3**. Benchmark the top 3 options, bring me your recommendation by Thursday, and we will decide together before writing any code."*
- Eliminates misunderstandings, micromanager anxiety, and accidental abdication.

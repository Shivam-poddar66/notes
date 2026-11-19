# 5) Sizing and Scaling Teams: The Two-Pizza Rule

## Executive Overview
As an engineering team grows, communication paths multiply exponentially, not linearly. If a team grows beyond a critical threshold, coordinating work consumes more time than doing the work itself. Managing team sizing, span of control, and communication channels is the key to preventing organizational bureaucracy.

---

## 1. The Communication Channels Equation

The number of potential communication links ($C$) in a group of size $N$ is governed by:

$$C = \frac{N(N - 1)}{2}$$

```text
  TEAM OF 5 ENGINEERS:               TEAM OF 12 ENGINEERS:
  C = (5 * 4) / 2 = 10 Channels      C = (12 * 11) / 2 = 66 Channels (6.6x increase!)

  TEAM OF 20 ENGINEERS:              TEAM OF 50 ENGINEERS:
  C = (20 * 19) / 2 = 190 Channels   C = (50 * 49) / 2 = 1,225 Channels!
```

---

## 2. Jeff Bezos’s "Two-Pizza Team" Rule

A software team should never be larger than what can be fed with **two large pizzas** (typically **5 to 8 engineers**).

### Why the 5–8 Member Size is Optimal:
1. **High Psychological Safety**: Team members know each other personally, understand each other's strengths and weaknesses, and communicate with high trust.
2. **Short, Disciplined Standups**: Daily standups finish in under 10 minutes without turning into lectures.
3. **Collective Ownership**: No individual can hide in the shadows; everyone is directly accountable for sprint deliverables.
4. **Single-Digit Cognitive Complexity**: The team can coordinate architectural decisions organically without formal committee approvals.

---

## 3. Manager Span of Control

| Seniority of Manager | Recommended Span of Control | Key Constraint |
| :--- | :--- | :--- |
| **Frontline EM (Direct Reports)** | **6 to 8 Engineers** | Must deliver weekly 30-min 1-on-1s, conduct reviews, and unblock code. More than 9 reports leads to burnout and canceled 1-on-1s. |
| **Senior EM / Group Lead** | **4 to 6 Tech Leads / EMs** | Manages managers; focuses on cross-squad architecture and hiring pipelines. |
| **Director / VP of Engineering** | **5 to 8 Senior EMs / Directors** | Focuses on organizational topology, multi-team strategy, budget, and executive alignment. |

# 2) The SCQA Narrative Framework

## Executive Overview
While the Minto Pyramid structures the logical arguments, **SCQA** (**S**ituation, **C**omplication, **Q**uestion, **A**nswer) is the narrative engine that introduces the topic. It bridges the gap between storytelling and hard business logic, establishing immediate stakeholder buy-in before presenting recommendations.

---

## 1. The SCQA Structural Flow

```text
  [ S: SITUATION ]     "Everything was stable and mutually agreed upon."
         │             (Sets the non-controversial baseline context)
         ▼
  [ C: COMPLICATION ]  "Suddenly, something changed, broke, or threatened the goal."
         │             (Creates emotional tension and cognitive friction)
         ▼
  [ Q: QUESTION ]      "What is the central problem we must solve right now?"
         │             (Frames the exact decision boundary)
         ▼
  [ A: ANSWER ]        "Here is our clear, actionable architectural proposal."
                       (Resolves the tension and delivers the BLUF)
```

---

## 2. Deconstructing Each Component

### 1. Situation (The Baseline)
- Must be a statement that everyone in the room already knows and agrees with.
- Establishes psychological safety and common ground.
- *Example*: *"Our mobile app has achieved 2 million active daily users with a 4.8-star rating across both app stores."*

### 2. Complication (The Trigger Event)
- The change in external conditions, system failure, security alert, or competitive threat.
- *Example*: *"However, our legacy authentication service was not designed for this load, and concurrent user login times have tripled during peak morning hours."*

### 3. Question (The Explicit Problem)
- The core question that naturally arises in the stakeholder's mind.
- *Example*: *"How do we scale our authentication tier to support 5 million users without risking downtime or security regressions?"*

### 4. Answer (The Resolution)
- Your concrete, high-level proposal.
- *Example*: *"By decoupling session storage into a managed Redis Cluster and issuing stateless JWTs, which we can roll out in three staged canary phases."*

---

## 3. SCQA Permutations for Different Audiences

| Audience Type | Optimal SCQA Ordering | Rationale |
| :--- | :--- | :--- |
| **C-Level Executive (Time-Constrained)** | **A $\longrightarrow$ S $\longrightarrow$ C** | Give the answer immediately, followed by context and risk. |
| **Engineering / Technical Review** | **S $\longrightarrow$ C $\longrightarrow$ Q $\longrightarrow$ A** | Engineers want to understand constraints and failure modes before evaluating the solution. |
| **Crisis / Incident Post-Mortem** | **C $\longrightarrow$ A $\longrightarrow$ S** | Start with what broke and what was fixed, then explain the baseline and prevention. |

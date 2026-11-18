# 2) Nonviolent Communication (NVC) for Teams

## Executive Overview
Created by clinical psychologist Dr. Marshall Rosenberg, **Nonviolent Communication (NVC)** is an empirical protocol for de-escalating interpersonal conflict, resolving emotional gridlock, and aligning teams. Traditional workplace debates are riddled with "violent" linguistic triggers: moralistic judgments (*"you're being irresponsible"*), diagnostic labels (*"you're disorganized"*), and demands masked as requests. NVC translates conflict into objective data, human needs, and actionable requests.

---

## 1. The 4 Components of the NVC Model

```text
  ┌────────────────────────────────────────────────────────┐
  │ 1. OBSERVATION: Pure facts without judgment or evaluation
  └──────────────────────────┬─────────────────────────────┘
                             │
  ┌──────────────────────────┴─────────────────────────────┐
  │ 2. FEELING: Internal emotional state without blame     │
  └──────────────────────────┬─────────────────────────────┘
                             │
  ┌──────────────────────────┴─────────────────────────────┐
  │ 3. NEED: The universal human or business value         │
  └──────────────────────────┬─────────────────────────────┘
                             │
  ┌──────────────────────────┴─────────────────────────────┐
  │ 4. REQUEST: Concrete, actionable, positive behavior    │
  └────────────────────────────────────────────────────────┘
```

---

## 2. NVC in Engineering Conflict Resolution

### Conflict Scenario: PR Code Review Deadlock
A senior developer leaves 40 nitpicky, aggressive comments on a feature branch, causing the author to feel micromanaged and insulted.

```text
  AGGRESSIVE / ACCUSATORY STATEMENT:
  "You are micromanaging my code, nitpicking pointless styling, and sabotaging my release."
  Result: Escalation, defensiveness, HR mediation.

  NVC TRANSFORMATION SCRIPT:
  1. OBSERVATION:
     "In the latest PR, there are 42 comments regarding variable naming conventions and whitespace."
  2. FEELING:
     "I feel overwhelmed and concerned about our delivery schedule..."
  3. NEED:
     "...because we both need high code maintainability while hitting our release milestone on Friday."
  4. REQUEST:
     "Would you be open to adopting an automated Prettier/ESLint linter in our pre-commit hook today,
      so we can merge this feature and automate styling rules going forward?"
  Result: Immediate resolution, automated tooling upgrade, zero bruised egos.
```

---

## 3. The Distinction Between Needs and Strategies
- **Needs are Universal**: Respect, autonomy, clarity, predictability, security, competence.
- **Strategies are Specific Implementations**: Mandating a specific git workflow, picking a specific cloud provider, writing a design doc in a specific template.
- When teams argue over architecture, they are almost never arguing over fundamental values; they are arguing over conflicting **strategies** to achieve the same underlying need. Uncovering the shared need collapses the conflict.

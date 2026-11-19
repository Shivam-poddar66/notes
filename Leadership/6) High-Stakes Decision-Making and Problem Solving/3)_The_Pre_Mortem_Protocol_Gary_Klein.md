# 3) The Pre-Mortem Protocol (Gary Klein)

## Executive Overview
A traditional **Post-Mortem** happens after a disaster: the system crashed, the company lost millions, and engineers sit in a room performing an autopsy. Research by cognitive psychologist Gary Klein demonstrated that humans suffer from intense **optimism bias** and **groupthink** when planning projects: people who harbor doubts remain silent because they don't want to be perceived as negative or unsupportive. A **Pre-Mortem** reverses this psychology by legitimizing and mandating dissent *before* a project begins.

---

## 1. The Pre-Mortem Conceptual Engine

```text
  TRADITIONAL PLANNING (OPTIMISM BIAS):
  "Let's assume our architecture plan is great, and everyone who disagrees is a blocker."
  Result: Silent dissent, overlooked failure modes, disaster at launch.

  THE PRE-MORTEM (PROSPECTIVE HINDSIGHT):
  "Assume it is 12 months in the future. The project has been an absolute, catastrophic failure.
   Our servers melted, our budget was blown, and the CEO fired our team.
   Take 10 minutes to write down the exact history of how and why we failed."
```

---

## 2. The 5-Step Pre-Mortem Execution Protocol

1. **Set the Stage (The Catastrophe Assumption)**:
   - The leader announces: *"Imagine we launched this new payment engine today, and 6 months from now, it has catastrophically failed. It corrupted transaction data, breached compliance, and crashed the website."*
2. **Individual Silent Brainstorming (10 Mins)**:
   - Every participant sits in silence writing down every single reason why the project died.
   - Crucial: By assuming the project *already failed*, individuals are freed from the social pressure of sounding disloyal.
3. **Round-Robin Reading**:
   - Go around the table. Each engineer reads one failure point from their list. No debating or defending allowed during this phase; just capture all failure modes on a whiteboard.
4. **Consolidation & Risk Matrix**:
   - Group the failure causes into clusters:
     - Technical/Architecture risks (e.g. partition lag, memory exhaustion).
     - Organizational/Team risks (e.g. key person dependency, cross-team delays).
     - External risks (e.g. third-party API rate limits).
5. **Architectural Mitigation Plan**:
   - Prioritize the top 3 highest-probability, highest-severity failure modes.
   - Build explicit automated guardrails, fallbacks, and circuit breakers into the design specification before writing a single line of production code.

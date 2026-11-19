# 3) Situational Leadership II Model (Hersey & Blanchard)

## Executive Overview
A common leadership fallacy is believing you must have a single "leadership style" (e.g. "I am a servant leader" or "I am a hands-off leader"). In reality, effective leadership requires tailoring your direction and support to the specific **development level** of an engineer on a *per-task basis*. A Principal Engineer might require hands-off autonomy for system design, but needs direct coaching when presenting to executive leadership for the first time.

---

## 1. The 4 Development Levels (D1 to D4)

| Level | Competence | Commitment / Confidence | Profile Example |
| :--- | :--- | :--- | :--- |
| **D1: Low Competence, High Commitment** | Lacks technical skill on this specific stack; enthusiastic and eager to learn. | High enthusiasm, low fear. | New college grad or junior engineer tackling their first distributed cache. |
| **D2: Some Competence, Low Commitment** | Has hit real-world edge cases; frustrated by complexity; self-doubt sets in. | Discouraged, overwhelmed. | Mid-level engineer debugging a multi-threading race condition for the first time. |
| **D3: High Competence, Variable Commitment** | Has the technical capability, but lacks confidence or feels burned out/hesitant. | Hesitant, self-critical, cautious. | Senior engineer pitching their first cross-organizational RFC. |
| **D4: High Competence, High Commitment** | Master of the domain; autonomous, highly self-motivated, proactive. | Confident, energized, accountable. | Staff engineer designing the team's next-generation storage pipeline. |

---

## 2. The 4 Leadership Styles (S1 to S4)

```text
  SUPPORTIVE BEHAVIOR (Praise, Active Listening, Emotional Support)
        ▲
        │
   High │   [ S3: SUPPORTING ]             [ S4: DELEGATING ]
        │   • Low Direction, High Support  • Low Direction, Low Support
        │   • For D3 (Competent/Hesitant)  • For D4 (Autonomous Master)
        │
        │   [ S2: COACHING ]               [ S1: DIRECTING ]
        │   • High Direction, High Support • High Direction, Low Support
    Low │   • For D2 (Frustrated Learner)  • For D1 (Enthusiastic Beginner)
        └────────────────────────────────────────────────────────►
          Low                         High
                     DIRECTIVE BEHAVIOR (Structure, Clear Steps, Checklists)
```

---

## 3. Style-to-Development Matching Rules

1. **S1 (Directing) for D1**:
   - Provide clear, explicit instructions: *"Clone this repo, follow the setup doc, write unit tests covering these 3 edge cases, and let's review at 3 PM."*
2. **S2 (Coaching) for D2**:
   - Offer technical guidance while rebuilding their confidence: *"Here is why the dead-lock occurred; let's walk through the trace together. You're doing great; distributed systems are inherently tricky."*
3. **S3 (Supporting) for D3**:
   - Ask empowering questions; do not micro-direct: *"You know this database better than anyone. What do you think the best sharding key is? I fully back your proposal."*
4. **S4 (Delegating) for D4**:
   - Hand over the goal and get out of the way: *"Here is our latency objective for Q3. Own the architecture and keep me updated on key risks."*

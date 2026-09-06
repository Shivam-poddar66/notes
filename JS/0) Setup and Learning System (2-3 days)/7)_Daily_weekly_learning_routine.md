# Daily and Weekly Learning Routine

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Overview & Learning Science

Consistency and active practice are the single most important factors in mastering JavaScript. Cognitive science demonstrates that **spaced repetition** and **deliberate practice** yield drastically higher long-term retention than passive reading or sporadic cramming.

### The 70-20-10 Learning Framework
- **70% Active Building & Debugging**: Writing actual code, solving console exercises, and building mini-projects without looking at solutions.
- **20% Code Review & Documentation**: Writing markdown notes, analyzing past mistake logs, and refactoring existing code.
- **10% Theory & Reading**: Reading official documentation (MDN, Node.js docs) and tutorial guides.

```
       +-------------------------------------------------------+
       |                  DELIBERATE PRACTICE                  |
       +-------------------------------------------------------+
       | 70% Hands-On Coding & Project Building                |
       | 20% Note Documentation & Mistake Log Review           |
       | 10% Reading Official Docs (MDN / Node Specs)          |
       +-------------------------------------------------------+
```

---

## 2. Daily Study System (The 90-Minute Focus Block)

Structure every daily learning session into four distinct, time-boxed blocks:

```
[00-15m] Active Recall  -->  [15-45m] Core Concept  -->  [45-75m] Hands-On Task  -->  [75-90m] Documentation
```

### 1. Active Recall (Minutes 00 - 15)
- Open yesterday's Markdown note and cover the implementation sections.
- Re-explain the main concepts out loud or write a 3-line mental summary.
- Review recent entries in your **Mistake Log** to ensure you do not repeat past errors.

### 2. Core Concept Study (Minutes 15 - 45)
- Read targeted documentation (e.g. `let` vs `const`, Promises, Event Loop).
- Avoid passive video consumption: type out every sample snippet into an editor manually—never copy-paste.

### 3. Hands-On Building (Minutes 45 - 75)
- Solve 2–3 console exercises or implement a specific mini-project milestone.
- Apply the 30-Minute Rule: If stuck on a bug for 30 minutes, isolate the issue into a standalone scratch file, inspect logs with DevTools/Console, and read exact error stack traces.

### 4. Documentation & Logging (Minutes 75 - 90)
- Summarize what you learned into your topic Markdown note.
- Record any unexpected bugs or misunderstandings in your Mistake Tracker.
- Commit your changes to Git: `git commit -m "docs: complete daily study on [topic]"`.

---

## 3. Weekly Learning Cadence (7-Day Cycle)

| Day | Phase | Core Objective | Key Activities |
| :--- | :--- | :--- | :--- |
| **Day 1** | New Topic | Foundations & Syntax | Read concepts, build basic code snippets |
| **Day 2** | New Topic | Deep Dive & Edge Cases | Test limits, trigger error cases, log stack traces |
| **Day 3** | Applied Practice | Exercises & Problem Solving | Solve 5 topic-specific exercises without hints |
| **Day 4** | New Topic | Advanced Patterns | Combine topic with previous phase concepts |
| **Day 5** | Mini-Project | Applied Implementation | Build a runnable CLI script or browser mini-project |
| **Day 6** | Spaced Review | Code Refactoring & Audit | Review past 14 days' notes, refactor messy code |
| **Day 7** | Assessment | Metrics & Weekly Plan | Audit mistake log, measure speed, plan next week |

---

## 4. The Mistake Log System

Every developer makes mistakes; top developers track and eliminate them systematically. Maintain a `MISTAKE_LOG.md` file in your notes repository.

### Mistake Log Structure

```markdown
# Personal JavaScript Mistake Log

| Date | Category | Code Snippet / Error | Root Cause | Prevention Strategy |
| :--- | :--- | :--- | :--- | :--- |
| 2026-09-06 | Scope | `Uncaught ReferenceError: x is not defined` | Declared `let x` inside `if` block, tried to access outside. | `let` and `const` are block-scoped. Access variables within block or declare in outer scope. |
| 2026-09-06 | Async | `Promise { <pending> }` | Forgot `await` keyword on `fetch()` call. | Async functions returning Promises must be awaited or chained with `.then()`. |
```

---

## 5. Automation & Progress Tracking via Git

Track your consistency objectively by inspecting your Git commit activity:

```bash
# Check commits made over the last 7 days
git log --since="7 days ago" --oneline

# Count total daily commits this month
git log --since="30 days ago" --oneline | wc -l

# View summary of files updated today
git diff --stat HEAD~1
```

---

## 6. Common Pitfalls & Anti-Patterns

### Anti-Pattern 1: "Tutorial Hell"
- **Symptom**: Watching hours of video tutorials without writing code independently.
- **Fix**: Never watch code being written without immediately closing the video and building it from scratch on your own machine.

### Anti-Pattern 2: Irregular Cramming
- **Symptom**: Studying 8 hours on Sunday, then doing zero coding Monday through Friday.
- **Fix**: Commit to a minimum of 45–90 minutes daily. Consistency builds neural pathways much faster than sporadic marathons.

### Anti-Pattern 3: Ignoring Error Tracebacks
- **Symptom**: Guessing random syntax fixes when an error occurs instead of reading the exact error message and line number.
- **Fix**: Always read stack traces from top to bottom before touching code.

---

## 7. Personal Setup Checklist & Exit Criteria

- [ ] Scheduled a fixed 90-minute daily learning window.
- [ ] Initialized `MISTAKE_LOG.md` to track recurring bugs and root causes.
- [ ] Enforced the 70-20-10 framework (70% building, 20% notes, 10% reading).
- [ ] Practiced the 30-Minute Rule when debugging stuck problems.
- [ ] Maintained a daily Git commit streak for project and note updates.
- [ ] Conducted a weekly retrospective on Day 7 to review metrics.

---

## 8. Quick Reference Daily Routine

```bash
00m - 15m : Review yesterday's note + Mistake Log
15m - 45m : Read MDN / documentation & type snippets manually
45m - 75m : Build hands-on mini-project / solve exercises
75m - 90m : Log new mistakes, update Markdown notes, & git push
```

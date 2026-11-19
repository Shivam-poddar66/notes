# 2) Managing Cognitive Load in Software Teams

## Executive Overview
In software engineering, team capacity is constrained not by hours in the day, but by **cognitive load**—the amount of mental effort required to perform a task. When an engineering team's cognitive load exceeds its working memory limit, code quality plummets, delivery slows to a crawl, and engineers burn out. Designing teams around bounded cognitive limits is a core architectural responsibility of engineering leadership.

---

## 1. The 3 Types of Cognitive Load (John Sweller)

```text
                  ┌──────────────────────────────────────────────┐
                  │            TOTAL COGNITIVE LOAD              │
                  └──────────────────────┬───────────────────────┘
                                         │
        ┌────────────────────────────────┼────────────────────────────────┐
        ▼                                ▼                                ▼
  [ INTRINSIC LOAD ]            [ EXTRANEOUS LOAD ]              [ GERMANE LOAD ]
  • Fundamental domain logic    • Friction, boilerplate,         • Creative problem-solving,
  • Syntax of programming lang  • Complex deploy pipelines       • Designing optimal systems,
  • Domain rules (e.g. tax laws)• Flaky test suites              • Adding business value
  (Minimize / Accept)           (ELIMINATE AGGRESSIVELY)         (MAXIMIZE TO THE LIMIT)
```

---

## 2. The Extraneous Load Crisis in Tech

In poorly managed engineering teams, up to 70% of an engineer's cognitive energy is burned on **extraneous friction**:
- Figuring out why the Kubernetes YAML deployment failed.
- Waiting 45 minutes for a flaky Jenkins pipeline to re-run.
- Chasing down credentials across 4 different cloud consoles.
- Navigating undocumented APIs written by another team 3 years ago.

### The Role of Platform Teams
The primary purpose of an internal developer platform (Platform Team) is to **reduce extraneous cognitive load to zero**. By providing self-service abstractions (e.g. `git push origin main` automatically builds, tests, runs security scans, and deploys to a preview environment), Stream-Aligned engineers can spend 90% of their mental bandwidth on **Germane Load** (shipping features for customers).

---

## 3. The Bounded Domain Rule
A single team should never own more than:
1. **One complex domain** (e.g., core financial ledger), OR
2. **Two to three simple, well-defined domains** (e.g., user profiles and notification preferences).
If an engineer must hold 15 microservices and 4 databases in their head just to deploy a single button, the team's domain boundary is broken and must be split.

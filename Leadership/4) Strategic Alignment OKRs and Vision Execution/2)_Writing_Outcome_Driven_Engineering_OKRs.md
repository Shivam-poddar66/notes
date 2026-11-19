# 2) Writing Outcome-Driven Engineering OKRs

## Executive Overview
Originating at Intel under Andy Grove and popularized at Google by John Doerr (*Measure What Matters*), **Objectives and Key Results (OKRs)** are the industry standard for goal setting. However, engineering organizations frequently butcher the framework by turning OKRs into boring task checklists (*"Ship feature X"*, *"Migrate to Kubernetes"*). A task checklist measures output, not business value. Elite engineering leaders write **outcome-driven OKRs** that measure measurable impact.

---

## 1. Objectives vs Key Results

```text
  [ THE OBJECTIVE (Qualitative & Inspirational) ]
  • Where do we want to go?
  • Ambitious, memorable, and motivating.
  • Example: "Build an impenetrable fortress around our customer data."
                        │
                        ▼
  [ THE KEY RESULTS (Quantitative & Measurable) ]
  • How do we know we got there?
  • Numeric, binary, outcome-oriented (never a task list).
  • Example: "Zero P1/P2 security vulnerabilities open > 7 days across all tiers."
```

---

## 2. Activity-Based vs Outcome-Based Key Results

| Bad (Activity / Output-Based) | Good (Outcome / Value-Based) | Why the Outcome Matters |
| :--- | :--- | :--- |
| *"Deploy the new Elasticsearch cluster."* | *"Reduce catalog search latency from 450ms to < 35ms for 99% of user queries."* | You could deploy Elasticsearch, configure it poorly, and make searches slower! |
| *"Rewrite the billing pipeline in Go."* | *"Reduce monthly compute infrastructure costs for the billing service by 40%."* | Rewriting code is a cost; lowering cloud spend is the value. |
| *"Implement automated end-to-end tests."* | *"Drop production defect escape rate from 12 bugs/sprint to < 2 bugs/sprint."* | Tests are useless if bugs still leak into production. |

---

## 3. The 70% Target Rule (Committed vs Aspirational OKRs)
- **Committed OKRs**: Must be achieved 100% (e.g. security compliance, system uptime SLAs). Falling short requires escalation and root-cause analysis.
- **Aspirational (Moonshot) OKRs**: Set so aggressively that achieving **70%** (a score of 0.7) is considered world-class success.
- If an engineering team achieves 100% on all aspirational OKRs every quarter, the leader is setting the bar too low and failing to push the boundaries of innovation.

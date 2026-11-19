# 2) The Cynefin Framework for Complex Systems

## Executive Overview
Created by Dave Snowden at IBM, the **Cynefin Framework** (pronounced *kuh-NEV-in*) is a decision-making model that categorizes problems and operating environments into five distinct domains: **Clear (Simple)**, **Complicated**, **Complex**, **Chaotic**, and **Confusion**. Many engineering management failures occur when leaders apply "best practices" (which work only in Clear domains) to Complex or Chaotic software environments.

---

## 1. The 5 Cynefin Domains

```text
               UNORDERED (Emergent)           ORDERED (Predictable)
        ┌─────────────────────────────┬─────────────────────────────┐
        │        [ COMPLEX ]          │       [ COMPLICATED ]       │
        │ • Domain of Emergence       │ • Domain of Experts         │
        │ • Unknown unknowns          │ • Known unknowns            │
        │ • Cause & effect only       │ • Cause & effect separated  │
        │   understood in retrospect  │   by time and space         │
        │ • Action: PROBE-SENSE-RESPOND│ • Action: SENSE-ANALYZE-    │
        │ • Practice: Emergent        │           RESPOND           │
        │                             │ • Practice: Good Practice   │
        ├─────────────────────────────┼─────────────────────────────┤
        │         [ CHAOTIC ]         │          [ CLEAR ]          │
        │ • Domain of Rapid Crisis    │ • Domain of Best Practice   │
        │ • High turbulence, zero     │ • Known knowns              │
        │   discernible cause/effect  │ • Cause & effect self-      │
        │ • Action: ACT-SENSE-RESPOND │   evident to everyone       │
        │ • Practice: Novel           │ • Action: SENSE-CATEGORIZE- │
        │                             │           RESPOND           │
        │                             │ • Practice: Best Practice   │
        └─────────────────────────────┴─────────────────────────────┘
                                [ CONFUSION ]
```

---

## 2. Engineering Decision Strategies by Domain

### 1. Clear Domain (Simple / Routine)
- **Example**: Creating a new git user account or configuring an SSL certificate.
- **Protocol**: **Sense $\longrightarrow$ Categorize $\longrightarrow$ Respond**. Apply standard operating procedures (SOPs) or automated scripts.

### 2. Complicated Domain (Expert Analysis)
- **Example**: Tuning PostgreSQL buffer pool memory parameters or optimizing database indexes.
- **Protocol**: **Sense $\longrightarrow$ Analyze $\longrightarrow$ Respond**. Hire or consult domain experts (DBAs), run benchmarks, evaluate trade-offs.

### 3. Complex Domain (Emergent / Distributed Systems)
- **Example**: Introducing microservices across 20 teams; predicting consumer behavior during a flash sale.
- **Protocol**: **Probe $\longrightarrow$ Sense $\longrightarrow$ Respond**. You cannot analyze your way to the answer in advance. Run safe-to-fail experiments, canary rollouts, and feature flags.

### 4. Chaotic Domain (Crisis / Outage)
- **Example**: Active ransomware attack or total datacenter power outage.
- **Protocol**: **Act $\longrightarrow$ Sense $\longrightarrow$ Respond**. Stop the bleeding first! Sever network connections, shed traffic, roll back releases. Analysis comes later.

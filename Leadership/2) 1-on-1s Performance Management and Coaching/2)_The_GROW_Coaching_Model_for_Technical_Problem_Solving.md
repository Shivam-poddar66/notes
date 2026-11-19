# 2) The GROW Coaching Model for Technical Problem Solving

## Executive Overview
When an engineer encounters a difficult technical challenge, cross-team conflict, or design roadblock, their default instinct is often to ask their manager: *"What should I do?"* If the manager immediately answers and solves the problem, they create intellectual dependency and rob the engineer of growth. The **GROW Model** (Sir John Whitmore) provides a structured questioning framework that guides the engineer to formulate their own robust solution.

---

## 1. The GROW Architecture

```text
  [ G: GOAL ]       "What is the specific outcome you want to achieve?"
         │          (Clarifies the target state and success criteria)
         ▼
  [ R: REALITY ]    "What is currently happening? What have you tried so far?"
         │          (Grounds the conversation in objective data and facts)
         ▼
  [ O: OPTIONS ]    "What are all the possible paths forward, constraints aside?"
         │          (Expands creative problem-solving and trade-off space)
         ▼
  [ W: WILL / WAY ] "Which option will you commit to, and what is your first step?"
                    (Locks in concrete accountability, timeline, and ownership)
```

---

## 2. GROW Coaching Transcript: Resolving an API Bottleneck

### 1. Goal (Clarify the Target)
- *Engineer*: *"Our microservice is dropping requests during peak load."*
- *Manager*: *"What is our target latency and throughput for this endpoint to satisfy our SLA?"*
- *Engineer*: *"We need p99 latency under 40ms at 15,000 requests per second."*

### 2. Reality (Ground in Facts)
- *Manager*: *"Where does the bottleneck appear in our Datadog APM traces right now?"*
- *Engineer*: *"The SQL query on the `order_items` table is performing sequential scans because the composite index is missing on `tenant_id` and `created_at`."*
- *Manager*: *"What else have you tried or observed?"*

### 3. Options (Explore Trade-offs)
- *Manager*: *"If you couldn't rewrite the SQL query, what other options exist?"*
- *Engineer*: *"Option A is adding a Redis cache in front of it. Option B is adding the composite index directly in PostgreSQL. Option C is vertical scaling the RDS instance."*
- *Manager*: *"What are the risks and trade-offs of each option?"*

### 4. Will / Way Forward (Commitment)
- *Manager*: *"Based on our maintenance window tonight, which approach do you recommend?"*
- *Engineer*: *"I recommend Option B—building the composite index concurrently. It solves the root cause with zero ongoing Redis cache invalidation complexity."*
- *Manager*: *"Brilliant. When will you have the migration script ready for peer review?"*
- *Engineer*: *"By 3:00 PM today."*
- *Result*: The engineer solved the problem, evaluated trade-offs, and owns the victory.

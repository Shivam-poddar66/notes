# 5) The Saga Pattern: Orchestration vs. Choreography

## Executive Overview
Because 2PC is too brittle and blocking for high-scale microservices, modern distributed systems implement the **Saga Pattern**. A Saga decomposes a distributed transaction into a sequence of **independent local database transactions**.

---

## 1. Saga Mechanics and Compensating Transactions

- Each step updates a single service's local database and emits a message or event.
- If a step in the chain fails, the Saga executes **Compensating Transactions** backward to reverse the side effects of previous successful steps.

```text
FORWARD EXECUTION (Happy Path):
[ 1. Order Svc: Create ] ──▶ [ 2. Payment Svc: Charge ] ──▶ [ 3. Inventory: Reserve ] ──▶ SUCCESS!

COMPENSATION ROLLBACK (When Inventory Step Fails):
[ 1. Order Svc: Create ] ──▶ [ 2. Payment Svc: Charge ] ──▶ [ 3. Inventory: OUT OF STOCK! ]
                                         │                                      │
                                         ▼                                      ▼
[ 1b. Order Svc: Mark CANCELLED ] ◀── [ 2b. Payment Svc: Refund $100 ] ◀────────┘
```

> **Crucial Rule**: A compensating transaction must be **Idempotent** and must **never fail** (it must retry until it succeeds, or escalate to human alert).

---

## 2. Orchestration vs. Choreography

```text
CHOREOGRAPHY (Event-Driven):
[ Order Svc ] ── `OrderCreated` ──▶ [ Payment Svc ] ── `PaymentCharged` ──▶ [ Inventory Svc ]
* Decentralized. Good for simple 2–3 step workflows.
* Becomes an untraceable "spaghetti architecture" when workflows exceed 4 steps.

ORCHESTRATION (Centralized State Machine):
                    ┌─────────────────────────┐
                    │    Saga Orchestrator    │
                    │ (Temporal / Step Func)  │
                    └────────────┬────────────┘
         ┌───────────────────────┼───────────────────────┐
    1. "Charge Card"        2. "Reserve Items"      3. "Send Confirmation"
         ▼                       ▼                       ▼
  [ Payment Svc ]         [ Inventory Svc ]       [ Notification Svc ]
```

| Dimension | Choreography | Orchestration |
| :--- | :--- | :--- |
| **Coordination** | Decentralized; services react to events | Centralized controller executes state machine |
| **Coupling** | Loose coupling (events only) | Participants are unaware of overall workflow |
| **Observability** | Hard to monitor total state across logs | Trivial: Orchestrator UI shows exact workflow state |
| **Complexity** | Cyclical dependency risk; hard to test | Easy to test, retry, and handle edge cases |
| **Industry Standard**| Simple asynchronous handoffs | Complex business flows (Temporal.io, Camunda, AWS Step Functions) |

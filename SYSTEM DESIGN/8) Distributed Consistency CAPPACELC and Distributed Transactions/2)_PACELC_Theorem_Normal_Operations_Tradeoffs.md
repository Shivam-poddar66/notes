# 2) PACELC Theorem: Normal Operations Trade-offs

## Executive Overview
The CAP Theorem only describes system behavior during a network partition ($P$). However, in a healthy system, network partitions occur less than 0.01% of the time. Daniel Abadi formulated the **PACELC Theorem** to describe the fundamental architectural trade-off that occurs during normal operations.

---

## 1. The PACELC Formula

$$\text{If }\mathbf{P}\text{ (Partition)} \implies \text{Choose }\mathbf{A}\text{ or }\mathbf{C}; \quad \mathbf{E}\text{lse (Normal)} \implies \text{Choose }\mathbf{L}\text{ (Latency) or }\mathbf{C}\text{ (Consistency)}$$

```text
                                  Distributed System
                                          │
                         Is there a Network Partition?
                                          │
                   ┌──────────────────────┴──────────────────────┐
                  YES                                            NO
                   │ (Partition)                                  │ (Else - Normal)
                   ▼                                              ▼
           Choose between:                                Choose between:
           A (Availability)                               L (Low Latency)
                  OR                                             OR
           C (Consistency)                                C (Strong Consistency)
```

---

## 2. Real-World Database Classifications

```text
┌─────────────────────────┬───────────────────┬───────────────────────────────────────────┐
│ Database Engine         │ PACELC Model      │ Architectural Trade-off                   │
├─────────────────────────┼───────────────────┼───────────────────────────────────────────┤
│ Apache Cassandra        │ PA / EL           │ During partition: Available; during       │
│                         │                   │ normal ops: prioritizes Latency over Cons.│
├─────────────────────────┼───────────────────┼───────────────────────────────────────────┤
│ Amazon DynamoDB         │ PA / EL (Default) │ Tunable: Can be configured as PC/EC via   │
│                         │                   │ ConsistentRead=true flag.                 │
├─────────────────────────┼───────────────────┼───────────────────────────────────────────┤
│ MongoDB                 │ PC / EC           │ During partition: Rejects writes if leader│
│                         │                   │ isolated; normal ops: strictly consistent.│
├─────────────────────────┼───────────────────┼───────────────────────────────────────────┤
│ Google Spanner          │ PC / EC           │ Uses TrueTime atomic clocks to deliver    │
│                         │                   │ linearizable consistency globally.        │
└─────────────────────────┴───────────────────┴───────────────────────────────────────────┘
```

> **Why Latency ($L$) Conflicts with Consistency ($C$) in Normal Ops**:
> To guarantee strong consistency ($C$), a database node must synchronously coordinate with replica nodes over the network (e.g., waiting for quorum or 2PC) before acknowledging a write. This waiting directly increases write **Latency ($L$)**.

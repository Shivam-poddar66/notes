# 3) Consistency Models Hierarchy: Linearizable to Eventual

## Executive Overview
Consistency in distributed systems is not a binary choice between "Strong" and "Eventual". There is a rigorous mathematical continuum of consistency models trading off latency, throughput, and system guarantees.

---

## 1. The Consistency Continuum

```text
[ STRONGEST GUARANTEES / HIGHEST LATENCY / LOWEST AVAILABILITY ]
                               │
                               ▼
                   1. Linearizable (Strict)
                   - Acts as if there is only a single global copy of data.
                   - Writes take effect instantaneously at a real-time point.
                               │
                               ▼
                   2. Sequential Consistency
                   - Operations execute in an agreed global sequence.
                   - Preserves the per-process program order.
                               │
                               ▼
                   3. Causal Consistency
                   - Operations related by causality are seen by all nodes in the same order.
                   - Concurrent (independent) operations may be seen in different order.
                               │
                               ▼
                   4. Read-Your-Writes (Session)
                   - A user's own updates are immediately visible to that user.
                               │
                               ▼
                   5. Monotonic Reads
                   - A user never sees data older than what they previously observed.
                               │
                               ▼
                   6. Eventual Consistency
                   - If no new writes occur, all replicas eventually converge to the same state.
                               │
[ WEAKEST GUARANTEES / LOWEST LATENCY / MAXIMUM AVAILABILITY ]
```

---

## 2. Real-World Use Cases by Consistency Level

- **Linearizable**: Banking balances, distributed leader election (ZooKeeper / Raft), inventory reservation counters.
- **Causal**: Collaborative chat threads, forum replies (you must see the original question before its reply).
- **Read-Your-Writes**: User profile updates, shopping cart additions.
- **Eventual**: Social media like counts, YouTube view counters, DNS propagation.

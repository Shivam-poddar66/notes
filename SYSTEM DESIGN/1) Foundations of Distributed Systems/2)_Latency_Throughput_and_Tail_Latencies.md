# 2) Latency, Throughput, and Tail Latencies

## Executive Overview
Performance in distributed systems is defined by two fundamental metrics: **Latency** (how fast a single operation completes) and **Throughput** (how many operations complete in a given timeframe). Senior architects optimize for **tail latency (p99 / p99.9)** because averages hide the worst user experiences.

---

## 1. Latency Numbers Every Distributed Systems Engineer Must Know

```text
┌────────────────────────────────────────────────────────┬────────────────┐
│ Operation                                              │ Real-World Time│
├────────────────────────────────────────────────────────┼────────────────┤
│ L1 CPU Cache Reference                                │ 0.5 – 1 ns     │
│ Branch Mispredict                                      │ 3 – 5 ns       │
│ L2 CPU Cache Reference                                │ 7 – 14 ns      │
│ Mutex Lock / Unlock                                    │ 25 ns          │
│ Main Memory (RAM) Reference                            │ 100 ns         │
│ Compress 1 KB with Snappy                              │ 2,000 ns (2 µs)│
│ Read 1 MB sequentially from memory                     │ 3,000 ns (3 µs)│
│ SSD Random Read                                        │ 10 – 50 µs     │
│ Read 1 MB sequentially from NVMe SSD                   │ 250 µs         │
│ Round trip within same datacenter                      │ 500 µs         │
│ Disk Seek (Magnetic HDD)                               │ 10,000 µs (10ms│
│ Read 1 MB sequentially from Magnetic HDD               │ 20,000 µs (20ms│
│ Send packet US East to US West (NYC to SF)             │ 40 – 60 ms     │
│ Transatlantic packet (London to NYC)                   │ 70 – 80 ms     │
│ Round-the-world packet (Singapore to NYC)              │ 150 – 200 ms   │
└────────────────────────────────────────────────────────┴────────────────┘
```

> **Key Takeaway**: Reading from RAM is ~1,000x faster than random SSD reads, and ~100,000x faster than magnetic disk seeks. Inter-datacenter network hops are orders of magnitude slower than any local storage operation.

---

## 2. Throughput vs. Latency (Little's Law)

- **Throughput (QPS / RPS)**: The rate at which the system processes requests ($\lambda$).
- **Latency (Response Time $W$)**: The average duration a request spends in the system.
- **Concurrency (Work in Progress $L$)**: The number of concurrent requests being processed.

### Little's Law:
$$L = \lambda \times W$$

**Example Application**:
- If your backend handles an arrival rate of $\lambda = 2,000\text{ requests/sec}$, and each request takes an average latency of $W = 100\text{ ms} = 0.1\text{ s}$:
  $$L = 2,000 \times 0.1 = 200\text{ concurrent in-flight requests}$$
- Your application server thread pool or asynchronous socket limit must be provisioned to handle at least 200 concurrent executions without queueing.

---

## 3. Why Averages Lie: Percentiles and Tail Latency

```text
Request Distribution:
0ms ─── p50 (Median, 50ms) ─── p95 (120ms) ─── p99 (350ms) ──────▶ p99.9 (2,500ms!)
```

- **p50 (Median)**: 50% of requests complete faster than this value.
- **p90 / p95**: 90% or 95% of requests complete faster than this.
- **p99 / p99.9 (Tail Latency)**: The slowest 1% or 0.1% of requests.

### The Fan-Out Amplification Effect:
In a microservices architecture, a single user request to the homepage might fan out to **100 downstream backend services** (recommendations, ads, user profile, notifications, reviews, cart):

If a single service has a $99\%$ success rate within 100 ms ($1\%$ tail latency probability):
$$\text{Probability that at least ONE service experiences tail latency} = 1 - (0.99)^{100} = 1 - 0.366 = \mathbf{63.4\%}$$

**Result**: Even though 99% of individual service calls are fast, **63.4% of all user page loads experience severe lag**!

### Mitigation Strategies for Tail Latency:
1. **Hedged Requests**: Send the same read request to two replicas; return whichever completes first and cancel the slower request.
2. **Strict Timeouts & Fast Failure**: Cancel requests that exceed p95 SLA rather than holding threads open.
3. **Deadlines & Cancellation Propagation**: Propagate gRPC context deadlines downstream across microservice hops.

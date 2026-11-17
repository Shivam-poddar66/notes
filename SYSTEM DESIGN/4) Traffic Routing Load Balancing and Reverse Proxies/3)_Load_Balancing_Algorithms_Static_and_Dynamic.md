# 3) Load Balancing Algorithms: Static and Dynamic

## Executive Overview
The load balancing algorithm determines how an incoming request is assigned to a specific server within a pool of healthy backend instances.

---

## 1. Static Algorithms (State-Agnostic)

Static algorithms distribute requests according to deterministic rules without inspecting the current real-time load or health of backend instances.

### A. Round Robin
- Requests are dispatched sequentially: Server 1 $\to$ Server 2 $\to$ Server 3 $\to$ Server 1.
- **Assumptions**: Assumes all backend servers have identical CPU/RAM specifications and all incoming requests take equal time to process.
- **Failure Mode**: If Server 2 receives 3 long-running report-generation requests, it becomes saturated while Server 1 and 3 sit idle.

### B. Weighted Round Robin
- Servers are assigned an integer weight proportional to their hardware capacity:
  - Server A (16 cores): Weight = 3
  - Server B (4 cores): Weight = 1
- For every 4 requests: 3 go to Server A, 1 goes to Server B.

### C. Source IP Hash
- Maps client IP address to a server index:
  $$\text{Index} = \text{hash}(\text{Client IP}) \pmod N$$
- **Pros**: Client with the same IP consistently hits the same server (basic session stickiness).
- **Cons**: If thousands of users connect behind a corporate proxy or mobile gateway sharing one public IP, that single backend server is overwhelmed.

---

## 2. Dynamic Algorithms (State-Aware)

Dynamic algorithms query real-time runtime metrics from backend instances before making a routing decision.

### A. Least Connections
- Directs traffic to the server with the fewest active, open TCP connections.
- **Ideal Workload**: Long-lived connections (WebSockets, database connection pools, video streaming, long-polling).

### B. Weighted Least Connections
- Computes: $\frac{\text{Active Connections}}{\text{Server Weight}}$.
- Directs traffic to the server with the lowest relative connection ratio.

### C. Least Response Time (Peak EWMA)
- Combines the number of active connections with the server's running response latency (Time To First Byte - TTFB) using an Exponentially Weighted Moving Average.
- **Benefit**: Automatically throttles traffic away from a server that is slowing down due to GC pauses or disk I/O contention.

---

## 3. Algorithm Selection Matrix

```text
┌──────────────────────────────┬──────────────────────────────────┐
│ Application Workload         │ Recommended Balancing Algorithm │
├──────────────────────────────┼──────────────────────────────────┤
│ Stateless REST APIs (Fast)   │ Round Robin / Weighted Round Rob.│
│ Long-lived WebSockets / Chat │ Least Connections                │
│ Variable-latency Heavy Work  │ Least Response Time (Peak EWMA)  │
│ Stateful In-Memory Cache     │ Consistent Hashing with VNodes   │
└──────────────────────────────┴──────────────────────────────────┘
```

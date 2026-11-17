# 4) Graceful Degradation, Load Shedding, and Chaos Engineering

## Executive Overview
When a system is subjected to traffic exceeding its physical hardware limits, allowing all requests to queue and time out degrades the experience for 100% of users. **Load Shedding** and **Graceful Degradation** prioritize core business revenue paths while proactively discarding non-critical workloads.

---

## 1. Load Shedding: Dropping Low-Priority Traffic

When CPU utilization exceeds 90% or the incoming request queue depth exceeds safety limits:
- The system must immediately reject incoming lower-priority requests with **HTTP 503 Service Unavailable** or **HTTP 429 Too Many Requests**, before threads are allocated to process them.

```text
PRIORITY TIERS FOR AN E-COMMERCE SYSTEM:
[ Tier 1: CRITICAL (Never drop) ]    ──▶ Checkout, Credit Card Payment, Login
[ Tier 2: IMPORTANT (Best effort) ]  ──▶ Product Search, Adding items to cart
[ Tier 3: NON-ESSENTIAL (Drop 1st) ] ──▶ Product Recommendations, Review Ratings, View History
```

---

## 2. Graceful Degradation Techniques

Instead of throwing error pages, the system serves degraded, functional alternatives:
1. **Stale Cache Fallback**: If the recommendation machine learning cluster is melting down, return the top 10 most popular products from a static Redis cache.
2. **Feature Toggling (Kill Switches)**: Remotely toggle off expensive UI widgets (e.g., real-time typing indicators in chat, personalized sidebar ads) via launch-darkly / feature flags to reduce backend compute by 40%.
3. **Asynchronous Deferral**: Instead of generating an export PDF invoice synchronously on checkout, tell the user: "Your invoice has been scheduled and will be emailed within 10 minutes."

---

## 3. Chaos Engineering (Netflix Chaos Monkey)
> *"The best time to discover your system's failure modes is during normal business hours when the entire engineering team is available, not at 3:00 AM on a Saturday."*

- **Chaos Monkey**: Randomly terminates production container instances and VMs to verify that autoscaling groups and load balancers recover without user impact.
- **Latency Monkey**: Injects synthetic 2-second delays into RPC calls to verify that circuit breakers trip and timeouts propagate correctly.
- **Chaos Kong**: Simulates an entire AWS Availability Zone or Region outage to verify cross-region active-active disaster recovery failover.

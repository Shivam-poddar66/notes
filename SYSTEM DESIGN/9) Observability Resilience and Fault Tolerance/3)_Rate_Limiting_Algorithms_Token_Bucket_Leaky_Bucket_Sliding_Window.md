# 3) Rate Limiting Algorithms: Token Bucket, Leaky Bucket, Sliding Window

## Executive Overview
Rate limiting controls the rate of traffic sent or received by an API network interface. It defends backends against DDoS attacks, brute-force credential stuffing, API quota abuse, and cascading service starvation.

---

## 1. Comparing the 4 Core Algorithms

```text
┌─────────────────────────┬───────────────────────────────┬─────────────────────────────┐
│ Algorithm               │ Mechanics                     │ Trade-off                   │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 1. Token Bucket         │ Tokens added at constant rate │ Handles bursts well;        │
│    (Nginx / Stripe)     │ up to bucket capacity         │ memory efficient            │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 2. Leaky Bucket         │ Requests enter queue, leak    │ Completely smooths traffic; │
│    (Traffic Shaping)    │ out at constant rate          │ drops bursts if queue full  │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 3. Fixed Window Counter │ Counts requests in fixed time │ 2x burst traffic spike      │
│                         │ blocks (e.g. 12:00 to 12:01)  │ at boundary edges!          │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 4. Sliding Window       │ Weights previous window count │ Memory efficient and bounds │
│    Counter (Cloudflare) │ with current window elapsed % │ burst spikes accurately     │
└─────────────────────────┴───────────────────────────────┴─────────────────────────────┘
```

---

## 2. Deep Dive: The Fixed Window Boundary Flaw

Suppose a rate limit allows **100 requests per minute**:
- A malicious client sends **100 requests at 11:59:59**.
- The window resets at 12:00:00.
- The client sends another **100 requests at 12:00:01**.
- **Result**: The server receives **200 requests within a 2-second window**, double the intended limit!

---

## 3. Sliding Window Counter (Production Standard)

The Sliding Window Counter computes an approximation based on the previous window's request volume:

```text
Previous Window (11:59): 80 requests
Current Window (12:00):  30 requests
Current Time: 12:00:18 (30% elapsed through current minute)

Formula:
Estimated Requests = (Previous Window * (1 - Elapsed %)) + Current Window
Estimated Requests = (80 * (1 - 0.30)) + 30 = (80 * 0.70) + 30 = 56 + 30 = 86 requests!
```
- Since $86 < 100$, the incoming request is allowed.
- It requires storing only two integers per client in Redis (`prev_count` and `curr_count`), achieving 99.9% accuracy with virtually zero memory overhead.

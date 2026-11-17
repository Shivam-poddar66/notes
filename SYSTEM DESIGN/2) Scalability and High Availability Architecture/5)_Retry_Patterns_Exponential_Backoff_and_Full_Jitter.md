# 5) Retry Patterns: Exponential Backoff and Full Jitter

## Executive Overview
When a network call fails due to a transient glitch (e.g., brief packet loss, load balancer blip), retrying immediately is dangerous. If 1,000 clients retry simultaneously at fixed intervals, they create a destructive **Thundering Herd** wave that repeatedly knocks the recovering backend offline.

---

## 1. Comparing Retry Strategies

```text
1. Immediate Retry (Worst):
   Time: 0s [Fail] ──▶ 0.01s [Retry] ──▶ 0.02s [Retry] ──▶ Downstream collapses!

2. Fixed Interval Retry:
   Time: 0s [Fail] ──▶ 2s [Retry] ──▶ 4s [Retry] ──▶ Synchronized waves of load!

3. Exponential Backoff:
   Wait = Base * (2 ^ attempt)
   Attempt 1: 1s, Attempt 2: 2s, Attempt 3: 4s, Attempt 4: 8s, Attempt 5: 16s

4. Exponential Backoff with Full Jitter (AWS Best Practice):
   Wait = random(0, min(MaxWait, Base * (2 ^ attempt)))
   * Spreads client retry attempts across a uniform distribution, smoothing out spikes!
```

---

## 2. AWS Jitter Algorithm Implementations

### Full Jitter Formula:
```python
import random
import time

def retry_with_full_jitter(base_wait=1, max_wait=32, max_retries=5):
    for attempt in range(max_retries):
        try:
            return call_remote_service()
        except TransientNetworkError:
            if attempt == max_retries - 1:
                raise
            
            # Compute exponential backoff ceiling
            temp = min(max_wait, base_wait * (2 ** attempt))
            
            # Pick uniform random sleep between 0 and ceiling
            sleep_duration = random.uniform(0, temp)
            time.sleep(sleep_duration)
```

---

## 3. Idempotency Requirement for Retries
**Golden Rule of Network Retries**: You must **NEVER retry a non-idempotent operation** without an **Idempotency Key**.
- If a `POST /payments/charge` call times out, you do not know if the credit card was charged or not. Retrying blindly risks double-charging the customer!
- Ensure every retry passes the same unique client `Idempotency-Key: UUIDv4`.

# 6) Chapter 2 Summary & Self-Assessment Checklist

## Chapter Summary
1. **Scaling Dimensions**: Horizontal scaling provides elastic, cost-effective resilience, but is limited by the serial, non-parallelizable bottlenecks described by Amdahl's Law.
2. **Stateless Autoscaling**: Decoupling ephemeral user state into Redis clusters and utilizing presigned URLs for object storage enables zero-friction autoscaling.
3. **Availability Realities**: 99.99% availability allows only 52.6 minutes of downtime per year. Error budgets bridge product feature velocity with operational reliability.
4. **Resiliency Patterns**:
   - **Circuit Breaker**: Trips to OPEN on sustained failures, failing fast to allow downstream systems to recover.
   - **Bulkheads**: Partition thread pools and connection pools to eliminate cascading starvation.
   - **Exponential Backoff with Full Jitter**: Randomizes retry intervals, eliminating the thundering herd effect during outages.

---

## 🎯 Chapter 2 Self-Assessment Checklist

- [ ] Can you explain Amdahl's Law and calculate maximum theoretical speedup when 15% of execution is serial?
- [ ] How do you handle session state in a 100-node cluster without using sticky sessions?
- [ ] What is the exact difference between an SLA, an SLO, and an SLI?
- [ ] How many minutes of downtime are permitted per year under a 99.99% (Four Nines) SLA?
- [ ] Walk through the 3 states of a Circuit Breaker (Closed, Open, Half-Open) and explain the transition triggers.
- [ ] How does the Bulkhead pattern prevent a slow third-party API from taking down internal services?
- [ ] Why does pure exponential backoff without jitter still produce thundering herd spikes?
- [ ] What is the mathematical formula for Full Jitter exponential backoff?
- [ ] Why must retried POST requests always include an Idempotency Key?

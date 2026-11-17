# 5) Chapter 1 Summary & Self-Assessment Checklist

## Chapter Summary
In this chapter, we established the fundamental engineering foundations of distributed systems:
1. **Macro-Architecture Choices**: Monoliths provide unmatched simplicity and speed for small teams, whereas microservices solve organizational scaling at the expense of distributed complexity and strict database-per-service requirements.
2. **Performance Physics**: Memory access (100 ns) is orders of magnitude faster than SSD reads (10–50 µs) or cross-region network packets (40–80 ms). Optimizing for tail latencies (p99/p99.9) is essential to combat fan-out amplification in microservices.
3. **Stateless Compute**: Removing sessions, local files, and server-bound cron jobs from application instances is the non-negotiable requirement for elastic autoscaling.
4. **Capacity Planning**: Applying the 80/20 rule, powers of two, and 86,400-second conversions enables rapid, credible back-of-the-envelope estimations under 5 minutes.

---

## 🎯 Chapter 1 Self-Assessment Checklist

Test your mastery before moving to Chapter 2:

- [ ] Can you articulate Conway's Law and explain why splitting a monolith prematurely hurts small teams?
- [ ] What is the Database-Per-Service pattern and what happens when services share tables directly?
- [ ] Recite the approximate latencies for: L1 cache, RAM reference, NVMe sequential read, and NYC-to-SF network round trip.
- [ ] State Little's Law ($L = \lambda \times W$) and calculate the concurrency required for 5,000 QPS with 200 ms latency.
- [ ] Explain why a 99% SLA per service leads to a 63% failure/lag rate when 100 services are called in fan-out.
- [ ] What are the 3 concrete steps to convert a stateful application into a horizontally scalable stateless app?
- [ ] What are the two tradeoffs of using signed JWTs for session management compared to Redis sessions?
- [ ] How many seconds are in one day, and how do you quickly convert 100M daily requests into average QPS?
- [ ] How does the 80/20 rule determine the required RAM size for a distributed Redis cache?

# 5) Chapter 9 Summary & Self-Assessment Checklist

## Chapter Summary
1. **The Three Pillars**:
   - Metrics (Prometheus) aggregate real-time numerical counters, gauges, and histograms.
   - Logs (ELK) record detailed contextual event JSONs.
   - Distributed Traces (OpenTelemetry) correlate end-to-end multi-service request lifetimes.
2. **Context Propagation**: OpenTelemetry injects standard `traceparent` W3C headers across HTTP and Kafka boundaries to connect child spans into an observable trace graph.
3. **Rate Limiting Mechanisms**: The Sliding Window Counter algorithm prevents fixed-window boundary spikes while maintaining near-zero memory footprint.
4. **Resiliency Operations**: Load shedding rejects low-priority traffic (recommendations) to protect core business transactions (checkout). Chaos Engineering proactively proves system fault tolerance in production.

---

## 🎯 Chapter 9 Self-Assessment Checklist

- [ ] Explain the difference between the RED method (Rate, Errors, Duration) and the USE method (Utilization, Saturation, Errors).
- [ ] What is the structural difference between a Metric, a Log, and a Trace?
- [ ] Detail the structure of the W3C `traceparent` header and explain how it propagates across microservices.
- [ ] Why does the Fixed Window Counter rate limiter allow twice the configured rate at window boundaries?
- [ ] Calculate the estimated request count for the Sliding Window Counter algorithm given: previous window = 100, current window = 40, elapsed % = 20%.
- [ ] How does Load Shedding differ from standard Rate Limiting?
- [ ] Describe two Graceful Degradation fallbacks for an e-commerce platform during high traffic bursts.
- [ ] What is Chaos Engineering and why did Netflix introduce Chaos Monkey to production clusters?

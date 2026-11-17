# 1) The Three Pillars of Observability: Metrics, Logs, Traces

## Executive Overview
Observability is the measure of how well internal states of a system can be inferred from knowledge of its external outputs. In distributed microservice architectures, monitoring individual servers via SSH is obsolete; systems require unified **Metrics**, **Logs**, and **Distributed Traces**.

---

## 1. Comparing the Three Pillars

```text
┌─────────────────────────┬───────────────────────────────┬─────────────────────────────┐
│ Pillar                  │ Data Format & Characteristics │ Tooling & Technology        │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 1. Metrics              │ Numeric, aggregated timeseries│ Prometheus, Grafana,        │
│                         │ data (Counters, Gauges, Hist) │ Datadog, InfluxDB           │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 2. Logs                 │ Discrete, structured JSON     │ ELK Stack (Elasticsearch,   │
│                         │ events with contextual info   │ Logstash, Kibana), Loki     │
├─────────────────────────┼───────────────────────────────┼─────────────────────────────┤
│ 3. Distributed Traces   │ Request path representation   │ OpenTelemetry, Jaeger,      │
│                         │ across multi-service hops     │ Zipkin, AWS X-Ray           │
└─────────────────────────┴───────────────────────────────┴─────────────────────────────┘
```

---

## 2. Deep Dive into Metrics Types

1. **Counters**: Monotonically increasing numbers (e.g., `http_requests_total`). Can only increase or reset to zero on reboot.
2. **Gauges**: Numerical values that go up and down (e.g., `memory_usage_bytes`, `active_threads`, `cpu_utilization_percent`).
3. **Histograms**: Samples observations (usually response times or payload sizes) and counts them in configurable buckets to compute percentiles (p50, p95, p99).

### The USE and RED Methods (Industry Best Practices):
- **RED Method (For Request-Driven Services)**:
  - **R**ate: Number of requests per second.
  - **E**rrors: Number of failing requests per second.
  - **D**uration: The amount of time requests take (latencies).
- **USE Method (For Infrastructure Resources - CPU, Memory, Disk)**:
  - **U**tilization: Average time the resource was busy (e.g., 85% CPU).
  - **S**aturation: Extra work queued that cannot be serviced (e.g., CPU run queue length).
  - **E**rrors: Count of error events (e.g., memory ECC errors, disk write failures).

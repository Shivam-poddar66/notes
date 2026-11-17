# 3) High Availability Metrics: SLA, SLO, SLI, and the Rule of Nines

## Executive Overview
High Availability (HA) guarantees that a system remains functional and accessible even in the presence of component, network, or hardware failures. It is quantified using the standard "Rule of Nines."

---

## 1. Calculating Availability and Downtime

$$\text{Availability} = \frac{\text{Total Operational Uptime}}{\text{Total Time}} \times 100\%$$

```text
┌───────────────────────┬────────────────────┬────────────────────┬────────────────────┐
│ Availability ("Nines")│ Downtime per Year  │ Downtime per Month │ Downtime per Day   │
├───────────────────────┼────────────────────┼────────────────────┼────────────────────┤
│ 90% ("One Nine")      │ 36.5 days          │ 73 hours           │ 2.4 hours          │
│ 99% ("Two Nines")     │ 3.65 days          │ 7.31 hours         │ 14.4 minutes       │
│ 99.9% ("Three Nines") │ 8.77 hours         │ 43.8 minutes       │ 1.44 minutes       │
│ 99.99% ("Four Nines") │ 52.6 minutes       │ 4.38 minutes       │ 8.64 seconds       │
│ 99.999% ("Five Nines")│ 5.26 minutes       │ 26.3 seconds       │ 864 milliseconds   │
│ 99.9999% ("Six Nines")│ 31.5 seconds       │ 2.63 seconds       │ 86.4 milliseconds  │
└───────────────────────┴────────────────────┴────────────────────┴────────────────────┘
```

> **Industry Context**: Standard web apps aim for $99.9\%$ (Three Nines). Enterprise SaaS (Stripe, AWS core APIs) targets $99.99\%$ (Four Nines). Telcos and hospital life-support systems target $99.999\%$ (Five Nines).

---

## 2. The Operational Hierarchy: SLI vs. SLO vs. SLA

```text
[ SLI (Indicator) ]  ──▶ Real-time Metric: "p99 latency is 140ms; error rate is 0.01%"
        │
        ▼ (Must meet)
[ SLO (Objective) ]  ──▶ Internal Target: "99.9% of HTTP requests succeed under 200ms"
        │
        ▼ (Protects)
[ SLA (Agreement) ]  ──▶ Legal Contract: "99.5% uptime guaranteed, or 20% billing credit"
```

### Error Budgets:
- An **Error Budget** is the allowable downtime under your SLO.
- If your SLO is $99.9\%$, your error budget for the month is $0.1\%$ (approx. 43.8 minutes of downtime).
- **SRE Principle**: If the error budget is healthy, product teams can ship features aggressively. If the error budget is exhausted, all feature deployments halt, and the engineering team works exclusively on stability and resilience.

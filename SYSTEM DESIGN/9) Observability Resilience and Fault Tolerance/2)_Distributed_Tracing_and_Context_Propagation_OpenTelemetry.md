# 2) Distributed Tracing and Context Propagation (OpenTelemetry)

## Executive Overview
When a single user click triggers a cascade of 20 synchronous and asynchronous microservice calls, standard log files are completely decoupled. **Distributed Tracing** injects a unique trace identity across HTTP headers, gRPC metadata, and Kafka record headers to reconstruct the end-to-end request lifecycle.

---

## 1. Trace, Span, and Context Hierarchy

```text
[ Trace: 4bf92f3577b34da6a3ce929d0e0e4736 ]
│
├── [ Root Span: Gateway HTTP GET /orders ] (Total: 180 ms)
│   │
│   ├── [ Child Span 1: Auth Service JWT Verify ] (10 ms)
│   │
│   ├── [ Child Span 2: Order Service DB Query ] (40 ms)
│   │
│   └── [ Child Span 3: Payment Service RPC ] (120 ms)
│       │
│       └── [ Child Span 3a: Stripe Gateway Call ] (105 ms) ──▶ (IDENTIFIED BOTTLENECK!)
```

### Core Entities:
- **Trace ID**: A globally unique 128-bit ID identifying the overall end-to-end request.
- **Span**: A single contiguous unit of work (e.g., executing a SQL query, calling an RPC endpoint). Contains start timestamp, duration, status, tags, and events.
- **Span ID**: A unique 64-bit ID identifying that specific unit of work.
- **Parent Span ID**: Points to the calling span, forming a directed acyclic graph (DAG) of the execution tree.

---

## 2. W3C Trace Context Header Standard

Distributed tracing propagates across network hops via standard HTTP headers:

```http
traceparent: 00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-01
              │  └──────────────┬──────────────┘ └───────┬──────┘ └┬┘
              │                 │                        │         │
          Version            Trace ID                 Span ID    Flags (01 = Sampled)
```

- When Service A calls Service B over HTTP, gRPC, or Kafka, the OpenTelemetry SDK extracts the `traceparent` header from incoming requests and injects it into all outgoing downstream client calls (**Context Propagation**).
- If an exception occurs in Service C, developers query `trace_id` in Kibana or Jaeger and see the exact causal chain in milliseconds.

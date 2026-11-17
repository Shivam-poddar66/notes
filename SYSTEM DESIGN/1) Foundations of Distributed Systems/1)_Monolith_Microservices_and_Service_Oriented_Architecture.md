# 1) Monolith, Microservices, and Service-Oriented Architecture (SOA)

## Executive Overview
Choosing the right macro-architecture is the foundational architectural decision of any software system. The trade-off is between operational simplicity and autonomous team velocity.

---

## 1. Architectural Styles Comparison

```text
MONOLITHIC ARCHITECTURE:
┌────────────────────────────────────────────────────────┐
│               Single Deployment Unit (Process)          │
│  ┌──────────────┐   ┌──────────────┐   ┌────────────┐  │
│  │ User Service │   │ Order Module │   │ Billing    │  │
│  └──────┬───────┘   └──────┬───────┘   └─────┬──────┘  │
│         │                  │                 │         │
│         ▼                  ▼                 ▼         │
│               In-Memory Method Calls (Fast)            │
├────────────────────────────────────────────────────────┤
│                   Shared Database                      │
└────────────────────────────────────────────────────────┘

MICROSERVICES ARCHITECTURE:
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│  User Service   │       │  Order Service  │       │ Billing Service │
│ (Dedicated DB)  │       │ (Dedicated DB)  │       │ (Dedicated DB)  │
└────────┬────────┘       └────────┬────────┘       └────────┬────────┘
         │                         │                         │
         └───────────── Network RPC / Event Bus ─────────────┘
```

| Dimension | Monolith | Microservices | Service-Oriented Architecture (SOA) |
| :--- | :--- | :--- | :--- |
| **Deployment** | Single artifact (JAR, WAR, binary, container) | Independent artifacts per domain boundary | Large multi-service components |
| **Communication** | In-memory function/method calls (< 1 µs) | Network RPC (gRPC), REST (HTTP), or Event Bus (Kafka) | Enterprise Service Bus (ESB), SOAP/XML |
| **Data Storage** | Single shared database | Database-per-service (strictly enforced) | Shared or service-specific databases |
| **Failure Domain** | Shared: A memory leak or crash kills entire app | Isolated: A crash in recommendations doesn't break payments | Moderately isolated |
| **Operational Overhead** | Minimal: Single CI/CD pipeline, simple logging | Extremely high: Service mesh, distributed tracing, Kubernetes | Heavy: ESB maintenance and XML transformation |
| **Scaling Flexibility** | Scale the whole application uniformly | Scale only CPU/memory intensive bottlenecks | Scale services independently |

---

## 2. Conway's Law & Organizational Coupling
> *"Organizations which design systems are constrained to produce designs which are copies of the communication structures of these organizations."* — Melvin Conway (1967)

- **Small Team (< 15 engineers)**: A monolithic architecture is almost always optimal. Cross-service boundaries introduce overhead, serialization costs, and deployment friction.
- **Large Organization (100+ engineers across multiple squads)**: Monoliths suffer from merge conflicts, locked CI/CD pipelines, and blast-radius anxiety. Microservices allow squads to deploy independently.

---

## 3. When to Choose What: Architectural Decision Rules

```text
                              Start Here
                                  │
                                  ▼
                   Is the domain well-understood
                  and team size < 20 engineers?
                                  │
                   ┌──────────────┴──────────────┐
                  YES                            NO
                   │                              │
                   ▼                              ▼
          [ Build Modular Monolith ]     Does a single feature require
          (Strict module boundaries,      10x different scale / tech
           single deployable process)    (e.g., Python ML vs Go API)?
                                                  │
                                   ┌──────────────┴──────────────┐
                                  NO                            YES
                                   │                              │
                                   ▼                              ▼
                          [ Stick to Monolith ]         [ Microservices with ]
                                                        [ Database-Per-Service ]
```

---

## 4. The Database-Per-Service Rule
In a microservices architecture, **services must NEVER directly read or write to another service's database**. 
- Sharing a database couples internal schemas, breaking independent deployability.
- If Service A needs data from Service B, it must:
  1. Call Service B's public API (REST/gRPC), OR
  2. Subscribe to domain events published by Service B (via Kafka/RabbitMQ) and maintain its own read-optimized projection.

# 2) Writing and Defending RFCs and ADRs

## Executive Overview
In modern engineering organizations, major architectural decisions are rarely decided verbally; they are proposed, debated, and approved through written documents: **RFCs (Requests for Comments)** and **ADRs (Architecture Decision Records)**. Writing a technically brilliant document is only half the battle; defending it against cross-functional skepticism without triggering political friction is the hallmark of Principal and Staff Engineers.

---

## 1. Anatomy of an Unbeatable RFC

```text
  1. METADATA & STATUS (Draft, Under Review, Approved, Superseded)
  2. EXECUTIVE SUMMARY & BLUF (1 Paragraph for Leadership)
  3. PROBLEM STATEMENT & CONTEXT (Current limitations, data metrics)
  4. REQUIREMENTS & NON-REQUIREMENTS (Explicit scope boundaries)
  5. PROPOSED ARCHITECTURE & DATA FLOW (Diagrams & protocol specs)
  6. ALTERNATIVES CONSIDERED & TRADE-OFF MATRIX (Why not Option B/C?)
  7. CROSS-CUTTING CONCERNS (Security, Observability, Cost, Compliance)
  8. FAILURE MODES & ROLLBACK CONTINGENCY (How it fails gracefully)
```

---

## 2. The "Alternatives Considered" Matrix (The Persuasion Weapon)

The fastest way to lose an RFC review is to present only your preferred solution. Stakeholders will immediately wonder if you suffered from confirmation bias. Presenting an objective trade-off matrix neutralizes objections before they are spoken.

```text
| Architecture Dimension | Option A: Monolith Refactor | Option B: Event-Driven Kafka (Proposed) | Option C: Serverless Lambdas |
| :--- | :--- | :--- | :--- |
| **P99 Read Latency** | High (50ms) | Low (< 5ms) | Variable (Cold starts 200ms) |
| **Max Peak Throughput** | Low (3,000 QPS) | Very High (100,000 QPS) | High (Auto-scaled) |
| **Operational Overhead** | Low (Existing team skill) | Moderate (Managed MSK cluster) | Low to Moderate |
| **Monthly Cloud Cost** | $400/mo | $1,200/mo | $800 - $3,500 (Fluctuating) |
| **Time to Ship MVP** | 3 weeks | 6 weeks | 4 weeks |
```

---

## 3. Defending the RFC: The Pre-Mortem & Socializing Strategy

### Strategy 1: The Pre-RFC 1-on-1 Coffee Chats
Never walk into an RFC review meeting where stakeholders are reading your proposal for the first time.
- Share an early draft with the top two most vocal skeptics in your organization 3 days before the meeting.
- Ask: *"I’d love your critical eyes on this storage proposal before I publish it. Where do you think this design will break down?"*
- Incorporate their feedback; they will enter the review meeting as allies rather than adversaries.

### Strategy 2: Welcoming Critique Without Emotion
- When a senior engineer attacks your design:
  - *Do NOT say*: *"You clearly didn't read section 4 where I addressed that."* (Defensive, sparks ego battles).
  - *DO say*: *"That is a very sharp callout on memory pressure during partition rebalancing. Here is how we benchmarked it, but let's explore if we need an explicit circuit breaker there."* (Collaborative, intellectual humility).

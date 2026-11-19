# 3) Engineering Roadmap Prioritization: RICE and MoSCoW

## Executive Overview
Engineering leaders operate under permanent scarcity: there are always 50 features, 30 refactoring tasks, and 20 bug fixes competing for 10 engineers' time. Prioritizing via "whoever shouts the loudest" or executive whim creates a fragmented, low-impact codebase. High-performing leaders use quantitative decision frameworks like **RICE Scoring** and **MoSCoW Prioritization** to bring objective discipline to roadmap planning.

---

## 1. The RICE Scoring Framework

Developed by Intercom, RICE evaluates competing initiatives across four distinct variables:

$$\text{RICE Score} = \frac{\text{Reach} \times \text{Impact} \times \text{Confidence}}{\text{Effort}}$$

```text
  1. REACH (How many users impacted per quarter?)
     • Example: 50,000 active checkout users.

  2. IMPACT (How much will it increase customer value?)
     • 3 = Massive  |  2 = High  |  1 = Medium  |  0.5 = Low  |  0.25 = Minimal

  3. CONFIDENCE (How certain are we about our estimates?)
     • 100% = High (validated by data/prototypes)
     • 80%  = Medium (user interviews/benchmarks)
     • 50%  = Low (gut feel / speculation)

  4. EFFORT (How many person-months will it require?)
     • Example: 2 engineers for 1.5 months = 3 person-months.
```

### RICE Calculation Example
- **Project Alpha (Kafka Event Streaming)**:
  - $\text{Reach} = 100{,}000\text{ users}$
  - $\text{Impact} = 3\text{ (eliminates checkout crashes)}$
  - $\text{Confidence} = 80\%$
  - $\text{Effort} = 4\text{ person-months}$
  - $\text{RICE Score} = \frac{100{,}000 \times 3 \times 0.8}{4} = \mathbf{60{,}000}$
- **Project Beta (Internal Admin Portal Reskin)**:
  - $\text{Reach} = 200\text{ internal staff}$
  - $\text{Impact} = 1$
  - $\text{Confidence} = 50\%$
  - $\text{Effort} = 2\text{ person-months}$
  - $\text{RICE Score} = \frac{200 \times 1 \times 0.5}{2} = \mathbf{50}$
- *Conclusion*: Project Alpha delivers 1,200x more leverage per engineering unit of effort.

---

## 2. The MoSCoW Framework (Release Gating)

When scoping a sprint, quarterly release, or MVP:
- **Must Have (M)**: Non-negotiable requirements. If missing, the release is canceled or legally non-compliant (e.g., PCI-DSS encryption).
- **Should Have (S)**: High value, but a manual workaround exists for launch.
- **Could Have (C)**: Desirable nice-to-haves if time permits; first to be cut when deadlines compress.
- **Won't Have (W)**: Explicitly agreed out-of-scope for this release to protect team focus.

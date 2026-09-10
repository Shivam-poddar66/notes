# Phase 3 Study Plan: Asynchronous JavaScript Mastery (3–4 Weeks)

A rigorous, structured roadmap engineered to take you from core event loop internals and Promise microtask semantics to distributed network reliability patterns, HTTP protocol fundamentals, and production architectural design.

---

## 1. Study Plan Architecture & Pedagogical Philosophy

```
+=================================================================================================+
| WEEK 1: Foundations, Event Loop Internals & Async Execution Flow                                |
|  - Sync vs Async, Web/Node APIs, Macrotasks vs Microtasks, Promises Spec, Async/Await           |
+=================================================================================================+
                                                |
                                                v
+=================================================================================================+
| WEEK 2: Concurrency Combinators, Flow Control & Advanced Orchestration                          |
|  - Promise.all / allSettled / race / any, Sequential vs Parallel, Concurrency Limiting (p-limit)|
+=================================================================================================+
                                                |
                                                v
+=================================================================================================+
| WEEK 3: Networking, HTTP Protocol, Fetch API & Resilient Boundaries                             |
|  - End-to-End Lifecycle, HTTP Verbs & Statuses, ETag Caching, JSON Boundaries & Safe Parsers    |
+=================================================================================================+
                                                |
                                                v
+=================================================================================================+
| WEEK 4: Hardening, Cancellation, Timeout Patterns & Production Mini-Projects                    |
|  - Exponential Backoff & Jitter, AbortController, Weather App, GitHub Finder, Promise Toolkit   |
+=================================================================================================+
```

---

## 2. Track A: Standard Track (4 Weeks / 20–25 Hours per Week)

### Week 1: Event Loop Internals & The Promise Foundation
* **Goal**: Master the single-threaded execution model, Call Stack, Event Loop phases, and Promise resolution mechanics.

| Day | Topics & Reading | Hands-On Coding & Drills |
| :--- | :--- | :--- |
| **Day 1** | Files 1 & 2: Sync vs Async, Web APIs vs Node.js APIs (libuv) | Trace Call Stack $\to$ Web API handoff using Node.js `--trace-event-categories`. |
| **Day 2** | Files 3 & 4: Event Loop Mental Model, Macrotasks vs Microtasks | Solve 10 tricky Event Loop execution order prediction challenges. |
| **Day 3** | Files 5 & 6: Callbacks, Inversion of Control & Refactoring | Refactor pyramid of doom callbacks into named functions and early-returns. |
| **Day 4** | File 7: Promises States, Resolution, and Settlement | Write a minimal A+ compliant `MyPromise` implementation from scratch. |
| **Day 5** | File 8: Promise Chaining & Error Propagation | Practice error bubbling down `.then().then().catch()` chains. |
| **Day 6** | File 9: `async/await` Control Flow & Error Handling | Deconstruct `async/await` into generator functions + promise yield runners. |
| **Day 7** | **Week 1 Review & Output Prediction Drills** | 90-minute timed mock interview quiz on microtask starvation & event loop ticks. |

---

### Week 2: Concurrency Combinators, Batching & Flow Control
* **Goal**: Master parallel vs sequential execution pipelines and build robust concurrency limits.

| Day | Topics & Reading | Hands-On Coding & Drills |
| :--- | :--- | :--- |
| **Day 8** | File 10: `Promise.all`, `allSettled`, `race`, `any` Comparison | Write complete, spec-compliant polyfills for all 4 combinators. |
| **Day 9** | File 11: Sequential vs Parallel Async Flows | Benchmark `for...of` vs `Promise.all` vs `Array.reduce` pipelines. |
| **Day 10**| File 12: Concurrency Limits & Batch Processing | Implement a sliding-window worker pool (`p-map`) preserving array order. |
| **Day 11**| File 12 (Advanced): Async Semaphores (`p-limit`) | Build an async semaphore with `.activeCount` and queue draining. |
| **Day 12**| Concurrency Hardening Drills | Build an async Directed Acyclic Graph (DAG) task runner engine. |
| **Day 13**| **Milestone Project**: Mini Project 3 (`Promise Utility Library`) | Implement and test `delay`, `timeout`, `retry`, and `settleMap`. |
| **Day 14**| **Week 2 Review & Code Audit** | Verify zero-leak timer disposal on early settlements in promise utilities. |

---

### Week 3: HTTP Protocol, Fetch API & Safe Data Handling
* **Goal**: Understand the complete physical network lifecycle, HTTP contracts, and untrusted JSON parsing boundaries.

| Day | Topics & Reading | Hands-On Coding & Drills |
| :--- | :--- | :--- |
| **Day 15**| File 13: `fetch` API Basics, Options, & Stream Readers | Build a reusable `httpClient` wrapper with automatic header formatting. |
| **Day 16**| File 14: Request/Response Lifecycle End to End | Profile DNS, TCP, TLS, and TTFB latency using `performance.getEntriesByName()`. |
| **Day 17**| File 15: HTTP Methods, Status Codes, & Headers Matrix | Build an HTTP status-code branching router handling 204, 401, 403, 404, 429. |
| **Day 18**| File 15 (Advanced): Browser & CDN Caching (`ETag`, SWR) | Implement a client-side `StaleWhileRevalidate` in-memory cache layer. |
| **Day 19**| File 16: JSON Parsing, Validation & Safe Data Handling | Build a schema validator and protect merge utilities from Prototype Pollution. |
| **Day 20**| File 16 (Advanced): DTO Normalizers & Nullish Safety | Transform messy external third-party API payloads into clean UI domain models. |
| **Day 21**| **Week 3 Review & Network Diagnostics** | Inspect Chrome DevTools Network Tab waterfalls and simulated offline throttles. |

---

### Week 4: Retries, Cancellation, Debugging & Production Projects
* **Goal**: Master system resilience, cancellation mechanics, anti-pattern debugging, and complete 2 end-to-end applications.

| Day | Topics & Reading | Hands-On Coding & Drills |
| :--- | :--- | :--- |
| **Day 22**| File 17: Retries, Exponential Backoff & Idempotency Keys | Implement backoff with Full Jitter and `Retry-After` header extraction. |
| **Day 23**| File 18: `AbortController`, `AbortSignal.timeout` & `.any` | Build an auto-cancelling Typeahead autocomplete search input. |
| **Day 24**| **Mini Project 1**: Weather App Using Public API | Complete multi-state Weather App with geocoding, retries, and unit toggling. |
| **Day 25**| **Mini Project 2**: GitHub User Finder with State Machine | Build OctoFinder with FSM (`IDLE`, `LOADING`, `SUCCESS`, `ERROR`) and debouncing. |
| **Day 26**| File 22: Async Debugging Checklist & Anti-Patterns | Audit legacy codebases for 10 common async bugs (forEach traps, floating promises). |
| **Day 27**| Mock Technical Interviews | Live coding drills: Async Queue, Timeout Race, Interceptor Pipeline. |
| **Day 28**| **Phase 3 Final Milestone Review** | Review `24)_Quick_revision_sheet.md` and verify readiness for Phase 4. |

---

## 3. Track B: Fast-Track (3 Weeks / Intensive 30–35 Hours per Week)

For senior engineers or candidates preparing for immediate technical interviews:

```
WEEK 1 (Days 1–7): Deep Core Async & Concurrency
  - Files 1 to 12.
  - Implement Promise Combinator Polyfills + Sliding Window Worker Pool.
  - Build Mini Project 3 (Promise Utility Library).

WEEK 2 (Days 8–14): Networking, HTTP, Boundaries & Resilience
  - Files 13 to 18.
  - Master AbortController cancellation + Exponential Backoff with Jitter.
  - Build Mini Project 1 (Weather App).

WEEK 3 (Days 15–21): State Machines, Debugging & Interview Drills
  - Mini Project 2 (GitHub Finder with Finite State Machine).
  - File 22 (Anti-Pattern Encyclopedia) + File 24 (Quick Revision).
  - Timed LeetCode-style Async Questions & Architectural System Design.
```

---

## 4. Daily Study Template (2–3 Hours / Day)

```
+-------------------------------------------------------------------------+
| STEP 1: Conceptual Study & Spec Review (45 Mins)                        |
|  - Read corresponding markdown notes and analyze ASCII memory models.   |
|  - Review ECMAScript / WHATWG specification rules.                      |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
| STEP 2: Hands-On Implementation from Scratch (60 Mins)                  |
|  - Write zero-dependency implementations (no libraries allowed).        |
|  - Write manual unit tests verifying edge cases.                        |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
| STEP 3: Debugging & Network Profiling (30 Mins)                         |
|  - Step through asynchronous execution in Chrome DevTools / Node Debug. |
|  - Test failure modes: network drops, 500 crashes, and race conditions. |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
| STEP 4: Spaced Repetition & Flashcard Synthesis (15 Mins)               |
|  - Add key concepts to personal flashcards (Anki / Obsidian).           |
+-------------------------------------------------------------------------+
```

---

## 5. Phase 3 Exit Gate Criteria (Self-Assessment Checklist)

You are ready to advance to **Phase 4: Object-Oriented & Design Patterns** only when you can pass all 6 criteria without referencing documentation:

- [ ] **1. Event Loop Fluency**: Can trace and explain the exact console output of any code combining `setTimeout`, `setImmediate`, `process.nextTick`, `Promise.then`, and `async/await`.
- [ ] **2. Polyfill Mastery**: Can code clean polyfills for `Promise.all`, `Promise.allSettled`, `Promise.race`, and `Promise.any` in under 15 minutes.
- [ ] **3. Concurrency Limiting**: Can build a worker pool or semaphore (`pLimit`) from memory.
- [ ] **4. Resilient Fetch Architecture**: Automatically equips network requests with `res.ok` status verification, `AbortSignal.timeout`, and exponential backoff with full jitter.
- [ ] **5. Race Condition Elimination**: Can prevent out-of-order UI state overwrites using `AbortController` in search/autocomplete workflows.
- [ ] **6. Production Projects Portfolio**: Have completely written, tested, and reviewed:
  1. Weather App with Geocoding and 4-State UI.
  2. GitHub User Finder with Finite State Machine.
  3. Promise Utility Library (`async-toolkit`).

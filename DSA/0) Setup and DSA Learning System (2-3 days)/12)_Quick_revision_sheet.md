# Quick Revision Sheet (JavaScript Focus)

Last updated: February 26, 2026  
Phase: Setup and DSA Learning System (Phase 0)

## Phase 0 Snapshot
- Primary language: JavaScript (for interviews/product roles).
- Goal: solve medium problems clearly, correctly, and within time.
- Duration: 2 to 3 days setup + ongoing weekly review.

## JavaScript DSA Non-Negotiables
- Use one Node.js template for all problems.
- Keep reusable snippets: BFS, DFS, binary search, prefix sums, heap.
- Use `Map`/`Set` over plain objects when key behavior matters.
- Track mistakes and schedule re-solves (D+1, D+3, D+7, D+30).

## JS Pitfalls to Remember
- Number precision: `Number` is safe only up to `2^53 - 1`.
- Sort bug: always pass comparator (`arr.sort((a, b) => a - b)`).
- String/array immutability and accidental copies can hurt performance.
- Recursion depth can fail on deep trees/graphs; prefer iterative when needed.

## Interview Timing Targets
- Easy: 10 to 15 minutes.
- Medium: 25 to 40 minutes.
- Hard: only after medium consistency is stable.

## Daily Session Skeleton
1. 10 to 15 min: review yesterday's mistakes.
2. 60 to 120 min: timed solving.
3. 20 to 30 min: postmortem and note updates.

## Pre-Submit Mini Checklist
- [ ] Complexity matches constraints.
- [ ] Empty/single/duplicate-heavy cases tested.
- [ ] Comparator, indexing, and boundaries verified.
- [ ] No leftover debug logs.

## Recovery Rule
If performance drops for 2 weeks:
- Reduce new problem count.
- Re-solve weak tagged problems.
- Re-focus on accuracy before speed.

## Phase 0 Exit Snapshot
- [ ] Node.js environment and template verified.
- [ ] Notes + mistake log active.
- [ ] Dashboard has baseline metrics.
- [ ] First weekly review completed.

# Problem Solving Framework and Checklist

Last updated: February 26, 2026  
Phase: Setup and DSA Learning System (Phase 0)

## Goal
Follow one consistent method from first read to final submission.

## Standard Framework
1. Understand: restate problem, inputs, outputs, and constraints.
2. Model: identify possible patterns and data structure choices.
3. Brute-force first: ensure correctness before optimization.
4. Optimize: reduce time/space with clear reason.
5. Prove: explain why optimized logic works.
6. Implement cleanly.
7. Test with targeted edge cases.
8. Reflect and log mistakes.

## Constraint-Based Pattern Hints
- `n <= 20`: brute force/backtracking might be acceptable.
- `n <= 10^5`: expect O(n) or O(n log n).
- Repeated range queries: prefix/suffix, Fenwick, segment tree candidates.
- Shortest path in unweighted graph: BFS.
- Exact state decisions over sequence: DP likely.

## Pre-Coding Checklist
- [ ] Do I know the invariant/state definition?
- [ ] Do I know complexity target from constraints?
- [ ] Did I write at least one failing edge case?
- [ ] Did I compare brute-force and optimized versions?

## Pre-Submit Checklist
- [ ] Empty input or minimal size works.
- [ ] Single element and duplicate-heavy cases checked.
- [ ] Large values and overflow-prone expressions handled.
- [ ] No debug prints left.
- [ ] Complexity claim matches real code.

## Post-Submit Reflection
- What triggered the key insight?
- Where did I waste time?
- Which checklist item would have prevented that?

# Quick Revision Sheet (Phase 1)

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Must-Remember Complexity Order
`O(1) < O(log n) < O(n) < O(n log n) < O(n^2) < O(2^n) < O(n!)`

## Constraint-to-Complexity Heuristic
- `n <= 20`: exponential might pass.
- `n <= 10^3`: often up to `O(n^2)`.
- `n <= 10^5`: target `O(n log n)` or `O(n)`.

## Master Theorem Snapshot
For `T(n) = aT(n/b) + f(n)` compare with `n^(log_b a)`:
- Smaller `f(n)` -> `Theta(n^(log_b a))`
- Equal-scale `f(n)` -> multiply by extra `log n`
- Larger `f(n)` with regularity -> `Theta(f(n))`

## Invariant Proof Mini-Template
1. Initialization.
2. Maintenance.
3. Termination implies answer correctness.

## Brute-Force to Optimized Steps
1. Write correct brute force.
2. Find bottleneck.
3. Remove repeated work.
4. Re-check complexity.
5. Re-prove correctness.

## Pre-Submit 30-Second Checklist
- [ ] Complexity fits constraints.
- [ ] Boundary cases tested.
- [ ] Invariant still valid after all updates.
- [ ] Hidden linear operations avoided in loops.
- [ ] No debug logs left.

## JavaScript Quick Cautions
- Numeric sort needs comparator.
- `shift/unshift` are linear.
- Watch precision near `2^53 - 1`.
- Prefer iterative DFS/BFS when recursion depth is risky.

## Phase 1 Exit Snapshot
- [ ] Can estimate feasible complexity instantly.
- [ ] Can justify optimized approach from brute force.
- [ ] Can provide correctness argument, not just intuition.
- [ ] Can debug with checklist instead of random trial.

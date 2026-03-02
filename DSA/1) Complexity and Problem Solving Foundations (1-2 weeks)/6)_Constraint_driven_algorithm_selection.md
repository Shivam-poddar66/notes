# Constraint Driven Algorithm Selection

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Select the right algorithm directly from constraints and problem shape.

## First 60 Seconds After Reading Problem
1. Extract key limits: `n`, `m`, value range, query count, memory limit.
2. Compute rough complexity budget.
3. Identify if preprocessing or multiple queries are involved.
4. Choose candidate patterns that fit the budget.

## Complexity Budget Guide
- `n <= 20`: exponential/backtracking can be valid.
- `n <= 10^3`: `O(n^2)` may pass, `O(n^3)` often risky.
- `n <= 10^5`: target `O(n log n)` or `O(n)`.
- `n >= 10^6`: prefer near-linear and tight constants.

## Constraint-to-Pattern Mapping
- Small search space: brute force + pruning.
- Large linear array: two pointers, prefix sums, hash maps.
- Sorted data or monotonic answer: binary search.
- Repeated range queries: preprocessing structures.
- Graph with unit edge weight: BFS.
- DAG/subproblem overlap: dynamic programming.

## Decision Matrix
Evaluate each candidate on:
- Time fit.
- Space fit.
- Implementation complexity.
- Bug risk under interview time.

Choose the simplest approach that comfortably fits limits.

## JavaScript-Specific Constraint Notes
- For very tight limits, avoid heavy object nesting in hot loops.
- Prefer iterative approaches when recursion depth can exceed call stack.
- Use typed arrays for dense numeric operations when needed.

## Red Flag Conditions
- Your approach is above target complexity by one class (example: `O(n^2)` for `n=10^5`).
- You need expensive per-query recomputation with high query count.
- Memory footprint grows with state combinations beyond practical limit.

## Selection Checklist Before Coding
- [ ] I can state target complexity from constraints.
- [ ] I have at least two candidate approaches.
- [ ] I chose one with acceptable risk and complexity.
- [ ] I can explain why rejected alternatives are weaker here.

## Mastery Checklist
- [ ] I can classify feasible complexity in under 1 minute.
- [ ] I can map constraints to 2 to 3 candidate strategies fast.
- [ ] I avoid overengineering when a simpler pattern passes.
- [ ] I adapt quickly when constraints are modified.

## Practice Targets
- 20 problems solved with a written "constraint-to-strategy" note before coding.
- Weekly drill: read 15 problems and only choose algorithm class without coding.

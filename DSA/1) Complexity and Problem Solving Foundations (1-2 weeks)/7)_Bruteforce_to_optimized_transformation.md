# Bruteforce to Optimized Transformation

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Develop a repeatable method to convert a correct brute-force idea into an efficient solution.

## Why Start with Brute Force
- It clarifies correctness and edge cases quickly.
- It gives a baseline complexity to improve.
- It prevents random optimization attempts with unclear logic.

## Transformation Pipeline
1. Write brute-force logic and exact complexity.
2. Identify bottleneck operation.
3. Ask if repeated work can be cached/preprocessed.
4. Replace costly operation with a better data structure or technique.
5. Re-evaluate complexity.
6. Re-prove correctness after optimization.

## Common Optimization Levers
- Repeated range computation -> prefix/suffix preprocessing.
- Nested lookup -> hash map or set.
- Repeated min/max in window -> deque or heap.
- Sorting to avoid pairwise comparisons.
- Monotonic property -> binary search on answer.
- Overlapping subproblems -> DP memoization/tabulation.

## Bottleneck Identification Questions
- Which loop dominates runtime?
- Which expression is recalculated many times?
- Which operation is not constant-time as assumed?
- Can I process incrementally instead of recomputing from scratch?

## Example Transformation Patterns
- `O(n^2)` subarray sum checks -> prefix sums for `O(1)` range sum query.
- Pair search in unsorted array: double loop -> hash map in one pass.
- Repeated sorting inside loop -> one global sort + linear scan logic.

## Correctness Guardrails After Optimization
- Check that optimization did not change semantics.
- Validate all edge cases handled by brute force still pass.
- Compare optimized output against brute-force on random small tests.

## Interview Communication Format
- "Baseline is `O(...)` due to this bottleneck."
- "I remove repeated work using X."
- "New complexity becomes `O(...)` with `O(...)` extra space."
- "Correctness is preserved because invariant Y stays true."

## Common Mistakes
- Optimizing before fully understanding brute force.
- Using advanced structures where simple preprocessing is enough.
- Claiming optimized complexity but keeping hidden expensive operations.
- Losing correctness while reducing complexity.

## Mastery Checklist
- [ ] I can identify bottleneck in under 2 minutes.
- [ ] I can generate at least 2 optimization directions.
- [ ] I can verify optimized output against brute-force baseline.
- [ ] I can explain transformation in a clear sequence.

## Practice Targets
- 15 problems solved with both brute-force and optimized versions.
- At least 5 random-test comparisons between both versions.

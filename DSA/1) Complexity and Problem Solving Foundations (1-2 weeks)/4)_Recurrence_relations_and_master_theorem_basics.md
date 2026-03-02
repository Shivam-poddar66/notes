# Recurrence Relations and Master Theorem Basics

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Analyze divide-and-conquer runtime rigorously instead of guessing.

## Recurrence Fundamentals
A recurrence expresses runtime in terms of smaller subproblems.

Common form:
- `T(n) = aT(n/b) + f(n)`
- `a`: number of subproblems.
- `n/b`: size of each subproblem.
- `f(n)`: combine/split overhead.

## Standard Solving Techniques
- Substitution method: guess and prove by induction.
- Recursion tree: expand levels and sum level cost.
- Master theorem: direct classification for the standard form.

## Master Theorem (Classic Form)
For `T(n) = aT(n/b) + f(n)`, compare `f(n)` with `n^(log_b a)`.

### Case 1
- If `f(n)` is polynomially smaller than `n^(log_b a)`, then:
- `T(n) = Theta(n^(log_b a))`.

### Case 2
- If `f(n) = Theta(n^(log_b a) * log^k n)`, then:
- `T(n) = Theta(n^(log_b a) * log^(k+1) n)`.

### Case 3
- If `f(n)` is polynomially larger than `n^(log_b a)` and regularity holds, then:
- `T(n) = Theta(f(n))`.

## High-Value Examples
- Merge sort: `T(n)=2T(n/2)+O(n)` -> `Theta(n log n)`.
- Binary search: `T(n)=T(n/2)+O(1)` -> `Theta(log n)`.
- Strassen-like style recurrence examples for practice with non-trivial exponents.

## When Master Theorem Does Not Apply
- Unequal subproblem sizes (example: `T(n)=T(n/3)+T(2n/3)+n`).
- Non-polynomial `f(n)` forms that violate conditions.
- Recurrences with additive shifts that break standard structure.

## Interview Workflow
1. Write recurrence from algorithm steps.
2. Identify `a`, `b`, and `f(n)`.
3. Compute `n^(log_b a)`.
4. Compare growth with `f(n)`.
5. State theorem case and final bound.

## Common Mistakes
- Wrongly identifying `a` or `b`.
- Forgetting combine step in `f(n)`.
- Applying Master theorem where prerequisites fail.
- Writing only final complexity without showing comparison step.

## Mastery Checklist
- [ ] I can derive recurrence from recursive code quickly.
- [ ] I can solve core recurrences with recursion tree and Master theorem.
- [ ] I can detect non-applicable cases and switch methods.
- [ ] I can explain each step clearly in interviews.

## Practice Targets
- 15 recurrence drills: 10 directly solvable by Master theorem, 5 by tree/substitution.
- 3 written derivations from real algorithms (merge sort, quicksort average intuition, segment tree build).

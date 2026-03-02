# Big O, Big Theta, and Big Omega

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Build precise complexity intuition so you can choose and defend algorithms under interview time pressure.

## Core Definitions
- `O(g(n))`: asymptotic upper bound. Runtime does not grow faster than `g(n)` up to constant factors.
- `Omega(g(n))`: asymptotic lower bound. Runtime grows at least as fast as `g(n)` up to constants.
- `Theta(g(n))`: tight bound. Runtime is both `O(g(n))` and `Omega(g(n))`.

Formal intuition:
- `f(n) in O(g(n))` if there exist constants `c > 0` and `n0` such that `f(n) <= c * g(n)` for all `n >= n0`.

## Growth Order You Must Memorize
From faster to slower:
- `O(1)`
- `O(log n)`
- `O(n)`
- `O(n log n)`
- `O(n^2)`
- `O(n^3)`
- `O(2^n)`
- `O(n!)`

## Dominance Rules
- Drop constants: `O(3n)` becomes `O(n)`.
- Drop lower-order terms: `O(n^2 + n)` becomes `O(n^2)`.
- Nested loops multiply, sequential blocks add.
- Binary search loop typically gives `O(log n)`.

## Common Pattern Complexity
- Single linear pass: `O(n)`.
- Two nested full passes: `O(n^2)`.
- Divide by 2 each step: `O(log n)`.
- Merge sort style recurrence: `O(n log n)`.
- Subset generation: `O(2^n)`.

## JavaScript Cost Notes (Interview-Relevant)
- `arr.push()` and `arr.pop()` are amortized `O(1)`.
- `arr.shift()` and `arr.unshift()` are `O(n)`.
- `Map` and `Set` operations are average `O(1)`, worst-case may degrade.
- `Array.sort()` is typically `O(n log n)` and requires a comparator for numeric sort.

## Complexity Analysis Workflow (Use Every Problem)
1. Identify input variables (`n`, `m`, value range, query count).
2. Count major operations in terms of those variables.
3. Derive time complexity.
4. Derive extra space complexity.
5. Check if complexity fits constraints.

## Interview Explanation Template
- "Input size is up to `n = ...`, so I should target around `O(n)` or `O(n log n)`."
- "Current approach does one pass and constant work per element, so time is `O(n)`."
- "I use an additional hash map of size at most `n`, so extra space is `O(n)`."

## High-Frequency Mistakes
- Mixing average-case and worst-case without stating it.
- Claiming `O(log n)` just because recursion exists.
- Forgetting hidden operations like string concatenation in loops.
- Ignoring complexity of library calls.

## Mastery Checklist
- [ ] I can analyze loops, recursion, and mixed structures accurately.
- [ ] I can explain both time and space complexity in one clear sentence.
- [ ] I can justify whether a solution is feasible for given constraints.
- [ ] I can catch wrong complexity claims in my own code reviews.

## Practice Targets
- 15 to 20 problems where the main task is complexity identification.
- 5 editorial reads focused only on "why this complexity is correct."
- 1 self-test: analyze 10 snippets and write complexity within 20 minutes.

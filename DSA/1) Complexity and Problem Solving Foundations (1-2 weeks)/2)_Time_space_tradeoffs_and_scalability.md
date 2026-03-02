# Time Space Tradeoffs and Scalability

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Learn to choose algorithms by balancing runtime, memory, and implementation risk under constraints.

## What Tradeoff Means
- You often spend extra memory to reduce time.
- You may accept slightly slower runtime to keep memory bounded.
- Best solution depends on problem constraints, not only asymptotic beauty.

## Classic Tradeoff Examples
- Hash map lookup (`O(1)` average time, `O(n)` extra space) vs sorting + binary search (`O(n log n)` time, less extra space).
- Prefix sums (`O(n)` preprocess + `O(1)` query) vs recomputing range sums (`O(k)` each query).
- Memoization (faster repeated queries, extra memory) vs recomputation (less memory, slower).

## Scalability Estimation (Back-of-Envelope)
Use rough checks before coding:
- If `n = 10^5`, `O(n^2)` is usually too slow.
- If `n = 10^6`, prefer `O(n)` or better.
- If memory limit is 256MB, avoid large multi-dimensional arrays unless required.

## Memory Budget Quick Rules
- `int`-like number array of size `n`: about `4n` bytes in low-level languages.
- In JavaScript, object-heavy structures can cost much more than raw arrays.
- Storing full paths/states for each node can dominate memory even when time looks good.

## Decision Framework
1. Read constraints and target complexity budget.
2. List 2 to 3 viable approaches.
3. Estimate time and extra space for each.
4. Eliminate approaches violating limits.
5. Choose the simplest approach that passes limits robustly.

## When to Favor Time
- Online assessments with strict runtime.
- High query count after one-time preprocessing.
- Repeated operations over large datasets.

## When to Favor Space
- Tight memory limit.
- Input already close to memory limit.
- Approach complexity increase from extra structures is not worth marginal speed gain.

## JavaScript-Specific Notes
- Prefer arrays for contiguous numeric data where possible.
- Avoid deep nested objects for heavy loops.
- Reuse buffers and avoid unnecessary copying in hot paths.
- Watch out for `slice` and spread operator use inside loops.

## Mistakes to Avoid
- Optimizing for average-case without checking worst-case constraints.
- Using hash maps by default even when sorting is cleaner and enough.
- Ignoring constant factors in interview-sized inputs.
- Choosing complicated structures with high bug risk when a simpler one passes.

## Mastery Checklist
- [ ] I can estimate if an approach will time out before coding.
- [ ] I can explain why I chose memory-heavy or memory-light design.
- [ ] I can give at least one alternative approach and compare tradeoffs.
- [ ] I can adapt my approach if constraints change.

## Practice Targets
- 10 problems focused on preprocess-vs-query tradeoffs.
- 10 problems where both hash and sort-based solutions exist.
- Weekly exercise: compare two accepted solutions and write tradeoff notes.

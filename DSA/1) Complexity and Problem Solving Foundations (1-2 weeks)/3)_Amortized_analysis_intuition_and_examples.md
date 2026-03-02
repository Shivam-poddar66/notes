# Amortized Analysis Intuition and Examples

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Understand why occasional expensive operations can still lead to low average cost per operation.

## Worst-Case vs Amortized
- Worst-case: maximum cost of a single operation.
- Amortized: average cost per operation over a valid sequence of operations.
- Amortized is not average over random inputs. It is a guaranteed bound over sequences.

## Three Standard Methods
### Aggregate Method
- Sum total cost of `n` operations.
- Divide by `n` to get amortized cost.

### Accounting Method
- Charge each operation a fixed "amortized fee."
- Save surplus "credit" to pay for future expensive operations.

### Potential Method
- Define a potential function `Phi(state) >= 0`.
- Amortized cost = actual cost + change in potential.

## Canonical Examples
### Dynamic Array Append
- Most appends are cheap, resizing is expensive.
- Total cost of `n` appends is `O(n)`.
- Amortized append cost is `O(1)`.

### Stack with MultiPop
- `push` and `pop` look simple, `multipop(k)` may be large.
- Across sequence, each element can be popped only once.
- Total for many operations is linear in pushes, so amortized `O(1)` per op.

### Hash Map Rehashing
- Occasional rehash is expensive.
- Rehashes happen infrequently as size grows.
- Average insert remains near `O(1)` in well-behaved hashing.

## JavaScript Practical Notes
- `push/pop` on arrays are typically amortized `O(1)`.
- `shift/unshift` are `O(n)`, avoid them in queue-heavy logic.
- For queues, use index-pointer technique or deque implementation.

## When Interviewers Expect Amortized Reasoning
- Dynamic arrays, vector-like growth behavior.
- Monotonic stack algorithms where each element is pushed/popped limited times.
- Sliding window structures with pointer movement where each pointer only moves forward.

## Explanation Template
- "Although one operation may cost `O(n)`, each element contributes to that expensive step a bounded number of times."
- "Across `m` operations, total work is `O(m)`, so amortized cost per operation is `O(1)`."

## Common Mistakes
- Claiming amortized complexity without sequence-level proof.
- Confusing amortized with expected complexity.
- Ignoring data structure operations that are actually linear (`shift`, list insertion in middle).

## Mastery Checklist
- [ ] I can prove amortized bounds with aggregate reasoning.
- [ ] I can explain why expensive events are infrequent.
- [ ] I can identify operations that are amortized, not worst-case constant.
- [ ] I can use amortized arguments in interviews clearly.

## Practice Targets
- 8 to 12 problems with monotonic stacks, sliding windows, or dynamic arrays.
- 1 written proof each using aggregate, accounting, and potential methods.

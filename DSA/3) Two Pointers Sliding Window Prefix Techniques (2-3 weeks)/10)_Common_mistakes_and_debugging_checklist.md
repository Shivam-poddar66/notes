# Common Mistakes and Debugging Checklist

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Prevent repeated implementation failures in two pointers, sliding window, prefix sum, difference array, modulo-prefix, and monotonic deque problems.

## Use This File in Two Moments
- Before coding: run the pre-coding checklist.
- After wrong answer/TLE: run the debug workflow and pattern-specific checks.

## Pre-Coding Checklist (60 Seconds)
- [ ] What is my invariant?
- [ ] What moves each pointer/index and why?
- [ ] Is boundary convention inclusive or exclusive?
- [ ] Am I solving count, existence, minimum, or maximum variant?
- [ ] What is the time complexity target?

## Global Mistakes Across Phase 3
- No explicit invariant before coding.
- Off-by-one on window bounds (`right - left + 1`).
- Updating answer at wrong time (before restoring validity).
- Mixing 0-based and 1-based indexing.
- Ignoring edge cases (`n=0`, `n=1`, `k=0`, `k>n`).
- Incorrectly assuming non-negative numbers for sum-window problems.

## Pattern-Specific Mistakes
## 1) Two Pointers (Same/Opposite)
- Moving both pointers without logical justification.
- Not handling duplicates when unique combinations are needed.
- Wrong loop condition (`l <= r` instead of `l < r`).
- Returning physical array length instead of logical length after compaction.

## 2) Fast/Slow Pointer
- Missing guard before `fast = fast.next.next`.
- Comparing node values instead of node references.
- Wrong middle-node variant for even list length.
- Resetting both pointers instead of one when finding cycle start.

## 3) Fixed-Size Sliding Window
- Starting update before first full window forms.
- Wrong outgoing index (`i-k` mistake).
- Forgetting to remove outgoing effect from frequency/sum.
- Accidentally writing variable-window logic in fixed-window problem.

## 4) Variable-Size Sliding Window
- Using `if` instead of `while` when shrinking.
- Not defining validity condition clearly.
- Using variable-window for non-monotonic constraints.
- Applying positive-number sum logic to arrays with negatives.

## 5) Prefix Sum + Frequency Map
- Forgetting base state (`0 -> 1` or `0 -> -1`).
- Updating map before using current prefix for answer.
- Overwriting earliest index in max-length variants.
- Prefix formula mismatch: `prefix[r+1] - prefix[l]`.

## 6) Modulo Prefix
- Using raw `%` with negatives (wrong remainder buckets).
- Missing special handling for `k = 0`.
- Not normalizing remainder with `((x % k) + k) % k`.

## 7) Difference Array
- Forgetting `r + 1` subtraction boundary.
- Out-of-bounds write at `r + 1`.
- Reconstructing without prefix accumulation.
- Misreading inclusive range `[l..r]`.

## 8) Monotonic Deque
- Storing values instead of indices.
- Not evicting expired indices before reading front.
- Wrong comparison sign (`<` vs `<=`, `>` vs `>=`) for duplicates.
- Breaking operation order: expire -> pop dominated -> push -> answer.

## Debug Workflow After Wrong Answer
1. Write invariant in one line.
2. Trace one failing test manually with pointer/window/map states.
3. Check update order:
   - add/remove state
   - validity restore
   - answer update
4. Validate boundary math (`l`, `r`, window length, prefix index).
5. Re-run with edge-case pack.

## Edge-Case Pack (Run Every Time)
- Empty input
- Single element
- All equal values
- Strictly increasing values
- Strictly decreasing values
- Contains negatives and zeros
- Max/min possible `k` or `limit`

## Complexity Sanity Check
- [ ] Each pointer/index moves at most `n` times?
- [ ] Any hidden nested scan inside loop?
- [ ] Map/deque operations amortized `O(1)`?
- [ ] Avoided repeated substring/subarray materialization?

## WA vs TLE Quick Diagnosis
- Wrong Answer likely:
  - invariant broken
  - off-by-one
  - bad initialization
  - wrong update order
- TLE likely:
  - nested loops not collapsed
  - frequent expensive operations in loop
  - missed monotonic/deque/prefix optimization

## Logging Template for Failed Problems
- Problem:
- Expected pattern:
- My wrong assumption:
- Broken invariant:
- Fix applied:
- Retest status:
- Retry date (D+7):

## JavaScript-Specific Traps
- `Array.shift()` inside huge loops can degrade performance.
- `sort()` without numeric comparator gives lexical ordering.
- `%` with negative numbers needs normalization for modulo-bucket logic.
- `Number` precision can break very large-sum problems.

## Final Submission Checklist
- [ ] Invariant holds at loop entry and exit.
- [ ] All boundary conditions tested.
- [ ] Correctness cases + edge cases both pass.
- [ ] Complexity is within expected limits.
- [ ] I can explain pointer/window/map transitions clearly.

## Mastery Exit Criteria
- [ ] I can identify likely bug category within 2 minutes.
- [ ] I can recover from WA without random rewrites.
- [ ] I repeat fewer than 3 known mistakes across last 20 problems.
- [ ] I maintain a failure log and re-solve corrected problems.

# Kadane and Subarray Maximization Patterns

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Master linear-time subarray optimization patterns based on running states.

## Kadane Core Idea
- At each index, decide:
  - Extend previous best-ending-here subarray.
  - Start new subarray at current index.
- Track global maximum across all positions.

## Standard Kadane State
- `bestEndingHere = max(nums[i], bestEndingHere + nums[i])`
- `bestSoFar = max(bestSoFar, bestEndingHere)`

Time: `O(n)`  
Extra space: `O(1)`

## Important Variants
### Maximum subarray sum with indices
- Track start pointer when restarting.
- Save best segment boundaries when global max updates.

### Minimum subarray sum
- Same logic with `min` instead of `max`.

### Maximum circular subarray sum
- Answer is max of:
  - Standard Kadane max subarray.
  - Total sum minus minimum subarray.
- Handle all-negative case carefully.

### Fixed-length and constrained variants
- Often combine prefix sums or deque with running max logic.

## Recognition Signals
- Problem asks max/min over contiguous subarray.
- Brute force is `O(n^2)` over all start/end pairs.
- Need one pass with running decision.

## Common Mistakes
- Failing all-negative arrays by initializing with `0`.
- Losing start index when resetting state.
- Using circular formula incorrectly when min subarray equals full array.
- Confusing contiguous with subsequence.

## Debug Checklist
- [ ] Is initialization using first element, not zero?
- [ ] Are transitions computed in correct order?
- [ ] Are edge cases tested: single element, all negatives, all positives?
- [ ] For circular case, did you handle full-array min exclusion?

## Mastery Checklist
- [ ] I can implement standard Kadane from memory in under 2 minutes.
- [ ] I can return both max sum and segment indices.
- [ ] I can solve circular variant correctly.
- [ ] I can explain why one pass is sufficient.

## Practice Targets
- 12 to 15 subarray optimization problems.
- At least 5 variants beyond plain max sum.

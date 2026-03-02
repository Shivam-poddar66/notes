# Monotonic Queue for Window Extremes

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Track window minimum/maximum in linear time using a deque with monotonic order invariants.

## Core Idea
- A normal sliding window gives boundaries.
- A monotonic deque gives extreme value (`max` or `min`) of current window in `O(1)` query time.
- Each index is pushed and popped at most once, so total time is `O(n)`.

## Why Not Naive
For each window of size `k`, scanning all `k` elements is `O(n*k)`.  
Monotonic deque reduces this to `O(n)`.

## Deque Invariants
Store indices, not values.

For max deque:
- Values are decreasing from front to back.
- Front always holds index of current window maximum.

For min deque:
- Values are increasing from front to back.
- Front always holds index of current window minimum.

Also:
- Remove out-of-window indices from front (`<= i - k`).

## Template 1: Sliding Window Maximum
```javascript
function maxSlidingWindow(nums, k) {
  if (k <= 0 || nums.length === 0) return [];

  const dq = []; // stores indices, values decreasing
  const ans = [];

  for (let i = 0; i < nums.length; i++) {
    while (dq.length && dq[0] <= i - k) dq.shift();

    while (dq.length && nums[dq[dq.length - 1]] <= nums[i]) dq.pop();
    dq.push(i);

    if (i >= k - 1) ans.push(nums[dq[0]]);
  }

  return ans;
}
```

Complexity:
- Time: `O(n)` amortized
- Space: `O(k)`

## Template 2: Sliding Window Minimum
```javascript
function minSlidingWindow(nums, k) {
  if (k <= 0 || nums.length === 0) return [];

  const dq = []; // stores indices, values increasing
  const ans = [];

  for (let i = 0; i < nums.length; i++) {
    while (dq.length && dq[0] <= i - k) dq.shift();

    while (dq.length && nums[dq[dq.length - 1]] >= nums[i]) dq.pop();
    dq.push(i);

    if (i >= k - 1) ans.push(nums[dq[0]]);
  }

  return ans;
}
```

## Template 3: Longest Subarray With `max - min <= limit`
Use two deques:
- `maxDq` decreasing
- `minDq` increasing

```javascript
function longestSubarray(nums, limit) {
  const maxDq = [];
  const minDq = [];
  let left = 0;
  let best = 0;

  for (let right = 0; right < nums.length; right++) {
    while (maxDq.length && nums[maxDq[maxDq.length - 1]] < nums[right]) maxDq.pop();
    while (minDq.length && nums[minDq[minDq.length - 1]] > nums[right]) minDq.pop();
    maxDq.push(right);
    minDq.push(right);

    while (nums[maxDq[0]] - nums[minDq[0]] > limit) {
      if (maxDq[0] === left) maxDq.shift();
      if (minDq[0] === left) minDq.shift();
      left++;
    }

    best = Math.max(best, right - left + 1);
  }

  return best;
}
```

## Step Order (Very Important)
At each index `i` for fixed window size `k`:
1. Remove out-of-window indices.
2. Remove dominated indices from back to maintain monotonic order.
3. Push current index.
4. Record answer when window is fully formed (`i >= k - 1`).

Changing this order often causes off-by-one bugs.

## High-Value Problems
### 1) Sliding Window Maximum
- Canonical monotonic deque problem.

### 2) Sliding Window Minimum
- Same pattern with reversed comparisons.

### 3) Shortest Subarray With Sum At Least K (advanced variant)
- Uses prefix sums + monotonic deque on prefix indices.

### 4) Constrained Subsequence Sum
- DP + monotonic deque for max of recent DP range.

### 5) Longest Continuous Subarray With Absolute Diff <= Limit
- Two-deque dynamic window.

## Why Amortized `O(n)` Is True
- Each index enters deque once.
- It can be popped from back at most once.
- It can be removed from front at most once.
- Total deque operations are linear.

## Common Mistakes
- Storing values instead of indices, then failing to detect window expiry.
- Using wrong comparison sign (`<` vs `<=`) and breaking duplicate handling.
- Forgetting to pop expired front indices before reading extreme.
- Using array `shift()` heavily in performance-critical JS without considering cost.
- Mixing max-deque and min-deque invariants.

## Debug Checklist
- [ ] Does deque store indices?
- [ ] Is front index always inside current window?
- [ ] Is monotonic order maintained after each push?
- [ ] Is answer recorded only when window is valid/full?
- [ ] Did I test duplicates and negative numbers?

## JavaScript Notes
- `shift()` is acceptable for interview-size inputs; for very large data, implement deque with head pointer for better performance.
- Keep comparison operators intentional:
  - max deque uses `<=` when popping back (to drop weaker duplicates)
  - min deque uses `>=` when popping back
- Always read extreme via `nums[dq[0]]`.

## Mastery Checklist
- [ ] I can write max/min monotonic deque from memory.
- [ ] I can justify every pop operation with invariant reasoning.
- [ ] I can combine monotonic deque with sliding window or prefix sums.
- [ ] I can explain amortized complexity clearly in interviews.

## Practice Targets
- 16 to 24 problems:
  - 8 pure max/min window problems
  - 4 dual-deque constraint window problems
  - 4 advanced prefix+deque or DP+deque problems
- Re-solve at least 5 failed problems after 7 days.

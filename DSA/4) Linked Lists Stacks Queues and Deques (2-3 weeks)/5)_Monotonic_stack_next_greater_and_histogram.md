# Monotonic Stack, Next Greater, and Histogram

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Use monotonic stacks to solve nearest-greater/smaller and range-boundary problems in linear time.

## Mental Model
- Monotonic stack keeps values (usually indices) in sorted order while scanning.
- Two common forms:
  - Monotonic increasing stack
  - Monotonic decreasing stack
- Core benefit: each index is pushed once and popped at most once => `O(n)`.

## When Monotonic Stack Is a Signal
- "Next greater/smaller element"
- "Previous greater/smaller element"
- "Nearest element to left/right with condition"
- "For each index, find first boundary where property breaks"
- "Largest rectangle / contribution of each element over subarrays"

## Complexity Summary

| Task Style | Time | Extra Space |
|---|---|---|
| Next/previous greater-smaller | `O(n)` | `O(n)` |
| Histogram area | `O(n)` | `O(n)` |
| Sum of subarray mins/maxs | `O(n)` | `O(n)` |

## Core Invariants
- Stack stores indices, not values, when distance/width matters.
- Stack order must stay monotonic after each iteration.
- Before pushing current index, pop all indices that violate invariant.
- After popping, stack top becomes nearest valid boundary.

## Template Family 1: Next Greater Element (Right Side)

Pattern:
- Maintain decreasing stack of indices.
- While current value is greater than stack-top value, current is NGE for popped index.

```js
function nextGreaterIndex(nums) {
  const n = nums.length;
  const ans = Array(n).fill(-1);
  const st = []; // decreasing by value

  for (let i = 0; i < n; i++) {
    while (st.length && nums[st[st.length - 1]] < nums[i]) {
      ans[st.pop()] = i;
    }
    st.push(i);
  }
  return ans;
}
```

## Template Family 2: Previous Smaller Element (Left Side)

Pattern:
- Maintain increasing stack.
- Pop while top >= current (or > current based on duplicate strategy).

```js
function prevSmallerIndex(nums) {
  const n = nums.length;
  const ans = Array(n).fill(-1);
  const st = []; // increasing by value

  for (let i = 0; i < n; i++) {
    while (st.length && nums[st[st.length - 1]] >= nums[i]) st.pop();
    ans[i] = st.length ? st[st.length - 1] : -1;
    st.push(i);
  }
  return ans;
}
```

## Duplicate Handling Rules (Very Important)
- Choose strict/non-strict comparisons consistently.
- For min-contribution problems:
  - one side strict, other side non-strict to avoid double counting.
- For max-contribution problems:
  - symmetric but reversed inequality logic.

Practical convention:
- Left boundary uses strict `<`
- Right boundary uses non-strict `<=`
or vice versa, but keep it consistent.

## Circular Next Greater (NGE II)

Pattern:
- Simulate two passes over array with `i % n`.
- Push indices only in first pass.

```js
function nextGreaterElementsCircular(nums) {
  const n = nums.length;
  const ans = Array(n).fill(-1);
  const st = []; // decreasing by value, stores indices

  for (let i = 0; i < 2 * n; i++) {
    const idx = i % n;
    while (st.length && nums[st[st.length - 1]] < nums[idx]) {
      ans[st.pop()] = nums[idx];
    }
    if (i < n) st.push(idx);
  }
  return ans;
}
```

## Histogram: Largest Rectangle in Histogram

Key idea:
- For each bar as minimum height, find first smaller bar on left and right.
- Width = `rightSmaller - leftSmaller - 1`.

Single-pass optimized version:

```js
function largestRectangleArea(heights) {
  const h = [...heights, 0]; // sentinel to flush stack
  const st = []; // increasing indices by height
  let best = 0;

  for (let i = 0; i < h.length; i++) {
    while (st.length && h[st[st.length - 1]] > h[i]) {
      const mid = st.pop();
      const left = st.length ? st[st.length - 1] : -1;
      const width = i - left - 1;
      best = Math.max(best, h[mid] * width);
    }
    st.push(i);
  }
  return best;
}
```

## Contribution Pattern (Advanced but High Yield)
- Instead of asking nearest boundary directly, compute how many subarrays each index contributes to.
- Used in:
  - Sum of Subarray Minimums
  - Sum of Subarray Ranges
  - Total Strength variants

Formula idea:
- `count = leftChoices * rightChoices`
- `contribution = nums[i] * count`

## Pattern Recognition Map
- Daily Temperatures -> next greater to right.
- Stock Span -> previous greater to left.
- Next Greater Element I/II -> NGE right / circular NGE.
- Largest Rectangle in Histogram -> nearest smaller both sides.
- Maximal Rectangle in Binary Matrix -> histogram per row + largest rectangle.
- Sum of Subarray Minimums -> contribution with previous/next smaller.

## Common Mistakes
- Storing values instead of indices, losing boundary info.
- Wrong pop condition (`<` vs `<=`) causing duplicate-count bugs.
- Forgetting sentinel pass, leaving stack unprocessed.
- Using monotonic increasing when problem needs decreasing (or vice versa).
- Mixing "return index" and "return value" expectations.

## Debugging Checklist
- [ ] Is stack storing indices consistently?
- [ ] Is monotonic invariant preserved after each iteration?
- [ ] Did I choose strict/non-strict comparisons deliberately?
- [ ] Did every index get popped at most once?
- [ ] Did I flush remaining indices (sentinel or final loop)?
- [ ] For histogram, are width boundaries computed as `i - left - 1`?

## Mastery Checklist
- [ ] I can derive next greater/smaller templates without notes.
- [ ] I can switch between index output and value output correctly.
- [ ] I can explain amortized `O(n)` proof clearly.
- [ ] I can solve histogram and circular NGE confidently.
- [ ] I can handle duplicate-sensitive contribution problems.

## Practice Targets
- Total: 25 monotonic-stack problems.
- Minimum split:
  - 8 next/previous greater-smaller
  - 6 circular/variant monotonic stack
  - 6 histogram/rectangle style
  - 5 contribution-based (subarray mins/ranges)
- Re-solve all failed problems on D+7 and D+30.

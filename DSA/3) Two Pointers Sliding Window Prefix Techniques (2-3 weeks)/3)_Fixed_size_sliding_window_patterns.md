# Fixed Size Sliding Window Patterns

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Solve exact-length subarray and substring problems in linear time by reusing window state instead of recomputing from scratch.

## Core Idea
- Window size is fixed: `k`.
- Build first window state once.
- Slide by one step:
  - Remove outgoing element (`i - k`)
  - Add incoming element (`i`)
- Update answer at every valid window.

This converts brute force `O(n*k)` into `O(n)`.

## When to Use Fixed Window
- Problem asks for subarray/substring of exact length `k`.
- You need max/min/sum/average/count over every length-`k` segment.
- Window metric can be updated incrementally in `O(1)` or near `O(1)`.

Examples:
- Maximum sum subarray of size `k`
- First negative number in every window of size `k`
- Count anagrams in a string
- Distinct elements in every window of size `k`

## Template 1: Rolling Sum (Max/Min/Average of Size K)
```javascript
function maxSumWindow(nums, k) {
  if (k <= 0 || k > nums.length) return null;

  let sum = 0;
  for (let i = 0; i < k; i++) sum += nums[i];
  let best = sum;

  for (let i = k; i < nums.length; i++) {
    sum += nums[i] - nums[i - k];
    best = Math.max(best, sum);
  }

  return best;
}
```

Complexity:
- Time: `O(n)`
- Space: `O(1)`

## Template 2: Frequency Window (Strings/Counts)
```javascript
function countAnagrams(s, p) {
  const need = new Map();
  for (const ch of p) need.set(ch, (need.get(ch) || 0) + 1);

  const win = new Map();
  let matches = 0;
  let ans = 0;
  const required = need.size;
  const k = p.length;

  for (let i = 0; i < s.length; i++) {
    const add = s[i];
    win.set(add, (win.get(add) || 0) + 1);
    if (need.has(add) && win.get(add) === need.get(add)) matches++;

    if (i >= k) {
      const del = s[i - k];
      if (need.has(del) && win.get(del) === need.get(del)) matches--;
      win.set(del, win.get(del) - 1);
      if (win.get(del) === 0) win.delete(del);
    }

    if (i >= k - 1 && matches === required) ans++;
  }

  return ans;
}
```

## Template 3: Count Condition in Every Window
```javascript
function windowsWithAtLeastXEven(nums, k, x) {
  if (k > nums.length) return 0;

  let even = 0;
  for (let i = 0; i < k; i++) {
    if (nums[i] % 2 === 0) even++;
  }

  let count = even >= x ? 1 : 0;

  for (let i = k; i < nums.length; i++) {
    if (nums[i - k] % 2 === 0) even--;
    if (nums[i] % 2 === 0) even++;
    if (even >= x) count++;
  }

  return count;
}
```

## Fixed Window vs Prefix Sum
- Fixed window is best for sequential scan and one-pass optimization.
- Prefix sum is useful when many random range-sum queries are needed.
- For single max-sum-of-size-`k`, both work; rolling window is simpler and constant-space.

## High-Value Problems
### 1) Maximum Sum Subarray of Size K
- Pure rolling sum.

### 2) Average of Subarrays of Size K
- Same as rolling sum, divide by `k`.

### 3) Count Occurrences of Anagrams
- Frequency maps/arrays per window.

### 4) Distinct Elements in Every Window
- Map counts + current distinct size.

### 5) First Negative Integer in Every Window
- Maintain deque of candidate negative indices.

## Invariant Checklist
- Window boundaries always represent exactly `k` elements once initialized.
- State reflects only current window.
- Outgoing element is removed before or with incoming update consistently.

## Common Mistakes
- Starting updates before first full window is formed.
- Using wrong outgoing index (`i - k`).
- Forgetting to delete zero-frequency keys.
- Mixing fixed window with variable-window shrink logic.
- Not handling `k > n` or `k <= 0`.

## Debug Checklist
- [ ] Did I initialize first window correctly?
- [ ] Do I remove exactly one outgoing and add one incoming element each step?
- [ ] Is answer updated only for complete windows?
- [ ] Did I test `k=1`, `k=n`, and `k>n` cases?
- [ ] For strings, did I normalize case rules if required by problem?

## JavaScript Notes
- For lowercase-only strings, use array freq (`length 26`) for speed.
- For general character sets, `Map` is safer.
- Use `Number.NEGATIVE_INFINITY` only when needed; often seed from first window sum.

## Mastery Checklist
- [ ] I can derive rolling update formula quickly.
- [ ] I can choose between sum/freq/deque state based on metric type.
- [ ] I can avoid off-by-one errors on first complete window.
- [ ] I can explain why each element is processed constant times.

## Practice Targets
- 15 to 22 problems:
  - 6 rolling sum/average
  - 6 frequency-map window problems
  - 4 deque-assisted fixed-window variants
- Re-solve at least 5 failed problems after 7 days.

# Prefix Sum and Range Query Models

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Use prefix-based precomputation to answer range queries and subarray conditions faster, typically reducing repeated work from `O(n^2)` to `O(n)` or `O(1)` per query.

## Core Idea
- Build cumulative array once.
- Convert each range query into arithmetic on prefix values.
- Reuse prefix states with hash maps for counting/longest-subarray patterns.

## 1D Prefix Sum Fundamentals
Define:
- `prefix[0] = 0`
- `prefix[i + 1] = prefix[i] + nums[i]`

Then range sum for inclusive `[l..r]` is:
- `sum(l, r) = prefix[r + 1] - prefix[l]`

Why this indexing is preferred:
- Avoids branch for `l = 0`.
- Keeps formula identical for all ranges.

## Template 1: Immutable Range Sum Query
```javascript
class NumArray {
  constructor(nums) {
    this.prefix = new Array(nums.length + 1).fill(0);
    for (let i = 0; i < nums.length; i++) {
      this.prefix[i + 1] = this.prefix[i] + nums[i];
    }
  }

  sumRange(left, right) {
    return this.prefix[right + 1] - this.prefix[left];
  }
}
```

Complexity:
- Build: `O(n)`
- Query: `O(1)`
- Space: `O(n)`

## Prefix Hashing: Subarray Sum Patterns
Prefix sum turns subarray equations into hashmap lookups.

For current running sum `curr`, subarray ending here has sum `k` if:
- previous prefix value `curr - k` exists.

## Template 2: Count Subarrays With Sum K
```javascript
function subarraySum(nums, k) {
  const freq = new Map();
  freq.set(0, 1); // Empty prefix

  let curr = 0;
  let count = 0;

  for (const x of nums) {
    curr += x;
    count += freq.get(curr - k) || 0;
    freq.set(curr, (freq.get(curr) || 0) + 1);
  }

  return count;
}
```

Critical invariant:
- `freq[p]` stores how many times prefix sum `p` appeared before current index.

## Template 3: Longest Subarray With Sum K
```javascript
function maxSubArrayLen(nums, k) {
  const firstIndex = new Map();
  firstIndex.set(0, -1);

  let curr = 0;
  let best = 0;

  for (let i = 0; i < nums.length; i++) {
    curr += nums[i];

    if (!firstIndex.has(curr)) firstIndex.set(curr, i);
    if (firstIndex.has(curr - k)) {
      best = Math.max(best, i - firstIndex.get(curr - k));
    }
  }

  return best;
}
```

Key rule:
- Store earliest index for each prefix value to maximize length.

## 2D Prefix Sum Model
For matrix queries, precompute:
- `pre[r + 1][c + 1]` = sum of rectangle from `(0,0)` to `(r,c)`.

Query rectangle `(r1,c1)` to `(r2,c2)`:
- `pre[r2+1][c2+1] - pre[r1][c2+1] - pre[r2+1][c1] + pre[r1][c1]`

## Template 4: 2D Range Sum Query
```javascript
class NumMatrix {
  constructor(matrix) {
    const m = matrix.length;
    const n = m ? matrix[0].length : 0;
    this.pre = Array.from({ length: m + 1 }, () => new Array(n + 1).fill(0));

    for (let r = 0; r < m; r++) {
      for (let c = 0; c < n; c++) {
        this.pre[r + 1][c + 1] =
          matrix[r][c] +
          this.pre[r][c + 1] +
          this.pre[r + 1][c] -
          this.pre[r][c];
      }
    }
  }

  sumRegion(r1, c1, r2, c2) {
    return (
      this.pre[r2 + 1][c2 + 1] -
      this.pre[r1][c2 + 1] -
      this.pre[r2 + 1][c1] +
      this.pre[r1][c1]
    );
  }
}
```

## Prefix Sum vs Sliding Window vs Segment Tree
- Prefix sum:
  - Best for immutable array many range-sum queries.
  - Great for count/length subarray equations with hash maps.
- Sliding window:
  - Best when constraints are monotonic and window is contiguous dynamic.
- Segment tree/Fenwick tree:
  - Use when array updates and queries both happen frequently.

## High-Value Problems
### 1) Range Sum Query - Immutable
- Basic 1D prefix model.

### 2) Subarray Sum Equals K
- Prefix hash frequency counting.

### 3) Maximum Size Subarray Sum Equals K
- Prefix + earliest index map.

### 4) Continuous Subarray Sum (multiple of k)
- Prefix modulo state repeat detection.

### 5) Range Sum Query 2D - Immutable
- 2D prefix rectangle inclusion-exclusion.

## Common Mistakes
- Off-by-one with prefix indexing (`i` vs `i+1`).
- Forgetting base prefix (`0`) in map-based counting.
- Updating hash map before using it for current answer.
- Overwriting earliest index in longest-length variant.
- Confusing inclusive vs exclusive boundaries in 2D query formula.

## Debug Checklist
- [ ] Is `prefix` length `n + 1` with `prefix[0] = 0`?
- [ ] Is range query formula exactly `prefix[r+1] - prefix[l]`?
- [ ] For counting, did I seed map with `0 -> 1`?
- [ ] For max length, am I preserving earliest index only?
- [ ] For 2D, did I add and subtract overlap exactly once?

## JavaScript Notes
- Use `Map` for prefix states; keys may be negative/large.
- If sums may exceed safe integer range, consider `BigInt` approach end-to-end.
- For performance, avoid rebuilding arrays/maps inside loops.

## Mastery Checklist
- [ ] I can derive 1D and 2D prefix formulas from first principles.
- [ ] I can switch between count and max-length prefix-hash variants.
- [ ] I can explain why prefix preprocessing changes query complexity.
- [ ] I can avoid boundary mistakes without trial-and-error.

## Practice Targets
- 18 to 26 problems:
  - 8 range-sum/query problems (1D and 2D)
  - 8 prefix-hash count/length problems
  - 4 modulo-prefix variants
- Re-solve at least 6 failed problems after 7 days.

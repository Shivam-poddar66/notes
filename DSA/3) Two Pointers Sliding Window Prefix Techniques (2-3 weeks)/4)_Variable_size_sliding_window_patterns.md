# Variable Size Sliding Window Patterns

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Master dynamic window expansion and shrinking to solve substring/subarray optimization and counting problems in linear time.

## Core Idea
- Maintain a window `[left..right]`.
- Expand `right` to include new elements.
- While window is invalid, move `left` to restore validity.
- Update answer when window meets objective (max length, min length, total count).

## When This Pattern Works
- You process contiguous ranges (subarray/substring).
- Constraint can be updated incrementally when adding/removing one element.
- Window validity has a monotonic behavior as pointers move.

Examples where it works well:
- At most `k` distinct characters.
- Sum >= target for non-negative arrays.
- At most `k` odd numbers.
- No repeated characters in current window.

## When It Does Not Directly Work
- Constraints with non-monotonic behavior under shrink/expand.
- Sum-based shortest/longest problems with negative numbers (basic template can break).
- Cases where validity cannot be tracked with local incremental state.

## Template 1: Longest Valid Window (At Most Constraint)
```javascript
function longestAtMostK(nums, k) {
  const freq = new Map();
  let left = 0;
  let ans = 0;

  for (let right = 0; right < nums.length; right++) {
    freq.set(nums[right], (freq.get(nums[right]) || 0) + 1);

    while (freq.size > k) {
      const x = nums[left];
      freq.set(x, freq.get(x) - 1);
      if (freq.get(x) === 0) freq.delete(x);
      left++;
    }

    ans = Math.max(ans, right - left + 1);
  }

  return ans;
}
```

Use for:
- Longest substring with at most `k` distinct chars.
- Fruit Into Baskets (`k = 2`).

## Template 2: Minimum Length Window (At Least Constraint)
```javascript
function minLenAtLeastTarget(nums, target) {
  let left = 0;
  let sum = 0;
  let best = Infinity;

  for (let right = 0; right < nums.length; right++) {
    sum += nums[right];

    while (sum >= target) {
      best = Math.min(best, right - left + 1);
      sum -= nums[left];
      left++;
    }
  }

  return best === Infinity ? 0 : best;
}
```

Important:
- This template assumes non-negative numbers.

## Template 3: Count Subarrays With Exactly K
For many problems:
`exactly(k) = atMost(k) - atMost(k - 1)`

```javascript
function countAtMostKOdd(nums, k) {
  let left = 0;
  let odd = 0;
  let total = 0;

  for (let right = 0; right < nums.length; right++) {
    if (nums[right] % 2 !== 0) odd++;

    while (odd > k) {
      if (nums[left] % 2 !== 0) odd--;
      left++;
    }

    total += right - left + 1;
  }

  return total;
}

function countExactlyKOdd(nums, k) {
  return countAtMostKOdd(nums, k) - countAtMostKOdd(nums, k - 1);
}
```

## High-Value Patterns
### 1) Longest Substring Without Repeating Characters
- Track last seen index or frequency map.
- Shrink until duplicate removed.
- Maximize window length.

### 2) Longest Repeating Character Replacement
- Keep frequency counts and `maxFreq`.
- Valid if `(windowLen - maxFreq) <= k`.
- Expand greedily, shrink when invalid.

### 3) Minimum Size Subarray Sum
- Running sum + shrink while sum is enough.
- Minimize length.

### 4) Number of Nice Subarrays
- Exactly `k` odd count.
- Use atMost difference method.

### 5) Subarrays With K Distinct Integers
- Exactly `k` distinct = `atMost(k) - atMost(k-1)`.

## Complexity Reasoning
- `right` moves from `0` to `n-1` once.
- `left` also moves at most `n` times total.
- Total time is `O(n)`; each element enters and leaves window once.
- Extra space depends on tracking structure (`Map`, array freq), often `O(k)` or `O(alphabet)`.

## Common Mistakes
- Updating answer before restoring window validity.
- Shrinking with `if` instead of `while` when multiple removals are needed.
- Wrong frequency decrement/delete order.
- Using this template for negative-number sum problems without adjustment.
- Off-by-one in length: use `right - left + 1`.

## Debug Checklist
- [ ] What makes the window invalid?
- [ ] Is invalid window always fixed using `while`?
- [ ] When exactly do I update answer?
- [ ] Is state update symmetric for add/remove?
- [ ] Did I test edge cases (`k=0`, empty input, all same, all distinct)?

## JavaScript Notes
- `Map` is safer than plain object for arbitrary keys.
- For lowercase letters, fixed-size array freq can be faster than `Map`.
- Beware of using stale `maxFreq` logic incorrectly; it is safe in standard replacement template but must follow known invariant.

## Mastery Checklist
- [ ] I can derive window state and validity condition before coding.
- [ ] I can solve max-length, min-length, and count variants.
- [ ] I know when exactly-k should be converted to atMost difference.
- [ ] I can explain why overall complexity is linear.

## Practice Targets
- 18 to 25 problems:
  - 8 longest/minimum window problems
  - 6 counting windows (`atMost`/`exactly`)
  - 4 mixed constraints (distinct + frequency + budget)
- Re-solve at least 6 failed problems after 7 days.

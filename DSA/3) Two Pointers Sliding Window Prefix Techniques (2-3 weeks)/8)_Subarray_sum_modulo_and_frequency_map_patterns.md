# Subarray Sum Modulo and Frequency Map Patterns

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Solve subarray count/existence/length problems by converting them into prefix-state matching with hash maps, including modulo-based states.

## Core Idea
For any index `i`, let `prefix[i]` be sum of first `i` elements.
- Subarray sum from `l` to `r` is `prefix[r + 1] - prefix[l]`.
- Many problems become: find previous prefix states that satisfy an equation with current prefix.

Frequency maps let us do this in one pass.

## Modulo Prefix Principle
Subarray sum `(l..r)` is divisible by `k` if:
- `(prefix[r + 1] - prefix[l]) % k === 0`
- Equivalent to: `prefix[r + 1] % k === prefix[l] % k`

So repeated remainder states are the key signal.

## Normalizing Modulo in JavaScript
JavaScript `%` may produce negative remainder for negative values.

Use:
- `rem = ((sum % k) + k) % k`

This guarantees `rem` is in `[0, k - 1]` when `k > 0`.

## Template 1: Count Subarrays Divisible by K
```javascript
function subarraysDivByK(nums, k) {
  const freq = new Map();
  freq.set(0, 1); // empty prefix remainder

  let sum = 0;
  let count = 0;

  for (const x of nums) {
    sum += x;
    const rem = ((sum % k) + k) % k;

    count += freq.get(rem) || 0;
    freq.set(rem, (freq.get(rem) || 0) + 1);
  }

  return count;
}
```

Complexity:
- Time: `O(n)`
- Space: `O(min(n, k))` typical

## Template 2: Subarray Sum Equals K (Frequency Map Core Pattern)
```javascript
function subarraySum(nums, k) {
  const freq = new Map();
  freq.set(0, 1);

  let sum = 0;
  let count = 0;

  for (const x of nums) {
    sum += x;
    count += freq.get(sum - k) || 0;
    freq.set(sum, (freq.get(sum) || 0) + 1);
  }

  return count;
}
```

Pattern rule:
- For target equation `prefix[j] - prefix[i] = target`,
- Look up prior state `prefix[i] = prefix[j] - target`.

## Template 3: Check Subarray Sum Multiple of K (length >= 2)
```javascript
function checkSubarraySum(nums, k) {
  if (k === 0) {
    for (let i = 1; i < nums.length; i++) {
      if (nums[i] === 0 && nums[i - 1] === 0) return true;
    }
    return false;
  }

  const firstIndex = new Map();
  firstIndex.set(0, -1);

  let sum = 0;
  for (let i = 0; i < nums.length; i++) {
    sum += nums[i];
    const rem = ((sum % k) + k) % k;

    if (firstIndex.has(rem)) {
      if (i - firstIndex.get(rem) >= 2) return true;
    } else {
      firstIndex.set(rem, i); // keep earliest for max gap
    }
  }

  return false;
}
```

## Template 4: Longest Subarray With Balanced State (0/1 Equal Count)
Convert `0 -> -1`, `1 -> +1`.  
Then equal count means transformed subarray sum is `0`.

```javascript
function findMaxLength(nums) {
  const firstIndex = new Map();
  firstIndex.set(0, -1);

  let state = 0;
  let best = 0;

  for (let i = 0; i < nums.length; i++) {
    state += nums[i] === 1 ? 1 : -1;

    if (firstIndex.has(state)) {
      best = Math.max(best, i - firstIndex.get(state));
    } else {
      firstIndex.set(state, i);
    }
  }

  return best;
}
```

## Choosing Map vs Array Frequency
- If modulo base `k` is small and fixed:
  - array frequency `new Array(k).fill(0)` can be faster.
- If states are sparse/large/negative:
  - use `Map`.

## High-Value Problems
### 1) Subarray Sums Divisible by K
- Prefix remainder frequency counting.

### 2) Continuous Subarray Sum
- Prefix remainder first index + minimum length condition.

### 3) Subarray Sum Equals K
- Prefix sum frequency counting.

### 4) Binary Subarrays With Sum
- Same prefix frequency model on binary arrays.

### 5) Contiguous Array (Equal 0 and 1)
- Prefix-state earliest index for max length.

## Invariant Checklist
- `freq[state]` stores number of prior prefixes with this state.
- For max-length variants, `firstIndex[state]` stores earliest index only.
- Base state (`0`) must be seeded before iteration.

## Common Mistakes
- Forgetting to normalize modulo for negative sums.
- Missing base seed (`0 -> 1` or `0 -> -1` depending variant).
- Updating map before using it for current answer.
- Overwriting earliest index in longest-length problems.
- Ignoring special case `k = 0` in multiple-of-`k` problems.

## Debug Checklist
- [ ] Did I define prefix state correctly for this problem?
- [ ] Is base state initialized before loop?
- [ ] Is lookup done before current-state insert where required?
- [ ] For modulo, am I using normalized remainder?
- [ ] Did I test negatives, zeros, and short arrays?

## JavaScript Notes
- `Map` avoids prototype-key issues and supports non-string keys cleanly.
- For huge input, avoid unnecessary object allocations inside loop.
- Keep prefix sum in `Number`; if risk of unsafe integer overflow exists, move entire logic to `BigInt` carefully (no mixing with `Number`).

## Mastery Checklist
- [ ] I can convert subarray equations into prefix-state lookups.
- [ ] I can distinguish count vs existence vs max-length map strategy.
- [ ] I can implement modulo variants without remainder bugs.
- [ ] I can explain why the method is linear time.

## Practice Targets
- 18 to 26 problems:
  - 8 sum-equals-target frequency-map problems
  - 6 modulo-remainder subarray problems
  - 4 longest balanced-state problems
  - 4 mixed prefix-state transformations
- Re-solve at least 6 failed problems after 7 days.

# Prefix Frequency and Counting Tricks

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Master prefix-based counting patterns that convert expensive subarray/subsequence logic into linear-time solutions.

## Prefix Sum Fundamentals
- `prefix[i]` stores sum of first `i` elements.
- Range sum `[l..r]` becomes `prefix[r+1] - prefix[l]`.
- Preprocessing `O(n)`, query `O(1)`.

## Prefix Hashing Patterns
### Count subarrays with sum `k`
- Maintain running sum `curr`.
- Count previous prefixes with value `curr - k`.
- Add current prefix to map.

### Longest subarray with condition
- Store first index of each prefix state.
- On repeated state, compute candidate length.

### Balance problems (equal 0/1, vowels/consonants)
- Transform values to `+1/-1` style.
- Reuse repeated prefix-state logic.

## Frequency Prefix Arrays
- For limited domain values, maintain cumulative counts by index.
- Useful for many offline range count queries.

## Difference Array Trick (Range Updates)
- For range add `[l..r] += x`:
  - `diff[l] += x`
  - `diff[r+1] -= x` (if in bounds)
- Reconstruct final array via prefix sum.

## JavaScript Notes
- Use `Map` for sparse prefix-state keys.
- Watch numeric overflow semantics if values can exceed safe integer range.
- Prefer iterative loops for predictable performance.

## Common Mistakes
- Missing base case prefix (`0` seen once before iteration).
- Updating map in wrong order (before using it for current query).
- Mixing 0-index and 1-index prefix conventions.
- Forgetting to keep earliest index when solving max-length.

## Debug Checklist
- [ ] Is initial map state correct?
- [ ] Are prefix transitions correct for each element?
- [ ] Is length/count computed from the right indices?
- [ ] Did you test negative values and zero-heavy arrays?

## Mastery Checklist
- [ ] I can derive prefix-hash approach from brute force.
- [ ] I can solve count and max-length variants confidently.
- [ ] I can explain map state meaning at every step.
- [ ] I can avoid off-by-one errors with clear indexing choice.

## Practice Targets
- 20 problems on subarray sum/count/length with prefix-state maps.
- 5 range update/query problems using prefix/difference arrays.

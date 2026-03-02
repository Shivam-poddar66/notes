# Two Pointers: Same Direction and Opposite Direction

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Master two-pointer techniques to reduce brute force `O(n^2)` scans into `O(n)` or `O(n log n)` solutions with clear invariants.

## Core Idea
- Use two indices that move with rules tied to a condition.
- Each movement should make measurable progress.
- Correctness comes from an invariant, not from trial-and-error pointer jumps.

## Same Direction vs Opposite Direction
## Same Direction (`left` and `right` both move left -> right)
- Used for compaction, deduplication, subsequence checks, and streaming-like scans.
- Often one pointer reads (`read`) and one writes (`write`).
- Typical on arrays/strings when order matters.

## Opposite Direction (`left` from start, `right` from end)
- Used when answer depends on pair relationship across ends.
- Common in sorted arrays, palindrome checks, and greedy pair optimization.
- Movement decision usually depends on sum/compare at both ends.

## Recognition Signals
- You need pair-based decisions but want linear time.
- Brute force compares many pairs/subranges repeatedly.
- Input is sorted (or can be sorted first).
- You need in-place transformation with `O(1)` extra space.

## Template 1: Same Direction (Read/Write Compaction)
```javascript
function compact(arr, keep) {
  let write = 0;
  for (let read = 0; read < arr.length; read++) {
    if (keep(arr[read])) {
      arr[write] = arr[read];
      write++;
    }
  }
  return write; // New logical length
}
```

Use cases:
- Remove duplicates from sorted array.
- Move all non-zero values to front.
- Remove target value in-place.

Invariant:
- `arr[0 .. write-1]` is always valid and finalized.

## Template 2: Opposite Direction on Sorted Array
```javascript
function twoSumSorted(nums, target) {
  let l = 0;
  let r = nums.length - 1;

  while (l < r) {
    const sum = nums[l] + nums[r];
    if (sum === target) return [l, r];
    if (sum < target) l++;
    else r--;
  }
  return [-1, -1];
}
```

Invariant:
- Every step removes at least one impossible pair.
- Search interval shrinks monotonically.

## High-Value Patterns
### 1) Remove Duplicates in Sorted Array (Same Direction)
- `read` scans all elements.
- `write` advances only when new unique value appears.
- Time `O(n)`, extra space `O(1)`.

### 2) Move Zeroes (Same Direction)
- Write all non-zero first.
- Fill remaining indices with zero.
- Stable order for non-zero elements.

### 3) Valid Palindrome (Opposite Direction)
- Compare `s[l]` and `s[r]`.
- Move inward while equal.
- If mismatch, fail immediately.

### 4) Two Sum II - Sorted Input (Opposite Direction)
- If sum too small, increase `l`.
- If sum too large, decrease `r`.
- Exactly one pass.

### 5) Container With Most Water (Opposite Direction + Greedy)
- Area depends on width and shorter height.
- Move pointer at shorter line only.
- Greedy works because keeping shorter line cannot improve area with smaller width.

## Choosing the Direction
- Use same direction when building/validating one forward state.
- Use opposite direction when pair relation from both ends controls movement.
- If array is unsorted and pair sum is needed:
  - Hash map gives `O(n)` time, `O(n)` space.
  - Sorting + opposite pointers gives `O(n log n)` time, `O(1)` or `O(n)` extra depending sort/storage needs.

## Correctness Checklist (Invariants First)
- [ ] What does each pointer represent?
- [ ] Which condition allows moving `left`?
- [ ] Which condition allows moving `right`?
- [ ] Does each move strictly reduce remaining search?
- [ ] Are boundary cases (`n=0`, `n=1`, all equal) safe?

## Common Mistakes
- Moving both pointers when only one is justified.
- Forgetting to handle duplicates when unique pairs are required.
- Losing stable order in compaction problems.
- Off-by-one in loop condition (`l < r` vs `l <= r`).
- Returning physical array length instead of logical length after in-place write.

## JavaScript Notes
- Strings are immutable; convert to array only if mutation is needed.
- Prefer `while (l < r)` for opposite-direction loops.
- When sorting numbers, always use comparator: `arr.sort((a, b) => a - b)`.

## Debug Strategy
1. Print pointer positions and the decision taken at each step.
2. Validate invariant after each move on a small input.
3. Test edge sets:
   - empty
   - single element
   - all duplicates
   - already valid / impossible case

## Mastery Checklist
- [ ] I can identify same vs opposite direction quickly.
- [ ] I can state invariant before coding.
- [ ] I can convert brute force pair scan to linear two-pointer logic.
- [ ] I can explain why each pointer move is correct.

## Practice Targets
- 15 to 25 problems total:
  - 8 same-direction
  - 7 opposite-direction
  - 3 mixed (sort + pointers or pointers + hashing comparison)
- Re-solve at least 5 failed problems after 7 days.

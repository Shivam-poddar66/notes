# Quick Revision Sheet

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## 60-Second Pattern Picker
- Pair in sorted array, palindrome, in-place compaction -> Two pointers.
- Exact length `k` window -> Fixed-size sliding window.
- "At most / at least / minimum window" -> Variable-size sliding window.
- Many range-sum queries, immutable array -> Prefix sum.
- Many range updates, final array once -> Difference array.
- Window max/min in `O(1)` query -> Monotonic deque.
- Subarray count/length with sum condition -> Prefix + hash map.
- Divisible by `k` / modulo condition -> Prefix remainder + map.

## Must-Remember Invariants
- Two pointers: every pointer move removes impossible states.
- Fixed window: state always represents exactly `k` elements after initialization.
- Variable window: shrink until valid before answer update.
- Prefix map count: map stores prior prefix-state frequencies.
- Prefix map length: map stores earliest index for each state.
- Deque: front index is valid and optimal extreme for current window.
- Difference array: `+` at start, `-` at `end + 1`, then prefix rebuild.

## Core Formulas
- Range sum `[l..r]`: `prefix[r + 1] - prefix[l]`
- Exactly `k` count: `atMost(k) - atMost(k - 1)`
- Divisible by `k`: equal prefix remainders
- Normalize modulo: `((x % k) + k) % k`
- Window length: `right - left + 1`

## Two Pointers Quick Rules
- Same direction: read/write compaction, dedup, stable moves.
- Opposite direction: sorted pair problems and palindrome checks.
- Fast/slow: cycle detection, middle node, linked-list split.
- Guard fast pointer: check `fast && fast.next` before `fast.next.next`.

## Fixed-Size Window Checklist
- Build first window state.
- Slide by removing outgoing and adding incoming.
- Update answer only for full windows (`i >= k - 1`).
- Edge checks: `k <= 0`, `k > n`.

## Variable-Size Window Checklist
- Expand right.
- While invalid, shrink left.
- Update answer after restoring validity.
- Use `while`, not `if`, for shrink when needed.
- Sum-window shrink template assumes non-negative values.

## Prefix Sum and Map Checklist
- Seed base state before loop (`0 -> 1` for count, `0 -> -1` for index-based length).
- Lookup first, then insert current state (for most count variants).
- Keep earliest index only in max-length problems.
- Watch off-by-one in prefix indexing.

## Difference Array Checklist
- Inclusive update `[l..r] += v`:
- `diff[l] += v`
- `diff[r + 1] -= v` (if in bounds)
- Reconstruct once using running sum.
- Validate first and last index updates.

## Monotonic Deque Checklist
- Store indices, not values.
- Remove expired indices from front first.
- Remove dominated indices from back.
- Push current index.
- Read answer from front.

## Top Mistakes to Avoid
- Updating answer before window is valid.
- Forgetting base map state.
- Wrong comparison signs in deque (`<=`/`>=`).
- Failing modulo normalization for negative sums.
- Mixing 0-based and 1-based index math.
- Returning full array length after in-place compaction.

## Complexity Targets (Default)
- Two pointers / sliding window / prefix-map / deque: `O(n)`
- Prefix query after preprocessing: query `O(1)`
- Difference array with `q` updates: `O(n + q)`
- Sort + two pointers: `O(n log n)`

## Interview Explain Script (Short)
1. State pattern trigger.
2. State invariant.
3. Show pointer/window/map transitions.
4. Explain why each index is processed constant times.
5. Give final time/space complexity.

## Pre-Submit Micro Checklist
- [ ] Edge cases tested (`n=0`, `n=1`, all same, all distinct, negatives/zeros where relevant)
- [ ] Boundary math checked (`l`, `r`, `k`, `r+1`, prefix offsets)
- [ ] Invariant still true at loop end
- [ ] Complexity matches requirement

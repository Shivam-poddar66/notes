# Hash Map and Hash Set Core Patterns

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Use hashing as a first-class tool for fast lookup, counting, deduplication, and relation tracking.

## Core Hashing Use Cases
- Existence check (`seen?`).
- Frequency counting.
- Complement lookup (`target - x`).
- Grouping by signature.
- Index caching for first/last occurrence logic.

## Map vs Set in JavaScript
- `Set`: store unique values only.
- `Map`: key-value associations with insertion order.
- Prefer `Map/Set` over plain objects for predictable key behavior.

## Pattern Templates
### Two Sum style complement
1. Iterate array.
2. Compute needed value.
3. If needed exists, return match.
4. Insert current value.

### Frequency map
1. Initialize empty `Map`.
2. Increment count for each item.
3. Use counts for validation or reconstruction.

### Prefix-index map
1. Store first index of seen state.
2. Reuse when state repeats for max-length or count problems.

## Complexity Expectations
- Average `Map`/`Set` operations: `O(1)`.
- Total linear pass with hash operations: `O(n)` average.
- Worst-case collisions can degrade, but interview assumptions treat well-distributed hashing.

## When Hashing Is Better Than Sorting
- Need original index quickly.
- Need online processing in one pass.
- Need frequency updates dynamically.

## When Sorting May Be Better
- Need ordered output anyway.
- Memory is tight and `O(n log n)` is acceptable.
- Easier correctness and lower bug risk for constraints.

## Common Mistakes
- Overwriting first index when you needed earliest occurrence.
- Using object reference keys unintentionally.
- Forgetting that floating/precision issues can break numeric key assumptions.
- Assuming hash solution is always best without checking memory tradeoff.

## Mastery Checklist
- [ ] I can detect hashable problem signals quickly.
- [ ] I can implement count/existence/index patterns from memory.
- [ ] I can compare hash vs sort strategy with constraints.
- [ ] I can handle duplicates and first/last occurrence correctly.

## Practice Targets
- 20 to 25 problems focused on map/set core patterns.
- 5 problems solved in both hash and sort methods with tradeoff notes.

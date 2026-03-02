# Quick Revision Sheet (Phase 2)

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Pattern Triggers
- Fast lookup/count needed -> hash map/set.
- Contiguous subarray optimization -> prefix sum or Kadane.
- Substring condition with movable bounds -> sliding window.
- Order helps simplify logic -> sort + scan/two pointers.
- Grid traversal/transform -> matrix patterns.

## Complexity Targets
- Arrays/strings one pass: `O(n)`.
- Sort-based methods: `O(n log n)`.
- Prefix query after preprocessing: `O(1)` per query.

## JS Critical Reminders
- Numeric sort needs comparator.
- `shift/unshift` are `O(n)`.
- Use `Map/Set` instead of object for robust key handling.
- Be careful with object-key reference equality in `Map`.

## High-Value Templates to Memorize
- Frequency map update pattern.
- Prefix sum + hash count pattern.
- Sliding window add/remove pattern.
- Kadane max subarray pattern.
- Matrix direction traversal pattern.

## Edge Case Mini Checklist
- [ ] Empty input and single element.
- [ ] All equal and all distinct values.
- [ ] Negative numbers and zero.
- [ ] Duplicate-heavy inputs.
- [ ] Maximum-size stress case.

## Debugging Triggers
- Wrong answer on random tests -> check invariant update order.
- Timeout -> check hidden linear ops inside loops.
- Intermittent bug -> check key serialization and mutation side effects.

## Phase 2 Exit Snapshot
- [ ] 60 problems completed with tracker entries.
- [ ] Medium pattern recognition is fast and reliable.
- [ ] D+7 and D+30 re-solves show retention.
- [ ] Recurring mistakes reduced with checklist use.

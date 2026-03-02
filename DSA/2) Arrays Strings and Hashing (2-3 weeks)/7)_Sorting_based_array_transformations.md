# Sorting Based Array Transformations

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Use sorting strategically to simplify constraints and enable linear scans.

## Why Sorting Helps
- Brings similar/close values together.
- Converts pairwise checks into pointer scans.
- Makes interval and merge logic simpler.

## Common Sort-Based Patterns
- Merge intervals.
- Meeting/overlap scheduling checks.
- Two-pointer after sorting for pair/triple conditions.
- Greedy matching between two arrays.
- Coordinate compression pre-step.

## Decision Rule
If direct unsorted processing is complex, test:
- Sort first (`O(n log n)`), then solve in one pass (`O(n)`).

## Core Templates
### Sort + sweep
- Sort by key.
- Maintain active state while scanning.
- Update answer on transitions.

### Sort + two pointers
- One pointer left, one right.
- Move pointers based on monotonic condition.

### Sort + grouping
- Aggregate equal keys in contiguous block.
- Process block-level statistics.

## JavaScript Comparator Rules
- Numeric sort must use comparator:
  - `arr.sort((a, b) => a - b)`.
- For tuples/objects, sort by primary then secondary keys explicitly.

## Stability and Tie Handling
- Decide tie order intentionally for interval and greedy logic.
- Incorrect tie-break can break correctness even with right complexity.

## Common Mistakes
- Forgetting numeric comparator and getting lexical order.
- Sorting when original indices are required but not stored.
- Wrong tie-break order for equal keys.
- Claiming `O(n)` after using sort.

## Mastery Checklist
- [ ] I can identify when `O(n log n)` sorting is acceptable.
- [ ] I can design correct comparator and tie-break logic.
- [ ] I can combine sorting with two pointers/sweeps confidently.
- [ ] I can preserve original index information when needed.

## Practice Targets
- 20 problems with sort + scan patterns.
- 5 comparator-heavy problems where tie-break correctness matters.

# Common Mistakes and Debugging Checklist

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Prevent repeat mistakes in arrays, strings, and hashing problems.

## Most Common Mistakes
- Off-by-one errors in window and range boundaries.
- Forgetting to update both add/remove operations in sliding window.
- Wrong sort comparator in JavaScript.
- Mutating arrays/strings unexpectedly.
- Prefix map initialized incorrectly.
- Duplicate handling mistakes in two-sum/grouping problems.

## Phase 2 Debug Flow
1. Reproduce on smallest failing case.
2. Print critical state transitions only.
3. Validate invariant for each iteration.
4. Check boundary updates (`l`, `r`, indices).
5. Re-check complexity for hidden linear calls.

## Sliding Window Debug Checklist
- [ ] Validity condition defined clearly?
- [ ] On expand: added right character/value?
- [ ] On shrink: removed left character/value?
- [ ] Answer update at correct time?

## Prefix Hash Debug Checklist
- [ ] Base map state includes prefix zero?
- [ ] Query-before-insert order correct?
- [ ] Stored earliest index when needed for max length?
- [ ] Negative and zero values tested?

## Hashing Debug Checklist
- [ ] Key encoding deterministic and collision-safe?
- [ ] Duplicate values handled correctly?
- [ ] Map updates not overwriting needed historical state?

## JavaScript Debug Traps
- Lexicographic numeric sort bug.
- Shared-row matrix initialization bug.
- Using `==` instead of strict checks in sensitive logic.
- Floating precision comparison issues.

## Prevention Rules
- Always write 3 edge tests before final submit.
- Always state invariant in comments or scratch notes.
- Always run a duplicate-heavy test for hashing problems.

## Mastery Checklist
- [ ] I can classify bug root cause quickly.
- [ ] I debug with invariant-based tracing, not random edits.
- [ ] I avoid repeat errors with a personal pre-submit checklist.
- [ ] I capture each failure in mistake log with prevention rule.

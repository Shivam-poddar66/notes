# Common Mistakes and Debugging Checklist

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Debug faster by using a structured checklist instead of random edits.

## High-Frequency Mistakes in Phase 1
- Wrong complexity claim due to hidden operations.
- Off-by-one errors in loops and binary search bounds.
- Incorrect initialization of answer variables.
- Missing edge-case handling for empty or single input.
- Incorrect assumptions about sortedness or uniqueness.
- Premature optimization that breaks correctness.

## Debugging Flow (Use in Order)
1. Reproduce bug on smallest failing input.
2. Confirm expected output manually.
3. Trace variables step by step.
4. Check invariant break point.
5. Patch one issue only.
6. Re-test old and new cases.

## Binary Search Specific Checklist
- [ ] Is search space definition correct?
- [ ] Loop condition (`<=` vs `<`) matches update rules?
- [ ] Mid computation and bound updates are consistent?
- [ ] Final returned index/value is validated?

## Two-Pointer/Window Checklist
- [ ] Pointer movement guarantees progress?
- [ ] Window state updates are symmetric on expand/shrink?
- [ ] All branches update answer when needed?
- [ ] Duplicate/edge windows tested?

## Complexity Debug Checklist
- [ ] Any nested operation hidden in helper call?
- [ ] Any expensive operation inside loop (`sort`, `slice`, `shift`)?
- [ ] Any re-initialization of large structures in repeated blocks?

## Logging Strategy
- Print only key variables at decision points.
- Avoid noisy logs across all iterations unless needed.
- Remove logs once bug is confirmed and fixed.

## Root-Cause Categories for Mistake Log
- Logic error.
- State update error.
- Boundary/index error.
- Constraint misread.
- Complexity mis-estimation.

## Mastery Checklist
- [ ] I can isolate failure to a specific invariant break.
- [ ] I avoid random code changes without a hypothesis.
- [ ] I can classify each bug by root cause.
- [ ] I update my preventive checklist from repeated mistakes.

## Practice Targets
- Maintain a debugging log for next 15 failed attempts.
- For each failure, add one "prevention rule" for future problems.

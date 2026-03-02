# DSA Note System and Mistake Log

Last updated: February 26, 2026  
Phase: Setup and DSA Learning System (Phase 0)

## Goal
Capture learning in a reusable format so mistakes are not repeated.

## Problem Note Template
Use this structure for every solved or attempted problem:
- Problem name and link.
- Pattern tags (example: two pointers, monotonic stack).
- Difficulty and solve time.
- Approach summary (brute-force and optimized).
- Complexity.
- Edge cases that matter.
- Final bug that blocked acceptance.
- One-line takeaway.

## Mistake Log Fields
- Date.
- Problem ID.
- Mistake type.
- Root cause.
- Preventive rule.
- Re-test date (D+7, D+30).

## Mistake Taxonomy
- Misread requirement.
- Wrong data structure choice.
- Boundary/index error.
- Overflow/type issue.
- Logic gap in transition or invariant.
- Incomplete edge-case testing.
- Time pressure panic and rushed coding.

## Weekly Review Routine
1. Group mistakes by type.
2. Identify top 2 recurring root causes.
3. Write one preventive checklist item per root cause.
4. Re-solve 3 failed problems without looking at old code.

## Quality Rule
If a mistake repeats 3 times, add a hard pre-submit checklist line and use it on every subsequent problem.

## Exit Criteria
- [ ] You have a single note template used for all new problems.
- [ ] You have a mistake log with at least one real entry.
- [ ] You have a weekly review slot in calendar/time blocks.

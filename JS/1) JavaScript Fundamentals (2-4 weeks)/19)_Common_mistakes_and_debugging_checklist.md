# Common Mistakes and Debugging Checklist (Phase 1)

## Frequent Mistakes
- Using `==` where `===` is needed.
- Using `var` unintentionally.
- Forgetting `break` in `switch`.
- Confusing `for...in` and `for...of`.
- Using `sort()` without numeric comparator.
- Mutating arrays/objects accidentally.
- Treating `null` and `undefined` as identical in all cases.
- Assuming `0 || default` is safe when `0` is valid.
- Ignoring `NaN` checks in parsed inputs.
- Swallowing errors without logs.

## Debugging Checklist
1. Reproduce issue with smallest input.
2. Read exact error message and stack trace line.
3. Verify variable types using `typeof` or `Array.isArray`.
4. Add targeted `console.log` checkpoints.
5. Check assumptions around null/undefined.
6. Check loop boundaries and off-by-one conditions.
7. Validate parsed numbers and string trimming.
8. Isolate function and test independently.
9. Write a failing test/input case.
10. Fix and retest both normal and edge cases.

## Preventive Habits
- Use ESLint and formatters.
- Prefer small pure functions.
- Keep naming explicit.
- Add quick manual test cases for each feature.

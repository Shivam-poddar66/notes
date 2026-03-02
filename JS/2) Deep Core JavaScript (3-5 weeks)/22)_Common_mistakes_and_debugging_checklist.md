# Common Mistakes and Debugging Checklist (Phase 2)

## Frequent Mistakes
- Misunderstanding hoisting vs TDZ.
- Expecting arrow function to have dynamic `this`.
- Losing method `this` when passing as callback.
- Incorrect prototype assignment (missing constructor reset).
- Deep copy assumptions from spread syntax.
- Mixing ESM/CJS without clear config.
- Hidden side effects at module top level.
- Closure capturing mutable state unexpectedly.

## Debugging Checklist
1. Reproduce issue with smallest snippet.
2. Log `this` and invocation site.
3. Inspect stack trace and call path.
4. Check declaration type (`var`/`let`/`const`).
5. Verify closure-captured variables.
6. Compare own vs prototype properties.
7. Confirm module type and import/export syntax.
8. Test side effects by isolating import statements.
9. Validate copy strategy for nested state.
10. Add targeted tests for edge behavior.

## Preventive Habits
- Prefer strict mode and lint rules.
- Keep module APIs explicit and small.
- Add behavior tests for internals-heavy utilities.

# Quick Revision Sheet (Phase 1)

## Syntax and Variables
- Expression returns value, statement performs action.
- Prefer `const`; use `let` when reassignment is needed.
- Avoid `var` in modern code.

## Data Types
- Primitives: string, number, bigint, boolean, undefined, null, symbol.
- `typeof null` is `"object"` (quirk).
- Objects/arrays are reference types.

## Coercion and Comparison
- Prefer `===` and `!==`.
- Falsy: `false`, `0`, `-0`, `0n`, `""`, `null`, `undefined`, `NaN`.
- Use `??` when `0`/`""` are valid values.

## Control Flow and Loops
- `if/else` for complex conditions.
- `switch` for fixed-value branches.
- `for...of` for iterable values.
- `for...in` for object keys.

## Functions
- Declaration, expression, arrow.
- Default params and rest params are essential.
- Prefer pure functions where possible.

## Collections
- Arrays: `map`, `filter`, `reduce`, `find`, `some`, `every`.
- Objects: `keys`, `values`, `entries`, spread, `assign`.
- `Set` for uniqueness.
- `Map` for key-value with any key type.

## Strings, Numbers, Dates
- Trim and validate user input.
- Numeric sort needs comparator: `(a, b) => a - b`.
- Watch floating point precision.
- Date month is 0-based in `getMonth()`.

## Errors
- Use `try/catch/finally` for risky operations.
- Throw meaningful errors.
- Read stack trace top frame in your code first.

## Milestone
- 15 exercises done.
- Calculator, To-do, Expense tracker completed.
- Ready to start Phase 2.

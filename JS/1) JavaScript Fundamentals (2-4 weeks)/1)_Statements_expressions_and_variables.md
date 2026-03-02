# Statements, Expressions, and Variables

## 1) Statements vs Expressions

### Statement
A statement performs an action.

```js
let total = 0;
if (total === 0) {
  total = 10;
}
```

### Expression
An expression produces a value.

```js
2 + 3;
"Hello" + " JS";
total > 5;
```

### Quick Rule
- If it returns a value, it is usually an expression.
- If it controls behavior, declaration, or flow, it is usually a statement.

## 2) Variables: `var`, `let`, `const`

### `let`
- Block-scoped.
- Can be reassigned.
- Preferred when value changes.

### `const`
- Block-scoped.
- Cannot be reassigned.
- Preferred default choice.
- For objects/arrays, reference is constant, contents can still change.

### `var`
- Function-scoped, not block-scoped.
- Hoisted with `undefined` initialization.
- Can be redeclared.
- Avoid in modern code unless maintaining legacy code.

## 3) Naming Rules and Conventions
- Allowed: letters, digits, `_`, `$`.
- Cannot start with digit.
- Case-sensitive (`userName` != `username`).
- Use `camelCase` for variable/function names.
- Use clear names: `userAge` instead of `x`.

## 4) Reassignment vs Mutation

```js
const user = { name: "A" };
user.name = "B";      // allowed (mutation)
// user = {};          // not allowed (reassignment)

let count = 1;
count = 2;             // allowed
```

## 5) Best Practices
- Prefer `const` first, switch to `let` only if needed.
- Keep variable scope as small as possible.
- Initialize variables near where they are used.
- Avoid global variables for app state.

## 6) Quick Practice
1. Write one statement and one expression each.
2. Convert all valid `var` declarations to `let` or `const`.
3. Refactor unclear variable names into meaningful ones.

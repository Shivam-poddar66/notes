# Lexical Environment and Scope Chain

## 1) Lexical Scope
Scope is decided by where code is written, not where it is called.

```js
const outer = "global";

function parent() {
  const inner = "parent";

  function child() {
    console.log(outer, inner);
  }

  child();
}

parent();
```

## 2) Scope Chain Lookup
When JS needs a variable:
1. Check local scope.
2. Check parent scope.
3. Continue upward to global scope.
4. If not found: `ReferenceError`.

## 3) Global, Function, and Block Scope
- Global: available everywhere.
- Function: inside function only.
- Block: `let`/`const` inside `{}`.

## 4) Lexical Environment in Practice
Nested functions keep references to outer variables.
This enables closures.

## 5) Best Practices
- Keep scope narrow to reduce bugs.
- Avoid hidden dependencies on globals.
- Use small helper functions with explicit inputs.

## 6) Quick Practice
1. Trace variable lookup for nested functions.
2. Create examples for all three scope types.
3. Refactor code to avoid global variable dependence.

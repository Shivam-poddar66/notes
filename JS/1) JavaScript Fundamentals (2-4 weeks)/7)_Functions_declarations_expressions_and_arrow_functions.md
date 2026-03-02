# Functions: Declaration, Expression, Arrow

## 1) Function Declaration

```js
function add(a, b) {
  return a + b;
}
```

- Hoisted with function body.
- Can be called before declaration in code order.

## 2) Function Expression

```js
const add = function (a, b) {
  return a + b;
};
```

- Stored in variable.
- Not callable before assignment.

## 3) Arrow Function

```js
const add = (a, b) => a + b;
```

- Short syntax.
- Lexical `this` behavior.
- Great for callbacks and small functions.

## 4) Choosing Function Style
- Declaration: reusable named utilities.
- Expression: conditional assignment patterns.
- Arrow: concise callbacks and functional pipelines.

## 5) Naming and Readability
- Use verb-based names: `calculateTotal`, `formatPrice`.
- Prefer small single-purpose functions.
- Keep side effects explicit.

## 6) Quick Practice
1. Rewrite one declaration as expression and arrow.
2. Build `isEven`, `sumArray`, `formatUserName` helpers.
3. Explain when to avoid arrow functions (method needing dynamic `this`).

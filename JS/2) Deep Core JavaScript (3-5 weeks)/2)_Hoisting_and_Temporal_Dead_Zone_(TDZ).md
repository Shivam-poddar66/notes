# Hoisting and Temporal Dead Zone (TDZ)

## 1) Hoisting Concept
During creation phase, declarations are registered before execution.

- Function declarations: hoisted with full definition.
- `var`: hoisted and initialized as `undefined`.
- `let`/`const`: hoisted but uninitialized (TDZ applies).

## 2) `var` Hoisting Example

```js
console.log(x); // undefined
var x = 10;
```

Equivalent model:

```js
var x;
console.log(x);
x = 10;
```

## 3) `let` and `const` with TDZ

```js
// console.log(y); // ReferenceError
let y = 20;
```

TDZ is the region from block start to declaration line.

## 4) Function Hoisting

```js
sayHello(); // works
function sayHello() {
  console.log("Hello");
}
```

Function expressions are different:

```js
// greet(); // TypeError or ReferenceError depending declaration
const greet = function () {};
```

## 5) Best Practices
- Declare before use for clarity.
- Prefer `const` and `let`.
- Avoid relying on hoisting behavior.

## 6) Quick Practice
1. Predict output of 5 hoisting snippets.
2. Explain TDZ in your own words.
3. Convert `var` code to `let`/`const` safely.

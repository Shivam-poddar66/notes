# Parameters, Returns, Scope, and Pure Functions

## 1) Parameters

### Default Parameters

```js
function greet(name = "Guest") {
  return `Hello, ${name}`;
}
```

### Rest Parameters

```js
function sum(...nums) {
  return nums.reduce((acc, n) => acc + n, 0);
}
```

## 2) Return Values
- A function returns `undefined` if no `return` statement is used.
- Prefer explicit returns for clarity.
- Use early return to reduce nesting.

## 3) Scope Basics
- Global scope: accessible everywhere (avoid large global state).
- Function scope: variables inside function.
- Block scope: `let`/`const` inside `{}`.

```js
if (true) {
  let blockValue = 1;
}
// blockValue is not accessible here
```

## 4) Pure vs Impure Functions

### Pure
- Same input -> same output.
- No side effects.

```js
function multiply(a, b) {
  return a * b;
}
```

### Impure
- Depends on or changes external state.

```js
let total = 0;
function addToTotal(v) {
  total += v;
}
```

## 5) Best Practices
- Keep most utility functions pure.
- Pass dependencies as arguments.
- Limit mutation to clear boundaries.

## 6) Quick Practice
1. Convert impure helper into pure version.
2. Use default and rest params in one function.
3. Create examples of global, function, and block scope.

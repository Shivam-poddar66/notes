# Execution Context and Call Stack

## 1) What is an Execution Context?
An execution context is the environment in which JS code is evaluated.

Types:
- Global execution context
- Function execution context
- Eval execution context (rare)

Each context contains:
- Variable environment
- Scope chain reference
- `this` binding

## 2) Global Execution Context
- Created first when script starts.
- Creates global object bindings.
- Runs top-level code.

## 3) Function Execution Context
Each function call creates a new context.

```js
function a() {
  b();
}

function b() {
  console.log("inside b");
}

a();
```

## 4) Call Stack
The call stack tracks active execution contexts in LIFO order.

Flow for above example:
1. Push global context.
2. Call `a` -> push `a` context.
3. Call `b` -> push `b` context.
4. `b` returns -> pop `b`.
5. `a` returns -> pop `a`.
6. Program ends.

## 5) Stack Overflow
Too much recursive depth crashes stack.

```js
function loop() {
  loop();
}
// loop(); // RangeError: Maximum call stack size exceeded
```

## 6) Debug Tips
- Use debugger breakpoints to inspect stack frames.
- Read stack trace from top frame in your code.
- Keep recursion with clear base condition.

## 7) Quick Practice
1. Draw call stack for nested function calls.
2. Build recursion with proper base case.
3. Explain why stack overflow happens.

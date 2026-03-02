# Synchronous vs Asynchronous Execution

## 1) Synchronous Execution
- Code runs line by line.
- Each step blocks the next step until finished.

```js
console.log("A");
console.log("B");
console.log("C");
```

## 2) Asynchronous Execution
- Long operations are delegated.
- Main thread keeps running while async work completes.

```js
console.log("A");
setTimeout(() => console.log("B"), 0);
console.log("C");
// Output: A C B
```

## 3) Why Async Matters
- Network requests
- Timers
- File I/O
- Database operations
- User interaction

## 4) Blocking vs Non-Blocking
Blocking code holds the call stack.
Non-blocking code schedules completion callbacks/promises.

## 5) Quick Practice
1. Predict output order for timer examples.
2. Identify blocking operations in a script.
3. Refactor one blocking sequence into async flow.

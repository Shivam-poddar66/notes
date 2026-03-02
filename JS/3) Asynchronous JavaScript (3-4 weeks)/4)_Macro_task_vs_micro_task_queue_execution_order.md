# Macro-task vs Micro-task Queue Execution Order

## 1) Macro-task Sources
- `setTimeout`
- `setInterval`
- message events
- I/O callbacks (environment-dependent)

## 2) Micro-task Sources
- `Promise.then/catch/finally`
- `queueMicrotask`
- `MutationObserver` (browser)

## 3) Execution Priority
After current stack finishes:
1. Drain all micro-tasks.
2. Execute one macro-task.
3. Repeat.

## 4) Example
```js
setTimeout(() => console.log("T1"), 0);
Promise.resolve()
  .then(() => console.log("P1"))
  .then(() => console.log("P2"));
setTimeout(() => console.log("T2"), 0);
// P1 P2 T1 T2
```

## 5) Starvation Risk
Too many chained micro-tasks can delay macro-tasks and UI responsiveness.

## 6) Quick Practice
1. Predict outputs for mixed queue snippets.
2. Create micro-task heavy snippet and observe delay.
3. Convert some micro-task work to macro-task scheduling when needed.

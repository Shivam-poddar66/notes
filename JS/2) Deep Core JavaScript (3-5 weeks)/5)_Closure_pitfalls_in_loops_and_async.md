# Closure Pitfalls in Loops and Async

## 1) Classic Loop Bug with `var`

```js
for (var i = 0; i < 3; i++) {
  setTimeout(() => console.log(i), 0);
}
// Output: 3 3 3
```

Reason: one shared `i` binding.

## 2) Fix with `let`

```js
for (let i = 0; i < 3; i++) {
  setTimeout(() => console.log(i), 0);
}
// Output: 0 1 2
```

## 3) Fix with IIFE (Legacy)

```js
for (var i = 0; i < 3; i++) {
  (function (j) {
    setTimeout(() => console.log(j), 0);
  })(i);
}
```

## 4) Async Closure Gotchas
- Capturing stale variables in callbacks.
- Using mutable outer state with delayed execution.
- Expecting synchronous order in asynchronous loops.

## 5) Debug Strategy
1. Log captured values and timestamps.
2. Prefer `let` in loop indices.
3. Pass state as function arguments.
4. Avoid unnecessary shared mutable state.

## 6) Quick Practice
1. Fix a buggy loop closure snippet.
2. Build delayed logger with correct per-item output.
3. Explain why async captures differ from sync runs.

# Callbacks and Inversion of Control

## 1) Callback Basics
A callback is a function passed to another function to run later.

```js
function process(data, done) {
  const result = data.trim();
  done(result);
}
```

## 2) Async Callback Example
```js
setTimeout(() => {
  console.log("done");
}, 1000);
```

## 3) Inversion of Control
When you pass callback, you trust external code to call it correctly.
Risks:
- callback called multiple times
- callback not called
- error handling inconsistency

## 4) Error-First Callback Style (Node legacy)
```js
function readConfig(path, cb) {
  // cb(error, data)
}
```

## 5) Best Practices
- Validate callback inputs.
- Wrap callback APIs in promises for consistency.
- Avoid deep callback nesting.

## 6) Quick Practice
1. Write one sync and one async callback utility.
2. Convert callback function to promise-based version.
3. Handle callback errors safely.

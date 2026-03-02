# Promises: States, Resolution, and Settlement

## 1) Promise States
- `pending`
- `fulfilled`
- `rejected`

## 2) Resolution vs Settlement
- Resolved: promise outcome is tied to another promise/value.
- Settled: final state is fulfilled or rejected.

## 3) Basic Promise
```js
const p = new Promise((resolve, reject) => {
  const ok = true;
  if (ok) resolve("success");
  else reject(new Error("failed"));
});
```

## 4) Consuming Promise
```js
p.then((value) => console.log(value))
 .catch((err) => console.error(err.message))
 .finally(() => console.log("done"));
```

## 5) Best Practices
- Avoid manual promise creation unless needed.
- Reject with `Error` objects.
- Always return or await promises in chains.

## 6) Quick Practice
1. Build promise wrapper for `setTimeout`.
2. Trigger resolve and reject paths.
3. Explain state transitions with timeline.

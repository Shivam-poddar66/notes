# Promise Combinators: `Promise.all`, `allSettled`, `race`, and `any` in JavaScript

---

## 1) Core Mental Model: What is a Promise Combinator?

A **Promise Combinator** is a static helper method on `Promise` that accepts an **iterable of Promises** and coordinates them into a single consolidated Promise based on a specific concurrency strategy.

JavaScript provides four distinct combinators to handle every possible multi-promise coordination pattern:

```
┌────────────────────────────────────────────────────────────────────────┐
│                      THE 4 PROMISE COMBINATORS                         │
│                                                                        │
│   1. Promise.all()        ──▶ ALL must succeed. (Fail-fast on 1st err)│
│   2. Promise.allSettled() ──▶ WAIT FOR ALL outcomes (Never rejects).   │
│   3. Promise.race()       ──▶ FIRST to settle wins (Fulfill OR Reject) │
│   4. Promise.any()        ──▶ FIRST to succeed wins (Ignores errors).  │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Master Head-to-Head Comparison Matrix

| Combinator | Introduced | Fulfillment Condition | Rejection Condition | Short-Circuits? | Result Shape |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`Promise.all`** | ES6 (2015) | **ALL** promises fulfill | On **FIRST** rejection | ⚡ Yes (On 1st reject) | Array of values: `[v1, v2]` |
| **`Promise.allSettled`**| ES2020 | **ALL** promises settle | **Never rejects** | ❌ No (Waits for all) | Array of status objects |
| **`Promise.race`** | ES6 (2015) | On **FIRST** settlement (if fulfilled) | On **FIRST** settlement (if rejected) | ⚡ Yes (On 1st settlement)| Single winning value/reason |
| **`Promise.any`** | ES2021 | On **FIRST** fulfillment | When **ALL** reject | ⚡ Yes (On 1st success) | Single value (or `AggregateError`) |

---

## 3) Deep-Dive into Each Combinator

---

### 1. `Promise.all()` (All-or-Nothing / Fail-Fast)

Use `Promise.all()` when multiple independent asynchronous operations are **all required** for the next step to proceed.

```js
async function loadDashboardData(userId) {
  try {
    // Runs all 3 network requests in parallel
    const [user, orders, settings] = await Promise.all([
      fetchUser(userId),
      fetchOrders(userId),
      fetchSettings(userId)
    ]);

    return { user, orders, settings };
  } catch (err) {
    // ⚠️ If ANY single request fails, Promise.all rejects IMMEDIATELY!
    console.error("Dashboard load failed due to:", err.message);
  }
}
```

> **Important**: When `Promise.all` rejects, the remaining un-settled promises **continue executing in the background**. JavaScript does not automatically cancel ongoing network requests (use `AbortController` for cancellation).

---

### 2. `Promise.allSettled()` (Complete Audit / Zero Short-Circuit)

Use `Promise.allSettled()` when you want to execute a batch of operations and inspect the result of every single task, regardless of whether some succeeded and others failed.

```js
const tasks = [
  fetch("/api/user-1"),
  fetch("/api/broken-endpoint"), // Will reject
  fetch("/api/user-3")
];

const results = await Promise.allSettled(tasks);

results.forEach((result, index) => {
  if (result.status === "fulfilled") {
    console.log(`Task #${index} succeeded with value:`, result.value);
  } else {
    console.error(`Task #${index} failed with reason:`, result.reason);
  }
});

// Output Structure:
// [
//   { status: "fulfilled", value: ResponseObj },
//   { status: "rejected",  reason: ErrorObj },
//   { status: "fulfilled", value: ResponseObj }
// ]
```

---

### 3. `Promise.race()` (First Settled Wins / Timeout Guard)

`Promise.race()` settles as soon as the **first promise in the list settles** (whether it fulfilled or rejected).

#### Real-World Pattern: Enforcing Request Timeouts
```js
function withTimeout(promise, ms = 3000) {
  const timeoutPromise = new Promise((_, reject) => {
    setTimeout(() => reject(new Error(`Operation timed out after ${ms}ms`)), ms);
  });

  // Race the actual task against the countdown timer!
  return Promise.race([promise, timeoutPromise]);
}

try {
  const data = await withTimeout(fetchLargeFile(), 2000);
  console.log("File loaded on time:", data);
} catch (err) {
  console.error("Operation failed or timed out:", err.message);
}
```

---

### 4. `Promise.any()` (First Success Wins / Fallback Mirrors)

`Promise.any()` returns the value of the **first promise that successfully fulfills**. It ignores rejections unless every single promise in the list rejects.

If all promises reject, it throws an **`AggregateError`**:

```js
async function fetchFromFastestCdn(fileUrl) {
  const mirrors = [
    fetch(`https://us-east.cdn.com/${fileUrl}`),
    fetch(`https://eu-west.cdn.com/${fileUrl}`),
    fetch(`https://ap-south.cdn.com/${fileUrl}`)
  ];

  try {
    // Returns the response from whichever mirror responds first with 200 OK!
    const fastestResponse = await Promise.any(mirrors);
    return await fastestResponse.json();
  } catch (err) {
    // All 3 mirrors failed!
    if (err instanceof AggregateError) {
      console.error("All CDN mirrors failed! Individual errors:", err.errors);
    }
  }
}
```

---

## 4) Writing Polyfills for All 4 Combinators (Senior Interview Question)

---

### Polyfill: `myPromiseAll`
```js
Promise.myPromiseAll = function (promises) {
  return new Promise((resolve, reject) => {
    const items = Array.from(promises);
    const results = new Array(items.length);
    let completed = 0;

    if (items.length === 0) return resolve([]);

    items.forEach((item, index) => {
      Promise.resolve(item).then(
        (val) => {
          results[index] = val; // Preserve input index order
          completed++;
          if (completed === items.length) resolve(results);
        },
        (err) => {
          reject(err); // Short-circuit on first rejection
        }
      );
    });
  });
};
```

---

### Polyfill: `myPromiseAllSettled`
```js
Promise.myPromiseAllSettled = function (promises) {
  return new Promise((resolve) => {
    const items = Array.from(promises);
    const results = new Array(items.length);
    let settled = 0;

    if (items.length === 0) return resolve([]);

    items.forEach((item, index) => {
      Promise.resolve(item).then(
        (val) => {
          results[index] = { status: "fulfilled", value: val };
          settled++;
          if (settled === items.length) resolve(results);
        },
        (err) => {
          results[index] = { status: "rejected", reason: err };
          settled++;
          if (settled === items.length) resolve(results);
        }
      );
    });
  });
};
```

---

### Polyfill: `myPromiseRace`
```js
Promise.myPromiseRace = function (promises) {
  return new Promise((resolve, reject) => {
    for (const item of promises) {
      Promise.resolve(item).then(resolve, reject);
    }
  });
};
```

---

### Polyfill: `myPromiseAny`
```js
Promise.myPromiseAny = function (promises) {
  return new Promise((resolve, reject) => {
    const items = Array.from(promises);
    const errors = new Array(items.length);
    let rejectedCount = 0;

    if (items.length === 0) {
      return reject(new AggregateError([], "All promises were rejected"));
    }

    items.forEach((item, index) => {
      Promise.resolve(item).then(
        (val) => resolve(val), // First fulfillment wins immediately!
        (err) => {
          errors[index] = err;
          rejectedCount++;
          if (rejectedCount === items.length) {
            reject(new AggregateError(errors, "All promises were rejected"));
          }
        }
      );
    });
  });
};
```

---

## 5) Decision Flowchart: Which Combinator Should You Use?

```
                      WHAT DO YOU NEED FROM YOUR TASKS?
                                     │
           ┌─────────────────────────┴─────────────────────────┐
           ▼                                                   ▼
   I need ALL tasks...                                 I need ONE task...
           │                                                   │
     ┌─────┴─────┐                                       ┌─────┴─────┐
     ▼           ▼                                       ▼           ▼
All MUST      Partial failure                       First to    First SUCCESS
succeed       is acceptable                         SETTLE      only (Ignore errs)
     │           │                                       │           │
     ▼           ▼                                       ▼           ▼
Promise.all  Promise.allSettled                     Promise.race  Promise.any
```

---

## 6) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                     PROMISE COMBINATORS CHEAT SHEET                        |
+───────────────────────────+────────────────────────────────────────────────+
| `Promise.all`             | All must fulfill. Rejects on 1st error.        |
| `Promise.allSettled`      | Never rejects. Returns `status` for all.       |
| `Promise.race`            | First to settle wins (success OR failure).     |
| `Promise.any`             | First to fulfill wins. Throws `AggregateError`.|
| Order Guarantee           | `.all` & `.allSettled` preserve input indices. |
| Timeout Pattern           | Implement via `Promise.race([task, timer])`.   |
| Mirror / Fallback Pattern | Implement via `Promise.any([mirrorA, mirrorB])`|
+───────────────────────────+────────────────────────────────────────────────+
```

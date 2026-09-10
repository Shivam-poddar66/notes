# Promises: States, Resolution, and Settlement in JavaScript

---

## 1) Core Mental Model: What is a Promise?

A **Promise** is a built-in JavaScript object that acts as a placeholder / receipt for the **eventual result (or failure) of an asynchronous operation**.

Instead of passing callbacks into a third-party function (which causes Inversion of Control), an asynchronous function returns a Promise object to you.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        THE PROMISE CONTRACT                            │
│                                                                        │
│   • Once settled, its state and value are IMMUTABLE (Frozen forever).  │
│   • Callbacks attached via `.then()` are GUARANTEED to run             │
│     asynchronously as Microtasks, even if already settled.             │
│   • Can be resolved or rejected exactly ONCE. Duplicate calls are      │
│     silently ignored.                                                  │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The 3 Formal States of a Promise

According to the ECMAScript specification, a Promise exists in exactly one of three states at any given moment:

```
                          ┌─────────────────────┐
                          │       PENDING       │
                          │ (Initial State)     │
                          └──────────┬──────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │                                 │
           resolve(value)                      reject(reason)
                    │                                 │
                    ▼                                 ▼
         ┌─────────────────────┐           ┌─────────────────────┐
         │      FULFILLED      │           │      REJECTED       │
         │ (Result: `value`)   │           │ (Result: `reason`)  │
         └─────────────────────┘           └─────────────────────┘
                    │                                 │
                    └────────────────┬────────────────┘
                                     ▼
                          ┌─────────────────────┐
                          │       SETTLED       │
                          │ (Immutable Terminal)│
                          └─────────────────────┘
```

| State | Meaning | Outcome Data | Can Change Again? |
| :--- | :--- | :--- | :--- |
| **`pending`** | Operation is still in progress. | None | ✅ Can transition to fulfilled or rejected |
| **`fulfilled`** | Operation completed successfully. | Resolves to a **`value`** | ❌ **Locked forever (Settled)** |
| **`rejected`** | Operation failed or encountered an error. | Rejects with a **`reason`** | ❌ **Locked forever (Settled)** |

> **Settled**: An informal umbrella term meaning the promise has left `pending` and reached its final immutable terminal state (**either Fulfilled or Rejected**).

---

## 3) Resolution vs. Settlement (The Technical Difference)

A critical distinction frequently tested in senior interviews is the difference between **Resolved** and **Settled**:

- **Settled**: The promise is in its final state (`fulfilled` or `rejected`).
- **Resolved**: The promise's fate is sealed. 
  - If resolved with a **primitive value** (`resolve(42)`), it settles immediately to **`fulfilled`**.
  - If resolved with **another Promise** (`resolve(otherPromise)`), the outer promise adopts the state of `otherPromise`. It is *resolved* (cannot be changed by caller), but remains *pending* until `otherPromise` settles!

```js
const innerPromise = new Promise((resolve) => {
  setTimeout(() => resolve("Data from server"), 2000);
});

const outerPromise = new Promise((resolve) => {
  // outerPromise is RESOLVED immediately with innerPromise,
  // but remains PENDING for 2 seconds until innerPromise SETTLES!
  resolve(innerPromise);
});

outerPromise.then((data) => {
  console.log(data); // "Data from server" (Fires after 2s)
});
```

---

## 4) Anatomy of the Promise Constructor

```js
const myPromise = new Promise((resolve, reject) => {
  // 1. The Executor function executes SYNCHRONOUSLY on the Call Stack!
  console.log("Executor running synchronously...");

  const isSuccess = true;

  if (isSuccess) {
    resolve({ id: 101, username: "Shivam" }); // Fulfill
  } else {
    reject(new Error("Network connection lost")); // Reject
  }
});
```

---

### The 3 Golden Guarantees of the Executor:

#### 1. The Executor Runs Synchronously
The function passed to `new Promise(...)` runs **immediately during instantiation**, before `new Promise` returns:

```js
console.log("A");
new Promise(() => console.log("B"));
console.log("C");
// Output: A -> B -> C (B is completely synchronous!)
```

#### 2. Settlement is Idempotent (Cannot change once settled)
Calling `resolve` or `reject` multiple times has no effect after the first call:

```js
const p = new Promise((resolve, reject) => {
  resolve("First Call Wins!");
  reject(new Error("Ignored!"));
  resolve("Also Ignored!");
});

p.then((val) => console.log(val)); // Logs: "First Call Wins!"
```

#### 3. Automatic Error Catching (Throw -> Reject)
Any unhandled exception thrown inside the executor function is caught automatically and converted to a rejection:

```js
const risky = new Promise((resolve, reject) => {
  JSON.parse("INVALID_JSON{"); // Throws SyntaxError
});

risky.catch((err) => {
  console.log("Safely caught syntax error:", err.message); // Caught cleanly!
});
```

---

## 5) Consuming Promises: `.then()`, `.catch()`, `.finally()`

---

### A. `Promise.prototype.then(onFulfilled, onRejected)`
Attaches callbacks for fulfillment and rejection. Always returns a **brand-new Promise**:

```js
fetchUser(101).then(
  (user) => { console.log("User loaded:", user.name); }, // onFulfilled
  (err)  => { console.error("Load failed:", err.message); } // onRejected
);
```

---

### B. `Promise.prototype.catch(onRejected)`
Syntactic sugar for `.then(null, onRejected)`. Preferred in clean code because it catches errors thrown anywhere upstream in the promise chain:

```js
fetchUser(101)
  .then((user) => user.name.toUpperCase())
  .catch((err) => console.error("Handled error:", err.message));
```

---

### C. `Promise.prototype.finally(onFinally)`
Executes cleanup logic regardless of whether the promise was fulfilled or rejected:
- Does **not** receive arguments (`value` or `reason`).
- Passes through the upstream fulfillment value or rejection reason to downstream handlers.

```js
function loadDataWithSpinner() {
  showLoadingSpinner();

  fetchData()
    .then((data) => renderData(data))
    .catch((err) => showErrorToast(err.message))
    .finally(() => {
      // 🛡️ Always hide spinner regardless of success or failure!
      hideLoadingSpinner();
    });
}
```

---

## 6) Static Helper Methods & ES2024 `Promise.withResolvers()`

---

### 1. `Promise.resolve(value)`
Creates an immediately fulfilled promise. If `value` is already a Promise, it returns that Promise instance directly:

```js
const instant = Promise.resolve(42);
instant.then((v) => console.log(v)); // 42
```

---

### 2. `Promise.reject(reason)`
Creates an immediately rejected promise. 
> ⚠️ **Best Practice**: Always reject with an instance of `Error` (`new Error(...)`) rather than a raw string to preserve stack traces!

```js
const failed = Promise.reject(new Error("Unauthorized access"));
failed.catch((err) => console.error(err.stack));
```

---

### 3. `Promise.withResolvers()` (ES2024 / Modern Standard)
Extracts `resolve` and `reject` functions directly without nesting your logic inside an executor callback:

```js
// Modern ES2024 Pattern:
const { promise, resolve, reject } = Promise.withResolvers();

// Resolve or reject from anywhere outside!
setTimeout(() => resolve("Deferred resolution"), 1000);

promise.then((msg) => console.log(msg)); // "Deferred resolution"
```

---

## 7) What is a "Thenable"?

A **Thenable** is any JavaScript object or function that implements a `.then()` method conforming to the Promises/A+ specification.

JavaScript engine treats thenables seamlessly as promises:

```js
const customThenable = {
  then(resolve, reject) {
    resolve("I am an assimilated thenable!");
  }
};

Promise.resolve(customThenable).then((msg) => {
  console.log(msg); // "I am an assimilated thenable!"
});
```

---

## 8) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         PROMISE STATES CHEAT SHEET                         |
+───────────────────────────+────────────────────────────────────────────────+
| 3 States                  | `pending`, `fulfilled`, `rejected`.           |
| Settled                   | Reached terminal state (`fulfilled`/`rejected`)|
| Immutability              | Once settled, state and value never change.    |
| Executor Timing           | `new Promise(executor)` runs SYNCHRONOUSLY.    |
| Idempotency               | Only 1st `resolve`/`reject` call has effect.   |
| Exception Safety          | Uncaught errors in executor trigger `reject()`.|
| `.finally()`              | Runs on success & failure; receives no args.   |
| Reject Best Practice      | Always pass `new Error("message")`.            |
| ES2024 Helper             | `const { promise, resolve, reject } = Promise.withResolvers()`|
+───────────────────────────+────────────────────────────────────────────────+
```

# Callbacks and Inversion of Control in JavaScript

---

## 1) Core Mental Model: What is a Callback?

A **Callback** is a function passed as an argument into another function (a Higher-Order Function), intended to be invoked either immediately (Synchronous Callback) or at a later time when an operation finishes (Asynchronous Callback).

```
┌────────────────────────────────────────────────────────────────────────┐
│                        SYNCHRONOUS vs. ASYNCHRONOUS                    │
│                                                                        │
│   Synchronous Callback (Blocks & completes immediately):               │
│   [1, 2, 3].map((num) => num * 2);                                     │
│                                                                        │
│   Asynchronous Callback (Hands over execution to runtime/event loop):   │
│   setTimeout(() => console.log("Fired later"), 1000);                  │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The "Inversion of Control" (IoC) Dilemma

**Inversion of Control** is the single greatest architectural flaw of raw callback-based asynchronous programming.

When you write asynchronous callback code:
- **Part 1**: Runs now (initializes the request).
- **Part 2**: Runs later (the callback containing your continuation logic).

Instead of your code retaining control over when and how Part 2 executes, **you surrender total control of Part 2 to a third-party library, API, or external utility**.

```
┌────────────────────────────────────────────────────────────────────────┐
│                     THE INVERSION OF CONTROL GAP                       │
│                                                                        │
│   // Your Code                                                         │
│   analyticsTracker.trackPurchase(cartPayload, function () {            │
│     chargeCustomerCreditCard(); // ⚠️ Critical Business Logic!         │
│     redirectUserToSuccessPage();                                       │
│   });                                                                  │
│                                                                        │
│   You are TRUSTING analyticsTracker:                                   │
│   • Not to call the callback 0 times (Customer never charged!)         │
│   • Not to call the callback 5 times (Customer charged 5 times!)       │
│   • Not to call the callback synchronously when expecting async        │
│   • Not to swallow unhandled errors                                    │
└────────────────────────────────────────────────────────────────────────┘
```

---

### The 5 Trust Failures of Inversion of Control:

1. **Called Too Early**: Callback runs synchronously before setup completes.
2. **Called Too Late (or Never)**: Network hang or silent failure leaves your application hanging indefinitely.
3. **Called Too Many Times**: Accidental loop or duplicate event triggers duplicate charges or writes.
4. **Fails to Pass Parameters**: Essential error or payload arguments are omitted.
5. **Swallows Exceptions**: Errors thrown inside your callback are silently caught and hidden by the host library.

---

## 3) The Node.js Error-First Callback Pattern (Nodeback)

To standardize asynchronous error handling before Promises, Node.js established the **Error-First Callback Convention**:

1. The first parameter is strictly reserved for an `Error` object (or `null` / `undefined` if successful).
2. The second and subsequent parameters contain the success payload.

```js
import fs from "node:fs";

// Standard Error-First Callback
fs.readFile("./config.json", "utf-8", (err, data) => {
  // 1. Mandatory Error Guard (Must check 'err' first!)
  if (err) {
    console.error("Failed to read file:", err.message);
    return;
  }

  // 2. Process Success Data
  try {
    const config = JSON.parse(data);
    console.log("Database Host:", config.host);
  } catch (parseErr) {
    console.error("Invalid JSON format:", parseErr.message);
  }
});
```

---

## 4) "Unleashing Zalgo" (The Sync vs. Async Callback Bug)

In JavaScript folklore, **"Zalgo"** refers to an asynchronous API that unexpectedly runs **synchronously** in certain execution branches (e.g. when fetching from an in-memory cache).

```js
// ❌ CATASTROPHIC ANTI-PATTERN: Unleashing Zalgo (Mixed Sync/Async)
const cache = new Map();

function getUserData(userId, callback) {
  if (cache.has(userId)) {
    // ⚠️ SYNC EXECUTION on Cache Hit!
    callback(null, cache.get(userId));
  } else {
    // ⚠️ ASYNC EXECUTION on Cache Miss!
    fetch(`/api/users/${userId}`)
      .then((res) => res.json())
      .then((data) => {
        cache.set(userId, data);
        callback(null, data);
      });
  }
}

// Why Zalgo breaks code:
let isReady = false;
getUserData(101, (err, user) => {
  console.log("Is system ready?", isReady); 
  // On Cache Miss: Logs "true" (Runs async after isReady = true)
  // On Cache Hit : Logs "false" ⚠️ (Runs sync BEFORE isReady = true!)
});
isReady = true;
```

---

### The Golden Rule of Zalgo Prevention:
> **An API must be 100% synchronous OR 100% asynchronous. It must NEVER mix both!**

#### Fixing Zalgo with `queueMicrotask`:
```js
function getUserDataSafe(userId, callback) {
  if (cache.has(userId)) {
    const cachedData = cache.get(userId);
    // Force cache hit to be asynchronous via a microtask!
    queueMicrotask(() => callback(null, cachedData));
  } else {
    fetch(`/api/users/${userId}`)
      .then((res) => res.json())
      .then((data) => {
        cache.set(userId, data);
        callback(null, data);
      });
  }
}
```

---

## 5) Defensive Callback Programming (Restoring Trust)

If you must work with legacy callback-based APIs, use defensive wrapper guards:

```js
// Guard 1: Ensure Callback is Called Exactly ONCE
function once(fn) {
  let called = false;
  let result;
  return function (...args) {
    if (!called) {
      called = true;
      result = fn.apply(this, args);
      fn = null; // Free reference for Garbage Collection
    }
    return result;
  };
}

// Guard 2: Timeout Protection (Guarantee Callback is Called)
function withTimeout(fn, timeoutMs, errorMessage) {
  let timerId;
  const wrapped = once((...args) => {
    clearTimeout(timerId);
    fn(...args);
  });

  timerId = setTimeout(() => {
    wrapped(new Error(errorMessage || `Callback timed out after ${timeoutMs}ms`));
  }, timeoutMs);

  return wrapped;
}
```

---

## 6) Promisification: Solving Inversion of Control Forever

**Promises** were introduced specifically to solve Inversion of Control. 

Instead of passing your callback function **into** an external utility, the utility returns a **Promise token**, allowing you to attach `.then()` handlers while **retaining full control over your execution flow**.

---

### Writing a Custom `promisify()` Utility:

```js
/**
 * Converts any standard Node.js Error-First Callback function into a Promise
 */
function customPromisify(originalFn) {
  return function (...args) {
    return new Promise((resolve, reject) => {
      // Append standard (err, result) callback to arguments list
      originalFn.call(this, ...args, (err, result) => {
        if (err) {
          reject(err);
        } else {
          resolve(result);
        }
      });
    });
  };
}

// Verification:
import fs from "node:fs";

const readFilePromise = customPromisify(fs.readFile);

// Clean, inverted-control-free execution:
async function loadConfig() {
  try {
    const data = await readFilePromise("./package.json", "utf-8");
    console.log("Package loaded successfully. Size:", data.length);
  } catch (err) {
    console.error("Promise rejected:", err.message);
  }
}

loadConfig();
```

---

## 7) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                     CALLBACKS & IOC QUICK MATRIX                           |
+───────────────────────────+────────────────────────────────────────────────+
| Callback Definition       | Function passed to run now or later.           |
| Inversion of Control      | Surrendering control of your continuation code.|
| Trust Issues              | Called 0 times, multiple times, or sync/async. |
| Nodeback Convention       | `callback(err, data)` -> Always check err first|
| Unleashing Zalgo          | Mixing sync and async branches in one function.|
| Zalgo Solution            | Use `queueMicrotask` to guarantee async.       |
| Ultimate Solution         | Promisification (`customPromisify` / Promises).|
+───────────────────────────+────────────────────────────────────────────────+
```

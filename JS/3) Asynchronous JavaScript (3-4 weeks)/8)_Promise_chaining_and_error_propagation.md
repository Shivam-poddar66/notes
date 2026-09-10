# Promise Chaining and Error Propagation in JavaScript

---

## 1) Core Mental Model: How Promise Chaining Works

The true power of Promises lies in **Promise Chaining**. 

Every call to `.then()`, `.catch()`, or `.finally()` returns a **brand-new Promise instance**, allowing you to construct flat, readable, asynchronous pipelines.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        PROMISE PIPELINE FLOW                           │
│                                                                        │
│   fetchUser(101)                                                       │
│        │                                                               │
│        ▼ (Returns User object)                                         │
│   .then(user => fetchOrders(user.id)) ──▶ (Waits for fetchOrders)      │
│        │                                                               │
│        ▼ (Returns Orders array)                                        │
│   .then(orders => filterRecent(orders)) ──▶ (Pure transformation)     │
│        │                                                               │
│        ▼ (If any error occurs anywhere upstream)                       │
│   .catch(err => handleGracefully(err))                                 │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The 4 Rules of Value Propagation in `.then()`

What a `.then()` handler returns determines the state of the downstream promise:

| Handler Return Statement | Downstream Promise Behavior |
| :--- | :--- |
| **Returns a Primitive or Object** (`return 42` or `return { name: "A" }`) | Downstream promise fulfills immediately with that value. |
| **Returns another Promise** (`return fetch("/data")`) | Downstream promise **waits** for the inner promise to settle and adopts its state & value. |
| **Throws an Error** (`throw new Error("Failed")`) | Downstream promise **rejects immediately** with the error. |
| **Returns nothing (Omitted `return`)** | Downstream promise fulfills immediately with **`undefined`**! |

---

## 3) The #1 Promise Bug: The "Missing Return" Trap

Forgetting the `return` keyword inside a `.then()` callback breaks the pipeline chain:

```js
// ❌ CATASTROPHIC BUG: Missing return statement!
getUser(101)
  .then((user) => {
    // ⚠️ Forgot 'return'! The promise returned by getProfile() is orphaned!
    getProfile(user.id); 
  })
  .then((profile) => {
    // 'profile' is UNDEFINED because previous .then returned nothing!
    console.log("Avatar:", profile.avatar); // ❌ TypeError: Cannot read properties of undefined
  });

// ✅ FIXED: Explicit return statement
getUser(101)
  .then((user) => {
    return getProfile(user.id); // ✅ Downstream waits for getProfile() to settle
  })
  .then((profile) => {
    console.log("Avatar:", profile.avatar); // ✅ Works!
  });
```

---

## 4) Error Propagation & Bubbling (The Marble Drop Model)

Errors in a promise chain behave like a marble dropped down a funnel: **they bubble down past all `.then()` handlers until they hit the nearest `.catch()` block**.

```
   Step 1: authenticate() ──▶ FULFILLED
             │
   Step 2: fetchAccount() ──▶ ❌ THROWS ERROR!
             │
             │ (Bypasses Step 3)
             ▼
   Step 3: calculateTax() ──▶ [ SKIPPED ]
             │
             ▼
   Step 4: .catch(error)  ──▶ 🛡️ CAUGHT & HANDLED!
             │
             ▼
   Step 5: .finally()     ──▶ 🧹 ALWAYS RUNS CLEANUP
```

---

### A. Recovering from Errors in a Chain (Fallback Values)
If a `.catch()` handler handles an error and returns a fallback value, the promise chain **recovers** and continues fulfilling downstream `.then()` handlers:

```js
fetchUserPreferences(userId)
  .catch((err) => {
    console.warn("Failed to load user preferences. Using defaults.", err.message);
    // 🛡️ Return default fallback object to recover the chain:
    return { theme: "light", notifications: false };
  })
  .then((preferences) => {
    // ✅ Receives either server preferences OR default preferences!
    applyTheme(preferences.theme);
  });
```

---

### B. Re-throwing Errors Inside `.catch()`
If an error cannot be recovered from, re-throw it to keep downstream promises in a rejected state:

```js
processPayment(orderId)
  .catch((err) => {
    logAuditError(err);
    // Re-throw so higher-level caller knows payment failed!
    throw new Error(`Payment processing aborted: ${err.message}`);
  });
```

---

## 5) The Nested Promise Anti-Pattern (Re-creating Callback Hell)

Developers transitioning from callbacks often nest promises inside `.then()` handlers instead of returning them:

```js
// ❌ ANTI-PATTERN: Nested Promises (Callback Hell with Promises!)
fetchUser(id).then((user) => {
  fetchOrders(user.id).then((orders) => {
    fetchInvoice(orders[0].id).then((invoice) => {
      console.log(invoice);
    });
  });
});

// ✅ REFACTORED: Flat Promise Pipeline
fetchUser(id)
  .then((user) => fetchOrders(user.id))
  .then((orders) => fetchInvoice(orders[0].id))
  .then((invoice) => console.log(invoice))
  .catch((err) => console.error("Pipeline failed:", err));
```

---

## 6) `.then(onSuccess, onError)` vs. `.catch()`: The Critical Difference

A common mistake is using the two-argument form of `.then(onSuccess, onError)`:

```js
// ❌ FLAWED: .then(onSuccess, onError)
fetchData().then(
  (data) => {
    processData(data); // ⚠️ If processData throws an error, onError DOES NOT CATCH IT!
  },
  (err) => {
    console.error("Caught error:", err); // Only catches errors from fetchData(), NOT processData()!
  }
);

// ✅ SAFE: .then(onSuccess).catch(onError)
fetchData()
  .then((data) => {
    processData(data); // If this throws...
  })
  .catch((err) => {
    console.error("Caught error:", err); // ...it IS caught here!
  });
```

---

## 7) Unhandled Promise Rejections & Process Crashing

If a promise rejects and has no `.catch()` handler attached anywhere along its chain, it triggers an **Unhandled Promise Rejection**:

- **In Modern Node.js (v15+)**: Unhandled rejections terminate the process with exit code `1`!
- **In Browsers**: Logs a red error to the developer console.

```js
// Global Fallback Handlers (Last Line of Defense)

// 1. Browser:
window.addEventListener("unhandledrejection", (event) => {
  console.error("Global unhandled rejection:", event.reason);
  event.preventDefault(); // Prevents default console error logging
});

// 2. Node.js:
process.on("unhandledRejection", (reason, promise) => {
  console.error("[CRITICAL] Unhandled Rejection at:", promise, "reason:", reason);
  // Graceful shutdown / alert Sentry
});
```

---

## 8) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                     PROMISE CHAINING QUICK MATRIX                          |
+───────────────────────────+────────────────────────────────────────────────+
| Chaining Rule             | Every `.then()` returns a NEW Promise.         |
| Missing Return Trap       | Always `return` inside `.then()` callbacks.    |
| Error Bubbling            | Errors bypass `.then()` until nearest `.catch`.|
| Error Recovery            | Returning a value from `.catch()` recovers.    |
| Two-Argument `.then` Trap | Use `.catch()`; `.then(ok, err)` misses ok errs|
| Flattening                | Return inner promise instead of nesting .then()|
| Cleanup Handler           | `.finally()` always runs; receives no args.    |
+───────────────────────────+────────────────────────────────────────────────+
```

# `async`/`await` Control Flow and Error Handling in JavaScript

---

## 1) Core Mental Model: Syntactic Sugar over Generators & Promises

Introduced in ES2017 (ES8), **`async/await`** revolutionized asynchronous JavaScript. 

Under the hood, **`async/await` is syntactic sugar over Promises and Generators (`function*` + `yield`)**. It allows you to write non-blocking asynchronous code using standard synchronous control flow constructs: `for` loops, `if/else` statements, and `try/catch/finally` blocks.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        HOW ASYNC/AWAIT WORKS                           │
│                                                                        │
│   async function loadData() {                                          │
│     console.log("1: Start");                                           │
│     const data = await fetch("/api"); ──▶ Pauses function execution;   │
│                                           yields back to Event Loop.   │
│     console.log("2: Resumed");        ──▶ Runs as a MICROTASK once     │
│     return data.json();                   the fetch promise fulfills.  │
│   }                                                                    │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The `async` Keyword: 3 Automatic Guarantees

Declaring a function with `async` alters its behavior in three fundamental ways:

1. **Always Returns a Promise**:
   - If you return a primitive value (`return 42`), the engine automatically wraps it in `Promise.resolve(42)`.
   - If you return a Promise, it returns that Promise's resolution.
2. **Converts Thrown Exceptions to Rejections**:
   - Any synchronous `throw new Error(...)` inside an `async` function is automatically converted into `Promise.reject(error)`.
3. **Enables the `await` Operator**:
   - Allows using `await` inside the function body.

```js
async function calculate(val) {
  if (val < 0) throw new Error("Negative value not allowed");
  return val * 2;
}

calculate(10).then((res) => console.log(res)); // 20
calculate(-5).catch((err) => console.error(err.message)); // "Negative value not allowed"
```

---

## 3) The `await` Operator: Mechanics & Execution

The `await` operator pauses the execution of the `async` function until the awaited expression settles:

- **If Fulfilled**: Unwraps the fulfilled value and assigns it (`const user = await getUser()`).
- **If Rejected**: **Throws the rejection reason as a standard catchable JavaScript error!**
- **If Awaiting Non-Promise**: Automatically wraps it via `Promise.resolve(val)` (`await 42` evaluates to `42`).

> **Microtask Resumption**: The code immediately following an `await` statement is scheduled in the **Microtask Queue**. It will not execute until the Call Stack clears!

---

## 4) Top-Level `await` (ES2022 Standard)

In modern ECMAScript Modules (`type="module"` or `.mjs`), you can use `await` outside of `async` functions at the top level of a file:

```js
// --- dbClient.mjs (Top-Level Await) ---
import { connectDatabase } from "./db.js";

// Top-level await halts module evaluation until connection is established:
export const connection = await connectDatabase(process.env.DB_URI);
console.log("Database connected. Module ready for export.");
```

---

## 5) Error Handling Architectural Patterns

---

### Pattern 1: Standard `try...catch...finally`

```js
async function fetchUserProfile(userId) {
  showLoader();

  try {
    const response = await fetch(`/api/users/${userId}`);

    // HTTP 404 / 500 do NOT reject fetch(); must check response.ok!
    if (!response.ok) {
      throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
    }

    const user = await response.json();
    return user;
  } catch (err) {
    console.error(`[fetchUserProfile] Failed for user #${userId}:`, err.message);
    // Graceful fallback or rethrow
    throw err;
  } finally {
    // 🛡️ Always hide loader regardless of success or exception!
    hideLoader();
  }
}
```

---

### Pattern 2: The "Go-Style / Tuple" Error Handling Pattern

To avoid messy deeply-nested `try/catch` blocks across multiple asynchronous calls, use a tuple wrapper:

```js
// Utility wrapper: Returns [error, data]
export async function to(promise) {
  try {
    const data = await promise;
    return [null, data];
  } catch (err) {
    return [err, null];
  }
}

// Clean sequential usage without try/catch:
async function handleOrder(orderId) {
  const [orderErr, order] = await to(fetchOrder(orderId));
  if (orderErr) return showNotification("Failed to load order");

  const [paymentErr, receipt] = await to(processPayment(order.total));
  if (paymentErr) return showNotification("Payment failed");

  showNotification(`Order successful! Receipt #${receipt.id}`);
}
```

---

### Pattern 3: Inline Default Fallback via `.catch()`

For non-critical background data, attach `.catch()` directly to the awaited expression:

```js
async function renderDashboard(userId) {
  // If user settings fail to load, fall back to default settings without try/catch!
  const settings = await fetchSettings(userId).catch(() => ({ theme: "light" }));
  
  applyTheme(settings.theme);
}
```

---

## 6) Sequential Waterfall vs. Parallel Concurrency

One of the most common performance bugs in modern JavaScript is creating **accidental sequential waterfalls**:

```
                  SEQUENTIAL WATERFALL (SLOW: 6 seconds total)
┌────────────────────────────────────────────────────────────────────────┐
│  const user    = await fetchUser();    // ⏳ Takes 2s                 │
│  const orders  = await fetchOrders();  // ⏳ Takes 2s (Waited for user)│
│  const metrics = await fetchMetrics(); // ⏳ Takes 2s (Waited for orders)│
└────────────────────────────────────────────────────────────────────────┘

                  PARALLEL CONCURRENCY (FAST: 2 seconds total)
┌────────────────────────────────────────────────────────────────────────┐
│  const [user, orders, metrics] = await Promise.all([                   │
│    fetchUser(),                                                        │
│    fetchOrders(),                                                      │
│    fetchMetrics()                                                      │
│  ]); // ⚡ All 3 requests fire simultaneously across network!          │
└────────────────────────────────────────────────────────────────────────┘
```

```js
// ❌ SLOW: Unnecessary sequential waterfall
async function loadUserDataSlow(userId) {
  const profile = await fetchProfile(userId); // 1s
  const friends = await fetchFriends(userId); // 1s
  const photos = await fetchPhotos(userId);   // 1s
  return { profile, friends, photos }; // Total: 3 seconds!
}

// ✅ FAST: Parallel execution using Promise.all
async function loadUserDataFast(userId) {
  const [profile, friends, photos] = await Promise.all([
    fetchProfile(userId),
    fetchFriends(userId),
    fetchPhotos(userId)
  ]);
  return { profile, friends, photos }; // Total: 1 second!
}
```

---

## 7) The `async` in Loops Trap (`forEach` vs `for...of` vs `Promise.all`)

---

### Trap: `Array.prototype.forEach` is NOT Async-Aware!

```js
// ❌ BROKEN: forEach does NOT wait for promises!
async function processAll(ids) {
  ids.forEach(async (id) => {
    await sendNotification(id);
  });
  console.log("All done!"); // ⚠️ Logs IMMEDIATELY before notifications finish!
}
```

---

### Correct Approach A: Sequential Execution (One by One)
Use a standard `for...of` loop when tasks **must run in strict sequence**:

```js
async function processSequentially(ids) {
  for (const id of ids) {
    await sendNotification(id); // Waits for each notification before starting next
  }
  console.log("All notifications sent in sequence.");
}
```

---

### Correct Approach B: Concurrent Execution (All in Parallel)
Use `map` combined with `Promise.all` when tasks **can run simultaneously**:

```js
async function processConcurrently(ids) {
  await Promise.all(ids.map((id) => sendNotification(id)));
  console.log("All notifications sent concurrently!");
}
```

---

## 8) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                      ASYNC / AWAIT QUICK MATRIX                            |
+───────────────────────────+────────────────────────────────────────────────+
| `async` Return Value      | Always wrapped in a Promise.                   |
| `await` Behavior          | Unwraps fulfilled value; throws on rejection.  |
| Resumption Queue          | Code after `await` resumes as a Microtask.     |
| Top-Level Await           | Supported natively in ES Modules (`.mjs`).     |
| Waterfall Prevention      | Use `await Promise.all([...])` for parallelism.|
| Loop Warning              | Never use `await` inside `forEach()`.          |
| Sequential Loops          | Use `for (const item of items) { await ... }`. |
| Error Handling            | `try...catch...finally` or `to(promise)` tuple.|
+───────────────────────────+────────────────────────────────────────────────+
```

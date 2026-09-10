# Phase 3 Quick Revision Cheat Sheet: Asynchronous JavaScript

A dense, high-yield reference manual summarizing core mental models, queue priorities, spec behaviors, combinator formulas, HTTP protocol mechanics, and production code snippets for rapid revision before technical interviews.

---

## 1. Async Runtime & Event Loop Cheatsheet

```
+===================================================================================================+
|                                    EVENT LOOP PRIORITY ORDER                                      |
|                                                                                                   |
|  1. Synchronous Call Stack (Always executes to completion first)                                  |
|                                  |                                                                |
|                                  v                                                                |
|  2. Process NextTick Queue (Node.js ONLY: process.nextTick drains BEFORE microtasks)             |
|                                  |                                                                |
|                                  v                                                                |
|  3. Microtask Queue (Drains completely until empty between every task):                           |
|     - Promise callbacks (.then, .catch, .finally)                                                 |
|     - async/await resume points                                                                   |
|     - queueMicrotask()                                                                            |
|     - MutationObserver                                                                            |
|                                  |                                                                |
|                                  v                                                                |
|  4. UI Render Opportunity (Browser: requestAnimationFrame -> Style -> Layout -> Paint)           |
|                                  |                                                                |
|                                  v                                                                |
|  5. Macrotask Queue (ONE task processed per Event Loop turn):                                    |
|     - setTimeout / setInterval                                                                    |
|     - setImmediate (Node.js)                                                                      |
|     - I/O events (network sockets, file descriptors)                                              |
|     - UI Event Handlers (click, input, scroll)                                                    |
+===================================================================================================+
```

### Critical Execution Rules
1. **Microtasks Starve Macrotasks**: If a microtask enqueues another microtask recursively, the Event Loop will **never** proceed to render or execute `setTimeout` callbacks.
2. **`async/await` is Syntactic Sugar**: Code *before* the first `await` is 100% synchronous. Code *after* the `await` is wrapped inside a microtask (`Promise.resolve(val).then(...)`).

---

## 2. Promise States, Mechanics & Combinators

### A. Promise States
- **`pending`**: Initial state; in-flight.
- **`fulfilled`**: Resolved with a value (terminal).
- **`rejected`**: Failed with a reason (terminal).
- **`settled`**: Either fulfilled or rejected (immutable).

### B. Promise Combinators Matrix

| Combinator | Resolves When... | Rejects When... | Ideal Production Use Case |
| :--- | :--- | :--- | :--- |
| **`Promise.all`** | **ALL** promises fulfill (returns array of values) | **ANY** single promise rejects (fail-fast) | Dependent parallel data fetching (dashboard tiles) |
| **`Promise.allSettled`** | **ALL** promises settle (never rejects) | Never | Bulk processing where partial failure is allowed |
| **`Promise.race`** | **FIRST** promise settles (fulfilled OR rejected) | First promise rejects | Quickest ping / raw timeout racing |
| **`Promise.any`** | **FIRST** promise fulfills | **ALL** promises reject (`AggregateError`) | Redundant CDN fallback or multi-mirror downloads |

---

## 3. Polyfill Quick Reference (Mental Formulas)

```javascript
// Promise.all (Fail-Fast)
Promise.myAll = (promises) => new Promise((resolve, reject) => {
  const items = Array.from(promises);
  if (!items.length) return resolve([]);
  const results = [];
  let completed = 0;
  items.forEach((p, i) => Promise.resolve(p).then(val => {
    results[i] = val;
    if (++completed === items.length) resolve(results);
  }, reject));
});

// Promise.allSettled (Never Rejects)
Promise.myAllSettled = (promises) => Promise.all(
  Array.from(promises).map(p => 
    Promise.resolve(p).then(
      value => ({ status: 'fulfilled', value }),
      reason => ({ status: 'rejected', reason })
    )
  )
);

// Promise.race (First Settle Wins)
Promise.myRace = (promises) => new Promise((resolve, reject) => {
  Array.from(promises).forEach(p => Promise.resolve(p).then(resolve, reject));
});

// Promise.any (First Fulfill Wins)
Promise.myAny = (promises) => new Promise((resolve, reject) => {
  const items = Array.from(promises);
  if (!items.length) return reject(new AggregateError([], 'All promises rejected'));
  const errors = [];
  let rejectedCount = 0;
  items.forEach((p, i) => Promise.resolve(p).then(resolve, err => {
    errors[i] = err;
    if (++rejectedCount === items.length) reject(new AggregateError(errors, 'All rejected'));
  }));
});
```

---

## 4. HTTP Protocol, Status Codes & Caching Summary

### Semantic Status Codes
- **200 OK**: Normal response with body.
- **201 Created**: Resource created (POST/PUT).
- **204 No Content**: Successful action with empty body (DELETE/PUT). **Do NOT call `res.json()`!**
- **304 Not Modified**: Conditional cache hit (ETag matched `If-None-Match`).
- **400 Bad Request**: Malformed syntax or invalid parameters.
- **401 Unauthorized**: Missing/invalid authentication token.
- **403 Forbidden**: Authenticated, but user lacks permissions.
- **404 Not Found**: Resource does not exist.
- **409 Conflict**: State conflict (duplicate key/version collision).
- **422 Unprocessable Entity**: Valid syntax, but domain validation failed.
- **429 Too Many Requests**: Rate limit exceeded (check `Retry-After`).
- **500 Internal Server Error**: Unhandled server crash.
- **502 / 503 / 504**: Gateway or upstream infrastructure outages (retryable).

### Caching Directives
- `max-age=N`: Cache is fresh for $N$ seconds.
- `no-cache`: Must revalidate ETag with server before using cached copy.
- `no-store`: Never cache anywhere (use for sensitive banking data).
- `immutable`: File will never change while fresh (use with hashed assets).
- `stale-while-revalidate=N`: Serve cached data immediately, revalidate in background.

---

## 5. Production Reliability Snippets

### A. Cancellable Fetch with Per-Request Timeout
```javascript
async function fetchWithTimeout(url, { timeoutMs = 5000, signal, ...opts } = {}) {
  const timeoutSignal = AbortSignal.timeout(timeoutMs);
  const combinedSignal = signal ? AbortSignal.any([signal, timeoutSignal]) : timeoutSignal;

  const res = await fetch(url, { ...opts, signal: combinedSignal });
  if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
  return res.status === 204 ? null : res.json();
}
```

### B. Exponential Backoff with Full Jitter
```javascript
async function retryWithJitter(taskFn, retries = 3, baseDelay = 200, maxDelay = 5000) {
  for (let i = 0; i <= retries; i++) {
    try {
      return await taskFn();
    } catch (err) {
      if (i === retries) throw err;
      const delay = Math.floor(Math.random() * Math.min(maxDelay, baseDelay * Math.pow(2, i)));
      await new Promise(r => setTimeout(r, delay));
    }
  }
}
```

### C. Concurrency Limiter (Semaphore / `p-limit`)
```javascript
function pLimit(concurrency) {
  let active = 0;
  const queue = [];

  const next = () => {
    active--;
    if (queue.length > 0) queue.shift()();
  };

  const run = async (fn, resolve, reject) => {
    active++;
    try { resolve(await fn()); } 
    catch (err) { reject(err); } 
    finally { next(); }
  };

  return (fn) => new Promise((resolve, reject) => {
    const task = () => run(fn, resolve, reject);
    active < concurrency ? task() : queue.push(task);
  });
}
```

---

## 6. Top 5 Fatal Anti-Patterns to Avoid

1. ❌ **`array.forEach(async fn)`**: `forEach` fires synchronously without awaiting returned promises. Use `for...of` or `Promise.all(map)`.
2. ❌ **Blind `fetch()` Catch**: `fetch()` resolves on 404/500! Always check `if (!res.ok) throw Error(...)`.
3. ❌ **Reading Body Multiple Times**: `res.json()` consumes the stream. Clone `res.clone()` if multiple reads are needed.
4. ❌ **Unprotected Autocomplete Search**: Always cancel previous requests with `AbortController` to prevent stale out-of-order responses from overwriting fresh input.
5. ❌ **Floating Unhandled Promises**: Always attach `.catch()` to background promises to avoid `UnhandledPromiseRejection`.

---

## 7. 4-State UI Lifecycle Formula

```
           +---------------------------------------------+
           |               1. IDLE STATE                 |
           +---------------------------------------------+
                                  | (User action / fetch initiated)
                                  v
           +---------------------------------------------+
           |              2. LOADING STATE               |
           |      (Show spinner / skeleton view)         |
           +---------------------------------------------+
                        /                     \
        (HTTP 200 OK)  /                       \ (HTTP 4xx/5xx or Network Drop)
                      v                         v
  +-----------------------+         +-----------------------+
  |    3. SUCCESS STATE   |         |    4. ERROR STATE     |
  |  (Render UI Domain)   |         |  (Render Retry Action)|
  +-----------------------+         +-----------------------+
```

---

## 8. Phase 3 Mastery Self-Audit

- [x] Can explain Call Stack vs Web APIs vs Task vs Microtask queue.
- [x] Know why `process.nextTick` runs before `Promise.then`.
- [x] Can implement all 4 Promise combinators from memory.
- [x] Know how to build a sliding window concurrency worker pool.
- [x] Understand HTTP idempotency across `GET`, `POST`, `PUT`, `PATCH`, `DELETE`.
- [x] Can implement exponential backoff with full jitter and `AbortController`.
- [x] Have built the 3 Phase Mini-Projects (Weather App, GitHub Finder, Promise Utility Library).

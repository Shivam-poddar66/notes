# Asynchronous Debugging Checklist & Anti-Pattern Encyclopedia

Debugging asynchronous JavaScript is notoriously tricky because asynchronous bugs do not manifest with simple, linear call-stack traces. An un-awaited promise, a swallowed rejection, or a typeahead race condition can silently corrupt application state minutes after the offending code has executed.

This guide provides an enterprise **Debugging Decision Tree**, an **Encyclopedia of Top 10 Asynchronous Anti-Patterns** (with concrete Before/After fixes), and a **Senior Engineer's Diagnostic Checklist**.

---

## 1. The Async Debugging Mental Decision Tree

```
                           [ Asynchronous Defect Occurs ]
                                         |
               +-------------------------+-------------------------+
               |                                                   |
    [ No Data / Infinite Stall ]                         [ Wrong / Stale Data ]
               |                                                   |
       - Forgot 'await'?                                   - Race condition / out-of-order response?
       - Promise never resolved/rejected?                  - Array.forEach used instead of for...of?
       - Deadlock in semaphore / lock?                     - Stale closure in React useEffect / hook?
               |                                                   |
               +-------------------------+-------------------------+
                                         |
                               [ Exception / Crash ]
                                         |
                       - Unhandled Promise Rejection?
                       - fetch() didn't check res.ok?
                       - JSON.parse failed on HTML 502 page?
                       - Double-read on response body stream?
```

---

## 2. Top 10 Asynchronous Anti-Patterns & Solutions

### Anti-Pattern 1: The Missing `await` (Silent Promise Injection)

```javascript
// ❌ BUG: Returns [Promise, Promise] instead of values. Logic silently passes truthy objects!
async function getUserRoles(userId) {
  const roles = fetchRoles(userId); // Missing await! 'roles' is a Promise object, not an Array
  if (roles.length > 0) { // roles.length is undefined! Condition evaluates to FALSE
    return roles;
  }
  return ['guest'];
}

// ✅ FIXED: Await the Promise
async function getUserRolesFixed(userId) {
  const roles = await fetchRoles(userId);
  if (roles.length > 0) {
    return roles;
  }
  return ['guest'];
}
```

---

### Anti-Pattern 2: The `Array.prototype.forEach` Async Trap

```javascript
// ❌ BUG: forEach executes callbacks synchronously without awaiting them.
// 'saveAll' returns BEFORE files are written!
async function saveAll(files) {
  files.forEach(async (file) => {
    await fs.writeFile(file.name, file.content);
  });
  console.log("All files saved!"); // Prints immediately!
}

// ✅ FIXED: Sequential (for...of) or Concurrent (Promise.all + map)
async function saveAllFixed(files) {
  await Promise.all(files.map(file => fs.writeFile(file.name, file.content)));
  console.log("All files saved!");
}
```

---

### Anti-Pattern 3: Assuming `fetch()` Rejects on HTTP 4xx / 5xx

```javascript
// ❌ BUG: 404 Not Found or 500 Server Error resolves successfully.
// res.json() parses server error page and corrupts application state.
async function loadUserData(userId) {
  try {
    const res = await fetch(`/api/users/${userId}`);
    const data = await res.json();
    return data;
  } catch (err) {
    console.error("Network failed", err); // Never caught for 404/500!
  }
}

// ✅ FIXED: Explicitly inspect res.ok
async function loadUserDataFixed(userId) {
  const res = await fetch(`/api/users/${userId}`);
  if (!res.ok) {
    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
  }
  return res.json();
}
```

---

### Anti-Pattern 4: Race Conditions in Typeahead / Search Inputs

```javascript
// ❌ BUG: Request 1 ("ca") might resolve AFTER Request 2 ("cats"), displaying stale "ca" results.
let searchResults = [];
async function onSearchInput(query) {
  const res = await fetch(`/api/search?q=${query}`);
  searchResults = await res.json(); // Stale overwrite!
  render(searchResults);
}

// ✅ FIXED: Cancel previous request via AbortController
let activeController = null;
async function onSearchInputFixed(query) {
  if (activeController) activeController.abort();
  activeController = new AbortController();

  try {
    const res = await fetch(`/api/search?q=${query}`, { signal: activeController.signal });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const results = await res.json();
    render(results);
  } catch (err) {
    if (err.name !== 'AbortError') console.error(err);
  }
}
```

---

### Anti-Pattern 5: Swallowing Errors with Empty `catch` Blocks

```javascript
// ❌ BUG: Swallows error; caller receives 'undefined' without knowing operation failed.
async function getAuthToken() {
  try {
    return await api.fetchToken();
  } catch (err) {
    // Empty catch!
  }
}

// ✅ FIXED: Log, enrich, and re-throw or return explicit Result object
async function getAuthTokenFixed() {
  try {
    return await api.fetchToken();
  } catch (err) {
    console.error("Token acquisition failed:", err);
    throw new Error(`Authentication failure: ${err.message}`);
  }
}
```

---

### Anti-Pattern 6: Double Consumption of `Response` Body Stream

```javascript
// ❌ BUG: ReadableStream can only be consumed once!
// Throws: TypeError: Failed to execute 'json' on 'Response': body stream already read
async function handleResponse(res) {
  const text = await res.text();
  console.log("Raw Response:", text);
  const data = await res.json(); // CRASH!
  return data;
}

// ✅ FIXED: Parse JSON from text string or clone response
async function handleResponseFixed(res) {
  const text = await res.text();
  console.log("Raw Response:", text);
  return JSON.parse(text);
}
```

---

### Anti-Pattern 7: Dangling Timer Memory Leaks in Custom Promises

```javascript
// ❌ BUG: If targetPromise resolves early, setTimeout remains alive in memory until expiry!
function timeoutLeak(targetPromise, ms) {
  return Promise.race([
    targetPromise,
    new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), ms))
  ]);
}

// ✅ FIXED: Clear timer in finally block
async function timeoutClean(targetPromise, ms) {
  let timer;
  const timeoutPromise = new Promise((_, reject) => {
    timer = setTimeout(() => reject(new Error('Timeout')), ms);
  });

  try {
    return await Promise.race([targetPromise, timeoutPromise]);
  } finally {
    clearTimeout(timer); // Reclaims memory immediately
  }
}
```

---

### Anti-Pattern 8: Unintentional Async Waterfalls

```javascript
// ❌ SLOW: 3 independent calls run sequentially (~600ms total latency)
async function getDashboardData() {
  const profile = await fetchProfile();   // 200ms
  const metrics = await fetchMetrics();   // 200ms
  const news    = await fetchNews();      // 200ms
  return { profile, metrics, news };
}

// ✅ FAST: Parallel execution (~200ms total latency)
async function getDashboardDataFixed() {
  const [profile, metrics, news] = await Promise.all([
    fetchProfile(),
    fetchMetrics(),
    fetchNews()
  ]);
  return { profile, metrics, news };
}
```

---

### Anti-Pattern 9: Unhandled Rejections in Floating Promises

```javascript
// ❌ BUG: Fire-and-forget promise causes unhandled rejection if background sync fails
function onSubmit(data) {
  saveLocal(data);
  syncToCloud(data); // Floating promise with NO await and NO .catch()!
}

// ✅ FIXED: Attach explicit error handler to floating promises
function onSubmitFixed(data) {
  saveLocal(data);
  syncToCloud(data).catch(err => {
    console.error("Background sync failed:", err);
    reportTelemetry(err);
  });
}
```

---

### Anti-Pattern 10: Retrying Non-Idempotent `POST` Requests

```javascript
// ❌ DANGEROUS BUG: Retrying charge card on network glitch can charge customer 3 times!
async function checkout(cart) {
  return retry(() => fetch('/api/charge', {
    method: 'POST',
    body: JSON.stringify(cart)
  }));
}

// ✅ FIXED: Attach unique Idempotency Key
async function checkoutFixed(cart) {
  const idempotencyKey = crypto.randomUUID();
  return retry(() => fetch('/api/charge', {
    method: 'POST',
    headers: { 'Idempotency-Key': idempotencyKey },
    body: JSON.stringify(cart)
  }));
}
```

---

## 3. The 10-Point Senior Async Debugging Checklist

When investigating an asynchronous bug, systematically verify these 10 items:

```markdown
 [ ] 1. PROMISE CONSUMPTION: Is every Promise-returning function awaited or chained with .then()/.catch()?
 [ ] 2. ARRAY ITERATION: Are async operations inside loops using `for...of` (sequential) or `Promise.all(map)` (parallel) instead of `forEach`?
 [ ] 3. HTTP STATUS VERIFICATION: Does the fetch handler explicitly check `if (!res.ok)` before reading payload?
 [ ] 4. STREAM BODY USAGE: Is the response body being read only once?
 [ ] 5. RACE CONDITIONS & CANCEL: Are rapid user inputs protected by `AbortController` or sequence generation tokens?
 [ ] 6. UNHANDLED REJECTIONS: Are all floating/background promises guarded with `.catch()`?
 [ ] 7. TIMER TEARDOWN: Are `setTimeout` and `setInterval` handlers cleared in `finally` blocks?
 [ ] 8. IDEMPOTENCY SAFETY: Are retries restricted to transient errors and guarded by `Idempotency-Key` headers on mutations?
 [ ] 9. CONTENT-TYPE INTEGRITY: Is `res.json()` protected against HTML 502/504 error pages?
 [ ] 10. TIME-TO-SETTLEMENT: Are all external network requests wrapped with strict timeouts (`AbortSignal.timeout`)?
```

---

## 4. Diagnostics & Instrumentation Helper

Drop this debug wrapper around any async function to trace execution order, arguments, resolution latency, and error states in console logs:

```javascript
/**
 * Wraps an async function with timing, execution order, and error telemetry
 * @param {string} label
 * @param {Function} asyncFn
 */
function traceAsync(label, asyncFn) {
  let callCount = 0;

  return async function (...args) {
    const id = ++callCount;
    const start = performance.now();
    console.log(`⏱️ [${label} #${id}] STARTED with args:`, args);

    try {
      const result = await asyncFn.apply(this, args);
      const duration = Math.round(performance.now() - start);
      console.log(`✅ [${label} #${id}] RESOLVED in ${duration}ms with:`, result);
      return result;
    } catch (err) {
      const duration = Math.round(performance.now() - start);
      console.error(`❌ [${label} #${id}] REJECTED in ${duration}ms with error:`, err);
      throw err;
    }
  };
}

// Example Usage
const fetchUserTraced = traceAsync('FetchUser', async (id) => {
  const res = await fetch(`https://jsonplaceholder.typicode.com/users/${id}`);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
});

fetchUserTraced(1);
```

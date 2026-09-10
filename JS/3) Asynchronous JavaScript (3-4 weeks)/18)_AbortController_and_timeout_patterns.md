# `AbortController` and Timeout Patterns: Production Masterclass

In modern frontend applications and high-throughput Node.js microservices, asynchronous operations cannot be assumed to run indefinitely. User interactions (e.g. typing in a search bar, changing tabs, navigating away) and network degradation require an explicit **cancellation mechanism**.

The standard **`AbortController`** and **`AbortSignal`** APIs provide the universal interface across Web APIs, Node.js (`fs`, `events`, `http`), and custom Promise pipelines to cleanly cancel in-flight operations, prevent race conditions, and free operating system resources.

---

## 1. The Core Mental Model of `AbortController`

An `AbortController` separates the **capability to trigger an abort** from the **signal that listens for it**:

```
+------------------------------------+
|          AbortController           |
|  - abort(reason)                   |
+------------------------------------+
                  | (exposes)
                  v
+------------------------------------+
|            AbortSignal             |
|  - aborted: boolean                |
|  - reason: any                     |
|  - onabort: event handler          |
|  - addEventListener('abort', ...)  |
+------------------------------------+
                  |
         +--------+--------+
         |                 |
         v                 v
   [ fetch() API ]   [ Custom Async Loop / Event ]
   (Closes socket)   (Cleans timers & throws AbortError)
```

- **`AbortController` (Producer)**: Held by the caller who decides *when* to cancel (e.g., component unmount, timeout, user keystroke).
- **`AbortSignal` (Consumer)**: Passed into the underlying asynchronous task (`fetch`, DB driver, FileReader, timer) to notify it when cancellation has occurred.

---

## 2. Anatomy of `AbortSignal` & Native Methods

### A. Manual Cancellation
```javascript
const controller = new AbortController();
const signal = controller.signal;

console.log(signal.aborted); // false

// Trigger cancellation with custom reason
controller.abort(new Error("User cancelled operation"));

console.log(signal.aborted); // true
console.log(signal.reason);  // Error: User cancelled operation
```

### B. Modern Static Factory Methods

#### 1. `AbortSignal.timeout(delayMs)`
Creates a signal that automatically aborts with a `TimeoutError` after a specified millisecond duration:
```javascript
try {
  const res = await fetch('https://api.example.com/slow-endpoint', {
    signal: AbortSignal.timeout(3000) // Aborts automatically after 3 seconds
  });
  const data = await res.json();
} catch (err) {
  if (err.name === 'TimeoutError') {
    console.error("Request timed out after 3000ms");
  }
}
```

#### 2. `AbortSignal.any(iterableSignals)`
Combines multiple signals into a single composite signal that triggers when **any** of the input signals abort (e.g., Manual User Cancel OR Timeout):
```javascript
const userCancelController = new AbortController();
const timeoutSignal = AbortSignal.timeout(5000);

// Aborts if user clicks cancel OR if 5 seconds elapse
const combinedSignal = AbortSignal.any([
  userCancelController.signal,
  timeoutSignal
]);

const res = await fetch('/api/data', { signal: combinedSignal });
```

---

## 3. The Race Condition Trap: Search Typeahead / Autocomplete

When a user types rapidly in a search box, multiple asynchronous `fetch()` requests are fired concurrently. Because network latency is non-deterministic, **Request 1 (for "j") might finish AFTER Request 2 (for "java")**, overwriting the latest results with stale data!

```
User types "J"    --> [ Request 1: "J" (300ms latency) ] --------------------> Resolves at T=350ms (STALE OVERWRITE!)
User types "Java" --> [ Request 2: "Java" (100ms latency) ] -> Resolves at T=200ms
```

### Production Implementation: Auto-Cancelling Search Client

```javascript
class TypeaheadSearchClient {
  constructor(endpoint) {
    this.endpoint = endpoint;
    this.activeController = null;
  }

  async search(query) {
    // 1. Abort previous in-flight request if it exists
    if (this.activeController) {
      this.activeController.abort('New search query initiated');
    }

    // 2. Create fresh controller for current query
    this.activeController = new AbortController();
    const { signal } = this.activeController;

    try {
      const response = await fetch(`${this.endpoint}?q=${encodeURIComponent(query)}`, { signal });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      return data;
    } catch (error) {
      // 3. Gracefully distinguish AbortError from real network errors
      if (error.name === 'AbortError' || signal.aborted) {
        console.log(`[Search] Query "${query}" aborted. Skipping state update.`);
        return null; // Don't throw for intentional cancellations
      }
      throw error; // Re-throw legitimate network or server failures
    } finally {
      // 4. Clean up controller reference if this was the active one
      if (this.activeController?.signal === signal) {
        this.activeController = null;
      }
    }
  }
}

// Verification Test
const client = new TypeaheadSearchClient('https://jsonplaceholder.typicode.com/todos');
client.search('a'); // Fired and immediately aborted!
client.search('ab'); // Fired and immediately aborted!
client.search('abc').then(data => {
  if (data) console.log("Final query resolved:", data);
});
```

---

## 4. Making Custom Asynchronous Functions Cancellable

Any asynchronous task (e.g. database streams, file chunkers, or timers) can be made cancellable by listening to the `abort` event on the provided `signal`.

### Production Pattern: Cancellable Delay & Long-Running Worker

```javascript
/**
 * Cancellable sleep utility
 * @param {number} ms - Milliseconds to sleep
 * @param {AbortSignal} [signal] - Optional AbortSignal
 * @returns {Promise<void>}
 */
function sleep(ms, signal) {
  return new Promise((resolve, reject) => {
    // 1. If signal already aborted prior to call, reject immediately
    if (signal?.aborted) {
      return reject(new DOMException(signal.reason || 'Aborted', 'AbortError'));
    }

    const timer = setTimeout(() => {
      cleanup();
      resolve();
    }, ms);

    const onAbort = () => {
      cleanup();
      reject(new DOMException(signal.reason || 'Aborted', 'AbortError'));
    };

    const cleanup = () => {
      clearTimeout(timer);
      signal?.removeEventListener('abort', onAbort);
    };

    signal?.addEventListener('abort', onAbort, { once: true });
  });
}

/**
 * Long-running batch processor respecting cancellation
 */
async function processLargeBatch(items, workerFn, signal) {
  const results = [];

  for (let i = 0; i < items.length; i++) {
    // Check cancellation boundary before starting next item
    if (signal?.aborted) {
      throw new DOMException(signal.reason || 'Batch aborted', 'AbortError');
    }

    const itemResult = await workerFn(items[i]);
    results.push(itemResult);

    // Optional delay between items with signal propagation
    await sleep(50, signal);
  }

  return results;
}
```

---

## 5. Memory Management & Event Listener Cleanup

A common memory leak with `AbortSignal` is attaching event listeners (`signal.addEventListener('abort', ...)`) and **forgetting to remove them** if the asynchronous operation completes successfully.

```javascript
// ❌ MEMORY LEAK: Listener remains on signal indefinitely!
function leakyAsync(signal) {
  return new Promise((resolve) => {
    signal.addEventListener('abort', () => { /* ... */ });
    // If never aborted, the closure holds memory
  });
}

// ✅ CORRECT: Always cleanup listeners or use { once: true }
function cleanAsync(signal) {
  return new Promise((resolve, reject) => {
    const onAbort = () => reject(new DOMException('Aborted', 'AbortError'));
    signal.addEventListener('abort', onAbort, { once: true });

    doWork().then(res => {
      signal.removeEventListener('abort', onAbort);
      resolve(res);
    });
  });
}
```

---

## 6. Real-World Gotchas & Edge Cases

### Gotcha 1: `fetch()` Socket State on Abort
When `fetch()` is aborted in browsers, the browser immediately terminates the underlying TCP socket and tears down the connection. On the server side, Node.js/Nginx will detect an abrupt client disconnect (`ECONNRESET` or `EPIPE`). Do not treat this as a server failure.

### Gotcha 2: Distinguishing `AbortError` vs `TimeoutError`
- If aborted manually via `controller.abort()`: Throws `DOMException` with `name: 'AbortError'`.
- If aborted via `AbortSignal.timeout(ms)`: Throws `DOMException` with `name: 'TimeoutError'`.
- Always inspect `err.name` in your error boundary to display appropriate UI messages.

---

## 7. Senior Interview Challenges

### Challenge: Implement a Resilient Timeout & Retry Wrapper with `AbortController`
Build a high-order function `fetchWithTimeoutAndRetry` that:
1. Enforces a per-attempt timeout of $K$ ms.
2. Supports a parent cancellation signal from the caller.
3. Automatically retries up to $N$ times on timeouts or network failures.

```javascript
async function fetchWithTimeoutAndRetry(url, options = {}, { timeoutMs = 3000, maxRetries = 2 } = {}) {
  const { signal: parentSignal, ...fetchOptions } = options;

  let attempt = 0;

  while (attempt <= maxRetries) {
    attempt++;
    
    // Check if parent signal already cancelled
    if (parentSignal?.aborted) {
      throw new DOMException('Request cancelled by user', 'AbortError');
    }

    // 1. Create timeout signal for this specific attempt
    const timeoutSignal = AbortSignal.timeout(timeoutMs);

    // 2. Combine parent caller signal with per-attempt timeout signal
    const combinedSignal = parentSignal 
      ? AbortSignal.any([parentSignal, timeoutSignal])
      : timeoutSignal;

    try {
      const response = await fetch(url, {
        ...fetchOptions,
        signal: combinedSignal
      });

      if (!response.ok && response.status >= 500 && attempt <= maxRetries) {
        throw new Error(`Server Error HTTP ${response.status}`);
      }

      return response;

    } catch (error) {
      // If caller intentionally cancelled, DO NOT retry! Fail immediately.
      if (parentSignal?.aborted) {
        throw error;
      }

      const isTimeout = error.name === 'TimeoutError';
      const isNetwork = error instanceof TypeError || error.message.includes('Server Error');

      if (attempt > maxRetries || (!isTimeout && !isNetwork)) {
        throw error;
      }

      console.warn(`Attempt ${attempt} failed (${error.name}: ${error.message}). Retrying...`);
      await new Promise(r => setTimeout(r, 200 * attempt));
    }
  }
}

// Verification Test
// const userController = new AbortController();
// fetchWithTimeoutAndRetry('https://jsonplaceholder.typicode.com/posts/1', {
//   signal: userController.signal
// }, { timeoutMs: 2000, maxRetries: 2 }).then(res => console.log("Done:", res.status));
```

---

## 8. Summary & Best Practices

| Pattern / Goal | Recommended API / Syntax |
| :--- | :--- |
| **Manual User Cancellation** | `const ctrl = new AbortController(); fetch(url, { signal: ctrl.signal }); ctrl.abort();` |
| **Simple Timeout** | `fetch(url, { signal: AbortSignal.timeout(5000) })` |
| **Multiple Cancellation Reasons** | `AbortSignal.any([userSignal, timeoutSignal, routeSignal])` |
| **Detecting Cancellation** | `if (err.name === 'AbortError' \|\| err.name === 'TimeoutError')` |
| **Event Listener Memory Cleanup** | Always `removeEventListener` or use `{ once: true }` |
| **Race-Condition Prevention** | Abort existing controller before initiating new typeahead query |

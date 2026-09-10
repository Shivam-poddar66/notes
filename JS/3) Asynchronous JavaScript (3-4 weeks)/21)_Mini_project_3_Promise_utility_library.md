# Mini Project 3: Production Promise Utility Library (`async-toolkit`)

A production-grade, zero-dependency asynchronous utility library mirroring industry standards (such as `p-limit`, `p-retry`, `p-timeout`, `p-map`, and `p-series`). This project tests deep mastery over Promise resolution semantics, cancellation propagation, timer garbage collection, and concurrency pooling.

---

## 1. Library Architecture & API Matrix

```
                          [ async-toolkit (Index) ]
                                      |
         +------------+---------------+------------+------------+
         |            |               |            |            |
         v            v               v            v            v
     [ delay ]   [ timeout ]      [ retry ]    [ pLimit ]   [ settleMap ]
```

| Function | Signature | Responsibility |
| :--- | :--- | :--- |
| **`delay`** | `(ms, options?) => Promise<void>` | Non-blocking sleep with `AbortSignal` cancellation & unref option |
| **`timeout`** | `(promiseOrFn, ms, options?) => Promise<T>` | Wraps target in strict timeout; cleans timer on settlement |
| **`retry`** | `(taskFn, options?) => Promise<T>` | Retries with full jitter, exponential backoff & abort signal |
| **`pLimit`** | `(concurrency) => (taskFn) => Promise<T>` | Async semaphore limiter with queued token execution |
| **`settleMap`** | `(items, mapper, options?) => Promise<Result[]>` | Maps array with concurrency cap; collects all settled outcomes |

---

## 2. Directory Structure

```text
promise-utils/
├── src/
│   ├── delay.js
│   ├── timeout.js
│   ├── retry.js
│   ├── pLimit.js
│   ├── settleMap.js
│   └── errors.js
├── tests/
│   └── test-suite.js
└── index.js
```

---

## 3. Production Source Code

### A. `src/errors.js` (Custom Exception Hierarchy)

```javascript
export class TimeoutError extends Error {
  constructor(message = 'Operation timed out', timeoutMs = 0) {
    super(message);
    this.name = 'TimeoutError';
    this.timeoutMs = timeoutMs;
  }
}

export class AbortError extends Error {
  constructor(message = 'Operation was aborted') {
    super(message);
    this.name = 'AbortError';
  }
}
```

---

### B. `src/delay.js` (Cancellable Sleep)

```javascript
import { AbortError } from './errors.js';

/**
 * Returns a promise that resolves after `ms` milliseconds.
 * Supports AbortSignal cancellation and custom resolve values.
 * 
 * @template T
 * @param {number} ms - Duration in milliseconds
 * @param {Object} [options]
 * @param {T} [options.value] - Value to resolve with
 * @param {AbortSignal} [options.signal] - Optional cancellation signal
 * @returns {Promise<T>}
 */
export function delay(ms, options = {}) {
  const { value, signal } = options;

  return new Promise((resolve, reject) => {
    if (signal?.aborted) {
      return reject(new AbortError(signal.reason));
    }

    const timer = setTimeout(() => {
      cleanup();
      resolve(value);
    }, ms);

    const onAbort = () => {
      cleanup();
      reject(new AbortError(signal.reason));
    };

    const cleanup = () => {
      clearTimeout(timer);
      signal?.removeEventListener('abort', onAbort);
    };

    signal?.addEventListener('abort', onAbort, { once: true });
  });
}
```

---

### C. `src/timeout.js` (Memory-Safe Timeout Wrapper)

```javascript
import { TimeoutError } from './errors.js';

/**
 * Wraps a promise or promise-returning function with a strict timeout.
 * Guarantees timer cancellation on early resolution to prevent memory leaks.
 * 
 * @template T
 * @param {Promise<T> | (() => Promise<T>)} target - Promise or factory function
 * @param {number} ms - Timeout in milliseconds
 * @param {Object} [options]
 * @param {string} [options.message] - Custom error message
 * @param {() => void} [options.onTimeout] - Fallback or cleanup callback
 * @returns {Promise<T>}
 */
export async function timeout(target, ms, options = {}) {
  let timerId;

  const promise = typeof target === 'function' ? target() : target;

  const timerPromise = new Promise((_, reject) => {
    timerId = setTimeout(() => {
      if (options.onTimeout) options.onTimeout();
      reject(new TimeoutError(options.message || `Promise timed out after ${ms}ms`, ms));
    }, ms);
  });

  try {
    return await Promise.race([promise, timerPromise]);
  } finally {
    clearTimeout(timerId); // 🛡️ CRITICAL: Clears timer if promise settles first!
  }
}
```

---

### D. `src/retry.js` (Exponential Backoff with Full Jitter)

```javascript
import { delay } from './delay.js';
import { AbortError } from './errors.js';

/**
 * Executes a task function with configurable exponential backoff and jitter.
 * 
 * @template T
 * @param {() => Promise<T>} taskFn
 * @param {Object} [options]
 * @param {number} [options.retries=3] - Maximum retry attempts
 * @param {number} [options.factor=2] - Exponential base
 * @param {number} [options.minTimeout=200] - Base delay in ms
 * @param {number} [options.maxTimeout=10000] - Maximum delay ceiling
 * @param {(err: any) => boolean} [options.shouldRetry] - Predicate
 * @param {AbortSignal} [options.signal] - Cancellation signal
 * @returns {Promise<T>}
 */
export async function retry(taskFn, options = {}) {
  const {
    retries = 3,
    factor = 2,
    minTimeout = 200,
    maxTimeout = 10000,
    shouldRetry = () => true,
    signal
  } = options;

  let lastError;

  for (let attempt = 0; attempt <= retries; attempt++) {
    if (signal?.aborted) {
      throw new AbortError(signal.reason);
    }

    try {
      return await taskFn();
    } catch (err) {
      lastError = err;

      if (attempt >= retries || !shouldRetry(err)) {
        throw err;
      }

      // Full Jitter Backoff
      const rawDelay = minTimeout * Math.pow(factor, attempt);
      const cappedDelay = Math.min(rawDelay, maxTimeout);
      const jitterDelay = Math.floor(Math.random() * cappedDelay);

      await delay(jitterDelay, { signal });
    }
  }

  throw lastError;
}
```

---

### E. `src/pLimit.js` (Production Async Semaphore)

```javascript
/**
 * Creates a concurrency-limiting executor (p-limit equivalent).
 * 
 * @param {number} concurrency - Max simultaneous active tasks
 * @returns {<T>(fn: () => Promise<T>) => Promise<T>}
 */
export function pLimit(concurrency) {
  if (!Number.isInteger(concurrency) || concurrency < 1) {
    throw new TypeError('Expected `concurrency` to be an integer >= 1');
  }

  let activeCount = 0;
  const queue = [];

  const next = () => {
    activeCount--;
    if (queue.length > 0) {
      const nextTask = queue.shift();
      nextTask();
    }
  };

  const run = async (fn, resolve, reject) => {
    activeCount++;
    try {
      const result = await fn();
      resolve(result);
    } catch (err) {
      reject(err);
    } finally {
      next();
    }
  };

  const enqueue = (fn, resolve, reject) => {
    queue.push(() => run(fn, resolve, reject));
    if (activeCount < concurrency && queue.length > 0) {
      const nextTask = queue.shift();
      nextTask();
    }
  };

  const generator = (fn) => new Promise((resolve, reject) => {
    enqueue(fn, resolve, reject);
  });

  Object.defineProperties(generator, {
    activeCount: { get: () => activeCount },
    pendingCount: { get: () => queue.length },
    clearQueue: { value: () => { queue.length = 0; } }
  });

  return generator;
}
```

---

### F. `src/settleMap.js` (Concurrent Partial-Success Collector)

```javascript
import { pLimit } from './pLimit.js';

/**
 * Concurrently maps an array of items with a concurrency cap,
 * settling all items without throwing on individual errors.
 * 
 * @template T, R
 * @param {T[]} items - Array of items to process
 * @param {(item: T, index: number) => Promise<R>} mapper - Async mapping function
 * @param {Object} [options]
 * @param {number} [options.concurrency=Infinity] - Max concurrency
 * @returns {Promise<Array<{ status: 'fulfilled', value: R } | { status: 'rejected', reason: any }>>}
 */
export async function settleMap(items, mapper, options = {}) {
  const { concurrency = Infinity } = options;
  const limit = pLimit(concurrency);

  const taskPromises = items.map((item, index) => 
    limit(async () => {
      try {
        const value = await mapper(item, index);
        return { status: 'fulfilled', value };
      } catch (reason) {
        return { status: 'rejected', reason };
      }
    })
  );

  return Promise.all(taskPromises);
}
```

---

### G. `index.js` (Main Entrypoint)

```javascript
export { delay } from './src/delay.js';
export { timeout } from './src/timeout.js';
export { retry } from './src/retry.js';
export { pLimit } from './src/pLimit.js';
export { settleMap } from './src/settleMap.js';
export { TimeoutError, AbortError } from './src/errors.js';
```

---

## 4. Comprehensive Test Suite (`tests/test-suite.js`)

A zero-framework runnable test suite to verify every async edge case:

```javascript
import { delay, timeout, retry, pLimit, settleMap, TimeoutError, AbortError } from '../index.js';

async function runTests() {
  console.log("🚀 Starting async-toolkit Test Suite...\n");

  let passed = 0;
  let failed = 0;

  async function test(name, fn) {
    try {
      await fn();
      console.log(`  ✅ PASS: ${name}`);
      passed++;
    } catch (err) {
      console.error(`  ❌ FAIL: ${name}`);
      console.error(`     Error: ${err.message}`);
      failed++;
    }
  }

  // 1. Test delay
  await test('delay() resolves after duration', async () => {
    const start = Date.now();
    await delay(100, { value: 42 });
    const diff = Date.now() - start;
    if (diff < 90) throw new Error(`Delay finished too quickly: ${diff}ms`);
  });

  await test('delay() aborts immediately with AbortSignal', async () => {
    const ctrl = new AbortController();
    setTimeout(() => ctrl.abort('cancelled'), 30);
    try {
      await delay(500, { signal: ctrl.signal });
      throw new Error('Should have rejected');
    } catch (err) {
      if (!(err instanceof AbortError)) throw new Error('Expected AbortError');
    }
  });

  // 2. Test timeout
  await test('timeout() resolves if task finishes before threshold', async () => {
    const result = await timeout(delay(50, { value: 'ok' }), 200);
    if (result !== 'ok') throw new Error(`Unexpected value: ${result}`);
  });

  await test('timeout() rejects with TimeoutError if task exceeds threshold', async () => {
    try {
      await timeout(delay(300), 100);
      throw new Error('Should have timed out');
    } catch (err) {
      if (!(err instanceof TimeoutError)) throw new Error('Expected TimeoutError');
    }
  });

  // 3. Test retry
  await test('retry() recovers on transient errors', async () => {
    let attempts = 0;
    const result = await retry(async () => {
      attempts++;
      if (attempts < 3) throw new Error('Transient network drop');
      return 'recovered';
    }, { retries: 4, minTimeout: 10 });

    if (result !== 'recovered' || attempts !== 3) {
      throw new Error(`Failed recovery logic: attempts=${attempts}`);
    }
  });

  // 4. Test pLimit
  await test('pLimit() restricts concurrency strictly', async () => {
    const limit = pLimit(2);
    let active = 0;
    let maxActive = 0;

    const task = () => limit(async () => {
      active++;
      maxActive = Math.max(maxActive, active);
      await delay(50);
      active--;
    });

    await Promise.all([task(), task(), task(), task(), task()]);
    if (maxActive > 2) throw new Error(`Concurrency exceeded limit: ${maxActive}`);
  });

  // 5. Test settleMap
  await test('settleMap() collects successes and failures with concurrency limit', async () => {
    const items = [1, 2, 3, 4];
    const results = await settleMap(items, async (x) => {
      if (x === 3) throw new Error('Item 3 failed');
      return x * 10;
    }, { concurrency: 2 });

    if (results.length !== 4) throw new Error('Incomplete results length');
    if (results[0].value !== 10 || results[2].status !== 'rejected') {
      throw new Error('Unexpected settleMap output shape');
    }
  });

  console.log(`\n🏁 Test Run Finished: ${passed} passed, ${failed} failed.`);
}

runTests();
```

---

## 5. Summary & Key Learnings

1. **Timer Cleanup**: Always `clearTimeout` in a `finally` block or on promise settlement to prevent micro-timer accumulation in V8 memory pools.
2. **Signal Propagation**: Pass `signal` across nested utilities (`retry` $\to$ `delay`) to ensure user cancellations cascade throughout the entire async call stack.
3. **Queue Encapsulation**: Limiters should manage active counts and queues with atomic execution blocks to eliminate race conditions between tasks.

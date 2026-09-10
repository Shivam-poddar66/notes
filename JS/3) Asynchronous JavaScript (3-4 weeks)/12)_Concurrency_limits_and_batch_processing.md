# Concurrency Limits and Batch Processing: Production Architecture

In real-world JavaScript engineering—whether frontend network layers, serverless functions, or high-throughput Node.js microservices—unbounded asynchronous concurrency is a frequent cause of system crashes, socket exhaustion (`EMFILE`), database pool starvation, and API rate-limiting penalties (`HTTP 429 Too Many Requests`).

Controlling concurrency through **Batch Chunking**, **Worker Queue Pools**, and **Token/Semaphore Limiters** is essential for resilient distributed software.

---

## 1. The Physics of Concurrency in JavaScript

While JavaScript is single-threaded (executing on one Call Stack), asynchronous I/O leverages operating system thread pools (libuv) and network socket tables. 

```
UNBOUNDED PARALLEL (10,000 Tasks)
Main Thread -> Initiates 10,000 Promises
                   |
     OS Socket Table / HTTP Agents
     [ Socket 1 ][ Socket 2 ] ... [ Socket 10,000 ] -> CRASH (EMFILE / Socket Timeout / 429)
     Heap Memory: 10,000 pending closures held in memory

CONTROLLED CONCURRENCY POOL (Concurrency = 3)
Queue: [ Task 4, Task 5, Task 6, ... Task 10,000 ]
Active:
  Worker 1: [ Task 1 (in-flight) ]
  Worker 2: [ Task 2 (in-flight) ]
  Worker 3: [ Task 3 (in-flight) ]
  -> Constant RAM, steady socket utilization, zero rate-limit spikes.
```

---

## 2. Pattern 1: Batch Chunking (Step-by-Step Windowing)

Batch processing divides a large dataset into discrete slices of size $K$, running all $K$ items concurrently, waiting for the entire slice to settle before dispatching the next.

```
Time --->
Batch 1: [ Task 1 (50ms) ][ Task 2 (200ms) ]  --> (Waits for slowest: 200ms)
Batch 2:                                       [ Task 3 (100ms) ][ Task 4 (150ms) ]
```

### Production Implementation: Resilient Batch Processor

```javascript
/**
 * Processes items in sequential batches with parallel execution per batch
 * @template T, R
 * @param {T[]} items - Input collection
 * @param {number} batchSize - Maximum items per batch
 * @param {(item: T, index: number) => Promise<R>} workerFn - Async worker
 * @returns {Promise<Array<{ status: 'fulfilled', value: R } | { status: 'rejected', reason: any }>>}
 */
async function processInBatches(items, batchSize, workerFn) {
  const allResults = [];

  for (let i = 0; i < items.length; i += batchSize) {
    const chunk = items.slice(i, i + batchSize);
    
    // Execute all items in current chunk concurrently
    const chunkPromises = chunk.map((item, chunkIndex) => 
      workerFn(item, i + chunkIndex)
    );

    // Using allSettled prevents a single item failure from stopping subsequent batches
    const chunkResults = await Promise.allSettled(chunkPromises);
    allResults.push(...chunkResults);
  }

  return allResults;
}

// Example Usage
const urls = Array.from({ length: 12 }, (_, i) => `https://api.example.com/item/${i + 1}`);

const results = await processInBatches(urls, 4, async (url, idx) => {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
});
```

### Pros & Cons of Batch Chunking
- **Pros**: Easy to implement; natural checkpoints for database transactions or progress reporting.
- **Cons**: **Head-of-Line / Long-Tail Latency Blocking**. If 3 items take 10ms and 1 item takes 1000ms, 3 worker slots sit idle for 990ms waiting for the slowest task in the batch to resolve.

---

## 3. Pattern 2: Continuous Worker Pool (Sliding Window)

Instead of waiting for an entire batch to finish, a **Worker Pool** immediately dispatches the next queued task as soon as *any* single worker slot frees up.

```
Time --->
Worker 1: [ Task 1 (50ms) ][ Task 4 (150ms)       ][ Task 6 (40ms) ]
Worker 2: [ Task 2 (200ms)                       ][ Task 7 (100ms) ]
Worker 3: [ Task 3 (80ms)  ][ Task 5 (90ms) ]
```

### Production Implementation: Zero-Dependency `p-map` (Worker Pool)

```javascript
/**
 * Concurrently maps an array with a fixed concurrency limit (sliding window)
 * @template T, R
 * @param {T[]} items
 * @param {number} concurrency
 * @param {(item: T, index: number) => Promise<R>} mapper
 * @returns {Promise<R[]>}
 */
async function mapConcurrent(items, concurrency, mapper) {
  return new Promise((resolve, reject) => {
    const results = new Array(items.length);
    let nextIndex = 0;
    let completedCount = 0;
    let hasFailed = false;

    if (items.length === 0) {
      resolve([]);
      return;
    }

    // Determine initial worker count
    const workerCount = Math.min(concurrency, items.length);

    async function worker() {
      while (nextIndex < items.length && !hasFailed) {
        const currentIndex = nextIndex++;
        const item = items[currentIndex];

        try {
          const result = await mapper(item, currentIndex);
          results[currentIndex] = result;
          completedCount++;

          if (completedCount === items.length) {
            resolve(results);
            return;
          }
        } catch (error) {
          hasFailed = true;
          reject(error);
          return;
        }
      }
    }

    // Spawn concurrent worker loops
    for (let i = 0; i < workerCount; i++) {
      worker();
    }
  });
}

// Verification
const mockFetch = (id, ms) => new Promise(res => setTimeout(() => res(`Data ${id}`), ms));
const taskData = [
  { id: 1, delay: 300 },
  { id: 2, delay: 100 },
  { id: 3, delay: 50 },
  { id: 4, delay: 80 },
  { id: 5, delay: 200 }
];

console.time('Pool Execution');
const poolResults = await mapConcurrent(taskData, 2, task => mockFetch(task.id, task.delay));
console.timeEnd('Pool Execution');
console.log("Results preserved in original index order:", poolResults);
```

---

## 4. Pattern 3: Async Semaphore / Token Bucket (`p-limit`)

When tasks are triggered dynamically across different functions (rather than from a single array), an **Async Semaphore** throttles invocations globally.

### Spec & Implementation: Production Async Semaphore / `p-limit`

```javascript
/**
 * Creates a concurrency-limiting execution function
 * @param {number} concurrency - Max simultaneous active tasks
 * @returns {<T>(fn: () => Promise<T>) => Promise<T>}
 */
function createLimiter(concurrency) {
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

  return function limit(fn) {
    return new Promise((resolve, reject) => {
      const execute = () => run(fn, resolve, reject);

      if (activeCount < concurrency) {
        execute();
      } else {
        queue.push(execute);
      }
    });
  };
}

// Usage with disparate async calls
const limit = createLimiter(2);

const promises = [
  limit(() => fetch('/api/users')),
  limit(() => fetch('/api/orders')),
  limit(() => fetch('/api/analytics')),
  limit(() => fetch('/api/billing'))
];

const responses = await Promise.all(promises);
```

---

## 5. Architectural Comparison: Batching vs Worker Pool vs Semaphore

| Dimension | Batch Chunking | Continuous Worker Pool | Async Semaphore (`p-limit`) |
| :--- | :--- | :--- | :--- |
| **Execution Model** | Block-by-block lockstep | Continuous sliding window | Token-based queue dispatcher |
| **Worker Utilization** | Low (blocked by slowest item) | Optimal ($100\%$ saturation) | Optimal ($100\%$ saturation) |
| **Input Source** | Static Arrays | Static Arrays | Dynamic ad-hoc Promise calls |
| **Ordering Guarantee** | Guaranteed by batch order | Preserved by index mapping | Natural promise resolution |
| **Memory Overhead** | Lowest (garbage collected per batch) | Low | Low-Medium (retains pending closures) |
| **Ideal For** | Database chunk writes, ETL | Bulk file processing, image ops | Shared API Client throttling |

---

## 6. Real-World Gotchas & Edge Cases

### Gotcha 1: Concurrency Cap vs Network Round-Trip Throttling
Limiting concurrency to `5` ensures only 5 requests are in flight, but **does not guarantee compliance with strict Rate Limits** (e.g. "Max 10 requests per second").
- If each request takes 20ms, concurrency of 5 will execute **250 requests/sec**!
- *Rule*: Combine Concurrency Limiters with **Leaky Bucket** / **Token Delay** algorithms for rate-limited APIs.

```javascript
// Concurrency Limiter WITH Inter-Request Delay
function createRateLimitedLimiter(concurrency, minDelayMs) {
  const limit = createLimiter(concurrency);
  let lastCall = 0;

  return (fn) => limit(async () => {
    const now = Date.now();
    const wait = Math.max(0, lastCall + minDelayMs - now);
    lastCall = now + wait;
    if (wait > 0) await new Promise(r => setTimeout(r, wait));
    return fn();
  });
}
```

### Gotcha 2: Unhandled Rejection During Pool Execution
If using custom worker loops without attaching `.catch()` to the worker invocation promises, an unhandled rejection can bypass the outer wrapper and crash Node.js processes (`ERR_UNHANDLED_REJECTION`). Always encapsulate worker inner bodies in `try...catch...finally`.

---

## 7. Senior Interview Challenges

### Challenge 1: Implement an Auto-Retrying Concurrent Queue with Priority
Build a queue that processes tasks up to a given concurrency limit, supports integer priorities (higher priority tasks execute first), and auto-retries failed tasks up to $N$ times.

```javascript
class PriorityAsyncQueue {
  constructor(concurrency = 2, maxRetries = 2) {
    this.concurrency = concurrency;
    this.maxRetries = maxRetries;
    this.activeCount = 0;
    this.queue = []; // Array of { taskFn, priority, retries, resolve, reject }
  }

  add(taskFn, priority = 0) {
    return new Promise((resolve, reject) => {
      this.queue.push({
        taskFn,
        priority,
        retries: 0,
        resolve,
        reject
      });
      // Sort descending by priority
      this.queue.sort((a, b) => b.priority - a.priority);
      this._dequeue();
    });
  }

  async _dequeue() {
    if (this.activeCount >= this.concurrency || this.queue.length === 0) {
      return;
    }

    this.activeCount++;
    const item = this.queue.shift();

    try {
      const result = await item.taskFn();
      item.resolve(result);
    } catch (err) {
      if (item.retries < this.maxRetries) {
        item.retries++;
        console.warn(`Retrying task (attempt ${item.retries}/${this.maxRetries})...`);
        this.queue.push(item);
        this.queue.sort((a, b) => b.priority - a.priority);
      } else {
        item.reject(err);
      }
    } finally {
      this.activeCount--;
      this._dequeue();
    }
  }
}

// Verification Test
const queue = new PriorityAsyncQueue(2, 1);
const makeTask = (name, delayMs, failOnce = false) => {
  let failed = false;
  return async () => {
    await new Promise(r => setTimeout(r, delayMs));
    if (failOnce && !failed) {
      failed = true;
      throw new Error(`${name} failed`);
    }
    return `${name} done`;
  };
};

queue.add(makeTask('Low Priority Task', 100), 1).then(console.log);
queue.add(makeTask('High Priority Task', 50, true), 10).then(console.log);
queue.add(makeTask('Medium Priority Task', 50), 5).then(console.log);
```

---

## 8. Summary & Best Practices

1. **Default to Continuous Worker Pools**: Prefer sliding-window worker pools (`p-map`) over batch chunking to eliminate idle time caused by slow outlier requests.
2. **Standard Production Concurrency Defaults**:
   - **Browser HTTP/1.1**: Concurrency $4-6$ (Browser domain socket limit).
   - **Browser HTTP/2**: Concurrency $10-20$ (Multiplexed streams).
   - **Node.js File I/O (`fs`)**: Concurrency $16-64$ (tuned against `UV_THREADPOOL_SIZE`).
   - **External Third-Party APIs**: Concurrency $3-5$ (unless explicitly documented higher).
3. **Preserve Index Ordering**: Always return an array matching the input sequence regardless of completion order.
4. **Isolate State with Finally**: Ensure semaphore release logic (`next()`) is placed in a `finally` block so failures never permanently deadlock worker slots.

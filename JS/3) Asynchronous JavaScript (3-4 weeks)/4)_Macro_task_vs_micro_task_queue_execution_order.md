# Macrotask vs. Microtask Queue Execution Order in JavaScript

---

## 1) Core Mental Model: The Two-Tier Queue System

To prevent asynchronous tasks from colliding while ensuring responsive UI rendering and predictable promise resolution, JavaScript uses a **Two-Tier Queue Architecture**:

1. **Microtask Queue (Jobs / High-Priority Queue)**: Holds short, immediate operations that must run **as soon as the Call Stack clears, before any other task or UI repaint**.
2. **Macrotask Queue (Task Queue / Standard Priority)**: Holds broader background tasks and events (timers, user clicks, network events).

```
┌────────────────────────────────────────────────────────────────────────┐
│                        THE EXECUTION CYCLE FLOW                        │
│                                                                        │
│   1. CALL STACK: Runs synchronous code until completely EMPTY.         │
│          │                                                             │
│          ▼                                                             │
│   2. MICROTASK QUEUE: DRAIN ALL microtasks to completion (0 remaining).│
│          │                                                             │
│          ▼                                                             │
│   3. RENDER STEP: (Browser only) Update DOM, Styles, Paint screen.     │
│          │                                                             │
│          ▼                                                             │
│   4. MACROTASK QUEUE: Run exactly ONE macrotask.                       │
│          │                                                             │
│          └──▶ Loop back to Step 2 (Drain all microtasks again!)        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Comprehensive Inventory of Queue Sources

| Queue Type | Browser API Sources | Node.js API Sources | Execution Behavior |
| :--- | :--- | :--- | :--- |
| **Microtask Queue** | • `Promise.then() / catch() / finally()`<br>• `queueMicrotask()`<br>• `MutationObserver`<br>• `async/await` resumption | • `Promise` resolution<br>• `queueMicrotask()`<br>• `process.nextTick()` *(Highest priority)* | **All drained continuously** until empty before yielding. |
| **Macrotask Queue** | • `setTimeout()` / `setInterval()`<br>• DOM User Events (`click`, `input`)<br>• `MessageChannel` / `postMessage`<br>• UI I/O Events | • `setTimeout()` / `setInterval()`<br>• `setImmediate()`<br>• Network & File I/O Callbacks<br>• Socket Close Events | **One task executed per event loop iteration**. |

---

## 3) How `async/await` Translates to Microtasks

Under the hood, the `await` keyword is syntactic sugar over `Promise.resolve().then()`:

```js
async function example() {
  console.log("A: Inside async function (Synchronous)");
  
  await Promise.resolve(); // ◀── Everything AFTER this line is a MICROTASK!
  
  console.log("B: After await (Microtask)");
}

console.log("1: Start");
example();
console.log("2: End");

// Output Sequence:
// 1: Start
// A: Inside async function (Synchronous)
// 2: End
// B: After await (Microtask)
```

---

## 4) Detailed Step-by-Step Code Walkthrough

Let us trace a comprehensive execution with mixed synchronous code, promises, `async/await`, timers, and explicit microtasks:

```js
console.log("1: Global Sync");

setTimeout(() => {
  console.log("2: Timeout 1");
  Promise.resolve().then(() => console.log("3: Promise inside Timeout 1"));
}, 0);

async function asyncRunner() {
  console.log("4: Async Start");
  await null;
  console.log("5: Async End");
}

asyncRunner();

queueMicrotask(() => {
  console.log("6: queueMicrotask");
});

new Promise((resolve) => {
  console.log("7: Promise Constructor (Sync)");
  resolve();
}).then(() => {
  console.log("8: Promise.then");
});

setTimeout(() => {
  console.log("9: Timeout 2");
}, 0);

console.log("10: Global End");
```

---

### Step-by-Step State Trace:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 1: Synchronous Execution (Call Stack)                                           │
│   • Logs: "1: Global Sync"                                                             │
│   • `setTimeout(..., 0)` registers Timeout 1 ──▶ Queued in MACROTASK                   │
│   • `asyncRunner()` starts ──▶ Logs: "4: Async Start"                                  │
│     (hits `await null` ──▶ remainder "5: Async End" scheduled as MICROTASK 1)          │
│   • `queueMicrotask(...)` ──▶ scheduled as MICROTASK 2                                 │
│   • `new Promise(executor)` runs executor synchronously ──▶ Logs: "7: Promise Ctor"   │
│     (resolved `.then(...)` ──▶ scheduled as MICROTASK 3)                               │
│   • `setTimeout(..., 0)` registers Timeout 2 ──▶ Queued in MACROTASK                   │
│   • Logs: "10: Global End"                                                             │
│                                                                                        │
│   Synchronous Output So Far: 1, 4, 7, 10                                               │
│   Microtask Queue: [ "5: Async End", "6: queueMicrotask", "8: Promise.then" ]          │
│   Macrotask Queue: [ Timeout 1, Timeout 2 ]                                            │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ PHASE 2: Drain ALL Microtasks to Completion                                            │
│   • Pops Microtask 1 ──▶ Logs: "5: Async End"                                          │
│   • Pops Microtask 2 ──▶ Logs: "6: queueMicrotask"                                     │
│   • Pops Microtask 3 ──▶ Logs: "8: Promise.then"                                       │
│   (Microtask Queue is now 0)                                                           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ PHASE 3: Execute ONE Macrotask (Timeout 1)                                             │
│   • Pops Timeout 1 ──▶ Logs: "2: Timeout 1"                                            │
│   • Its inner `Promise.resolve().then()` pushes a NEW Microtask: ("3: Promise...")     │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ PHASE 4: Drain Microtasks Again before next Macrotask!                                 │
│   • Pops Microtask ──▶ Logs: "3: Promise inside Timeout 1"                             │
│   (Microtask Queue is 0)                                                               │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ PHASE 5: Execute Next Macrotask (Timeout 2)                                            │
│   • Pops Timeout 2 ──▶ Logs: "9: Timeout 2"                                            │
│                                                                                        │
│   FINAL CONSOLE OUTPUT: 1 ──▶ 4 ──▶ 7 ──▶ 10 ──▶ 5 ──▶ 6 ──▶ 8 ──▶ 2 ──▶ 3 ──▶ 9      │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 5) Node.js Specific: `process.nextTick()` Priority

In Node.js, **`process.nextTick()` does NOT live in the standard microtask queue**. 

Node maintains a separate **`nextTick` Queue** that executes **BEFORE standard Promise microtasks**:

```js
// Node.js Execution Demonstration:
setTimeout(() => console.log("1: setTimeout"), 0);

Promise.resolve().then(() => console.log("2: Promise"));

process.nextTick(() => {
  console.log("3: process.nextTick");
});

console.log("4: Sync");

// Node.js Output:
// 4: Sync
// 3: process.nextTick (Runs BEFORE Promises!)
// 2: Promise
// 1: setTimeout
```

---

## 6) Practical Use Cases: When to Use What

---

### A. When to use `queueMicrotask()`: State Batching & Immediate Cleanup
Use `queueMicrotask` when you want to execute work immediately after the current synchronous code finishes, without waiting for timers or allowing user click events to interleave:

```js
class BatchLogger {
  #messages = [];
  #scheduled = false;

  log(msg) {
    this.#messages.push(msg);

    if (!this.#scheduled) {
      this.#scheduled = true;
      // Flush batch at the end of the current synchronous stack
      queueMicrotask(() => {
        console.log(`[Batch Flush]: Sent ${this.#messages.length} messages:`, this.#messages);
        this.#messages = [];
        this.#scheduled = false;
      });
    }
  }
}

const logger = new BatchLogger();
logger.log("Event A");
logger.log("Event B");
logger.log("Event C");
// Synchronously queues 3 events, then automatically flushes in 1 microtask:
// [Batch Flush]: Sent 3 messages: ['Event A', 'Event B', 'Event C']
```

---

### B. When to use `setTimeout(fn, 0)`: Yielding to the Browser UI Thread
If you have a heavy CPU task (processing 100,000 items), running it synchronously or with microtasks freezes the UI. Using `setTimeout` breaks work across multiple macrotasks, allowing the browser to **repaint and handle clicks between chunks**:

```js
function processChunkedData(items, chunkSize = 1000) {
  let index = 0;

  function processNextBatch() {
    const end = Math.min(index + chunkSize, items.length);
    while (index < end) {
      // Do heavy work on items[index]
      index++;
    }

    if (index < items.length) {
      // Yield to the browser render thread before doing next chunk!
      setTimeout(processNextBatch, 0);
    } else {
      console.log("All chunks processed successfully.");
    }
  }

  processNextBatch();
}
```

---

## 7) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                     MACROTASK vs. MICROTASK CHEAT SHEET                    |
+───────────────────────────+────────────────────────────────────────────────+
| Microtask Priority        | Always executes first; drained to zero.        |
| Microtask Sources         | `Promise.then`, `await`, `queueMicrotask`.     |
| Macrotask Priority        | Runs ONE task after microtask queue is empty.  |
| Macrotask Sources         | `setTimeout`, `setInterval`, DOM click events. |
| Node.js nextTick          | `process.nextTick` runs before Promise microtasks|
| Browser Rendering         | Occurs between microtask drain and macrotasks. |
| Chunking Heavy CPU Tasks  | Use `setTimeout(chunk, 0)` to yield UI frames. |
+───────────────────────────+────────────────────────────────────────────────+
```

# 6) Microtasks and Priority Queues: nextTick vs Promises

## Executive Overview
While the 6 phases of the Libuv Event Loop govern Macrotasks (Timers, I/O, `setImmediate`), Node.js features two distinct **Microtask Queues** that execute with ultra-high priority between every phase transition and immediately following synchronous JavaScript operations. Understanding `process.nextTick()` and Promise microtask priority is essential to predicting execution order and preventing Event Loop starvation.

---

## 1. The Microtask Queues

In Node.js, asynchronous callbacks are fundamentally categorized into:

1. **Macrotasks (Libuv Event Loop Tasks)**:
   - Timers (`setTimeout`, `setInterval`)
   - I/O events (TCP reads, file chunks)
   - Check phase (`setImmediate`)
   - Close events (`socket.on('close')`)
2. **Microtasks (Engine-Level Priority Queues)**:
   - **`process.nextTick` Queue**: Specific to Node.js.
   - **Promise Microtask Queue**: Standard ECMAScript (`Promise.then()`, `Promise.catch()`, `Promise.finally()`, `queueMicrotask()`, async/await resumptions).

---

## 2. The Execution Priority Hierarchy

When JavaScript completes executing the current synchronous stack frame, Node.js resolves microtasks in this exact priority order:

```text
               ┌────────────────────────────────────────────────────────┐
               │              Synchronous JavaScript Code               │
               └───────────────────────────┬────────────────────────────┘
                                           │
                                           ▼
               ┌────────────────────────────────────────────────────────┐
               │               process.nextTick() Queue                 │
               │   (Drained completely before ANY promise microtasks)   │
               └───────────────────────────┬────────────────────────────┘
                                           │
                                           ▼
               ┌────────────────────────────────────────────────────────┐
               │              Promise Microtask Queue                   │
               │  (Promise.then(), async/await, queueMicrotask())       │
               └───────────────────────────┬────────────────────────────┘
                                           │
                                           ▼
               ┌────────────────────────────────────────────────────────┐
               │          Proceed to Next Event Loop Phase              │
               │         (Timers -> Pending -> Poll -> Check)           │
               └────────────────────────────────────────────────────────┘
```

> **Rule**: The Microtask queues are checked and drained **after every single callback** in the Event Loop before advancing to the next callback or next phase.

---

## 3. `process.nextTick()` vs `Promise.then()` vs `queueMicrotask()`

```javascript
console.log('1. Synchronous Start');

setTimeout(() => {
  console.log('7. setTimeout (Timers Phase)');
}, 0);

setImmediate(() => {
  console.log('8. setImmediate (Check Phase)');
});

queueMicrotask(() => {
  console.log('5. queueMicrotask (Promise Microtask Queue)');
});

Promise.resolve().then(() => {
  console.log('4. Promise.then (Promise Microtask Queue)');
});

process.nextTick(() => {
  console.log('2. process.nextTick 1 (nextTick Queue)');
  process.nextTick(() => {
    console.log('3. Nested nextTick (nextTick Queue)');
  });
});

console.log('6. Synchronous End');
```

### Exact Output:
```text
1. Synchronous Start
6. Synchronous End
2. process.nextTick 1 (nextTick Queue)
3. Nested nextTick (nextTick Queue)
4. Promise.then (Promise Microtask Queue)
5. queueMicrotask (Promise Microtask Queue)
7. setTimeout (Timers Phase)
8. setImmediate (Check Phase)
```

### Why does nested `nextTick` run before `Promise.then`?
Because `process.nextTick()` drains its queue recursively until completely empty before passing execution to the Promise Microtask queue.

---

## 4. `setTimeout(fn, 0)` vs `setImmediate(fn)`

The execution order between `setTimeout(fn, 0)` and `setImmediate(fn)` depends entirely on the context in which they are scheduled.

### Case A: Called in the Top-Level Main Module
```javascript
// main-context.js
setTimeout(() => console.log('timeout'), 0);
setImmediate(() => console.log('immediate'));
```
- **Result**: Non-deterministic (Can print `timeout -> immediate` OR `immediate -> timeout`).
- **Why?** Entering the loop takes a few CPU cycles. In Node.js, `setTimeout(fn, 0)` is converted to `setTimeout(fn, 1)`. If the OS enters the Timers phase before 1 millisecond has elapsed, timer hasn't expired yet $\rightarrow$ proceeds to Poll $\rightarrow$ executes `setImmediate` in Check phase. If entering took $>1$ ms $\rightarrow$ timer executes first.

### Case B: Called inside an I/O Cycle (e.g. `fs.readFile`)
```javascript
// io-context.js
import fs from 'node:fs';

fs.readFile(__filename, () => {
  setTimeout(() => console.log('timeout'), 0);
  setImmediate(() => console.log('immediate'));
});
```
- **Result**: **GUARANTEED** `immediate` runs before `timeout` 100% of the time!
- **Why?** The `fs.readFile` callback runs in the **Poll phase**. After the Poll phase finishes, the Event Loop *always* advances directly to the **Check phase** (`setImmediate`) before circling back to Timers.

---

## 5. Event Loop Starvation with `process.nextTick()`

Because Node.js will never advance to the Libuv Event Loop until the `nextTick` queue is completely empty, recursively scheduling `nextTick` blocks all I/O and Timers permanently:

```javascript
// DANGEROUS: Event Loop Starvation
function starveLoop() {
  process.nextTick(starveLoop); // Never yields control back to Libuv!
}

starveLoop();

// This HTTP server or timer will NEVER execute!
setTimeout(() => console.log('Will never be printed!'), 100);
```

### Best Practice for `process.nextTick()`:
Use `process.nextTick()` only when an API must allow the user to attach event listeners synchronously before an asynchronous event fires:

```javascript
import { EventEmitter } from 'node:events';

class DataFetcher extends EventEmitter {
  constructor() {
    super();
    // Allow caller to register .on('ready') in the current tick before firing
    process.nextTick(() => {
      this.emit('ready', { data: 'initial payload' });
    });
  }
}

const fetcher = new DataFetcher();
// Listener successfully catches the event!
fetcher.on('ready', (payload) => console.log('Received:', payload));
```

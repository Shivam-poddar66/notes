# The Event Loop: Core Mental Model in JavaScript

---

## 1) Core Purpose: What is the Event Loop?

The **Event Loop** is the coordinating mechanism inside the JavaScript runtime environment that bridges the **single-threaded Call Stack** with the **asynchronous Task Queues**.

Its sole responsibility can be described in one sentence:

> **The Event Loop continuously monitors the Call Stack. If the Call Stack is empty, it transfers queued asynchronous callbacks from the Task Queues to the Call Stack for execution.**

```
┌─────────────────────────────────────────────────────────────────────────┐
│                       THE COMPLETE EVENT LOOP SYSTEM                    │
│                                                                         │
│   +──────────────────────────+          +───────────────────────────+   │
│   │        CALL STACK        │          │      WEB / NODE APIS      │   │
│   │  (Executes Synchronous   │ ───────▶ │ (Timers, Network Fetch,   │   │
│   │   JavaScript code)       │          │  DOM Events, File I/O)    │   │
│   +─────────────▲────────────+          +─────────────┬─────────────+   │
│                 │                                     │                 │
│                 │ (Pushes work when stack is empty)   │                 │
│                 │                                     ▼                 │
│   +─────────────┴────────────+          +───────────────────────────+   │
│   │        EVENT LOOP        │ ◀─────── │      MICROTASK QUEUE      │   │
│   │  (The Infinite Traffic   │          │ (Promises, queueMicrotask)│   │
│   │   Controller)            │          +───────────────────────────+   │
│   │                          │          +───────────────────────────+   │
│   │                          │ ◀─────── │      MACROTASK QUEUE      │   │
│   │                          │          │ (setTimeout, DOM events)  │   │
│   +──────────────────────────+          +───────────────────────────+   │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The 5 Essential Components of the Async Engine

1. **Call Stack**: Executes one execution context at a time in LIFO order. Runs all synchronous code first.
2. **Memory Heap**: Stores objects, function instances, and closure environments.
3. **Web APIs / Background Threads**: C++ background subsystems handling hardware timers, network sockets, and file I/O.
4. **Microtask Queue (Job Queue)**: High-priority queue holding **Promise callbacks (`.then()`, `.catch()`, `.finally()`)**, **`queueMicrotask()`**, and **`MutationObserver`**.
5. **Macrotask Queue (Task Queue)**: Standard-priority queue holding **`setTimeout`**, **`setInterval`**, **`setImmediate` (Node)**, and **DOM UI events (`click`, `scroll`)**.

---

## 3) The Event Loop Algorithm (Pseudocode Specification)

According to the HTML5 Living Standard, the Event Loop executes in a continuous, deterministic cycle:

```js
// Conceptual Event Loop Cycle
while (true) {
  // 1. If Call Stack is NOT empty, let it run synchronous code to completion.
  if (!callStack.isEmpty()) {
    continue;
  }

  // 2. DRAIN the entire Microtask Queue until COMPLETELY EMPTY!
  while (!microtaskQueue.isEmpty()) {
    const microtask = microtaskQueue.dequeue();
    callStack.push(microtask);
    callStack.execute();
  }

  // 3. Optional: Browser Render Step (Layout, Paint, Compositing)
  if (isRepaintTime()) {
    runAnimationCallbacks(); // requestAnimationFrame
    renderDOM();
  }

  // 4. Run exactly ONE oldest Macrotask from the Macrotask Queue
  if (!macrotaskQueue.isEmpty()) {
    const macrotask = macrotaskQueue.dequeue();
    callStack.push(macrotask);
    callStack.execute();
  }
}
```

---

### The 2 Golden Rules of the Event Loop:
1. **Microtasks have absolute priority over Macrotasks**: Before a new Macrotask is allowed to run, the **entire Microtask Queue must be drained to zero**.
2. **Microtasks chaining microtasks run in the same tick**: If a Microtask queues another Microtask, the new Microtask executes **before** any Macrotask or screen render!

---

## 4) Step-by-Step Walkthrough with Full State Trace

Let us trace a multi-layered asynchronous execution snippet:

```js
console.log("1: Synchronous Start");

setTimeout(() => {
  console.log("2: Timeout 0ms");
}, 0);

Promise.resolve()
  .then(() => {
    console.log("3: Promise 1");
    return "Next";
  })
  .then(() => {
    console.log("4: Promise 2");
  });

queueMicrotask(() => {
  console.log("5: Explicit Microtask");
});

console.log("6: Synchronous End");
```

---

### Frame-by-Frame Execution State:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ STEP 1: Execute Synchronous Code                                                       │
│   • Log "1: Synchronous Start" ──▶ [ Call Stack executes & pops ]                      │
│   • `setTimeout(fn2, 0)`       ──▶ Web API handles timer; `fn2` queued in MACROTASK    │
│   • `Promise.resolve().then()` ──▶ `fn3` queued in MICROTASK                           │
│   • `queueMicrotask(fn5)`      ──▶ `fn5` queued in MICROTASK                           │
│   • Log "6: Synchronous End"   ──▶ [ Call Stack executes & pops ]                      │
│                                                                                        │
│   Console Output: "1: Synchronous Start", "6: Synchronous End"                         │
│   Call Stack    : [ EMPTY ]                                                            │
│   Microtask Queue: [ fn3 (Promise 1), fn5 (queueMicrotask) ]                           │
│   Macrotask Queue: [ fn2 (Timeout 0ms) ]                                               │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ STEP 2: Drain the Microtask Queue                                                      │
│   • Event Loop sees Call Stack is EMPTY.                                               │
│   • Pops `fn3` from Microtask Queue ──▶ Logs "3: Promise 1".                           │
│     (Chained `.then(fn4)` is now added to the Microtask Queue!)                        │
│   • Pops `fn5` from Microtask Queue ──▶ Logs "5: Explicit Microtask".                  │
│   • Pops `fn4` from Microtask Queue ──▶ Logs "4: Promise 2".                           │
│                                                                                        │
│   Microtask Queue is now COMPLETELY EMPTY (0 items).                                   │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ STEP 3: Execute ONE Macrotask                                                          │
│   • Event Loop checks Microtask Queue (Empty).                                         │
│   • Pops `fn2` from Macrotask Queue ──▶ Logs "2: Timeout 0ms".                         │
│                                                                                        │
│   FINAL OUTPUT SEQUENCE: 1 ──▶ 6 ──▶ 3 ──▶ 5 ──▶ 4 ──▶ 2                              │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 5) The Microtask Starvation Hazard (Freezing the Engine)

Because the Event Loop **must drain all microtasks before yielding to macrotasks or the UI renderer**, recursively scheduling microtasks will completely **lock the thread and freeze the browser tab**:

```js
// ❌ DANGEROUS: Infinite Microtask Loop (Starves the Event Loop!)
function infiniteMicrotasks() {
  Promise.resolve().then(() => {
    infiniteMicrotasks(); // Recursively queues new microtasks infinitely
  });
}

// infiniteMicrotasks(); 
// Result: 
// 1. Microtask queue NEVER empties.
// 2. Macrotasks (setTimeout, UI clicks) NEVER execute.
// 3. Screen NEVER repaints -> Browser freezes completely!
```

---

## 6) The Browser Render Pipeline in the Event Loop

In web browsers, the screen repaints at **60 frames per second (every 16.6ms)** or **120 frames per second (every 8.3ms)**.

```
┌────────────────────────────────────────────────────────────────────────┐
│                       BROWSER EVENT LOOP FRAME                         │
│                                                                        │
│   ┌──────────────┐      ┌────────────────┐      ┌──────────────────┐   │
│   │ Run 1 Macro- │ ──▶  │ Drain ALL      │ ──▶  │ RENDER STEP:     │   │
│   │ task         │      │ Microtasks     │      │ • rAF callbacks  │   │
│   └──────────────┘      └────────────────┘      │ • Style / Layout │   │
│                                                 │ • Paint / Pixels │   │
│                                                 └──────────────────┘   │
└────────────────────────────────────────────────────────────────────────┘
```

- **`requestAnimationFrame(callback)` (rAF)**: Fires right **before the browser performs a visual repaint**. Always use `requestAnimationFrame` for animations to avoid dropped frames.
- **`setTimeout(fn, 0)`**: Fires as a macrotask and has no synchronization with the display refresh rate.

---

## 7) Node.js Event Loop Phases Overview

While the Browser has a general Macrotask queue, Node.js uses **Libuv**, which divides macrotasks into **6 specific phases**:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        NODE.JS LIBUV EVENT LOOP                        │
│                                                                        │
│   1. Timers Phase:          Executes `setTimeout` and `setInterval`.   │
│   2. Pending Callbacks:     Executes I/O callbacks deferred from prior.│
│   3. Idle / Prepare:        Internal Node.js maintenance.              │
│   4. Poll Phase:            Retrieves new I/O events; blocks if idle.  │
│   5. Check Phase:           Executes `setImmediate()` callbacks.       │
│   6. Close Callbacks:       Executes `socket.on('close', ...)`.        │
│                                                                        │
│   ⭐ Note: `process.nextTick()` and Promises drain between EVERY phase!│
└────────────────────────────────────────────────────────────────────────┘
```

---

## 8) Master Interview Challenge: The Ultimate Event Loop Puzzle

```js
console.log("Start");

setTimeout(() => {
  console.log("Timeout 1");
  Promise.resolve().then(() => console.log("Promise inside Timeout"));
}, 0);

new Promise((resolve) => {
  console.log("Promise Constructor"); // Synchronous!
  resolve();
}).then(() => {
  console.log("Promise 1");
});

setTimeout(() => {
  console.log("Timeout 2");
}, 0);

console.log("End");
```

<details>
<summary><b>View Output & Complete Breakdown</b></summary>

**Output**:
```text
Start
Promise Constructor
End
Promise 1
Timeout 1
Promise inside Timeout
Timeout 2
```

**Step-by-Step Reason**:
1. `"Start"` logs synchronously.
2. `setTimeout(Timeout 1)` queued in Macrotask.
3. `new Promise(executor)` runs its executor **synchronously**, so `"Promise Constructor"` logs immediately.
4. `.then(Promise 1)` queued in Microtask.
5. `setTimeout(Timeout 2)` queued in Macrotask.
6. `"End"` logs synchronously.
7. Call stack empty -> Drains Microtask: `"Promise 1"` logs.
8. Event Loop runs 1st Macrotask: `"Timeout 1"` logs.
   - Its inner `.then()` adds `"Promise inside Timeout"` to Microtask queue.
9. Event Loop drains Microtask before running 2nd Macrotask: `"Promise inside Timeout"` logs.
10. Event Loop runs 2nd Macrotask: `"Timeout 2"` logs.
</details>

---

## 9) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         EVENT LOOP CHEAT SHEET                             |
+───────────────────────────+────────────────────────────────────────────────+
| Call Stack                | Runs synchronous code strictly first.          |
| Microtask Priority        | Always drained to ZERO before next Macrotask.  |
| Microtasks Include        | `Promise.then`, `queueMicrotask`, `MutationObs`|
| Macrotasks Include        | `setTimeout`, `setInterval`, DOM click events. |
| Promise Executor          | `new Promise((res) => { ... })` is SYNC!       |
| Starvation Risk           | Recursive microtasks freeze browser and stack. |
| UI Rendering              | Occurs after microtasks drain, before macrotask|
+───────────────────────────+────────────────────────────────────────────────+
```

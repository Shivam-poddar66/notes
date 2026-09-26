# 8) Hands-On Lab Experiments and Mental Model Verification

This lab contains 8 practical code experiments designed to verify your mental model of the V8 engine, Libuv threadpool, Event Loop phases, and priority queues. Run each script with `node` and analyze the annotated output traces.

---

## Lab Experiment 1: Priority Queue Sequencing

**Goal**: Trace exact execution order between `process.nextTick`, `Promise.then`, `queueMicrotask`, `setTimeout`, and `setImmediate`.

```javascript
// experiment-1.js
console.log('1: Sync script start');

setTimeout(() => {
  console.log('2: setTimeout (Timers Phase)');
}, 0);

setImmediate(() => {
  console.log('3: setImmediate (Check Phase)');
});

queueMicrotask(() => {
  console.log('4: queueMicrotask');
});

Promise.resolve().then(() => {
  console.log('5: Promise microtask 1');
  return 'data';
}).then(() => {
  console.log('6: Promise microtask 2 (Chained)');
});

process.nextTick(() => {
  console.log('7: nextTick 1');
  process.nextTick(() => {
    console.log('8: nextTick 2 (Nested)');
  });
});

console.log('9: Sync script end');
```

### Execution Trace & Explanation:
1. `1: Sync script start` $\rightarrow$ Executed immediately on the call stack.
2. `9: Sync script end` $\rightarrow$ Top-level synchronous script finishes.
3. `7: nextTick 1` $\rightarrow$ `nextTick` queue has highest microtask priority.
4. `8: nextTick 2 (Nested)` $\rightarrow$ `nextTick` queue is drained completely before Promise queue.
5. `4: queueMicrotask` & `5: Promise microtask 1` $\rightarrow$ Standard Promise microtasks execute.
6. `6: Promise microtask 2 (Chained)` $\rightarrow$ Next microtask tick.
7. `2: setTimeout` or `3: setImmediate` $\rightarrow$ Macrotasks execute according to Libuv phase.

---

## Lab Experiment 2: Deterministic `setImmediate` vs `setTimeout` in I/O

**Goal**: Prove why `setImmediate` always beats `setTimeout(..., 0)` inside an I/O callback.

```javascript
// experiment-2.js
import fs from 'node:fs';

fs.readFile(new URL(import.meta.url), () => {
  console.log('--- Inside fs.readFile I/O Callback (Poll Phase) ---');

  setTimeout(() => {
    console.log('Timer callback (Timers Phase)');
  }, 0);

  setImmediate(() => {
    console.log('Immediate callback (Check Phase)');
  });
});
```

### Explanation:
The `fs.readFile` callback runs in the **Poll Phase**. Once the Poll queue finishes, the loop **must** transition to the **Check Phase** before it can loop back around to the **Timers Phase**. Thus, `setImmediate` is guaranteed to execute before `setTimeout`.

---

## Lab Experiment 3: Libuv Threadpool Concurrency Benchmark

**Goal**: Observe the default 4-thread limit of Libuv by running 8 simultaneous crypto hash operations.

```javascript
// experiment-3.js
import crypto from 'node:crypto';

const start = Date.now();

function runHash(id) {
  crypto.pbkdf2('my-secret-password', 'salt-12345', 100000, 512, 'sha512', () => {
    console.log(`Task ${id} completed in ${Date.now() - start} ms`);
  });
}

// Launch 8 concurrent tasks
for (let i = 1; i <= 8; i++) {
  runHash(i);
}
```

### Expected Output (Default `UV_THREADPOOL_SIZE=4`):
```text
Task 1 completed in 210 ms
Task 2 completed in 214 ms
Task 3 completed in 215 ms
Task 4 completed in 218 ms
-------------------------------- (Tasks 5-8 were queued waiting for threads)
Task 5 completed in 420 ms
Task 6 completed in 422 ms
Task 7 completed in 425 ms
Task 8 completed in 428 ms
```

### Test with `UV_THREADPOOL_SIZE=8`:
```bash
UV_THREADPOOL_SIZE=8 node experiment-3.js
# All 8 tasks complete concurrently in ~220ms!
```

---

## Lab Experiment 4: Starving the Event Loop with `process.nextTick`

**Goal**: Demonstrate how recursive `nextTick` calls prevent timers and I/O from running.

```javascript
// experiment-4.js
let count = 0;

setTimeout(() => {
  console.log('Timer completed! (You will never see this if starved)');
}, 10);

function infiniteTick() {
  count++;
  if (count % 100000 === 0) {
    console.log(`Tick count: ${count}`);
  }
  // To fix: change process.nextTick(infiniteTick) to setImmediate(infiniteTick)
  process.nextTick(infiniteTick);
}

infiniteTick();
```

---

## Lab Experiment 5: V8 Hidden Class Shape Transitions

**Goal**: Measure property access speed difference between Monomorphic and Megamorphic shapes.

```javascript
// experiment-5.js
const ITERATIONS = 10_000_000;

// Setup Monomorphic objects (identical shape)
const monoObjects = Array.from({ length: 1000 }, () => ({ x: 1, y: 2 }));

// Setup Megamorphic objects (5+ different shapes)
const megaObjects = Array.from({ length: 1000 }, (_, i) => {
  switch (i % 5) {
    case 0: return { x: 1, y: 2 };
    case 1: return { y: 2, x: 1 };
    case 2: return { x: 1, z: 3, y: 2 };
    case 3: return { a: 0, x: 1, y: 2 };
    default: return { x: 1, y: 2, extra: true };
  }
});

function accessProps(arr) {
  let sum = 0;
  for (let i = 0; i < arr.length; i++) {
    sum += arr[i].x + arr[i].y;
  }
  return sum;
}

// Warmup JIT
for (let i = 0; i < 1000; i++) {
  accessProps(monoObjects);
}

console.time('Monomorphic Access (Fast)');
for (let i = 0; i < 5000; i++) {
  accessProps(monoObjects);
}
console.timeEnd('Monomorphic Access (Fast)');

console.time('Megamorphic Access (Slow)');
for (let i = 0; i < 5000; i++) {
  accessProps(megaObjects);
}
console.timeEnd('Megamorphic Access (Slow)');
```

---

## Lab Experiment 6: Measuring GC Pauses & Heap Allocation

**Goal**: Programmatically monitor Garbage Collection pause times.

```javascript
// experiment-6.js
import { PerformanceObserver, performance } from 'node:perf_hooks';

const obs = new PerformanceObserver((list) => {
  const entry = list.getEntries()[0];
  console.log(`[GC Event] Type: ${entry.name} | Duration: ${entry.duration.toFixed(3)} ms`);
});
obs.observe({ entryTypes: ['gc'] });

// Trigger memory churn
const leak = [];
for (let i = 0; i < 50; i++) {
  leak.push(new Array(100_000).fill('churn-data'));
}
```
Run with:
```bash
node --expose-gc experiment-6.js
```

# Sequential vs Parallel Async Flows: Masterclass Architecture

Understanding how to orchestrate asynchronous tasks—choosing between strict sequence, unbounded concurrency, and complex directed dependency graphs—is one of the most critical skills in production JavaScript and Node.js systems architecture.

---

## 1. Architectural & Mental Models

When executing asynchronous operations, the primary decision factor is **data dependency** versus **throughput/latency requirements**.

```
1. SEQUENTIAL (Waterfall)
   T0 ----------- T1 ----------- T2 ----------- T3
   [  Task A  ] -> [  Task B  ] -> [  Task C  ]
   Total Duration = Time(A) + Time(B) + Time(C)

2. PARALLEL / CONCURRENT (Fork-Join)
   T0 ----------- T1
   |-- [ Task A ] ----|
   |-- [ Task B ] ----| -> [ Aggregated Result ]
   |-- [ Task C ] ----|
   Total Duration = Max(Time(A), Time(B), Time(C))

3. PIPELINE / HYBRID (DAG - Directed Acyclic Graph)
   T0 ----------- T1 ----------- T2
   [ Fetch User ] ---+--> [ Fetch Orders ] --+--> [ Render Dashboard ]
                     +--> [ Fetch Prefs  ] --+
```

### Core Execution Modalities

| Pattern | Definition | Best Used When | Primary Risks |
| :--- | :--- | :--- | :--- |
| **Strict Sequential** | Step $N+1$ begins strictly after Step $N$ resolves | Step $N+1$ depends on output of Step $N$; strict rate limiting | Waterfall latency; underutilized I/O channels |
| **Unbounded Parallel** | All tasks initiated simultaneously in single tick | Tasks are mutually independent; maximum throughput needed | Socket exhaustion; rate limit/429 errors; memory spikes |
| **Pipelined / Graph (DAG)**| Independent subsets run in parallel; dependencies chain | Real-world applications (e.g., auth $\to$ parallel data $\to$ combine) | Race conditions in state aggregation; partial failures |

---

## 2. Sequential Flows: Patterns & Traps

### A. The Classic `for...of` vs `forEach` Trap

The most common junior mistake is attempting sequential asynchronous execution inside Array functional methods (`forEach`, `map`, `filter`).

```javascript
const userIds = [101, 102, 103];

// ❌ ANTI-PATTERN: Array.prototype.forEach DOES NOT await promises!
// forEach fires callbacks synchronously without observing return values.
async function brokenSequentialFetch(ids) {
  console.log("Start");
  ids.forEach(async (id) => {
    const data = await fetchUserData(id); // Runs concurrently in unhandled microtasks!
    console.log(`Fetched ${id}`);
  });
  console.log("End"); // Prints BEFORE data is fetched!
}
// Output:
// Start
// End
// Fetched 101
// Fetched 102
// Fetched 103

// ✅ CORRECT: for...of Loop (Pauses each iteration)
async function correctSequentialLoop(ids) {
  console.log("Start");
  const results = [];
  for (const id of ids) {
    const data = await fetchUserData(id);
    results.push(data);
    console.log(`Fetched ${id}`);
  }
  console.log("End");
  return results;
}
// Output:
// Start
// Fetched 101
// Fetched 102
// Fetched 103
// End
```

### B. Functional Sequential Pipeline with `Array.prototype.reduce`

When chaining promises without `async/await` loops (e.g., dynamic middleware pipelines or sequential reducers):

```javascript
/**
 * Executes an array of promise-returning functions strictly sequentially
 * @param {Array<() => Promise<any>>} taskFactories 
 * @returns {Promise<Array<any>>}
 */
function runSequentialPipeline(taskFactories) {
  return taskFactories.reduce((chainPromise, currentTaskFactory) => {
    return chainPromise.then((resultsAccumulator) => {
      return currentTaskFactory().then((result) => {
        resultsAccumulator.push(result);
        return resultsAccumulator;
      });
    });
  }, Promise.resolve([]));
}

// Async/await variation for functional accumulator:
async function asyncPipelineReducer(items, asyncTransformer, initialAcc) {
  let acc = initialAcc;
  for (const item of items) {
    acc = await asyncTransformer(acc, item);
  }
  return acc;
}
```

---

## 3. Parallel & Concurrent Flows: Fork-Join Architecture

### A. Eager Invocation vs Deferred Factory Functions

A critical distinction: **Promises represent in-flight operations**. Creating a promise starts execution immediately. To defer execution, you must wrap promise creation inside a factory function `() => Promise<T>`.

```javascript
// ❌ MISTAKE: Unintentional Parallelization during setup
function badSetup(tasks) {
  // If tasks is [fetch(a), fetch(b), fetch(c)], ALL requests are ALREADY sent!
  // It is impossible to run them sequentially now.
}

// ✅ CORRECT: Task Factories
const deferredTasks = [
  () => fetch('/api/1'),
  () => fetch('/api/2'),
  () => fetch('/api/3')
];
```

### B. Fork-Join with `Promise.all` and `Promise.allSettled`

```javascript
/**
 * Parallel Execution: Fail-Fast (Promise.all)
 * Total Time = Max(t1, t2, t3)
 */
async function fetchDashboardFailFast(userId) {
  try {
    // All 3 network sockets open simultaneously
    const [profile, settings, notifications] = await Promise.all([
      fetchProfile(userId),
      fetchSettings(userId),
      fetchNotifications(userId)
    ]);

    return { profile, settings, notifications };
  } catch (err) {
    // If ANY promise rejects, execution immediately drops here.
    // NOTE: Remaining requests still complete in the background (no auto-cancel).
    console.error("Dashboard failed to load:", err);
    throw err;
  }
}

/**
 * Parallel Execution: Resilient Partial-Success (Promise.allSettled)
 */
async function fetchDashboardResilient(userId) {
  const results = await Promise.allSettled([
    fetchProfile(userId),
    fetchSettings(userId),
    fetchNotifications(userId)
  ]);

  const [profileRes, settingsRes, notifRes] = results;

  return {
    profile: profileRes.status === 'fulfilled' ? profileRes.value : null,
    settings: settingsRes.status === 'fulfilled' ? settingsRes.value : DEFAULT_SETTINGS,
    notifications: notifRes.status === 'fulfilled' ? notifRes.value : [],
    errors: results.filter(r => r.status === 'rejected').map(r => r.reason)
  };
}
```

---

## 4. Complex Hybrid Flows: Directed Acyclic Graph (DAG)

Real-world enterprise applications are rarely pure waterfall or pure parallel. They form dependency trees.

```
       [ 1. Authenticate Token ]
                  |
         +--------+--------+
         |                 |
  [ 2. User Profile ]  [ 3. Feature Flags ]
         |                 |
  [ 4. User Orders ]       |
         |                 |
         +--------+--------+
                  |
       [ 5. Composite View Response ]
```

### Production Implementation: DAG Orchestrator

```javascript
async function renderUserPortal(token) {
  console.time('portal_load');

  // Step 1: Sequential dependency (Auth required first)
  const authSession = await authenticateUser(token);

  // Step 2 & 3: Run independent branches in parallel
  // Branch A: Profile -> Orders (sequential inside branch)
  // Branch B: Feature Flags (standalone)
  const [ordersResult, flagsResult] = await Promise.all([
    (async () => {
      const profile = await fetchUserProfile(authSession.userId);
      const orders = await fetchUserOrders(profile.customerId);
      return { profile, orders };
    })(),
    fetchFeatureFlags(authSession.tenantId)
  ]);

  console.timeEnd('portal_load');

  return {
    user: ordersResult.profile,
    orders: ordersResult.orders,
    features: flagsResult
  };
}
```

---

## 5. Memory Footprint & Resource Consumption

### Visualizing Resource Load

```
UNBOUNDED PARALLEL (10,000 files/requests)
RAM Usage:   /==============================\ Peak Spike (OOM Risk)
Sockets:     [=========== 10,000 open ==========] (EMFILE / Socket Timeout)

CONTROLLED BATCH / SEQUENTIAL
RAM Usage:   /---\_/---\_/---\_/---\_/---\ Constant / Stable
Sockets:     [ 5 open ]                           Predictable throughput
```

### Microtask & Garbage Collection Mechanics
- When 5,000 promises are launched in parallel with `Promise.all(promises)`, 5,000 `PromiseCapability` records and closure lexical environments remain pinned in heap memory until all or one rejects.
- Sequential loops allow the V8 Garbage Collector to reclaim intermediate resolution objects on each iteration pass once closures go out of scope.

---

## 6. Real-World Gotchas & Edge Cases

### Gotcha 1: Unintentional Async Waterfall

```javascript
// ❌ ACCIDENTAL WATERFALL: Independent tasks awaited in series
async function loadUserData(userId) {
  const profile = await api.getProfile(userId);     // 200ms
  const friends = await api.getFriends(userId);     // 200ms
  const posts   = await api.getRecentPosts(userId); // 200ms
  // Total: ~600ms latency
  return { profile, friends, posts };
}

// ✅ FIXED: Non-dependent promises awaited concurrently
async function loadUserDataOptimized(userId) {
  const profilePromise = api.getProfile(userId);
  const friendsPromise = api.getFriends(userId);
  const postsPromise   = api.getRecentPosts(userId);

  // All 3 network requests initiated simultaneously!
  const [profile, friends, posts] = await Promise.all([
    profilePromise,
    friendsPromise,
    postsPromise
  ]);
  // Total: ~200ms latency
  return { profile, friends, posts };
}
```

### Gotcha 2: Silent Background Leaks on `Promise.all` Rejection
If one promise rejects early in `Promise.all([p1, p2, p3])`, the runtime immediately rejects the parent promise. **However, `p2` and `p3` are not cancelled.** They continue executing in background memory, potentially causing mutation side effects or unhandled rejections if they fail later.

*Solution*: Use `AbortController` (covered in Topic 18) to propagate cancellation signals.

---

## 7. Senior Interview Challenges

### Challenge 1: Implement `asyncMapSequential` vs `asyncMapParallel`
Implement both sequential and parallel versions of asynchronous `map` without using third-party libraries.

```javascript
/**
 * Sequential Map: Executes transformer one element at a time
 */
async function mapSequential(array, asyncFn) {
  const results = [];
  for (let i = 0; i < array.length; i++) {
    const result = await asyncFn(array[i], i, array);
    results.push(result);
  }
  return results;
}

/**
 * Parallel Map: Executes all transformers concurrently
 */
async function mapParallel(array, asyncFn) {
  return Promise.all(array.map((item, index) => asyncFn(item, index, array)));
}

// Test Verification
const delay = (ms, val) => new Promise(res => setTimeout(() => res(val), ms));
const items = [10, 20, 30];

console.time('Sequential');
await mapSequential(items, async (x) => delay(100, x * 2)); // ~300ms
console.timeEnd('Sequential');

console.time('Parallel');
await mapParallel(items, async (x) => delay(100, x * 2));   // ~100ms
console.timeEnd('Parallel');
```

---

### Challenge 2: Dynamic Execution Order Graph
Write an async task runner that takes tasks with named dependencies and executes tasks as soon as their prerequisites finish, maximizing parallelism.

```javascript
/**
 * @typedef {Object} Task
 * @property {string} name
 * @property {string[]} deps - Array of task names that must resolve first
 * @property {(results: Record<string, any>) => Promise<any>} run
 */

async function executeTaskGraph(tasks) {
  const results = {};
  const inProgress = new Map(); // name -> Promise

  function executeTask(task) {
    if (inProgress.has(task.name)) {
      return inProgress.get(task.name);
    }

    const taskPromise = (async () => {
      // 1. Resolve all dependencies concurrently first
      const depTasks = task.deps.map(depName => {
        const found = tasks.find(t => t.name === depName);
        if (!found) throw new Error(`Missing dependency: ${depName}`);
        return executeTask(found);
      });

      await Promise.all(depTasks);

      // 2. Run current task once dependencies are met
      const output = await task.run(results);
      results[task.name] = output;
      return output;
    })();

    inProgress.set(task.name, taskPromise);
    return taskPromise;
  }

  // Launch execution for all root-level tasks
  await Promise.all(tasks.map(executeTask));
  return results;
}

// Verification
const taskList = [
  { name: 'auth', deps: [], run: async () => delay(50, { uid: 42 }) },
  { name: 'profile', deps: ['auth'], run: async (r) => delay(100, { name: 'Alice', uid: r.auth.uid }) },
  { name: 'config', deps: [], run: async () => delay(80, { theme: 'dark' }) },
  { name: 'render', deps: ['profile', 'config'], run: async (r) => `Rendered ${r.profile.name} with ${r.config.theme}` }
];

executeTaskGraph(taskList).then(console.log);
// Output after ~150ms:
// {
//   auth: { uid: 42 },
//   config: { theme: 'dark' },
//   profile: { name: 'Alice', uid: 42 },
//   render: 'Rendered Alice with dark'
// }
```

---

## 8. Summary & Decision Matrix

```
                        Is Task N dependent on Task N-1?
                                   /        \
                                 YES         NO
                                 /            \
                   [ Sequential Flow ]    Are there rate/system limits?
                    - for...of loop               /              \
                    - reduce pipeline           YES               NO
                                                /                  \
                                     [ Concurrency Pool ]    [ Parallel Flow ]
                                      (Topic 12: p-limit)     - Promise.all
                                                              - Promise.allSettled
```

- **Sequential (`for...of`, `reduce`)**: Use when operation ordering is mandatory or output of step $A$ directly feeds into step $B$.
- **Parallel (`Promise.all`, `Promise.allSettled`)**: Use when operations are disjoint, latency optimization is critical, and the environment can handle simultaneous socket/CPU/memory allocation.
- **Hybrid (Async DAG / Composite branches)**: Structure enterprise requests into independent parallel branches containing sequential sub-chains.

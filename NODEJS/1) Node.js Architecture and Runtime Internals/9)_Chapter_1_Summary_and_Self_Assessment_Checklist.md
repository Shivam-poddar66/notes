# 9) Chapter 1 Summary and Self-Assessment Checklist

## Chapter 1 Quick Revision Summary

### 1. Architecture Pillars
- **V8**: Compiles JS to machine code via Ignition (bytecode) and TurboFan (JIT). Manages the Heap.
- **Libuv**: Cross-platform C library managing non-blocking I/O (`epoll`/`kqueue`/`IOCP`), the Event Loop, and a 4-thread worker threadpool.
- **C++ Core Bindings**: Connects V8 JS calls with native Libuv / OpenSSL / OS primitives.

### 2. Event Loop Sequence
```text
Synchronous Code -> nextTick Queue -> Promise Microtask Queue ->
[1. Timers] -> microtasks ->
[2. Pending Callbacks] -> microtasks ->
[3. Idle/Prepare] -> microtasks ->
[4. Poll Phase (I/O)] -> microtasks ->
[5. Check Phase (setImmediate)] -> microtasks ->
[6. Close Callbacks] -> microtasks -> Loop around
```

### 3. Execution Priority Rules
1. **Synchronous Call Stack**: Highest immediate priority.
2. **`process.nextTick()`**: Runs before any Promise microtask.
3. **Promise Microtasks**: Runs before next Event Loop phase.
4. **`setImmediate()`**: Runs in Check phase, guaranteed immediately after Poll phase.
5. **`setTimeout(fn, 0)`**: Runs in Timers phase on next loop turn if deadline reached.

---

## Self-Assessment Challenge Questions

Test your understanding with these 10 core conceptual questions:

### Q1: Does `http.createServer` or `fetch()` use the Libuv Threadpool?
**Answer**: No. Network sockets use non-blocking OS kernel pollers (`epoll` on Linux, `kqueue` on macOS, `IOCP` on Windows). They do not consume worker threads.

### Q2: Why does `fs.readFile()` use the Libuv Threadpool?
**Answer**: Because standard POSIX file system system calls do not offer uniform non-blocking asynchronous APIs across all OS platforms. Libuv makes blocking `read()` calls inside worker threads to keep the main thread non-blocking.

### Q3: What happens when `UV_THREADPOOL_SIZE` is exhausted by heavy crypto operations?
**Answer**: File system operations (`fs.*`) and DNS lookups (`dns.lookup`) become queued and starved, causing massive latency spikes even though CPU and disk are not bottlenecked.

### Q4: What is the difference between V8's New Space and Old Space?
**Answer**: New Space holds short-lived objects collected quickly via the Scavenger algorithm (semi-space copying). Objects that survive multiple scavenge cycles are tenured to Old Space, which is collected via Mark-Sweep-Compact.

### Q5: Why is `delete obj.prop` considered a performance anti-pattern in V8?
**Answer**: It deletes properties from the Hidden Class (Shape/Map), forcing V8 to abandon fast inline caching and transition the object into slow dictionary mode.

---

## Chapter 1 Mastery Verification Checklist

Check off each item once you can explain or implement it with confidence:

- [ ] Explain the roles of V8, Libuv, C++ Bindings, and OS syscalls.
- [ ] Diagram and explain all 6 phases of the Libuv Event Loop from memory.
- [ ] Explain the exact priority difference between `process.nextTick()`, `Promise.then()`, and `queueMicrotask()`.
- [ ] Identify which standard APIs use the Libuv threadpool vs non-blocking OS kernel descriptors.
- [ ] Size and configure `UV_THREADPOOL_SIZE` correctly for multi-core servers.
- [ ] Explain how V8 Hidden Classes and Inline Caching optimize property lookups.
- [ ] Explain how Minor GC (Scavenger) and Major GC (Mark-Sweep-Compact) manage memory.
- [ ] Explain why `setImmediate` always executes before `setTimeout(..., 0)` inside an I/O callback.
- [ ] Diagnose and prevent Event Loop starvation caused by synchronous operations or recursive microtasks.
- [ ] Monitor Event Loop delay programmatically using `perf_hooks.monitorEventLoopDelay()`.

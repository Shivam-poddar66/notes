# 8) Chapter 2 Summary and Self-Assessment Checklist

## Chapter 2 Quick Revision Summary

### 1. File System (`fs` and `fs/promises`)
- **Libuv Threadpool**: All async POSIX `fs.*` calls execute on the Libuv threadpool (default: 4 threads). `fs.*Sync()` calls run directly on the V8 main thread and block the Event Loop.
- **File Descriptors**: Non-negative integer references to kernel open-file table entries. Must always be closed in `finally` blocks to prevent descriptor exhaustion (`EMFILE`).
- **Atomic Writes**: Writing directly to a file is vulnerable to partial corruption. Always write to a unique temporary file (`.tmp`), call `fileHandle.sync()`, and atomically replace using `fs.rename()`.
- **Directory Traversal**: Use `fs.readdir(dir, { recursive: true, withFileTypes: true })` to avoid per-file `stat()` syscalls.

### 2. Paths & URLs
- **`path.join()` vs `path.resolve()`**: `join` concatenates and normalizes segments. `resolve` simulates `cd` commands from right-to-left to construct an absolute path.
- **ESM Path Standard**: CommonJS `__dirname`/`__filename` do not exist in ESM. Use `import.meta.dirname` / `import.meta.filename` (Node 20.11+) or `fileURLToPath(import.meta.url)`.
- **Path Traversal Defense**: Always check `safePath.startsWith(SAFE_ROOT + path.sep)` to prevent traversal attacks (`../../etc/passwd`).

### 3. EventEmitter (`events`)
- **Synchronous Dispatch**: `emitter.emit()` executes all attached listeners **synchronously** in the current call stack.
- **Error Event Handling**: An unhandled `'error'` event without any active listener will crash the Node.js process immediately.
- **Leak Detection**: Emits a warning when more than 10 listeners are attached to a single event. Clean up with `AbortSignal` or `.off()`.

### 4. Process, OS & Diagnostics
- **Memory Inspection**: `process.memoryUsage()` returns `rss` (Resident Set Size in RAM), `heapTotal`, `heapUsed`, `external` (Buffers / C++ native allocations), and `arrayBuffers`.
- **High-Resolution Timers**: Use `process.hrtime.bigint()` for monotonic nanosecond-accurate performance metrics.
- **Crash Management**: Never resume on `uncaughtException`. Log, trigger graceful teardown of DB/HTTP handles, and exit with non-zero code.

### 5. Cryptography (`crypto`)
- **Hashing vs Passwords**: Use SHA-256 for data checksums; use memory-hard KDFs (`crypto.scrypt` or `argon2`) with unique random salts for passwords.
- **Symmetric Encryption**: Always use AEAD ciphers like `aes-256-gcm` with unique 96-bit IVs and verify the `authTag`.
- **Timing Safe Comparisons**: Use `crypto.timingSafeEqual()` on equal-length Buffers to prevent side-channel timing attacks.

### 6. Modern Web APIs
- **Global `fetch`**: Native HTTP client backed by `undici`. Supports `AbortSignal.timeout()` and `AbortSignal.any()`.
- **Performance**: `performance.now()`, `PerformanceObserver`, and `monitorEventLoopDelay()`.

---

## Self-Assessment Challenge Questions

### Q1: Why does calling `fs.readFileSync()` inside an Express request handler hurt performance more than calling `crypto.pbkdf2Sync()`?
**Answer**: Both block the single V8 JavaScript thread! Neither runs on the Libuv threadpool in their `*Sync` forms. When the main thread is executing synchronous disk reads or synchronous crypto operations, no incoming TCP connections, timeouts, or microtasks can execute.

### Q2: What causes an `EMFILE: too many open files` error in Node.js, and how do you fix it?
**Answer**: `EMFILE` occurs when the process reaches the operating system's per-process limit of open file descriptors (`ulimit -n` on Linux). This is caused by forgetting to close `FileHandle` instances, leaking unconsumed read streams, or attempting to open thousands of files concurrently without concurrency throttling (e.g. using `p-limit`).

### Q3: Why is `crypto.timingSafeEqual()` required when comparing HMAC signatures or auth tokens?
**Answer**: Standard equality (`===`) short-circuits on the first mismatched byte. An attacker can repeatedly send requests and measure microsecond response time differences to discover correct characters one by one. `crypto.timingSafeEqual()` always compares every byte in constant time.

### Q4: If an `EventEmitter` listener throws an asynchronous error inside an `async () => {}` handler, will the emitter's `'error'` listener catch it?
**Answer**: No! An `async` function returns a rejected Promise. Unless the `EventEmitter` was created with `{ captureRejections: true }`, an unhandled Promise rejection (`unhandledRejection`) occurs rather than an `'error'` event dispatch.

### Q5: What is the difference between `process.memoryUsage().heapUsed` and `process.memoryUsage().rss`?
**Answer**: `heapUsed` measures only the active V8 JavaScript objects (closures, object literals, strings). `rss` (Resident Set Size) is the total physical memory allocated to the process by the OS, including the V8 heap, C++ addon allocations, Libuv handles, thread stacks, and external Node.js Buffers.

---

## Chapter 2 Mastery Verification Checklist

Check off each item once you can explain or implement it with confidence:

- [ ] Open, read, and write files using low-level File Descriptors (`fs.open`, `FileHandle`) with proper resource cleanup.
- [ ] Implement atomic file updates using the `.tmp` + `fileHandle.sync()` + `fs.rename()` pattern.
- [ ] Recursively traverse directory trees using `fs.readdir` with `withFileTypes: true`.
- [ ] Secure file paths against Directory Traversal attacks using `path.resolve()` boundary checks.
- [ ] Retrieve directory and file paths in ESM using `import.meta.dirname` and `fileURLToPath(import.meta.url)`.
- [ ] Implement a strongly typed `TypedEventEmitter` in TypeScript.
- [ ] Attach and automatically detach event listeners using `AbortSignal`.
- [ ] Handle process lifecycle signals (`SIGTERM`, `SIGINT`) to orchestrate a graceful zero-downtime shutdown.
- [ ] Profile memory allocations across `rss`, `heapUsed`, and `external` using `process.memoryUsage()`.
- [ ] Measure micro-benchmarks accurately using `process.hrtime.bigint()`.
- [ ] Hash and verify passwords securely with `crypto.scrypt()` and random salts.
- [ ] Encrypt and decrypt data using authenticated AES-256-GCM with authentication tags.
- [ ] Compare signatures and API tokens securely using `crypto.timingSafeEqual()`.
- [ ] Execute HTTP requests using global `fetch` with `AbortSignal.timeout()` and `AbortSignal.any()`.
- [ ] Monitor Event Loop lag in production with `perf_hooks.monitorEventLoopDelay()`.

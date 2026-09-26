# 4) Process, OS, and Diagnostics APIs

## Executive Overview
The global `process` object and the core `node:os` module provide a bridge between the V8 JavaScript runtime and the host operating system. They expose vital capabilities for process lifecycle management, signal trapping (`SIGTERM`/`SIGINT`), runtime resource introspection, high-resolution performance benchmarking, and V8 heap diagnostics.

---

## 1. The Global `process` Object Architecture

The `process` object is a global instance of `EventEmitter` initialized during Node.js bootstrap (`src/node_process_methods.cc`). It provides direct access to the current executing process state.

### 1.1 Core Process Properties & Context
```typescript
import process from 'node:process';

// Process Identifiers
console.log('Process ID (PID):', process.pid);
console.log('Parent Process ID (PPID):', process.ppid);
console.log('Node.js Version:', process.version);
console.log('Platform / Architecture:', process.platform, process.arch);

// Directory & Arguments
console.log('Current Working Directory:', process.cwd());
console.log('Executable Path:', process.execPath);
console.log('Command Line Arguments:', process.argv);

// Setting exit code cleanly without abrupt immediate termination
process.exitCode = 0;
```

---

## 2. Memory Diagnostics: `process.memoryUsage()`

Understanding how Node.js allocates memory is critical for detecting memory leaks and tuning container resource limits (`--max-old-space-size`).

```typescript
import process from 'node:process';

const mem = process.memoryUsage();
console.log({
  rss: `${(mem.rss / 1024 / 1024).toFixed(2)} MB`,
  heapTotal: `${(mem.heapTotal / 1024 / 1024).toFixed(2)} MB`,
  heapUsed: `${(mem.heapUsed / 1024 / 1024).toFixed(2)} MB`,
  external: `${(mem.external / 1024 / 1024).toFixed(2)} MB`,
  arrayBuffers: `${(mem.arrayBuffers / 1024 / 1024).toFixed(2)} MB`,
});
```

### Memory Component Definitions

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   Resident Set Size (RSS) Total                        │
│ ┌───────────────────────────────────────┐ ┌──────────────────────────┐ │
│ │            V8 Engine Heap             │ │       C++ / Native       │ │
│ │ ┌───────────────────┬───────────────┐ │ │ - Libuv Handles          │ │
│ │ │   heapUsed        │   Unused      │ │ │ - OpenSSL State          │ │
│ │ │ (Active JS Objects│ Allocated     │ │ │ - Node.js Core Bindings  │ │
│ │ │  Strings, Closures│ V8 Memory)    │ │ ├──────────────────────────┤ │
│ │ └───────────────────┴───────────────┘ │ │     External Memory      │ │
│ │             heapTotal                 │ │ - Buffers outside V8     │ │
│ └───────────────────────────────────────┘ │ - ArrayBuffers           │ │
│                                           └──────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────┘
```

- **`rss` (Resident Set Size)**: Total memory allocated for the process in RAM (includes V8 heap, C++ bindings, thread stacks, shared libraries).
- **`heapTotal`**: Total memory allocated by V8 for JavaScript object storage.
- **`heapUsed`**: Memory currently occupied by actual live JavaScript objects, closures, strings.
- **`external`**: Memory bound to JavaScript objects but allocated in C++ memory outside V8 (e.g. `Buffer` instances, `crypto` keys).
- **`arrayBuffers`**: Memory allocated for `ArrayBuffer` and `SharedArrayBuffer` structures (included in `external`).

---

## 3. High-Resolution Benchmarking (`process.hrtime.bigint()`)

Standard `Date.now()` is tied to the system clock and susceptible to clock drift or NTP adjustments. For measuring sub-millisecond execution times, micro-benchmarks, and network latencies, always use `process.hrtime.bigint()`, which queries monotonic CPU hardware timers.

```typescript
import process from 'node:process';

export function benchmarkOperation(name: string, fn: () => void): void {
  const start = process.hrtime.bigint();
  
  fn(); // Execute target operation
  
  const end = process.hrtime.bigint();
  const elapsedNanoseconds = end - start;
  const elapsedMicroseconds = Number(elapsedNanoseconds) / 1_000;
  const elapsedMilliseconds = Number(elapsedNanoseconds) / 1_000_000;

  console.log(`[BENCHMARK] ${name}: ${elapsedMilliseconds.toFixed(3)} ms (${elapsedMicroseconds.toFixed(1)} µs)`);
}
```

---

## 4. Process Lifecycle Events & Crash Resilience

Node.js provides essential hooks into runtime failure modes:

| Event | Firing Condition | Recommended Action |
|---|---|---|
| `uncaughtExceptionMonitor` | An unhandled exception was thrown (fires before `uncaughtException`). | Log error metrics / telemetry without altering crash semantics. |
| `uncaughtException` | Uncaught synchronous error reaches top of call stack. | Log error, clean up state, and **exit immediately (`process.exit(1)`)**. |
| `unhandledRejection` | A Promise rejected without a `.catch()` handler. | Log warning or fail hard in strict mode. |
| `beforeExit` | Event loop queue is empty, right before process termination. | Can schedule async cleanup work. |
| `exit` | Process is terminating. | Only synchronous cleanup allowed; Event Loop is already dead. |

> [!CAUTION]
> **Never attempt to "resume" an application after an `uncaughtException`**. The V8 runtime state is corrupt and unrecoverable; variables may be in an inconsistent state, causing cascading failures and data corruption. Always exit and let your orchestrator (Kubernetes, PM2, Systemd) restart the worker.

---

## 5. Host Operating System Introspection (`node:os`)

The `node:os` module allows inspecting hardware constraints:

```typescript
import os from 'node:os';

console.log('OS Type:', os.type());                   // 'Linux', 'Darwin', 'Windows_NT'
console.log('Uptime (seconds):', os.uptime());
console.log('Total System RAM (GB):', (os.totalmem() / 1024 ** 3).toFixed(2));
console.log('Free System RAM (GB):', (os.freemem() / 1024 ** 3).toFixed(2));
console.log('CPU Cores:', os.cpus().length);
console.log('CPU Model:', os.cpus()[0]?.model);
console.log('System Load Averages (1m, 5m, 15m):', os.loadavg()); // POSIX only
```

---

## 6. Production Pattern: Graceful Shutdown Coordinator

In Kubernetes and cloud environments, pods receive `SIGTERM` when scaling down or deploying. Applications must stop accepting new traffic, finish inflight HTTP requests, close database connection pools, and exit cleanly.

```typescript
import http from 'node:http';
import process from 'node:process';

export class GracefulShutdownManager {
  private isShuttingDown = false;
  private connections = new Set<any>();

  constructor(
    private server: http.Server,
    private dbPool: { close: () => Promise<void> },
    private timeoutMs: number = 10000
  ) {
    this.trackConnections();
    this.registerSignalTraps();
  }

  private trackConnections() {
    this.server.on('connection', (socket) => {
      this.connections.add(socket);
      socket.on('close', () => this.connections.delete(socket));
    });
  }

  private registerSignalTraps() {
    const handleSignal = async (signal: string) => {
      if (this.isShuttingDown) return;
      this.isShuttingDown = true;
      console.log(`[SHUTDOWN] Received ${signal}. Starting graceful shutdown...`);

      // 1. Set forced timeout timer
      const forceExitTimer = setTimeout(() => {
        console.error('[SHUTDOWN] Forced shutdown timeout expired. Exiting immediately.');
        process.exit(1);
      }, this.timeoutMs);
      forceExitTimer.unref(); // Don't let this timer keep event loop alive

      try {
        // 2. Stop accepting new HTTP requests
        await new Promise<void>((resolve, reject) => {
          this.server.close((err) => (err ? reject(err) : resolve()));
        });
        console.log('[SHUTDOWN] HTTP server closed to new connections.');

        // 3. Close open keep-alive idle connections
        for (const socket of this.connections) {
          socket.destroy();
        }

        // 4. Drain and close database connection pools
        await this.dbPool.close();
        console.log('[SHUTDOWN] Database pool successfully drained.');

        console.log('[SHUTDOWN] Graceful shutdown completed cleanly.');
        process.exit(0);
      } catch (error) {
        console.error('[SHUTDOWN] Error encountered during shutdown:', error);
        process.exit(1);
      }
    };

    process.on('SIGTERM', () => handleSignal('SIGTERM'));
    process.on('SIGINT', () => handleSignal('SIGINT'));
  }
}
```

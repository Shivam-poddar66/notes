# 7) Synchronous vs Asynchronous Execution and Event Loop Blocking

## Executive Overview
The cardinal rule of high-performance Node.js engineering is: **"Don't Block the Event Loop."** Because user JavaScript runs on a single main thread, any synchronous computation that monopolizes CPU time freezes all other concurrent users, pending HTTP requests, timers, and socket connections across the entire application.

---

## 1. The Anatomy of an Event Loop Block

Consider a Node.js web server handling 5,000 concurrent client requests.

```text
Request 1 (Normal I/O) ────> [ Event Loop: Main Thread ] ──> Fast Response (2ms)
Request 2 (CPU Heavy)  ────> [ Synchronous Loop: 3000ms ]
Request 3 (Normal I/O) ────> [ WAITING IN QUEUE... ] ──────> Frozen! (3000ms delay)
Request 4 (Health Check)───> [ WAITING IN QUEUE... ] ──────> K8s Probe Timeout / Pod Killed!
```

When Request 2 triggers a heavy synchronous operation (e.g. parsing a 50MB JSON payload or executing an unindexed nested loop), the main thread cannot process the Poll phase. Requests 3 and 4 sit completely stalled in the kernel network buffer.

---

## 2. Common Event Loop Blockers in Production

### A. Large JSON Parsing & Serialization
`JSON.parse()` and `JSON.stringify()` are synchronous $O(N)$ operations that execute entirely on the main thread:
```javascript
// BAD: 50MB payload blocks the event loop for ~150ms!
const data = JSON.parse(hugeJsonStringFromDisk);

// SOLUTION: Stream parse using JSONStream or stream-json
import StreamJSON from 'stream-json';
```

### B. Regular Expression Catastrophic Backtracking (ReDoS)
Vulnerable regular expressions with overlapping nested quantifiers (e.g., `/(a+)+$/`) take exponential time $O(2^N)$ on mismatch inputs:
```javascript
// VULNERABLE: Takes seconds or minutes on input: "aaaaaaaaaaaaaaaaaaaaaaaaaaaa!"
const regex = /(a+)+$/;
regex.test('aaaaaaaaaaaaaaaaaaaaaaaaaaaa!'); // FREEZES ENTIRE SERVER!

// SOLUTION: Use safe non-backtracking engines (e.g. node-re2 / Google RE2)
```

### C. Synchronous Cryptography & Hashing
```javascript
import crypto from 'node:crypto';

// BAD: Blocks main thread for ~250ms per login!
const hash = crypto.pbkdf2Sync('password', 'salt', 100000, 64, 'sha512');

// GOOD: Dispatched asynchronously to Libuv threadpool
crypto.pbkdf2('password', 'salt', 100000, 64, 'sha512', (err, key) => {
  // Main thread remained 100% free during hashing!
});
```

### D. Synchronous File System APIs in Request Handlers
```javascript
// NEVER use Sync APIs inside request handlers:
app.get('/config', (req, res) => {
  const config = fs.readFileSync('/etc/app.json'); // BLOCKS ALL REQUESTS!
  res.send(config);
});
```

---

## 3. Mitigation Strategies for Heavy Computations

```
┌──────────────────────────────────────────────┬────────────────────────────────────────────────────────┐
│ Computation Type                             │ Recommended Architectural Solution                     │
├──────────────────────────────────────────────┼────────────────────────────────────────────────────────┤
│ Long Iterative Array/Data Processing         │ Time-Slicing / Chunking with `setImmediate()`          │
│ CPU-Intensive Tasks (Image/Video/Crypto/ML)  │ Worker Threads (`worker_threads` / `piscina`)          │
│ External CLI / Legacy Binaries (FFmpeg/GPG)  │ Child Processes (`child_process.spawn`)                │
│ Large Data Transfers (Files, Network Pipes)  │ Node.js Streams (`stream.pipeline`)                    │
└──────────────────────────────────────────────┴────────────────────────────────────────────────────────┘
```

### Strategy 1: Time-Slicing with `setImmediate`
Break a large synchronous loop of $1,000,000$ iterations into batches of $1,000$, yielding control back to the Event Loop between chunks:

```javascript
function processLargeDatasetInChunks(items, batchSize = 1000) {
  let index = 0;

  function processChunk() {
    const chunkEnd = Math.min(index + batchSize, items.length);

    while (index < chunkEnd) {
      // Process item
      heavyTransform(items[index]);
      index++;
    }

    if (index < items.length) {
      // Yield control back to Libuv to process pending I/O and timers!
      setImmediate(processChunk);
    } else {
      console.log('All items processed without freezing the server!');
    }
  }

  processChunk();
}
```

### Strategy 2: Offloading to Worker Threads
```javascript
import { Worker } from 'node:worker_threads';

function runCpuTask(data) {
  return new Promise((resolve, reject) => {
    const worker = new Worker('./cpu-worker.js', { workerData: data });
    worker.on('message', resolve);
    worker.on('error', reject);
    worker.on('exit', (code) => {
      if (code !== 0) reject(new Error(`Worker stopped with exit code ${code}`));
    });
  });
}
```

---

## 4. Monitoring Event Loop Delay in Production

Node.js provides the native `perf_hooks.monitorEventLoopDelay()` histogram API to track event loop lag with microsecond precision:

```javascript
import { monitorEventLoopDelay } from 'node:perf_hooks';

// Resolution: sample every 10 milliseconds
const histogram = monitorEventLoopDelay({ resolution: 10 });
histogram.enable();

setInterval(() => {
  console.log({
    minLag: `${(histogram.min / 1e6).toFixed(2)} ms`,
    meanLag: `${(histogram.mean / 1e6).toFixed(2)} ms`,
    p50Lag: `${(histogram.percentile(50) / 1e6).toFixed(2)} ms`,
    p99Lag: `${(histogram.percentile(99) / 1e6).toFixed(2)} ms`,
    maxLag: `${(histogram.max / 1e6).toFixed(2)} ms`,
  });

  // Alert if p99 latency exceeds 50ms
  if (histogram.percentile(99) > 50 * 1e6) {
    console.error('CRITICAL: Event Loop is experiencing severe blocking lag!');
  }

  histogram.reset();
}, 5000);
```

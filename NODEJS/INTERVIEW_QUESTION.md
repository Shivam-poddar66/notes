# Node.js Interview Questions & Detailed Answers (50 Essential Q&A)

A comprehensive guide containing 50 essential Node.js interview questions and detailed answers covering core architecture, event loop mechanics, streams, async programming, security, scaling, and performance optimization.

---

## Table of Contents
1. [Core Architecture & Fundamentals (Q1 – Q10)](#1-core-architecture--fundamentals-q1--q10)
2. [Streams & Buffer Management (Q11 – Q15)](#2-streams--buffer-management-q11--q15)
3. [Asynchronous Patterns & Event Emitter (Q16 – Q20)](#3-asynchronous-patterns--event-emitter-q16--q20)
4. [Web Servers & Express.js Integration (Q21 – Q30)](#4-web-servers--expressjs-integration-q21--q30)
5. [Database Interaction & Data Persistence (Q31 – Q35)](#5-database-interaction--data-persistence-q31--q35)
6. [Multiprocessing, Clustering & Scaling (Q36 – Q42)](#6-multiprocessing-clustering--scaling-q36--q42)
7. [Security, Testing & Performance Tuning (Q43 – Q50)](#7-security-testing--performance-tuning-q43--q50)

---

## 1) Core Architecture & Fundamentals (Q1 – Q10)

### Q1: What is Node.js, and how does its architecture differ from traditional web server environments?
**Answer:**
Node.js is an open-source, cross-platform, single-threaded JavaScript runtime environment built on Chrome's V8 engine. Unlike traditional multi-threaded web servers (like Apache HTTP Server) that spawn a new OS thread per incoming request (which consumes memory and context-switching overhead), Node.js operates on an **event-driven, non-blocking I/O model**.

Node.js uses a single main thread to handle client requests. Asynchronous I/O operations (such as reading from disk, database queries, or network requests) are delegated to the underlying system OS or libuv thread pool. Once the I/O operation completes, a callback is queued and picked up by the Event Loop, allowing a single server process to handle tens of thousands of concurrent connections effortlessly.

---

### Q2: What is libuv, and what role does it play in Node.js?
**Answer:**
`libuv` is a C library developed initially for Node.js to provide cross-platform asynchronous I/O abstractions. It handles:
1. **The Event Loop**: Managing timers, I/O callbacks, poll phase, and check handles across Windows (IOCP), Linux (epoll), and macOS (kqueue).
2. **ThreadPool**: A pool of background worker threads (default size is 4, configurable via `process.env.UV_THREADPOOL_SIZE`). Libuv delegates expensive tasks that cannot be performed asynchronously at the OS level to this thread pool, including:
   - File System operations (`fs` module).
   - Cryptographic CPU-heavy functions (`crypto.pbkdf2`, `crypto.randomBytes`).
   - Compression algorithms (`zlib`).
   - DNS lookups (`dns.lookup`).

---

### Q3: Explain the Event Loop in Node.js and its 6 major phases.
**Answer:**
The Event Loop is the core mechanism that enables Node.js to perform non-blocking I/O operations. It runs continuously in a single thread, executing callbacks across six distinct phases in order:

```text
   ┌───────────────────────────┐
┌─>│           timers          │ <── setTimeout(), setInterval()
│  └─────────────┬─────────────┘
│  ┌─────────────┴─────────────┐
│  │     pending callbacks     │ <── Executed I/O callbacks deferred from previous loop
│  └─────────────┬─────────────┘
│  ┌─────────────┴─────────────┐
│  │    idle, prepare phase    │ <── Internal Node.js engine usage
│  └─────────────┬─────────────┘
│  ┌─────────────┴─────────────┐
│  │         poll phase        │ <── Retrieve new I/O events; execute I/O callbacks
│  └─────────────┬─────────────┘
│  ┌─────────────┴─────────────┐
│  │        check phase        │ <── setImmediate() callbacks
│  └─────────────┬─────────────┘
│  ┌─────────────┴─────────────┐
└──│      close callbacks      │ <── e.g. socket.on('close', ...)
   └───────────────────────────┘
```

1. **Timers Phase**: Executes callbacks scheduled by `setTimeout()` and `setInterval()`.
2. **Pending Callbacks Phase**: Executes I/O callbacks deferred from the previous iteration.
3. **Idle, Prepare Phase**: Internal engine operations.
4. **Poll Phase**: Retrieves new I/O events and executes their callbacks. If no timers are ready and no `setImmediate` is scheduled, Node will block here waiting for I/O.
5. **Check Phase**: Executes `setImmediate()` callbacks.
6. **Close Callbacks Phase**: Executes close handlers like `socket.on('close', ...)`.

---

### Q4: What is the difference between `process.nextTick()` and `setImmediate()`?
**Answer:**

| Feature | `process.nextTick()` | `setImmediate()` |
| :--- | :--- | :--- |
| **Execution Queue** | Microtask Queue (Next Tick Queue) | Check Phase of the Event Loop |
| **Timing** | Fires **immediately after the current operation finishes**, before the Event Loop proceeds to the next phase | Fires during the **Check Phase** of the current or next Event Loop iteration |
| **Precedence** | Runs **before** `Promise.then` and before any Event Loop phase transitions | Runs after the Poll phase completes |
| **Risk** | Recursive `process.nextTick()` calls will cause **Event Loop starvation**, blocking I/O | Safe against Event Loop starvation |

```js
setImmediate(() => console.log("1: setImmediate"));
process.nextTick(() => console.log("2: process.nextTick"));
Promise.resolve().then(() => console.log("3: Promise.then"));

// Output:
// 2: process.nextTick
// 3: Promise.then
// 1: setImmediate
```

---

### Q5: What are Microtasks vs. Macrotasks in Node.js?
**Answer:**
- **Microtasks**: Include `process.nextTick()` callbacks and `Promise` reaction callbacks (`.then()`, `.catch()`, `.finally()`). The Microtask queue is processed completely immediately after the current JavaScript operation finishes, before moving to the next Event Loop phase.
- **Macrotasks**: Include `setTimeout()`, `setInterval()`, `setImmediate()`, and I/O callbacks. Macrotasks are executed one by one within their respective Event Loop phases.

---

### Q6: How does V8 compile and execute JavaScript in Node.js?
**Answer:**
Google Chrome's **V8 engine** compiles JavaScript directly to native machine code before execution using a two-tier compiler architecture:
1. **Ignition (Interpreter)**: Quickly generates and executes bytecode from abstract syntax trees (AST) to achieve fast initial startup.
2. **TurboFan (Optimizing Compiler)**: Collects profiling feedback during execution (type feedback). Frequently executed functions ("hot code") are passed to TurboFan, which re-compiles the bytecode into highly optimized native machine code. If type assumptions change at runtime, TurboFan performs **de-optimization** back to bytecode.

---

### Q7: What are Global Objects in Node.js? List important examples.
**Answer:**
Global objects are available in all modules without explicitly importing them using `require()` or `import`.

Key Node.js Global Objects:
- `global`: The root global namespace object (analogous to `window` in browsers).
- `process`: Provides information and control over the current Node.js process (e.g., `process.env`, `process.exit()`, `process.cwd()`).
- `Buffer`: Used to handle raw binary data.
- `console`: Standard output/error printing (`console.log`, `console.error`).
- `__dirname`: Absolute path of the directory containing the current module *(CommonJS only)*.
- `__filename`: Absolute path of the current module file *(CommonJS only)*.
- `setTimeout()`, `clearTimeout()`, `setInterval()`, `setImmediate()`.

---

### Q8: Compare CommonJS (CJS) vs. ES Modules (ESM) in Node.js.
**Answer:**

| Feature | CommonJS (CJS) | ES Modules (ESM) |
| :--- | :--- | :--- |
| **Syntax** | `const fs = require('fs');` / `module.exports` | `import fs from 'node:fs';` / `export default` |
| **Loading Mechanism** | **Synchronous**, loaded at runtime | **Asynchronous**, parsed and linked at compile-time |
| **Top-Level `await`** | Not supported | Supported natively |
| **File Extension** | `.js` (default legacy) or `.cjs` | `.mjs` or `"type": "module"` in `package.json` |
| **Module Scope** | Contains `__dirname` and `__filename` | Does not contain `__dirname` (Use `import.meta.url`) |

---

### Q9: What happens when you type `require('module_name')` in Node.js?
**Answer:**
Node.js follows a 5-step module resolution algorithm:
1. **Resolving**: Determines the absolute file path of the module (Core module $\rightarrow$ File path `./foo` $\rightarrow$ `node_modules` lookup up the directory tree).
2. **Loading**: Reads the file content from disk.
3. **Wrapping**: Wraps the code in a module wrapper function to enforce local scope:
   ```js
   (function(exports, require, module, __filename, __dirname) {
     // Module code lives here
   });
   ```
4. **Evaluating**: V8 executes the wrapped function.
5. **Caching**: Caches the exported object in `require.cache`. Subsequent `require()` calls return the cached object instantly without re-executing the file.

---

### Q10: Why is Node.js called "single-threaded" if it utilizes background threads?
**Answer:**
Node.js is called single-threaded because **JavaScript user code executes on a single main thread (the V8 Event Loop thread)**. 

However, Node.js is multi-threaded under the hood: `libuv` maintains a thread pool (4 worker threads by default) for handling heavy asynchronous tasks like disk I/O, DNS lookups, and crypto hashing. Network I/O operations are delegated asynchronously directly to the OS kernel sockets.

---

## 2) Streams & Buffer Management (Q11 – Q15)

### Q11: What are Streams in Node.js, and what are the 4 fundamental stream types?
**Answer:**
Streams are objects that allow reading data from a source or writing data to a destination **in continuous chunks** without loading the entire dataset into RAM memory.

**The 4 Stream Types:**
1. **Readable**: Data source you can read from (e.g., `fs.createReadStream()`, `http.IncomingMessage`).
2. **Writable**: Destination you can write to (e.g., `fs.createWriteStream()`, `http.ServerResponse`).
3. **Duplex**: Stream that is both Readable and Writable independently (e.g., TCP `net.Socket`).
4. **Transform**: A Duplex stream where the output is computed based on input modifications (e.g., `zlib.createGzip()`, `crypto.createCipheriv()`).

---

### Q12: What is "Backpressure" in Streams, and how do you handle it?
**Answer:**
Backpressure occurs when data is being **read/produced faster than the destination consumer can write/process it**. If unhandled, chunks accumulate in the Writable stream's internal memory buffer (`highWaterMark`), leading to high RAM usage and memory crashes.

**Handling Backpressure:**
- **Using `.pipe()`**: Automatically manages backpressure by pausing the Readable stream when `.write()` returns `false` and resuming when the Writable stream emits the `'drain'` event.
- **Using `pipeline()` from `node:stream/promises`**:
  ```js
  import { pipeline } from 'node:stream/promises';
  import fs from 'node:fs';
  import zlib from 'node:zlib';

  await pipeline(
    fs.createReadStream('input.txt'),
    zlib.createGzip(),
    fs.createWriteStream('input.txt.gz')
  );
  ```

---

### Q13: What is a Buffer in Node.js, and when should you use it?
**Answer:**
A `Buffer` is a global Node.js class used to handle raw binary data directly outside the V8 heap in allocated raw memory chunks. 

**Use Cases:**
- Reading binary files (images, audio, PDFs).
- Processing network TCP socket streams.
- Performing cryptography encryption/decryption operations.

```js
// Allocating a zero-filled buffer of 10 bytes
const buf = Buffer.alloc(10);

// Creating a buffer from a string
const bufStr = Buffer.from("Hello World", "utf-8");
console.log(bufStr); // <Buffer 48 65 6c 6c 6f 20 57 6f 72 6c 64>
```

---

### Q14: What causes Memory Leaks in Node.js, and how do you detect/fix them?
**Answer:**
**Common Causes:**
1. **Uncleaned Global Variables**: Variables attached to `global` or top-level module scope that are never garbage collected.
2. **Forgotten Event Listeners**: Adding `.on('event', fn)` listeners without invoking `.removeListener()` or `.off()`.
3. **Uncleared Timers**: `setInterval()` calls that remain running indefinitely.
4. **Closures**: Inner functions holding references to large outer scope variables long after they are needed.

**Detection & Tools:**
- Pass `--inspect` flag and inspect Heap Snapshots in Chrome DevTools.
- Use `process.memoryUsage()` to track `heapUsed` and `rss`.
- Diagnostic utilities like `heapdump` or `clinic doctor`.

---

### Q15: Why is `pipeline()` preferred over `.pipe()` for stream handling in modern Node.js?
**Answer:**
While `.pipe()` forwards data between streams, it **does not automatically forward error events** across stream chains. If an error occurs in the middle stream of a `readStream.pipe(transformStream).pipe(writeStream)` pipeline, memory leaks occur because the streams are not automatically closed or destroyed.

`pipeline()` (and `node:stream/promises` `pipeline`) properly destroys all streams in the pipeline if any stream emits an error or closes prematurely, guaranteeing cleanup.

---

## 3) Asynchronous Patterns & Event Emitter (Q16 – Q20)

### Q16: What is Callback Hell, and how do you resolve it in modern Node.js?
**Answer:**
Callback Hell (or the Pyramid of Doom) refers to deeply nested asynchronous callbacks that make code unreadable, fragile, and difficult to handle errors.

```js
// Callback Hell Anti-Pattern
getData(function(a) {
  getMoreData(a, function(b) {
    getMoreData2(b, function(c) {
      // Deep nesting...
    });
  });
});
```

**Resolving Solutions:**
1. **Promises**: Flatten nesting using `.then().catch()`.
2. **`async / await`**: Write asynchronous code that reads sequentially like synchronous code.
3. **`util.promisify()`**: Convert legacy callback functions into Promises.

---

### Q17: What is the `EventEmitter` class, and how do you use it?
**Answer:**
`EventEmitter` is a core module (`node:events`) that implements the Observer Pattern in Node.js. Many core modules (like `http.Server`, `fs.ReadStream`) inherit from `EventEmitter`.

```js
import { EventEmitter } from 'node:events';

class OrderProcessor extends EventEmitter {
  placeOrder(orderId) {
    console.log(`Order ${orderId} placed.`);
    this.emit('orderPlaced', { orderId, amount: 100 });
  }
}

const processor = new OrderProcessor();

// Listener
processor.on('orderPlaced', (data) => {
  console.log(`Sending email for order ${data.orderId}`);
});

processor.placeOrder(101);
```

---

### Q18: What is the Error-First Callback Pattern in Node.js?
**Answer:**
The Error-First Callback pattern is a standard convention in Node.js asynchronous APIs where the callback function accepts an **error object as its very first parameter**.

- If an error occurred, `err` will be an instance of `Error`, and remaining data parameters will be `undefined` or `null`.
- If no error occurred, `err` will be `null` or `undefined`, and data parameters will contain the successful payload.

```js
import fs from 'node:fs';

fs.readFile('file.txt', 'utf-8', (err, data) => {
  if (err) {
    console.error("Failed to read file:", err.message);
    return;
  }
  console.log("File content:", data);
});
```

---

### Q19: Explain `Promise.all()`, `Promise.allSettled()`, `Promise.race()`, and `Promise.any()`.
**Answer:**
- **`Promise.all([p1, p2])`**: Resolves when **ALL** promises resolve. Rejects immediately if **ANY** promise rejects.
- **`Promise.allSettled([p1, p2])`**: Waits for **ALL** promises to settle (either resolve or reject) and returns an array of status objects `{ status: 'fulfilled'|'rejected', value|reason }`.
- **`Promise.race([p1, p2])`**: Settles as soon as the **FIRST** promise settles (resolves OR rejects).
- **`Promise.any([p1, p2])`**: Resolves as soon as the **FIRST** promise fulfills (resolves). Rejects with an `AggregateError` only if **ALL** promises reject.

---

### Q20: How do you handle Uncaught Exceptions and Unhandled Rejections in Node.js?
**Answer:**
You can listen to global process events to catch unhandled errors:

```js
// Catch unhandled Promise rejections
process.on('unhandledRejection', (reason, promise) => {
  console.error('Unhandled Rejection at:', promise, 'reason:', reason);
  // Perform graceful logging
});

// Catch uncaught synchronous exceptions
process.on('uncaughtException', (err) => {
  console.error('Uncaught Exception thrown:', err);
  // Best practice: Log error and gracefully shut down process
  process.exit(1);
});
```
> **Best Practice**: `uncaughtException` indicates the process is in an unpredictable state. Always log the error, release resources, and allow PM2 or Kubernetes to restart the container.

---

## 4) Web Servers & Express.js Integration (Q21 – Q30)

### Q21: How do you build a basic HTTP web server in Node.js without third-party frameworks?
**Answer:**
Using the native `node:http` module:

```js
import http from 'node:http';

const server = http.createServer((req, res) => {
  if (req.method === 'GET' && req.url === '/api/health') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ status: 'OK', uptime: process.uptime() }));
  } else {
    res.writeHead(404, { 'Content-Type': 'text/plain' });
    res.end('Not Found');
  }
});

server.listen(3000, () => {
  console.log('Server running on port 3000');
});
```

---

### Q22: What is Middleware in Express.js, and what are the 5 types of middleware?
**Answer:**
Middleware functions are functions that have access to the Request object (`req`), Response object (`res`), and the `next` function in the application’s request-response cycle.

**The 5 Middleware Types:**
1. **Application-level**: Bound to `app` instance (`app.use()`).
2. **Router-level**: Bound to an instance of `express.Router()`.
3. **Error-handling**: Middleware with 4 parameters: `(err, req, res, next)`.
4. **Built-in**: Included with Express (`express.json()`, `express.static()`).
5. **Third-party**: Installed via npm (`cors`, `helmet`, `morgan`).

---

### Q23: How does Express Error-Handling Middleware work?
**Answer:**
Express recognizes error-handling middleware by checking if the function signature defines exactly **four parameters**: `(err, req, res, next)`.

```js
// Error-handling middleware (placed AFTER all route definitions)
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(err.statusCode || 500).json({
    success: false,
    message: err.message || 'Internal Server Error'
  });
});
```

---

### Q24: What is CORS, and how do you configure it in a Node.js API?
**Answer:**
CORS (Cross-Origin Resource Sharing) is a HTTP-header-based browser security mechanism that restricts a web browser from making HTTP requests to a domain different from the one that served the web page.

Using the `cors` package:
```js
import cors from 'cors';

const corsOptions = {
  origin: 'https://myapp.com', // Allow only specific origin
  methods: ['GET', 'POST', 'PUT', 'DELETE'],
  allowedHeaders: ['Content-Type', 'Authorization'],
  credentials: true
};

app.use(cors(corsOptions));
```

---

### Q25: How do JWT (JSON Web Tokens) work for Authentication in Node.js APIs?
**Answer:**
1. **Login**: Client sends credentials (`email`, `password`) to `/api/login`.
2. **Signing**: Server verifies credentials, creates a JWT containing a payload (e.g. `{ userId: 101, role: 'admin' }`), signs it with a secret key (`jwt.sign()`), and sends it back to the client.
3. **Client Storage**: Client stores token in Memory or HTTP-Only Cookies.
4. **Verification**: Client attaches token in `Authorization: Bearer <token>` header for subsequent requests. Server verifies signature using `jwt.verify()` in a middleware.

---

### Q26: What is the purpose of `express.json()` and `urlencoded()` middleware?
**Answer:**
- `express.json()`: Parses incoming HTTP requests with JSON payloads and populates `req.body`.
- `express.urlencoded({ extended: true })`: Parses incoming requests with URL-encoded payloads (from HTML form submits).

---

### Q27: How do you handle File Uploads in Express.js?
**Answer:**
Using the `multer` middleware, which processes `multipart/form-data`:

```js
import multer from 'multer';

const upload = multer({ 
  dest: 'uploads/',
  limits: { fileSize: 5 * 1024 * 1024 } // 5MB limit
});

app.post('/api/upload', upload.single('avatar'), (req, res) => {
  res.json({ file: req.file });
});
```

---

### Q28: What is Rate Limiting, and why is it crucial for Node.js APIs?
**Answer:**
Rate limiting restricts the number of requests a single IP address or user client can make within a specified timeframe. It prevents **Denial of Service (DoS)** attacks, brute-force login attempts, and API scraping.

```js
import rateLimit from 'express-rate-limit';

const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100, // Limit each IP to 100 requests per windowMs
  message: 'Too many requests from this IP, please try again later.'
});

app.use('/api/', limiter);
```

---

### Q29: What is Helmet.js, and how does it secure Express applications?
**Answer:**
`helmet` is a collection of 15 smaller middleware functions that set secure HTTP response headers to protect against common web vulnerabilities (XSS, Clickjacking, MIME sniffing):

```js
import helmet from 'helmet';
app.use(helmet());
```
Headers set include: `Content-Security-Policy`, `X-Frame-Options` (prevents clickjacking), `Strict-Transport-Security` (HSTS), and `X-Content-Type-Options`.

---

### Q30: How do you compress HTTP responses in Node.js?
**Answer:**
Using the `compression` middleware, which compresses HTTP response bodies using Gzip or Brotli before sending them to the browser, significantly reducing bandwidth and improving load times:

```js
import compression from 'compression';
app.use(compression());
```

---

## 5) Database Interaction & Data Persistence (Q31 – Q35)

### Q31: What is Connection Pooling, and why is it essential in Node.js database clients?
**Answer:**
Creating a new TCP database connection for every incoming HTTP request is expensive in terms of network latency and CPU handshake overhead. 

A **Connection Pool** maintains a cache of open database connections that can be reused across requests. When a query needs to execute, it borrows an available connection from the pool, executes the query, and releases the connection back to the pool.

---

### Q32: Compare Mongoose (ODM for MongoDB) vs. Prisma (ORM for SQL/NoSQL).
**Answer:**

| Feature | Mongoose | Prisma |
| :--- | :--- | :--- |
| **Type** | ODM (Object Data Modeling) for MongoDB | Modern ORM for PostgreSQL, MySQL, SQLite, MongoDB |
| **Schema Definition** | Defined in JS/TS schema files | Defined in a declarative `schema.prisma` file |
| **Type Safety** | Requires manual TypeScript interface definitions | Auto-generates fully type-safe client definitions |
| **Migrations** | Not built-in | Built-in database migration CLI (`prisma migrate`) |

---

### Q33: How do you handle Database Transactions in Node.js (PostgreSQL & MongoDB)?
**Answer:**
**PostgreSQL (using `pg` client):**
```js
const client = await pool.connect();
try {
  await client.query('BEGIN');
  await client.query('UPDATE accounts SET balance = balance - 100 WHERE id = 1');
  await client.query('UPDATE accounts SET balance = balance + 100 WHERE id = 2');
  await client.query('COMMIT');
} catch (err) {
  await client.query('ROLLBACK');
  throw err;
} finally {
  client.release();
}
```

---

### Q34: What are Mongoose Middleware Hooks (Pre and Post Hooks)?
**Answer:**
Mongoose hooks (also called middleware) are functions that execute control during the execution of asynchronous schema actions (such as `save`, `validate`, `remove`).

```js
import bcrypt from 'bcrypt';

userSchema.pre('save', async function(next) {
  if (!this.isModified('password')) return next();
  this.password = await bcrypt.hash(this.password, 10);
});
```

---

### Q35: How do Database Indexes improve Node.js query performance?
**Answer:**
Without an index, the database engine must perform a **Collection/Table Scan** (scanning every document/row from start to finish $O(N)$). 

An index builds a B-Tree data structure on specified fields, allowing the database to perform logarithmic lookups $O(\log N)$. In Node.js ODM/ORMs, indexing frequently searched fields (e.g. `email`, `createdAt`) dramatically reduces response latency.

---

## 6) Multiprocessing, Clustering & Scaling (Q36 – Q42)

### Q36: What is the Node.js `cluster` module, and how does it enable multi-core scaling?
**Answer:**
Because Node.js runs on a single CPU core, a single Node process cannot utilize multi-core server processors by default. 

The `node:cluster` module allows spawning a cluster of child worker processes that **all share the same server port**. The master process receives incoming TCP connections and distributes them across worker processes using a Round-Robin load balancing algorithm.

```js
import cluster from 'node:cluster';
import http from 'node:http';
import os from 'node:os';

const numCPUs = os.cpus().length;

if (cluster.isPrimary) {
  console.log(`Primary master process ${process.pid} is running`);
  // Fork workers
  for (let i = 0; i < numCPUs; i++) {
    cluster.fork();
  }
} else {
  // Workers share the TCP connection on port 8000
  http.createServer((req, res) => {
    res.writeHead(200);
    res.end(`Handled by worker ${process.pid}`);
  }).listen(8000);
}
```

---

### Q37: Compare Worker Threads vs. Child Processes vs. Cluster module.
**Answer:**

| Feature | `worker_threads` | `child_process` | `cluster` |
| :--- | :--- | :--- | :--- |
| **Memory** | **Shared memory** (`ArrayBuffer`, `SharedArrayBuffer`) | Separate OS processes & memory | Separate OS processes & memory |
| **IPC** | MessagePort / postMessage | Stdin/Stdout/Stderr / IPC channel | IPC channel / Shared Socket |
| **Best Used For** | CPU-intensive JavaScript tasks (image processing, crypto) | Running external shell scripts / commands | Scaling HTTP web servers across CPU cores |

---

### Q38: What are the 4 methods in `node:child_process` module?
**Answer:**
1. **`spawn(command, [args])`**: Streams data back in chunks via stdout/stderr (Ideal for long-running processes returning large data).
2. **`exec(command, callback)`**: Spawns a shell and buffers the output in memory (Ideal for small CLI commands).
3. **`execFile(file, [args], callback)`**: Executes an executable file directly without spawning a shell (Faster and safer than `exec`).
4. **`fork(modulePath)`**: Spawns a new Node.js V8 process with a built-in IPC communication channel.

---

### Q39: What is PM2, and what are its key capabilities?
**Answer:**
PM2 is a production process manager for Node.js applications. 

**Key Capabilities:**
- **Cluster Mode**: Automatically scales Node apps across all CPU cores without modifying code (`pm2 start app.js -i max`).
- **Auto-Restart**: Automatically restarts apps if they crash due to unhandled exceptions or out-of-memory errors.
- **Zero-Downtime Reload**: Reloads application instances sequentially without dropping client connections (`pm2 reload app`).
- **Log Management**: Consolidates stdout/stderr logs.

---

### Q40: What are Message Queues (BullMQ / RabbitMQ / Redis PubSub), and why use them?
**Answer:**
Message Queues decouple heavy background tasks (e.g. sending transactional emails, video encoding, generating PDF reports) from the main API HTTP request-response cycle. 

Instead of forcing a user to wait 10 seconds for an email to send, the API enqueues a job into a Redis-backed queue (e.g., BullMQ) and returns a `202 Accepted` response instantly. Separate worker processes process jobs asynchronously from the queue.

---

### Q41: How do WebSockets differ from HTTP requests in Node.js?
**Answer:**
- **HTTP**: Half-duplex, request-response model. Client initiates every request; server cannot push data to client unprompted.
- **WebSockets (`ws` / Socket.io)**: Full-duplex, persistent TCP connection. Allows bidirectional real-time communication (Server can push updates to clients instantly, ideal for chat apps, live stock tickers, multiplayer games).

---

### Q42: What is Graceful Shutdown, and how do you implement it in Node.js?
**Answer:**
Graceful Shutdown ensures that when a server receives a termination signal (`SIGTERM` from Kubernetes/Docker or `SIGINT` from Ctrl+C), it:
1. Stops accepting new incoming HTTP connections.
2. Waits for all active requests to complete.
3. Closes database connection pools and message queue consumers.
4. Exits the process cleanly.

```js
const server = app.listen(3000);

function gracefulShutdown(signal) {
  console.log(`Received ${signal}. Shutting down gracefully...`);
  server.close(async () => {
    console.log('HTTP server closed.');
    await pool.end(); // Close DB pool
    console.log('Database connections closed. Process exiting.');
    process.exit(0);
  });
}

process.on('SIGTERM', () => gracefulShutdown('SIGTERM'));
process.on('SIGINT', () => gracefulShutdown('SIGINT'));
```

---

## 7) Security, Testing & Performance Tuning (Q43 – Q50)

### Q43: How do you prevent SQL Injection and NoSQL Injection in Node.js?
**Answer:**
- **SQL Injection Prevention**: Never concatenate raw user strings into SQL queries. Always use **Parameterized Queries** (prepared statements) or ORMs.
  ```js
  // SAFE: Parameterized Query
  await pool.query('SELECT * FROM users WHERE email = $1', [userEmail]);
  ```
- **NoSQL Injection Prevention**: Sanitize user inputs to prevent MongoDB query operator injection (e.g. `{ "$gt": "" }`). Use libraries like `express-mongo-sanitize` or validate schemas with `zod`/`joi`.

---

### Q44: What is the difference between Unit Testing, Integration Testing, and E2E Testing?
**Answer:**
- **Unit Testing**: Tests individual functions or modules in isolation without external dependencies (mocking DB calls, HTTP requests). *Tools: Jest, Vitest*.
- **Integration Testing**: Tests how multiple modules interact together (e.g. testing an Express route endpoint with a real database). *Tools: Supertest + Jest*.
- **End-to-End (E2E) Testing**: Tests the complete application flow from frontend UI down to backend API and database. *Tools: Playwright, Cypress*.

---

### Q45: How do you test Express APIs using Jest and Supertest?
**Answer:**

```js
import request from 'supertest';
import app from '../app.js';

describe('GET /api/health', () => {
  it('should return 200 OK with status message', async () => {
    const res = await request(app).get('/api/health');
    expect(res.statusCode).toEqual(200);
    expect(res.body).toHaveProperty('status', 'OK');
  });
});
```

---

### Q46: Compare `package.json` vs. `package-lock.json`.
**Answer:**
- **`package.json`**: Defines high-level project metadata, scripts, and dependency version ranges using Semantic Versioning operators (`^1.2.0` allows minor updates, `~1.2.0` allows patch updates).
- **`package-lock.json`**: Automatically generated file that records the **exact resolved version tree and integrity hashes** of every installed package and sub-dependency. Ensures deterministic builds across different environments.

---

### Q47: What is the difference between `npm`, `npx`, `yarn`, and `pnpm`?
**Answer:**
- **`npm`**: Default Node Package Manager bundled with Node.js.
- **`npx`**: Package Executing Tool bundled with npm. Executes npm binaries directly without globally installing them (e.g. `npx create-react-app`).
- **`yarn`**: Alternative package manager developed by Facebook emphasizing speed and lockfile stability.
- **`pnpm`**: High-performance package manager that uses a content-addressable global store and hard links to save disk space and prevent duplicate package downloads.

---

### Q48: How do you handle CPU-intensive tasks without blocking the Event Loop?
**Answer:**
1. **Worker Threads (`node:worker_threads`)**: Move expensive computation (crypto, image manipulation, data compression) to a dedicated worker thread.
2. **Child Processes (`fork`)**: Delegate work to a separate Node process.
3. **Offloading to External Microservices / Job Queues**: Offload computation to dedicated Python/Go workers or serverless functions via BullMQ/RabbitMQ.

---

### Q49: How do you measure CPU and Memory usage in a running Node.js process?
**Answer:**
Using native `process` methods:

```js
// Memory Usage Snapshot
const memory = process.memoryUsage();
console.log({
  rss: `${Math.round(memory.rss / 1024 / 1024)} MB`,          // Resident Set Size
  heapTotal: `${Math.round(memory.heapTotal / 1024 / 1024)} MB`,
  heapUsed: `${Math.round(memory.heapUsed / 1024 / 1024)} MB`,
  external: `${Math.round(memory.external / 1024 / 1024)} MB`
});

// High-Resolution Execution Time Measurement
const start = process.hrtime.bigint();
// ... execute operation ...
const end = process.hrtime.bigint();
console.log(`Execution time: ${Number(end - start) / 1e6} ms`);
```

---

### Q50: Summary Checklist for Production Readiness of Node.js Applications.
**Answer:**
- [x] **Environment Security**: Store all secrets in `process.env` (never hardcode keys).
- [x] **HTTP Security Headers**: Enable `helmet`, CORS restrictions, and rate limiting.
- [x] **Process Management**: Use PM2 or Kubernetes containers for cluster scaling and auto-restarting.
- [x] **Error Handling**: Implement global `uncaughtException` and `unhandledRejection` handlers and standard error middleware.
- [x] **Logging**: Use structured JSON loggers like `pino` or `winston` (avoid raw `console.log`).
- [x] **Graceful Shutdown**: Handle `SIGTERM`/`SIGINT` to safely close database pools and HTTP servers.
- [x] **Health Checks**: Expose `/health` or `/liveness` endpoints for load balancer health probes.

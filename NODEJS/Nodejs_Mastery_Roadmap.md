# Node.js Mastery Roadmap (Complete 2026 Edition)

A structured, battle-tested, chapter-by-chapter roadmap to master **Node.js** from foundational runtime mechanics to Principal / Staff Backend System Architect level.

---

## Table of Contents
1. [How to Use This Roadmap](#how-to-use-this-roadmap)
2. [Mastery Definition & Senior Benchmarks](#mastery-definition--senior-benchmarks)
3. [Visual Roadmap Flowchart](#visual-roadmap-flowchart)
4. [Chapter 0: Foundations, Environment & Toolchain](#chapter-0-foundations-environment--toolchain)
5. [Chapter 1: Node.js Architecture & Runtime Internals (The Engine Room)](#chapter-1-nodejs-architecture--runtime-internals-the-engine-room)
6. [Chapter 2: Core Standard Modules & Node.js API Mastery](#chapter-2-core-standard-modules--nodejs-api-mastery)
7. [Chapter 3: Buffer & Stream Processing (High-Throughput I/O)](#chapter-3-buffer--stream-processing-high-throughput-io)
8. [Chapter 4: HTTP, Web Protocols & Network Programming](#chapter-4-http-web-protocols--network-programming)
9. [Chapter 5: Production Web Frameworks & API Architecture](#chapter-5-production-web-frameworks--api-architecture)
10. [Chapter 6: Database Persistence, ORMs & Data Access Layers](#chapter-6-database-persistence-orms--data-access-layers)
11. [Chapter 7: Authentication, Authorization & App Security (Hardening)](#chapter-7-authentication-authorization--app-security-hardening)
12. [Chapter 8: Concurrency, Multithreading & Multiprocessing](#chapter-8-concurrency-multithreading--multiprocessing)
13. [Chapter 9: Testing, Quality Assurance & CI/CD Pipelines](#chapter-9-testing-quality-assurance--cicd-pipelines)
14. [Chapter 10: Performance Profiling, Memory Diagnostics & Native Addons](#chapter-10-performance-profiling-memory-diagnostics--native-addons)
15. [Chapter 11: Enterprise Architecture, Microservices & Distributed Systems](#chapter-11-enterprise-architecture-microservices--distributed-systems)
16. [Chapter 12: Production DevOps, Containers & Cloud Native Node.js](#chapter-12-production-devops-containers--cloud-native-nodejs)
17. [Chapter 13: Production Capstone Projects Portfolio](#chapter-13-production-capstone-projects-portfolio)
18. [Chapter 14: 100-Point Senior Node.js Mastery Evaluation Checklist](#chapter-14-100-point-senior-nodejs-mastery-evaluation-checklist)

---

## How to Use This Roadmap

- **Target Schedule**: 12–15 hours/week over 20–28 weeks.
- **Code While Reading**: Never read runtime theory passively. Write standalone scratch files to inspect execution order, memory heaps, and event loop behaviors.
- **Maintain a Learning Lab**: Store all mini-projects, stream experiments, worker threads, and benchmarks in a dedicated GitHub repository (`nodejs-mastery-lab`).
- **Focus on Internals First**: Frameworks (Express, Nest, Fastify) change over time, but libuv, V8 memory layout, TCP sockets, and streams remain constant.

---

## Mastery Definition & Senior Benchmarks

You have achieved true mastery of Node.js when you can:

1. **Predict Event Loop Sequencing**: Explain the exact callback order across Timers, Pending, Poll, Check, Close, `process.nextTick()`, and Promise microtasks without running the code.
2. **Handle Massive Streams with Zero Memory Growth**: Process multi-gigabyte files, chunked HTTP uploads, and video streams while maintaining a flat memory footprint (< 50 MB).
3. **Debug & Fix Production Bottlenecks**: Profile CPU bottlenecks using Flamegraphs, diagnose memory leaks using V8 Heap Snapshots, and monitor event loop delays in real time.
4. **Scale Beyond a Single Thread**: Architect high-concurrency systems using Cluster mode, Worker Threads (`SharedArrayBuffer` / `Atomics`), and asynchronous messaging.
5. **Architect Resilient Backend Systems**: Implement production-ready microservices adhering to Clean Architecture, robust security hardening, distributed tracing, and graceful teardown.

---

## Visual Roadmap Flowchart

```
┌────────────────────────────────────────────────────────────────────────┐
│             Chapter 0: Modern JS/TS Toolchain & Environment            │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│             Chapter 1: Node.js Architecture & Runtime Internals        │
│          (V8 Engine, Libuv, Event Loop 6 Phases, Microtasks)           │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│             Chapter 2: Core Modules & Chapter 3: Streams/Buffers       │
│        (fs, events, crypto, Buffer, Duplex/Transform Streams)          │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│             Chapter 4: Network Protocols & Chapter 5: Web APIs         │
│          (HTTP/1.1, HTTP/2, WebSockets, TCP/UDP, Fastify, NestJS)      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│             Chapter 6: Databases & Chapter 7: Security Hardening       │
│          (PostgreSQL, Redis, Prisma, JWT/OAuth2, OWASP Defense)        │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│       Chapter 8: Multithreading/Clustering & Chapter 9: Testing        │
│        (Worker Threads, SharedArrayBuffer, Vitest, Testcontainers)     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│       Chapter 10: Performance Profiling & Chapter 11: System Design    │
│        (Heap Snapshots, Flamegraphs, Clean Architecture, Kafka/EDA)    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│          Chapter 12: Cloud Native DevOps & Chapter 13: Capstones       │
│        (Docker, Kubernetes, Observability, 5 Full-Scale Projects)      │
└────────────────────────────────────────────────────────────────────────┘
```

---

## Chapter 0: Foundations, Environment & Toolchain

### 0.1 Modern JavaScript & ECMAScript for Node.js
- **Execution & Scope**: Lexical scope, closures, function hoisting, `this` binding rules in CJS vs ESM.
- **Asynchronous Primitives**:
  - `Promise` states, chaining, and error propagation.
  - Concurrency combinators: `Promise.all()`, `Promise.allSettled()`, `Promise.race()`, `Promise.any()`.
  - `async` / `await` syntax, desugaring into generator functions.
  - Async Iterators & Generators (`Symbol.asyncIterator`, `for await (... of ...)`).
- **Binary & Memory Primitives**: `ArrayBuffer`, `TypedArray` (`Uint8Array`, `Int32Array`), `DataView`, `structuredClone()`.
- **Advanced Types & APIs**: `Symbol`, `BigInt`, `WeakMap`, `WeakSet`, `FinalizationRegistry`, `WeakRef`.

### 0.2 Node Version Management & Releases
- Node release schedule: Current vs LTS (Active LTS, Maintenance LTS, EOL).
- Version managers: `fnm` (Fast Node Manager in Rust), `nvm`, `volta`.
- Package managers: `npm` vs `pnpm` (content-addressable hard-links) vs `yarn` (Berry/PnP).
- `package.json` anatomy: `exports` field map, `imports` (subpath imports `#`), `type: "module"`, `engines`, `peerDependencies`, `overrides`/`resolutions`.

### 0.3 Node.js Module Systems (CJS vs ESM)
- **CommonJS (CJS)**:
  - `require()`, `module.exports`, `exports`.
  - Synchronous module resolution algorithm (`Module._load`).
  - `__dirname`, `__filename`, and circular dependency resolution.
- **ECMAScript Modules (ESM)**:
  - `import` / `export` syntax, top-level `await`.
  - Asynchronous static module record resolution and instantiation phase.
  - `import.meta.url`, `import.meta.resolve()`, dynamic `import()`.
  - Dual package hazard and writing CJS/ESM universal libraries (`tsup`, `unbuild`).

### 0.4 Development Tooling & Runtime Flags
- Built-in TypeScript support & modern execution: `node --watch`, `node --env-file`, `tsx`, `ts-node`, `tsc`.
- Modern linter & formatter: `Biome` / `ESLint v9` (flat config) + `Prettier`.
- VS Code & IDE Debugging: Launch configurations (`launch.json`), attach to process, source maps.
- POSIX Terminal & OS Signals: `SIGINT`, `SIGTERM`, `SIGHUP`, exit codes (`process.exitCode`), standard streams (`process.stdin`, `process.stdout`, `process.stderr`).

---

## Chapter 1: Node.js Architecture & Runtime Internals (The Engine Room)

### 1.1 The Anatomy of Node.js
```
┌──────────────────────────────────────────────────────────┐
│                   JavaScript Application                 │
├──────────────────────────────────────────────────────────┤
│             Node.js Standard Library (JS APIs)           │
├────────────────────────────┬─────────────────────────────┤
│   Node.js C++ Bindings     │       V8 Engine (JS/Wasm)   │
├────────────────────────────┴─────────────────────────────┤
│                          Libuv                           │
│   (Event Loop, Thread Pool, File I/O, Network Pollers)   │
├──────────────────────────────────────────────────────────┤
│      Core OS APIs: epoll (Linux), kqueue (Mac), IOCP     │
└──────────────────────────────────────────────────────────┘
```

- **V8 Engine**: Just-In-Time (JIT) compilation, parsing AST, Ignition interpreter, Sparkplug, TurboFan optimizing compiler.
- **V8 Memory Management**:
  - **New Space (Nursery + Intermediate)**: Short-lived objects, collected by Scavenger algorithm (semi-space copy, Cheneys algorithm).
  - **Old Pointer Space & Old Data Space**: Long-lived objects promoted via tenure; collected by Major GC (Mark-Sweep-Compact).
  - **Large Object Space**: Objects exceeding single-page size, never moved by GC.
  - **Code Space**: JIT-compiled machine code instructions.
- **Libuv Core Engine**:
  - Abstraction layer over platform-specific asynchronous I/O (`epoll` on Linux, `kqueue` on macOS/BSD, `IOCP` on Windows).
  - The internal Libuv Threadpool (Default 4 threads; configured via `UV_THREADPOOL_SIZE`).
  - Tasks executed on Threadpool: `fs.*` operations, `crypto.pbkdf2`/`scrypt`/`randomBytes`, `zlib.*`, `dns.lookup`.
  - Operations executed asynchronously without threadpool: Network sockets (`net`, `http`, `tls`, `dgram`), `dns.resolve*`.

### 1.2 The Event Loop: Detailed 6 Phases
The Event Loop executes in continuous ticks across 6 distinct phases in strict order:

```text
       ┌───────────────────────────┐
    ┌─>│          timers           │ ───> setTimeout(), setInterval()
    │  └─────────────┬─────────────┘
    │  ┌─────────────┴─────────────┐
    │  │     pending callbacks     │ ───> Deferred I/O callbacks (TCP errors, etc.)
    │  └─────────────┬─────────────┘
    │  ┌─────────────┴─────────────┐
    │  │    idle, prepare phase    │ ───> Node.js internal usage only
    │  └─────────────┬─────────────┘
    │  ┌─────────────┴─────────────┐
    │  │         poll phase        │ ───> Retrieve new I/O events, read/write sockets
    │  └─────────────┬─────────────┘
    │  ┌─────────────┴─────────────┐
    │  │        check phase        │ ───> setImmediate() callbacks
    │  └─────────────┬─────────────┘
    │  ┌─────────────┴─────────────┐
    └──│      close callbacks      │ ───> socket.on('close', ...)
       └───────────────────────────┘
```

### 1.3 Microtasks & Priority Queues
- **Microtask Queues (Executed immediately after current operation, before the next event loop phase)**:
  1. `process.nextTick()` Queue (Highest priority; fires before Promise microtasks).
  2. Promise Microtask Queue (`Promise.resolve().then()`, `queueMicrotask()`).
- Starving the event loop using recursive `process.nextTick()` vs `setImmediate()`.
- Difference between `setTimeout(fn, 0)` and `setImmediate(fn)` inside vs outside an I/O cycle.

---

## Chapter 2: Core Standard Modules & Node.js API Mastery

### 2.1 File System (`fs` and `fs/promises`)
- File descriptor management: `fs.open()`, `fs.read()`, `fs.write()`, `fs.close()`.
- Safe atomic file operations (writing to temporary file and renaming with `fs.rename()`).
- Directory traversal: `fs.readdir(dir, { withFileTypes: true, recursive: true })`.
- File monitoring: `fs.watch()` (OS native events) vs `fs.watchFile()` (polling stat) vs `chokidar`.
- File permissions, POSIX modes (`0o755`, `0o644`), `fs.access()`, `fs.constants`.

### 2.2 Path & URL Standards
- Cross-platform path arithmetic: `path.join()`, `path.resolve()`, `path.normalize()`, `path.parse()`, `path.relative()`, `path.sep`, `path.posix` vs `path.win32`.
- WHATWG URL API: `new URL()`, `URLSearchParams`, converting between file URLs and paths (`url.fileURLToPath()`, `url.pathToFileURL()`).

### 2.3 Event Emitter (`events`)
- Core API: `.on()`, `.once()`, `.emit()`, `.removeListener()`, `.prependListener()`.
- Memory leak detection: `setMaxListeners()`, default limit of 10, inspecting leaks with `.listenerCount()`.
- Error event convention: Why unhandled `'error'` events crash the process.
- Modern async integration: `events.once(emitter, 'event')`, `events.on(emitter, 'event')` async iterator.
- Building custom event-driven architectural components.

### 2.4 Process, OS & Diagnostics
- `process.env`, `process.argv`, `process.cwd()`, `process.pid`, `process.ppid`.
- Memory profiling APIs: `process.memoryUsage()` (`rss`, `heapTotal`, `heapUsed`, `external`, `arrayBuffers`).
- High-resolution timers: `process.hrtime.bigint()`.
- Process lifecycle handlers: `uncaughtException`, `uncaughtExceptionMonitor`, `unhandledRejection`, `warning`, `exit`.
- System metrics via `os` module: `os.cpus()`, `os.totalmem()`, `os.freemem()`, `os.loadavg()`, `os.networkInterfaces()`.

### 2.5 Cryptography & Security Utilities (`crypto`)
- Hashing & HMAC: `crypto.createHash('sha256')`, `crypto.createHmac('sha256', key)`.
- Password hashing: `crypto.scrypt()`, `crypto.pbkdf2()`, salt generation via `crypto.randomBytes()`.
- Symmetric encryption: AES-256-GCM authenticated encryption/decryption (`crypto.createCipheriv`, `crypto.createDecipheriv`).
- Asymmetric cryptography: RSA / ECC key pairs (`crypto.generateKeyPairSync`), public key encryption, digital signatures (`crypto.createSign`, `crypto.createVerify`).
- Timing attack prevention: `crypto.timingSafeEqual()`.

### 2.6 Modern Web APIs in Node.js
- Global Fetch API (`fetch`, `Request`, `Response`, `Headers`, `FormData`).
- Cancellation with `AbortController` and `AbortSignal` (`AbortSignal.timeout()`, `AbortSignal.any()`).
- High-resolution performance metrics (`performance.now()`, `PerformanceObserver`).
- Text encoding: `TextEncoder`, `TextDecoder`.
- Built-in WebSocket client and server capabilities.

---

## Chapter 3: Buffer & Stream Processing (High-Throughput I/O)

### 3.1 Buffer Mechanics & Binary Data
- How Buffers allocate raw binary memory outside the V8 heap in C++ memory.
- Allocation methods:
  - `Buffer.alloc(size)` (Zero-filled, safe).
  - `Buffer.allocUnsafe(size)` (Fast, uninitialized memory - risk of data leakage).
  - `Buffer.from(array | string | buffer)` (Encodings: `utf-8`, `hex`, `base64`, `ascii`, `latin1`).
- Buffer manipulation: Slicing (`buf.subarray()` vs `buf.slice()`), copying (`buf.copy()`), concatenation (`Buffer.concat()`).
- Byte order and Endianness (`readUInt32BE`, `readUInt32LE`, Float conversions).

### 3.2 Stream Architecture Deep Dive
```
             ┌─────────────────┐       ┌─────────────────┐
Source ────> │ Readable Stream │ ────> │ Writable Stream │ ────> Destination
             └─────────────────┘       └─────────────────┘
                      │                         ▲
                      ▼                         │
             ┌──────────────────────────────────┴─┐
             │ Transform / Duplex Stream (Filter) │
             └────────────────────────────────────┘
```

- **4 Core Stream Types**:
  1. `Readable`: Source of data (`fs.createReadStream`, `http.IncomingMessage`, `process.stdin`).
  2. `Writable`: Destination of data (`fs.createWriteStream`, `http.ServerResponse`, `process.stdout`).
  3. `Duplex`: Both readable and writable independently (`net.Socket`).
  4. `Transform`: Output is computed from input (`zlib.createGzip`, `crypto.createCipheriv`).

### 3.3 Flow Control, Backpressure & Pipeline
- **Operating Modes of Readable Streams**:
  - **Flowing Mode**: Data read continuously from underlying system, emitted via `'data'` events.
  - **Paused Mode**: Data must be explicitly fetched using `stream.read()`.
- **The Backpressure Problem**:
  - Producer writes faster than consumer can process.
  - `writable.write(chunk)` returns `false` when internal buffer reaches `highWaterMark` (default 16KB for objectMode, 64KB for byte streams).
  - Producer must pause and wait for the `'drain'` event before resuming.
- **`stream.pipeline()`**:
  - Why `readable.pipe(writable)` causes memory leaks on error.
  - Using `stream.pipeline()` / `stream/promises` for automatic cleanup, error forwarding, and resource destruction.
- **Async Iteration with Streams**: Consuming readable streams cleanly with `for await (const chunk of stream)`.

### 3.4 Custom Stream Implementations & Web Streams
- Implementing custom `Readable` (`_read(size)`), `Writable` (`_write(chunk, encoding, callback)`), and `Transform` (`_transform(chunk, encoding, callback)`).
- Interoperability between Node.js Streams and WHATWG Web Streams (`ReadableStream`, `WritableStream`, `TransformStream`, `Readable.toWeb()`, `Readable.fromWeb()`).

---

## Chapter 4: HTTP, Web Protocols & Network Programming

### 4.1 Low-Level TCP/IP & Socket Programming (`net`)
- Building raw TCP servers: `net.createServer((socket) => { ... })`.
- TCP socket events: `'connect'`, `'data'`, `'drain'`, `'error'`, `'end'`, `'close'`.
- Nagle's algorithm control: `socket.setNoDelay(true)`.
- Keep-Alive configurations: `socket.setKeepAlive(true, initialDelay)`.
- Unix Domain Sockets (Linux/macOS) and Named Pipes (Windows) for local ultra-fast IPC.

### 4.2 UDP / Datagram Sockets (`dgram`)
- UDP vs TCP trade-offs: Connectionless, packet-oriented, low latency, no guaranteed delivery.
- Creating UDP servers and clients (`dgram.createSocket('udp4')`).
- Multicasting and broadcasting packets across networks.

### 4.3 Raw HTTP & HTTPS Core Modules
- Anatomy of `http.createServer((req, res) => { ... })`:
  - `req` (`http.IncomingMessage`): A Readable stream containing headers, method, URL, and raw body chunks.
  - `res` (`http.ServerResponse`): A Writable stream for status codes, headers (`res.setHeader()`), and body streaming (`res.write()`, `res.end()`).
- Parsing raw multipart form data, chunked transfer encoding (`Transfer-Encoding: chunked`).
- HTTPS server configuration: TLS certificates, private keys, ALPN negotiation, mutual TLS (mTLS) client verification (`tls.createServer`).

### 4.4 HTTP/2 & HTTP/3
- HTTP/2 mechanics: Binary framing layer, multiplexing over single TCP connection, header compression with HPACK, server push.
- Building HTTP/2 servers using Node's `http2` module (`http2.createSecureServer`).
- Understanding QUIC / HTTP/3 over UDP.

### 4.5 WebSockets & Real-Time Communication
- The WebSocket handshake (HTTP 101 Switching Protocols).
- Building high-performance WebSocket servers using `ws`.
- Framing, binary data transmission, ping/pong heartbeat health checks.
- Real-time horizontal scaling: Synchronizing multiple Node instances via Redis Pub/Sub.

---

## Chapter 5: Production Web Frameworks & API Architecture

### 5.1 Web Framework Landscape & Selection
```
┌─────────────────┬──────────────────────────────────────────────────────────┐
│ Framework       │ Primary Strengths & Ideal Use Cases                      │
├─────────────────┼──────────────────────────────────────────────────────────┤
│ Express.js      │ Industry ubiquitous, vast middleware ecosystem, simple. │
│ Fastify         │ High throughput, schema compilation, plugin architecture.│
│ NestJS          │ Enterprise-grade, TypeScript-first, Angular-like DI/IoC. │
│ Hono            │ Ultra-lightweight, multi-runtime (Node, Bun, Edge/Deno). │
└─────────────────┴──────────────────────────────────────────────────────────┘
```

### 5.2 Deep Dive: Express.js Internals
- Middleware chain mechanics: `(req, res, next) => {}`.
- Error handling middleware signature: `(err, req, res, next) => {}`.
- Router routing table matching, parameter extraction (`req.params`, `req.query`).
- Common pitfalls: Unhandled promise rejections inside async route handlers (and Express 5 automatic promise catching).

### 5.3 Deep Dive: Fastify Architecture
- High-performance design: Schema-driven serialization with `fast-json-stringify`.
- Request validation with `ajv` (JSON Schema).
- Plugin encapsulation model (`fastify-plugin`, decorator pattern, hooks lifecycle: `onRequest`, `preParsing`, `preValidation`, `preHandler`, `preSerialization`, `onError`, `onResponse`).

### 5.4 Deep Dive: NestJS Enterprise Architecture
- Inversion of Control (IoC) & Dependency Injection (DI) container.
- Modules, Controllers, Providers, Services, Repositories.
- NestJS Lifecycle: Pipes (validation), Guards (authorization), Interceptors (aspect-oriented logging/caching), Filters (exception handling).
- Microservices transport layer (TCP, Redis, NATS, Kafka, RabbitMQ).

### 5.5 Modern API Paradigms
- **RESTful API Design**: Resource naming, HTTP verbs semantics, idempotency, HATEOAS, API versioning strategies (URI, query, Accept header).
- **GraphQL APIs**:
  - Schema-first vs Code-first (TypeGraphQL, Pothos).
  - Resolvers, mutations, subscriptions.
  - Solving the $N+1$ query problem using `DataLoader` batching and caching.
- **gRPC & Protocol Buffers**:
  - Defining `.proto` service contracts.
  - Implementing `@grpc/grpc-js` servers and clients.
  - Unary calls, client streaming, server streaming, bidirectional streaming.
- **OpenAPI / Swagger**: Auto-generating API specifications and interactive docs (`swagger-ui-express`, `@fastify/swagger`).

---

## Chapter 6: Database Persistence, ORMs & Data Access Layers

### 6.1 Relational Databases (PostgreSQL & MySQL)
- Connection Pool architecture: Min/max connections, idle timeout, connection lifecycle, avoiding pool starvation.
- Low-level drivers: `pg` (node-postgres), `mysql2`.
- Transaction management: `BEGIN`, `COMMIT`, `ROLLBACK`, Savepoints, ACID guarantees.
- Transaction isolation levels: `Read Uncommitted`, `Read Committed`, `Repeatable Read`, `Serializable`.
- Handling concurrency: Optimistic locking (version columns) vs Pessimistic locking (`SELECT ... FOR UPDATE`).

### 6.2 Modern ORMs & Query Builders
```
┌───────────────┬────────────────────────────────────────────────────────────┐
│ Tool          │ Key Characteristics & Architectural Approach               │
├───────────────┼────────────────────────────────────────────────────────────┤
│ Prisma        │ Schema-first DSL, type-safe generated client, Rust engine. │
│ Drizzle ORM   │ TypeScript-first, zero-overhead, SQL-like query builder.   │
│ Knex.js       │ Mature, flexible SQL query builder without ORM overhead.   │
│ TypeORM       │ Data Mapper & Active Record patterns, decorator-driven.    │
└───────────────┴────────────────────────────────────────────────────────────┘
```
- Schema migrations, seeding, rollback strategies, handling zero-downtime database migrations.
- Avoiding the $N+1$ query problem in ORMs (eager loading vs relation joins).

### 6.3 NoSQL & Document Databases (MongoDB)
- MongoDB Native Driver vs Mongoose ODM.
- Mongoose schemas, models, hooks (pre/post save), virtuals, and discriminators.
- Aggregation Pipeline stages: `$match`, `$group`, `$lookup`, `$unwind`, `$project`, `$facet`.
- Replica Sets, write concerns (`w: "majority"`), read preferences, and Change Streams.
- MongoDB Indexing: Compound indexes, TTL indexes, Partial indexes, Text search indexes.

### 6.4 In-Memory Caching & Distributed Data (Redis)
- Redis Data structures: Strings, Hashes, Lists, Sets, Sorted Sets (ZSET), HyperLogLogs, Bitmaps.
- Caching Patterns: Cache-Aside, Write-Through, Write-Behind, Refresh-Ahead.
- Combating Cache Issues:
  - **Cache Stampede / Thundering Herd**: Probabilistic early expiration (XFetch algorithm) / Distributed Mutex Locks.
  - **Cache Penetration**: Bloom filters, caching null values.
  - **Cache Avalanche**: Randomizing TTLs with jitter.
- Distributed Locks with Redis (Redlock algorithm).
- Job Queues & Background Workers: `BullMQ` (Redis-backed delayed jobs, retries, concurrency, parent-child flows).
- Redis Streams for append-only log streaming.

---

## Chapter 7: Authentication, Authorization & App Security (Hardening)

### 7.1 Authentication Architecture
- **Stateless Token Authentication (JWT)**:
  - Header, Payload, Signature mechanics.
  - Cryptographic algorithms: Symmetric HMAC (`HS256`) vs Asymmetric RSA/ECDSA (`RS256`, `ES256`).
  - Secure Token Lifecycle: Short-lived Access Tokens (5-15 min) + Long-lived Refresh Tokens (stored in DB/Redis with rotation & reuse detection).
- **Stateful Session Authentication**:
  - Session generation, storage in Redis/DB.
  - Secure Cookie flags: `HttpOnly` (mitigate XSS), `Secure` (HTTPS only), `SameSite: 'Strict' | 'Lax'`, `Signed` / Encrypted cookies.
- **OAuth 2.0 & OpenID Connect (OIDC)**:
  - Authorization Code Flow with PKCE (Proof Key for Code Exchange).
  - Social logins (Google, GitHub, Apple) using Passport.js / Arctic.
- **Password Hashing Standards**:
  - `Argon2id` (current industry gold standard), `bcrypt`, `scrypt`.

### 7.2 Authorization & Access Control Models
- **RBAC (Role-Based Access Control)**: User -> Roles -> Permissions.
- **ABAC (Attribute-Based Access Control)**: Evaluating subjects, objects, actions, and environmental attributes.
- Policy engines in Node.js: `@casl/ability`, `Oso`.

### 7.3 OWASP Top 10 Defenses for Node.js Applications
- **Injection Attacks**:
  - SQL Injection: Parameterized queries, prepared statements.
  - NoSQL Injection: Sanitizing `$where`, `$gt` operators from input payloads.
  - Command Injection: Avoiding `child_process.exec()` with user input; using `execFile()` with fixed argument arrays.
- **Cross-Site Scripting (XSS)**: Input sanitization (`DOMPurify`), Output encoding, Content Security Policy (`CSP`).
- **Cross-Site Request Forgery (CSRF)**: Anti-CSRF double-submit cookies, `SameSite` cookie enforcement.
- **Server-Side Request Forgery (SSRF)**: Whitelisting outbound hostnames, resolving and blocking private IP ranges (`127.0.0.1`, `10.0.0.0/8`, `169.254.169.254`).
- **Regular Expression Denial of Service (ReDoS)**: Detecting catastrophic backtracking with `safe-regex`, utilizing non-backtracking engines (Google RE2).
- **Prototype Pollution**:
  - Securing JSON parsing and recursive object merging (`lodash.merge` vulnerabilities).
  - Using `Object.create(null)`, `Map`, or `Object.freeze(Object.prototype)`.
- **Security Headers & Traffic Protection**:
  - `helmet` middleware suite (HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy).
  - CORS configuration (never reflect untrusted `Origin: *` with credentials).
  - Rate Limiting: Sliding window log / Token bucket algorithms using Redis (`rate-limiter-flexible`).
  - Dependency Vulnerability Audits: `npm audit`, Snyk, Dependabot.

---

## Chapter 8: Concurrency, Multithreading & Multiprocessing

### 8.1 Process Scaling & Multiprocessing (`child_process`)
- 4 Process Creation APIs:
  - `spawn()`: Streams I/O, best for high-volume data and long-running processes.
  - `exec()`: Buffers output in memory, executes inside a shell (vulnerable to injection).
  - `execFile()`: Invokes an executable directly without invoking a shell.
  - `fork()`: Spawns a new Node.js instance with an IPC communication channel.
- IPC Messaging: `process.send()`, `process.on('message')`, passing server handles across processes.
- Managing child process lifecycles, exit codes, and zombie processes.

### 8.2 The `cluster` Module
- Master/Primary process vs Worker processes model.
- How master binds to the port and distributes incoming connections to workers using OS-level Round-Robin (or OS accept).
- Zero-Downtime Reloads: Graceful rolling restart by terminating and spawning one worker at a time.
- Production Process Management with **PM2**:
  - Cluster mode configuration (`pm2.config.js`).
  - Automatic restart on failure, memory limits (`max_memory_restart`), log management.

### 8.3 Worker Threads (`worker_threads`)
```
Main Thread (Event Loop) ────────┬─────── SharedArrayBuffer / Atomics ────────┬─────── Worker Thread 1 (V8 + Libuv)
                                 │                                            │
                                 └─────── MessageChannel (Port1/Port2) ───────┴─────── Worker Thread 2 (V8 + Libuv)
```

- When to use Worker Threads: CPU-intensive operations (image resizing, video encoding, cryptography, machine learning inference, parsing huge JSON/CSV).
- Anatomy of Worker Threads:
  - Each worker has its own isolated V8 engine instance and Libuv Event Loop.
  - Data transfer via structured cloning over `MessagePort` (`parentPort.postMessage`).
  - Transferable Objects: `ArrayBuffer` transfer of ownership (zero-copy memory transfer).
- **Shared Memory & Synchronization**:
  - `SharedArrayBuffer` for true shared memory across threads.
  - `Atomics` operations (`Atomics.add`, `Atomics.load`, `Atomics.wait`, `Atomics.notify`) for thread-safe lock-free concurrency.
- Thread Pooling with `Piscina`: Managing reusable worker threads, queueing tasks, CPU core allocation.

---

## Chapter 9: Testing, Quality Assurance & CI/CD Pipelines

### 9.1 Testing Methodologies & The Test Pyramid
- **Unit Testing**: Testing isolated pure functions, domain models, business logic.
- **Integration Testing**: Testing API routes with live databases, Redis instances, and external services.
- **End-to-End (E2E) Testing**: Simulating full user journeys through the application stack.
- Test Doubles: Mocks, Stubs, Spies, Fakes (`sinon`, `vitest.fn()`).

### 9.2 Modern Test Frameworks
- **Node.js Native Test Runner (`node:test` & `node:assert`)**: Zero-dependency, native execution, mocking, subtests, coverage reporting (`node --test --experimental-test-coverage`).
- **Vitest**: Blazing fast ESM-first testing with Vite tooling, snapshot testing, concurrency.
- **Jest & Supertest**: End-to-end HTTP endpoint assertion testing.

### 9.3 Integration Testing with Testcontainers
- Running real, disposable database instances inside Docker containers dynamically during integration test runs (`@testcontainers/postgresql`, `@testcontainers/redis`).
- Eliminating mocked database inconsistencies and testing actual SQL transactions/constraints.
- Mocking external 3rd-party HTTP APIs using **MSW (Mock Service Worker)** or `nock`.

### 9.4 CI/CD & Automated Code Quality
- GitHub Actions CI pipeline: Matrix testing across Node.js LTS versions (Node 20, 22).
- Static analysis & linting gates: ESLint, TypeScript `tsc --noEmit`, Biome, Prettier check.
- Automated security scanning: `npm audit --audit-level=high`, CodeQL, Trivy container scanning.
- Automated semantic releases and changelog generation (`semantic-release`).

---

## Chapter 10: Performance Profiling, Memory Diagnostics & Native Addons

### 10.1 Profiling CPU Bottlenecks
- Identifying synchronous blocks that stall the event loop.
- Built-in V8 CPU Profiler:
  ```bash
  node --prof app.js
  node --prof-process isolate-0x...-v8.log > processed.txt
  ```
- Generating interactive Flamegraphs with **0x** and **Clinic.js Flame**:
  ```bash
  npx 0x app.js
  npx clinic flame -- node app.js
  ```
- Analyzing hot paths, deep call stacks, and costly regex/serialization routines.

### 10.2 Diagnosing Memory Leaks & Heap Management
- Common Node.js memory leak causes:
  1. Global variables and uncleaned caches.
  2. Unremoved event listeners (`EventEmitter.on` without `removeListener`).
  3. Closures retaining outer scope variables unintentionally.
  4. Detached timer references (`setInterval` never cleared).
- Capturing V8 Heap Snapshots:
  - Programmatic snapshots: `v8.writeHeapSnapshot()`.
  - Connecting Chrome DevTools via `node --inspect`.
  - Comparing snapshots in Chrome DevTools Memory panel (Retained Size vs Shallow Size, Distance from GC root, Retainers tree).
- Continuous diagnostics: Node.js Diagnostic Reports (`process.report.writeReport()`).

### 10.3 Event Loop Delay & Real-time Metrics
- Measuring event loop latency with `perf_hooks.monitorEventLoopDelay({ resolution: 10 })`.
- Calculating `p50`, `p90`, `p99`, and `max` event loop delays.
- Benchmarking HTTP throughput: `autocannon`, `wrk`, `k6`.

### 10.4 Native C++ Addons & Node-API (N-API)
- Why build Native Addons? (C/C++ performance, legacy library bindings, hardware interfaces).
- **Node-API (N-API)**: ABI-stable C interface guaranteeing binary compatibility across Node.js versions.
- Modern C++ bindings with `node-addon-api`.
- Modern Rust bindings for Node.js using **NAPI-RS** (blazing fast, memory-safe native extensions).

---

## Chapter 11: Enterprise Architecture, Microservices & Distributed Systems

### 11.1 Software Architecture Patterns in Node.js
```
┌──────────────────────────────────────────────────────────┐
│                   Domain Layer (Pure JS)                 │
│         Entities, Value Objects, Domain Events           │
├──────────────────────────────────────────────────────────┤
│                     Application Layer                    │
│            Use Cases, DTOs, Port Interfaces              │
├──────────────────────────────────────────────────────────┤
│                     Adapters Layer                       │
│    Controllers, Presenters, Gateways, Event Handlers     │
├──────────────────────────────────────────────────────────┤
│                  Infrastructure Layer                    │
│    Database (Prisma/Postgres), Redis, Express/Fastify    │
└──────────────────────────────────────────────────────────┘
```

- **Clean Architecture / Hexagonal Architecture (Ports & Adapters)**:
  - Decoupling core business domain rules from frameworks, databases, and network transports.
  - Dependency Inversion Principle: High-level modules depend on abstractions (interfaces/ports), not concretions.
- **Domain-Driven Design (DDD)**:
  - Ubiquitous Language, Bounded Contexts.
  - Entities, Value Objects, Aggregates, Domain Repositories, Domain Events.
- **Modular Monolith vs Microservices**: When to break the monolith; identifying bounded context seams.

### 11.2 Asynchronous Event-Driven Architecture (EDA)
- Message Brokers & Event Streaming:
  - **RabbitMQ**: AMQP protocol, exchanges (direct, topic, fanout, headers), queues, routing keys, consumer acknowledgements.
  - **Apache Kafka**: Topics, partitions, consumer groups, offset management, replayability.
  - **Redis Streams / BullMQ**: Lightweight messaging for mid-tier workloads.
- **Distributed Reliability Patterns**:
  - **Transactional Outbox Pattern**: Writing domain changes and events to the database within the same ACID transaction to prevent dual-write bugs.
  - **Saga Pattern**: Managing distributed multi-service transactions via Choreography (event-driven) or Orchestration (centralized orchestrator).
  - **Idempotency & Deduplication**: Ensuring consumers handle duplicate message deliveries safely (Idempotency Keys).
  - **Dead Letter Queues (DLQ)** & Exponential Backoff Retries.

### 11.3 Observability, Telemetry & Distributed Tracing
- **Structured Logging**: JSON logging with high performance using `pino` or `winston`.
- **Metrics Collection**: Exposing Prometheus metrics (`prom-client`), monitoring memory, CPU, HTTP request duration histograms, active connections.
- **Distributed Tracing**: Instrumenting code with **OpenTelemetry (OTel)**, correlating traces and spans across microservices with Jaeger / Zipkin.
- **Health Checks & Graceful Teardown**:
  - Health check endpoints (`/health/live`, `/health/ready`).
  - Handling `SIGTERM` / `SIGINT`: Stop accepting new connections, finish pending HTTP requests, flush queues, close database pools, exit with `0`.

---

## Chapter 12: Production DevOps, Containers & Cloud Native Node.js

### 12.1 Production Dockerization for Node.js
- Multi-stage Docker build pipeline for minimal image size and attack surface:
  ```dockerfile
  # Stage 1: Build & Dependencies
  FROM node:22-alpine AS builder
  WORKDIR /app
  COPY package.json pnpm-lock.yaml ./
  RUN npm install -g pnpm && pnpm install --frozen-lockfile
  COPY . .
  RUN pnpm build && pnpm prune --prod

  # Stage 2: Production Minimal Runtime
  FROM node:22-alpine AS runner
  WORKDIR /app
  ENV NODE_ENV=production
  # Security: Run as non-root user
  USER node
  COPY --chown=node:node --from=builder /app/node_modules ./node_modules
  COPY --chown=node:node --from=builder /app/dist ./dist
  COPY --chown=node:node --from=builder /app/package.json ./package.json

  EXPOSE 3000
  CMD ["node", "dist/index.js"]
  ```
- Running as a non-root user (`USER node`).
- Using `dumb-init` or `tini` to properly handle PID 1 signal forwarding and reaping zombie processes.
- Docker layer caching optimization.

### 12.2 Kubernetes & Orchestration
- Kubernetes manifests: `Deployment`, `Service`, `Ingress`, `ConfigMap`, `Secret`.
- Horizontal Pod Autoscaler (HPA) based on CPU, memory, and custom Prometheus metrics.
- Configuring Liveness, Readiness, and Startup probes.
- Graceful termination: Aligning Kubernetes `terminationGracePeriodSeconds` with Node.js shutdown timers.

### 12.3 Serverless & Edge Node.js
- AWS Lambda architecture with Node.js: Cold starts, execution environments, layer sharing.
- Managing database connections in serverless (avoiding connection exhaustion via AWS RDS Proxy, Prisma Accelerate).
- Edge computing runtimes: Cloudflare Workers, Vercel Edge Runtime (Node.js compatibility mode).

---

## Chapter 13: Production Capstone Projects Portfolio

To solidify complete mastery, build these 5 production-grade portfolio capstone systems from scratch:

### 1. High-Concurrency Real-Time Collaboration & Chat Engine
- **Core Tech**: Node.js, Fastify, `ws` (WebSockets), Redis Streams, PostgreSQL, TypeScript.
- **Key Features**:
  - Multi-room real-time messaging with typing indicators, presence, and read receipts.
  - Horizontally scaled across 4 Node instances synchronized via Redis Pub/Sub.
  - Heartbeat ping/pong connection tracking, automatic reconnection, missed message replay from Redis Stream buffer.
  - JWT authentication during WebSocket handshake with token refresh.

### 2. Distributed Media Transcoding & File Processing Pipeline
- **Core Tech**: Node.js Streams, Worker Threads (`piscina`), S3 / MinIO, BullMQ, FFmpeg, Docker.
- **Key Features**:
  - Multipart chunked video upload directly streamed to S3 storage.
  - Background transcoding job queue resizing videos to multiple resolutions (1080p, 720p, 480p) using child processes and Worker Threads.
  - Webhook notifications on completion with exponential backoff retries.
  - Memory consumption kept under 60MB regardless of video file size (10GB+).

### 3. Multi-Tenant Enterprise SaaS Backend (Clean Architecture)
- **Core Tech**: NestJS / Fastify, PostgreSQL, Drizzle ORM / Prisma, Redis, Docker, OpenTelemetry.
- **Key Features**:
  - Multi-tenant data isolation (tenant ID row-level security or separate schemas).
  - Full Clean Architecture (Domain Entities, Use Cases, Repositories, Inversion of Control).
  - RBAC + ABAC granular permission engine with CASL.
  - Stripe subscription webhooks with transactional outbox event publishing.
  - Comprehensive unit, integration (Testcontainers), and E2E test suites with 90%+ coverage.

### 4. High-Throughput Custom API Gateway & Reverse Proxy
- **Core Tech**: Raw Node.js `http`/`http2`, Streams, `net`, `rate-limiter-flexible`, Redis.
- **Key Features**:
  - Dynamic reverse proxying and request streaming without buffering payloads into memory.
  - Distributed token bucket rate-limiting by IP / API key.
  - JWT verification and header enrichment before routing downstream.
  - Circuit Breaker pattern with fallback responses when downstream services fail.
  - Live Prometheus metrics and OpenTelemetry trace forwarding.

### 5. Resilient FinTech Payment Gateway & Ledger Service
- **Core Tech**: Node.js, PostgreSQL, Apache Kafka / RabbitMQ, TypeScript, Vitest.
- **Key Features**:
  - Double-entry bookkeeping ledger ensuring total debits equal total credits under ACID transactions.
  - Strictly idempotent payment processing using unique Idempotency Keys.
  - Distributed Saga Orchestration for handling multi-step cross-bank fund transfers.
  - Dead Letter Queue (DLQ) automated replay mechanisms and audit logging.

---

## Chapter 14: 100-Point Senior Node.js Mastery Evaluation Checklist

Track your progress across all 14 core competencies:

### Runtime & Architecture (10 Points)
- [ ] 1. Explain the roles of V8, Libuv, C++ Bindings, and OS syscalls.
- [ ] 2. Diagram and explain the 6 phases of the Libuv Event Loop from memory.
- [ ] 3. Articulate the exact execution priority between `process.nextTick()`, Promise microtasks, `setImmediate()`, and `setTimeout()`.
- [ ] 4. Explain which operations run on the Libuv threadpool vs OS kernel asynchronous pollers.
- [ ] 5. Explain how `UV_THREADPOOL_SIZE` impacts throughput for crypto and filesystem operations.
- [ ] 6. Explain V8 Heap layout: New Space (Nursery/Intermediate), Old Space, Large Object Space, Code Space.
- [ ] 7. Explain Scavenger GC (semi-space copy) vs Major GC (Mark-Sweep-Compact).
- [ ] 8. Explain how the V8 JIT compiler optimizes code and what triggers de-optimization.
- [ ] 9. Understand Top-Level Await mechanics and its impact on the module dependency graph.
- [ ] 10. Explain CommonJS synchronous loading vs ESM asynchronous static graph resolution.

### Buffers & Streams (10 Points)
- [ ] 11. Explain how Buffers allocate memory outside the V8 heap in C++ memory.
- [ ] 12. Differentiate `Buffer.alloc()` vs `Buffer.allocUnsafe()` and explain the security implications.
- [ ] 13. Master the 4 stream types: Readable, Writable, Duplex, Transform.
- [ ] 14. Explain Backpressure, the `highWaterMark` threshold, and handling the `'drain'` event.
- [ ] 15. Differentiate Readable stream paused mode (`.read()`) vs flowing mode (`'data'`).
- [ ] 16. Implement a custom Transform stream that parses and transforms data on the fly.
- [ ] 17. Safely chain multiple streams with `stream.pipeline()` and `stream/promises`.
- [ ] 18. Consume readable streams using async iteration (`for await (... of ...)`).
- [ ] 19. Convert between Node.js Streams and WHATWG Web Streams (`toWeb`, `fromWeb`).
- [ ] 20. Stream a 5GB+ file over HTTP with constant memory footprint under 50MB.

### Networking & Web Protocols (8 Points)
- [ ] 21. Build a raw TCP server using the `net` module and handle socket lifecycles.
- [ ] 22. Build a UDP datagram server using `dgram`.
- [ ] 23. Implement raw HTTP parsing using `http.createServer` without external libraries.
- [ ] 24. Configure an HTTPS server with TLS certificates, ALPN, and ciphers.
- [ ] 25. Explain the WebSocket upgrade handshake and framing protocol.
- [ ] 26. Scale WebSocket connections horizontally using Redis Pub/Sub.
- [ ] 27. Build an HTTP/2 server utilizing multiplexed streams with `http2`.
- [ ] 28. Implement gRPC unary and streaming services with Protocol Buffers.

### Web Frameworks & APIs (8 Points)
- [ ] 29. Master Express middleware mechanics and asynchronous error propagation.
- [ ] 30. Implement Fastify plugins with encapsulation and lifecycle hooks.
- [ ] 31. Build enterprise applications with NestJS using Dependency Injection, Guards, and Interceptors.
- [ ] 32. Build REST APIs adhering to REST constraints, status codes, and idempotency.
- [ ] 33. Build GraphQL servers with resolvers, mutations, and subscriptions.
- [ ] 34. Solve the GraphQL $N+1$ query problem using `DataLoader`.
- [ ] 35. Implement compile-time schema validation with Zod / TypeBox / Ajv.
- [ ] 36. Auto-generate OpenAPI / Swagger specifications from code.

### Databases & Persistence (8 Points)
- [ ] 37. Manage PostgreSQL connection pools and prevent connection starvation.
- [ ] 38. Implement ACID transactions with rollback savepoints in SQL drivers.
- [ ] 39. Understand SQL isolation levels and solve dirty reads, non-repeatable reads, phantom reads.
- [ ] 40. Master Prisma or Drizzle ORM for type-safe queries and zero-downtime migrations.
- [ ] 41. Design MongoDB aggregation pipelines and multi-document ACID transactions.
- [ ] 42. Implement Redis caching strategies (Cache-Aside, Write-Through).
- [ ] 43. Prevent Cache Stampede, Cache Penetration, and Cache Avalanche.
- [ ] 44. Implement distributed locks using Redis (Redlock) and background jobs with BullMQ.

### Security & Hardening (10 Points)
- [ ] 45. Implement stateless JWT auth with access & refresh token rotation and reuse detection.
- [ ] 46. Implement stateful session auth using encrypted `HttpOnly`, `SameSite` cookies and Redis.
- [ ] 47. Implement OAuth 2.0 Authorization Code Flow with PKCE.
- [ ] 48. Hash passwords securely using Argon2id or Scrypt.
- [ ] 49. Prevent SQL, NoSQL, and Command Injection across all data inputs.
- [ ] 50. Prevent ReDoS by detecting catastrophic backtracking and using safe regex.
- [ ] 51. Mitigate Prototype Pollution vulnerabilities in object merging and parsing.
- [ ] 52. Implement SSRF defenses by filtering internal and private IP ranges.
- [ ] 53. Harden HTTP headers using `helmet` and configure secure CORS policies.
- [ ] 54. Implement distributed sliding-window rate limiting.

### Concurrency & Multithreading (8 Points)
- [ ] 55. Use `child_process.spawn()` vs `exec()` vs `execFile()` vs `fork()`.
- [ ] 56. Establish IPC channels between parent and child processes.
- [ ] 57. Scale HTTP servers across all CPU cores using the `cluster` module.
- [ ] 58. Perform zero-downtime rolling reloads in cluster mode.
- [ ] 59. Offload CPU-heavy tasks to `worker_threads`.
- [ ] 60. Share zero-copy memory across Worker Threads using `SharedArrayBuffer`.
- [ ] 61. Coordinate thread synchronization using `Atomics` (`wait`, `notify`, `add`).
- [ ] 62. Manage worker thread pools efficiently using `Piscina`.

### Testing & QA (8 Points)
- [ ] 63. Write unit tests using the Node.js native test runner (`node:test`, `node:assert`).
- [ ] 64. Write unit and integration test suites using Vitest.
- [ ] 65. Test HTTP endpoints and middleware using Supertest.
- [ ] 66. Run disposable PostgreSQL and Redis instances in integration tests with Testcontainers.
- [ ] 67. Mock external network requests with MSW (Mock Service Worker) and Nock.
- [ ] 68. Measure and enforce code coverage standards.
- [ ] 69. Build automated CI pipelines with GitHub Actions.
- [ ] 70. Enforce static typing and linting with TypeScript strict mode and Biome.

### Profiling & Performance Diagnostics (8 Points)
- [ ] 71. Generate and interpret V8 CPU profiles (`--prof`, `--prof-process`).
- [ ] 72. Generate and analyze Flamegraphs using 0x or Clinic.js Flame to spot hot paths.
- [ ] 73. Capture and inspect V8 Heap Snapshots in Chrome DevTools to locate memory leaks.
- [ ] 74. Differentiate Shallow Size vs Retained Size and trace GC Retainer trees.
- [ ] 75. Monitor Event Loop Delay using `perf_hooks.monitorEventLoopDelay()`.
- [ ] 76. Generate and inspect Node.js Diagnostic Reports on failure.
- [ ] 77. Benchmark API throughput using `autocannon` or `k6`.
- [ ] 78. Build a native C++ addon (Node-API) or Rust extension (NAPI-RS).

### System Architecture & Microservices (8 Points)
- [ ] 79. Implement Clean Architecture / Hexagonal Architecture with decoupled domain layers.
- [ ] 80. Apply Domain-Driven Design (DDD) principles with Entities, Aggregates, Repositories.
- [ ] 81. Build asynchronous event-driven services using RabbitMQ (AMQP) or Apache Kafka.
- [ ] 82. Implement the Transactional Outbox Pattern to guarantee reliable event publishing.
- [ ] 83. Implement the Saga Pattern (Orchestration or Choreography) for distributed transactions.
- [ ] 84. Implement Dead Letter Queues (DLQ) and exponential backoff retry strategies.
- [ ] 85. Instrument distributed tracing across microservices using OpenTelemetry.
- [ ] 86. Collect structured JSON logs with Pino and aggregate with ELK / Grafana Loki.

### DevOps & Cloud Native (6 Points)
- [ ] 87. Write multi-stage, non-root production Dockerfiles for Node.js apps.
- [ ] 88. Use `dumb-init` / `tini` as PID 1 to handle signal forwarding and prevent zombie processes.
- [ ] 89. Deploy Node.js workloads to Kubernetes with Deployments, Services, and HPA.
- [ ] 90. Implement production Liveness, Readiness, and Startup probes.
- [ ] 91. Implement graceful shutdown handlers (`SIGTERM`/`SIGINT`) with connection draining.
- [ ] 92. Deploy serverless Node.js functions and manage database connection pooling.

### Senior Portfolio & Mastery Capstones (8 Points)
- [ ] 93. Build a real-time collaboration & chat engine with WebSockets, Redis Streams & Clustering.
- [ ] 94. Build a distributed media transcoding pipeline with Streams, Worker Threads & BullMQ.
- [ ] 95. Build a multi-tenant enterprise SaaS backend with Clean Architecture & RBAC.
- [ ] 96. Build a high-throughput custom API Gateway & Reverse Proxy with rate-limiting & caching.
- [ ] 97. Build a resilient FinTech ledger service with Kafka, Outbox Pattern & Idempotency.
- [ ] 98. Write technical architecture RFCs and conduct rigorous code reviews.
- [ ] 99. Contribute to open-source Node.js ecosystem packages or core modules.
- [ ] 100. Confidently architect, deploy, monitor, and troubleshoot multi-million request/day Node.js systems in production.

# 2) Stream Architecture and The Four Core Stream Types

## Executive Overview
In traditional data processing, an entire file or network response is loaded completely into memory (`fs.readFile`) before any processing or transmission begins. When handling large files (e.g. 5GB video) or high concurrency (10,000 simultaneous connections), buffering entire payloads into RAM leads to immediate process memory exhaustion and V8 Out-Of-Memory (OOM) crashes.

**Streams** solve this by breaking continuous data into small, manageable chunks (typically 16KB to 64KB) and processing them sequentially over time with a flat, constant memory footprint.

---

## 1. Buffering vs Streaming: Memory Profile

```text
Buffering Model (fs.readFile):
Memory Spike = Size of File * Active Requests
[5GB File] ──────> Loads Entire 5GB into RAM ──────> OOM Crash / Heavy GC

Streaming Model (fs.createReadStream):
Memory Footprint = ~64KB per Request (Constant)
[5GB File] ──[64KB Chunk]──> [Process] ──[64KB Chunk]──> Flat ~30MB Total Process RAM!
```

---

## 2. The 4 Fundamental Stream Types

All streams in Node.js inherit from `EventEmitter` (`node:events`) and are instances of one of four foundational abstract classes in `node:stream`:

```text
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     Readable    │       │     Duplex      │       │     Writable    │
│  (Data Source)  │       │  (Two-Way Comm) │       │   (Data Sink)   │
│                 │       │                 │       │                 │
│  - fs.ReadStream│       │  - net.Socket   │       │ - fs.WriteStream│
│  - IncomingMsg  │       │  - TLS Socket   │       │ - ServerResponse│
│  - process.stdin│       │  - WebSocket    │       │ - process.stdout│
└────────┬────────┘       └─────────────────┘       └────────▲────────┘
         │                                                   │
         │                ┌─────────────────┐                │
         └───────────────>│    Transform    ├────────────────┘
                          │(Filter/Convert) │
                          │                 │
                          │  - zlib.Gzip    │
                          │  - crypto.Cipher│
                          └─────────────────┘
```

### 1. `Readable` Stream (Data Producer)
An abstraction for a source from which data can be consumed.
- **Examples**: `fs.createReadStream()`, `http.IncomingMessage` (`req`), `process.stdin`, `crypto.randomBytes()`.
- **Primary Events**: `'data'`, `'readable'`, `'end'`, `'error'`, `'close'`.

### 2. `Writable` Stream (Data Consumer / Sink)
An abstraction for a destination to which data can be written.
- **Examples**: `fs.createWriteStream()`, `http.ServerResponse` (`res`), `process.stdout`, `net.Socket`.
- **Primary Events**: `'drain'`, `'finish'`, `'error'`, `'close'`, `'pipe'`.

### 3. `Duplex` Stream (Bidirectional Channel)
An abstraction for an entity that is **both Readable and Writable independently**.
- **Examples**: `net.Socket` (can send TCP packets while receiving TCP packets on separate internal buffers), Unix domain sockets.
- Reading and writing operate on two completely isolated internal queues.

### 4. `Transform` Stream (Throughput Filter / Mutator)
A specialized subclass of `Duplex` where the **Writable input is mathematically or computationally transformed into the Readable output**.
- **Examples**: `zlib.createGzip()` (raw bytes in -> compressed bytes out), `crypto.createCipheriv()` (plaintext in -> ciphertext out).

---

## 3. The `highWaterMark` & Internal Buffer Queues

Both `Readable` and `Writable` streams maintain an internal FIFO queue (implemented in `node:stream` as a linked list of `Buffer` chunks).

The **`highWaterMark`** option specifies the maximum number of bytes (or objects) that the internal queue can hold before the stream stops requesting more data from the underlying source:

- **Default for Byte Streams**: `64 * 1024` bytes (**64 KB**).
- **Default for `objectMode: true` Streams**: `16` objects.

```text
Readable Internal Queue:
[ Chunk 1 (64KB) ] ── [ Chunk 2 (64KB) ] ── (Reached highWaterMark) -> Pauses OS Read Poller!

Writable Internal Queue:
[ Chunk 1 ] ── [ Chunk 2 ] ── [ Chunk 3 ] ──> (Queue >= highWaterMark) -> writable.write() returns FALSE!
```

---

## 4. Complete Stream Lifecycle Events

```typescript
import fs from 'node:fs';

const readable = fs.createReadStream('large_dataset.bin', { highWaterMark: 64 * 1024 });
const writable = fs.createWriteStream('output.bin');

// READABLE LIFECYCLE EVENTS
readable.on('open', (fd) => {
  console.log(`[READABLE] OS File Descriptor ${fd} opened.`);
});

readable.on('data', (chunk: Buffer) => {
  console.log(`[READABLE] Received chunk of ${chunk.length} bytes.`);
});

readable.on('end', () => {
  console.log('[READABLE] Stream reached EOF (End of File). No more data to emit.');
});

readable.on('error', (err) => {
  console.error('[READABLE] Stream encountered an error:', err.message);
});

// WRITABLE LIFECYCLE EVENTS
writable.on('drain', () => {
  console.log('[WRITABLE] Internal buffer emptied below highWaterMark. Safe to resume writing.');
});

writable.on('finish', () => {
  console.log('[WRITABLE] All data written and flushed to destination (after writable.end()).');
});

writable.on('close', () => {
  console.log('[WRITABLE] Underlying file descriptor closed.');
});
```

---

## 5. Modern Object-Mode Streams

By default, streams only accept `Buffer`, `Uint8Array`, or `string` chunks. By setting **`objectMode: true`**, streams can pass arbitrary JavaScript objects directly through the pipeline without JSON stringification overhead:

```typescript
import { Transform } from 'node:stream';

interface UserRecord {
  id: string;
  email: string;
  points: number;
}

// Transform stream accepting UserRecord objects and emitting enriched UserRecord objects
export class UserEnricherStream extends Transform {
  constructor() {
    super({ objectMode: true }); // Enable JS Object chunks
  }

  override _transform(user: UserRecord, encoding: string, callback: () => void) {
    // Add computed VIP status
    const enriched = {
      ...user,
      isVip: user.points > 1000,
      processedAt: new Date().toISOString()
    };

    this.push(enriched);
    callback();
  }
}
```

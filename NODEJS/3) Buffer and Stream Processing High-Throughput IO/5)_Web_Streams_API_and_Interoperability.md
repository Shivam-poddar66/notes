# 5) Web Streams API and Interoperability

## Executive Overview
With the rise of edge computing runtimes (Cloudflare Workers, Deno, Vercel Edge) and modern web standards, the **WHATWG Web Streams Standard** (`ReadableStream`, `WritableStream`, `TransformStream`) has become a universal primitive across both frontend browsers and modern Node.js runtimes.

Node.js standard library (`node:stream/web`) provides full WHATWG Web Streams compliance, along with high-performance bidirectional bridge methods to seamlessly convert between classic Node.js streams and Web Streams.

---

## 1. Node.js Classic Streams vs WHATWG Web Streams

| Feature | Node.js Classic Streams (`node:stream`) | WHATWG Web Streams (`node:stream/web`) |
|---|---|---|
| **Core Architecture** | `EventEmitter` based (`.on('data')`, `.on('drain')`) | Promise / Reader-Writer lock based (`reader.read()`) |
| **Cross-Platform Compatibility** | Node.js specific | Universal (Browsers, Deno, Cloudflare Workers, Bun) |
| **Flow Control** | `highWaterMark` with `'drain'` event callback loop | Internal queuing strategy with asynchronous pull controllers |
| **Pipelines** | `stream.pipeline()` / `readable.pipe()` | `readableStream.pipeThrough()`, `pipeTo()` |
| **Locking Mechanism** | Multiple listeners can attach to one stream | Strict locking via `getReader()` (Exclusive locked reader) |

---

## 2. Converting Between Node.js & Web Streams

Node.js provides built-in adapter functions in `node:stream`:

```typescript
import { Readable, Writable } from 'node:stream';
import fs from 'node:fs';

// 1. Convert Node.js Readable -> WHATWG ReadableStream
const nodeReadStream = fs.createReadStream('source.tar.gz');
const webReadableStream: ReadableStream<Uint8Array> = Readable.toWeb(nodeReadStream);

// 2. Convert WHATWG ReadableStream -> Node.js Readable
const nodeConvertedBack: Readable = Readable.fromWeb(webReadableStream);

// 3. Convert Node.js Writable -> WHATWG WritableStream
const nodeWriteStream = fs.createWriteStream('dest.tar.gz');
const webWritableStream: WritableStream<Uint8Array> = Writable.toWeb(nodeWriteStream);
```

---

## 3. Composing Native Web Streams (`pipeThrough` & `pipeTo`)

```typescript
import { ReadableStream, TransformStream, WritableStream } from 'node:stream/web';

// 1. Create a Web Stream Data Source
const webSource = new ReadableStream<string>({
  start(controller) {
    controller.enqueue('first-chunk');
    controller.enqueue('second-chunk');
    controller.enqueue('third-chunk');
    controller.close();
  }
});

// 2. Create a Web Transform Stream (Uppercase converter)
const uppercaseTransformer = new TransformStream<string, string>({
  transform(chunk, controller) {
    controller.enqueue(chunk.toUpperCase());
  }
});

// 3. Create a Web Writable Sink
const webSink = new WritableStream<string>({
  write(chunk) {
    console.log('[WEB SINK] Processed chunk:', chunk);
  },
  close() {
    console.log('[WEB SINK] Stream completely finished.');
  }
});

// 4. Pipe together using WHATWG standard methods
await webSource
  .pipeThrough(uppercaseTransformer)
  .pipeTo(webSink);

/*
Output:
[WEB SINK] Processed chunk: FIRST-CHUNK
[WEB SINK] Processed chunk: SECOND-CHUNK
[WEB SINK] Processed chunk: THIRD-CHUNK
[WEB SINK] Stream completely finished.
*/
```

---

## 4. BYOB (Bring-Your-Own-Buffer) Readers for Zero-Allocation

For extreme low-latency performance in network and binary decoders, WHATWG ByteStreams support **BYOB (Bring Your Own Buffer)** readers. BYOB allows the consumer to supply a pre-allocated `ArrayBuffer` directly to the reader, eliminating temporary garbage collector allocations:

```typescript
async function readWithByob(stream: ReadableStream<Uint8Array>) {
  // Obtain dedicated byte reader
  const reader = stream.getReader({ mode: 'byob' });
  
  // Allocate reusable 4KB buffer
  let buffer = new ArrayBuffer(4096);

  try {
    while (true) {
      // Pass pre-allocated buffer view directly to reader (Zero Allocation)
      const { done, value } = await reader.read(new Uint8Array(buffer, 0, 4096));
      
      if (done) {
        console.log('[BYOB] Stream read complete.');
        break;
      }

      console.log(`[BYOB] Read ${value.byteLength} bytes directly into memory.`);
      // Reclaim the transferred buffer for subsequent reads
      buffer = value.buffer;
    }
  } finally {
    reader.releaseLock();
  }
}
```

# 6) Modern Web APIs and Global Standards in Node.js

## Executive Overview
Historically, Node.js and browser JavaScript environments maintained separate, incompatible API ecosystems. With modern Node.js versions (v18, v20, v22+), the runtime has aligned with WinterCG (Web-interoperable Runtimes Community Group) and WHATWG standards. Node.js now provides first-class global implementations of `fetch`, `AbortController`, `WebSocket`, `FormData`, `TextEncoder`/`TextDecoder`, `structuredClone`, and high-resolution `performance` APIs out of the box with zero external dependencies.

---

## 1. Native Global Fetch API (`undici` Engine)

Node.js bundles the high-performance `undici` HTTP/1.1 and HTTP/2 client directly as the backend for the global `fetch` API.

### 1.1 Complete Production `fetch` Request Lifecycle
```typescript
interface UserProfile {
  id: string;
  name: string;
  email: string;
}

async function fetchUserProfile(userId: string): Promise<UserProfile> {
  // Built-in timeout with AbortSignal.timeout()
  const signal = AbortSignal.timeout(3500); // 3.5s timeout

  const headers = new Headers({
    'Accept': 'application/json',
    'User-Agent': 'NodejsMasteryApp/1.0',
    'Authorization': 'Bearer YOUR_JWT_TOKEN'
  });

  const response = await fetch(`https://api.example.com/v1/users/${encodeURIComponent(userId)}`, {
    method: 'GET',
    headers,
    signal,
  });

  if (!response.ok) {
    throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
  }

  const data = (await response.json()) as UserProfile;
  return data;
}
```

### 1.2 Streaming Large Uploads & Downloads with Native Web Streams
`fetch` in Node.js supports standard WHATWG `ReadableStream` and `WritableStream`:

```typescript
import fs from 'node:fs';
import { Readable } from 'node:stream';

async function streamUploadFile(filePath: string, targetUrl: string) {
  const nodeStream = fs.createReadStream(filePath);
  
  // Convert Node.js Readable stream into Web ReadableStream
  const webStream = Readable.toWeb(nodeStream);

  const response = await fetch(targetUrl, {
    method: 'POST',
    body: webStream,
    // @ts-ignore - Required for streaming request bodies in Node.js fetch
    duplex: 'half',
  });

  console.log('Upload status:', response.status);
}
```

---

## 2. Asynchronous Cancellation & Timeouts (`AbortController`)

`AbortController` and `AbortSignal` provide the unified standard for cooperative cancellation across asynchronous operations in Node.js, supported by `fetch`, `fs/promises`, `events`, `timers/promises`, `child_process`, and HTTP clients.

### 2.1 Multi-Signal Composition: `AbortSignal.any()`

```typescript
import { setTimeout as sleep } from 'node:timers/promises';

async function executeResilientTask(userCancelSignal: AbortSignal) {
  // 1. Timeout signal (auto-aborts after 5 seconds)
  const timeoutSignal = AbortSignal.timeout(5000);

  // 2. Combine caller's cancellation signal with timeout signal
  const combinedSignal = AbortSignal.any([userCancelSignal, timeoutSignal]);

  try {
    console.log('[TASK] Starting long-running network operation...');
    const response = await fetch('https://httpbin.org/delay/2', {
      signal: combinedSignal,
    });
    const result = await response.json();
    console.log('[TASK] Completed successfully:', result);
  } catch (error: any) {
    if (error.name === 'AbortError') {
      if (timeoutSignal.aborted) {
        console.error('[TASK] Failed due to Request Timeout (> 5s).');
      } else if (userCancelSignal.aborted) {
        console.error('[TASK] Cancelled by user action.');
      }
    } else {
      console.error('[TASK] Unexpected network error:', error);
    }
  }
}
```

---

## 3. High-Resolution Performance APIs (`perf_hooks`)

The global `performance` object complies with the W3C High Resolution Time Level 2 specification.

### 3.1 Measuring Code Blocks with `PerformanceObserver`

```typescript
import { performance, PerformanceObserver } from 'node:perf_hooks';

// 1. Set up an observer to aggregate performance measurements
const obs = new PerformanceObserver((list) => {
  const entries = list.getEntries();
  for (const entry of entries) {
    console.log(`[PERF ENTRY] ${entry.name}: ${entry.duration.toFixed(3)} ms`);
  }
});
obs.observe({ entryTypes: ['measure'], buffered: true });

// 2. Mark and measure execution blocks
performance.mark('expensive-calc-start');

// Simulate computationally heavy task
let sum = 0;
for (let i = 0; i < 1_000_000; i++) {
  sum += Math.sqrt(i);
}

performance.mark('expensive-calc-end');
performance.measure('Expensive Math Calculation', 'expensive-calc-start', 'expensive-calc-end');
```

### 3.2 Real-time Event Loop Delay Monitoring

```typescript
import { monitorEventLoopDelay } from 'node:perf_hooks';

// Samples event loop delay at 20ms resolution
const histogram = monitorEventLoopDelay({ resolution: 20 });
histogram.enable();

// Periodically report latency percentiles
setInterval(() => {
  console.log({
    minDelayMs: (histogram.min / 1e6).toFixed(2),
    meanDelayMs: (histogram.mean / 1e6).toFixed(2),
    p50Ms: (histogram.percentile(50) / 1e6).toFixed(2),
    p99Ms: (histogram.percentile(99) / 1e6).toFixed(2),
    maxDelayMs: (histogram.max / 1e6).toFixed(2),
  });
  histogram.reset();
}, 5000);
```

---

## 4. Text Encoding Standards (`TextEncoder` & `TextDecoder`)

Web Standard byte conversion tools operate directly on `Uint8Array` binary structures without requiring the legacy Node.js `Buffer` wrapper:

```typescript
// 1. Encoding string to Uint8Array (always UTF-8)
const encoder = new TextEncoder();
const utf8Bytes: Uint8Array = encoder.encode('Hello World 🚀 Node.js');

// 2. Decoding Uint8Array back to string
const decoder = new TextDecoder('utf-8', { fatal: true }); // Throws on invalid byte sequence
const decodedString = decoder.decode(utf8Bytes);

console.log('Decoded text:', decodedString);

// 3. Streaming Multi-byte Decoder (handles characters split across chunk boundaries)
const streamDecoder = new TextDecoder('utf-8', { fatal: false });
const chunk1 = new Uint8Array([0xf0, 0x9f]); // First half of 4-byte 🚀 emoji
const chunk2 = new Uint8Array([0x9a, 0x80]); // Second half

const textPart1 = streamDecoder.decode(chunk1, { stream: true }); // '' (Buffering incomplete sequence)
const textPart2 = streamDecoder.decode(chunk2, { stream: false }); // '🚀' (Emits complete glyph)

console.log('Streamed glyph:', textPart1 + textPart2);
```

---

## 5. Global WebSocket Client (`Node.js v21+`)

Node.js includes a native WHATWG `WebSocket` client globally available without third-party packages:

```typescript
export function createWebSocketConnection(url: string) {
  const ws = new WebSocket(url);

  ws.addEventListener('open', () => {
    console.log('[WS] Connected to server.');
    ws.send(JSON.stringify({ type: 'SUBSCRIBE', channel: 'ticker_btc' }));
  });

  ws.addEventListener('message', (event) => {
    console.log('[WS] Message received from server:', event.data);
  });

  ws.addEventListener('close', (event) => {
    console.log(`[WS] Connection closed (code: ${event.code}, reason: ${event.reason})`);
  });

  ws.addEventListener('error', (error) => {
    console.error('[WS] WebSocket error encountered:', error);
  });

  return ws;
}
```

---

## 6. Deep Cloning Primitives: `structuredClone()`

Unlike `JSON.parse(JSON.stringify(obj))`, native `structuredClone()` correctly handles circular references, `Date`, `RegExp`, `Map`, `Set`, `ArrayBuffer`, and `TypedArrays`.

```typescript
const original = {
  id: 101,
  metadata: new Map([['owner', 'dev-team'], ['tier', 'enterprise']]),
  timestamp: new Date(),
  flags: new Set(['active', 'verified']),
  circularRef: {} as any
};
original.circularRef.self = original; // Circular reference

// Deep clone preserved perfectly without stack overflow
const cloned = structuredClone(original);

console.log(cloned.metadata.get('tier')); // 'enterprise'
console.log(cloned.circularRef.self === cloned); // true
console.log(cloned.circularRef.self === original); // false (isolated copy)
```

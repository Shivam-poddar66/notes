# 3) Flow Control, Backpressure, and Pipeline Mastery

## Executive Overview
The single most critical concept in high-throughput stream processing is **Backpressure**. Backpressure is the feedback mechanism that prevents a fast data producer (e.g. reading from a fast SSD at 2 GB/s) from overwhelming a slow data consumer (e.g. sending over a slow 3G cellular connection at 50 KB/s).

Failing to handle backpressure properly results in unbounded memory accumulation in the V8 heap, causing severe garbage collection stalls and catastrophic Out-Of-Memory (OOM) crashes.

---

## 1. Operating Modes of Readable Streams

A `Readable` stream operates in one of two distinct modes:

```text
┌────────────────────────────────────────────────────────┐
│               Readable Stream Modes                    │
├──────────────────────────┬─────────────────────────────┤
│       Paused Mode        │        Flowing Mode         │
│  - Default initial state │  - Activated by:            │
│  - Data fetched via      │    • .on('data', handler)   │
│    stream.read()         │    • .pipe(writable)        │
│  - Controlled pull model │    • .resume()              │
│                          │  - Uncontrolled push model  │
└──────────────────────────┴─────────────────────────────┘
```

Switching back to paused mode is accomplished by calling `readable.pause()` or removing all `'data'` listeners.

---

## 2. The Backpressure Mechanism in Depth

When writing to a `Writable` stream:
1. `writable.write(chunk)` places the chunk into the stream's internal queue.
2. If the internal queue size is **less than `highWaterMark`**, `writable.write()` returns **`true`**.
3. If the internal queue size **reaches or exceeds `highWaterMark`**, `writable.write()` returns **`false`** (Signaling: *"Stop sending data! Buffer is full!"*).
4. Once the underlying destination (kernel socket buffer, disk controller) drains the queue, the Writable emits the **`'drain'`** event (Signaling: *"Buffer is ready for more data!"*).

```text
Producer (Fast SSD) ────────[ 64KB Chunk ]────────> Consumer Internal Queue
                                                           │
                                                           ▼
                                                    [ Exceeds 64KB ]
                                                           │
                                                           ▼
Producer <──────── Returns FALSE (Pause!) ─────────────────┘
    │
    ▼
(Producer Pauses Reading)
    │
    ▼
Consumer drains to Disk/Network ──────> Emits 'drain' Event
                                             │
                                             ▼
Producer Resumes Reading! <──────────────────┘
```

### 2.1 Manual Backpressure Handling Loop (The Hard Way)

```typescript
import fs from 'node:fs';

export function copyWithManualBackpressure(srcPath: string, destPath: string) {
  const readStream = fs.createReadStream(srcPath);
  const writeStream = fs.createWriteStream(destPath);

  readStream.on('data', (chunk) => {
    // Attempt to write chunk
    const canAcceptMore = writeStream.write(chunk);

    // If internal queue is saturated, pause reading from source
    if (!canAcceptMore) {
      console.log('[BACKPRESSURE] Destination queue saturated. Pausing source stream...');
      readStream.pause();
    }
  });

  // When consumer flushes internal buffer, resume reading
  writeStream.on('drain', () => {
    console.log('[BACKPRESSURE] Buffer drained. Resuming source stream...');
    readStream.resume();
  });

  readStream.on('end', () => {
    writeStream.end();
  });
}
```

---

## 3. The Flaws of `readable.pipe(writable)`

While `readable.pipe(writable)` automatically handles backpressure and `'drain'` events, **it does not handle errors safely**:

```text
┌────────────────────────────────────────────────────────┐
│           The Danger of readable.pipe(writable)        │
│                                                        │
│   Source (fs.read) ──────.pipe()──────> Dest (fs.write)│
│          │                                     │       │
│          ▼ Error!                              ▼ Error!│
│   Unhandled Error!                      Destination    │
│   Stream NOT destroyed                  FD stays OPEN  │
│   Memory Leaks!                         Resource Leak! │
└────────────────────────────────────────────────────────┘
```

1. **No Error Forwarding**: An error in `Source` does **not** destroy `Dest`, leaving file descriptors open.
2. **Crash Vulnerability**: You must manually attach `.on('error')` listeners to every single stream in the chain.
3. **No Automatic Cleanup**: When an error occurs midway through a 5-step transform chain, intermediate streams remain dangling in memory.

---

## 4. Enterprise Standard: `stream.pipeline()` & `stream/promises`

Modern Node.js provides `stream.pipeline()` and the Promise-based `pipeline` from `node:stream/promises`.

### Advantages:
- Automatically manages backpressure.
- Automatically listens for errors across **all** streams in the pipeline.
- Automatically **destroys and cleans up** all streams in the pipeline if any stream errors or closes prematurely.
- Full support for `AbortSignal` for graceful cancellation.

```typescript
import { pipeline } from 'node:stream/promises';
import fs from 'node:fs';
import zlib from 'node:zlib';
import crypto from 'node:crypto';

export async function compressAndEncryptFile(
  sourceFile: string,
  destinationFile: string,
  secretKey32: Buffer,
  abortSignal: AbortSignal
): Promise<void> {
  const iv = crypto.randomBytes(12);

  // Write IV to destination first or pass via metadata
  await fs.promises.writeFile(destinationFile, iv);

  const sourceStream = fs.createReadStream(sourceFile);
  const gzipStream = zlib.createGzip();
  const cipherStream = crypto.createCipheriv('aes-256-gcm', secretKey32, iv);
  const destinationStream = fs.createWriteStream(destinationFile, { flags: 'a' });

  try {
    // Pipeline coordinates 4 streams safely with abort support
    await pipeline(
      sourceStream,
      gzipStream,
      cipherStream,
      destinationStream,
      { signal: abortSignal }
    );

    console.log('[PIPELINE] File compressed and encrypted successfully with zero memory leak!');
  } catch (err: any) {
    if (err.name === 'AbortError') {
      console.warn('[PIPELINE] Operation cancelled via AbortSignal. All streams destroyed.');
    } else {
      console.error('[PIPELINE] Pipeline failed:', err.message);
    }
    throw err;
  }
}
```

---

## 5. Modern Stream Consumption via Async Iteration

You can consume readable streams natively using `for await ... of`. Async iterators automatically handle backpressure under the hood by pausing the source stream between loop iterations:

```typescript
import fs from 'node:fs';

async function processLogFileInChunks(logPath: string) {
  const stream = fs.createReadStream(logPath, { encoding: 'utf-8', highWaterMark: 16 * 1024 });

  let lineCount = 0;

  // Async iterator automatically pauses source while loop body executes
  for await (const chunk of stream) {
    lineCount += (chunk.match(/\n/g) || []).length;
    
    // Simulate asynchronous parsing work
    await new Promise((resolve) => setImmediate(resolve));
  }

  console.log(`Total lines parsed: ${lineCount}`);
}
```

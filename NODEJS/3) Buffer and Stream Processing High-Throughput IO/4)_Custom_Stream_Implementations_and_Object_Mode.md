# 4) Custom Stream Implementations and Object Mode

## Executive Overview
While standard libraries provide out-of-the-box stream instances for files, sockets, and compression, real-world distributed architectures require writing **custom streams**. Whether parsing custom binary protocols, streaming rows directly from database cursors, throttling network throughput, or batching bulk database inserts, understanding how to extend `Readable`, `Writable`, `Transform`, and `Duplex` is a core senior skill.

---

## 1. Implementing a Custom `Readable` Stream

To implement a `Readable` stream, extend `Readable` and implement the private **`_read(size)`** method:
- **`this.push(chunk)`**: Adds chunk to internal queue. Returns `false` if `highWaterMark` reached.
- **`this.push(null)`**: Signals EOF (End of File / Stream Completion).

```typescript
import { Readable, ReadableOptions } from 'node:stream';

export interface CounterStreamOptions extends ReadableOptions {
  maxCount: number;
}

export class CounterReadableStream extends Readable {
  private current = 0;
  private max: number;

  constructor(options: CounterStreamOptions) {
    super(options);
    this.max = options.maxCount;
  }

  // Invoked automatically by Node.js when the stream is ready for more data
  override _read(size: number): void {
    this.current++;
    if (this.current <= this.max) {
      const payload = Buffer.from(`Counter record #${this.current}\n`, 'utf-8');
      
      // Push data chunk into internal buffer queue
      this.push(payload);
    } else {
      // Signal End of Stream (EOF)
      this.push(null);
    }
  }
}
```

---

## 2. Implementing a Custom `Writable` Stream (with Batching via `_writev`)

To implement a `Writable` stream, extend `Writable` and implement **`_write(chunk, encoding, callback)`**. For high-performance batching (e.g. bulk database inserts), implement **`_writev(chunks, callback)`**:

```typescript
import { Writable, WritableOptions } from 'node:stream';

interface DatabaseBulkInsertWritableOptions extends WritableOptions {
  dbClient: { insertMany: (items: any[]) => Promise<void> };
}

export class DatabaseBulkInsertWritable extends Writable {
  private dbClient: { insertMany: (items: any[]) => Promise<void> };

  constructor(options: DatabaseBulkInsertWritableOptions) {
    super({ ...options, objectMode: true });
    this.dbClient = options.dbClient;
  }

  // Single chunk fallback
  override async _write(chunk: any, encoding: string, callback: (error?: Error | null) => void) {
    try {
      await this.dbClient.insertMany([chunk]);
      callback(null);
    } catch (err: any) {
      callback(err);
    }
  }

  // High-Performance Batched Write (Combines multiple buffered chunks into a single DB call)
  override async _writev(
    chunks: Array<{ chunk: any; encoding: string }>,
    callback: (error?: Error | null) => void
  ) {
    try {
      const items = chunks.map((entry) => entry.chunk);
      console.log(`[BULK INSERT] Inserting batch of ${items.length} records into database...`);
      await this.dbClient.insertMany(items);
      callback(null);
    } catch (err: any) {
      callback(err);
    }
  }
}
```

---

## 3. Implementing a Custom `Transform` Stream

A `Transform` stream processes incoming chunks, mutates or parses them, and emits the resulting output.
- **`_transform(chunk, encoding, callback)`**: Called for every incoming chunk. Use `this.push(result)` to emit.
- **`_flush(callback)`**: Called right before the stream closes to emit any final buffered data.

### Real-World Example: Line-by-Line Chunk Splitter (Handles Arbitrary Chunk Cuts)

```typescript
import { Transform, TransformCallback } from 'node:stream';

export class LineSplitterTransform extends Transform {
  private buffer = '';

  constructor() {
    super({ readableObjectMode: true }); // Emits string lines as objects
  }

  override _transform(chunk: Buffer | string, encoding: string, callback: TransformCallback): void {
    this.buffer += chunk.toString('utf-8');
    const lines = this.buffer.split('\n');

    // Retain the last incomplete fragment in the buffer
    this.buffer = lines.pop() || '';

    // Emit each complete line
    for (const line of lines) {
      if (line.trim().length > 0) {
        this.push(line);
      }
    }

    callback();
  }

  override _flush(callback: TransformCallback): void {
    // Flush the final remaining line if non-empty
    if (this.buffer.trim().length > 0) {
      this.push(this.buffer);
    }
    callback();
  }
}
```

---

## 4. `stream.PassThrough`: Monitoring & Teeing Streams

`PassThrough` is a trivial `Transform` implementation that outputs its input without modifying it. It is widely used for:
1. **Calculating Progress & Metrics**: Measuring byte counters midway through a pipeline.
2. **Teeing / Splitting**: Multiplexing one readable stream into multiple destinations (e.g. Write to Local Disk AND Upload to Cloud Storage simultaneously).

```typescript
import { PassThrough } from 'node:stream';
import fs from 'node:fs';

export function createProgressTracker(totalSizeBytes: number) {
  let bytesPassed = 0;
  const tracker = new PassThrough();

  tracker.on('data', (chunk: Buffer) => {
    bytesPassed += chunk.length;
    const percent = ((bytesPassed / totalSizeBytes) * 100).toFixed(1);
    console.log(`[PROGRESS] Processed: ${percent}% (${bytesPassed}/${totalSizeBytes} bytes)`);
  });

  return tracker;
}
```

# 6) Hands-On Lab Experiments and Production Pipelines

This chapter provides four production-grade stream pipelines demonstrating high-throughput streaming, memory-constrained processing, rate limiting, and on-the-fly cryptography.

---

## Lab 1: Multi-Gigabyte CSV Stream Parser with Constant Memory Footprint

### Objective
Process a 10GB CSV file line-by-line, parse fields, filter invalid rows, and stream formatted JSON output directly to disk with a constant V8 process memory footprint of **< 35 MB**.

```typescript
import fs from 'node:fs';
import { Transform, TransformCallback } from 'node:stream';
import { pipeline } from 'node:stream/promises';

// Step 1: Custom Transform to parse CSV chunks into typed objects
class CsvParserTransform extends Transform {
  private buffer = '';
  private headers: string[] | null = null;

  constructor() {
    super({ readableObjectMode: true }); // Emits JS Objects
  }

  override _transform(chunk: Buffer, encoding: string, callback: TransformCallback) {
    this.buffer += chunk.toString('utf-8');
    const lines = this.buffer.split('\n');
    this.buffer = lines.pop() || ''; // Keep incomplete trailing fragment

    for (const line of lines) {
      const trimmed = line.trim();
      if (!trimmed) continue;

      if (!this.headers) {
        // First row contains column headers
        this.headers = trimmed.split(',').map((h) => h.trim());
      } else {
        const values = trimmed.split(',').map((v) => v.trim());
        const record: Record<string, string> = {};
        this.headers.forEach((header, index) => {
          record[header] = values[index] ?? '';
        });
        this.push(record);
      }
    }
    callback();
  }
}

// Step 2: Custom Transform to filter and format to JSON Lines
class JsonFormatterTransform extends Transform {
  constructor() {
    super({ writableObjectMode: true }); // Accepts JS Objects, emits Buffers/Strings
  }

  override _transform(record: Record<string, string>, encoding: string, callback: TransformCallback) {
    // Filter out inactive users
    if (record.status === 'ACTIVE') {
      const jsonLine = JSON.stringify({
        userId: record.id,
        email: record.email,
        tier: record.tier || 'standard',
        processedAt: new Date().toISOString()
      }) + '\n';

      this.push(Buffer.from(jsonLine, 'utf-8'));
    }
    callback();
  }
}

// Step 3: Run the pipeline
export async function processHugeCsvFile(inputCsvPath: string, outputJsonlPath: string) {
  const readStream = fs.createReadStream(inputCsvPath, { highWaterMark: 64 * 1024 });
  const writeStream = fs.createWriteStream(outputJsonlPath);

  console.log('[CSV PIPELINE] Processing started...');
  await pipeline(
    readStream,
    new CsvParserTransform(),
    new JsonFormatterTransform(),
    writeStream
  );
  console.log('[CSV PIPELINE] Processing finished with constant memory profile!');
}
```

---

## Lab 2: Streaming Crypto-Gzip Pipeline (Encrypt + Compress + SHA-256 on the fly)

### Architecture
Tees an incoming readable stream into a cryptographic pipeline while calculating the uncompressed SHA-256 hash checksum in real time using `stream.PassThrough`:

```typescript
import { pipeline } from 'node:stream/promises';
import { PassThrough } from 'node:stream';
import fs from 'node:fs';
import zlib from 'node:zlib';
import crypto from 'node:crypto';

export async function secureCompressAndHashStream(
  sourcePath: string,
  destEncryptedPath: string,
  key32: Buffer
): Promise<{ sha256Checksum: string; ivHex: string; tagHex: string }> {
  const iv = crypto.randomBytes(12);
  const cipher = crypto.createCipheriv('aes-256-gcm', key32, iv);
  const gzip = zlib.createGzip({ level: 9 });
  const hash = crypto.createHash('sha256');

  const sourceStream = fs.createReadStream(sourcePath);
  const destinationStream = fs.createWriteStream(destEncryptedPath);

  // Use PassThrough to tap raw bytes for hashing without breaking pipeline flow
  const hashTap = new PassThrough();
  hashTap.on('data', (chunk) => hash.update(chunk));

  await pipeline(
    sourceStream,
    hashTap,
    gzip,
    cipher,
    destinationStream
  );

  return {
    sha256Checksum: hash.digest('hex'),
    ivHex: iv.toString('hex'),
    tagHex: cipher.getAuthTag().toString('hex'),
  };
}
```

---

## Lab 3: Rate-Limiting Token Bucket Transform Stream

```typescript
import { Transform, TransformCallback } from 'node:stream';

export interface RateLimiterOptions {
  bytesPerSecond: number;
}

export class RateLimiterTransform extends Transform {
  private bytesPerSecond: number;
  private bytesSentInCurrentWindow = 0;
  private windowStart = Date.now();

  constructor(options: RateLimiterOptions) {
    super();
    this.bytesPerSecond = options.bytesPerSecond;
  }

  override _transform(chunk: Buffer, encoding: string, callback: TransformCallback): void {
    const now = Date.now();
    const elapsed = now - this.windowStart;

    if (elapsed >= 1000) {
      // Reset 1-second rate limit window
      this.windowStart = now;
      this.bytesSentInCurrentWindow = 0;
    }

    this.bytesSentInCurrentWindow += chunk.length;

    if (this.bytesSentInCurrentWindow > this.bytesPerSecond) {
      const waitTimeMs = Math.max(0, 1000 - elapsed);
      // Throttle and delay callback execution to introduce backpressure
      setTimeout(() => {
        this.push(chunk);
        callback();
      }, waitTimeMs);
    } else {
      this.push(chunk);
      callback();
    }
  }
}
```

---

## Lab 4: Resilient Streamed Chunked HTTP Uploader with AbortSignal

```typescript
import { Readable } from 'node:stream';
import fs from 'node:fs';

export async function uploadStreamWithProgress(
  filePath: string,
  targetUrl: string,
  abortSignal: AbortSignal
): Promise<void> {
  const stats = await fs.promises.stat(filePath);
  const totalBytes = stats.size;
  let bytesUploaded = 0;

  const fileStream = fs.createReadStream(filePath, { highWaterMark: 128 * 1024 });

  // Monitor progress on the fly
  fileStream.on('data', (chunk: Buffer) => {
    bytesUploaded += chunk.length;
    const progress = ((bytesUploaded / totalBytes) * 100).toFixed(2);
    console.log(`[UPLOAD PROGRESS] ${progress}% (${bytesUploaded} / ${totalBytes} bytes)`);
  });

  // Convert to Web ReadableStream for fetch API
  const webStream = Readable.toWeb(fileStream);

  const response = await fetch(targetUrl, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/octet-stream',
      'Content-Length': totalBytes.toString(),
    },
    body: webStream,
    // @ts-ignore
    duplex: 'half',
    signal: abortSignal,
  });

  if (!response.ok) {
    throw new Error(`Upload failed with HTTP ${response.status}: ${response.statusText}`);
  }

  console.log('[UPLOAD SUCCESS] File uploaded completely.');
}
```

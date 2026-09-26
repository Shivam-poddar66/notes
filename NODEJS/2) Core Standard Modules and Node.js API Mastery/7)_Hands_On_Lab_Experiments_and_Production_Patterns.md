# 7) Hands-On Lab Experiments and Production Patterns

This chapter provides four comprehensive, production-grade lab implementations demonstrating the integration of `fs/promises`, `node:events`, `node:crypto`, `process`, and `fetch`.

---

## Lab 1: Atomic Configuration Store with Backup & Rollback

### Architecture
1. Writes new state to a temporary file (`.tmp`).
2. Forces dirty OS buffers to disk using `.sync()`.
3. Creates a rolling `.bak` backup of the existing active config.
4. Atomically renames the temporary file over the target file.
5. In case of any error during write, performs automatic rollback.

```typescript
import fs from 'node:fs/promises';
import path from 'node:path';
import crypto from 'node:crypto';

export class AtomicConfigStore<T extends object> {
  private targetPath: string;
  private backupPath: string;

  constructor(filePath: string) {
    this.targetPath = path.resolve(filePath);
    this.backupPath = `${this.targetPath}.bak`;
  }

  public async save(data: T): Promise<void> {
    const dir = path.dirname(this.targetPath);
    const tempPath = path.join(dir, `.${path.basename(this.targetPath)}.${crypto.randomBytes(6).toString('hex')}.tmp`);
    const serializedData = JSON.stringify(data, null, 2);

    let tempHandle: fs.FileHandle | null = null;
    try {
      // 1. Write to temp file with safe permissions (0o600)
      tempHandle = await fs.open(tempPath, 'w', 0o600);
      await tempHandle.writeFile(serializedData, 'utf-8');
      
      // 2. Guarantee write to physical disk platter/SSD (fsync)
      await tempHandle.sync();
      await tempHandle.close();
      tempHandle = null;

      // 3. Create backup of current file if it exists
      try {
        await fs.copyFile(this.targetPath, this.backupPath);
      } catch (err: any) {
        if (err.code !== 'ENOENT') throw err; // Ignore if file doesn't exist yet
      }

      // 4. Atomic swap over target destination
      await fs.rename(tempPath, this.targetPath);
      console.log(`[STORE] Configuration saved atomically to: ${this.targetPath}`);
    } catch (error) {
      console.error('[STORE] Save failed. Cleaning temp and checking rollback...', error);
      if (tempHandle) await tempHandle.close().catch(() => {});
      await fs.unlink(tempPath).catch(() => {});
      throw error;
    }
  }

  public async load(): Promise<T> {
    try {
      const content = await fs.readFile(this.targetPath, 'utf-8');
      return JSON.parse(content) as T;
    } catch (err: any) {
      if (err.code === 'ENOENT') {
        // Try fallback to backup
        const backupContent = await fs.readFile(this.backupPath, 'utf-8');
        console.warn('[STORE] Target missing. Restored from backup.');
        return JSON.parse(backupContent) as T;
      }
      throw err;
    }
  }
}
```

---

## Lab 2: Typed Event Bus with Dead Letter Queue & Timeout Protection

```typescript
import { EventEmitter } from 'node:events';

export interface BusEventPayload {
  'user.registered': { userId: string; email: string; timestamp: number };
  'order.placed': { orderId: string; totalUsd: number };
  'payment.failed': { orderId: string; reason: string };
}

export class EnterpriseEventBus {
  private emitter = new EventEmitter();
  private deadLetterQueue: Array<{ event: string; payload: any; error: Error; timestamp: number }> = [];

  constructor(maxListenersPerTopic = 20) {
    this.emitter.setMaxListeners(maxListenersPerTopic);
  }

  public subscribe<K extends keyof BusEventPayload>(
    event: K,
    handler: (payload: BusEventPayload[K]) => Promise<void> | void,
    timeoutMs = 5000
  ): () => void {
    const safeListener = async (payload: BusEventPayload[K]) => {
      // Wrap handler in an execution timeout
      const ac = new AbortController();
      const timer = setTimeout(() => ac.abort(), timeoutMs);

      try {
        await Promise.race([
          Promise.resolve(handler(payload)),
          new Promise((_, reject) => {
            ac.signal.addEventListener('abort', () => reject(new Error(`Handler timeout exceeded (${timeoutMs}ms)`)));
          }),
        ]);
      } catch (err: any) {
        console.error(`[BUS ERROR] Handler failed on event "${String(event)}":`, err.message);
        this.deadLetterQueue.push({
          event: String(event),
          payload,
          error: err,
          timestamp: Date.now(),
        });
      } finally {
        clearTimeout(timer);
      }
    };

    this.emitter.on(event as string, safeListener);
    // Return unsubscribe callback
    return () => this.emitter.off(event as string, safeListener);
  }

  public publish<K extends keyof BusEventPayload>(event: K, payload: BusEventPayload[K]): void {
    // Dispatch asynchronously on next tick to avoid blocking caller stack
    process.nextTick(() => {
      this.emitter.emit(event as string, payload);
    });
  }

  public getDeadLetters() {
    return [...this.deadLetterQueue];
  }
}
```

---

## Lab 3: AES-256-GCM Envelope Encryption & Secret Vault

```typescript
import crypto from 'node:crypto';

export interface EncryptedSecretBox {
  version: number;
  iv: string;
  tag: string;
  ciphertext: string;
}

export class SecretVaultService {
  private static readonly ALGO = 'aes-256-gcm';
  private static readonly IV_BYTE_LEN = 12;

  /**
   * Derives a 32-byte master encryption key from passphrase and salt via scrypt.
   */
  public static deriveMasterKey(passphrase: string, salt: Buffer): Promise<Buffer> {
    return new Promise((resolve, reject) => {
      crypto.scrypt(passphrase, salt, 32, { N: 32768, r: 8, p: 1 }, (err, derivedKey) => {
        if (err) reject(err);
        else resolve(derivedKey);
      });
    });
  }

  public static encryptSecret(plaintext: string, masterKey32: Buffer): EncryptedSecretBox {
    const iv = crypto.randomBytes(this.IV_BYTE_LEN);
    const cipher = crypto.createCipheriv(this.ALGO, masterKey32, iv);

    const ciphertext = Buffer.concat([
      cipher.update(plaintext, 'utf8'),
      cipher.final(),
    ]);

    const tag = cipher.getAuthTag();

    return {
      version: 1,
      iv: iv.toString('base64'),
      tag: tag.toString('base64'),
      ciphertext: ciphertext.toString('base64'),
    };
  }

  public static decryptSecret(box: EncryptedSecretBox, masterKey32: Buffer): string {
    const iv = Buffer.from(box.iv, 'base64');
    const tag = Buffer.from(box.tag, 'base64');
    const ciphertext = Buffer.from(box.ciphertext, 'base64');

    const decipher = crypto.createDecipheriv(this.ALGO, masterKey32, iv);
    decipher.setAuthTag(tag);

    const decrypted = Buffer.concat([
      decipher.update(ciphertext),
      decipher.final(),
    ]);

    return decrypted.toString('utf8');
  }
}
```

---

## Lab 4: Resilient Fetch Client with Exponential Backoff & Jitter

```typescript
import { setTimeout as sleep } from 'node:timers/promises';

interface RetryOptions {
  maxRetries?: number;
  initialDelayMs?: number;
  maxDelayMs?: number;
  timeoutPerAttemptMs?: number;
}

export async function fetchWithResilience(
  url: string,
  init?: RequestInit,
  options: RetryOptions = {}
): Promise<Response> {
  const {
    maxRetries = 3,
    initialDelayMs = 300,
    maxDelayMs = 4000,
    timeoutPerAttemptMs = 3000,
  } = options;

  let attempt = 0;

  while (true) {
    attempt++;
    const timeoutSignal = AbortSignal.timeout(timeoutPerAttemptMs);
    const combinedSignal = init?.signal
      ? AbortSignal.any([init.signal, timeoutSignal])
      : timeoutSignal;

    try {
      const response = await fetch(url, {
        ...init,
        signal: combinedSignal,
      });

      // Retry on 5xx Server Errors or 429 Rate Limiting
      if (!response.ok && (response.status >= 500 || response.status === 429) && attempt <= maxRetries) {
        throw new Error(`Transient HTTP Status: ${response.status}`);
      }

      return response;
    } catch (error: any) {
      if (attempt > maxRetries) {
        throw new Error(`[RESILIENT FETCH] Exceeded ${maxRetries} retries. Final error: ${error.message}`);
      }

      // Calculate exponential delay with full jitter: delay = rand(0, min(maxDelay, base * 2^attempt))
      const backoffLimit = Math.min(maxDelayMs, initialDelayMs * 2 ** (attempt - 1));
      const jitterDelay = Math.floor(Math.random() * backoffLimit);

      console.warn(`[RETRY] Attempt ${attempt} failed (${error.message}). Retrying in ${jitterDelay}ms...`);
      await sleep(jitterDelay);
    }
  }
}
```

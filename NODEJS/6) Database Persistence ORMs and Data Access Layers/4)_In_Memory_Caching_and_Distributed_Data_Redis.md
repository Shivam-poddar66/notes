# 4) In-Memory Caching and Distributed Data: Redis

## Executive Overview
Redis (Remote Dictionary Server) is an in-memory, single-threaded data structure server used as a high-speed cache, message broker, distributed lock coordinator, and job queue backend. In high-concurrency Node.js architectures, Redis offloads read-heavy pressure from primary databases (PostgreSQL/MongoDB) and provides sub-millisecond data retrieval.

Building resilient distributed caching systems requires mastering **Redis Data Structures**, **Caching Invalidation Strategies**, mitigating failure modes (**Cache Stampede**, **Cache Penetration**, **Cache Avalanche**), implementing **Distributed Locks (Redlock)**, and running background job queues with **`BullMQ`**.

---

## 1. Core Redis Data Structures in Node.js (`ioredis`)

```typescript
import Redis from 'ioredis';

export const redis = new Redis({
  host: process.env.REDIS_HOST || '127.0.0.1',
  port: parseInt(process.env.REDIS_PORT || '6379', 10),
  password: process.env.REDIS_PASSWORD || undefined,
  maxRetriesPerRequest: 3,
  enableReadyCheck: true,
  lazyConnect: true,
});

redis.on('error', (err) => console.error('[REDIS ERROR]', err.message));
```

### 1.1 Key Data Structures Overview

| Structure | Common Redis Commands | Typical Use Case |
|---|---|---|
| **String** | `SET`, `GET`, `INCR`, `SETEX` | Session storage, token storage, rate limit counters |
| **Hash** | `HSET`, `HGET`, `HGETALL`, `HINCRBY` | User profile objects, metadata dictionaries |
| **List** | `LPUSH`, `RPOP`, `BRPOP`, `LRANGE` | Simple FIFO task queues, activity feeds |
| **Set** | `SADD`, `SMEMBERS`, `SISMEMBER`, `SINTER` | Unique user IDs, followers/following, tag systems |
| **Sorted Set (ZSET)**| `ZADD`, `ZRANGEBYSCORE`, `ZREVRANGE` | Leaderboards, sliding window rate limiters |
| **Bitmap** | `SETBIT`, `GETBIT`, `BITCOUNT` | Daily active user (DAU) retention tracking |
| **HyperLogLog** | `PFADD`, `PFCOUNT` | Cardinality estimation (unique page visits > 100M) |

---

## 2. Distributed Caching Patterns

```text
1. Cache-Aside (Most Common):
Application ────> Check Cache ──(Miss)──> Query Database ────> Store in Cache
     │                                                             │
     └───────────────────(Hit: Return Cached Data) <───────────────┘

2. Write-Through:
Application ────> Update Cache ────> Synchronously Write to Database

3. Write-Behind (Write-Back):
Application ────> Update Cache ────> Async Worker drains updates to DB in batches
```

### 2.1 Implementing the Cache-Aside Pattern with Automatic TTL

```typescript
import { redis } from './redis';

export async function getCachedData<T>(
  cacheKey: string,
  ttlSeconds: number,
  fetchFromDb: () => Promise<T>
): Promise<T> {
  // 1. Attempt cache lookup
  const cachedValue = await redis.get(cacheKey);
  if (cachedValue) {
    return JSON.parse(cachedValue) as T;
  }

  // 2. Cache Miss: Execute database query
  const freshData = await fetchFromDb();

  // 3. Populate cache with TTL (Time-To-Live)
  if (freshData !== null && freshData !== undefined) {
    await redis.setex(cacheKey, ttlSeconds, JSON.stringify(freshData));
  }

  return freshData;
}
```

---

## 3. Combating Distributed Cache Failure Modes

```text
┌────────────────────┬────────────────────────────────────┬──────────────────────────────────┐
│ Failure Mode       │ Problem Description                │ Architectural Solution           │
├────────────────────┼────────────────────────────────────┼──────────────────────────────────┤
│ Cache Stampede /   │ A hot key expires; 10,000 requests │ Mutex Locking / Probabilistic    │
│ Thundering Herd    │ hit database simultaneously.       │ Early Expiration (XFetch).       │
├────────────────────┼────────────────────────────────────┼──────────────────────────────────┤
│ Cache Penetration  │ Queries for non-existent IDs       │ Bloom Filters or Caching NULL    │
│                    │ bypass cache and hammer the DB.    │ values with short TTL.           │
├────────────────────┼────────────────────────────────────┼──────────────────────────────────┤
│ Cache Avalanche    │ Thousands of keys expire at the    │ Add randomized jitter            │
│                    │ exact same second.                 │ to TTL durations.                │
└────────────────────┴────────────────────────────────────┴──────────────────────────────────┘
```

### 3.1 Mitigating Cache Avalanche with TTL Jitter

```typescript
export function getJitteredTtl(baseTtlSeconds: number, jitterMaxSeconds = 60): number {
  const jitter = Math.floor(Math.random() * jitterMaxSeconds);
  return baseTtlSeconds + jitter;
}
```

### 3.2 Probabilistic Early Expiration (XFetch Algorithm)
Instead of waiting for a hot key to expire, worker requests compute whether to recompute the value in the background based on remaining TTL and computation time:

$$\Delta + \beta \times \ln(\text{random}()) > \text{TTL}_{\text{remaining}}$$

---

## 4. Distributed Locks: Safe Atomic Mutexes (Redlock & Lua)

When coordinating work across multiple clustered Node.js server instances (e.g. ensuring a nightly report is only generated by one node), a local JavaScript mutex is insufficient.

### 4.1 Single-Node Redis Distributed Lock with Lua Unlock Script

A distributed lock must ensure:
1. **Mutual Exclusion**: Only one worker holds the lock.
2. **Deadlock Free**: Lock automatically expires via TTL if worker crashes.
3. **Safe Release**: A worker **never** unlocks a lock held by another worker whose execution took longer than the TTL.

```typescript
import { redis } from './redis';
import crypto from 'node:crypto';

export class DistributedLock {
  private static readonly UNLOCK_LUA_SCRIPT = `
    if redis.call("get", KEYS[1]) == ARGV[1] then
      return redis.call("del", KEYS[1])
    else
      return 0
    end
  `;

  /**
   * Acquires an exclusive distributed lock.
   */
  public static async acquire(
    lockKey: string,
    ttlMs: number
  ): Promise<{ acquired: boolean; token: string; release: () => Promise<boolean> }> {
    const token = crypto.randomUUID();
    // 'PX' sets millisecond expiration, 'NX' sets key only if it DOES NOT exist
    const result = await redis.set(lockKey, token, 'PX', ttlMs, 'NX');

    const acquired = result === 'OK';

    const release = async (): Promise<boolean> => {
      // Execute atomic Lua script to ensure we only delete OUR token
      const res = await redis.eval(
        this.UNLOCK_LUA_SCRIPT,
        1,
        lockKey,
        token
      );
      return res === 1;
    };

    return { acquired, token, release };
  }
}
```

---

## 5. Enterprise Background Job Queues with `BullMQ`

`BullMQ` is a Redis-backed distributed task queue for Node.js supporting concurrency control, exponential backoff retries, parent-child job workflows, and delayed execution.

```typescript
import { Queue, Worker, Job } from 'bullmq';
import { redis } from './redis';

const connection = {
  host: process.env.REDIS_HOST || '127.0.0.1',
  port: parseInt(process.env.REDIS_PORT || '6379', 10),
};

// 1. Initialize Job Queue (Producer)
export const emailQueue = new Queue('email-queue', { connection });

export async function enqueueWelcomeEmail(userId: string, email: string) {
  await emailQueue.add(
    'send-welcome',
    { userId, email },
    {
      attempts: 5, // Retry up to 5 times
      backoff: {
        type: 'exponential',
        delay: 2000, // 2s, 4s, 8s, 16s...
      },
      removeOnComplete: true, // Auto-cleanup completed jobs
    }
  );
  console.log(`[QUEUE] Enqueued welcome email job for: ${email}`);
}

// 2. Initialize Queue Worker (Consumer)
export const emailWorker = new Worker(
  'email-queue',
  async (job: Job) => {
    console.log(`[WORKER] Processing job ${job.id} of type: ${job.name}`);
    const { email } = job.data;
    
    // Simulate sending email...
    await new Promise((res) => setTimeout(res, 1000));
    
    console.log(`[WORKER] Successfully sent email to: ${email}`);
    return { status: 'DELIVERED', timestamp: Date.now() };
  },
  {
    connection,
    concurrency: 10, // Process 10 concurrent jobs per worker instance
  }
);
```

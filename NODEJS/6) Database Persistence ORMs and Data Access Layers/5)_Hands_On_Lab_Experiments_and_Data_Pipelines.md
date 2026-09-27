# 5) Hands-On Lab Experiments and Data Pipelines

This chapter provides four comprehensive, production-grade lab architectures demonstrating the integration of PostgreSQL transactions, Drizzle ORM repositories, MongoDB aggregation analytics, and Redis BullMQ distributed queues.

---

## Lab 1: High-Concurrency Financial Fund Transfer in PostgreSQL

### Objective
Execute an atomic, isolated fund transfer between two bank accounts ensuring:
1. Deadlock-free account locking (always locking account IDs in deterministic alphanumeric order).
2. Balance verification with pessimistic `SELECT ... FOR UPDATE`.
3. Complete rollback on any balance overdraft or network failure.

```typescript
import { PoolClient } from 'pg';
import { dbPool } from './db';

export interface TransferResult {
  transactionId: string;
  sourceAccountId: string;
  targetAccountId: string;
  amountUsd: number;
  newSourceBalance: number;
  newTargetBalance: number;
}

export async function transferFunds(
  sourceId: string,
  targetId: string,
  amountUsd: number
): Promise<TransferResult> {
  if (amountUsd <= 0) throw new Error('Transfer amount must be positive');
  if (sourceId === targetId) throw new Error('Cannot transfer to same account');

  const client: PoolClient = await dbPool.connect();

  try {
    await client.query('BEGIN ISOLATION LEVEL READ COMMITTED');

    // Deadlock Prevention: Always acquire locks in deterministic alphabetical order
    const [firstLockId, secondLockId] = [sourceId, targetId].sort();

    // Lock first account
    await client.query('SELECT id FROM accounts WHERE id = $1 FOR UPDATE', [firstLockId]);
    // Lock second account
    await client.query('SELECT id FROM accounts WHERE id = $1 FOR UPDATE', [secondLockId]);

    // 1. Fetch current balances
    const sourceRes = await client.query('SELECT balance FROM accounts WHERE id = $1', [sourceId]);
    const targetRes = await client.query('SELECT balance FROM accounts WHERE id = $1', [targetId]);

    const sourceAccount = sourceRes.rows[0];
    const targetAccount = targetRes.rows[0];

    if (!sourceAccount || !targetAccount) {
      throw new Error('One or both accounts do not exist');
    }

    if (Number(sourceAccount.balance) < amountUsd) {
      throw new Error(`Insufficient funds: Available $${sourceAccount.balance}, Requested $${amountUsd}`);
    }

    // 2. Perform updates
    const updateSource = await client.query(
      'UPDATE accounts SET balance = balance - $1 WHERE id = $2 RETURNING balance',
      [amountUsd, sourceId]
    );

    const updateTarget = await client.query(
      'UPDATE accounts SET balance = balance + $1 WHERE id = $2 RETURNING balance',
      [amountUsd, targetId]
    );

    // 3. Insert audit ledger entry
    const ledgerRes = await client.query(
      `INSERT INTO ledger_entries (source_account_id, target_account_id, amount_usd, status)
       VALUES ($1, $2, $3, 'COMPLETED') RETURNING id`,
      [sourceId, targetId, amountUsd]
    );

    await client.query('COMMIT');

    return {
      transactionId: ledgerRes.rows[0].id,
      sourceAccountId: sourceId,
      targetAccountId: targetId,
      amountUsd,
      newSourceBalance: Number(updateSource.rows[0].balance),
      newTargetBalance: Number(updateTarget.rows[0].balance),
    };
  } catch (error) {
    await client.query('ROLLBACK');
    throw error;
  } finally {
    client.release();
  }
}
```

---

## Lab 2: Production Drizzle ORM Base Repository with Soft-Deletes & Pagination

```typescript
import { NodePgDatabase } from 'drizzle-orm/node-postgres';
import { eq, and, isNull, desc, count } from 'drizzle-orm';
import { pgTable, uuid, varchar, timestamp, boolean } from 'drizzle-orm/pg-core';

// 1. Base Entity Table Definition
export const articles = pgTable('articles', {
  id: uuid('id').primaryKey().defaultRandom(),
  title: varchar('title', { length: 255 }).notNull(),
  content: varchar('content', { length: 5000 }).notNull(),
  isPublished: boolean('is_published').default(false).notNull(),
  deletedAt: timestamp('deleted_at'), // Soft-delete column
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

export interface PaginationParams {
  page: number;
  limit: number;
}

export interface PaginatedResult<T> {
  data: T[];
  total: number;
  page: number;
  totalPages: number;
}

export class ArticleRepository {
  constructor(private db: NodePgDatabase) {}

  public async findActivePaginated(pagination: PaginationParams): Promise<PaginatedResult<typeof articles.$inferSelect>> {
    const { page, limit } = pagination;
    const offset = (page - 1) * limit;

    // Filter out soft-deleted records
    const whereCondition = and(isNull(articles.deletedAt), eq(articles.isPublished, true));

    // Parallel execution for data and count queries
    const [data, totalCountRes] = await Promise.all([
      this.db
        .select()
        .from(articles)
        .where(whereCondition)
        .orderBy(desc(articles.createdAt))
        .limit(limit)
        .offset(offset),
      this.db
        .select({ total: count() })
        .from(articles)
        .where(whereCondition),
    ]);

    const total = totalCountRes[0]?.total ?? 0;

    return {
      data,
      total,
      page,
      totalPages: Math.ceil(total / limit),
    };
  }

  public async softDelete(articleId: string): Promise<boolean> {
    const result = await this.db
      .update(articles)
      .set({ deletedAt: new Date() })
      .where(and(eq(articles.id, articleId), isNull(articles.deletedAt)));

    return (result.rowCount ?? 0) > 0;
  }
}
```

---

## Lab 3: MongoDB Aggregation Pipeline for Real-Time E-Commerce Metrics

```typescript
import mongoose, { Schema, model } from 'mongoose';

const OrderItemSchema = new Schema({
  productId: { type: Schema.Types.ObjectId, ref: 'Product', required: true },
  category: { type: String, required: true },
  quantity: { type: Number, required: true },
  priceUsd: { type: Number, required: true },
});

const OrderSchema = new Schema(
  {
    customerId: { type: Schema.Types.ObjectId, ref: 'User', required: true },
    items: [OrderItemSchema],
    totalAmountUsd: { type: Number, required: true },
    paymentStatus: { type: String, enum: ['PAID', 'PENDING', 'REFUNDED'], required: true },
  },
  { timestamps: true }
);

export const Order = model('Order', OrderSchema);

export async function generateCategorySalesReport(startDate: Date, endDate: Date) {
  return await Order.aggregate([
    // Match completed sales within time interval
    {
      $match: {
        paymentStatus: 'PAID',
        createdAt: { $gte: startDate, $lte: endDate },
      },
    },
    // Deconstruct order items
    { $unwind: '$items' },
    // Group by category and compute summary metrics
    {
      $group: {
        _id: '$items.category',
        totalRevenue: { $sum: { $multiply: ['$items.quantity', '$items.priceUsd'] } },
        totalUnitsSold: { $sum: '$items.quantity' },
        distinctOrders: { $addToSet: '$_id' },
      },
    },
    // Shape output projection
    {
      $project: {
        _id: 0,
        category: '$_id',
        totalRevenueUsd: { $round: ['$totalRevenue', 2] },
        totalUnitsSold: 1,
        totalOrdersCount: { $size: '$distinctOrders' },
      },
    },
    // Sort by highest revenue
    { $sort: { totalRevenueUsd: -1 } },
  ]);
}
```

---

## Lab 4: BullMQ Job Queue with Graceful Shutdown & Redis Mutex

```typescript
import { Queue, Worker, Job } from 'bullmq';
import { DistributedLock } from './distributed-lock';

const connection = { host: '127.0.0.1', port: 6379 };

export const reportQueue = new Queue('report-generation', { connection });

// Worker that generates heavy PDF reports with single-instance execution guarantee
export const reportWorker = new Worker(
  'report-generation',
  async (job: Job) => {
    const { reportId, organizationId } = job.data;
    const lockKey = `lock:report:${organizationId}`;

    console.log(`[WORKER] Acquiring distributed lock for org: ${organizationId}`);
    const { acquired, release } = await DistributedLock.acquire(lockKey, 30000); // 30s TTL

    if (!acquired) {
      console.warn(`[WORKER] Report already being generated for org ${organizationId}. Skipping.`);
      return { skipped: true };
    }

    try {
      console.log(`[WORKER] Generating complex report ${reportId}...`);
      await new Promise((res) => setTimeout(res, 3000)); // Simulate PDF rendering
      console.log(`[WORKER] Report ${reportId} finished successfully.`);
      return { success: true, url: `https://cdn.example.com/reports/${reportId}.pdf` };
    } finally {
      await release();
      console.log(`[WORKER] Released lock for org: ${organizationId}`);
    }
  },
  { connection, concurrency: 5 }
);

// Graceful Worker Teardown Handler
export async function closeQueueAndWorker() {
  console.log('[SHUTDOWN] Closing BullMQ worker gracefully...');
  await reportWorker.close(); // Waits for active inflight jobs to finish
  await reportQueue.close();
  console.log('[SHUTDOWN] Queue and worker closed cleanly.');
}
```

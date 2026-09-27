# 1) Relational Databases, Connection Pools, and Transactions

## Executive Overview
Relational Database Management Systems (PostgreSQL, MySQL) are the backbone of transactional data persistence. In high-concurrency Node.js applications, application throughput and stability depend on proper **Connection Pool sizing**, strict **ACID transaction boundaries**, understanding **Transaction Isolation Levels**, and managing **Concurrency Contention** through optimistic or pessimistic locking.

---

## 1. Connection Pool Architecture & Lifecycle

Opening a TCP connection to PostgreSQL/MySQL involves a 3-way TCP handshake, SSL/TLS negotiation, authentication verification, and process/thread allocation on the database server (costing 20ms–100ms+ per connection).

A **Connection Pool** maintains a warm set of reusable database clients:

```text
┌────────────────────────────────────────────────────────┐
│                   Node.js Application                  │
│   HTTP Req 1      HTTP Req 2      HTTP Req 3           │
│       │               │               │                │
│       ▼               ▼               ▼                │
│ ┌────────────────────────────────────────────────────┐ │
│ │             Database Connection Pool               │ │
│ │  ┌──────────────┐ ┌──────────────┐ ┌─────────────┐ │ │
│ │  │ Active (Conn)│ │ Active (Conn)│ │ Idle (Conn) │ │ │
│ │  └──────┬───────┘ └──────┬───────┘ └─────────────┘ │ │
│ └─────────┼────────────────┼─────────────────────────┘ │
└───────────┼────────────────┼───────────────────────────┘
            │                │
            ▼                ▼
┌────────────────────────────────────────────────────────┐
│               PostgreSQL Database Server               │
│          Backend Worker 1      Backend Worker 2        │
└────────────────────────────────────────────────────────┘
```

### 1.1 Pool Sizing Formula (PostgreSQL Standard)
Setting a pool to 500 connections does **not** make queries 500x faster; it creates massive disk I/O thrashing and context switching on the database server.

The optimal pool size formula:
$$\text{Pool Size} = (\text{Core Count} \times 2) + \text{Spindle / SSD Count}$$

For a 4-core database server with SSD storage, a pool of **10 to 15 connections per Node.js process** is optimal.

### 1.2 Defensive Pool Configuration (`pg` / `node-postgres`)

```typescript
import { Pool, PoolClient } from 'pg';

export const dbPool = new Pool({
  host: process.env.DB_HOST || 'localhost',
  port: parseInt(process.env.DB_PORT || '5432', 10),
  database: process.env.DB_NAME || 'mastery_db',
  user: process.env.DB_USER || 'postgres',
  password: process.env.DB_PASSWORD || 'secret',
  
  // Connection Pool Tuning:
  max: 20,                       // Max active connections in pool
  min: 4,                        // Minimum warm idle connections
  idleTimeoutMillis: 30000,      // Close idle connections after 30s
  connectionTimeoutMillis: 3000, // Timeout after 3s if pool has no free connections
  maxUses: 7500,                 // Automatically cycle/recreate connection after 7,500 queries (prevents memory leaks)
});

// Always attach an error listener to prevent idle client disconnects from crashing the process
dbPool.on('error', (err: Error, client: PoolClient) => {
  console.error('[DB POOL ERROR] Unexpected error on idle client:', err.message);
});
```

---

## 2. Parameterized Queries & Prepared Statements

> [!CAUTION]
> Never concatenate raw user input into SQL queries. Always use **Parameterized Queries** where variables are sent out-of-band via PostgreSQL's binary protocol (`$1, $2, ...`), completely eliminating SQL Injection.

```typescript
import { dbPool } from './db';

export async function findUserByEmail(email: string) {
  // Safe: Query and parameters are separated
  const query = 'SELECT id, username, role, created_at FROM users WHERE email = $1 AND is_active = $2';
  const values = [email, true];

  const { rows } = await dbPool.query(query, values);
  return rows[0] || null;
}
```

---

## 3. Transaction Management & ACID Guarantees

A database transaction is an atomic unit of work satisfying **ACID** properties:
- **Atomicity**: All statements succeed, or the entire transaction is rolled back.
- **Consistency**: Database transitions from one valid state to another, preserving constraints.
- **Isolation**: Concurrent transactions execute without cross-talk or race conditions.
- **Durability**: Committed data persists even in the event of an unexpected server crash.

### 3.1 Transaction Execution Pattern with Auto-Rollback

```typescript
import { dbPool } from './db';
import { PoolClient } from 'pg';

export async function executeTransaction<T>(
  callback: (client: PoolClient) => Promise<T>
): Promise<T> {
  const client = await dbPool.connect(); // Checkout dedicated connection
  try {
    await client.query('BEGIN'); // Start transaction
    
    const result = await callback(client);
    
    await client.query('COMMIT'); // Commit changes
    return result;
  } catch (error) {
    await client.query('ROLLBACK'); // Rollback on any failure
    console.error('[TRANSACTION ROLLED BACK]', error);
    throw error;
  } finally {
    // Crucial: Always release the client back to the pool!
    client.release();
  }
}
```

---

## 4. Transaction Isolation Levels & Concurrency Anomalies

SQL standards define four isolation levels to balance consistency against concurrency performance:

```text
┌──────────────────┬─────────────┬──────────────────────┬──────────────┬─────────────────────────┐
│ Isolation Level  │ Dirty Reads │ Non-Repeatable Reads │ Phantom Read │ Serialization Anomalies │
├──────────────────┼─────────────┼──────────────────────┼──────────────┼─────────────────────────┤
│ Read Uncommitted │ Possible    │ Possible             │ Possible     │ Possible                │
│ Read Committed   │ Prevented   │ Possible             │ Possible     │ Possible                │
│ (Postgres Default│             │                      │              │                         │
├──────────────────┼─────────────┼──────────────────────┼──────────────┼─────────────────────────┤
│ Repeatable Read  │ Prevented   │ Prevented            │ Prevented    │ Possible                │
│                  │             │                      │ (in Postgres)│                         │
├──────────────────┼─────────────┼──────────────────────┼──────────────┼─────────────────────────┤
│ Serializable     │ Prevented   │ Prevented            │ Prevented    │ Prevented               │
│ (Highest Safety) │             │                      │              │                         │
└──────────────────┴─────────────┴──────────────────────┴──────────────┴─────────────────────────┘
```

- **Dirty Read**: Transaction reads uncommitted data written by a concurrent transaction that later rolls back.
- **Non-Repeatable Read**: Re-reading a row returns different data because a concurrent transaction committed changes in between.
- **Phantom Read**: Re-running a query with a `WHERE` clause returns newly inserted rows committed by another transaction.

Setting Isolation Level in PostgreSQL:
```typescript
await client.query('BEGIN TRANSACTION ISOLATION LEVEL REPEATABLE READ');
```

---

## 5. Concurrency Control: Optimistic vs Pessimistic Locking

When multiple concurrent requests attempt to modify the same resource (e.g. subtracting inventory during a flash sale or transferring funds between bank accounts):

### 5.1 Optimistic Locking (Best for Low Contention)
Does not lock rows during reading. Adds a `version` or `updated_at` column. When updating, checks if `version` matches. If 0 rows updated, a race condition occurred:

```sql
UPDATE products 
SET stock = stock - 1, version = version + 1 
WHERE id = $1 AND version = $2;
```

```typescript
export async function deductStockOptimistic(productId: string, currentVersion: number): Promise<boolean> {
  const result = await dbPool.query(
    'UPDATE products SET stock = stock - 1, version = version + 1 WHERE id = $1 AND version = $2',
    [productId, currentVersion]
  );
  // Returns true if row was successfully updated, false if concurrent modification occurred
  return (result.rowCount ?? 0) > 0;
}
```

### 5.2 Pessimistic Locking (Best for High Contention)
Acquires an exclusive row-level lock on the database record using **`SELECT ... FOR UPDATE`**, forcing other transactions attempting to read/modify that row to block until this transaction commits or rolls back:

```typescript
export async function deductStockPessimistic(client: PoolClient, productId: string, quantity: number) {
  // Lock the specific product row exclusively
  const { rows } = await client.query(
    'SELECT stock FROM products WHERE id = $1 FOR UPDATE',
    [productId]
  );

  const product = rows[0];
  if (!product || product.stock < quantity) {
    throw new Error('Insufficient stock');
  }

  await client.query(
    'UPDATE products SET stock = stock - $1 WHERE id = $2',
    [quantity, productId]
  );
}
```

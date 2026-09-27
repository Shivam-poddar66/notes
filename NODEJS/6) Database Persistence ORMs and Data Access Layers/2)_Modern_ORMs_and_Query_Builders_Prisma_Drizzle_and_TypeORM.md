# 2) Modern ORMs and Query Builders: Prisma, Drizzle, and TypeORM

## Executive Overview
Object-Relational Mapping (ORM) and Query Building in modern Node.js has evolved from heavy, decorator-driven Active Record models (TypeORM) toward **TypeScript-first, zero-overhead SQL query builders (Drizzle ORM)** and **schema-first type-safe engines (Prisma)**.

Understanding the tradeoffs between runtime query engine overhead, cold-start latency, bundle size, connection management, and zero-downtime migration patterns is vital for modern production architectures.

---

## 1. Modern ORM & Query Builder Comparison

```text
┌─────────────┬────────────────────┬─────────────────┬──────────────────┬──────────────────┐
│ Tool        │ Architectural Type │ Type Safety     │ Engine / Runtime │ Performance      │
├─────────────┼────────────────────┼─────────────────┼──────────────────┼──────────────────┤
│ Drizzle ORM │ SQL Query Builder  │ Pure TypeScript │ Native JS driver │ Near-Raw SQL     │
│             │ + Relational Layer │ Type Inference  │ (Zero overhead)  │ (Fastest)        │
├─────────────┼────────────────────┼─────────────────┼──────────────────┼──────────────────┤
│ Prisma      │ Schema DSL +       │ Generated TS    │ Rust Query       │ High Safety,     │
│             │ Generated Client   │ from Schema DSL │ Engine Binary    │ Engine Overhead  │
├─────────────┼────────────────────┼─────────────────┼──────────────────┼──────────────────┤
│ TypeORM     │ Data Mapper &      │ TS Decorators + │ Pure JS /        │ Moderate         │
│             │ Active Record      │ Reflect-Metadata│ Reflection       │ (Legacy pattern) │
├─────────────┼────────────────────┼─────────────────┼──────────────────┼──────────────────┤
│ Knex.js     │ Pure SQL Builder   │ Manual typing   │ Pure JS driver   │ Near-Raw SQL     │
│             │ (No ORM mapping)   │ (Knex types)    │ (Zero overhead)  │ (Fast)           │
└─────────────┴───────────────────┴─────────────────┴──────────────────┴──────────────────┘
```

---

## 2. Drizzle ORM: The TypeScript-First Standard

Drizzle ORM defines tables directly in TypeScript code without external DSLs or code-generation binaries.

### 2.1 Schema Definition (`schema.ts`)
```typescript
import { pgTable, uuid, varchar, timestamp, integer, boolean } from 'drizzle-orm/pg-core';
import { relations } from 'drizzle-orm';

export const users = pgTable('users', {
  id: uuid('id').primaryKey().defaultRandom(),
  username: varchar('username', { length: 50 }).notNull().unique(),
  email: varchar('email', { length: 255 }).notNull().unique(),
  isActive: boolean('is_active').default(true).notNull(),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

export const posts = pgTable('posts', {
  id: uuid('id').primaryKey().defaultRandom(),
  userId: uuid('user_id').references(() => users.id, { onDelete: 'cascade' }).notNull(),
  title: varchar('title', { length: 255 }).notNull(),
  viewCount: integer('view_count').default(0).notNull(),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

// Relational Mappings
export const usersRelations = relations(users, ({ many }) => ({
  posts: many(posts),
}));

export const postsRelations = relations(posts, ({ one }) => ({
  author: one(users, {
    fields: [posts.userId],
    references: [users.id],
  }),
}));
```

### 2.2 Relational & SQL Queries with Drizzle
```typescript
import { drizzle } from 'drizzle-orm/node-postgres';
import { eq, desc, gt } from 'drizzle-orm';
import { dbPool } from './db';
import * as schema from './schema';

export const db = drizzle(dbPool, { schema });

// 1. Relational Query (Single joined SQL execution under the hood)
export async function getUserWithRecentPosts(userId: string) {
  return await db.query.users.findFirst({
    where: eq(schema.users.id, userId),
    with: {
      posts: {
        where: gt(schema.posts.viewCount, 100),
        orderBy: [desc(schema.posts.createdAt)],
        limit: 10,
      },
    },
  });
}

// 2. Direct SQL-Style Query Builder
export async function incrementPostViews(postId: string) {
  return await db
    .update(schema.posts)
    .set({ viewCount: schema.posts.viewCount + 1 })
    .where(eq(schema.posts.id, postId))
    .returning({ updatedViews: schema.posts.viewCount });
}
```

---

## 3. Prisma: Schema-First DSL & Generated Types

Prisma defines schemas in a centralized `.prisma` file and utilizes a native Rust query engine.

### 3.1 `schema.prisma`
```prisma
datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

generator client {
  provider = "prisma-client-js"
}

model User {
  id        String   @id @default(uuid())
  email     String   @unique
  username  String   @unique
  posts     Post[]
  createdAt DateTime @default(now())
}

model Post {
  id        String   @id @default(uuid())
  title     String
  authorId  String
  author    User     @relation(fields: [authorId], references: [id])
  createdAt DateTime @default(now())
}
```

### 3.2 Querying via Prisma Client
```typescript
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient({
  log: ['warn', 'error'],
});

export async function createPostForUser(userId: string, title: string) {
  return await prisma.post.create({
    data: {
      title,
      author: {
        connect: { id: userId },
      },
    },
    include: {
      author: true, // Eager load relation
    },
  });
}
```

---

## 4. Zero-Downtime Database Migrations: The Expand-and-Contract Pattern

In modern CI/CD pipelines where multiple application server instances are updated in a rolling deployment, altering a column name or dropping a column directly in a single migration causes running instances to crash.

### The 4-Phase Expand-and-Contract Migration Workflow

```text
Phase 1: EXPAND (Database)
├── Add new column (nullable or default value)
└── Old & New columns both exist in DB

Phase 2: DUAL-WRITE (Application Code Deploy)
├── App writes to BOTH Old and New columns
└── App reads from Old column (backward compatible with older pods)

Phase 3: BACKFILL & READ-SWITCH (Database & App Deploy)
├── Backfill historical data from Old column into New column
└── Deploy App to read exclusively from New column

Phase 4: CONTRACT (Database Cleanup)
├── Drop Old column from database schema
└── Remove dual-write logic from application code
```

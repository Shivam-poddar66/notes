# 3) NoSQL and Document Databases: MongoDB and Mongoose

## Executive Overview
MongoDB is a distributed, document-oriented NoSQL database that stores data in flexible, binary JSON (BSON) format. In enterprise Node.js environments, developers interface with MongoDB either via the low-level **MongoDB Native Driver** or the schema-enforcing **Mongoose ODM**.

Achieving peak performance and data consistency requires mastering **Aggregation Pipelines**, **Replica Set Write Concerns & Read Preferences**, the **ESR Indexing Rule**, and **Change Streams**.

---

## 1. Mongoose ODM Architecture & Schemas

Mongoose adds a schema-validation and middleware layer on top of the native MongoDB driver.

```typescript
import mongoose, { Schema, Document, Model } from 'mongoose';

export interface IUser extends Document {
  email: string;
  fullName: string;
  role: 'admin' | 'customer';
  loginCount: number;
  lastLoginAt?: Date;
}

const UserSchema = new Schema<IUser>(
  {
    email: { type: String, required: true, unique: true, lowercase: true, trim: true },
    fullName: { type: String, required: true },
    role: { type: String, enum: ['admin', 'customer'], default: 'customer' },
    loginCount: { type: Number, default: 0 },
    lastLoginAt: { type: Date },
  },
  {
    timestamps: true, // Automatically manages createdAt and updatedAt
  }
);

// Mongoose Pre-Save Middleware Hook
UserSchema.pre('save', function (next) {
  if (this.isModified('email')) {
    console.log(`[AUDIT] User email modified to: ${this.email}`);
  }
  next();
});

// Compound Index following the ESR rule (Role = Equality, CreatedAt = Sort)
UserSchema.index({ role: 1, createdAt: -1 });

export const UserModel: Model<IUser> = mongoose.model<IUser>('User', UserSchema);
```

---

## 2. Aggregation Pipeline Mastery

The MongoDB Aggregation Framework processes documents through an ordered multi-stage data transformation pipeline:

```text
[Collection: Orders]
        │
        ▼ Stage 1: $match (Filter status: 'COMPLETED' using Index)
[Filtered Orders]
        │
        ▼ Stage 2: $unwind (Deconstruct items array)
[Individual Order Items]
        │
        ▼ Stage 3: $group (Group by productId, calculate total revenue & quantity)
[Aggregated Product Revenue]
        │
        ▼ Stage 4: $lookup (Left outer join with Products collection)
[Joined Product Details]
        │
        ▼ Stage 5: $sort (Sort by totalRevenue descending)
[Final Analytics Result]
```

```typescript
import { OrderModel } from './models/order';

export async function getTopSellingProductsAnalytics(startDate: Date, limit = 10) {
  return await OrderModel.aggregate([
    // Stage 1: Filter completed orders in date range (Index scan)
    {
      $match: {
        status: 'COMPLETED',
        createdAt: { $gte: startDate },
      },
    },
    // Stage 2: Deconstruct items array into separate documents
    {
      $unwind: '$items',
    },
    // Stage 3: Group by item productId and accumulate revenue
    {
      $group: {
        _id: '$items.productId',
        totalUnitsSold: { $sum: '$items.quantity' },
        totalRevenueUsd: { $sum: { $multiply: ['$items.quantity', '$items.unitPrice'] } },
      },
    },
    // Stage 4: Join with 'products' collection
    {
      $lookup: {
        from: 'products',
        localField: '_id',
        foreignField: '_id',
        as: 'productDetails',
      },
    },
    // Stage 5: Flatten productDetails array
    {
      $unwind: '$productDetails',
    },
    // Stage 6: Project structured response shape
    {
      $project: {
        _id: 0,
        productId: '$_id',
        productName: '$productDetails.name',
        sku: '$productDetails.sku',
        totalUnitsSold: 1,
        totalRevenueUsd: 1,
      },
    },
    // Stage 7: Sort by revenue descending
    {
      $sort: { totalRevenueUsd: -1 },
    },
    // Stage 8: Limit output count
    {
      $limit: limit,
    },
  ]);
}
```

---

## 3. High-Performance Indexing: The ESR Rule

When designing compound indexes in MongoDB, always order index fields by:
1. **E - Equality**: Exact match fields (`status: 'ACTIVE'`, `tenantId: '123'`).
2. **S - Sort**: Fields used in `.sort({ createdAt: -1 })`.
3. **R - Range**: Comparison filter fields (`age: { $gte: 21 }`, `price: { $lt: 100 }`).

```typescript
// Query:
// db.orders.find({ tenantId: 'T1', price: { $gte: 50 } }).sort({ createdAt: -1 })

// OPTIMAL INDEX (ESR):
// { tenantId: 1, createdAt: -1, price: 1 }
//   (Equality)    (Sort)          (Range)
```

---

## 4. Replica Sets: Write Concerns & Read Preferences

In a MongoDB Replica Set (Primary + Secondary nodes):

### 4.1 Write Concerns (`w` & `j`)
- **`w: 1`**: Write is acknowledged as soon as written to memory on the Primary node. (Fast, but risk of data loss if Primary crashes before replication).
- **`w: "majority"`**: Write is acknowledged only after being written to a majority of replica set nodes. (Standard for high durability).
- **`j: true`**: Write is flushed to the on-disk journal before acknowledgment.

```typescript
// Critical Financial Transaction Write:
await UserModel.updateOne(
  { _id: userId },
  { $inc: { balance: -100 } },
  { writeConcern: { w: 'majority', j: true, wtimeout: 5000 } }
);
```

### 4.2 Read Preferences
- **`primary` (Default)**: Reads always routed to Primary node (guarantees strong read consistency).
- **`secondaryPreferred`**: Reads routed to Secondary replicas when available (reduces load on Primary for read-heavy reporting).

---

## 5. Reactive Event Streaming with Change Streams

MongoDB **Change Streams** allow applications to listen to real-time data changes at the collection, database, or cluster level backed by the MongoDB replication oplog:

```typescript
import { UserModel } from './models/user';

export function startUserChangeWatcher() {
  // Listen only to update operations where loginCount changed
  const changeStream = UserModel.watch([
    {
      $match: {
        operationType: 'update',
        'updateDescription.updatedFields.loginCount': { $exists: true },
      },
    },
  ]);

  changeStream.on('change', (change) => {
    console.log('[CHANGE STREAM] User login count updated:', change.documentKey._id);
  });

  changeStream.on('error', (err) => {
    console.error('[CHANGE STREAM ERROR]', err);
  });
}
```

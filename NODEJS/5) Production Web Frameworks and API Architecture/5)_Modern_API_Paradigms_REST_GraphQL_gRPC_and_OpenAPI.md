# 5) Modern API Paradigms: REST, GraphQL, gRPC, and OpenAPI

## Executive Overview
Modern distributed backends rarely rely on a single communication protocol. An enterprise architecture typically utilizes **REST** for public-facing developer APIs, **GraphQL** for dynamic client UI queries, and **gRPC / Protocol Buffers** for ultra-low-latency inter-service microservice communication.

Understanding the tradeoffs, wire formats, serialization costs, and concurrency characteristics of each paradigm is essential for senior backend system design.

---

## 1. Paradigm Comparison: REST vs GraphQL vs gRPC

```text
┌──────────────┬──────────────────┬─────────────────┬──────────────────┬──────────────────┐
│ Paradigm     │ Transport / Wire │ Data Fetching   │ Schema / Typing  │ Best Suited For  │
├──────────────┼──────────────────┼─────────────────┼──────────────────┼──────────────────┤
│ REST         │ HTTP/1.1 or 2    │ Fixed endpoint  │ OpenAPI /        │ Public APIs,     │
│              │ Text / JSON      │ per resource    │ JSON Schema      │ Webhooks, CRUD   │
├──────────────┼──────────────────┼─────────────────┼──────────────────┼──────────────────┤
│ GraphQL      │ HTTP/1.1 or 2    │ Declarative     │ GraphQL SDL /    │ Mobile apps,     │
│              │ Text / JSON      │ client query    │ Strict Type AST  │ complex data UIs │
├──────────────┼──────────────────┼─────────────────┼──────────────────┼──────────────────┤
│ gRPC         │ HTTP/2 only      │ Remote RPC      │ Protocol Buffers │ Low-latency      │
│              │ Binary (Protobuf)│ procedure call  │ (.proto files)   │ microservices    │
└──────────────┴──────────────────┴─────────────────┴──────────────────┴──────────────────┘
```

---

## 2. RESTful Architecture & RFC 7807 Standard

### 2.1 HTTP Verbs & Idempotency Rules
- **Idempotent**: Performing the same operation multiple times yields the exact same server state (`GET`, `PUT`, `DELETE`, `HEAD`, `OPTIONS`).
- **Non-Idempotent**: Each invocation may create new state or side effects (`POST`, `PATCH`).

### 2.2 Standardized Error Responses (RFC 7807 Problem Details)
Instead of returning ad-hoc error formats, enterprise REST APIs adhere to **RFC 7807**:

```json
{
  "type": "https://api.example.com/errors/insufficient-funds",
  "title": "Insufficient Funds",
  "status": 422,
  "detail": "Your account balance of $12.50 is lower than the transfer amount $50.00",
  "instance": "/accounts/acc_8819/transfers/txn_002",
  "invalidParams": [
    { "name": "amount", "reason": "Exceeds available balance" }
  ]
}
```

---

## 3. GraphQL & Solving the $N+1$ Query Problem with `DataLoader`

In GraphQL, nested resolvers execute independently. For example, fetching 50 users and their posts leads to 1 SQL query for users and 50 separate SQL queries for posts (**51 queries = $N+1$ problem**).

### 3.1 The Solution: `DataLoader` Batching & Caching
`DataLoader` uses Node.js `process.nextTick()` to collect all individual IDs requested during a single tick of the event loop and executes a single batched query (`SELECT * FROM posts WHERE userId IN (...)`):

```text
Without DataLoader:
User 1 Resolver ──> SQL: SELECT * FROM posts WHERE userId = 1
User 2 Resolver ──> SQL: SELECT * FROM posts WHERE userId = 2   (50 Queries!)
User 3 Resolver ──> SQL: SELECT * FROM posts WHERE userId = 3

With DataLoader (Event Loop Tick Batching):
User 1, 2, 3... Resolvers ──> [DataLoader Queue] ──> Single SQL: SELECT * FROM posts WHERE userId IN (1, 2, 3...)
```

```typescript
import DataLoader from 'dataloader';

// Batch loading function receives an array of keys and must return an array of results of identical length
export function createPostBatchLoader(db: any) {
  return new DataLoader<string, any[]>(async (userIds: readonly string[]) => {
    console.log(`[DATALOADER] Batching database lookup for ${userIds.length} user IDs`);
    
    const posts = await db.query(
      'SELECT * FROM posts WHERE user_id = ANY($1)',
      [[...userIds]]
    );

    // Group posts by user_id
    const postMap = new Map<string, any[]>();
    userIds.forEach((id) => postMap.set(id, []));
    posts.forEach((post: any) => postMap.get(post.user_id)?.push(post));

    // Return array matching the exact index order of userIds
    return userIds.map((id) => postMap.get(id) || []);
  });
}
```

---

## 4. gRPC & Protocol Buffers (`@grpc/grpc-js`)

gRPC operates on top of HTTP/2 multiplexed binary streams. It provides up to **7x–10x higher serialization throughput** than JSON over HTTP/1.1.

### 4.1 Protocol Buffer Definition (`order.proto`)
```protobuf
syntax = "proto3";

package order;

service OrderService {
  rpc GetOrder (OrderRequest) returns (OrderResponse);
  rpc StreamLiveUpdates (OrderRequest) returns (stream OrderStatusUpdate);
}

message OrderRequest {
  string order_id = 1;
}

message OrderResponse {
  string order_id = 1;
  double amount_usd = 2;
  string status = 3;
}

message OrderStatusUpdate {
  string status = 1;
  int64 timestamp = 2;
}
```

### 4.2 High-Performance gRPC Server Implementation

```typescript
import * as grpc from '@grpc/grpc-js';
import * as protoLoader from '@grpc/proto-loader';
import path from 'node:path';

const PROTO_PATH = path.resolve('order.proto');
const packageDefinition = protoLoader.loadSync(PROTO_PATH, {
  keepCase: true,
  longs: String,
  enums: String,
  defaults: true,
  oneofs: true,
});

const protoDescriptor = grpc.loadPackageDefinition(packageDefinition) as any;
const orderProto = protoDescriptor.order;

const server = new grpc.Server();

// Implement Unary RPC
server.addService(orderProto.OrderService.service, {
  GetOrder: (call: any, callback: any) => {
    const orderId = call.request.order_id;
    console.log(`[gRPC SERVER] Processing GetOrder for: ${orderId}`);

    callback(null, {
      order_id: orderId,
      amount_usd: 149.99,
      status: 'CONFIRMED',
    });
  },

  // Implement Server-Streaming RPC
  StreamLiveUpdates: (call: any) => {
    const orderId = call.request.order_id;
    let count = 0;

    const interval = setInterval(() => {
      count++;
      call.write({
        status: count === 1 ? 'PROCESSING' : count === 2 ? 'SHIPPED' : 'DELIVERED',
        timestamp: Date.now(),
      });

      if (count >= 3) {
        clearInterval(interval);
        call.end(); // Complete stream
      }
    }, 1000);
  },
});

server.bindAsync('0.0.0.0:50051', grpc.ServerCredentials.createInsecure(), (err, port) => {
  if (err) throw err;
  console.log(`[gRPC SERVER] Listening on port: ${port}`);
});
```

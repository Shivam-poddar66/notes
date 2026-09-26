# 6) Hands-On Lab Experiments and Production Architectures

This chapter provides four comprehensive, production-grade lab architectures demonstrating the integration of Fastify, NestJS, GraphQL DataLoaders, and gRPC Protocol Buffers.

---

## Lab 1: Production Fastify Microservice with Auto-Generated OpenAPI Docs

### Architecture
- Input validation via TypeBox JSON Schema.
- Compiled serialization via `fast-json-stringify`.
- Encapsulated health and metric plugins.
- Interactive OpenAPI / Swagger UI documentation at `/documentation`.

```typescript
import Fastify from 'fastify';
import swagger from '@fastify/swagger';
import swaggerUi from '@fastify/swagger-ui';
import { Type, Static } from '@sinclair/typebox';

const server = Fastify({
  logger: {
    level: 'info',
    transport: {
      target: 'pino-pretty',
    },
  },
});

// 1. Register OpenAPI Specification Generator
await server.register(swagger, {
  openapi: {
    info: {
      title: 'Order Processing Microservice API',
      description: 'Production Fastify high-throughput order gateway',
      version: '1.0.0',
    },
  },
});

await server.register(swaggerUi, {
  routePrefix: '/documentation',
});

// 2. Define Schemas
const CreateOrderBody = Type.Object({
  customerId: Type.String({ format: 'uuid' }),
  items: Type.Array(
    Type.Object({
      productId: Type.String(),
      quantity: Type.Integer({ minimum: 1 }),
      unitPrice: Type.Number({ minimum: 0.01 }),
    }),
    { minItems: 1 }
  ),
});

const OrderResponse = Type.Object({
  orderId: Type.String(),
  status: Type.String(),
  totalAmount: Type.Number(),
  createdAt: Type.String(),
});

type CreateOrderBodyType = Static<typeof CreateOrderBody>;
type OrderResponseType = Static<typeof OrderResponse>;

// 3. Register Route with Schema Validation & Fast Serialization
server.post<{ Body: CreateOrderBodyType; Reply: OrderResponseType }>(
  '/api/v1/orders',
  {
    schema: {
      tags: ['Orders'],
      summary: 'Create a new order',
      body: CreateOrderBody,
      response: {
        201: OrderResponse,
      },
    },
  },
  async (request, reply) => {
    const { customerId, items } = request.body;
    const totalAmount = items.reduce((sum, item) => sum + item.quantity * item.unitPrice, 0);

    reply.status(201);
    return {
      orderId: `ORD-${Date.now()}`,
      status: 'CONFIRMED',
      totalAmount,
      createdAt: new Date().toISOString(),
    };
  }
);

// 4. Start Server
const start = async () => {
  try {
    await server.listen({ port: 3000, host: '0.0.0.0' });
    console.log('[FASTIFY] Server running on http://localhost:3000/documentation');
  } catch (err) {
    server.log.error(err);
    process.exit(1);
  }
};

start();
```

---

## Lab 2: Enterprise NestJS Module with Full Request Lifecycle Pipeline

```typescript
import {
  Module,
  Controller,
  Get,
  Post,
  Body,
  UseGuards,
  UseInterceptors,
  Injectable,
  ExecutionContext,
  CanActivate,
  CallHandler,
  NestInterceptor,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { IsNotEmpty, IsNumber, Min } from 'class-validator';

// 1. DTO with Validation Decorators
export class ProcessPaymentDto {
  @IsNotEmpty()
  accountId!: string;

  @IsNumber()
  @Min(1.0)
  amountUsd!: number;
}

// 2. Custom Security Guard
@Injectable()
export class ApiKeyGuard implements CanActivate {
  canActivate(context: ExecutionContext): boolean {
    const request = context.switchToHttp().getRequest();
    const apiKey = request.headers['x-api-key'];
    return apiKey === 'production-secret-token';
  }
}

// 3. Response Transformation Interceptor (Standard Envelope Wrapper)
@Injectable()
export class TransformResponseInterceptor implements NestInterceptor {
  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    return next.handle().pipe(
      map((data) => ({
        success: true,
        data,
        metadata: {
          timestamp: new Date().toISOString(),
        },
      }))
    );
  }
}

// 4. Payment Controller
@Controller('api/v1/payments')
@UseGuards(ApiKeyGuard)
@UseInterceptors(TransformResponseInterceptor)
export class PaymentsController {
  @Post()
  async processPayment(@Body() dto: ProcessPaymentDto) {
    return {
      transactionId: `TX-${Math.floor(Math.random() * 1000000)}`,
      status: 'SETTLED',
      amountCharged: dto.amountUsd,
    };
  }
}

// 5. Root Module
@Module({
  controllers: [PaymentsController],
})
export class PaymentsModule {}
```

---

## Lab 3: High-Performance GraphQL Server with DataLoader Caching

```typescript
import DataLoader from 'dataloader';

// Simulated Mock Database
const MOCK_DB = {
  users: [
    { id: '1', name: 'Alice' },
    { id: '2', name: 'Bob' },
  ],
  posts: [
    { id: '101', authorId: '1', title: 'Deep Dive into Libuv' },
    { id: '102', authorId: '1', title: 'Mastering Node.js Streams' },
    { id: '103', authorId: '2', title: 'Fastify vs Express Benchmark' },
  ],
};

// 1. Create Per-Request DataLoader Factory
export function createDataLoaders() {
  return {
    postsByAuthorLoader: new DataLoader<string, any[]>(async (authorIds) => {
      console.log(`[SQL BATCH QUERY] SELECT * FROM posts WHERE author_id IN (${authorIds.join(',')})`);
      
      const allMatchingPosts = MOCK_DB.posts.filter((p) => authorIds.includes(p.authorId));
      
      return authorIds.map((authorId) =>
        allMatchingPosts.filter((post) => post.authorId === authorId)
      );
    }),
  };
}

// 2. GraphQL Resolvers
export const resolvers = {
  Query: {
    users: () => MOCK_DB.users,
  },
  User: {
    // Nested field resolver uses DataLoader to batch queries across all returned users in a single event loop tick
    posts: (parentUser: { id: string }, args: any, context: { loaders: ReturnType<typeof createDataLoaders> }) => {
      return context.loaders.postsByAuthorLoader.load(parentUser.id);
    },
  },
};
```

---

## Lab 4: End-to-End gRPC Client and Server with Protocol Buffers

```typescript
import * as grpc from '@grpc/grpc-js';
import * as protoLoader from '@grpc/proto-loader';

// Client implementation to communicate with gRPC server
export function createGrpcClient(serverAddress = 'localhost:50051') {
  const packageDef = protoLoader.loadSync('order.proto', {
    keepCase: true,
    longs: String,
    enums: String,
    defaults: true,
    oneofs: true,
  });

  const orderProto = (grpc.loadPackageDefinition(packageDef) as any).order;
  const client = new orderProto.OrderService(
    serverAddress,
    grpc.credentials.createInsecure()
  );

  return {
    // 1. Call Unary RPC
    getOrder: (orderId: string): Promise<any> => {
      return new Promise((resolve, reject) => {
        client.GetOrder({ order_id: orderId }, (err: any, response: any) => {
          if (err) reject(err);
          else resolve(response);
        });
      });
    },

    // 2. Listen to Server-Streaming RPC
    subscribeLiveUpdates: (orderId: string, onUpdate: (data: any) => void) => {
      const call = client.StreamLiveUpdates({ order_id: orderId });
      call.on('data', onUpdate);
      call.on('end', () => console.log('[gRPC CLIENT] Stream ended by server.'));
      call.on('error', (err: any) => console.error('[gRPC CLIENT] Error:', err));
    },
  };
}
```

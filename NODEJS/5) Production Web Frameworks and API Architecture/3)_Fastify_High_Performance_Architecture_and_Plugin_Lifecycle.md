# 3) Fastify High-Performance Architecture and Plugin Lifecycle

## Executive Overview
Fastify is a high-performance web framework designed specifically for minimal overhead and developer ergonomics. Through its **Radix Trie router (`find-my-way`)**, **ahead-of-time JSON schema serialization (`fast-json-stringify`)**, and **strict plugin encapsulation model**, Fastify can achieve up to 75,000+ requests per second on modern multi-core machines.

---

## 1. Fastify High-Performance Pillars

```text
┌────────────────────────────────────────────────────────────────────────┐
│                        Fastify Core Engine                             │
├────────────────────────┬───────────────────────┬───────────────────────┤
│   find-my-way Router   │     Ajv Validator     │  fast-json-stringify  │
│  - Radix Trie structure│  - High-speed JSON    │  - Pre-compiles JSON  │
│  - O(k) matching time  │    Schema validator   │    serialization code │
│  - Zero regex overhead │  - Coerces & sanitizes│  - Avoids dynamic V8  │
│                        │    request payloads   │    property traversal │
└────────────────────────┴───────────────────────┴───────────────────────┘
```

---

## 2. Schema-Driven Architecture & Validation

In Fastify, schemas are not an afterthought—they define both **input validation** and **output serialization**:

```typescript
import Fastify from 'fastify';
import { Type, Static } from '@sinclair/typebox';

const fastify = Fastify({ logger: true });

// Define TypeBox Schemas (Compiles to JSON Schema + generates TypeScript types)
const UserParamsSchema = Type.Object({
  userId: Type.String({ format: 'uuid' }),
});

const UserResponseSchema = Type.Object({
  id: Type.String(),
  username: Type.String(),
  email: Type.String({ format: 'email' }),
  role: Type.Union([Type.Literal('admin'), Type.Literal('user')]),
});

type UserParams = Static<typeof UserParamsSchema>;
type UserResponse = Static<typeof UserResponseSchema>;

fastify.get<{ Params: UserParams; Reply: UserResponse }>(
  '/api/v1/users/:userId',
  {
    schema: {
      params: UserParamsSchema,
      response: {
        200: UserResponseSchema, // Fastify uses this schema to pre-compile serialization!
      },
    },
  },
  async (request, reply) => {
    const { userId } = request.params; // Fully type-safe and validated!
    
    // Any extra properties on this object NOT in UserResponseSchema will be automatically stripped!
    return {
      id: userId,
      username: 'johndoe',
      email: 'john@example.com',
      role: 'admin',
    };
  }
);
```

---

## 3. Plugin Encapsulation Model

Fastify organizes applications into a **directed acyclic graph (DAG)** of plugins. By default, every plugin creates an **encapsulated child scope**:
- Decorators, hooks, and middlewares registered in a child plugin **do not leak** into parent or sibling plugins.

```text
Root Fastify Instance (Global Scope)
  │
  ├── Plugin A (/api/v1/auth) ──> Auth Hooks ONLY apply here
  │
  └── Plugin B (/api/v1/public) ──> Public endpoints (No auth hooks)
```

### 3.1 Breaking Encapsulation with `fastify-plugin` (`fp`)
When you want to share a plugin globally (e.g. Database Connection, Redis Client, Authentication Utility), wrap the plugin in `fastify-plugin`:

```typescript
import fp from 'fastify-plugin';
import { FastifyInstance, FastifyPluginAsync } from 'fastify';

// 1. Declare TypeScript decorator type extension
declare module 'fastify' {
  interface FastifyInstance {
    db: { query: (sql: string) => Promise<any[]> };
  }
}

const databasePlugin: FastifyPluginAsync = async (fastify: FastifyInstance) => {
  const dbClient = {
    query: async (sql: string) => [{ id: 1, name: 'Sample Record' }],
  };

  // Decorate the root fastify instance
  fastify.decorate('db', dbClient);
};

// fp() instructs Fastify NOT to create an encapsulated sub-scope
export default fp(databasePlugin, {
  name: 'custom-database-plugin',
});
```

---

## 4. The 7-Stage Fastify Lifecycle Hooks

Fastify executes requests through a strict, deterministic sequence of lifecycle hooks:

```text
Incoming Request
   │
   ▼
1. onRequest ────────────> Authentication / Early Request ID assignment
   │
   ▼
2. preParsing ───────────> Inspect / alter raw stream before body parsing
   │
   ▼
3. preValidation ────────> Alter request body before Ajv schema validation
   │
   ▼
[Ajv Validation] ────────> Validates headers, params, query, body against schema
   │
   ▼
4. preHandler ───────────> Authorization / Role checks / Business pre-checks
   │
   ▼
[Route Handler] ─────────> Executes controller logic & returns result
   │
   ▼
5. preSerialization ────> Transform or format outgoing payload before JSON stringify
   │
   ▼
[fast-json-stringify] ───> Pre-compiled schema serialization to Buffer
   │
   ▼
6. onResponse ───────────> Metrics, telemetry, access logging (After bytes sent)
   │
   ▼
(In Case of Error) ─────> 7. onError Hook ──> Global Error Handler
```

### Hook Implementation Example:

```typescript
fastify.addHook('onRequest', async (request, reply) => {
  request.log.info({ url: request.raw.url }, 'Incoming request received');
});

fastify.addHook('preHandler', async (request, reply) => {
  // Authorization check
  const apiKey = request.headers['x-api-key'];
  if (!apiKey) {
    reply.status(401).send({ error: 'Missing API Key' });
  }
});
```

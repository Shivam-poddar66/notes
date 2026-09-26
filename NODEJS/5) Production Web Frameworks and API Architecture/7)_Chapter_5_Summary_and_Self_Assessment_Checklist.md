# 7) Chapter 5 Summary and Self-Assessment Checklist

## Chapter 5 Quick Revision Summary

### 1. Web Framework Tradeoffs
- **Express.js**: Linear middleware execution, dynamic regex routing via `path-to-regexp`, dynamic `JSON.stringify()` serialization. Ideal for ubiquitous compatibility.
- **Fastify**: $O(k)$ Radix Trie routing (`find-my-way`), pre-compiled JSON serialization (`fast-json-stringify`), JSON Schema validation (`Ajv`), strict plugin encapsulation. Ideal for maximum throughput.
- **NestJS**: Enterprise Inversion of Control (IoC) and Dependency Injection (DI), modular architecture, 6-stage request pipeline (Middleware -> Guards -> Interceptors -> Pipes -> Handler -> Filters). Ideal for large multi-team enterprise systems.
- **Hono**: Zero-dependency, ultra-lightweight multi-runtime framework ideal for Edge (Cloudflare Workers, Deno, Lambda).

### 2. Express.js Mechanics
- **Middleware Arity**: Express uses `fn.length === 4` to identify error-handling middleware (`(err, req, res, next)`).
- **Async Errors**: Express 4 requires catching rejections manually (`asyncHandler`); Express 5 catches rejected Promises natively.

### 3. Fastify Architecture
- **Plugin Encapsulation**: Plugins create child contexts by default. Use `fastify-plugin` (`fp`) to share decorators and global hooks.
- **Lifecycle Sequence**: `onRequest` -> `preParsing` -> `preValidation` -> `preHandler` -> `preSerialization` -> `onError` -> `onResponse`.

### 4. API Paradigms
- **REST**: Idempotent verbs (`GET`, `PUT`, `DELETE`), RFC 7807 Problem Details error schema.
- **GraphQL**: Declarative client queries; solve the $N+1$ database query bottleneck using `DataLoader` batching and caching.
- **gRPC**: HTTP/2 binary transport with Protocol Buffers (`.proto`), providing high throughput, type safety, and streaming RPCs.

---

## Self-Assessment Challenge Questions

### Q1: Why is Fastify's `fast-json-stringify` significantly faster than standard `JSON.stringify()`?
**Answer**: Standard `JSON.stringify()` uses V8 runtime reflection to dynamically inspect every key, type, and prototype property of an object on every call. `fast-json-stringify` compiles the defined JSON Schema ahead of time into a static string concatenation function (e.g. `'{"id":' + obj.id + ',"name":"' + obj.name + '"}'`), eliminating dynamic object inspection and V8 GC overhead.

### Q2: Why must `DataLoader` instances in GraphQL be created per-request rather than as a global singleton?
**Answer**: A global `DataLoader` singleton would share its internal cache across all users and requests, causing severe security data leaks (User A seeing cached data belonging to User B) and stale data bugs. Creating loaders inside the per-request GraphQL `context` ensures cache isolation while still batching queries within that specific request lifecycle.

### Q3: What is the exact execution order of NestJS request pipeline components?
**Answer**:
1. Middleware (Global -> Module)
2. Guards (`CanActivate`)
3. Interceptors (Pre-controller logic)
4. Pipes (`ValidationPipe` / transformation)
5. Controller Route Handler (Business logic)
6. Interceptors (Post-controller logic / RxJS operators)
7. Exception Filters (Triggered on any thrown exception)

### Q4: How does `find-my-way` (Fastify's router) outperform Express's router for large APIs?
**Answer**: Express stores routes in a linear array and tests each route regex sequentially ($O(N)$ where $N$ is number of routes). `find-my-way` compiles routes into a Radix Trie (prefix tree), matching paths in $O(k)$ time where $k$ is the character length of the URL, completely independent of how many thousands of routes are registered.

### Q5: When should you choose gRPC over REST for backend service communication?
**Answer**: Choose gRPC for internal inter-service (microservice-to-microservice) communication where low latency, compact binary payload size, bidirectional streaming, and strict cross-language schema contracts (.proto) are required. Choose REST for public APIs, browser clients, and external third-party developer integrations.

---

## Chapter 5 Mastery Verification Checklist

Check off each item once you can explain or implement it with confidence:

- [ ] Compare architectural tradeoffs between Express, Fastify, NestJS, and Hono.
- [ ] Explain how Express uses `fn.length === 4` to route unhandled errors.
- [ ] Handle asynchronous errors safely across Express 4 and Express 5.
- [ ] Implement schema validation and fast serialization in Fastify using TypeBox / JSON Schema.
- [ ] Manage Fastify plugin encapsulation boundaries and global sharing with `fastify-plugin`.
- [ ] Trace all 7 lifecycle hooks in the Fastify request-response pipeline.
- [ ] Architect a NestJS modular application utilizing Providers, Controllers, and Modules.
- [ ] Implement NestJS Guards for Role-Based Access Control (RBAC).
- [ ] Implement NestJS Interceptors for execution timing and response transformation using RxJS.
- [ ] Validate request DTOs using `class-validator` and `ValidationPipe`.
- [ ] Design RESTful APIs adhering to HTTP idempotency semantics and RFC 7807 Problem Details.
- [ ] Diagnose and eliminate the $N+1$ query problem in GraphQL using `DataLoader`.
- [ ] Define Protocol Buffer service contracts (`.proto`) and generate gRPC stubs.
- [ ] Build a gRPC microservice server and client in Node.js supporting streaming RPCs.
- [ ] Automate OpenAPI / Swagger documentation from code schemas.

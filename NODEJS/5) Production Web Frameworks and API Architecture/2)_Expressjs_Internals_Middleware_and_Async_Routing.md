# 2) Express.js Internals, Middleware, and Async Routing

## Executive Overview
Express.js remains the most widely deployed Node.js web framework. Its architecture is built entirely around an asynchronous linear pipeline of **Middleware Layers**. Understanding how Express resolves routes, dispatches execution through `next()`, checks function arity for error handlers, and handles Promise rejections is fundamental for maintaining production codebases.

---

## 1. Express.js Internal Routing Architecture

Internally, Express maintains a single `Router` instance containing an array of **`Layer`** objects (`router.stack`).

```text
Incoming HTTP Request (GET /api/v1/users/42)
   │
   ▼
[Express Router Stack]
   ├── Layer 1: Global Logger Middleware (path: '/')
   ├── Layer 2: Body Parser Middleware (path: '/')
   ├── Layer 3: Sub-Router (path: '/api/v1')
   │      └── Layer 3.1: Auth Middleware
   │      └── Layer 3.2: Route (GET /users/:id)
   │             ├── Route Layer: Param Validation
   │             └── Route Layer: User Controller
   └── Layer 4: Global Error Handler (4 arguments)
```

Each `Layer` contains:
- `path`: The path pattern (compiled into a regular expression via `path-to-regexp`).
- `handle`: The middleware callback function `(req, res, next)`.
- `route`: If the layer is a route endpoint, points to a `Route` object containing its own internal stack of method handlers.

---

## 2. Middleware Chain Mechanics & `next()`

When a request arrives, Express executes matching layers in sequential order:

```typescript
import express, { Request, Response, NextFunction } from 'express';

const app = express();

// 1. Standard Middleware
app.use((req: Request, res: Response, next: NextFunction) => {
  req.headers['x-request-start'] = Date.now().toString();
  // Pass execution to next layer in the stack
  next();
});

// 2. Short-Circuiting Middleware
app.use((req: Request, res: Response, next: NextFunction) => {
  const isBlocked = req.headers['x-blocked'] === 'true';
  if (isBlocked) {
    // Terminate request early without calling next()
    res.status(403).json({ error: 'Access Forbidden' });
    return;
  }
  next();
});
```

---

## 3. The 4-Argument Error Handler & Function Arity

Express uses JavaScript's `Function.prototype.length` (function arity) to distinguish between regular request middleware and error-handling middleware:

```javascript
// Express internal check:
if (layer.handle.length === 4) {
  // It's an error handler: fn(err, req, res, next)
} else {
  // It's a standard middleware: fn(req, res, next)
}
```

> [!CAUTION]
> If you omit the unused `next` parameter from an error handler (e.g. `(err, req, res) => {}`), its `.length` will be `3`. Express will treat it as standard middleware, causing unhandled errors to bypass your error handler completely!

```typescript
import { Request, Response, NextFunction } from 'express';

// CORRECT: Exactly 4 parameters defined
export function globalErrorHandler(
  err: any,
  req: Request,
  res: Response,
  next: NextFunction
) {
  const statusCode = err.statusCode || 500;
  console.error(`[ERROR] ${req.method} ${req.url} - ${err.message}`, err.stack);

  res.status(statusCode).json({
    success: false,
    error: {
      code: err.code || 'INTERNAL_SERVER_ERROR',
      message: err.message || 'An unexpected error occurred',
    },
  });
}
```

---

## 4. Asynchronous Error Handling: Express 4 vs Express 5

### 4.1 The Express 4 Unhandled Rejection Hazard
In Express 4, route handlers returning rejected Promises **do not pass errors to `next(err)` automatically**. Unhandled rejections stall the HTTP request until socket timeout or crash the process:

```typescript
// DANGEROUS IN EXPRESS 4: Crashes or hangs without try/catch
app.get('/users/:id', async (req, res, next) => {
  const user = await database.findUser(req.params.id); // If this rejects, request HANGS!
  res.json(user);
});

// Express 4 Workaround: Manual wrapper or express-async-errors
const asyncHandler = (fn: any) => (req: Request, res: Response, next: NextFunction) => {
  Promise.resolve(fn(req, res, next)).catch(next);
};

app.get('/users/:id', asyncHandler(async (req: Request, res: Response) => {
  const user = await database.findUser(req.params.id);
  res.json(user);
}));
```

### 4.2 Native Async Support in Express 5
Express 5 natively catches rejected Promises returned from async middleware and route handlers, forwarding them directly to error handlers without boilerplate wrappers.

---

## 5. Production Express Architectural Pattern

```typescript
import express, { Request, Response, NextFunction } from 'express';
import crypto from 'node:crypto';

const app = express();

// 1. Request ID Tracking Middleware
app.use((req: Request, res: Response, next: NextFunction) => {
  const requestId = (req.headers['x-request-id'] as string) || crypto.randomUUID();
  req.headers['x-request-id'] = requestId;
  res.setHeader('x-request-id', requestId);
  next();
});

// 2. Structured JSON Parsing with payload limit
app.use(express.json({ limit: '1mb' }));

// 3. User Router with Domain Logic
const userRouter = express.Router();
userRouter.get('/:id', async (req: Request, res: Response) => {
  const { id } = req.params;
  // Simulated lookup
  res.json({ id, name: 'Alice Smith', tier: 'premium' });
});
app.use('/api/v1/users', userRouter);

// 4. 404 Not Found Catch-all
app.use((req: Request, res: Response) => {
  res.status(404).json({ error: 'Route Not Found', path: req.originalUrl });
});

// 5. Centralized Error Handler (4 arguments)
app.use(globalErrorHandler);

export default app;
```

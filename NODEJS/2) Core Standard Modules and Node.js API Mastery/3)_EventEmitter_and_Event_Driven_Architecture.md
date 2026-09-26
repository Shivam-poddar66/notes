# 3) EventEmitter and Event-Driven Architecture

## Executive Overview
Node.js is designed from the ground up around an asynchronous event-driven architecture. The core `node:events` module provides the `EventEmitter` class, which forms the foundational backbone of nearly every standard I/O library in Node.js, including `net.Socket`, `http.Server`, `fs.ReadStream`, and `process`.

Understanding the internal synchronous dispatch mechanics of `EventEmitter`, memory leak thresholds, error channel bubbling, and modern Promise/AsyncIterator integrations is essential for architecting resilient backend systems.

---

## 1. EventEmitter Internal Dispatch Mechanics

A common misconception is that `EventEmitter.emit()` executes asynchronously. In reality, **`EventEmitter.emit()` invokes all registered listener callbacks synchronously in the order they were registered.**

```text
EventEmitter.emit('event', data)
   │
   ▼
[Synchronous Execution Loop]
   ├── Listener 1(data)  <── Executes immediately on Call Stack
   ├── Listener 2(data)  <── Executes immediately on Call Stack
   └── Listener 3(data)  <── Executes immediately on Call Stack
   │
   ▼
Returns boolean (true if listeners were called, false otherwise)
```

```typescript
import { EventEmitter } from 'node:events';

const emitter = new EventEmitter();

emitter.on('ping', () => console.log('1. First listener (Sync)'));
emitter.on('ping', () => console.log('2. Second listener (Sync)'));

console.log('START');
emitter.emit('ping');
console.log('END');

/*
Output:
START
1. First listener (Sync)
2. Second listener (Sync)
END
*/
```

> [!NOTE]
> If a listener needs to execute asynchronously, it must explicitly defer its internal work using `setImmediate()`, `process.nextTick()`, or an `async` function.

---

## 2. Core API & Listener Registration

| Method | Description |
|---|---|
| `.on(eventName, listener)` | Appends listener to end of listener array for given event. |
| `.once(eventName, listener)` | Registers a one-time listener that auto-unregisters after first execution. |
| `.prependListener(eventName, listener)` | Inserts listener at the *beginning* of the listener array. |
| `.prependOnceListener(eventName, listener)` | Inserts one-time listener at the beginning of the listener array. |
| `.removeListener(eventName, listener)` / `.off()` | Removes specific registered callback instance. |
| `.removeAllListeners([eventName])` | Removes all listeners for all or specific event. |
| `.listenerCount(eventName)` | Returns the number of active listeners for an event. |

---

## 3. The Special `'error'` Event Convention

The `EventEmitter` class treats `'error'` events as a special case in the runtime:
- If an `'error'` event is emitted and has **at least one listener**, the listener executes normally.
- If an `'error'` event is emitted and has **zero listeners**, Node.js throws the unhandled error, printing the stack trace and **terminating the process**.

```typescript
import { EventEmitter } from 'node:events';

const service = new EventEmitter();

// Unhandled error event crashes the application:
// service.emit('error', new Error('Database connection failed!')); // CRASH!

// Defensive pattern: Always register an error listener
service.on('error', (err) => {
  console.error('[SERVICE ERROR HANDLER] Caught error gracefully:', err.message);
});

service.emit('error', new Error('Database connection failed!')); // Handled safely!
```

---

## 4. Memory Leak Detection & `MaxListenersExceededWarning`

By default, an `EventEmitter` will emit a `MaxListenersExceededWarning` to `process.stderr` if more than **10 listeners** are added for a single event. This warning is designed to catch memory leaks caused by repeatedly attaching listeners without cleaning them up (e.g. inside a recurring HTTP request loop).

```text
(node:12345) MaxListenersExceededWarning: Possible EventEmitter memory leak detected.
11 data listeners added to [CustomStream]. Use emitter.setMaxListeners() to increase limit
```

### 4.1 Raising or Disabling Limits
```typescript
import { EventEmitter } from 'node:events';

const emitter = new EventEmitter();

// Set for specific instance
emitter.setMaxListeners(50);

// Set globally across all new EventEmitter instances
EventEmitter.defaultMaxListeners = 25;
```

### 4.2 Modern Listener Teardown with `AbortSignal`
Modern Node.js (`v15+`) allows passing an `AbortSignal` to automatically detach listeners when an operation is cancelled:

```typescript
import { EventEmitter } from 'node:events';

const emitter = new EventEmitter();
const ac = new AbortController();

emitter.on('taskCompleted', (data) => {
  console.log('Task complete:', data);
}, { signal: ac.signal });

// Triggering abort removes the event listener automatically
ac.abort();
console.log('Listeners count:', emitter.listenerCount('taskCompleted')); // 0
```

---

## 5. Modern Asynchronous Integration

### 5.1 `events.once()` Promise Wrapper
Converts any single event emission into a native `Promise`:

```typescript
import { once, EventEmitter } from 'node:events';

async function waitForServerReady(serverEmitter: EventEmitter) {
  try {
    // Waits until 'ready' fires, or rejects if 'error' fires first
    const [payload] = await once(serverEmitter, 'ready', { signal: AbortSignal.timeout(5000) });
    console.log('Server is ready with configuration:', payload);
  } catch (err: any) {
    console.error('Server failed to start in time:', err.message);
  }
}
```

### 5.2 `events.on()` Async Iterator
Turns an event stream into an asynchronous iterable loop (`for await ... of`):

```typescript
import { on, EventEmitter } from 'node:events';

const chatRoom = new EventEmitter();

async function startMessageConsumer(ac: AbortController) {
  try {
    // Iterates asynchronously every time 'message' event is emitted
    for await (const [message] of on(chatRoom, 'message', { signal: ac.signal })) {
      console.log('New Message Received:', message);
    }
  } catch (err: any) {
    if (err.name === 'AbortError') {
      console.log('Message consumer stopped.');
    }
  }
}
```

---

## 6. Type-Safe Custom EventEmitter in TypeScript

In enterprise applications, raw string event names lead to typos and runtime bugs. Below is the production-standard strongly typed `TypedEventEmitter` pattern:

```typescript
import { EventEmitter } from 'node:events';

// Define domain events and their expected payload signatures
interface OrderEventMap {
  orderCreated: [orderId: string, amount: number];
  orderShipped: [orderId: string, trackingNumber: string];
  orderFailed: [orderId: string, reason: Error];
}

export class OrderService extends EventEmitter {
  // Strongly typed override for .on()
  override on<K extends keyof OrderEventMap>(
    event: K,
    listener: (...args: OrderEventMap[K]) => void
  ): this {
    return super.on(event, listener as (...args: any[]) => void);
  }

  // Strongly typed override for .emit()
  override emit<K extends keyof OrderEventMap>(
    event: K,
    ...args: OrderEventMap[K]
  ): boolean {
    return super.emit(event, ...args);
  }

  public createOrder(id: string, amount: number) {
    // Business logic...
    this.emit('orderCreated', id, amount);
  }

  public shipOrder(id: string, tracking: string) {
    // Business logic...
    this.emit('orderShipped', id, tracking);
  }
}

// Usage Example
const orderService = new OrderService();

orderService.on('orderCreated', (orderId, amount) => {
  // Types are fully inferred: orderId is string, amount is number
  console.log(`Order ${orderId} created for $${amount}`);
});

orderService.createOrder('ORD-9901', 250.00);
```

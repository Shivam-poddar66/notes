# Mini-Project 1: Custom Production-Grade Event Emitter

---

## 1) Project Overview & Architectural Goal

The **Publish-Subscribe (Pub/Sub) / Observer Pattern** is one of the most fundamental design patterns in software engineering. It powers Node.js (`EventEmitter`), the browser DOM (`EventTarget`), microservices message brokers, and modern state management libraries.

### What You Master in this Project:
- **Function References & Memory Identity**: Managing callback references in memory.
- **Closures**: Encapsulating self-removing wrappers in `once()`.
- **Data Structures**: Using `Map` and `Set` for $O(1)$ lookups and duplicate prevention.
- **Safe Iteration**: Preventing array mutation bugs when listeners unsubscribe during an active `emit()`.
- **Memory Leak Protection**: Implementing listener thresholds and teardown functions.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      EVENT EMITTER ARCHITECTURE                         │
│                                                                         │
│   Event Registry: Map<string, Set<Function>>                            │
│                                                                         │
│   "user:login" ──▶ Set { [fn: sendWelcomeEmail], [fn: logAuditTrail] }  │
│   "cart:update"──▶ Set { [fn: recalculateTotal], [fn: syncToStorage] }  │
│                                                                         │
│   [ emit("user:login", payload) ]                                       │
│          │                                                              │
│          ├──▶ Calls sendWelcomeEmail(payload)                           │
│          └──▶ Calls logAuditTrail(payload)                              │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Complete Production-Grade Implementation

```js
/**
 * Production-Grade Custom Event Emitter
 */
class CustomEventEmitter {
  #events = new Map();
  #maxListeners = 10;

  constructor(maxListeners = 10) {
    this.#maxListeners = maxListeners;
  }

  /**
   * Set maximum listener threshold to detect memory leaks
   */
  setMaxListeners(n) {
    if (typeof n !== "number" || n < 0) {
      throw new TypeError("maxListeners must be a non-negative number");
    }
    this.#maxListeners = n;
  }

  /**
   * Subscribe a listener to an event.
   * Returns an unsubscribe teardown function for easy cleanup.
   */
  on(event, listener) {
    this.#validateArguments(event, listener);

    if (!this.#events.has(event)) {
      this.#events.set(event, new Set());
    }

    const listeners = this.#events.get(event);

    // Memory Leak Warning (Node.js style)
    if (listeners.size >= this.#maxListeners) {
      console.warn(
        `[MaxListenersExceededWarning] Possible EventTarget memory leak detected. ` +
        `${listeners.size + 1} "${event}" listeners added. Use setMaxListeners() to increase limit.`
      );
    }

    listeners.add(listener);

    // Return auto-unsubscribe callback
    return () => this.off(event, listener);
  }

  /**
   * Subscribe a one-time listener that automatically unsubscribes after 1 execution.
   */
  once(event, listener) {
    this.#validateArguments(event, listener);

    const onceWrapper = (...args) => {
      this.off(event, onceWrapper);
      listener.apply(this, args);
    };

    // Store reference to original listener so .off(event, listener) works!
    onceWrapper.originalListener = listener;

    this.on(event, onceWrapper);

    return () => this.off(event, onceWrapper);
  }

  /**
   * Remove a specific listener from an event.
   */
  off(event, listener) {
    this.#validateArguments(event, listener);

    if (!this.#events.has(event)) return this;

    const listeners = this.#events.get(event);

    // Match either the direct listener or the wrapper's originalListener
    for (const item of listeners) {
      if (item === listener || item.originalListener === listener) {
        listeners.delete(item);
        break;
      }
    }

    // Clean up map key if no listeners remain
    if (listeners.size === 0) {
      this.#events.delete(event);
    }

    return this;
  }

  /**
   * Synchronously trigger all listeners for an event.
   */
  emit(event, ...args) {
    if (typeof event !== "string") {
      throw new TypeError("Event name must be a string");
    }

    if (!this.#events.has(event)) return false;

    // ⚠️ CRITICAL: Copy listeners to array before iterating!
    // Prevents index shifting / skipping if a listener unsubscribes during emit.
    const listenersSnapshot = [...this.#events.get(event)];

    for (const listener of listenersSnapshot) {
      try {
        listener.apply(this, args);
      } catch (error) {
        // Isolate errors: One crashing listener must not block other listeners
        console.error(`[EventEmitter Error] Exception in listener for "${event}":`, error);
      }
    }

    return true;
  }

  /**
   * Asynchronously trigger all listeners and wait for all Promises to settle.
   */
  async emitAsync(event, ...args) {
    if (!this.#events.has(event)) return [];

    const listenersSnapshot = [...this.#events.get(event)];

    const promises = listenersSnapshot.map(async (listener) => {
      return listener.apply(this, args);
    });

    return Promise.allSettled(promises);
  }

  /**
   * Return number of active listeners for an event.
   */
  listenerCount(event) {
    return this.#events.has(event) ? this.#events.get(event).size : 0;
  }

  /**
   * Get array of all registered event names.
   */
  eventNames() {
    return Array.from(this.#events.keys());
  }

  /**
   * Remove all listeners for a specific event, or all events if none specified.
   */
  removeAllListeners(event) {
    if (event) {
      this.#events.delete(event);
    } else {
      this.#events.clear();
    }
    return this;
  }

  #validateArguments(event, listener) {
    if (typeof event !== "string" || event.trim() === "") {
      throw new TypeError("Event name must be a non-empty string");
    }
    if (typeof listener !== "function") {
      throw new TypeError("Listener must be a function");
    }
  }
}
```

---

## 3) Deep-Dive: Critical Engineering Edge Cases

---

### Edge Case 1: The Active Mutation Bug During `emit()`

What happens if a listener calls `emitter.off()` or `emitter.on()` **while the emitter is currently running through its listeners**?

```js
// ❌ Dangerous without snapshotting:
// If this.#events.get(event) is iterated directly and listener A deletes itself,
// the loop skips listener B because the collection shrinks during iteration!

// ✅ The Fix: Snapshotting via Array Spread
const listenersSnapshot = [...this.#events.get(event)];
```

---

### Edge Case 2: The `once()` Unsubscribe Dilemma

If a consumer registers a listener with `emitter.once("login", handleLogin)` and later wants to cancel it *before* it fires by calling `emitter.off("login", handleLogin)`:
- `once()` wrapped `handleLogin` inside `onceWrapper`.
- `this.events.get("login")` contains `onceWrapper`, **not** `handleLogin`.
- **The Solution**: Attach `.originalListener = listener` property to the wrapper so `off()` can match against both the wrapper and the original function reference.

---

### Edge Case 3: Error Isolation

In production systems, if Listener 1 throws an unhandled exception, **Listener 2 and Listener 3 must still execute**:

```js
try {
  listener.apply(this, args);
} catch (error) {
  console.error(`[EventEmitter Error] Exception in listener:`, error);
}
```

---

## 4) Verification & Comprehensive Test Suite

Run this test script to verify all capabilities:

```js
// --- TEST SUITE ---
const emitter = new CustomEventEmitter(3);

console.log("=== TEST 1: Basic Subscribe & Emit ===");
let loginCount = 0;
const unsubLogin = emitter.on("user:login", (user) => {
  loginCount++;
  console.log(`[Test 1] Welcome, ${user.name}! (Count: ${loginCount})`);
});

emitter.emit("user:login", { name: "Shivam" }); // Logs
emitter.emit("user:login", { name: "Alice" });  // Logs

console.log("=== TEST 2: Auto-Unsubscribe via Return Callback ===");
unsubLogin(); // Unsubscribe using returned teardown function
emitter.emit("user:login", { name: "Bob" }); // Does nothing (Safely unsubscribed)
console.log("Listener count after unsub:", emitter.listenerCount("user:login")); // 0

console.log("\n=== TEST 3: once() Single Execution ===");
let discountGiven = 0;
emitter.once("first:purchase", (amount) => {
  discountGiven += amount * 0.2;
  console.log(`[Test 3] Applied 20% discount: $${discountGiven}`);
});

emitter.emit("first:purchase", 100); // Fires -> $20
emitter.emit("first:purchase", 50);  // Ignored!
console.log("Total discount:", discountGiven); // 20

console.log("\n=== TEST 4: Async Listeners (emitAsync) ===");
emitter.on("order:process", async (orderId) => {
  await new Promise((res) => setTimeout(res, 50));
  console.log(`[Async 1] Payment processed for order #${orderId}`);
  return "PAYMENT_SUCCESS";
});

emitter.on("order:process", async (orderId) => {
  await new Promise((res) => setTimeout(res, 20));
  console.log(`[Async 2] Inventory updated for order #${orderId}`);
  return "INVENTORY_SUCCESS";
});

(async () => {
  const results = await emitter.emitAsync("order:process", 90210);
  console.log("[Test 4] Async Settle Status:", results.map((r) => r.status));
})();
```

---

## 5) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                     CUSTOM EVENT EMITTER ARCHITECTURE                      |
+───────────────────────────+────────────────────────────────────────────────+
| Registry Structure        | `Map<string, Set<Function>>` for $O(1)$ CRUD.  |
| Unsubscribe Method        | Return `() => this.off(event, listener)`.      |
| `once()` Wrapper          | Wrapped function with `.originalListener` tag. |
| Safe Emission             | Copy listener `Set` into Array before loop.    |
| Fault Tolerance           | Wrap listener execution in `try...catch`.      |
| Async Dispatch            | `Promise.allSettled(snapshot.map(fn => fn()))` |
| Leak Prevention           | Warning when `listeners.size >= maxListeners`. |
+───────────────────────────+────────────────────────────────────────────────+
```

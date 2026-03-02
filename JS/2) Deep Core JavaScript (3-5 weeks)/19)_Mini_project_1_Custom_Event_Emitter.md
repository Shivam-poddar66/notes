# Mini Project 1: Custom Event Emitter

## Goal
Implement a lightweight event system to master closures, maps, arrays, and function references.

## Required API
- `on(eventName, handler)`
- `off(eventName, handler)`
- `once(eventName, handler)`
- `emit(eventName, payload)`

## Suggested Implementation
Use `Map<string, Set<Function>>`.

```js
class EventEmitter {
  constructor() {
    this.events = new Map();
  }

  on(event, handler) {
    if (!this.events.has(event)) this.events.set(event, new Set());
    this.events.get(event).add(handler);
  }

  emit(event, payload) {
    if (!this.events.has(event)) return;
    for (const handler of this.events.get(event)) {
      handler(payload);
    }
  }
}
```

## Test Scenarios
1. Multiple handlers for same event.
2. `off` removes exact handler.
3. `once` runs one time.
4. Emitting unknown event should not crash.

## Extensions
- Wildcard event support.
- Async handlers with `Promise.allSettled`.
- Listener count and leak warnings.

## Done Criteria
- All API methods stable.
- Memory-safe handler management.
- Edge cases tested.

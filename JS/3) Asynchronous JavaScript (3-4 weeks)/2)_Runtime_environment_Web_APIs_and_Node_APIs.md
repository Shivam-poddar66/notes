# Runtime Environment: Web APIs and Node APIs

## 1) JavaScript Engine vs Runtime
- JS engine executes language code.
- Runtime provides extra async APIs.

## 2) Browser Runtime (Web APIs)
Examples:
- `setTimeout`
- DOM events
- `fetch`
- `localStorage`
- `WebSocket`

## 3) Node Runtime APIs
Examples:
- `fs` async file operations
- network sockets
- timers
- streams
- process events

## 4) Important Mental Model
Async capabilities are not from JS language alone.
They come from runtime APIs integrated with event loop behavior.

## 5) Practical Difference
Same JS syntax, different available APIs.

```js
// Browser only
fetch("/api/data");

// Node only
// import fs from 'node:fs/promises';
```

## 6) Quick Practice
1. List 10 browser APIs and 10 Node APIs.
2. Mark which APIs are async.
3. Build one small script in each environment.

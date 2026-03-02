# Event Loop: Core Mental Model

## 1) Components
- Call stack
- Task queues
- Runtime APIs
- Event loop scheduler

## 2) How It Works
1. Execute synchronous code on call stack.
2. Async operations are handed to runtime.
3. Completed callbacks/promises are queued.
4. Event loop pushes queued work when stack is empty.

## 3) Key Rule
Micro-tasks run before macro-tasks when stack is clear.

## 4) Example
```js
console.log("start");
Promise.resolve().then(() => console.log("micro"));
setTimeout(() => console.log("macro"), 0);
console.log("end");
// start, end, micro, macro
```

## 5) Why This Matters
- Explains output order.
- Prevents race condition bugs.
- Helps debug UI timing behavior.

## 6) Quick Practice
1. Draw event loop flow for 5 snippets.
2. Explain why promise callback runs before timer.
3. Create one custom example with nested micro-tasks.

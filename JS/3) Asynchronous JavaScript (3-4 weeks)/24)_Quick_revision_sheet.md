# Quick Revision Sheet (Phase 3)

## Async Foundations
- Sync blocks next line.
- Async delegates work and resumes via queues.
- Runtime APIs provide async primitives.

## Event Loop
- Call stack must clear before queued tasks run.
- Micro-task queue drains before macro-task queue.
- Promise callbacks are micro-tasks.

## Patterns
- Callbacks are older async style.
- Promises improve composition and error flow.
- `async`/`await` gives linear readable async code.

## Promise Combinators
- `all`: all must pass.
- `allSettled`: get all outcomes.
- `race`: first settled wins.
- `any`: first fulfilled wins.

## Networking
- `fetch` resolves on HTTP errors too; check `res.ok`.
- Understand methods, status codes, headers.
- Validate JSON shape before use.

## Reliability
- Retry only transient failures.
- Use exponential backoff for retries.
- Use `AbortController` for cancellation.
- Use timeout wrappers for stuck requests.

## UI State
- Keep explicit states: idle/loading/success/error.
- Prevent stale response overwrite.

## Milestone
- 3 mini-projects completed with proper failure handling.
- Ready for Phase 4 Browser JavaScript and Web Platform.

# Async Debugging Checklist and Common Mistakes

## Common Mistakes
- Forgetting to `await` promise.
- Missing `return` in promise chain.
- Unhandled promise rejections.
- Treating HTTP 4xx/5xx as fetch rejection automatically.
- No timeout or cancellation strategy.
- Updating UI from stale responses.
- Over-parallelizing requests and hitting rate limits.
- Retrying non-idempotent requests unsafely.

## Debugging Checklist
1. Confirm exact async execution order with logs.
2. Track promise states and branch paths.
3. Inspect network tab status, headers, payload.
4. Verify `res.ok` handling.
5. Add request IDs to detect stale updates.
6. Test slow network and offline behavior.
7. Test timeout, abort, and retry branches.
8. Capture and surface meaningful errors.
9. Reproduce with minimal script.
10. Add regression tests for bug scenario.

## Preventive Habits
- Centralize API helper logic.
- Use consistent async error format.
- Keep loading/success/error states explicit.

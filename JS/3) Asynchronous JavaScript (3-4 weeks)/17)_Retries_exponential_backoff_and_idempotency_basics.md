# Retries, Exponential Backoff, and Idempotency Basics

## 1) When to Retry
Retry transient failures only:
- network errors
- timeouts
- `429` and some `5xx`

Do not blindly retry all `4xx` errors.

## 2) Exponential Backoff
Delay grows each attempt.

```js
const delay = (attempt) => 200 * 2 ** attempt;
```

Optional jitter avoids synchronized retry storms.

## 3) Idempotency
Safe retries depend on operation type.
- `GET` is usually safe.
- `POST` may create duplicates unless idempotency key exists.

## 4) Retry Wrapper Sketch
```js
async function retry(fn, max = 3) {
  let lastErr;
  for (let i = 0; i < max; i++) {
    try {
      return await fn();
    } catch (err) {
      lastErr = err;
      await new Promise((r) => setTimeout(r, 200 * 2 ** i));
    }
  }
  throw lastErr;
}
```

## 5) Quick Practice
1. Add retry wrapper around fetch helper.
2. Retry only allowed status classes.
3. Add max-attempt logging.

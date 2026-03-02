# `AbortController` and Timeout Patterns

## 1) Why Cancellation Matters
Cancel stale requests when:
- user changes search query quickly
- user navigates away
- timeout threshold exceeded

## 2) `AbortController` Basics
```js
const controller = new AbortController();
const res = await fetch(url, { signal: controller.signal });
// controller.abort();
```

## 3) Timeout via Abort
```js
function fetchWithTimeout(url, ms) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), ms);

  return fetch(url, { signal: controller.signal })
    .finally(() => clearTimeout(timer));
}
```

## 4) Error Handling
Abort usually throws `AbortError`.
Handle separately from server/network errors.

## 5) UI Pattern
- Start request -> show loading.
- New request starts -> abort old request.
- Only latest response updates UI.

## 6) Quick Practice
1. Build cancellable search input.
2. Add timeout wrapper around API helper.
3. Distinguish abort vs HTTP failure in UI messages.

# `async`/`await` Control Flow and Error Handling

## 1) `async` Function
Always returns a promise.

```js
async function getValue() {
  return 42;
}
```

## 2) `await`
Pauses function execution until promise settles.

```js
async function run() {
  const data = await fetchData();
  return data;
}
```

## 3) Error Handling with `try/catch`
```js
async function loadUser(id) {
  try {
    const res = await fetch(`/users/${id}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.json();
  } catch (err) {
    console.error("loadUser failed:", err.message);
    throw err;
  }
}
```

## 4) Parallel `await` Pattern
Do not serially await independent requests.

```js
const [a, b] = await Promise.all([getA(), getB()]);
```

## 5) Best Practices
- Use `try/catch` around awaited risky calls.
- Keep functions small and composable.
- Re-throw when caller must know failure.

## 6) Quick Practice
1. Convert promise chain to async/await.
2. Add fallback behavior for failed API call.
3. Optimize serial awaits into parallel awaits.

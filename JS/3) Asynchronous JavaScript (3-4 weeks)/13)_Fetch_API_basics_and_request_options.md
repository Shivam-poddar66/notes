# `fetch` API Basics and Request Options

## 1) Basic GET Request
```js
const res = await fetch("https://api.example.com/items");
const data = await res.json();
```

## 2) Check HTTP Status
`fetch` rejects only on network failures.
HTTP 4xx/5xx still resolve with `ok: false`.

```js
if (!res.ok) throw new Error(`HTTP ${res.status}`);
```

## 3) POST Request Example
```js
const res = await fetch("/api/items", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ name: "Notebook" })
});
```

## 4) Common Options
- `method`
- `headers`
- `body`
- `signal` (for cancellation)

## 5) Best Practices
- Always check `res.ok`.
- Parse response safely based on content type.
- Handle timeout and cancellation.

## 6) Quick Practice
1. Build GET helper with status check.
2. Build POST helper with JSON body.
3. Add uniform error object format.

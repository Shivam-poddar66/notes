# JSON Parsing, Validation, and Safe Data Handling

## 1) Parsing JSON
```js
const payload = await res.json();
```

Potential failures:
- invalid JSON
- unexpected shape
- missing required fields

## 2) Validate Shape
Use guards before using data.

```js
function isUser(obj) {
  return obj && typeof obj.id === "number" && typeof obj.login === "string";
}
```

## 3) Defensive Access
Use optional chaining and fallback values.

```js
const city = payload?.address?.city ?? "Unknown";
```

## 4) Input Sanitization Mindset
- Treat external data as untrusted.
- Validate early at boundary.
- Fail fast with clear error messages.

## 5) Best Practices
- Separate parse step and validate step.
- Return typed-safe normalized object shape.
- Keep validation utilities reusable.

## 6) Quick Practice
1. Parse API data and validate required keys.
2. Build normalizer function for optional fields.
3. Handle malformed JSON with try/catch.

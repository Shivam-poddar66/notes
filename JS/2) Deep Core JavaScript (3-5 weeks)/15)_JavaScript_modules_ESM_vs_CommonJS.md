# JavaScript Modules: ESM vs CommonJS

## 1) Why Modules
Modules split code into reusable files with explicit public APIs.

## 2) ESM (ECMAScript Modules)
Syntax:

```js
// math.js
export function add(a, b) {
  return a + b;
}

// app.js
import { add } from "./math.js";
```

Features:
- Static structure (good for tooling/tree-shaking).
- Native in modern browsers and Node (with config).

## 3) CommonJS (CJS)
Syntax:

```js
// math.cjs
module.exports.add = (a, b) => a + b;

// app.cjs
const { add } = require("./math.cjs");
```

Features:
- Historically default in Node.
- Runtime/dynamic style.

## 4) Key Differences
- `import/export` vs `require/module.exports`.
- ESM loads asynchronously and is statically analyzable.
- CJS is synchronous loading model.

## 5) Interop Notes
Mixed ESM/CJS projects need careful configuration (`type`, extension, build setup).

## 6) Best Practices
- Prefer ESM for new codebases.
- Keep module API small and explicit.
- Avoid circular dependencies.

## 7) Quick Practice
1. Build same utility in ESM and CJS.
2. Export multiple utilities and import selectively.
3. Trigger and fix a circular dependency issue.

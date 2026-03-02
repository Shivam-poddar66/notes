# Default vs Named Exports and Module Design

## 1) Named Exports

```js
export const TAX = 0.18;
export function total(a, b) {
  return a + b;
}
```

Import:

```js
import { TAX, total } from "./finance.js";
```

## 2) Default Export

```js
export default function formatCurrency(value) {
  return `INR ${value}`;
}
```

Import name is chosen by importer:

```js
import fmt from "./format.js";
```

## 3) When to Use Which
- Named exports: preferred for utilities and explicit APIs.
- Default export: useful for one primary thing per module.

## 4) Module API Design Rules
- Keep modules focused (single responsibility).
- Avoid giant utility files.
- Export only what consumers need.
- Group related functions and constants.

## 5) Barrel Files (`index.js`)
Use carefully to simplify imports. Keep dependency graph clear.

## 6) Quick Practice
1. Refactor one file into named exports.
2. Convert one module to default export and justify choice.
3. Create feature folder with clean module boundary.

# Default vs. Named Exports and Clean Module Design in JavaScript

---

## 1) Core Mental Model: The Two Export Modalities

In ECMAScript Modules (ESM), there are two distinct mechanisms for exposing public APIs from a module:
1. **Named Exports**: Explicitly exports one or more identifiers bound to specific names. Consumers must import them using the exact exported name (or explicitly alias them).
2. **Default Export**: Exports a single, primary value under the special internal identifier `"default"`. Consumers can import it using any identifier name of their choosing.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        NAMED vs. DEFAULT EXPORTS                       │
│                                                                        │
│   Named Exports:                                                       │
│   export const API_KEY = "123";                                        │
│   export function authenticate() {}                                    │
│   ──▶ import { API_KEY, authenticate } from './auth.js';               │
│       (Predictable, refactor-safe, autocompletable)                    │
│                                                                        │
│   Default Export:                                                      │
│   export default class AuthService {}                                  │
│   ──▶ import MyAuthService from './auth.js';                           │
│       (Single primary entity, flexible consumer naming)                │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Comprehensive Syntax Reference

---

### A. Named Exports & Imports

```js
// --- 1. Inline Named Exports ---
export const MAX_RETRY = 3;
export function calculateTax(amount, rate) { return amount * rate; }
export class PaymentProcessor {}

// --- 2. Export Clause (At bottom of file) ---
const formatCurrency = (v) => `$${v.toFixed(2)}`;
const parseCurrency = (str) => parseFloat(str.replace("$", ""));

export { formatCurrency, parseCurrency as parseUSD };

// --- 3. Named Imports ---
import { MAX_RETRY, calculateTax, formatCurrency, parseUSD } from "./finance.js";

// --- 4. Renaming / Aliasing on Import ---
import { calculateTax as calcSalesTax } from "./finance.js";

// --- 5. Namespace / Wildcard Import ---
import * as Finance from "./finance.js";
Finance.calculateTax(100, 0.1);
```

---

### B. Default Exports & Imports

```js
// --- 1. Default Function Export ---
export default function generateId() {
  return crypto.randomUUID();
}

// --- 2. Default Class Export ---
// export default class Logger {}

// --- 3. Default Expression / Object Export ---
// const config = { env: "production" };
// export default config;

// --- 4. Default Import (Choose any name) ---
import createUniqueId from "./idGenerator.js";
```

---

### C. Combined Default and Named Imports

A single module can export both a default entity and supplementary named utilities:

```js
// --- apiClient.js ---
export const API_VERSION = "v2";
export function buildHeaders() { return { Authorization: "Bearer ..." }; }

export default class ApiClient {
  constructor(baseUrl) { this.baseUrl = baseUrl; }
}

// --- consumer.js ---
// Option A: Clean combined import
import ApiClient, { API_VERSION, buildHeaders } from "./apiClient.js";

// Option B: Importing default via named syntax
import { default as ApiClientClass, API_VERSION } from "./apiClient.js";
```

---

## 3) Named vs. Default Exports: The Industry Standard Debate

In modern JavaScript and TypeScript development, there is a strong industry consensus favoring **Named Exports** for most codebases.

| Dimension | Named Exports (Recommended) | Default Exports |
| :--- | :--- | :--- |
| **Refactoring Safety** | 🟢 **100% Safe**: Renaming an export updates all import references across the entire project automatically. | 🔴 **Risky**: Importer can name it anything (`import Foo from './bar'`), leading to naming inconsistencies across files. |
| **IDE Autocomplete** | 🟢 **Instant**: Typing the function name triggers automatic import suggestions. | 🟡 **Weak**: IDE cannot always predict which default export is intended. |
| **Tree-Shaking** | 🟢 **Optimal**: Bundlers can eliminate unused named exports with zero ambiguity. | 🟡 **Can bail out**: Bundlers sometimes bundle entire default export objects. |
| **API Clarity** | 🟢 Explicit surface area. | 🟡 Implicit single payload. |

---

### When to Use Default Exports:
- **React / UI Component Files**: When a file represents exactly one visual component (`Button.jsx`, `Modal.tsx`).
- **File-Based Routing Frameworks**: Next.js (`page.tsx`), Remix, Vite SSR routes require default exports for route pages.
- **Configuration Files**: `vite.config.js`, `tailwind.config.js`, `playwright.config.ts`.

---

## 4) Re-Exporting, Aggregation & Barrel Files (`index.js`)

A **Barrel File** is a central entry point (`index.js` or `index.ts`) that rolls up and re-exports symbols from multiple nested submodule files:

```
src/
└── components/
    ├── Button.js
    ├── Card.js
    ├── Modal.js
    └── index.js   <── BARREL FILE
```

---

### Re-Export Syntax Patterns:

```js
// --- src/components/index.js ---

// 1. Re-export all named exports from submodules
export * from "./Button.js";
export * from "./Card.js";

// 2. Re-export specific named symbols with aliasing
export { Modal, ModalHeader as Header } from "./Modal.js";

// 3. Re-export a default export as a named export
export { default as Tooltip } from "./Tooltip.js";
```

Now consumers can import everything from one clean entry path:
```js
import { Button, Card, Modal, Tooltip } from "./components/index.js";
```

---

### ⚠️ The Barrel File Performance Trap (Build Time & Bundle Size)

While barrel files provide clean import paths, **giant root barrel files can severely degrade performance**:
1. **Slow Development Servers**: In Vite / Next.js, importing one tiny icon from `import { CheckIcon } from 'lucide-react'` or a giant barrel file forces the dev server to parse hundreds of unnecessary files.
2. **Tree-Shaking Bailouts**: If a barrel file contains side-effects, bundlers cannot safely eliminate unused re-exported modules.

**Best Practice**: Keep barrel files small and localized to individual feature folders. Avoid massive application-wide root barrel files.

---

## 5) Architectural Clean Module Design Principles

---

### Principle 1: Single Responsibility & High Cohesion
A module should focus on doing **one logical domain task well**. Avoid monolithic "God modules" like `utils.js` or `helpers.js` containing 50 unrelated functions.

```
❌ Monolithic Anti-Pattern:
utils.js (Holds date formatting, math calculations, JWT decoding, string regexes)

✅ Clean Modular Design:
├── utils/
│   ├── date.js
│   ├── string.js
│   ├── crypto.js
│   └── math.js
```

---

### Principle 2: Information Hiding & Encapsulation
Export **only the minimal public API surface** that consumers need. Keep internal helper functions, private constants, and implementation details unexported inside the file:

```js
// --- authService.js ---

// 🔒 PRIVATE TO MODULE (Not exported):
const SECRET_SALT = "x98f#_secure";
function hashPassword(pwd) {
  return crypto.createHmac("sha256", SECRET_SALT).update(pwd).digest("hex");
}

// 🌐 PUBLIC API (Exported):
export async function registerUser(email, password) {
  const hashedPassword = hashPassword(password);
  return database.saveUser({ email, password: hashedPassword });
}
```

---

### Principle 3: Dependency Graph Structure (Avoid Circular Graphs)

Module dependencies must form a **Directed Acyclic Graph (DAG)**. Code should flow downward from high-level features to low-level primitives:

```
[ Application Routes / UI Pages ]
               │
               ▼
   [ Domain Business Services ]
               │
               ▼
[ Shared Primitives / Core Utils / DB ]
```

---

## 6) Dynamic Imports (`import()`) & Code Splitting

ESM supports dynamic on-demand module loading using the function-like `import(modulePath)` expression, which returns a **Promise**:

```js
// Load heavy charting library only when user clicks "View Analytics"
async function renderAnalyticsDashboard() {
  console.log("Loading heavy chart engine...");

  // Dynamic import splits this into a separate network chunk!
  const { ChartEngine } = await import("./heavyChartEngine.js");

  const chart = new ChartEngine("#chart-container");
  chart.render();
}

document.querySelector("#analytics-btn").addEventListener("click", renderAnalyticsDashboard);
```

---

## 7) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         MODULE DESIGN CHEAT SHEET                          |
+───────────────────────────+────────────────────────────────────────────────+
| Concept                   | Best Practice / Rule                           |
+───────────────────────────+────────────────────────────────────────────────+
| Primary Choice            | Prefer **Named Exports** for refactor safety.  |
| Default Exports           | Use for Single-entity files (React pages/UI).  |
| Re-Exporting              | `export { x } from './file.js'` (Barrel files).|
| Barrel File Hazard        | Avoid giant root barrel files (slows bundlers).|
| Information Hiding        | Unexported functions remain private to module. |
| Dynamic Imports           | `await import('./lib.js')` for code splitting. |
| Monolithic Utils          | Split into domain-specific utility modules.    |
+───────────────────────────+────────────────────────────────────────────────+
```

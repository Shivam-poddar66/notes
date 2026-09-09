# JavaScript Modules: ECMAScript Modules (ESM) vs. CommonJS (CJS)

---

## 1) Core Evolution: Why JavaScript Needed Modules

In early JavaScript, all scripts shared a single global namespace (`window`), causing naming collisions, uncontrolled mutations, and order-dependent `<script>` loading bugs.

Over time, several module patterns and systems evolved:
1. **IIFE / Revealing Module Pattern** (2000s): Emulated scope privacy using closures.
2. **AMD / RequireJS** (2010): Asynchronous Module Definition for browsers.
3. **CommonJS (CJS)** (2009): The synchronous module system built for server-side Node.js.
4. **ECMAScript Modules (ESM)** (2015+ / ES6): The official, static, universal standard for both browsers and servers.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    COMMONJS (CJS) vs. ECMASCRIPT MODULES (ESM)          │
│                                                                         │
│   CommonJS (CJS)                       ECMAScript Modules (ESM)         │
│   ────────────────────────             ────────────────────────         │
│   • Node.js legacy standard            • Universal JS standard (ES6+)   │
│   • `require()` & `module.exports`     • `import` & `export`            │
│   • Synchronous & Dynamic runtime      • Asynchronous & Static graph    │
│   • Exports are COPIES (Snapshots)     • Exports are LIVE BINDINGS      │
│   • No Tree-Shaking                    • Perfect Tree-Shaking           │
│   • File: `.cjs` or default Node       • File: `.mjs` or `"type":"module"│
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Master Head-to-Head Comparison Matrix

| Feature | CommonJS (CJS) | ECMAScript Modules (ESM) |
| :--- | :--- | :--- |
| **Syntax** | `const pkg = require('./pkg')`<br>`module.exports = { ... }` | `import { fn } from './pkg.js'`<br>`export const fn = ...` |
| **Parsing & Resolution** | **Runtime / Dynamic** (evaluated when line runs) | **Compile-Time / Static** (analyzed before execution) |
| **Loading Model** | **Synchronous** (blocks I/O on disk read) | **Asynchronous** (3-phase graph resolution) |
| **Export Binding Model** | **Value Copy (Snapshot)** | **Live Read-Only Binding (Reference)** |
| **Tree-Shaking (DCE)** | ❌ Difficult / Bailed out by bundlers | ✅ First-class native dead-code elimination |
| **Top-Level `await`** | ❌ Forbidden at top level | ✅ Supported natively (`await fetch(...)`) |
| **Browser Compatibility** | ❌ Needs Bundler (Webpack/Browserify) | ✅ Supported natively (`<script type="module">`) |
| **Environment Globals** | `__dirname`, `__filename`, `require`, `module` | `import.meta.url`, `import.meta.dirname` (Node 20+) |
| **Top-Level `this`** | `module.exports` (`{}`) | `undefined` |
| **Strict Mode** | Opt-in via `"use strict";` | **Always Strict Mode by default** |

---

## 3) The Fundamental Mechanical Difference: Value Copies vs. Live Bindings

The most critical architectural difference between CJS and ESM is how exported values are shared between files:

```
                  COMMONJS (COPIED VALUE)
┌────────────────────────────────────────────────────────┐
│  math.cjs: module.exports = { count: 1 }               │
│  app.cjs : const { count } = require('./math.cjs')     │
│                                                        │
│  math.cjs increments `count` ──▶ app.cjs STILL SEES 1! │
│  (app.cjs received a static snapshot at require time)  │
└────────────────────────────────────────────────────────┘

                  ESM (LIVE READ-ONLY BINDING)
┌────────────────────────────────────────────────────────┐
│  math.mjs: export let count = 1;                       │
│  app.mjs : import { count } from './math.mjs';         │
│                                                        │
│  math.mjs increments `count` ──▶ app.mjs SEES 2!       │
│  (app.mjs holds a live pointer to math's memory slot)  │
└────────────────────────────────────────────────────────┘
```

---

### Concrete Code Demonstration:

#### CommonJS (Snapshot Copy):
```js
// --- counter.cjs ---
let count = 1;
function increment() { count++; }
module.exports = { count, increment };

// --- consumer.cjs ---
const { count, increment } = require("./counter.cjs");
console.log(count); // 1
increment();
console.log(count); // 1 ⚠️ (Did NOT update! Copied primitive value)
```

#### ESM (Live Binding):
```js
// --- counter.mjs ---
export let count = 1;
export function increment() { count++; }

// --- consumer.mjs ---
import { count, increment } from "./counter.mjs";
console.log(count); // 1
increment();
console.log(count); // 2 ✅ (Live binding reflected instantly!)

// Direct mutation by consumer is strictly forbidden:
// count = 10; // ❌ TypeError: Assignment to constant variable / invalid assignment
```

---

## 4) The 3-Phase Loading Lifecycle of ESM

Because ESM modules can be loaded across the network in browsers or from disk in Node.js, the JavaScript engine loads ESM code in three separate phases:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        ESM 3-PHASE LIFECYCLE                            │
│                                                                         │
│  Phase 1: Construction (Parsing & Module Graph Building)                │
│    • Finds all `import` statements at compile time                      │
│    • Downloads / fetches all dependency files in parallel               │
│    • Parses code into AST Module Records (No JS executed yet!)          │
│                                                                         │
│  Phase 2: Instantiation (Memory Wiring)                                 │
│    • Allocates memory locations for all exports and imports             │
│    • Wires `import` pointers directly to `export` memory slots          │
│    • Creates the Live Bindings (No values assigned yet)                 │
│                                                                         │
│  Phase 3: Evaluation (Code Execution)                                   │
│    • Executes top-level code from bottom-of-graph upward                │
│    • Populates the allocated memory slots with actual values            │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 5) CommonJS Internals: The Module Wrapper Function

Under the hood in Node.js, when you load a CommonJS file, Node wraps your entire code inside a hidden IIFE function before passing it to V8:

```js
// What Node.js actually executes under the hood:
(function (exports, require, module, __filename, __dirname) {
  // --- YOUR COMMONJS CODE RUNS HERE ---
  const secret = "12345";
  module.exports = { secret };
});
```

- This explains why `require`, `module`, `exports`, `__filename`, and `__dirname` appear like globals, but are actually **local arguments** to the wrapper function.
- Node caches the resulting `module.exports` object in `require.cache`. Subsequent `require()` calls return the cached object instantly without re-executing the file.

---

## 6) Circular Dependencies: CJS vs. ESM

Circular dependencies occur when Module A imports Module B, and Module B imports Module A.

### In CommonJS (Risk of Incomplete State):
When CJS encounters a circular loop, it returns the **incomplete `exports` snapshot** of Module A created up to that exact point in execution:

```js
// --- a.cjs ---
exports.loaded = false;
const b = require("./b.cjs");
exports.loaded = true;

// --- b.cjs ---
const a = require("./a.cjs");
console.log("b.cjs saw a.loaded as:", a.loaded); // false! (Snapshot taken before a finished)
```

---

### In ESM (Handled Gracefully via Live Bindings & Hoisting):
Because ESM wires memory slots during Instantiation before running Evaluation in Phase 3, functions and live bindings are accessible across circular boundaries:

```js
// --- a.mjs ---
import { bFn } from "./b.mjs";
export function aFn() { return "A calling -> " + bFn(); }

// --- b.mjs ---
import { aFn } from "./a.mjs";
export function bFn() { return "B ready"; }

// --- main.mjs ---
import { aFn } from "./a.mjs";
console.log(aFn()); // "A calling -> B ready" (Resolves cleanly)
```

---

## 7) Interoperability: Mixing CJS and ESM in Node.js

Modern Node.js allows mixing CJS and ESM, but strict compatibility rules apply:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        INTEROPERABILITY RULES                          │
│                                                                        │
│   1. ESM importing CommonJS:  ✅ Supported                            │
│      import cjsModule from './legacy.cjs';                             │
│                                                                        │
│   2. CommonJS requiring ESM:  ❌ FORBIDDEN (Synchronous require fails) │
│      const esm = require('./modern.mjs');                              │
│      // ❌ Error [ERR_REQUIRE_ESM]: require() of ES Module not supported│
│                                                                        │
│   3. Dynamic Import in CJS:   ✅ Supported (Asynchronous)              │
│      async function load() {                                           │
│        const esm = await import('./modern.mjs');                       │
│      }                                                                 │
└────────────────────────────────────────────────────────────────────────┘
```

---

### Migrating `__dirname` and `__filename` to ESM:

ESM does not have `__dirname` or `__filename`. Use `import.meta`:

```js
// Modern ESM (Node 20.11+):
console.log(import.meta.dirname);
console.log(import.meta.filename);

// Universal ESM (Any Node version):
import { fileURLToPath } from "node:url";
import { dirname } from "node:path";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
```

---

## 8) Dual-Package Publishing (Package.json `exports` Map)

When authoring modern npm packages supporting both CJS and ESM consumers, configure the `package.json` `"exports"` field:

```json
{
  "name": "my-awesome-lib",
  "version": "1.0.0",
  "type": "module",
  "main": "./dist/index.cjs",
  "module": "./dist/index.js",
  "exports": {
    "import": {
      "types": "./dist/index.d.ts",
      "default": "./dist/index.js"
    },
    "require": {
      "types": "./dist/index.d.cts",
      "default": "./dist/index.cjs"
    }
  }
}
```

---

## 9) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                        MODULE SYSTEMS QUICK MATRIX                         |
+───────────────────────────+────────────────────────────────────────────────+
| Concept                   | Rule / Takeaway                                |
+───────────────────────────+────────────────────────────────────────────────+
| Syntax                    | CJS: `require/module.exports` \| ESM: `import/export`|
| Evaluation Timing         | CJS: Synchronous Runtime \| ESM: Static 3-Phase|
| Value Model               | CJS: Value Copy Snapshot \| ESM: Live Binding  |
| Tree-Shaking              | ESM natively supports dead code elimination.   |
| Top-Level Await           | Supported natively in ESM.                     |
| Enable ESM in Node        | Add `"type": "module"` in package.json or `.mjs`|
| Node Paths in ESM         | Use `import.meta.dirname` / `import.meta.url`. |
+───────────────────────────+────────────────────────────────────────────────+
```

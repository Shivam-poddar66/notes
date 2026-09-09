# Phase 2: Deep Core JavaScript — Quick Revision Sheet & Cheat Card

---

## 1) Execution Context & Call Stack

```
┌────────────────────────────────────────────────────────────────────────┐
│                        EXECUTION CONTEXT SUMMARY                       │
│                                                                        │
│  • Single-Threaded & Synchronous: 1 Call Stack executes 1 frame at once│
│  • Types: Global Execution Context (GEC) & Function Context (FEC)      │
│  • Lifecycle:                                                          │
│      1. Creation Phase: Memory allocated, scope chain built, `this` set │
│      2. Execution Phase: Code runs line-by-line, values assigned       │
│  • Call Stack: LIFO (Last-In, First-Out) data structure                │
│  • Stack Overflow: Exceeding call stack limit (RangeError)             │
│      Fix: Base cases, iterative conversion, Trampolining               │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Hoisting & Temporal Dead Zone (TDZ)

| Construct | Hoisted? | Initial Creation Value | Access Before Declaration |
| :--- | :--- | :--- | :--- |
| **`function` Declaration** | **Yes** | Full function body | ✅ **Executes successfully** |
| **`var` Variable** | **Yes** | `undefined` | ⚠️ **Returns `undefined`** |
| **`let` / `const`** | **Yes** | `<uninitialized>` | ❌ **`ReferenceError` (TDZ)** |
| **`class` Declaration** | **Yes** | `<uninitialized>` | ❌ **`ReferenceError` (TDZ)** |
| **`var fn = () => {}`** | **Yes** (Variable only)| `undefined` | ❌ **`TypeError: fn is not a fn`**|

- **Hoisting** is memory allocation during the Creation Phase, *not* physical code movement.
- **TDZ** is the time-span between scope entry and line of initialization.

---

## 3) Scope Chain & Closures

- **Lexical Scope**: Scope is determined at **author time** (where code is written), not invocation time.
- **Scope Chain**: Bottom-up search (Local -> Parent -> Global). Stops at first match.
- **Closure**: Function + its enclosing Lexical Environment reference (`[[Environment]]`).
  - Survives execution context pop because it is retained in the **Memory Heap**.
  - Captures **Live Variable References**, not static snapshot copies.
- **The Loop `var` Trap**:
  ```js
  // ❌ Prints 3, 3, 3 (Shared var binding)
  for (var i = 0; i < 3; i++) setTimeout(() => console.log(i), 100);

  // ✅ Prints 0, 1, 2 (Fresh lexical binding per iteration)
  for (let i = 0; i < 3; i++) setTimeout(() => console.log(i), 100);
  ```

---

## 4) The `this` Keyword: 5 Call-Site Rules

```
[ Highest Precedence ]
   1. Lexical `this` (Arrow Functions `() => {}`): Inherited from lexical parent scope.
   2. `new` Binding: `this` points to the newly instantiated object.
   3. Explicit Binding: `.call(ctx, ...args)`, `.apply(ctx, [args])`, `.bind(ctx)`.
   4. Implicit Binding: `obj.method()` -> `this` is object before the dot (`obj`).
   5. Default Binding: Standalone `fn()` -> `undefined` (strict) or `globalThis` (sloppy).
[ Lowest Precedence ]
```

- **Losing `this`**: Passing an object method as a callback detaches `this`.
  - Fix: Arrow wrapper `() => obj.method()`, constructor `.bind(this)`, or class arrow field.
- **Double Bind**: `.bind(obj1).bind(obj2)` -> **`obj1` always wins!**

---

## 5) Prototypes & Object-Oriented Inheritance

- **Prototypal Delegation**: Objects delegate missing property lookups upward via `[[Prototype]]` to `Object.prototype -> null`.
- **`[[Prototype]]` vs `prototype`**:
  - `[[Prototype]]` (`__proto__`): Internal link on instance objects.
  - `Constructor.prototype`: Blueprint object attached to constructor functions.
- **The 4 Steps of `new`**:
  1. Create `{}`.
  2. Set `newObj.[[Prototype]] = Constructor.prototype`.
  3. Run constructor with `this = newObj`.
  4. Return `newObj` (unless constructor explicitly returns another object).
- **ES5 Inheritance Pattern**:
  ```js
  function Child(...args) { Parent.call(this, ...args); }
  Child.prototype = Object.create(Parent.prototype);
  Child.prototype.constructor = Child;
  ```
- **ES6 Classes**:
  - Syntactic sugar over prototypes (`typeof Class === "function"`).
  - Enforces `new`, runs in **Strict Mode**, methods are **non-enumerable**.
  - Hard private fields: `#privateField`.
  - `extends` sets up both instance and static prototype chains (`super()` required before `this`).

---

## 6) Object Internals, Immutability & Copying

### A. Property Descriptors
- **Data Descriptor**: `value`, `writable`, `enumerable`, `configurable`.
- **Accessor Descriptor**: `get`, `set`, `enumerable`, `configurable`.
- **Default Gotcha**: Literal `obj.x = 1` sets flags to `true`; `Object.defineProperty` defaults omitted flags to `false`.

### B. Object Integrity Levels
- **`Object.preventExtensions(obj)`**: Cannot add new keys.
- **`Object.seal(obj)`**: Cannot add or delete keys (`configurable: false`).
- **`Object.freeze(obj)`**: Cannot add, delete, or modify keys (`writable: false`, `configurable: false`).
- **Shallow Trap**: `Object.freeze` is shallow. Use recursive `deepFreeze` for deep immutability.

### C. Copying Strategies
- **Shallow Copy**: `{ ...obj }` / `Object.assign({}, obj)` (Nested object references are shared!).
- **JSON Deep Hack**: `JSON.parse(JSON.stringify(obj))` (Flawed: drops `undefined`, `Date` becomes string, crashes on cycles).
- **Native Deep Copy**: `structuredClone(obj)` (Handles cycles, `Date`, `Map`, `Set`, `RegExp`; cannot clone functions).

---

## 7) Modules: ESM vs. CommonJS

| Feature | ECMAScript Modules (ESM) | CommonJS (CJS) |
| :--- | :--- | :--- |
| **Syntax** | `import` / `export` | `require` / `module.exports` |
| **Loading** | Asynchronous 3-Phase Graph | Synchronous Runtime Execution |
| **Export Binding** | **Live Read-Only Binding** | **Value Copy (Snapshot)** |
| **Tree-Shaking** | ✅ Fully Supported | ❌ Bails out / Bundles whole file |
| **Top-Level Await**| ✅ Supported natively | ❌ Forbidden |
| **Paths in Node** | `import.meta.dirname` / `import.meta.url` | `__dirname` / `__filename` |

- **Named vs Default**: Prefer **Named Exports** for refactor safety, autocomplete, and optimal tree-shaking.
- **Tree-Shaking**: Keep modules pure and declare `"sideEffects": false` in `package.json`.

---

## 8) Advanced Language Features & Metaprogramming

- **Deep Destructuring**: `const { data: { user: { id = "default" } } } = res;`
- **Optional Chaining (`?.`)**: Short-circuits on `null` or `undefined` (`user?.address?.city`).
- **Nullish Coalescing (`??`)**: Fallback for `null` / `undefined` only (preserves `0`, `""`, `false`).
- **Logical Assignment**: `a ??= 10` (Assigns only if `a` is nullish).
- **Iterators**: Objects implementing `[Symbol.iterator]()` returning `{ next() -> { value, done } }`.
- **Generators (`function*`)**: Coroutines that pause/resume state via `yield`.
- **Symbols**: Guaranteed unique primitive keys (`Symbol("id")`).
  - Metaprogramming: `Symbol.toPrimitive` (custom coercion), `Symbol.toStringTag`.

---

## 9) Master Formula Card for Interviews

```
┌────────────────────────────────────────────────────────────────────────┐
│                        MASTER INTERVIEW FORMULAS                       │
│                                                                        │
│   • `typeof NaN` ──────────────▶ "number"                              │
│   • `typeof null` ─────────────▶ "object" (Legacy engine bug)          │
│   • `typeof function() {}` ────▶ "function"                            │
│   • `[] + []` ─────────────────▶ "" (Empty string)                     │
│   • `[] + {}` ─────────────────▶ "[object Object]"                     │
│   • `Function instanceof Object`▶ true                                 │
│   • `Object instanceof Function`▶ true                                 │
│   • `Object.prototype.__proto__`▶ null (End of chain)                  │
│   • `(0, obj.fn)()` ───────────▶ Default binding applied (`undefined`)│
│   • `structuredClone(cycle)` ──▶ ✅ Clones safely                      │
│   • `const + freeze` ──────────▶ True immutable constant              │
└────────────────────────────────────────────────────────────────────────┘
```

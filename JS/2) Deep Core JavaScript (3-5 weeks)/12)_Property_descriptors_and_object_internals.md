# Property Descriptors and Object Internals in JavaScript

---

## 1) Core Mental Model: Objects Beyond Key-Value Pairs

In JavaScript, objects are not merely naive hash maps of string keys and values. Every property attached to an object is governed by an internal engine record called a **Property Descriptor**.

Property descriptors define the exact behavioral attributes of a property: whether it can be modified, enumerated in loops, configured, or if its value is dynamically calculated via getters and setters.

```
┌──────────────────────────────────────────────────────────────────────────┐
│                      ANATOMY OF A PROPERTY DESCRIPTOR                    │
│                                                                          │
│                 ┌──────────────────────────────────────┐                 │
│                 │       PROPERTY DESCRIPTOR TYPE       │                 │
│                 └──────────────────┬───────────────────┘                 │
│                                    │                                     │
│             ┌──────────────────────┴──────────────────────┐              │
│             ▼                                             ▼              │
│   ┌─────────────────────┐                       ┌────────────────────┐   │
│   │   DATA DESCRIPTOR   │                       │ ACCESSOR DESCRIPTOR│   │
│   ├─────────────────────┤                       ├────────────────────┤   │
│   │ • value             │                       │ • get (getter fn)  │   │
│   │ • writable          │                       │ • set (setter fn)  │   │
│   │ • enumerable        │                       │ • enumerable       │   │
│   │ • configurable      │                       │ • configurable     │   │
│   └─────────────────────┘                       └────────────────────┘   │
└──────────────────────────────────────────────────────────────────────────┘
```

> **Rule**: A property descriptor must be either a **Data Descriptor** or an **Accessor Descriptor**; it **cannot be both** (e.g. specifying both `value` and `get` throws a `TypeError`).

---

## 2) The 4 Core Descriptor Attributes Explained

---

### 1. `value` (Data Descriptor only)
The actual value held by the property (`number`, `string`, `object`, `function`, etc.). Defaults to `undefined`.

---

### 2. `writable` (Data Descriptor only)
- **`true`**: The property's value can be changed using the assignment operator (`=`).
- **`false`**: The property is **read-only**.
  - In Non-Strict Mode: Reassignment fails silently.
  - In Strict Mode: Throws `TypeError: Cannot assign to read only property`.

```js
"use strict";
const user = {};
Object.defineProperty(user, "id", {
  value: 1001,
  writable: false // Read-only
});

// user.id = 2002; // ❌ TypeError: Cannot assign to read only property 'id'
```

---

### 3. `enumerable` (Data & Accessor Descriptors)
- **`true`**: The property is visible during enumeration:
  - Included in `for...in` loops
  - Returned by `Object.keys()` and `Object.entries()`
  - Serialized by `JSON.stringify()`
  - Copied by `Object.assign()` and the object spread operator (`{ ...obj }`)
- **`false`**: The property is **hidden / non-enumerable**. It remains accessible via direct access (`obj.secret`), but is excluded from iteration and serialization.

```js
const config = { appName: "App" };

Object.defineProperty(config, "apiKey", {
  value: "SECRET_KEY_12345",
  enumerable: false // Hidden from serialization
});

console.log(config.apiKey); // "SECRET_KEY_12345" (Direct access works)
console.log(Object.keys(config)); // ["appName"] (apiKey omitted)
console.log(JSON.stringify(config)); // '{"appName":"App"}' (apiKey excluded!)
```

---

### 4. `configurable` (Data & Accessor Descriptors)
- **`true`**: The property can be deleted via `delete obj.prop`, and its descriptor attributes can be modified in the future.
- **`false`**: The property is **locked / non-configurable**:
  - Cannot be deleted (`delete obj.prop` throws `TypeError` in strict mode).
  - Cannot convert between Data and Accessor descriptors.
  - Cannot change `enumerable`.
  - Cannot change `configurable` from `false` back to `true`.
  - **The only permitted modification**: Changing `writable` from `true` to `false` (one-way lock).

```js
"use strict";
const secureObj = {};
Object.defineProperty(secureObj, "permanentId", {
  value: "UUID-9876",
  configurable: false
});

// delete secureObj.permanentId; // ❌ TypeError: Cannot delete property 'permanentId'
```

---

## 3) The Critical Default Values Trap

How a property is created drastically changes its default descriptor flags:

| Attribute | Literal Assignment (`obj.x = 10` or `{ x: 10 }`) | `Object.defineProperty(obj, 'x', { value: 10 })` |
| :--- | :--- | :--- |
| **`value`** | `10` | `10` |
| **`writable`** | ✅ **`true`** | ❌ **`false`** (Default) |
| **`enumerable`** | ✅ **`true`** | ❌ **`false`** (Default) |
| **`configurable`** | ✅ **`true`** | ❌ **`false`** (Default) |

> ⚠️ **CRITICAL GOTCHA**: When using `Object.defineProperty()` or `Object.create()`, omitted descriptor flags default to **`false`**, resulting in an immutable, hidden, non-deletable property!

---

## 4) Accessor Descriptors (Getters & Setters)

Accessor descriptors use `get` and `set` functions instead of a static `value` and `writable` flag:

```js
const userAccount = {
  firstName: "Shivam",
  lastName: "Poddar"
};

Object.defineProperty(userAccount, "fullName", {
  get() {
    return `${this.firstName} ${this.lastName}`;
  },
  set(nameStr) {
    const [first, last] = nameStr.split(" ");
    this.firstName = first || "";
    this.lastName = last || "";
  },
  enumerable: true,
  configurable: true
});

console.log(userAccount.fullName); // "Shivam Poddar"
userAccount.fullName = "Alice Cooper";
console.log(userAccount.firstName); // "Alice"
console.log(userAccount.lastName);  // "Cooper"
```

---

## 5) Inspecting & Cloning Descriptors with `getOwnPropertyDescriptors`

### Inspecting a Single Property:
```js
const item = { title: "Book", price: 20 };
const descriptor = Object.getOwnPropertyDescriptor(item, "title");
console.log(descriptor);
// { value: 'Book', writable: true, enumerable: true, configurable: true }
```

---

### The Spread / `Object.assign` Trap & The Fix (ES2017)

When you clone an object using `Object.assign({}, source)` or `{ ...source }`, it **executes getters and copies their static return values**, completely stripping the getter/setter functions and non-default descriptors!

```js
const original = {
  _temp: 25,
  get fahrenheit() {
    return (this._temp * 9) / 5 + 32;
  }
};

// ❌ Flawed Clone (Converts getter to static value 77):
const flawedClone = { ...original };
console.log(Object.getOwnPropertyDescriptor(flawedClone, "fahrenheit"));
// { value: 77, writable: true, enumerable: true, configurable: true }

// ✅ Perfect Deep Descriptor Clone (Preserves getters, setters, descriptors):
const perfectClone = Object.defineProperties(
  {},
  Object.getOwnPropertyDescriptors(original)
);
console.log(Object.getOwnPropertyDescriptor(perfectClone, "fahrenheit"));
// { get: [Function: get fahrenheit], set: undefined, enumerable: true, configurable: true }
```

---

## 6) Real-World Use Cases

---

### Use Case 1: Exposing Non-Enumerable Metadata
Hide internal state or database identifiers from logging and REST API JSON serialization:

```js
function createEntity(data, internalDbId) {
  const entity = { ...data };

  Object.defineProperty(entity, "__dbId", {
    value: internalDbId,
    writable: false,
    enumerable: false, // Hidden from JSON.stringify() and Object.keys()
    configurable: false
  });

  return entity;
}

const user = createEntity({ name: "Alice", role: "Dev" }, "raw-mongo-id-9982");
console.log(JSON.stringify(user)); // '{"name":"Alice","role":"Dev"}'
console.log(user.__dbId); // "raw-mongo-id-9982"
```

---

### Use Case 2: Building Observable Reactive Properties (Vue 2 Engine Pattern)

Before ES6 `Proxy`, reactive frameworks (like Vue.js 2.x) implemented reactivity by converting plain properties into reactive getters and setters using `Object.defineProperty`:

```js
function defineReactive(obj, key, val) {
  const subscribers = new Set();

  Object.defineProperty(obj, key, {
    get() {
      console.log(`[Reactivity Track] Property "${key}" read.`);
      return val;
    },
    set(newVal) {
      if (newVal === val) return;
      console.log(`[Reactivity Trigger] Property "${key}" changed from ${val} to ${newVal}`);
      val = newVal;
      subscribers.forEach((fn) => fn(val));
    },
    enumerable: true,
    configurable: true
  });
}

const state = {};
defineReactive(state, "count", 0);

state.count;     // [Reactivity Track] Property "count" read.
state.count = 5; // [Reactivity Trigger] Property "count" changed from 0 to 5
```

---

## 7) V8 Engine Internals: Hidden Classes & Shapes

Under the hood, JavaScript engines (such as Chrome's V8) optimize property lookups using **Hidden Classes (also known as Shapes or Maps)**:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        V8 SHAPE TRANSITION TREE                        │
│                                                                        │
│   const p1 = {};          ──▶ Shape 0 (Empty)                          │
│   p1.x = 5;               ──▶ Shape 1 (Offset 0: x)                    │
│   p1.y = 10;              ──▶ Shape 2 (Offset 0: x, Offset 1: y)       │
│                                                                        │
│   const p2 = {};          ──▶ Shape 0                                  │
│   p2.y = 10; // Out of order!                                          │
│                           ──▶ Shape 3 (Offset 0: y) ⚠️ NEW BRANCH!     │
└────────────────────────────────────────────────────────────────────────┘
```

### Engine Optimization Best Practices:
1. **Initialize properties in the same order**: Always declare object properties in consistent order so objects share identical Hidden Classes in V8.
2. **Avoid `delete obj.prop` in hot paths**: Deleting properties mutates the object shape into a slow hash-table dictionary mode (de-optimizing Inline Caching). Instead, assign `obj.prop = null` or `undefined`.
3. **Prefer object literals over frequent `Object.defineProperty` in loops**: Repeatedly calling `Object.defineProperty` slows down JIT shape transitions.

---

## 8) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                      PROPERTY DESCRIPTORS CHEAT SHEET                      |
+───────────────────────────+────────────────────────────────────────────────+
| Descriptor Attribute      | Purpose & Effect                               |
+───────────────────────────+────────────────────────────────────────────────+
| `value`                   | Data value stored in property.                 |
| `writable`                | If false, prevents reassignment (`obj.x = 2`). |
| `enumerable`              | If false, hides property from loops & JSON.    |
| `configurable`            | If false, prevents deletion and descriptor edit|
| `get` / `set`             | Accessor functions for dynamic properties.     |
| Literal Default           | All flags default to `true`.                   |
| `defineProperty` Default  | Unspecified flags default to `false`.          |
| Perfect Object Clone      | `Object.defineProperties({}, Object.getOwnPropertyDescriptors(src))` |
+───────────────────────────+────────────────────────────────────────────────+
```

# Shallow Copy vs. Deep Copy and `structuredClone` in JavaScript

---

## 1) Core Mental Model: The Memory Architecture of Copying

In JavaScript, primitive values (`number`, `string`, `boolean`, `null`, `undefined`, `symbol`, `bigint`) are stored and copied **by value**. 

Non-primitive objects (`objects`, `arrays`, `functions`, `dates`, `maps`) are stored in the **Memory Heap**, and variables only store a **reference pointer** to that memory location.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        REFERENCE vs SHALLOW vs DEEP                    │
│                                                                        │
│   1. Reference Assignment: `b = a`                                     │
│      Two variables point to the EXACT SAME object in Heap memory.      │
│      Mutating `b` mutates `a`.                                         │
│                                                                        │
│   2. Shallow Copy: `b = { ...a }`                                      │
│      Creates a new top-level object, but nested objects/arrays         │
│      still share the EXACT SAME reference pointers!                    │
│                                                                        │
│   3. Deep Copy: `b = structuredClone(a)`                               │
│      Recursively duplicates every single object, array, and nested     │
│      structure into completely independent memory locations.           │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Shallow Copying

A **Shallow Copy** duplicates only the top-level (first layer) properties of an object or array. If any property value is an object or array, only the reference pointer is copied.

```js
const original = {
  name: "Shivam",
  skills: ["JavaScript", "TypeScript"], // Nested reference!
  address: { city: "Bangalore", zip: "560001" } // Nested reference!
};

// Shallow copy using Object Spread
const shallow = { ...original };

// Top-level primitive modification is independent:
shallow.name = "Alice";
console.log(original.name); // "Shivam" (Untouched)

// ⚠️ BUG: Mutating nested objects mutates BOTH!
shallow.skills.push("Node.js");
shallow.address.city = "Mumbai";

console.log(original.skills);       // ["JavaScript", "TypeScript", "Node.js"] (Mutated!)
console.log(original.address.city); // "Mumbai" (Mutated!)
```

---

### Common Shallow Copy Techniques:

1. **Object Spread**: `{ ...source }`
2. **Array Spread**: `[ ...sourceArr ]`
3. **`Object.assign()`**: `Object.assign({}, source)`
4. **`Array.prototype.slice()`**: `sourceArr.slice()`
5. **`Array.from()`**: `Array.from(sourceArr)`

---

## 3) The Historic Deep Copy Hack: `JSON.parse(JSON.stringify())`

Before `structuredClone` was standardized, developers frequently used the JSON serialization trick for deep cloning:

```js
const deepCopy = JSON.parse(JSON.stringify(original));
```

---

### Why the JSON Trick is Flawed (The 8 Major Limitations)

| Data Type / Feature | JSON Trick Behavior | Why It Fails |
| :--- | :--- | :--- |
| **`Date` Objects** | Converts to ISO string (`"2026-09-10T01..."`) | Loses all `Date` prototype methods (`.getTime()`). |
| **`undefined`** | Property is completely **deleted / stripped** | JSON format does not support `undefined`. |
| **`NaN`, `Infinity`, `-Infinity`** | Converted to **`null`** | JSON spec lacks representation for `NaN`/`Infinity`. |
| **`RegExp` Objects** | Converted to empty object `{}` | RegExp patterns are discarded. |
| **`Map` & `Set`** | Converted to empty object `{}` | Internal map/set structures are lost. |
| **`Function` / Methods** | Completely **deleted / stripped** | Functions cannot be serialized to JSON. |
| **`Symbol` Keys & Values** | Completely **deleted / stripped** | Symbols cannot be serialized to JSON. |
| **Circular References** | ❌ **Throws Fatal `TypeError`** | `Converting circular structure to JSON`. |

```js
// Demonstration of JSON serialization data loss:
const problematic = {
  created: new Date(),
  pattern: /^[a-z]+$/,
  tags: new Set(["js", "node"]),
  missing: undefined,
  score: NaN,
  greet() { return "Hi"; }
};

const corrupted = JSON.parse(JSON.stringify(problematic));

console.log(typeof corrupted.created); // "string" (Lost Date object!)
console.log(corrupted.pattern);        // {} (Lost RegExp!)
console.log(corrupted.tags);           // {} (Lost Set!)
console.log(corrupted.missing);        // undefined (Key completely deleted!)
console.log(corrupted.score);          // null (NaN converted to null!)
console.log(corrupted.greet);          // undefined (Method deleted!)
```

---

## 4) Modern Native Deep Copy: `structuredClone()`

Standardized in HTML5 and ECMAScript runtime environments (available in Node.js 17+ and all modern browsers), `structuredClone()` provides **native, high-performance deep copying**.

```js
const complexData = {
  title: "Master JS",
  publishedAt: new Date("2026-01-15"),
  regexFilter: /^[0-9]+$/i,
  categories: new Set(["Engineering", "Architecture"]),
  metadata: new Map([["version", "2.1"], ["stable", true]]),
  scores: [100, 95, 88]
};

// Native Deep Clone
const cleanClone = structuredClone(complexData);

cleanClone.categories.add("Security");
cleanClone.scores.push(70);

console.log(cleanClone.publishedAt instanceof Date); // true
console.log(cleanClone.regexFilter instanceof RegExp); // true
console.log(complexData.categories.has("Security")); // false (Independent memory!)
console.log(complexData.scores.length); // 3 (Original untouched!)
```

---

### Circular References Handled Gracefully:

```js
const circularObj = { name: "Cycle" };
circularObj.self = circularObj; // Circular reference to itself

// structuredClone handles this natively without crashing:
const safeClone = structuredClone(circularObj);
console.log(safeClone.self === safeClone); // true
console.log(safeClone !== circularObj);     // true (Independent copy)
```

---

### What `structuredClone()` CANNOT Clone:

1. **Functions / Methods**: Throws `DataCloneError: could not be cloned` (functions cannot be serialized across realms).
2. **DOM Nodes**: Cannot clone HTML elements.
3. **Property Descriptors**: Getters/setters are evaluated to static values, and `writable`/`enumerable` flags are not preserved.
4. **Prototypes**: Cloned objects lose custom prototype links and reset to `Object.prototype`.

---

## 5) Writing a Custom Production-Grade `deepClone()` Algorithm

In senior technical interviews, you will often be asked to implement a complete `deepClone` function that handles circular references, prototypes, symbols, and specialized data types:

```js
function deepClone(target, hash = new WeakMap()) {
  // 1. Handle Primitives & Functions
  if (target === null || typeof target !== "object") {
    return target;
  }

  // 2. Handle Dates
  if (target instanceof Date) {
    return new Date(target.getTime());
  }

  // 3. Handle RegExp
  if (target instanceof RegExp) {
    return new RegExp(target.source, target.flags);
  }

  // 4. Handle Circular References via WeakMap
  if (hash.has(target)) {
    return hash.get(target);
  }

  // 5. Handle Set
  if (target instanceof Set) {
    const cloneSet = new Set();
    hash.set(target, cloneSet);
    target.forEach((val) => cloneSet.add(deepClone(val, hash)));
    return cloneSet;
  }

  // 6. Handle Map
  if (target instanceof Map) {
    const cloneMap = new Map();
    hash.set(target, cloneMap);
    target.forEach((val, key) => cloneMap.set(deepClone(key, hash), deepClone(val, hash)));
    return cloneMap;
  }

  // 7. Handle Arrays and Plain/Custom Objects (Preserving Prototype)
  const cloneObj = Array.isArray(target)
    ? []
    : Object.create(Object.getPrototypeOf(target));

  hash.set(target, cloneObj);

  // 8. Copy all own property keys (including Symbols)
  const allKeys = Reflect.ownKeys(target);
  for (const key of allKeys) {
    const descriptor = Object.getOwnPropertyDescriptor(target, key);
    if (descriptor) {
      if ("value" in descriptor) {
        descriptor.value = deepClone(descriptor.value, hash);
      }
      Object.defineProperty(cloneObj, key, descriptor);
    }
  }

  return cloneObj;
}

// Verification:
const symKey = Symbol("secret");
const originalComplex = {
  num: 42,
  date: new Date(),
  set: new Set([1, 2, 3]),
  [symKey]: "Hidden Symbol Data"
};

const clonedResult = deepClone(originalComplex);
console.log(clonedResult[symKey]); // "Hidden Symbol Data"
console.log(clonedResult.date instanceof Date); // true
console.log(clonedResult.set !== originalComplex.set); // true
```

---

## 6) Master Strategy Matrix: When to Use What

| Technique | Speed | Nested Independence | Preserves Types (Date/Map/Set) | Handles Cycles | Clones Functions |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Object Spread `{...a}`** | ⚡ Fastest | ❌ No (Shallow) | N/A | N/A | ✅ (Reference copy) |
| **`JSON.parse(JSON.stringify())`** | 🐢 Slow | ✅ Yes | ❌ Corrupts / Strips | ❌ Crashes | ❌ Strips |
| **`structuredClone()`** | 🚀 Fast (Native C++) | ✅ Yes | ✅ Full Support | ✅ Handles | ❌ Throws Error |
| **Custom `deepClone()`** | ⚖️ Moderate | ✅ Yes | ✅ Full Support | ✅ (With WeakMap) | ✅ Configurable |

---

## 7) Decision Rule for Production Code

1. **Flat Data / React State Updates**: Use **Object Spread (`{ ...state }`)** for speed and clean syntax.
2. **Deep Plain Data / API Payloads**: Use **`structuredClone()`** as the modern standard.
3. **Complex Domain Objects with Classes / Methods / Symbols**: Use a **custom `deepClone()`** utility or specialized libraries like **Lodash `cloneDeep`** or **Immer** (for structural sharing).

---

## 8) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         COPYING IN JS CHEAT SHEET                          |
+───────────────────────────+────────────────────────────────────────────────+
| Shallow Copy              | `{ ...obj }` / `[ ...arr ]` (1 level deep).    |
| JSON Deep Copy Hack       | `JSON.parse(JSON.stringify(obj))` (Flawed).    |
| Native Deep Copy          | `structuredClone(obj)` (ES standard).          |
| Circular References       | Handled by `structuredClone` & `WeakMap`.      |
| Function Cloning          | Functions cannot be cloned; copy reference.    |
| React / Redux State       | Prefer shallow spread for top-level updates.   |
+───────────────────────────+────────────────────────────────────────────────+
```

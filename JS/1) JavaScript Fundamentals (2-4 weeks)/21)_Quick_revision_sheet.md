# Quick Revision Sheet: JavaScript Fundamentals (Phase 1)

This cheat sheet serves as an instant reference for core concepts, syntax, rules, and interview quick-facts covered in Phase 1.

---

## 1) Syntax, Variables & Scope

- **Expression vs. Statement**:
  - **Expression**: Evaluates to a single value (`2 + 3`, `age >= 18 ? "A" : "B"`, `fn()`).
  - **Statement**: Performs an action or controls flow (`if`, `for`, `let x = 10;`). Cannot be assigned to a variable.
- **`var` vs. `let` vs. `const`**:

| Feature | `var` | `let` | `const` |
| :--- | :--- | :--- | :--- |
| **Scope** | Function Scope | Block Scope | Block Scope |
| **Hoisting** | Hoisted with `undefined` | Hoisted in TDZ | Hoisted in TDZ |
| **Reassignment** | Allowed | Allowed | **Forbidden** |
| **Initial Value Required?** | No | No | **Yes** |

- **Temporal Dead Zone (TDZ)**: Period between entering scope and reaching `let`/`const` declaration. Accessing variables in TDZ throws `ReferenceError`.

---

## 2) Data Types & Memory

- **7 Primitive Types**: `string`, `number`, `bigint`, `boolean`, `undefined`, `null`, `symbol`.
  - Stored by **Value** on the Stack. Immutable.
- **Reference Types**: `object` (Objects, Arrays, Functions, Dates, Maps, Sets).
  - Stored by **Reference** in Heap memory. Mutable.
- **`typeof` Quick Lookup**:
  - `typeof null` $\rightarrow$ `"object"` *(Historical JS quirk)*.
  - `typeof []` $\rightarrow$ `"object"` *(Use `Array.isArray()` instead!)*.
  - `typeof function(){}` $\rightarrow$ `"function"`.
  - `typeof NaN` $\rightarrow$ `"number"`.
- **Copying Objects**:
  - **Shallow Copy**: `{ ...obj }` or `Object.assign({}, obj)` (Nested references shared!).
  - **Deep Copy**: `structuredClone(obj)`.

---

## 3) Coercion, Equality & Operators

- **The 8 Falsy Values**: `false`, `0`, `-0`, `0n`, `""`, `null`, `undefined`, `NaN`. Everything else is **truthy** (including `[]` and `{}`).
- **Equality**:
  - **Strict (`===`)**: Checks type and value without coercion. *(Default choice)*.
  - **Loose (`==`)**: Triggers automatic coercion. *(Avoid, except `val == null` to catch both `null` and `undefined`)*.
- **Short-Circuit Operators**:
  - `a || b`: Returns first truthy value (Overrides `0`, `""`, `false`).
  - `a ?? b`: Returns `b` **only** if `a` is `null` or `undefined` (Preserves `0`, `""`, `false`).
  - `obj?.prop`: Optional Chaining; stops evaluation and returns `undefined` if `obj` is nullish.

---

## 4) Control Flow & Loops

- **Guard Clauses**: Return early from functions to eliminate deeply nested `if` blocks ("Pyramid of Doom").
- **`switch` Cases**: Uses strict equality (`===`). Wrap `case` blocks in `{}` if declaring `let` or `const` variables.
- **`for...of` vs. `for...in`**:
  - `for...of`: Iterates over **Values** of Iterables (Arrays, Strings, Maps, Sets). Supports `break`/`continue`.
  - `for...in`: Iterates over **Keys/Properties** of Objects. Avoid using on Arrays!

---

## 5) Functions & Scope Chain

- **Function Declarations**: `function foo() {}` — Fully hoisted along with body (callable before declaration).
- **Function Expressions**: `const foo = function() {}` — Variable hoisted in TDZ (not callable before declaration).
- **Arrow Functions**: `const foo = () => {}`:
  - **Lexical `this`**: Inherits `this` from surrounding scope (does NOT bind its own `this`).
  - Cannot be used as constructor functions (`new` throws `TypeError`).
  - Do NOT have their own `arguments` object (use rest parameter `...args`).
- **Pure Functions**:
  1. **Deterministic**: Same input always returns same output.
  2. **No Side Effects**: Does not mutate external variables, DOM, or perform I/O.

---

## 6) Collections Cheat Sheet (Arrays, Objects, Sets, Maps)

### Array Methods Matrix

| Method | Purpose | Returns | Mutates Original? |
| :--- | :--- | :--- | :--- |
| `map(fn)` | Transform elements 1-to-1 | New Array | **No** |
| `filter(fn)` | Extract elements passing condition | New Array | **No** |
| `reduce(fn, init)` | Accumulate elements to single value | Any Value | **No** |
| `find(fn)` / `findLast(fn)` | Locate matching element | Element / `undefined` | **No** |
| `some(fn)` / `every(fn)` | Check boolean conditions | Boolean | **No** |
| `sort(fn)` | Sort elements in place | Original Array | **Yes** *(Use `toSorted()` for immutable)* |
| `at(-1)` | Access negative index elements | Element | **No** |

### Object Utilities
- `Object.keys(obj)` $\rightarrow$ Array of property names.
- `Object.values(obj)` $\rightarrow$ Array of property values.
- `Object.entries(obj)` $\rightarrow$ Array of `[key, value]` pairs.
- `Object.fromEntries(entries)` $\rightarrow$ Reconstructs object from `[key, value]` array.
- `Object.hasOwn(obj, prop)` $\rightarrow$ Safer replacement for `hasOwnProperty`.

### `Set` & `Map`
- **`Set`**: Collection of unique values. Deduplicate array: `[...new Set(arr)]`.
  - *ES2024 Operations*: `setA.intersection(setB)`, `setA.union(setB)`, `setA.difference(setB)`.
- **`Map`**: Key-value collection supporting **any data type as keys** (objects, functions). Preserves insertion order, direct `.size` property.

---

## 7) Built-in Utilities & Error Handling

- **Numbers**:
  - `parseInt(str, 10)`: Always specify radix `10`.
  - `toFixed(digits)`: Returns a **string** representation of rounded decimal.
  - `Number.isNaN(val)`: Reliable check for `NaN` (`NaN === NaN` is false!).
- **`Math`**: `Math.floor()`, `Math.ceil()`, `Math.round()`, `Math.trunc()`.
  - Random Integer Formula `[min, max]`: `Math.floor(Math.random() * (max - min + 1)) + min`.
- **Dates**:
  - `new Date().getMonth()` is **0-indexed** (0 = January, 11 = December).
  - Use `.toISOString()` for data transfer and `Intl.DateTimeFormat` for UI formatting.
- **Error Handling**:
  - `try...catch...finally`: `finally` always runs (avoid returning values inside `finally`).
  - Always throw instances of `Error` or custom subclasses (`class MyError extends Error {}`).

---

## 8) Rapid-Fire Interview Flashcards

1. **Q: Why does `0.1 + 0.2 !== 0.3` in JavaScript?**  
   *A*: Numbers use IEEE 754 64-bit binary floating-point representation, which cannot represent certain decimal fractions precisely.
2. **Q: What is the difference between `null` and `undefined`?**  
   *A*: `undefined` means a variable has been declared but not assigned a value (or a parameter/property is missing). `null` is an explicit intentional assignment representing "no value".
3. **Q: Why should you avoid `for...in` for Arrays?**  
   *A*: `for...in` iterates over string keys, includes prototype properties, and index order is not guaranteed.
4. **Q: What happens if you omit `initialValue` in `reduce()` on an empty array?**  
   *A*: JavaScript throws a runtime `TypeError`.
5. **Q: How do arrow functions handle `this`?**  
   *A*: Arrow functions do not have their own `this`. They capture `this` lexically from the surrounding scope at the time of creation.

# Data Types and Type System Basics

## 1) Overview of JavaScript's Type System

JavaScript is a **dynamically typed** and **weakly typed** language.
- **Dynamically Typed**: Variable declarations (`let`, `var`) are not bound to a specific data type. The type is attached to the *value*, not the variable name. A variable can hold a string at one moment and a number later.
- **Weakly Typed**: JavaScript performs automatic type conversion (coercion) when operators encounter mismatched types (e.g., `"5" - 2` evaluates to `3`).

```js
let data = "Hello"; // Currently a string
data = 42;          // Reassigned to a number (valid in JS)
```

---

## 2) Primitive vs. Reference Types

JavaScript categorizes all values into two main categories: **Primitives** and **Non-Primitives (Reference Types)**.

| Feature | Primitive Types | Non-Primitive / Reference Types |
| :--- | :--- | :--- |
| **Data Types** | `string`, `number`, `bigint`, `boolean`, `undefined`, `null`, `symbol` | `object` (Arrays, Functions, Objects, Dates, Maps, Sets, etc.) |
| **Storage** | Stored directly by **value** on the Stack | Stored by **reference** (address in Heap memory) |
| **Mutability** | **Immutable** (cannot be altered in place; operations return new values) | **Mutable** (properties/elements can be modified in place) |
| **Copy Behavior** | Copying creates a completely independent copy | Copying creates a duplicate reference pointing to the same memory location |

---

## 3) The 7 Primitive Types

### 1. `string`
Represents textual data. Strings are immutable sequences of UTF-16 code units.
```js
const greeting = "Hello, World!";
const multiline = `Template literals allow
multiline strings.`;
```

### 2. `number`
Represents both integer and floating-point numbers using the **IEEE 754 double-precision 64-bit format**.

#### Safe Integer Limits
JavaScript numbers lose precision for integers outside the safe range:
```js
console.log(Number.MAX_SAFE_INTEGER); //  9,007,199,254,740,991  (2^53 - 1)
console.log(Number.MIN_SAFE_INTEGER); // -9,007,199,254,740,991
```

#### Floating-Point Precision Quirk
```js
0.1 + 0.2; // 0.30000000000000004 (due to binary representation of decimals)
```

#### Special Numeric Values
- **`Infinity` & `-Infinity`**: Results of operations like dividing by zero (`1 / 0`).
- **`NaN` ("Not-a-Number")**: Represents an invalid numeric operation (e.g., `"abc" * 2`).
  - **Quirk**: `NaN` is the only value in JS that is not equal to itself (`NaN === NaN` evaluates to `false`).
  - **Best Practice**: Use `Number.isNaN(val)` rather than global `isNaN(val)`.

### 3. `bigint`
Represents integers of arbitrary precision beyond `Number.MAX_SAFE_INTEGER`. Created by appending `n` to an integer literal or calling `BigInt()`.
```js
const hugeInt = 9007199254740991000n;
const parsedBigInt = BigInt("9007199254740991000");

// Note: Cannot mix BigInt and Number in math operations directly without explicit conversion!
// 10n + 5; // TypeError: Cannot mix BigInt and other types
const result = 10n + BigInt(5); // 15n
```

### 4. `boolean`
Logical type with only two possible values: `true` and `false`.
```js
const isOnline = true;
const hasPermission = false;
```

### 5. `undefined`
Indicates that a variable has been declared but has not yet been assigned a value, or that a function parameter / object property is missing.
```js
let user;
console.log(user); // undefined

function show(param) {
  console.log(param); // undefined if argument omitted
}
```

### 6. `null`
Represents an **intentional absence of any object value**. Must be explicitly assigned by the developer.
```js
let selectedUser = null; // Currently no user selected
```

### 7. `symbol`
A unique and immutable primitive value used primarily as unique keys for object properties to prevent property name collisions.
```js
const id1 = Symbol("id");
const id2 = Symbol("id");
console.log(id1 === id2); // false (always unique)
```

---

## 4) Detailed Comparison: `undefined` vs. `null`

| Criteria | `undefined` | `null` |
| :--- | :--- | :--- |
| **Meaning** | Value is missing / uninitialized | Value is intentionally set to "empty" |
| **Assigned by** | JavaScript engine (default) | Developer |
| **`typeof` output** | `"undefined"` | `"object"` (historical JS bug) |
| **Number Coercion** | `Number(undefined)` $\rightarrow$ `NaN` | `Number(null)` $\rightarrow$ `0` |
| **Equality** | `null == undefined` $\rightarrow$ `true` | `null === undefined` $\rightarrow$ `false` |

```js
// Coercion behaviors
console.log(5 + undefined); // NaN
console.log(5 + null);      // 5
```

---

## 5) The `typeof` Operator and Its Quirks

The `typeof` operator returns a string indicating the type of the unevaluated operand.

### `typeof` Reference Table
| Operand | `typeof` Result | Notes / Edge Cases |
| :--- | :--- | :--- |
| `"Hello"` | `"string"` | String primitive |
| `42` | `"number"` | Number primitive |
| `10n` | `"bigint"` | BigInt primitive |
| `true` | `"boolean"` | Boolean primitive |
| `undefined` | `"undefined"` | Undefined primitive |
| `Symbol("id")` | `"symbol"` | Symbol primitive |
| `{ name: "A" }` | `"object"` | Plain Object |
| `[1, 2, 3]` | `"object"` | **Arrays return `"object"`!** |
| `null` | `"object"` | **Historical JS bug!** |
| `function() {}` | `"function"` | Special case for executable objects |

### Correct Type Verification Helpers
To accurately check non-primitive types:
```js
// Checking for Arrays
Array.isArray([1, 2, 3]); // true

// Checking for Null safely
const isNull = (val) => val === null;

// Checking for Plain Objects
const isPlainObject = (val) => val !== null && typeof val === "object" && !Array.isArray(val);
```

---

## 6) Memory Allocation: Value vs. Reference Behavior

### Primitive Value Copying (Stack Memory)
Primitives are passed by **value**. Modifying the copy does not affect the original.

```js
let x = 10;
let y = x; // Copy of value 10 created
y = 20;

console.log(x); // 10 (Original unchanged)
console.log(y); // 20
```

### Reference Copying (Heap Memory)
Objects are passed by **reference**. Multiple variables share the same pointer to the heap object.

```js
const person1 = { name: "Alice" };
const person2 = person1; // Copying reference address

person2.name = "Bob";

console.log(person1.name); // "Bob" (Original modified!)
console.log(person2.name); // "Bob"
```

### Shallow vs. Deep Copying
To duplicate an object without keeping shared references:

```js
const original = { a: 1, nested: { b: 2 } };

// 1. Shallow Copy (Spread operator) - copies top level only
const shallowCopy = { ...original };
shallowCopy.a = 99;         // original.a remains 1
shallowCopy.nested.b = 88;  // original.nested.b becomes 88 (shared reference!)

// 2. Deep Copy (Modern structuredClone API)
const deepCopy = structuredClone(original);
deepCopy.nested.b = 999;    // original.nested.b remains untouched
```

---

## 7) Best Practices
1. **Never use Primitive Wrapper Objects**: Avoid `new String()`, `new Number()`, or `new Boolean()`. Use primitive literals instead.
2. **Use `===` (Strict Equality)**: Avoid loose equality `==` to prevent unexpected type coercion.
3. **Use `Array.isArray()`**: Do not rely on `typeof` when checking arrays.
4. **Use `Number.isNaN()`**: Always use `Number.isNaN()` to check for `NaN`.
5. **Use `structuredClone()` for Deep Copies**: Avoid hacky `JSON.parse(JSON.stringify())` when copying objects containing Dates, Maps, Sets, or undefined properties.

---

## 8) Quick Exercises & Practice

### Exercise 1: Predict the `typeof` Output
What will `typeof` return for each expression?
1. `typeof NaN`
2. `typeof (1 / 0)`
3. `typeof null`
4. `typeof []`
5. `typeof (typeof 42)`

<details>
<summary>View Solutions</summary>

1. `"number"` (`NaN` is a numeric type value)
2. `"number"` (`Infinity` is a numeric type value)
3. `"object"` (Historical JS quirk)
4. `"object"` (Arrays are reference objects)
5. `"string"` (`typeof 42` returns `"number"`, and `typeof "number"` is `"string"`)
</details>

---

### Exercise 2: Value vs. Reference Prediction
What will be logged to the console?

```js
let str1 = "JavaScript";
let str2 = str1;
str2.toUpperCase(); // Note: String methods return new strings

console.log(str1);

const objA = { count: 5 };
const objB = objA;
objB.count += 5;

console.log(objA.count);
```

<details>
<summary>View Solutions</summary>

- `str1`: `"JavaScript"` (Strings are primitive and immutable; `.toUpperCase()` returns a new string but doesn't mutate `str1` or `str2`).
- `objA.count`: `10` (`objA` and `objB` reference the exact same object in memory).
</details>

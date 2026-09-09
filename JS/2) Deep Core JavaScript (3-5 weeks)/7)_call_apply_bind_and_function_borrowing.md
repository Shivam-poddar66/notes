# `call`, `apply`, `bind`, and Function Borrowing in JavaScript

---

## 1) Core Mental Model: Explicit Binding

In JavaScript, functions are first-class objects inheriting from `Function.prototype`. Built into `Function.prototype` are three essential methods that allow you to explicitly control what `this` refers to during function execution:

1. **`Function.prototype.call()`**: Invokes the function **immediately**, passing arguments individually as a comma-separated list.
2. **`Function.prototype.apply()`**: Invokes the function **immediately**, passing arguments as an **array or array-like object**.
3. **`Function.prototype.bind()`**: Does **NOT** invoke the function immediately. Instead, it returns a **new function** with `this` permanently locked to the provided context, optionally pre-filling initial arguments (**Partial Application**).

```
┌───────────────────────────────────────────────────────────────────────────┐
│                           EXPLICIT BINDING METHODS                        │
│                                                                           │
│   Method      Execution Timing     Argument Format      Return Value      │
│   ──────      ────────────────     ───────────────      ────────────      │
│   .call()     Immediate            Comma-separated      Function result   │
│   .apply()    Immediate            Array / Arguments    Function result   │
│   .bind()     Deferred (Lazy)      Comma-separated      Brand-new Function│
└───────────────────────────────────────────────────────────────────────────┘
```

---

## 2) `Function.prototype.call()`

### Syntax:
```js
func.call(thisArg, arg1, arg2, /* ..., */ argN);
```

### Example:
```js
function printInvoice(taxRate, currency) {
  const tax = this.subtotal * taxRate;
  const total = this.subtotal + tax;
  return `${this.client}: ${currency}${total.toFixed(2)} (Tax: ${currency}${tax.toFixed(2)})`;
}

const invoiceA = { client: "Acme Corp", subtotal: 1000 };
const invoiceB = { client: "Globex Inc", subtotal: 2500 };

console.log(printInvoice.call(invoiceA, 0.1, "$")); // "Acme Corp: $1100.00 (Tax: $100.00)"
console.log(printInvoice.call(invoiceB, 0.2, "€")); // "Globex Inc: €3000.00 (Tax: €500.00)"
```

### Behavior with `null`, `undefined`, and Primitives:
- **Non-Strict Mode**:
  - Passing `null` or `undefined` as `thisArg` defaults to `window` (or `global`).
  - Passing a primitive (number, string, boolean) automatically **boxes** it into its Object wrapper (`Number`, `String`, `Boolean`).
- **Strict Mode (`"use strict"`)**:
  - `this` remains exactly what was passed (`null`, `undefined`, or the raw primitive value).

---

## 3) `Function.prototype.apply()`

### Syntax:
```js
func.apply(thisArg, [argsArray]);
```

`apply()` is identical to `call()`, except that parameters are supplied as a single array.

```js
const args = [0.15, "£"];
console.log(printInvoice.apply(invoiceA, args)); // "Acme Corp: £1150.00 (Tax: £150.00)"
```

---

### Historic Uses of `apply()` vs. Modern ES6 Spread (`...`)

Before the ES6 spread operator (`...`) was introduced in 2015, `apply()` was the standard method for passing arrays to functions accepting variable arguments:

```js
const numbers = [14, 58, 20, 77, 3];

// 1. Historic ES5 Approach using apply:
const maxES5 = Math.max.apply(null, numbers); // 77

// 2. Modern ES6 Spread Approach:
const maxES6 = Math.max(...numbers); // 77

// 3. Merging Arrays into an existing array in ES5:
const target = [1, 2];
const source = [3, 4, 5];
Array.prototype.push.apply(target, source); // target is now [1, 2, 3, 4, 5]

// Modern ES6:
target.push(...source);
```

---

## 4) `Function.prototype.bind()` & Partial Application

### Syntax:
```js
const boundFunc = func.bind(thisArg, arg1, /* ..., */ argN);
```

`bind()` creates a new **bound function** that wraps the original function object. Calling the bound function will execute the wrapped function with `this` locked to `thisArg`.

---

### Feature A: Preserving Context Across Callbacks

```js
class NotificationService {
  constructor(serviceName) {
    this.serviceName = serviceName;
  }

  send(message) {
    console.log(`[${this.serviceName}] Sending: ${message}`);
  }

  startHeartbeat() {
    // ⚠️ Without bind, 'this' inside send() would be window / Timeout object!
    setInterval(this.send.bind(this, "Heartbeat OK"), 3000);
  }
}

const service = new NotificationService("AuthService");
// service.startHeartbeat(); // Logs "[AuthService] Sending: Heartbeat OK" every 3s
```

---

### Feature B: Partial Application (Pre-configuring Arguments)

`bind()` allows you to preset initial arguments. When the bound function is later invoked, newly supplied arguments are appended after the preset ones:

```js
function calculateTotal(taxRate, shippingCost, itemPrice) {
  const tax = itemPrice * taxRate;
  return itemPrice + tax + shippingCost;
}

// Preset taxRate = 0.08 (8%) and shippingCost = 15
const calcStandardOrder = calculateTotal.bind(null, 0.08, 15);

// Only need to supply the remaining argument: itemPrice
console.log(calcStandardOrder(100)); // 100 + 8 + 15 = 123
console.log(calcStandardOrder(200)); // 200 + 16 + 15 = 231
```

---

### The "Double Bind" Trap (Multiple `.bind()` Calls)

**Rule: A function can only be bound ONCE.** Subsequent `.bind()` calls can preset additional arguments, but they **cannot change the original `this` binding**.

```js
function showName() {
  console.log(this.name);
}

const user1 = { name: "Alice" };
const user2 = { name: "Bob" };

const bound1 = showName.bind(user1);
const bound2 = bound1.bind(user2); // Attempting to re-bind to user2

bound2(); // Output: "Alice" (user1 ALWAYS WINS)
```

*Why?* The bound function returned by `bound1` is a wrapper that internally calls `showName.call(user1)`. Calling `bound2()` simply wraps the wrapper; when executed, the innermost wrapper still calls `showName` with `user1`.

---

## 5) Function Borrowing: Real-World Patterns

Function Borrowing is an OOP pattern where an object borrows a method from another object or prototype without inheriting from it.

---

### Pattern 1: Safe `hasOwnProperty` Inspection

Never call `obj.hasOwnProperty(key)` directly on user-provided or API objects, because:
1. The object might have `Object.create(null)` as its prototype (no `hasOwnProperty` on prototype).
2. The property `hasOwnProperty` might be shadowed/overridden (`const obj = { hasOwnProperty: false }`).

```js
const unsafePayload = Object.create(null);
unsafePayload.role = "admin";

// ❌ Throws TypeError: unsafePayload.hasOwnProperty is not a function
// unsafePayload.hasOwnProperty("role");

// ✅ Safe Function Borrowing:
const hasRole = Object.prototype.hasOwnProperty.call(unsafePayload, "role");
console.log(hasRole); // true

// Modern Alternative (ES2022+):
console.log(Object.hasOwn(unsafePayload, "role")); // true
```

---

### Pattern 2: Borrowing Array Methods for Array-Like Objects

Array-like objects have integer indices and a `.length` property, but lack `Array.prototype` methods (e.g. `arguments`, DOM `NodeList`):

```js
function processArguments() {
  // Borrow Array.prototype.slice to convert arguments into a real Array
  const argsArray = Array.prototype.slice.call(arguments);
  
  // Borrow Array.prototype.join
  const joined = Array.prototype.join.call(arguments, " - ");
  
  return joined;
}

console.log(processArguments("Alpha", "Beta", "Gamma")); // "Alpha - Beta - Gamma"
```

---

### Pattern 3: Borrowing Methods Across Different Domain Models

```js
const car = {
  brand: "Tesla",
  speed: 120,
  accelerate(amount) {
    this.speed += amount;
    console.log(`${this.brand} speed: ${this.speed} km/h`);
  }
};

const boat = {
  brand: "Sunseeker",
  speed: 30
};

// Boat borrows the car's accelerate method
car.accelerate.call(boat, 15); // "Sunseeker speed: 45 km/h"
console.log(boat.speed); // 45
```

---

## 6) Writing Polyfills for `call`, `apply`, and `bind`

Implementing these polyfills from scratch is a favorite senior-level JavaScript interview question.

---

### Polyfill: `Function.prototype.myCall`

```js
Function.prototype.myCall = function (context, ...args) {
  // 1. Fallback to globalThis if context is null or undefined
  context = context ?? globalThis;

  // 2. Wrap primitive context in Object
  context = Object(context);

  // 3. Create a unique symbol property to avoid overriding existing properties
  const uniqueKey = Symbol("fn");

  // 4. Attach function to context as a temporary method
  context[uniqueKey] = this;

  // 5. Invoke function (Implicit Binding applies!)
  const result = context[uniqueKey](...args);

  // 6. Clean up temporary property
  delete context[uniqueKey];

  return result;
};

// Verification:
function greet(greeting) {
  return `${greeting}, ${this.name}!`;
}
console.log(greet.myCall({ name: "Shivam" }, "Hello")); // "Hello, Shivam!"
```

---

### Polyfill: `Function.prototype.myApply`

```js
Function.prototype.myApply = function (context, argsArray = []) {
  context = context ?? globalThis;
  context = Object(context);

  if (!Array.isArray(argsArray) && typeof argsArray[Symbol.iterator] !== "function") {
    throw new TypeError("CreateListFromArrayLike called on non-object");
  }

  const uniqueKey = Symbol("fn");
  context[uniqueKey] = this;

  const result = context[uniqueKey](...argsArray);
  delete context[uniqueKey];

  return result;
};

// Verification:
console.log(greet.myApply({ name: "Shivam" }, ["Hey"])); // "Hey, Shivam!"
```

---

### Polyfill: `Function.prototype.myBind` (Production-Grade with `new` Support)

A true spec-compliant `bind` polyfill must also handle cases where the bound function is invoked as a constructor with `new`:

```js
Function.prototype.myBind = function (context, ...boundArgs) {
  const originalFn = this;

  if (typeof originalFn !== "function") {
    throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
  }

  function boundFunction(...callArgs) {
    // If called with 'new boundFunction()', 'this' is an instance of boundFunction.
    // In that case, use the newly constructed instance as 'this' instead of the bound context!
    const isCalledWithNew = this instanceof boundFunction;
    const effectiveContext = isCalledWithNew ? this : context;

    return originalFn.apply(effectiveContext, [...boundArgs, ...callArgs]);
  }

  // Preserve prototype chain for 'new' constructor instances
  if (originalFn.prototype) {
    boundFunction.prototype = Object.create(originalFn.prototype);
    boundFunction.prototype.constructor = boundFunction;
  }

  return boundFunction;
};

// Verification:
function Person(role, name) {
  this.role = role;
  this.name = name;
}

const DevConstructor = Person.myBind(null, "Developer");
const devInstance = new DevConstructor("Alice");

console.log(devInstance.role); // "Developer"
console.log(devInstance.name); // "Alice"
console.log(devInstance instanceof Person); // true
```

---

## 7) Quick Comparison Matrix & Summary

```
+────────────────────────────────────────────────────────────────────────────+
|                        EXPLICIT BINDING COMPARISON                         |
+────────────────────+────────────────────────+──────────────────────────────+
| Method             | Invocation Timing      | Argument Passing             |
+────────────────────+────────────────────────+──────────────────────────────+
| fn.call(ctx, a, b) | Immediate              | Individual comma-separated   |
| fn.apply(ctx, [a]) | Immediate              | Array or array-like list     |
| fn.bind(ctx, a)    | Lazy (Returns new Fn)  | Partial application + later  |
+────────────────────+────────────────────────+──────────────────────────────+
```

### Key Takeaways:
1. Use **`call`** when you want to execute a function immediately with a custom `this` and know arguments individually.
2. Use **`apply`** when arguments are already in an array or list.
3. Use **`bind`** when creating event listeners, deferred callbacks, or partially applying arguments.
4. **`bind` is immutable**: Only the first `.bind(context)` sets `this`.
5. **Function Borrowing** is ideal for safely calling prototype utilities (like `Object.prototype.hasOwnProperty.call(obj, prop)`).

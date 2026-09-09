# `Object.freeze`, `Object.seal`, `Object.preventExtensions`, and Immutability in JavaScript

---

## 1) Core Mental Model: The 3 Object Integrity Levels

JavaScript provides three progressive levels of object integrity and immutability. Each level builds upon the previous one by systematically tightening the object's internal **`[[Extensible]]`** slot and its **Property Descriptors**:

```
┌────────────────────────────────────────────────────────────────────────┐
│                      THE OBJECT INTEGRITY SPECTRUM                     │
│                                                                        │
│   Level 1: Object.preventExtensions(obj)  [Least Restrictive]          │
│     ❌ Cannot add new properties                                       │
│     ✅ Can modify existing values                                      │
│     ✅ Can delete existing properties                                  │
│                                                                        │
│   Level 2: Object.seal(obj)                                            │
│     ❌ Cannot add new properties                                       │
│     ✅ Can modify existing values (if writable)                        │
│     ❌ Cannot delete existing properties (`configurable: false`)       │
│                                                                        │
│   Level 3: Object.freeze(obj)             [Most Restrictive]           │
│     ❌ Cannot add new properties                                       │
│     ❌ Cannot modify existing values (`writable: false`)               │
│     ❌ Cannot delete existing properties (`configurable: false`)       │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Master Comparison Matrix

| Operation | Standard Object | `preventExtensions()` | `seal()` | `freeze()` |
| :--- | :--- | :--- | :--- | :--- |
| **Add New Properties** | ✅ Allowed | ❌ **Forbidden** | ❌ **Forbidden** | ❌ **Forbidden** |
| **Modify Existing Values** | ✅ Allowed | ✅ Allowed | ✅ Allowed | ❌ **Forbidden** |
| **Delete Properties** | ✅ Allowed | ✅ Allowed | ❌ **Forbidden** | ❌ **Forbidden** |
| **Reconfigure Descriptors** | ✅ Allowed | ✅ Allowed | ❌ **Forbidden** | ❌ **Forbidden** |
| **Change `[[Prototype]]`** | ✅ Allowed | ❌ **Forbidden** | ❌ **Forbidden** | ❌ **Forbidden** |
| **Inspection Method** | `Object.isExtensible(obj)` | `!Object.isExtensible(obj)` | `Object.isSealed(obj)` | `Object.isFrozen(obj)` |

---

## 3) Detailed Breakdown of Each Method

---

### 1. `Object.preventExtensions(obj)`

Prevents any new properties from ever being added to the object. It sets the internal `[[Extensible]]` slot to `false`.

```js
"use strict";
const user = { name: "Shivam", role: "Admin" };

Object.preventExtensions(user);
console.log(Object.isExtensible(user)); // false

// 1. Modifying existing properties -> ALLOWED
user.name = "Shivam Poddar"; 
console.log(user.name); // "Shivam Poddar"

// 2. Deleting properties -> ALLOWED
delete user.role;
console.log(user.role); // undefined

// 3. Adding new properties -> FORBIDDEN
// user.age = 28; // ❌ TypeError: Cannot add property age, object is not extensible
```

---

### 2. `Object.seal(obj)`

Seals an object by preventing new properties from being added and marking all existing properties as **`configurable: false`**.

```js
"use strict";
const bankCard = { cardNumber: "4111-2222-3333-4444", balance: 500 };

Object.seal(bankCard);
console.log(Object.isSealed(bankCard)); // true

// 1. Modifying existing properties -> ALLOWED (writable is still true)
bankCard.balance = 750;
console.log(bankCard.balance); // 750

// 2. Deleting properties -> FORBIDDEN (configurable is false)
// delete bankCard.cardNumber; // ❌ TypeError: Cannot delete property 'cardNumber'

// 3. Adding new properties -> FORBIDDEN
// bankCard.expiry = "12/28"; // ❌ TypeError: Cannot add property expiry
```

---

### 3. `Object.freeze(obj)`

Freezes an object to achieve complete **shallow immutability**. It marks all existing properties as **`writable: false`** and **`configurable: false`**, and prevents extensions.

```js
"use strict";
const appConfig = {
  apiBaseUrl: "https://api.example.com",
  timeoutMs: 5000
};

Object.freeze(appConfig);
console.log(Object.isFrozen(appConfig)); // true

// 1. Modifying values -> FORBIDDEN
// appConfig.timeoutMs = 10000; // ❌ TypeError: Cannot assign to read only property 'timeoutMs'

// 2. Deleting properties -> FORBIDDEN
// delete appConfig.apiBaseUrl; // ❌ TypeError: Cannot delete property 'apiBaseUrl'

// 3. Adding properties -> FORBIDDEN
// appConfig.retries = 3; // ❌ TypeError: Cannot add property retries
```

---

## 4) `const` vs. `Object.freeze()`: The Critical Difference

Developers frequently confuse the `const` keyword with `Object.freeze()`:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        CONST vs. OBJECT.FREEZE                         │
│                                                                        │
│   `const` protects the VARIABLE BINDING (Identifier pointer)           │
│     const user = { name: "Alice" };                                    │
│     user = { name: "Bob" }; // ❌ Reassignment blocked!                │
│     user.name = "Bob";      // ✅ Mutation ALLOWED!                    │
│                                                                        │
│   `Object.freeze()` protects the OBJECT VALUE in Heap memory           │
│     let user = Object.freeze({ name: "Alice" });                       │
│     user.name = "Bob";      // ❌ Mutation blocked!                    │
│     user = { name: "Bob" }; // ✅ Variable reassignment ALLOWED!       │
│                                                                        │
│   TRUE IMMUTABLE CONSTANT: Combine BOTH!                               │
│     const CONFIG = Object.freeze({ ... });                             │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 5) The "Shallow Immutability" Trap & `deepFreeze`

`Object.freeze()` is strictly **shallow**. It freezes only the top-level property references of the object. Any nested objects, arrays, or dates remain completely mutable!

```js
const userProfile = Object.freeze({
  username: "shivam66",
  preferences: {
    theme: "dark", // Nested object!
    notifications: true
  }
});

// Top level is frozen:
// userProfile.username = "hacker"; // ❌ TypeError!

// ⚠️ BUG: Nested object is NOT frozen!
userProfile.preferences.theme = "light"; // ✅ Mutates successfully!
console.log(userProfile.preferences.theme); // "light"
```

---

### Production-Grade `deepFreeze` Implementation (Handling Circular References)

To make an entire nested object hierarchy immutable, you must recursively freeze all child objects while protecting against circular references:

```js
function deepFreeze(obj, seen = new WeakSet()) {
  // 1. If primitive or null, return immediately
  if (obj === null || (typeof obj !== "object" && typeof obj !== "function")) {
    return obj;
  }

  // 2. Prevent infinite loops from circular references
  if (seen.has(obj)) {
    return obj;
  }
  seen.add(obj);

  // 3. Freeze all own property objects recursively
  const propNames = Reflect.ownKeys(obj);

  for (const name of propNames) {
    const value = obj[name];
    if (value !== null && (typeof value === "object" || typeof value === "function")) {
      deepFreeze(value, seen);
    }
  }

  // 4. Freeze the object itself
  return Object.freeze(obj);
}

// Verification:
const complexState = deepFreeze({
  appId: "APP-01",
  security: {
    roles: ["ADMIN", "USER"],
    jwt: { secret: "12345" }
  }
});

// complexState.security.jwt.secret = "HACKED"; // ❌ TypeError: Cannot assign to read only property
// complexState.security.roles.push("SUPERADMIN"); // ❌ TypeError: Cannot add property 2, object is not extensible
```

---

## 6) Freezing Arrays & Gotchas

Arrays are objects in JavaScript, so freezing an array disables all mutator methods (`push`, `pop`, `splice`, `sort`, `reverse`, `shift`, `unshift`):

```js
"use strict";
const numbers = Object.freeze([1, 2, 3]);

// numbers[0] = 99;   // ❌ TypeError: Cannot assign to read only property '0'
// numbers.push(4);   // ❌ TypeError: Cannot add property 3, object is not extensible
// numbers.sort();    // ❌ TypeError: Cannot assign to read only property
```

---

## 7) Accessor Descriptors on Frozen Objects

> ⚠️ **Edge Case**: `Object.freeze()` prevents properties from being converted to accessors or having their `set` modified. However, **if an object already has a setter function before freezing, invoking the setter WILL still execute its code!**

```js
let internalCounter = 0;

const reactiveObj = {
  get count() { return internalCounter; },
  set count(v) { internalCounter = v; }
};

Object.freeze(reactiveObj);

reactiveObj.count = 42; // Invokes the existing setter function!
console.log(reactiveObj.count); // 42 (Mutated external state via setter!)
```

---

## 8) Immutability in Modern Frameworks (React / Redux Patterns)

In modern web development, state is treated as immutable. Instead of mutating frozen state, produce brand-new state snapshots using non-mutating operations:

```js
// 1. Updating object properties immutably:
const prevState = { user: "Shivam", count: 1 };
const nextState = { ...prevState, count: prevState.count + 1 };

// 2. Updating arrays immutably:
const prevItems = [1, 2, 3];
const addedItems = [...prevItems, 4];         // Append
const filteredItems = prevItems.filter(x => x !== 2); // Remove
const updatedItems = prevItems.map(x => x === 2 ? 20 : x); // Replace
```

---

## 9) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         IMMUTABILITY QUICK MATRIX                          |
+───────────────────────────+────────────────────────────────────────────────+
| Method                    | Behavior & Scope                               |
+───────────────────────────+────────────────────────────────────────────────+
| `preventExtensions(obj)`  | Blocks adding new keys; edits/deletions ok.    |
| `seal(obj)`               | Blocks adding/deleting keys; value edits ok.   |
| `freeze(obj)`             | Blocks adding, deleting, and editing values.   |
| `isFrozen(obj)`           | Checks if object is frozen.                    |
| Shallow Limitation        | `freeze` is shallow; nested objects stay free. |
| `deepFreeze` Pattern      | Recursive freeze with `WeakSet` circular guard.|
| `const` vs `freeze`       | `const` protects binding; `freeze` locks data. |
+───────────────────────────+────────────────────────────────────────────────+
```

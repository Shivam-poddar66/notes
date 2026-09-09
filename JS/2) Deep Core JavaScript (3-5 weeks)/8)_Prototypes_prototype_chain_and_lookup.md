# Prototypes, Prototype Chain, and Property Lookup in JavaScript

---

## 1) Core Mental Model: Prototypal Delegation

JavaScript does not feature classical class-based inheritance under the hood; instead, it uses **Prototypal Inheritance (Behavior Delegation)**.

When an object needs to access a property or method that it does not possess directly, JavaScript **delegates** the search upward through a linked chain of prototype objects until it finds the property or reaches the end of the chain (`null`).

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          PROTOTYPE CHAIN DELEGATION                     │
│                                                                         │
│   [ dog instance ]                                                      │
│     - name: "Rex"                                                       │
│     - [[Prototype]] ──────▶ [ Animal.prototype ]                        │
│                               - sound: "Bark"                           │
│                               - speak() { ... }                         │
│                               - [[Prototype]] ──────▶ [ Object.prototype ]
│                                                         - toString()    │
│                                                         - hasOwnProperty│
│                                                         - [[Prototype]] │
│                                                                │        │
│                                                                ▼        │
│                                                              null       │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The Three "Prototypes": Disambiguation

One of the biggest sources of confusion in JavaScript is the overloaded term *"prototype"*. There are three distinct concepts:

| Term | What it is | Where it exists | Purpose |
| :--- | :--- | :--- | :--- |
| **`[[Prototype]]`** | Internal hidden slot (link) | Every JavaScript object | Points to the object's parent prototype from which it inherits properties. |
| **`__proto__`** | Accessor property (getter/setter) | Defined on `Object.prototype` | Legacy property to get/set `[[Prototype]]`. (Avoid in modern JS; use `Object.getPrototypeOf`). |
| **`prototype` property** | Plain object property | Only on **Constructor Functions** and **Classes** | The object that will become `[[Prototype]]` of instances created with `new Func()`. |

```
Constructor Function:
function Dog(name) { this.name = name; }
  │
  ├── Dog.prototype ───────────┐ (Serves as blueprint)
                               │
Instance:                      ▼
const myDog = new Dog("Buddy");
myDog.[[Prototype]] ───────────┘ (Points to Dog.prototype)
```

---

## 3) The Built-In Prototype Hierarchy

All standard data structures in JavaScript inherit through a standard prototype hierarchy ending at `Object.prototype`:

```
Array Instance `[]` ──▶ Array.prototype ──▶ Object.prototype ──▶ null
Function Instance `fn` ──▶ Function.prototype ──▶ Object.prototype ──▶ null
Date Instance `new Date()` ──▶ Date.prototype ──▶ Object.prototype ──▶ null
Plain Object `{}` ──▶ Object.prototype ──▶ null
Pure Dictionary `Object.create(null)` ──▶ null (No prototype!)
```

```js
const numbers = [1, 2, 3];

console.log(Object.getPrototypeOf(numbers) === Array.prototype); // true
console.log(Object.getPrototypeOf(Array.prototype) === Object.prototype); // true
console.log(Object.getPrototypeOf(Object.prototype)); // null (Root of chain)
```

---

## 4) Property Lookup Algorithm: Reading vs. Writing

JavaScript treats property **reads (`[[Get]]`)** and property **writes (`[[Set]]`)** very differently.

---

### A. Reading a Property (`[[Get]]`)

When evaluating `obj.prop`:
1. Check if `prop` exists as an **own property** on `obj`. If yes, return its value.
2. If not found, check `Object.getPrototypeOf(obj)`.
3. Traverse up the prototype chain step-by-step.
4. If the chain reaches `null` without finding `prop`, evaluate to `undefined`.

```js
const vehicle = { wheels: 4, drive() { return "Moving"; } };
const car = Object.create(vehicle);
car.brand = "Toyota";

console.log(car.brand);  // "Toyota" (Found on own object)
console.log(car.wheels); // 4 (Found via prototype delegation on 'vehicle')
console.log(car.flying); // undefined (Traversed to null, not found)
```

---

### B. Writing a Property (`[[Set]]`) & Property Shadowing

When executing `obj.prop = value`:
- By default, JavaScript creates or updates an **own property directly on `obj`**.
- It **does NOT mutate the prototype object**.
- If a property with the same name exists on the prototype, the new own property **shadows** (hides) the prototype property for that specific instance.

```js
const proto = { count: 10 };
const child = Object.create(proto);

console.log(child.count); // 10 (Delegated to proto)

// Writing to child
child.count = 25; // Creates an OWN property on 'child'!

console.log(child.count); // 25 (Reads own property)
console.log(proto.count); // 10 (Prototype remains untouched!)
console.log(Object.hasOwn(child, "count")); // true
```

> **Exception (Setters & Read-only Properties)**: If the prototype defines a setter (`set prop(v)`) or a non-writable property (`writable: false`), direct assignment invokes the setter or fails in strict mode.

---

## 5) Inspecting & Manipulating Prototypes

Modern JavaScript provides standard static methods on `Object` to inspect and interact with the prototype chain safely:

### 1. `Object.getPrototypeOf(obj)`
Returns the `[[Prototype]]` of the specified object.
```js
const arr = [];
console.log(Object.getPrototypeOf(arr) === Array.prototype); // true
```

### 2. `Object.setPrototypeOf(obj, newProto)`
Changes the prototype of an existing object.
> ⚠️ **Performance Warning**: Mutating `[[Prototype]]` on existing objects severely degrades V8 engine optimizations (de-optimizing inline caches). Always prefer `Object.create()` during object creation.

### 3. `Object.create(proto, [propertiesObject])`
Creates a new object with the specified prototype:
```js
const userMethods = {
  login() { console.log(`${this.username} logged in`); }
};

const user = Object.create(userMethods);
user.username = "Shivam";
user.login(); // "Shivam logged in"
```

### 4. `Object.hasOwn(obj, prop)` (ES2022 Standard)
Replaces legacy `obj.hasOwnProperty(prop)`. Returns `true` only if `prop` is an own property directly on `obj` (ignores the prototype chain).

```js
console.log(Object.hasOwn(user, "username")); // true
console.log(Object.hasOwn(user, "login"));    // false (inherited)
```

### 5. The `in` Operator vs `for...in` Loop
- `prop in obj`: Returns `true` if `prop` exists on `obj` **OR anywhere along its prototype chain**.
- `for (const key in obj)`: Iterates over all enumerable properties of `obj` **AND its prototype chain**.

```js
console.log("login" in user); // true (Found in prototype)

for (const key in user) {
  if (Object.hasOwn(user, key)) {
    console.log("Own:", key);       // "username"
  } else {
    console.log("Inherited:", key); // "login"
  }
}
```

---

## 6) Why Prototypes Matter: Memory Efficiency

Creating methods inside constructor functions duplicates functions across every instance, wasting heap memory. Attaching methods to `prototype` shares a single function reference across millions of instances.

```js
// ❌ Memory Inefficient: 1,000,000 separate function instances in Heap
function BadUser(name) {
  this.name = name;
  this.greet = function () { return `Hi, ${this.name}`; };
}

// ✅ Memory Efficient: Exactly 1 function instance shared via Prototype
function GoodUser(name) {
  this.name = name;
}
GoodUser.prototype.greet = function () {
  return `Hi, ${this.name}`;
};

const u1 = new GoodUser("Alice");
const u2 = new GoodUser("Bob");
console.log(u1.greet === u2.greet); // true (Points to identical memory address)
```

---

## 7) The `instanceof` Operator Mechanics

The expression `obj instanceof Constructor` checks whether `Constructor.prototype` exists anywhere along `obj`'s prototype chain:

```js
function CustomArray() {}
CustomArray.prototype = Object.create(Array.prototype);

const list = new CustomArray();

console.log(list instanceof CustomArray); // true
console.log(list instanceof Array);       // true
console.log(list instanceof Object);      // true
```

### How `instanceof` Works Algorithmically:
```js
function customInstanceOf(obj, constructor) {
  if (obj === null || typeof obj !== "object" && typeof obj !== "function") {
    return false;
  }
  
  let currentProto = Object.getPrototypeOf(obj);
  const targetProto = constructor.prototype;

  while (currentProto !== null) {
    if (currentProto === targetProto) return true;
    currentProto = Object.getPrototypeOf(currentProto);
  }
  return false;
}
```

---

## 8) Prototype Pollution & Security Vulnerabilities

**Prototype Pollution** is a critical security vulnerability where an attacker injects properties into `Object.prototype`, affecting all objects in the entire JavaScript runtime.

```js
// Vulnerable recursive merge function:
function unsafeMerge(target, source) {
  for (const key in source) {
    if (typeof target[key] === "object" && typeof source[key] === "object") {
      unsafeMerge(target[key], source[key]);
    } else {
      target[key] = source[key];
    }
  }
  return target;
}

// Attacker sends malicious payload:
const maliciousPayload = JSON.parse('{"__proto__": {"isAdmin": true}}');
unsafeMerge({}, maliciousPayload);

// Now ALL objects in the system inherit isAdmin = true!
const normalUser = {};
console.log(normalUser.isAdmin); // ⚠️ true! (Security compromised)
```

### Prevention Strategies:
1. **Use `Object.create(null)`** or `Map` for key-value dictionary stores with no prototype.
2. **Freeze `Object.prototype`**: `Object.freeze(Object.prototype)` prevents runtime prototype modifications.
3. **Filter dangerous keys**: Always block keys like `__proto__`, `constructor`, and `prototype` in merge/clone utilities.

---

## 9) Classic Mind-Bending Interview Questions

### Challenge 1: The Circular Relationship of `Function` and `Object`

```js
console.log(Function instanceof Object); // true
console.log(Object instanceof Function); // true
```

<details>
<summary><b>View Explanation & Prototype Graph</b></summary>

**Why?**
1. `Object` is a constructor **function**, so it inherits from `Function.prototype`:
   `Object.__proto__ === Function.prototype` (Hence `Object instanceof Function` is `true`).
2. `Function.prototype` is an **object**, so it inherits from `Object.prototype`:
   `Function.prototype.__proto__ === Object.prototype` (Hence `Function instanceof Object` is `true`).
3. `Object.prototype.__proto__` ends at `null`.
</details>

---

### Challenge 2: Modifying Prototype After Object Creation

```js
function Gadget() {}
Gadget.prototype.price = 100;

const g1 = new Gadget();

// Replacing the entire prototype object
Gadget.prototype = { price: 200 };

const g2 = new Gadget();

console.log(g1.price); // 100
console.log(g2.price); // 200
```

**Why?**
- `g1` was created when `Gadget.prototype` pointed to Object A (`price: 100`). `g1`'s internal `[[Prototype]]` link remains attached to Object A.
- Reassigning `Gadget.prototype` to Object B (`price: 200`) updates the constructor's blueprint for future instances without changing existing instances.

---

## 10) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         PROTOTYPES QUICK MATRIX                            |
+───────────────────────────+────────────────────────────────────────────────+
| Concept                   | Rule / Takeaway                                |
+───────────────────────────+────────────────────────────────────────────────+
| `[[Prototype]]`           | Hidden internal link on instances.             |
| `Constructor.prototype`   | Blueprint object for instances created via `new|
| `Object.prototype`        | Default root of the prototype chain.           |
| `Object.create(null)`     | Pure dictionary with NO prototype (`null`).    |
| `[[Get]]` lookup          | Searches instance -> prototype -> null.        |
| `[[Set]]` assignment      | Creates own property; shadows prototype prop.  |
| `Object.hasOwn(obj, k)`   | Checks own property (ignores prototype chain). |
| `instanceof`              | Checks if `Constructor.prototype` is in chain. |
+───────────────────────────+────────────────────────────────────────────────+
```

# ES6 Classes vs. the Prototypal Model in JavaScript

---

## 1) Core Mental Model: Syntactic Sugar over Prototypes

When ECMAScript 2015 (ES6) introduced the `class` keyword, it did **not** introduce a new classical object-oriented inheritance model to JavaScript.

Under the hood, **an ES6 Class is syntactic sugar over Constructor Functions and Prototypal Delegation**.

```js
class User {
  constructor(name) {
    this.name = name;
  }
  greet() {
    return `Hello, ${this.name}`;
  }
}

console.log(typeof User); // "function"
console.log(User.prototype.greet); // [Function: greet]
```

```
┌────────────────────────────────────────────────────────────────────────┐
│                        ES6 CLASS COMPILES TO:                          │
│                                                                        │
│   function User(name) {                                                │
│     this.name = name;                                                  │
│   }                                                                    │
│   User.prototype.greet = function() { return `Hello, ${this.name}`; }; │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Side-by-Side Comparison: ES6 Class vs. ES5 Prototype Model

| Feature | ES6 `class` Syntax | ES5 Constructor & Prototype Model |
| :--- | :--- | :--- |
| **Definition** | `class User { ... }` | `function User(name) { ... }` |
| **Method Attachment** | Defined directly inside class body | Attached to `User.prototype.method = ...` |
| **Inheritance Syntax** | `class Admin extends User { ... }` | `Admin.prototype = Object.create(User.prototype)` |
| **Super Call** | `super(name)` | `User.call(this, name)` |
| **Static Methods** | `static create() { ... }` | `User.create = function() { ... }` |
| **Private Fields** | `#privateField` (True hard privacy) | Closures or `_privateField` convention |
| **Invocation without `new`** | ❌ **Throws `TypeError`** | ⚠️ Runs as normal function (unless guarded) |
| **Hoisting** | ❌ **Temporal Dead Zone (TDZ)** | ✅ Hoisted with function body |
| **Strict Mode** | ✅ **Always Strict Mode** | ⚠️ Depends on file/function scope |
| **Method Enumerability** | ❌ `enumerable: false` on prototype | ⚠️ `enumerable: true` by default |

---

## 3) The 4 Critical Semantic Differences (Not Just "Sugar")

Although ES6 classes compile to prototypes, the JavaScript engine enforces 4 strict behavioral guarantees on classes:

---

### 1. Mandatory `new` Invocation
A class constructor **cannot** be called without the `new` keyword:

```js
class Vehicle {}
// Vehicle(); // ❌ TypeError: Class constructor Vehicle cannot be invoked without 'new'

function OldVehicle() {}
OldVehicle(); // ✅ Runs without error (sets 'this' to global in non-strict mode)
```

---

### 2. Temporal Dead Zone (TDZ) Hoisting
Function declarations are fully hoisted with their implementations. Classes are hoisted, but remain **uninitialized in the TDZ**:

```js
// new Car(); // ❌ ReferenceError: Cannot access 'Car' before initialization
class Car {}

new Bike(); // ✅ Works (Function declaration hoisted)
function Bike() {}
```

---

### 3. Automatic Strict Mode
The entire body of an ES6 `class` executes in **Strict Mode (`"use strict"`)** automatically, even if the surrounding file is in sloppy mode:

```js
class StrictByDefault {
  mistake() {
    undeclaredVar = 42; // ❌ ReferenceError: undeclaredVar is not defined
  }
}
```

---

### 4. Non-Enumerable Prototype Methods
Methods defined in an ES6 class have their property descriptor set to **`enumerable: false`** on the prototype object. This prevents class methods from polluting `for...in` loops and `Object.keys()`:

```js
class ModernUser {
  login() {}
}

function LegacyUser() {}
LegacyUser.prototype.login = function () {};

console.log(Object.keys(ModernUser.prototype)); // [] (login is non-enumerable)
console.log(Object.keys(LegacyUser.prototype)); // ["login"] (login IS enumerable)
```

---

## 4) Anatomy of Modern ES6+ Classes

Modern JavaScript classes (ES6 through ES2022) support rich object-oriented features:

```js
class BankAccount {
  // 1. Public Class Field (Attached to instance)
  currency = "USD";

  // 2. Private Field (Hard encapsulated; accessible ONLY inside this class)
  #balance = 0;

  // 3. Static Private Field
  static #totalAccountsCreated = 0;

  // 4. Constructor
  constructor(accountHolder, initialDeposit) {
    this.accountHolder = accountHolder;
    this.#balance = initialDeposit;
    BankAccount.#totalAccountsCreated++;
  }

  // 5. Getter & Setter (Accessor Properties on BankAccount.prototype)
  get balance() {
    return `${this.currency} $${this.#balance}`;
  }

  set balance(amount) {
    if (amount < 0) throw new Error("Balance cannot be negative");
    this.#balance = amount;
  }

  // 6. Instance Method (On BankAccount.prototype)
  deposit(amount) {
    this.#balance += amount;
    return this.#balance;
  }

  // 7. Private Method (Accessible ONLY inside this class)
  #logAudit(action) {
    console.log(`[AUDIT] ${this.accountHolder}: ${action}`);
  }

  // 8. Static Method (On Constructor Function itself)
  static getTotalAccounts() {
    return BankAccount.#totalAccountsCreated;
  }

  // 9. Static Initialization Block (ES2022)
  static {
    console.log("BankAccount class initialized in engine.");
  }
}

const myAcc = new BankAccount("Shivam", 1000);
console.log(myAcc.balance); // "USD $1000"
myAcc.deposit(500);
console.log(myAcc.balance); // "USD $1500"

// console.log(myAcc.#balance); // ❌ SyntaxError: Private field '#balance' must be declared in an enclosing class
console.log(BankAccount.getTotalAccounts()); // 1
```

---

## 5) Inheritance with `extends` and `super`

The `extends` keyword links both the **Instance Prototype Chain** and the **Static Constructor Chain**:

```
                                [ Function.prototype ]
                                          ▲
                                          │
                                   [ ParentClass ] ◀────── Subclass.__proto__
                                          │             (Inherits Static Methods)
                                          ▼
                               [ ParentClass.prototype ]
                                          ▲
                                          │ (Subclass.prototype.__proto__)
                                          │
                                   [ Subclass ]
                                          │
                                          ▼
                               [ Subclass.prototype ]
                                          ▲
                                          │ (instance.[[Prototype]])
                                          │
                                  [ childInstance ]
```

```js
class Employee {
  constructor(name, salary) {
    this.name = name;
    this.salary = salary;
  }

  getDetails() {
    return `${this.name} earns $${this.salary}`;
  }

  static companyRules() {
    return "Standard corporate guidelines apply.";
  }
}

class Manager extends Employee {
  constructor(name, salary, department) {
    // ⚠️ MUST call super() BEFORE accessing 'this'!
    super(name, salary);
    this.department = department;
  }

  // Method Overriding & Polymorphism
  getDetails() {
    // Calling super method
    return `${super.getDetails()} in Department: ${this.department}`;
  }
}

const mgr = new Manager("Alice", 120000, "Engineering");

console.log(mgr.getDetails()); // "Alice earns $120000 in Department: Engineering"
console.log(Manager.companyRules()); // "Standard corporate guidelines apply." (Static inheritance!)

console.log(mgr instanceof Manager);  // true
console.log(mgr instanceof Employee); // true
```

---

## 6) Class Field Arrow Functions vs. Prototype Methods

In modern React and JavaScript code, you will frequently see two ways to define methods:

```js
class ButtonHandler {
  // Approach A: Prototype Method
  handleStandard() {
    console.log(this);
  }

  // Approach B: Class Field Arrow Function (Auto-bound)
  handleArrow = () => {
    console.log(this);
  };
}
```

### The Performance & Memory Trade-Off:

| Dimension | Prototype Method (`fn() {}`) | Class Field Arrow (`fn = () => {}`) |
| :--- | :--- | :--- |
| **Where it is stored** | On `ButtonHandler.prototype` | Directly on each **instance (`this`)** |
| **Memory Allocation** | Exactly **1** function in heap | **$N$ functions** created for $N$ instances |
| **`this` Binding** | Dynamic (Can lose binding in callbacks) | Permanently auto-bound to instance |
| **`super` Support** | ✅ Supports `super.method()` | ❌ Does not support `super` calls |
| **Best Practice** | Default for 95% of methods | Use selectively for event handler callbacks |

---

## 7) Subclassing Built-In Objects (`Array`, `Error`)

ES6 classes enable true subclassing of built-in engine objects, which was impossible in ES5 without hacking `__proto__`:

```js
class CustomError extends Error {
  constructor(message, statusCode) {
    super(message);
    this.name = "CustomError";
    this.statusCode = statusCode;
    // Captures clean stack trace (V8 engine)
    if (Error.captureStackTrace) {
      Error.captureStackTrace(this, CustomError);
    }
  }
}

try {
  throw new CustomError("Unauthorized Access", 401);
} catch (err) {
  console.log(err.name);       // "CustomError"
  console.log(err.statusCode); // 401
  console.log(err instanceof Error); // true
}
```

---

## 8) Common Interview Traps & Gotchas

### Trap 1: Accessing `this` Before `super()` in Derived Constructors

```js
class Parent {}

class Child extends Parent {
  constructor(name) {
    // this.name = name; // ❌ ReferenceError: Must call super constructor before using 'this'
    super();
    this.name = name; // ✅ Allowed after super()
  }
}
```

*Why?* In derived classes, the instance memory (`this`) is actually allocated by the base class constructor. Until `super()` runs, `this` does not exist in the derived constructor's environment.

---

### Trap 2: Classes Cannot be Statically Analyzed for Prototype Mutation

Just like constructor functions, class prototypes can still be monkey-patched at runtime:

```js
class App {}
App.prototype.customUtil = () => "Patched";

const app = new App();
console.log(app.customUtil()); // "Patched"
```

---

## 9) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         ES6 CLASS QUICK MATRIX                             |
+───────────────────────────+────────────────────────────────────────────────+
| Concept                   | Rule / Takeaway                                |
+───────────────────────────+────────────────────────────────────────────────+
| What is a class?          | Syntactic sugar over constructor + prototypes. |
| Call without `new`        | Throws `TypeError`.                            |
| Hoisting                  | Hoisted in Temporal Dead Zone (TDZ).           |
| Strict Mode               | Enforced automatically inside class bodies.    |
| Prototype Methods         | Non-enumerable by default.                     |
| `#privateField`           | Engine-enforced hard encapsulation.            |
| `extends`                 | Links both instance and static prototype chains|
| `super()` in derived ctor | Mandatory before accessing `this`.             |
+───────────────────────────+────────────────────────────────────────────────+
```

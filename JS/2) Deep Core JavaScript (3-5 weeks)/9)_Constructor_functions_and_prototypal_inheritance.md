# Constructor Functions and Prototypal Inheritance in JavaScript

---

## 1) Core Mental Model: The Constructor Pattern

Before ES6 introduced the `class` syntax in 2015, JavaScript implemented Object-Oriented Programming using **Constructor Functions** combined with **Prototype Linking**. 

Even today, ES6 classes are fundamentally **syntactic sugar** over this exact constructor and prototypal inheritance model.

A **Constructor Function** is a regular function intended to be invoked with the `new` operator. By convention, constructor function names start with a capital letter (`PascalCase`).

```js
// Constructor Function
function User(username, email) {
  // Instance properties (Unique to each object)
  this.username = username;
  this.email = email;
}

// Prototype Method (Shared across all instances via [[Prototype]])
User.prototype.getProfile = function () {
  return `${this.username} (${this.email})`;
};

const user1 = new User("shivam", "shivam@example.com");
console.log(user1.getProfile()); // "shivam (shivam@example.com)"
```

---

## 2) The 4-Step Mechanics of the `new` Operator

When you execute `new ConstructorFunction(arg1, arg2)`, the JavaScript engine performs exactly four steps under the hood:

```
                      THE `new` OPERATOR ALGORITHM
                                   │
                                   ▼
          ┌─────────────────────────────────────────────────┐
          │ 1. Create a brand-new, empty object `{}`        │
          └────────────────────────┬────────────────────────┘
                                   │
                                   ▼
          ┌─────────────────────────────────────────────────┐
          │ 2. Link `newObj.[[Prototype]]` to               │
          │    `ConstructorFunction.prototype`              │
          └────────────────────────┬────────────────────────┘
                                   │
                                   ▼
          ┌─────────────────────────────────────────────────┐
          │ 3. Execute `ConstructorFunction` with `this`     │
          │    bound to `newObj`                            │
          └────────────────────────┬────────────────────────┘
                                   │
                                   ▼
          ┌─────────────────────────────────────────────────┐
          │ 4. Return `newObj` unless the constructor       │
          │    explicitly returns a non-primitive object    │
          └─────────────────────────────────────────────────┘
```

---

### Step-by-Step Polyfill: Building `customNew()` from Scratch

Understanding how to write a polyfill for the `new` operator is a staple senior JavaScript interview question:

```js
function customNew(Constructor, ...args) {
  // Step 1 & 2: Create empty object linked to Constructor's prototype
  const newObj = Object.create(Constructor.prototype);

  // Step 3: Execute constructor with 'this' bound to newObj
  const result = Constructor.apply(newObj, args);

  // Step 4: If constructor returns a non-primitive object/function, return that;
  // otherwise, return our newly created instance.
  const isObject = typeof result === "object" && result !== null;
  const isFunction = typeof result === "function";

  return isObject || isFunction ? result : newObj;
}

// Verification:
function Product(title, price) {
  this.title = title;
  this.price = price;
}
Product.prototype.getFormattedPrice = function () {
  return `$${this.price.toFixed(2)}`;
};

const laptop = customNew(Product, "MacBook Pro", 1999);
console.log(laptop.title); // "MacBook Pro"
console.log(laptop.getFormattedPrice()); // "$1999.00"
console.log(laptop instanceof Product); // true
```

---

## 3) Constructor Return Rules: Primitives vs. Objects

What happens when a constructor function contains an explicit `return` statement?

1. **Returning a Primitive (`number`, `string`, `boolean`, `null`, `undefined`)**:
   - The primitive return value is **completely ignored**.
   - The newly constructed object (`this`) is returned.
2. **Returning a Non-Primitive Object (`{}`, `[]`, `new Date()`, `function`)**:
   - The newly constructed object is discarded.
   - The returned object is returned instead.

```js
function CaseA() {
  this.name = "Instance A";
  return 42; // Primitive: Ignored!
}

function CaseB() {
  this.name = "Instance B";
  return { custom: "Overridden Object" }; // Object: Replaces 'this'!
}

console.log(new CaseA().name);   // "Instance A"
console.log(new CaseB().name);   // undefined
console.log(new CaseB().custom); // "Overridden Object"
```

---

## 4) Scope-Safe Constructors & `new.target`

If a developer forgets to use `new` when calling a constructor, `this` will default to `undefined` (in strict mode) or the global object (in non-strict mode), causing bugs or crashes:

```js
// ❌ Dangerous without safeguards:
function BrokenUser(name) {
  this.name = name; // Crashes in strict mode: TypeError: Cannot set properties of undefined
}
// BrokenUser("Alice");
```

---

### Making Constructors Scope-Safe with `new.target` (ES6 Standard)

The `new.target` meta-property evaluates to a reference to the constructor function when invoked with `new`, and evaluates to `undefined` when called as a regular function:

```js
function SafeUser(name, role) {
  // If invoked without 'new', auto-instantiate properly
  if (!new.target) {
    return new SafeUser(name, role);
  }

  this.name = name;
  this.role = role;
}

const u1 = SafeUser("Shivam", "Admin"); // Invoked WITHOUT 'new'
const u2 = new SafeUser("Alice", "Dev"); // Invoked WITH 'new'

console.log(u1 instanceof SafeUser); // true (Safely auto-instantiated!)
console.log(u2 instanceof SafeUser); // true
```

---

## 5) Prototypal Inheritance Implementation (The Classic Pattern)

To achieve true classical-style inheritance using constructor functions, you must execute four coordinated steps:

```
┌────────────────────────────────────────────────────────────────────────┐
│               PROTOTYPAL INHERITANCE: 4 CRITICAL STEPS                 │
│                                                                        │
│   1. Parent Constructor Call:     Parent.call(this, ...args)           │
│   2. Prototype Linking:           Child.prototype = Object.create(...) │
│   3. Constructor Reset:           Child.prototype.constructor = Child  │
│   4. Child Method Attachment:     Child.prototype.method = ...         │
└────────────────────────────────────────────────────────────────────────┘
```

---

### Complete Production Example:

```js
// --- 1. PARENT CONSTRUCTOR ---
function Vehicle(make, model, year) {
  this.make = make;
  this.model = model;
  this.year = year;
}

// Parent Prototype Methods
Vehicle.prototype.getInfo = function () {
  return `${this.year} ${this.make} ${this.model}`;
};

Vehicle.prototype.startEngine = function () {
  return "Vroom! Engine started.";
};

// --- 2. CHILD CONSTRUCTOR ---
function ElectricCar(make, model, year, batteryCapacity) {
  // Step 1: Constructor Stealing / Super Call (Inherit instance properties)
  Vehicle.call(this, make, model, year);

  // Child-specific instance property
  this.batteryCapacity = batteryCapacity;
}

// Step 2: Prototype Linking (Inherit prototype methods)
// Create a new object whose [[Prototype]] points to Vehicle.prototype
ElectricCar.prototype = Object.create(Vehicle.prototype);

// Step 3: Repair the broken constructor reference
// Object.create wiped out ElectricCar.prototype.constructor; restore it!
ElectricCar.prototype.constructor = ElectricCar;

// Step 4: Add child-specific prototype methods & override parent methods
ElectricCar.prototype.charge = function () {
  return `Charging battery (${this.batteryCapacity} kWh)...`;
};

// Method Overriding (Polymorphism)
ElectricCar.prototype.startEngine = function () {
  return "Silent start! Electric motor ready.";
};

// --- VERIFICATION & USAGE ---
const tesla = new ElectricCar("Tesla", "Model S", 2024, 100);

console.log(tesla.getInfo());     // "2024 Tesla Model S" (Inherited from Vehicle)
console.log(tesla.startEngine()); // "Silent start! Electric motor ready." (Overridden)
console.log(tesla.charge());      // "Charging battery (100 kWh)..." (Child method)

console.log(tesla instanceof ElectricCar); // true
console.log(tesla instanceof Vehicle);     // true
console.log(tesla instanceof Object);      // true
```

---

## 6) Detailed Prototype Graph Architecture

```
                                  [ Object.prototype ]
                                     - toString()
                                     - hasOwnProperty()
                                     - [[Prototype]]: null
                                              ▲
                                              │
                              ┌───────────────┴──────────────┐
                              │                              │
                    [ Vehicle.prototype ]                    │
                       - getInfo()                           │
                       - startEngine()                       │
                       - constructor: Vehicle                │
                       - [[Prototype]] ──────────────────────┘
                              ▲
                              │ (via Object.create)
                    [ ElectricCar.prototype ]
                       - charge()
                       - startEngine() [Overridden]
                       - constructor: ElectricCar
                       - [[Prototype]] ───────┘
                              ▲
                              │
                    [ tesla instance ]
                       - make: "Tesla"
                       - model: "Model S"
                       - year: 2024
                       - batteryCapacity: 100
                       - [[Prototype]] ───────┘
```

---

## 7) The 3 Dangerous Anti-Patterns in Prototypal Inheritance

### Anti-Pattern 1: Direct Prototype Assignment (`Child.prototype = Parent.prototype`)
```js
// ❌ CATASTROPHIC ANTI-PATTERN:
ElectricCar.prototype = Vehicle.prototype;

// Modifying ElectricCar prototype directly corrupts Vehicle!
ElectricCar.prototype.charge = function () {};
console.log(Vehicle.prototype.charge); // ⚠️ charge() is now on Vehicle too!
```

---

### Anti-Pattern 2: Instantiation Linking (`Child.prototype = new Parent()`)
```js
// ❌ FLAWED ES3 ANTI-PATTERN:
ElectricCar.prototype = new Vehicle();
```
*Why this is bad:*
- Executes the `Vehicle` constructor before any actual instances are created, passing `undefined` parameters.
- Creates unwanted default instance properties on `ElectricCar.prototype`.

---

### Anti-Pattern 3: Shared Mutable Arrays/Objects on Prototype
```js
function User(name) {
  this.name = name;
}

// ❌ ANTI-PATTERN: Mutable array placed on prototype
User.prototype.permissions = [];

const u1 = new User("Alice");
const u2 = new User("Bob");

u1.permissions.push("ADMIN");

console.log(u2.permissions); // ⚠️ ["ADMIN"] (Bob unexpectedly gained Admin rights!)
```

*The Rule*: **Store mutable state (arrays, objects) on `this` inside the constructor, not on `prototype`**.

---

## 8) Summary Cheat Sheet & Decision Matrix

```
+────────────────────────────────────────────────────────────────────────────+
|                     CONSTRUCTOR INHERITANCE CHEAT SHEET                    |
+───────────────────────────+────────────────────────────────────────────────+
| Task                      | Correct Implementation                         |
+───────────────────────────+────────────────────────────────────────────────+
| Inherit Properties        | Parent.call(this, ...args) in child constructor|
| Inherit Methods           | Child.prototype = Object.create(Parent.prototype)|
| Fix Constructor Reference | Child.prototype.constructor = Child            |
| Scope Safety Guard        | if (!new.target) return new Child(...args)     |
| Check Inheritance         | instance instanceof Parent                     |
| Object Return in Ctor     | Explicit object return replaces new instance   |
| Shared Mutable Data       | NEVER put mutable arrays/objects on prototype  |
+───────────────────────────+────────────────────────────────────────────────+
```

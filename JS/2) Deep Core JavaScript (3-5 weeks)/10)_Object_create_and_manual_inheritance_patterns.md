# `Object.create` and Manual Inheritance Patterns in JavaScript

---

## 1) Core Mental Model: Pure Object Delegation (OLOO)

While constructor functions and ES6 classes attempt to mimic classical Class-based OOP, JavaScript's native design is based on **Pure Object Delegation**—often called **OLOO (Objects Linked to Other Objects)**.

`Object.create(proto)` allows you to create a new object directly linked to a parent prototype object **without running constructor functions, using the `new` keyword, or creating boilerplate prototype hierarchies**.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        PURE OBJECT DELEGATION                          │
│                                                                        │
│   const BaseLogger = {                                                 │
│     log(msg) { console.log(`[${this.prefix}] ${msg}`); }               │
│   };                                                                   │
│                                                                        │
│   const appLogger = Object.create(BaseLogger);                         │
│   appLogger.prefix = "APP";                                            │
│                                                                        │
│   appLogger.log("Started"); // [APP] Started                           │
│   (Delegates lookup directly to BaseLogger via [[Prototype]])          │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) `Object.create()` Syntax & Property Descriptors

### Syntax:
```js
Object.create(proto, [propertiesObject]);
```

1. **`proto`**: The object that should be the prototype of the newly-created object (or `null`).
2. **`propertiesObject` (Optional)**: An object whose own enumerable properties specify property descriptors to be added to the newly-created object.

---

### A. Creating Clean Delegated Objects
```js
const Human = {
  species: "Homo sapiens",
  breathe() {
    return `${this.name} is breathing oxygen.`;
  }
};

const individual = Object.create(Human);
individual.name = "Shivam";

console.log(individual.species);  // "Homo sapiens" (Delegated)
console.log(individual.breathe()); // "Shivam is breathing oxygen."
```

---

### B. Defining Property Descriptors on Creation

> ⚠️ **CRITICAL GOTCHA**: When using the second argument of `Object.create()`, properties created via property descriptors default to **`writable: false`**, **`enumerable: false`**, and **`configurable: false`** unless explicitly set to `true`!

```js
const bankAccount = Object.create(
  {
    getSummary() {
      return `Account #${this.accountNumber} - Balance: $${this.balance}`;
    }
  },
  {
    accountNumber: {
      value: "ACC-9081",
      writable: false,    // Read-only (Cannot be reassigned)
      enumerable: true,   // Shows up in Object.keys() / JSON.stringify()
      configurable: false // Cannot be deleted
    },
    balance: {
      value: 5000,
      writable: true,
      enumerable: true,
      configurable: true
    }
  }
);

console.log(bankAccount.getSummary()); // "Account #ACC-9081 - Balance: $5000"

// bankAccount.accountNumber = "HACKED"; // Silent failure in sloppy mode, TypeError in strict mode!
```

---

### C. Creating Pure Prototype-Less Dictionaries (`Object.create(null)`)

Standard object literals (`{}`) inherit from `Object.prototype`, which means they come with built-in properties like `toString`, `valueOf`, and `constructor`. This can cause bugs in hash maps or dictionary lookups:

```js
// Standard object:
const standardMap = {};
console.log("toString" in standardMap); // true (Inherited from Object.prototype!)

// Pure dictionary:
const cleanMap = Object.create(null);
console.log("toString" in cleanMap); // false
console.log(Object.getPrototypeOf(cleanMap)); // null

cleanMap["user1"] = "Shivam";
console.log(cleanMap.user1); // "Shivam"
```

---

## 3) Manual Inheritance Patterns in Practice

---

### Pattern 1: The OLOO Pattern (Objects Linked to Other Objects)

Instead of constructors and classes, OLOO uses pure objects and explicit initialization methods (`init`):

```js
// Base Delegate Object
const UIComponent = {
  init(elementId, label) {
    this.elementId = elementId;
    this.label = label;
    return this; // Enable method chaining
  },

  render() {
    console.log(`Rendering UI component [${this.label}] into #${this.elementId}`);
  }
};

// Child Delegate Object (Inherits from UIComponent)
const Button = Object.create(UIComponent);

// Extend Button with specialized behavior
Button.setupButton = function (elementId, label, onClick) {
  this.init(elementId, label); // Delegate to parent init
  this.onClick = onClick;
  return this;
};

Button.click = function () {
  console.log(`Button [${this.label}] clicked!`);
  if (typeof this.onClick === "function") this.onClick();
};

// Instantiate Objects
const submitBtn = Object.create(Button).setupButton("btn-1", "Submit Form", () => {
  console.log("Form payload sent to server.");
});

submitBtn.render(); // "Rendering UI component [Submit Form] into #btn-1"
submitBtn.click();  // "Button [Submit Form] clicked!" -> "Form payload sent to server."
```

---

### Pattern 2: Factory Functions with `Object.create`

Factory functions encapsulate object creation and delegate shared methods to a single prototype object:

```js
const userMethods = {
  login() {
    this.isOnline = true;
    console.log(`${this.name} is now online.`);
  },
  logout() {
    this.isOnline = false;
    console.log(`${this.name} logged out.`);
  }
};

function createUser(name, role) {
  const user = Object.create(userMethods);
  user.name = name;
  user.role = role;
  user.isOnline = false;
  return user;
}

const userA = createUser("Alice", "Engineer");
const userB = createUser("Bob", "Designer");

userA.login(); // "Alice is now online."
console.log(userA.login === userB.login); // true (Shared prototype method!)
```

---

### Pattern 3: Composition & Mixins with `Object.create`

Instead of creating rigid, deep vertical inheritance hierarchies, you can compose horizontal capabilities into prototypes using `Object.assign`:

```js
// Modular Behavior Mixins
const canFly = {
  fly() {
    return `${this.name} takes off into the sky at altitude ${this.maxAltitude}m.`;
  }
};

const canSwim = {
  swim() {
    return `${this.name} dives underwater to depth ${this.maxDepth}m.`;
  }
};

// Base Drone Prototype
const BaseDrone = {
  init(name) {
    this.name = name;
    return this;
  }
};

// Amphibious Drone Prototype composed via mixins
const AmphibiousDroneProto = Object.assign(
  Object.create(BaseDrone),
  canFly,
  canSwim
);

// Instantiate
const aquaSkyDrone = Object.create(AmphibiousDroneProto).init("AeroSub-X1");
aquaSkyDrone.maxAltitude = 500;
aquaSkyDrone.maxDepth = 50;

console.log(aquaSkyDrone.fly());  // "AeroSub-X1 takes off into the sky at altitude 500m."
console.log(aquaSkyDrone.swim()); // "AeroSub-X1 dives underwater to depth 50m."
```

---

## 4) `Object.create()` Polyfill (ES5 Implementation)

Douglas Crockford originally popularized `Object.create()` with this clean polyfill:

```js
if (typeof Object.create !== "function") {
  Object.create = function (proto) {
    if (typeof proto !== "object" && typeof proto !== "function" && proto !== null) {
      throw new TypeError("Object prototype may only be an Object or null");
    }

    // Temporary constructor function
    function Temp() {}
    Temp.prototype = proto;

    // Create instance linked to proto
    const result = new Temp();

    // Handle null prototype edge case
    if (proto === null) {
      result.__proto__ = null;
    }

    return result;
  };
}
```

---

## 5) Comparison: `Object.create()` vs. `new` Constructor vs. ES6 `class`

| Dimension | `Object.create()` (OLOO) | Constructor Function (`new`) | ES6 `class` |
| :--- | :--- | :--- | :--- |
| **Paradigm** | Pure Behavior Delegation | Simulated Classical OOP | Syntactic Sugar OOP |
| **Instantiation Syntax** | `Object.create(proto)` | `new Constructor()` | `new ClassName()` |
| **`this` Binding** | Implicit at call-site | Bound by `new` | Bound by `new` |
| **Super Call** | Manual delegation (`Parent.init`) | `Parent.call(this)` | `super(...)` |
| **Boilerplate** | Very low | High (prototype link + ctor reset)| Minimal |
| **Tooling / IDE Support** | Moderate | Good | Excellent (TypeScript, Autocomplete) |

---

## 6) Common Pitfalls & Anti-Patterns

### Pitfall 1: Mutating Shared Reference Objects on Prototypes
```js
const BaseConfig = {
  plugins: ["core", "logger"] // Mutable array on prototype!
};

const appA = Object.create(BaseConfig);
const appB = Object.create(BaseConfig);

appA.plugins.push("auth"); // Mutates shared array!

console.log(appB.plugins); // ⚠️ ["core", "logger", "auth"] (Polluted appB!)
```
**Fix**: Initialize mutable arrays/objects in an `init()` method on the instance, not on the delegate prototype:
```js
const SafeConfig = {
  init() {
    this.plugins = ["core", "logger"]; // Own property!
    return this;
  }
};
```

---

### Pitfall 2: Default Property Descriptor Behavior in 2nd Argument
```js
const obj = Object.create({}, {
  id: { value: 1 } // writable: false, enumerable: false by default!
});

obj.id = 2; // Silent fail (or TypeError in strict mode)
console.log(obj.id); // 1
console.log(Object.keys(obj)); // [] (Empty array because enumerable is false!)
```

---

## 7) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                      OBJECT.CREATE() QUICK MATRIX                          |
+───────────────────────────+────────────────────────────────────────────────+
| Concept                   | Rule / Takeaway                                |
+───────────────────────────+────────────────────────────────────────────────+
| `Object.create(proto)`    | Creates new object with `proto` as [[Proto]].  |
| `Object.create(null)`     | Creates clean object with NO prototype methods.|
| OLOO Pattern              | Objects linked to objects; no classes/`new`.   |
| Property Descriptors      | 2nd arg descriptor flags default to `false`.   |
| Mutable Data              | Keep arrays/objects on instances, not proto.   |
| Mixin Composition         | Combine features: `Object.assign(proto, mixin)`|
+───────────────────────────+────────────────────────────────────────────────+
```

# Advanced JavaScript Language Features Pack

---

## 1) Overview & Modern JavaScript Capabilities

Modern JavaScript (ES6 through ES2024+) provides expressive syntax and metaprogramming primitives that enable declarative data extraction, lazy stream evaluation, and robust object customization:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    ADVANCED LANGUAGE FEATURES PACK                      │
│                                                                         │
│  1. Deep Destructuring & Parameter Shaping                              │
│  2. Advanced Spread & Rest Mechanics                                    │
│  3. Optional Chaining (`?.`) & Nullish Operators (`??`, `??=`)          │
│  4. Iterables & The Iterator Protocol (`[Symbol.iterator]`)             │
│  5. Generators (`function*`) & Coroutines (`yield`, `yield*`)           │
│  6. Symbols & Metaprogramming (`Symbol.toPrimitive`, `toStringTag`)    │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Deep Destructuring & Parameter Shaping

Destructuring allows unpacking values from arrays or properties from objects into distinct variables with support for deep nesting, renaming, and default values.

---

### A. Nested Object Destructuring with Aliasing & Defaults

```js
const apiResponse = {
  status: 200,
  data: {
    user: {
      id: "usr_99",
      profile: {
        firstName: "Shivam",
        contact: { email: "shivam@example.com" }
      }
    }
  }
};

// Deep extraction, renaming, and default fallbacks:
const {
  status: responseCode,
  data: {
    user: {
      id: userId,
      profile: {
        firstName,
        contact: { email, phone = "N/A" } // 'phone' defaults to 'N/A'
      }
    }
  }
} = apiResponse;

console.log(responseCode); // 200
console.log(userId);       // "usr_99"
console.log(firstName);    // "Shivam"
console.log(email);        // "shivam@example.com"
console.log(phone);        // "N/A"
```

---

### B. Dynamic Computed Property Destructuring

You can destructure properties whose key names are computed dynamically at runtime:

```js
const keyName = "CURRENT_ENV";
const envConfig = { CURRENT_ENV: "production", DEBUG_MODE: true };

const { [keyName]: activeEnvironment } = envConfig;
console.log(activeEnvironment); // "production"
```

---

### C. Safe Function Parameter Destructuring

Always provide a double default (`= {}`) so the function can be called with zero arguments without throwing a `TypeError`:

```js
function configureServer({
  port = 8080,
  host = "localhost",
  ssl = false,
  headers = {}
} = {}) { // ◀── Safe fallback if no argument is passed
  return `Listening on http${ssl ? "s" : ""}://${host}:${port}`;
}

console.log(configureServer({ port: 3000, ssl: true })); // "Listening on https://localhost:3000"
console.log(configureServer()); // "Listening on http://localhost:8080" (Safely handles undefined)
```

---

## 3) Advanced Spread (`...`) and Rest Operators

Although both use the three-dot syntax `...`, their purposes are opposites:
- **Rest Operator**: Collects multiple individual values into a single array.
- **Spread Operator**: Expands a single array or object into individual elements or properties.

---

### A. Rest Parameters vs. Legacy `arguments` Object

| Feature | Rest Parameters (`...args`) | Legacy `arguments` Object |
| :--- | :--- | :--- |
| **Data Type** | **Real Array** (All `Array.prototype` methods work) | **Array-Like Object** (No `map`, `filter`, `reduce`) |
| **Arrow Functions** | ✅ Works in arrow functions | ❌ `arguments` does NOT exist in arrow functions |
| **Selective Capture** | ✅ Captures only remaining args (`fn(first, ...rest)`) | ❌ Contains all parameters passed |

```js
const calculateStats = (label, ...values) => {
  const sum = values.reduce((acc, curr) => acc + curr, 0);
  const avg = sum / values.length;
  return `${label} -> Average: ${avg.toFixed(2)}`;
};

console.log(calculateStats("Scores", 85, 90, 95, 100)); // "Scores -> Average: 92.50"
```

---

### B. Object Spread Precedence & Overrides

Properties spread later in an object literal override properties spread earlier:

```js
const defaultTheme = { mode: "light", font: "sans-serif", fontSize: 14 };
const userCustomization = { mode: "dark", fontSize: 16 };

// Merging with overrides
const finalTheme = {
  ...defaultTheme,
  ...userCustomization,
  updatedAt: new Date().toISOString() // Additional property
};

console.log(finalTheme.mode);     // "dark" (Overridden)
console.log(finalTheme.font);     // "sans-serif" (Retained)
console.log(finalTheme.fontSize); // 16 (Overridden)
```

---

## 4) Optional Chaining (`?.`) and Logical Assignment Operators

---

### A. Optional Chaining Variations

Optional chaining stops evaluation and immediately returns `undefined` if the operand before `?.` is `null` or `undefined` (short-circuiting):

```js
const app = {
  database: {
    query(sql) { return [{ id: 1 }]; }
  },
  metrics: [10, 20, 30]
};

// 1. Optional Property Access:
console.log(app?.database?.connectionString); // undefined (No crash)

// 2. Optional Function Call:
console.log(app?.analytics?.trackEvent?.("login")); // undefined (Does not throw error)

// 3. Optional Bracket / Index Access:
console.log(app?.metrics?.[0]); // 10
console.log(app?.logs?.[0]);    // undefined
```

---

### B. Nullish Coalescing (`??`) vs. Logical OR (`||`)

`||` checks for **any falsy value** (`0`, `""`, `false`, `NaN`, `null`, `undefined`), while `??` checks **strictly for nullish values (`null` or `undefined`)**:

```js
const config = {
  port: 0,          // Valid port number 0
  allowGuest: false,// Valid boolean false
  title: ""         // Valid empty string
};

// ❌ Bug with Logical OR:
console.log(config.port || 3000);       // 3000 ⚠️ (Overwrote valid port 0!)
console.log(config.allowGuest || true);  // true ⚠️ (Overwrote valid false!)

// ✅ Correct with Nullish Coalescing:
console.log(config.port ?? 3000);       // 0 (Preserved!)
console.log(config.allowGuest ?? true);  // false (Preserved!)
console.log(config.timeout ?? 5000);     // 5000 (Applied fallback for undefined)
```

---

### C. Modern Logical Assignment Operators (`??=`, `||=`, `&&=`)

```js
const options = { timeout: 0 };

// Assigns 5000 ONLY if options.timeout is null or undefined
options.timeout ??= 5000;
console.log(options.timeout); // 0 (Untouched)

let userToken = null;
userToken ??= "GENERATED_JWT_TOKEN";
console.log(userToken); // "GENERATED_JWT_TOKEN"
```

---

## 5) The Iteration Protocol: Iterables and Iterators

In JavaScript, any object can participate in iteration (`for...of` loops, spread `[...obj]`, `Array.from(obj)`) by conforming to the **Iteration Protocols**.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        THE ITERATION PROTOCOLS                         │
│                                                                        │
│   1. Iterable Protocol:                                                │
│      An object having a method at key `[Symbol.iterator]()`             │
│      that returns an Iterator.                                         │
│                                                                        │
│   2. Iterator Protocol:                                                │
│      An object possessing a `.next()` method that returns:             │
│      `{ value: any, done: boolean }`                                   │
└────────────────────────────────────────────────────────────────────────┘
```

---

### Building a Custom Iterable Data Structure:

```js
// Custom Number Range Iterable: range(1, 5)
function createRange(start, end, step = 1) {
  return {
    [Symbol.iterator]() {
      let current = start;
      return {
        next() {
          if (current <= end) {
            const val = current;
            current += step;
            return { value: val, done: false };
          }
          return { value: undefined, done: true };
        }
      };
    }
  };
}

const countToFive = createRange(1, 5);

// Works with for...of:
for (const num of countToFive) {
  console.log(num); // 1, 2, 3, 4, 5
}

// Works with Array Spread:
console.log([...createRange(10, 50, 10)]); // [10, 20, 30, 40, 50]
```

---

## 6) Generators (`function*`) & Coroutines

Generators are special functions that can **pause execution midway (`yield`) and resume later**, maintaining their local state and execution context.

---

### A. Generator Syntax & Execution Lifecycle

```js
function* idGenerator(prefix) {
  let count = 1;
  while (true) {
    // Execution PAUSES here and yields value to caller
    yield `${prefix}_${count++}`;
  }
}

const gen = idGenerator("USR");

console.log(gen.next().value); // "USR_1"
console.log(gen.next().value); // "USR_2"
console.log(gen.next().value); // "USR_3"
```

---

### B. Two-Way Data Communication with `yield`

Generators can both output values and **receive input** from the caller during subsequent `.next(inputValue)` calls:

```js
function* conversationFlow() {
  const name = yield "What is your name?";
  const role = yield `Welcome ${name}! What is your role?`;
  return `User ${name} registered as ${role}.`;
}

const bot = conversationFlow();

console.log(bot.next().value);            // "What is your name?"
console.log(bot.next("Shivam").value);    // "Welcome Shivam! What is your role?"
console.log(bot.next("Architect").value); // "User Shivam registered as Architect."
```

---

### C. Generator Delegation with `yield*`

`yield*` delegates iteration to another generator or iterable:

```js
function* subTask() {
  yield "Step 2A";
  yield "Step 2B";
}

function* mainWorkflow() {
  yield "Step 1";
  yield* subTask(); // Delegates to subTask
  yield "Step 3";
}

console.log([...mainWorkflow()]); // ["Step 1", "Step 2A", "Step 2B", "Step 3"]
```

---

## 7) Symbols & Well-Known Metaprogramming Symbols

A **Symbol** is a guaranteed unique and immutable primitive value used primarily as non-colliding property keys on objects.

```js
const ID_KEY = Symbol("id");
const SECRET_KEY = Symbol("id");

console.log(ID_KEY === SECRET_KEY); // false (Every Symbol() is strictly unique!)

const entity = {
  name: "Service",
  [ID_KEY]: 1001
};

console.log(entity[ID_KEY]); // 1001
console.log(Object.keys(entity)); // ["name"] (Symbol keys are hidden from standard iteration)
console.log(Reflect.ownKeys(entity)); // ["name", Symbol(id)]
```

---

### Global Symbol Registry (`Symbol.for` & `Symbol.keyFor`)

To share symbols across files, iframes, or modules, use the Global Symbol Registry:

```js
const globalSym1 = Symbol.for("app.sharedToken");
const globalSym2 = Symbol.for("app.sharedToken");

console.log(globalSym1 === globalSym2); // true (Points to same registry entry)
console.log(Symbol.keyFor(globalSym1)); // "app.sharedToken"
```

---

### Well-Known Metaprogramming Symbols

JavaScript uses built-in well-known symbols to customize core language behaviors:

```js
class Money {
  constructor(amount, currency) {
    this.amount = amount;
    this.currency = currency;
  }

  // 1. Customize Type Coercion (`Symbol.toPrimitive`)
  [Symbol.toPrimitive](hint) {
    if (hint === "number") return this.amount;
    if (hint === "string") return `${this.currency} ${this.amount.toFixed(2)}`;
    return this.amount; // default hint
  }

  // 2. Customize Object Tag in toString (`Symbol.toStringTag`)
  get [Symbol.toStringTag]() {
    return "MoneyInstance";
  }
}

const price = new Money(49.99, "USD");

console.log(+price + 10);              // 59.99 (Numeric coercion)
console.log(`Total: ${price}`);        // "Total: USD 49.99" (String coercion)
console.log(Object.prototype.toString.call(price)); // "[object MoneyInstance]"
```

---

## 8) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                     ADVANCED FEATURES QUICK MATRIX                         |
+───────────────────────────+────────────────────────────────────────────────+
| Deep Destructuring        | Unpack nested data with renaming and defaults. |
| Rest (`...args`)          | Gathers arguments into real Array.             |
| Spread (`...obj`)         | Shallow copy and property merging/overrides.   |
| Optional Chaining `?.`    | Short-circuits on `null`/`undefined`.          |
| Nullish Coalescing `??`   | Fallback only for `null` / `undefined`.        |
| Iterables                 | Implement `[Symbol.iterator]()` with `.next()`.|
| Generators `function*`    | Coroutines that pause/resume via `yield`.      |
| Symbols                   | Unique primitive keys; Metaprogramming hooks.  |
+───────────────────────────+────────────────────────────────────────────────+
```

# Statements, Expressions, and Variables

## 1) Statements vs. Expressions

### Expressions
An **expression** is any unit of code that evaluates to a single value. Anywhere JavaScript expects a value, you can place an expression.

```js
// Literal expressions
42;
"Hello World";
true;

// Arithmetic / String expressions
2 + 3;                  // 5
"Hello " + "JS";         // "Hello JS"

// Logical & Comparison expressions
total > 5;              // true or false
isLoggedIn && hasAccess; // boolean result

// Function call expression
Math.max(10, 20);       // 20

// Ternary expression (expression equivalent of if/else)
const status = age >= 18 ? "Adult" : "Minor";
```

### Statements
A **statement** is an instruction that performs an action, controls execution flow, or declares variables. Statements do **not** return a value where an expression is expected (you cannot pass a statement as a function argument or assign it to a variable).

```js
// Variable declaration statement
let total = 0;

// Conditional statement
if (total === 0) {
  total = 10;
}

// Loop statement
for (let i = 0; i < 3; i++) {
  console.log(i);
}

// Function declaration statement
function greet(name) {
  return `Hello, ${name}`;
}
```

### Expression Statements
An **expression statement** is an expression that stands alone as a statement. A semicolon at the end turns an expression into a statement.

```js
// Function call expression standing alone as a statement
console.log("Processing...");

// Assignment expression standing alone as a statement
x = 10;
```

### Quick Decision Rule
| Concept | Evaluates to Value? | Can be assigned / passed to function? | Examples |
| :--- | :--- | :--- | :--- |
| **Expression** | **Yes** | **Yes** | `5 + 5`, `user.name`, `age >= 18 ? 'A' : 'B'`, `fn()` |
| **Statement** | **No** | **No** | `if (...) {}`, `for (...) {}`, `let x = 10;`, `return;` |

---

## 2) Variables: `var`, `let`, and `const`

JavaScript provides three keywords to declare variables: `var` (ES5 legacy), `let` (ES6+), and `const` (ES6+).

### Summary Comparison Table
| Feature | `var` | `let` | `const` |
| :--- | :--- | :--- | :--- |
| **Scope** | Function Scope | Block Scope | Block Scope |
| **Hoisting** | Hoisted with `undefined` | Hoisted (Temporal Dead Zone) | Hoisted (Temporal Dead Zone) |
| **Reassignment** | Allowed | Allowed | **Not Allowed** |
| **Redeclaration** | Allowed | Disallowed in same scope | Disallowed in same scope |
| **Initial Value Required?** | No  (defaults to `undefined`) | No (defaults to `undefined`) | **Yes** (SyntaxError: Missing initializer in const declaration) |

---

### `const` (Constant Reference)
- **Scope**: Block-scoped (bounded by `{}`).
- **Reassignment**: **Forbidden**. Must be initialized when declared.
- **Best Practice**: Use `const` as your **default choice** for all variable declarations.

```js
const MAX_USERS = 100;
// MAX_USERS = 200; // TypeError: Assignment to constant variable.

const settings = { theme: "dark" };
// settings = {};   // TypeError: Assignment to constant variable.
```

---

### `let` (Reassignable Variable)
- **Scope**: Block-scoped (bounded by `{}`).
- **Reassignment**: Allowed. Initial value is optional (`undefined` by default).
- **Best Practice**: Use `let` only when you know the variable's value will change over time (e.g., counters, accumulators, reassigned flags).

```js
let count = 0;
count += 1; // Allowed

let statusMessage; // Initialized as undefined
statusMessage = "Loading...";
```

---

### `var` (Legacy Variable - Avoid)
- **Scope**: Function-scoped (ignores block `{}` statements like `if` or `for`).
- **Hoisting**: Hoisted to the top of its scope and initialized with `undefined`.
- **Redeclaration**: Permits redeclaring the same variable in the same scope without error.

```js
function varExample() {
  if (true) {
    var count = 10; // Not bound to if-block!
  }
  console.log(count); // 10 (Leaked out of the if-block)
}
```

---

## 3) Scope & The Temporal Dead Zone (TDZ)

### Scope Differences
- **Block Scope (`let`/`const`)**: Variables exist only inside the nearest pair of curly braces `{}`.
- **Function Scope (`var`)**: Variables exist throughout the entire enclosing function.

```js
if (true) {
  var varScoped = "I leak out of blocks";
  let letScoped = "I stay inside this block";
  const constScoped = "I stay inside this block too";
}

console.log(varScoped);   // "I leak out of blocks"
// console.log(letScoped);   // ReferenceError: letScoped is not defined
// console.log(constScoped); // ReferenceError: constScoped is not defined
```

### Hoisting & Temporal Dead Zone (TDZ)
- `var` declarations are hoisted to the top of their scope and initialized with `undefined`.
- `let` and `const` declarations are also hoisted, but they are **not initialized**. The period between entering scope and reaching the declaration line is called the **Temporal Dead Zone (TDZ)**. Accessing a variable in the TDZ throws a `ReferenceError`.

```js
// --- var Hoisting ---
console.log(a); // undefined (declaration hoisted)
var a = 5;

// --- let/const TDZ ---
// console.log(b); // ReferenceError: Cannot access 'b' before initialization
let b = 10;
```

---

## 4) Reassignment vs. Mutation

Understanding the distinction between reassignment and mutation is critical when working with `const`.

- **Reassignment**: Changing which value or memory reference a variable binding points to.
- **Mutation**: Modifying the internal contents of an existing object or array in memory without changing its reference.

```js
// Primitive values are immutable
let name = "Alice";
name = "Bob"; // Reassignment

// Objects/Arrays declared with const
const user = { name: "Alice", age: 25 };

// 1. Mutation (ALLOWED with const)
user.age = 26;            // Modifying property
user.city = "New York";   // Adding property

// 2. Reassignment (FORBIDDEN with const)
// user = { name: "Bob" }; // TypeError: Assignment to constant variable.
```

> **Note**: To make an object completely immutable (prevent mutation), use `Object.freeze(obj)`.

---

## 5) Naming Rules and Conventions

### Identifier Rules (Enforced by JavaScript)
1. Must begin with a letter, underscore (`_`), or dollar sign (`$`).
2. Subsequent characters can include numbers (`0-9`).
3. Cannot start with a digit (e.g., `let 1stPlace = true;` is invalid).
4. Cannot use reserved JavaScript keywords (`let`, `class`, `return`, `function`, etc.).
5. Identifiers are case-sensitive (`userAge` and `userage` are different variables).

### Naming Conventions (Industry Best Practices)
- **`camelCase`**: Standard for variables, function names, and object keys.
  - Examples: `userProfile`, `isLoggedIn`, `totalCartAmount`
- **`PascalCase`**: Used for Classes, Constructor functions, and React components.
  - Examples: `UserProfile`, `PaymentGateway`
- **`UPPER_SNAKE_CASE`**: Used for hardcoded, global configuration constants.
  - Examples: `MAX_RETRY_COUNT`, `API_BASE_URL`
- **Booleans**: Prefix with `is`, `has`, `should`, or `can`.
  - Examples: `isActive`, `hasPermission`, `shouldRender`

---

## 6) Best Practices Checklist
1. **Prefer `const` by Default**: Default to `const`. Switch to `let` only when reassignment is explicitly required.
2. **Never Use `var`**: Avoid `var` in modern JavaScript to eliminate scope leaks and hoisting bugs.
3. **Minimize Variable Scope**: Declare variables as close as possible to where they are used.
4. **Use Strict Mode**: Always run JavaScript in modules or with `'use strict';` to prevent implicit global variable creation.
5. **Clear, Intention-Revealing Names**: Favor self-explanatory names (`itemCount`) over vague single letters (`n`), except for standard loop indices (`i`, `j`).

---

## 7) Quick Exercises & Practice

### Exercise 1: Expression vs. Statement Identification
Identify whether each line is a **Statement** or an **Expression**:
1. `let score = 95;`
2. `score >= 50 ? "Pass" : "Fail"`
3. `if (isReady) { start(); }`
4. `Math.min(5, 12)`

<details>
<summary>View Solutions</summary>

1. **Statement** (Variable declaration statement)
2. **Expression** (Ternary operator evaluates to a string value)
3. **Statement** (If control flow statement)
4. **Expression** (Function call evaluates to a numeric value `5`)
</details>

---

### Exercise 2: Predict the Output
What will be logged to the console in each case?

```js
// Case A
console.log(x);
var x = 100;

// Case B
const colors = ["red", "green"];
colors.push("blue");
console.log(colors);

// Case C
if (true) {
  let inner = "Secret";
}
console.log(inner);
```

<details>
<summary>View Solutions</summary>

- **Case A**: `undefined` (due to `var` hoisting).
- **Case B**: `["red", "green", "blue"]` (`const` allows array mutation, only reassignment is blocked).
- **Case C**: `ReferenceError: inner is not defined` (`let` is block-scoped and cannot be accessed outside `if` block).
</details>

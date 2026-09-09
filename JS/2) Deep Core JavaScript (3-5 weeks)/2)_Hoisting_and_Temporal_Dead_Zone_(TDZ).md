# Hoisting and the Temporal Dead Zone (TDZ) in JavaScript

---

## 1) What is Hoisting? (The Real Mechanism)

A common beginner misconception is that the JavaScript engine physically cuts and pastes variable and function declarations to the top of the file. 

**Hoisting is not physical code movement.** 

Hoisting is a direct consequence of how the JavaScript engine runs in two distinct phases:
1. **Creation Phase (Memory Allocation Phase)**: The engine parses the code and registers all variable, function, and class identifiers in their respective Environment Records in memory before executing a single line of code.
2. **Execution Phase**: The engine executes code sequentially, reading from and writing to those registered memory locations.

Because declarations are processed in memory during the Creation Phase, JavaScript allows you to access or reference certain identifiers before their lexical line of definition.

```
       SOURCE CODE                               ENGINE MEMORY (CREATION PHASE)
┌────────────────────────┐                    ┌───────────────────────────────────┐
│ console.log(age);      │                    │ Environment Record:               │
│ var age = 25;          │  ─── Parsed ───▶   │   - age: undefined                │
│ function greet() {     │                    │   - greet: [Function Reference]   │
│   return "Hello";      │                    │   - user: <uninitialized (TDZ)>   │
│ }                      │                    └───────────────────────────────────┘
│ let user = "Alice";    │
└────────────────────────┘
```

---

## 2) Category-by-Category Hoisting Breakdown

How an identifier behaves when referenced prior to its declaration depends entirely on the keyword or construct used:

| Construct | Hoisted? | Initialized In Creation Phase? | Accessing Before Declaration Results In |
| :--- | :--- | :--- | :--- |
| **`function` Declaration** | **Yes** | **Yes**, with full function body | Executes successfully |
| **`var` Variable** | **Yes** | **Yes**, initialized to `undefined` | Evaluates to `undefined` |
| **`let` Variable** | **Yes** | **No** (Uninitialized / in TDZ) | `ReferenceError: Cannot access before initialization` |
| **`const` Variable** | **Yes** | **No** (Uninitialized / in TDZ) | `ReferenceError: Cannot access before initialization` |
| **`class` Declaration** | **Yes** | **No** (Uninitialized / in TDZ) | `ReferenceError: Cannot access before initialization` |
| **Function Expression (`var`)** | **Yes** (Variable only) | `undefined` | `TypeError: fn is not a function` |
| **Function Expression (`const`/`let`)** | **Yes** (Variable only) | **No** (Uninitialized / in TDZ) | `ReferenceError: Cannot access before initialization` |
| **`import` Statement** | **Yes** | Fully hoisted & evaluated | Modules loaded before file body executes |

---

## 3) `var` Hoisting Mechanics

When `var` declarations are registered in the `VariableEnvironment` during the creation phase, they are immediately initialized with the primitive value `undefined`.

```js
console.log(greeting); // undefined (NOT ReferenceError)
var greeting = "Hello World";
console.log(greeting); // "Hello World"
```

### Mental Model & Execution Sequence:

```js
// --- What actually happens in the Engine ---

// Phase 1: Creation Phase
var greeting = undefined; // Memory allocated and initialized

// Phase 2: Execution Phase
console.log(greeting);    // Reads 'undefined'
greeting = "Hello World"; // Assignment updates the memory slot
console.log(greeting);    // Reads "Hello World"
```

---

## 4) Function Hoisting: Declarations vs. Expressions

### A. Function Declarations (Fully Hoisted)
Function declarations are hoisted with their **entire implementation**. You can safely invoke them anywhere in their enclosing scope, even before their textual definition.

```js
sayHello(); // "Hello from hoisted function!"

function sayHello() {
  console.log("Hello from hoisted function!");
}
```

---

### B. Function Expressions (Variable Hoisting Rules Apply)
When a function is assigned to a variable, only the variable declaration is hoisted—**not the function assignment**.

#### 1. Function Expression with `var`:
```js
greet(); // TypeError: greet is not a function

var greet = function () {
  console.log("Welcome!");
};
```
*Why `TypeError`?* During the creation phase, `var greet` is initialized to `undefined`. In the execution phase, `greet()` attempts to invoke `undefined()`, resulting in a `TypeError`.

#### 2. Function Expression with `const` / `let` / Arrow Function:
```js
calcTotal(); // ReferenceError: Cannot access 'calcTotal' before initialization

const calcTotal = (price, tax) => price + tax;
```
*Why `ReferenceError`?* `const calcTotal` is uninitialized and resides in the Temporal Dead Zone.

---

## 5) The Temporal Dead Zone (TDZ) Deep Dive

### What is the TDZ?
The **Temporal Dead Zone (TDZ)** is the period between entering a scope (when a variable is registered in the Lexical Environment) and the exact line of code where the variable is initialized with a value.

```
{
  // ==========================================
  // [START OF BLOCK SCOPE]
  // TDZ for variable 'score' starts here
  //
  // console.log(score); // ❌ ReferenceError!
  // ==========================================
  
  let score = 95; // ◀── TDZ ends here (Initialization line)
  
  console.log(score); // ✅ 95 (Accessible)
}
```

---

### Proof that `let` and `const` ARE Hoisted

A frequent interview question is: *"Are `let` and `const` hoisted?"*
**Answer**: **Yes, they are hoisted**, but unlike `var`, they are not initialized with `undefined`.

#### Concrete Proof:
```js
let x = "global";

function testScope() {
  // If 'let x' was NOT hoisted, this log would look up the scope chain and print "global".
  // Because it IS hoisted, local 'x' shadows global 'x', but is in the TDZ!
  console.log(x); // ReferenceError: Cannot access 'x' before initialization
  let x = "local";
}

testScope();
```
If `let x = "local"` was not hoisted, the function would find `let x = "global"` in its outer scope and output `"global"`. The fact that it throws a `ReferenceError` proves that the local identifier was hoisted and bound to the local environment before execution began.

---

### Why is it called *Temporal* (Time-based) and NOT *Spatial* (Position-based)?

The TDZ depends on the **time of execution**, not where code is positioned textually in the file.

```js
function display() {
  console.log(message); // Log line is physically ABOVE the declaration!
}

// TDZ for 'message' is active here...

let message = "JavaScript is dynamic!"; // TDZ ends here

display(); // ✅ "JavaScript is dynamic!" (Executed AFTER declaration time)
```

In the example above, `display()` textually appears before `let message`. However, because `display()` is called **after** `message` has been initialized, no TDZ violation occurs.

---

### The `typeof` Operator in the TDZ

In ES5 (`var`), `typeof` was guaranteed to be safe and never threw an error:
```js
console.log(typeof nonExistentVariable); // "undefined"
```

In ES6+, using `typeof` on a variable currently trapped in the TDZ throws a `ReferenceError`:
```js
console.log(typeof myVar); // ReferenceError: Cannot access 'myVar' before initialization
let myVar = 10;
```

---

### Default Function Parameters and the TDZ

Default parameters are evaluated in their own intermediate lexical scope from left to right. Referencing parameters to the right causes a TDZ error:

```js
// ❌ Invalid: 'b' is in TDZ when evaluating default value for 'a'
function calculate(a = b, b = 10) {
  return a + b;
}
// calculate(); // ReferenceError: Cannot access 'b' before initialization

// ✅ Valid: 'a' is already initialized when evaluating 'b'
function calculateValid(a = 10, b = a * 2) {
  return a + b;
}
console.log(calculateValid()); // 30 (10 + 20)
```

---

## 6) Class Hoisting

Like `let` and `const`, ES6 classes are hoisted to the top of their enclosing scope, but are **not initialized** (residing in the TDZ):

```js
// const pet = new Animal("Dog"); // ReferenceError: Cannot access 'Animal' before initialization

class Animal {
  constructor(name) {
    this.name = name;
  }
}

const pet = new Animal("Dog"); // ✅ Works
```

---

## 7) Precedence & Collision Rules: The Hoisting Order

When variable declarations and function declarations share the same identifier name in the same scope, the engine follows strict precedence rules:

### Rule 1: Function Declarations Take Precedence Over Variable Declarations (`var`)

```js
console.log(typeof value); // "function"

var value = 100;
function value() {
  return "I am a function";
}

console.log(typeof value); // "number"
```

#### Step-by-Step Breakdown:
1. **Creation Phase**:
   - `var value` is registered (`undefined`).
   - `function value() {}` is registered, **overwriting** `undefined` with the function reference.
   - Initial memory state: `value -> Function`.
2. **Execution Phase**:
   - `console.log(typeof value)` prints `"function"`.
   - `value = 100` assigns the number `100`, overwriting the function in memory.
   - `console.log(typeof value)` prints `"number"`.

---

### Rule 2: Multiple Function Declarations with the Same Name (Last One Wins)

```js
function greet() {
  return "First";
}

function greet() {
  return "Second";
}

console.log(greet()); // "Second"
```

---

### Rule 3: `let` / `const` Identifier Collision

You cannot redeclare an existing identifier in the same scope with `let` or `const`, regardless of whether the other declaration was `var`, `function`, or `let`:

```js
var user = "Alice";
let user = "Bob"; // SyntaxError: Identifier 'user' has already been declared
```

---

## 8) Tricky Code Puzzles & Interview Questions

### Challenge 1: Hoisting with `var` and `if` Blocks
```js
var x = 1;

function test() {
  if (!x) {
    var x = 10;
  }
  console.log(x);
}

test();
```

<details>
<summary><b>View Output & Walkthrough</b></summary>

**Output**: `10`

**Step-by-Step Explanation**:
1. Inside `test()`, `var x` is hoisted to the top of the function scope and initialized to `undefined`.
2. At the start of `test()` execution, local `x` is `undefined` (which is falsy).
3. The condition `if (!x)` evaluates to `if (!undefined)` -> `if (true)`.
4. The block executes: `x = 10`.
5. `console.log(x)` logs `10`.
</details>

---

### Challenge 2: Loop Scoping (`var` vs `let`)
```js
for (var i = 0; i < 3; i++) {
  setTimeout(() => console.log("var i:", i), 100);
}

for (let j = 0; j < 3; j++) {
  setTimeout(() => console.log("let j:", j), 100);
}
```

<details>
<summary><b>View Output & Walkthrough</b></summary>

**Output**:
```text
var i: 3
var i: 3
var i: 3
let j: 0
let j: 1
let j: 2
```

**Step-by-Step Explanation**:
- `var i` is hoisted to the enclosing function/global scope. There is only **one shared instance** of `i`. When the `setTimeout` callbacks run after the loop terminates, `i` has reached `3`.
- `let j` is block-scoped. JavaScript creates a **new lexical environment and a fresh binding of `j` for every single loop iteration**. Each timer closure captures its own distinct `j`.
</details>

---

### Challenge 3: Function Declarations Inside Blocks
```js
"use strict";

console.log(typeof fn); // "undefined" in strict mode

if (true) {
  function fn() {
    return "Inside block";
  }
}

console.log(typeof fn); // "undefined" in strict mode
```

> **Note**: In ES6 **Strict Mode**, function declarations inside blocks (`{ ... }`) are block-scoped like `let`. In non-strict (sloppy) mode, legacy browser-specific rules apply. Always use strict mode or modules.

---

## 9) Best Practices & Production Guidelines

1. **Always Use `const` by Default, `let` When Reassignment is Needed**:
   - Eliminates unintentional `var` hoisting and global leakage.
2. **Declare Variables at the Top of Their Relevant Block**:
   - Avoids entering the TDZ unexpectedly and makes data flow explicit.
3. **Never Rely on `var` Hoisting**:
   - Relying on hoisting makes code fragile, unpredictable, and hard to maintain.
4. **Prefer Function Declarations for Top-Level Utility Functions**:
   - Keeps main code readable from top to bottom (you can call high-level logic first and define implementation details below).
5. **Enable ESLint Rules**:
   - `no-use-before-define`: Prevents accessing variables/functions before definition.
   - `no-var`: Disallows legacy `var`.
   - `no-redeclare`: Prevents accidental duplicate identifier declarations.

---

## 10) Summary & Quick Revision Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                           HOISTING QUICK MATRIX                            |
+───────────────────────────+──────────+─────────────────+───────────────────+
| Declaration               | Hoisted? | Initial Value   | Access before def |
+───────────────────────────+──────────+─────────────────+───────────────────+
| function name() {}        | YES      | Full function   | ✅ Works          |
| var name = val            | YES      | undefined       | ⚠️ undefined      |
| let name = val            | YES      | <uninitialized> | ❌ ReferenceError |
| const name = val          | YES      | <uninitialized> | ❌ ReferenceError |
| class Name {}             | YES      | <uninitialized> | ❌ ReferenceError |
| var fn = function() {}    | YES      | undefined       | ❌ TypeError      |
| const fn = () => {}       | YES      | <uninitialized> | ❌ ReferenceError |
+───────────────────────────+──────────+─────────────────+───────────────────+
```

- **Hoisting** is memory allocation during the Creation Phase, not physical code movement.
- **TDZ** is the time-span between scope entry and variable initialization for `let`, `const`, and `class`.
- `let`/`const` are hoisted—proven by local shadowing throwing a `ReferenceError` instead of falling back to outer scope.

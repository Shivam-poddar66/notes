# Lexical Environment and Scope Chain in JavaScript

---

## 1) Core Definitions & Mental Model

To master advanced JavaScript, you must understand how the engine organizes variables in memory and resolves them when your code runs.

- **Scope**: The boundary or accessibility region of variables, functions, and objects in your code.
- **Lexical**: Means *"relating to the written text or source code"*. Lexical scoping means variable scope is determined by **where functions and blocks are physically authored in the source code**, *not* where or when they are called at runtime.
- **Lexical Environment**: The internal engine data structure that stores local identifier-to-value bindings and holds a link to the enclosing (outer) environment.
- **Scope Chain**: The linked hierarchy of Lexical Environments created through outer environment references, traversed when resolving variable names.

```
+─────────────────────────────────────────────────────────────────────────+
|                      GLOBAL LEXICAL ENVIRONMENT                         |
|  Environment Record: { globalUser: "Admin", appVersion: "2.0" }         |
|  Outer Env: null                                                        |
+────────────────────────────────────▲────────────────────────────────────+
                                     │ (OuterEnvReference)
+────────────────────────────────────┴────────────────────────────────────+
|                      PARENT LEXICAL ENVIRONMENT                         |
|  Environment Record: { parentRole: "Manager", teamId: 404 }             |
|  Outer Env: Points to Global                                            |
+────────────────────────────────────▲────────────────────────────────────+
                                     │ (OuterEnvReference)
+────────────────────────────────────┴────────────────────────────────────+
|                      CHILD LEXICAL ENVIRONMENT                          |
|  Environment Record: { localTask: "Review Code" }                       |
|  Outer Env: Points to Parent                                            |
+─────────────────────────────────────────────────────────────────────────+
```

---

## 2) Anatomy of a Lexical Environment (ECMAScript Spec)

According to the ECMAScript specification, every Lexical Environment consists of two fundamental components:

```
+──────────────────────────────────────────────────────────────────────────+
|                           LEXICAL ENVIRONMENT                            |
|                                                                          |
|  1. Environment Record (ER):                                             |
|     Records the actual identifier bindings (variables, functions,        |
|     parameters, classes) created within its scope.                       |
|                                                                          |
|  2. Outer Environment Reference (OuterEnv):                              |
|     A reference (pointer) to the Lexical Environment of the enclosing    |
|     lexical parent. For the Global Environment, this reference is null.  |
+──────────────────────────────────────────────────────────────────────────+
```

### Types of Environment Records:
1. **Declarative Environment Record**: Used by function scopes, block scopes (`let`/`const`), `catch` blocks, and modules to store variable and function declarations.
2. **Object Environment Record**: Used by the global scope in browsers to bind identifiers directly to properties of the `window` object (legacy `var` and function declarations).
3. **Global Environment Record**: A composite record combining both a Declarative Record (for top-level `let`, `const`, `class`) and an Object Record (for `var`, top-level `function`, built-in globals like `Object`, `Array`).

---

## 3) The Scopes in JavaScript

JavaScript features four main types of scope:

| Scope Type | Defined By | Introduced In | Keywords / Constructs |
| :--- | :--- | :--- | :--- |
| **Global Scope** | Default top-level environment | ES1 (1997) | Top-level variables, functions, `window`/`globalThis` |
| **Module Scope** | Inside an ES Module (`type="module"` / `.mjs`) | ES6 (2015) | `import`, `export`, variables at module root |
| **Function Scope** | Inside a `function` body | ES1 (1997) | `var`, `let`, `const`, parameters, nested functions |
| **Block Scope** | Inside any curly brace pair `{ ... }` | ES6 (2015) | `let`, `const`, `class`, `for`, `if`, `while`, `switch` |

---

### A. Global Scope
Variables declared outside of any function or block live in the Global Scope. They are accessible from anywhere in the script.

```js
const API_URL = "https://api.example.com"; // Global scope

function fetchData() {
  console.log("Fetching from:", API_URL); // Accessible inside function
}
```

> **Warning (Global Pollution & Sloppy Mode)**: In non-strict mode, assigning to an undeclared variable (`x = 42;`) implicitly creates a global property. In strict mode (`"use strict";`), this throws a `ReferenceError`.

---

### B. Function Scope
Variables declared with `var`, `let`, or `const` inside a function are accessible only within that function and any nested functions.

```js
function authenticate() {
  var token = "secret_jwt_token";
  let userId = 1001;
}

// console.log(token);  // ReferenceError: token is not defined
// console.log(userId); // ReferenceError: userId is not defined
```

---

### C. Block Scope (`let` & `const`)
Any pair of curly braces `{ ... }` creates a block scope for `let`, `const`, and `class`. Unlike `var`, these variables cannot leak out of the block.

```js
if (true) {
  var leakedVar = "I leak out of if-blocks";
  let blockedLet = "I stay inside this block";
  const blockedConst = "I also stay inside";
}

console.log(leakedVar);    // ✅ "I leak out of if-blocks"
// console.log(blockedLet);   // ❌ ReferenceError: blockedLet is not defined
// console.log(blockedConst); // ❌ ReferenceError: blockedConst is not defined
```

---

## 4) Lexical Scoping vs. Dynamic Scoping

A critical milestone in mastering JavaScript is understanding why JavaScript is **Lexically Scoped** (Static Scope) rather than **Dynamically Scoped**.

- **Lexical Scope (JavaScript)**: Scope is resolved based on where the function is **declared in source code**.
- **Dynamic Scope (e.g., Bash, Perl local)**: Scope is resolved based on where the function is **called at runtime**.

### Concrete Demonstration:

```js
const value = "GLOBAL";

function printValue() {
  console.log(value); // Where is printValue declared? Inside Global Scope!
}

function caller() {
  const value = "LOCAL_TO_CALLER";
  printValue(); // Invoked here
}

caller(); // Output: "GLOBAL" (NOT "LOCAL_TO_CALLER")
```

```
                        LEXICAL SCOPING PATH:
┌────────────────────────────────────────────────────────────────────────┐
│ Global Scope: const value = "GLOBAL";                                  │
│   ▲                                                                    │
│   │ (printValue's [[OuterEnv]] points directly to Global Scope!)       │
│   │                                                                    │
│ function printValue() {                                                │
│   console.log(value); ──▶ Looks up to its lexical parent ──▶ "GLOBAL"  │
│ }                                                                      │
│                                                                        │
│ function caller() {                                                    │
│   const value = "LOCAL_TO_CALLER";                                     │
│   printValue(); ──▶ Invocation site DOES NOT change printValue's scope!│
│ }                                                                      │
└────────────────────────────────────────────────────────────────────────┘
```

When `printValue` is defined, the engine attaches an internal property `[[Environment]]` pointing to the Global Lexical Environment. No matter where `printValue` is passed or invoked, its scope chain always points back to its lexical birth location.

---

## 5) The Scope Chain Resolution Algorithm

When the JavaScript engine encounters an identifier (e.g., `userName`) during the execution phase, it follows a strict **Bottom-Up Lookup Algorithm**:

```
                  START: Identifier Lookup for `x`
                                │
                                ▼
            ┌───────────────────────────────────────┐
            │ Is `x` in current Local Environment?  │
            └───────────────────┬───────────────────┘
                                │
                   ┌────────────┴────────────┐
                 YES                        NO
                   │                         │
                   ▼                         ▼
         [ Return local value ]    ┌──────────────────────────────────┐
                                   │ Is OuterEnvReference === null?   │
                                   └────────────────┬─────────────────┘
                                                    │
                                      ┌─────────────┴─────────────┐
                                    YES                          NO
                                      │                           │
                                      ▼                           ▼
                             [ ReferenceError:            [ Move to OuterEnv ]
                             x is not defined ]                   │
                                                                  └───▶ Loop to Step 1
```

### Key Properties of Scope Chain Lookup:
1. **One-Way Direction**: Lookup goes strictly **upward** (inner -> outer -> global). An outer scope can **never** look down into an inner scope.
2. **Sibling Isolation**: Two child functions inside the same parent cannot access each other's local variables.
3. **First-Match Terminates**: The engine stops searching immediately upon finding the first match in the chain (even if an identical variable exists further up).

---

## 6) Variable Shadowing and Illegal Shadowing

### Variable Shadowing
When a variable declared in an inner scope has the exact same name as a variable in an outer scope, the inner variable **shadows** (hides) the outer variable within the inner scope.

```js
const theme = "light"; // Outer variable

function renderComponent() {
  const theme = "dark"; // Shadows outer 'theme'
  console.log("Local theme:", theme); // "dark"
}

renderComponent();
console.log("Global theme:", theme); // "light"
```

---

### Illegal Shadowing
In JavaScript, you **cannot shadow a `let` or `const` variable using `var` within an inner block**, because `var` attempts to hoist to the enclosing function/global scope, creating an identifier collision in the same lexical space.

```js
// ❌ Illegal Shadowing: SyntaxError
let count = 10;
{
  // var count = 20; // SyntaxError: Identifier 'count' has already been declared
}

// ✅ Legal Shadowing: let shadowing var is completely valid
var total = 100;
{
  let total = 200; // Allowed (block-scoped 'total' does not collide with outer var)
  console.log(total); // 200
}
console.log(total); // 100
```

---

## 7) Step-by-Step Code Walkthrough: Nested Scope Chain

Let us trace a multi-level nested scope chain in action:

```js
const globalCompany = "Acme Corp";

function createDepartment(deptName) {
  const deptBudget = 50000;

  function createProject(projectName) {
    const projectCost = 12000;

    function printSummary() {
      console.log(
        `${globalCompany} -> ${deptName} ($${deptBudget}) -> ${projectName} ($${projectCost})`
      );
    }

    return printSummary;
  }

  return createProject;
}

const dept = createDepartment("Engineering");
const project = dept("Platform Modernization");
project();
```

### Detailed Environment Trace at `printSummary()` Execution:

```
+───────────────────────────────────────────────────────────────────────────+
|                      [LEVEL 4: printSummary Environment]                  |
|  Declarative Record: { (empty) }                                          |
|  Outer Reference   : Points to Level 3                                    |
+─────────────────────────────────────▲─────────────────────────────────────+
                                      │
+─────────────────────────────────────┴─────────────────────────────────────+
|                      [LEVEL 3: createProject Environment]                 |
|  Declarative Record: { projectName: "Platform Modernization",             |
|                        projectCost: 12000,                                |
|                        printSummary: Function }                           |
|  Outer Reference   : Points to Level 2                                    |
+─────────────────────────────────────▲─────────────────────────────────────+
                                      │
+─────────────────────────────────────┴─────────────────────────────────────+
|                      [LEVEL 2: createDepartment Environment]              |
|  Declarative Record: { deptName: "Engineering",                           |
|                        deptBudget: 50000,                                 |
|                        createProject: Function }                          |
|  Outer Reference   : Points to Level 1                                    |
+─────────────────────────────────────▲─────────────────────────────────────+
                                      │
+─────────────────────────────────────┴─────────────────────────────────────+
|                      [LEVEL 1: Global Environment]                        |
|  Declarative Record: { globalCompany: "Acme Corp", dept: Fn, project: Fn }|
|  Outer Reference   : null                                                 |
+───────────────────────────────────────────────────────────────────────────+
```

When `printSummary()` executes:
1. `projectCost` -> Resolved in **Level 3** (1 hop).
2. `projectName` -> Resolved in **Level 3** (1 hop).
3. `deptBudget`  -> Resolved in **Level 2** (2 hops).
4. `deptName`    -> Resolved in **Level 2** (2 hops).
5. `globalCompany` -> Resolved in **Level 1** (3 hops).

---

## 8) Block Scoping Internals (ES6 Blocks)

How does the JavaScript engine handle block scopes inside an existing Function Execution Context?

When execution enters an `if`, `for`, or `{}` block containing `let` or `const` declarations:
1. The engine creates a **new Lexical Environment** for the block.
2. The block's `OuterEnvReference` is set to the current execution context's active environment.
3. When execution leaves the block, the engine restores the previous Lexical Environment.

```js
function process(flag) {
  let a = 1;
  
  if (flag) {
    let b = 2; // New block-level Lexical Environment created here
    console.log(a + b); // 3 (Looks up 'a' in outer function environment)
  } // Block Lexical Environment discarded
  
  // console.log(b); // ReferenceError: b is not defined
}

process(true);
```

---

## 9) Common Interview Questions & Gotchas

### Q1: What will this code print?

```js
var a = 1;

function b() {
  a = 10;
  return;
  function a() {}
}

b();
console.log(a);
```

<details>
<summary><b>View Answer & Detailed Walkthrough</b></summary>

**Output**: `1`

**Explanation**:
1. Inside `b()`, `function a() {}` is a **function declaration**.
2. During the **Creation Phase** of `b()`, a local identifier `a` is registered in `b`'s Lexical Environment and initialized to the function.
3. During the **Execution Phase** of `b()`, `a = 10` assigns `10` to the **local** `a` (overwriting the local function reference).
4. `b()` returns without touching global `a`.
5. Global `a` remains `1`.
</details>

---

### Q2: How does `eval` or `with` affect the Scope Chain?

- The `with` statement modifies the scope chain by adding the passed object to the top of the scope chain during execution.
- `eval()` can introduce new bindings into the local environment dynamically at runtime.
- **Impact**: Both break compiler optimizations (like hidden classes and inline caching in V8) because the engine cannot statically determine where variables reside. Both are **forbidden in strict mode (`"use strict";`)**.

---

### Q3: What is the difference between Scope and Context?

| Feature | Scope | Context (`this`) |
| :--- | :--- | :--- |
| **Meaning** | Accessibility and lifetime of variables | The object that owns or executes the current function |
| **Determined by** | Where the code/function is **written** (Lexical) | How the function is **called** at runtime (`this` binding) |
| **Mechanics** | Environment Records & Scope Chain | Call-site rules (implicit, explicit, `new`, arrow) |

---

## 10) Summary & Quick Revision Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                        SCOPE CHAIN SUMMARY MATRIX                          |
+───────────────────────────+────────────────────────────────────────────────+
| Concept                   | Key Rule / Takeaway                            |
+───────────────────────────+────────────────────────────────────────────────+
| Lexical Scoping           | Scope is static, determined at write-time.     |
| Lexical Environment       | Environment Record + OuterEnvReference link.   |
| Scope Chain Lookup        | Bottom-up, inner-to-outer, stops at 1st match. |
| Outer Scope Isolation     | Parents cannot see inside children's scopes.   |
| Block Scope               | `{}` creates new Lexical Env for `let`/`const`.|
| Variable Shadowing        | Local declaration hides outer same-name var.   |
| Illegal Shadowing         | Cannot shadow `let`/`const` with `var` in block|
| Lexical Lookup Failure    | Returns `ReferenceError: x is not defined`.    |
+───────────────────────────+────────────────────────────────────────────────+
```

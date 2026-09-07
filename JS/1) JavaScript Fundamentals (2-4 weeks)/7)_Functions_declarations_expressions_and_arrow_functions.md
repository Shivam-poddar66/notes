# Functions: Declarations, Expressions, and Arrow Functions

## 1) Functions as First-Class Citizens

In JavaScript, functions are **First-Class Objects**. This means functions can be:
- Assigned to variables, object properties, or array elements.
- Passed as arguments to other functions (callbacks).
- Returned as values from other functions (higher-order functions).

```js
// Functions stored in an array
const operations = [
  function (a, b) { return a + b; },
  function (a, b) { return a - b; }
];

console.log(operations[0](5, 3)); // 8
```

---

## 2) Function Declarations

A **Function Declaration** defines a named function statement.

```js
function calcTotal(price, tax) {
  return price + (price * tax);
}
```

### Hoisting Behavior
Function declarations are **fully hoisted** to the top of their enclosing scope along with their function body. You can invoke them in code *before* their line of declaration.

```js
console.log(greet("Alice")); // "Hello, Alice!" (Works due to full hoisting)

function greet(name) {
  return `Hello, ${name}!`;
}
```

---

## 3) Function Expressions

A **Function Expression** defines a function inside an expression (typically assigned to a variable).

### Anonymous vs. Named Function Expressions
```js
// Anonymous Function Expression
const multiply = function (a, b) {
  return a * b;
};

// Named Function Expression (Helpful for recursion and stack traces)
const factorial = function computeFactorial(n) {
  if (n <= 1) return 1;
  return n * computeFactorial(n - 1);
};
```

### Hoisting Behavior
Function expressions follow standard variable hoisting rules:
- If declared with `const` or `let`, the variable is in the **Temporal Dead Zone (TDZ)** before assignment.
- Calling a function expression before its declaration throws a `ReferenceError` or `TypeError`.

```js
// console.log(add(2, 3)); // ReferenceError: Cannot access 'add' before initialization
const add = function (a, b) {
  return a + b;
};
```

---

## 4) Arrow Functions (ES6)

Arrow functions provide a compact syntax for writing function expressions and feature **lexical binding** for `this`.

### Concise Syntax Variations
```js
// 1. Standard Multi-line Arrow Function
const divide = (a, b) => {
  const result = a / b;
  return result;
};

// 2. Single Expression with Implicit Return
const square = x => x * x; // Single parameter does not require parentheses ()

// 3. Implicit Return of Object Literals (Must wrap object in parentheses!)
const makeUser = (id, name) => ({ id: id, name: name });
```

### Key Differences in Arrow Functions
1. **Lexical `this`**: Arrow functions do **not** bind their own `this`. They inherit `this` from the surrounding enclosing execution context.
2. **No `arguments` Object**: Arrow functions do not possess their own `arguments` object. Use rest parameters (`...args`) instead.
3. **Cannot be Constructor Functions**: Calling an arrow function with `new` throws `TypeError: ... is not a constructor`.
4. **No `prototype` Property**: Arrow functions do not have a `prototype` object.

---

## 5) Detailed Function Comparison Matrix

| Feature | Function Declaration | Function Expression | Arrow Function |
| :--- | :--- | :--- | :--- |
| **Syntax** | `function foo() {}` | `const foo = function() {}` | `const foo = () => {}` |
| **Hoisting** | Fully Hoisted (Callable before line) | Variable Hoisted (TDZ/`undefined`) | Variable Hoisted (TDZ/`undefined`) |
| **`this` Binding** | Dynamic (Caller-dependent) | Dynamic (Caller-dependent) | **Lexical** (Enclosing scope) |
| **Can use `new`?** | **Yes** (Constructors) | **Yes** (Constructors) | **No** (Throws `TypeError`) |
| **Has `arguments`?**| **Yes** | **Yes** | **No** (Use `...args`) |
| **Best Used For** | Top-level module/utility functions | Conditionally assigned handlers | Callbacks, array methods, short pipeline functions |

---

## 6) When NOT to Use Arrow Functions

### 1. Object Methods (Needing Dynamic `this`)
```js
const counter = {
  count: 0,
  // BUG: Arrow function binds 'this' to global window/undefined!
  badIncrement: () => {
    this.count++; // NaN or error
  },
  // CORRECT: Method shorthand has dynamic 'this'
  goodIncrement() {
    this.count++;
  }
};
```

### 2. Event Listeners (Needing Element `this`)
```js
const button = document.querySelector("#submitBtn");

// BAD: 'this' will NOT point to the button element!
button.addEventListener("click", () => {
  // this.classList.add("clicked"); // Bug!
});

// CORRECT: Standard function binds 'this' to the clicked DOM element
button.addEventListener("click", function () {
  this.classList.add("clicked");
});
```

### 3. Object Prototypes
```js
function Car(make) {
  this.make = make;
}

// BAD: Arrow function breaks prototype 'this'
// Car.prototype.getMake = () => this.make; // Bug!

// CORRECT: Standard function expression
Car.prototype.getMake = function () {
  return this.make;
};
```

---

## 7) Naming Conventions & Design Best Practices
- **Use Action Verbs**: Prefix function names with verbs describing what they do (`getUserData`, `calculateTotal`, `isEmailValid`, `renderHeader`).
- **Single Responsibility Principle (SRP)**: Each function should perform one clear task.
- **Keep Functions Small**: Short, focused functions are easier to test, debug, and reuse.

---

## 8) Quick Practice & Exercises

### Exercise 1: Spot and Fix the Bug
Why does `user.greet()` output `undefined` and how do you fix it?

```js
const user = {
  name: "Shivam",
  greet: () => {
    return `Hello, my name is ${this.name}`;
  }
};

console.log(user.greet());
```

<details>
<summary>View Solution</summary>

**Cause**: Arrow functions have a lexical `this`, which inherits `this` from the outer global/module scope (where `this.name` is `undefined`), rather than binding to `user`.

**Fix**: Use standard method shorthand:
```js
const user = {
  name: "Shivam",
  greet() {
    return `Hello, my name is ${this.name}`;
  }
};
```
</details>

---

### Exercise 2: Refactor to Concise Arrow Functions
Refactor the following function to a single-line arrow function with implicit return:

```js
function getAdults(users) {
  return users.filter(function (user) {
    return user.age >= 18;
  });
}
```

<details>
<summary>View Solution</summary>

```js
const getAdults = users => users.filter(user => user.age >= 18);
```
</details>

# Parameters, Returns, Scope, and Pure Functions

## 1) Function Parameters and Arguments

- **Parameters**: Variable names listed in the function definition.
- **Arguments**: Real values passed to the function when it is invoked.

```js
function greet(name, role) { // 'name' and 'role' are parameters
  return `${name} is a ${role}`;
}

greet("Alice", "Developer"); // "Alice" and "Developer" are arguments
```

---

### Default Parameters (ES6)
Default parameters allow parameters to be initialized with default values if **no value** or **`undefined`** is passed.

```js
function createServer(port = 8080, host = "localhost") {
  return `Server running on ${host}:${port}`;
}

console.log(createServer());           // "Server running on localhost:8080"
console.log(createServer(3000));       // "Server running on localhost:3000"
console.log(createServer(undefined));  // "Server running on localhost:8080" (Triggers default)
console.log(createServer(null));       // "Server running on null:null" (null does NOT trigger default!)
```

> **Note**: Default parameter expressions are evaluated at call time from left to right, meaning earlier parameters can be used in later default expressions:
> ```js
> function calculateDiscount(price, discount = price * 0.1) {
>   return price - discount;
> }
> ```

---

### Rest Parameters (`...args`)
The **Rest Parameter** syntax allows a function to accept an indefinite number of arguments as a **real Array**.

```js
// Must be the LAST parameter in the function signature!
function sumAll(initialValue, ...numbers) {
  return numbers.reduce((total, num) => total + num, initialValue);
}

console.log(sumAll(10, 1, 2, 3, 4)); // 20
```

---

### Parameter Destructuring
Extract properties directly from object or array arguments in the parameter list:

```js
// Object destructuring in parameter list with defaults
function displayUserProfile({ name, age, status = "Active" }) {
  console.log(`${name} (${age}): ${status}`);
}

displayUserProfile({ name: "Bob", age: 30 }); // "Bob (30): Active"
```

---

### Argument Passing: Value vs. Reference
- **Primitives** (strings, numbers, booleans) are passed by **value** (copied). Modifying the parameter inside the function does not affect the original variable.
- **Objects & Arrays** are passed by **reference**. Modifying properties inside the function mutates the original object!

```js
// Primitive: Pass-by-value
function updatePrimitive(x) {
  x = 100;
}
let num = 10;
updatePrimitive(num);
console.log(num); // 10 (Unchanged)

// Object: Pass-by-reference
function updateObject(obj) {
  obj.score = 99; // Mutates original object!
}
const player = { score: 0 };
updateObject(player);
console.log(player.score); // 99 (Mutated!)
```

---

## 2) Return Values & Control Flow

1. **Implicit `undefined` Return**: If a function does not contain an explicit `return` statement, or if `return;` is executed without a value, the function automatically evaluates to `undefined`.
2. **Returning Multiple Values**: Use Arrays or Objects to return multiple items from a single function:
   ```js
   function getMinMax(numbers) {
     return {
       min: Math.min(...numbers),
       max: Math.max(...numbers)
     };
   }
   
   const { min, max } = getMinMax([5, 2, 9, 1]);
   ```

---

## 3) Scope & The Scope Chain

**Scope** determines the accessibility (visibility) of variables in different parts of code.

### 1. Global Scope
Variables declared outside any function or block `{}`. Accessible anywhere in the program.
- *Risk*: Polluting global scope can lead to variable collisions and hard-to-debug side effects.

### 2. Function (Local) Scope
Variables declared inside a function body. Accessible only within that function.

```js
function printSecret() {
  const secret = "SuperSecret123";
  console.log(secret); // Accessible inside
}
// console.log(secret); // ReferenceError: secret is not defined
```

### 3. Block Scope (ES6)
Variables declared with `let` or `const` inside any pair of curly braces `{}` (e.g., `if`, `for`, `while`).

```js
if (true) {
  var varVariable = "I leak out";
  let letVariable = "I stay inside block";
}

console.log(varVariable); // "I leak out"
// console.log(letVariable); // ReferenceError: letVariable is not defined
```

### 4. Lexical Scope & Scope Chain
Inner functions have access to variables declared in their outer enclosing scopes. JavaScript resolves variable references by moving **upward through the Scope Chain** until it finds the variable or reaches the Global Scope.

```js
const globalVar = "Global";

function outer() {
  const outerVar = "Outer";

  function inner() {
    const innerVar = "Inner";
    console.log(`${globalVar} -> ${outerVar} -> ${innerVar}`);
  }
  
  inner();
}

outer(); // Logs: "Global -> Outer -> Inner"
```

---

## 4) Pure vs. Impure Functions

### Pure Functions
A function is **Pure** if it satisfies **two mandatory conditions**:
1. **Deterministic**: Returns the exact same output for the exact same input every time.
2. **No Side Effects**: Does not mutate outside state, modify arguments, or interact with external I/O.

```js
// PURE FUNCTION
function add(a, b) {
  return a + b; // Depends ONLY on arguments, returns value, no side effects
}
```

### What Counts as a Side Effect?
- Modifying a global variable or outer scope variable.
- Mutating object/array parameters passed into the function.
- DOM manipulation (`document.getElementById`).
- Printing to console (`console.log`) or writing to a file/database.
- Triggering HTTP network calls (`fetch`, `axios`).
- Using non-deterministic functions (`Math.random()`, `Date.now()`).

### Impure Functions (Examples)

```js
// Impure Example 1: Depends on external mutable state
let taxRate = 0.08;
function calculateTotal(price) {
  return price + (price * taxRate); // Impure: Result changes if taxRate changes
}

// Impure Example 2: Mutates external argument
function addItem(cart, item) {
  cart.push(item); // Impure: Mutates the original 'cart' array!
  return cart;
}

// Impure Example 3: Non-deterministic
function generateID(prefix) {
  return `${prefix}_${Math.random()}`; // Impure: Output varies for same input
}
```

### Refactoring Impure Functions to Pure Functions

```js
// --- IMPURE ---
let cart = ["Apple"];
function addToCartImpure(item) {
  cart.push(item); // Side effect: mutates global cart
}

// --- REFACTORED TO PURE ---
function addToCartPure(currentCart, item) {
  return [...currentCart, item]; // Returns a NEW array without mutating original
}

const initialCart = ["Apple"];
const updatedCart = addToCartPure(initialCart, "Banana");
console.log(initialCart); // ["Apple"] (Original preserved!)
console.log(updatedCart); // ["Apple", "Banana"]
```

---

## 5) Benefits of Pure Functions
- **Testability**: Pure functions are effortless to unit test (no mock databases, DOM, or global state setup needed).
- **Predictability**: Eliminates unexpected bugs caused by shared state mutations.
- **Cacheability (Memoization)**: Since same inputs yield same outputs, heavy pure calculations can be cached easily.

---

## 6) Quick Practice & Exercises

### Exercise 1: Identify Pure vs. Impure
Classify each function as **Pure** or **Impure**:

```js
// Function A
const doubleAll = arr => arr.map(x => x * 2);

// Function B
let count = 0;
const increment = () => ++count;

// Function C
const getTime = () => new Date().toISOString();
```

<details>
<summary>View Solutions</summary>

- **Function A**: **Pure** (`Array.prototype.map` creates a new array without mutating `arr`; returns deterministic output).
- **Function B**: **Impure** (Mutates external `count` variable).
- **Function C**: **Impure** (Non-deterministic output depending on current time).
</details>

---

### Exercise 2: Refactor to Pure Function
Refactor `updateAge` so it does not mutate the `user` parameter object:

```js
function updateAge(user, newAge) {
  user.age = newAge;
  return user;
}
```

<details>
<summary>View Solution</summary>

```js
function updateAgePure(user, newAge) {
  return {
    ...user,
    age: newAge
  };
}
```
</details>

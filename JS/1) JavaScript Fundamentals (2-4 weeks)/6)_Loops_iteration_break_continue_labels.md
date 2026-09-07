# Loops, Iteration, `break`, `continue`, and Labeled Statements

## 1) Overview of Loops in JavaScript

Loops automate repetitive tasks by executing a block of code multiple times based on condition checks or iterables.

### Loop Classification Summary

| Loop Type | Best Suited For | Target Data Type |
| :--- | :--- | :--- |
| **`for`** | Known iteration count or index-based control | Arrays, Numbers |
| **`while`** | Unknown iteration count controlled by a dynamic condition | Booleans / Dynamic states |
| **`do...while`** | Tasks that must run **at least once** before checking condition | User input prompts, retry attempts |
| **`for...of`** | Iterating over **values** of an iterable | Arrays, Strings, Maps, Sets |
| **`for...in`** | Iterating over **keys / properties** of an object | Objects (Enumerable properties) |

---

## 2) Standard Control Loops

### The `for` Loop
Executes code a specific number of times.

```js
// Syntax: for (initializer; condition; final-expression)
for (let i = 0; i < 5; i++) {
  console.log(`Index: ${i}`);
}
```

#### Closure Scope in `for` Loops (`let` vs `var`)
Using `let` creates a new block-scoped binding for `i` on **every single iteration**, fixing classic asynchronous closure bugs associated with `var`.

```js
// --- BUG WITH var ---
for (var i = 0; i < 3; i++) {
  setTimeout(() => console.log(`var: ${i}`), 100); // Logs: 3, 3, 3
}

// --- CORRECT WITH let ---
for (let j = 0; j < 3; j++) {
  setTimeout(() => console.log(`let: ${j}`), 100); // Logs: 0, 1, 2
}
```

---

### The `while` Loop
Evaluates condition **before** executing the loop body. Runs as long as condition remains **truthy**.

```js
let count = 3;

while (count > 0) {
  console.log(`Countdown: ${count}`);
  count--; // Crucial: Modify condition to prevent infinite loops!
}
```

---

### The `do...while` Loop
Executes the block **first**, then evaluates the condition. Guaranteed to run **at least once**.

```js
let attempts = 0;

do {
  attempts++;
  console.log(`Attempt #${attempts}`);
} while (attempts < 0); // Condition is false, but block ran 1 time
```

---

## 3) Modern Iteration: `for...of` vs. `for...in`

### `for...of` (Iterating Values)
Iterates over data contained inside **Iterable objects** (`Array`, `String`, `Map`, `Set`, `NodeList`, `arguments`).

```js
const colors = ["red", "green", "blue"];

for (const color of colors) {
  console.log(color); // "red", "green", "blue"
}

// Iterating over string characters
for (const char of "JS") {
  console.log(char); // "J", "S"
}
```

> **Key Feature**: Supports `break`, `continue`, and `return` (unlike `Array.prototype.forEach()`).

---

### `for...in` (Iterating Enumerable Property Keys)
Iterates over all **enumerable property names (keys)** of an object, including properties inherited from its prototype chain.

```js
const user = { name: "Alice", age: 25, role: "Admin" };

for (const key in user) {
  // Use Object.hasOwn() to filter out inherited prototype properties
  if (Object.hasOwn(user, key)) {
    console.log(`${key}: ${user[key]}`);
  }
}
```

### `for...of` vs. `for...in` Comparison Table

| Feature | `for...of` | `for...in` |
| :--- | :--- | :--- |
| **Iterates over** | **Values** of an iterable | **Keys / Indexes** of an object |
| **Works on** | Arrays, Strings, Maps, Sets | Plain Objects |
| **Array Behavior** | Yields array element values (`10, 20`) | Yields array indexes as **strings** (`"0", "1"`) |
| **Prototype Chain** | Ignores prototype properties | Includes inherited enumerable properties |

> **Warning**: Never use `for...in` to iterate Arrays. Index order is not guaranteed and array methods added to prototypes will leak into the loop.

---

## 4) Loop Control: `break` and `continue`

- **`break`**: Immediately terminates the innermost enclosing loop and transfers control to the statement following the loop.
- **`continue`**: Skips the remaining code in the current iteration and jumps directly to the next iteration step.

```js
// --- break Example ---
for (let i = 1; i <= 10; i++) {
  if (i === 5) break; // Stops loop when i reaches 5
  console.log(i); // Logs 1, 2, 3, 4
}

// --- continue Example ---
for (let i = 1; i <= 5; i++) {
  if (i % 2 === 0) continue; // Skip even numbers
  console.log(i); // Logs 1, 3, 5
}
```

---

## 5) Labeled Statements (Breaking Nested Loops)

By default, `break` or `continue` only affects the immediate loop in which it resides. A **label** provides an identifier to a statement, allowing `break` or `continue` to target outer loops.

### Syntax
```js
labelName: statement
```

### Practical Matrix Search Example
Search a 2D matrix for a target value and exit both inner and outer loops immediately upon discovery:

```js
const matrix = [
  [1, 2, 3],
  [4, 99, 6],
  [7, 8, 9]
];

const target = 99;
let foundPosition = null;

// Attach label 'matrixLoop' to the outer loop
matrixLoop: for (let r = 0; r < matrix.length; r++) {
  for (let c = 0; c < matrix[r].length; c++) {
    if (matrix[r][c] === target) {
      foundPosition = { row: r, col: c };
      break matrixLoop; // Breaks outer 'matrixLoop' entirely!
    }
  }
}

console.log(foundPosition); // { row: 1, col: 1 }
```

> **Clean Code Tip**: Use labels sparingly. Overusing labels can make code flow harder to trace. In functions, returning early (`return`) is often cleaner than labeled breaks.

---

## 6) Iterating Objects Safely in Modern JS

Instead of `for...in`, the recommended modern approach for iterating objects is combining `Object.keys()`, `Object.values()`, or `Object.entries()` with `for...of`:

```js
const product = { id: 101, name: "Laptop", price: 999 };

// 1. Iterating Keys and Values together with Object.entries()
for (const [key, value] of Object.entries(product)) {
  console.log(`${key} -> ${value}`);
}

// 2. Iterating Values directly
for (const val of Object.values(product)) {
  console.log(val);
}
```

---

## 7) Best Practices Checklist
1. **Prefer `for...of` for Iterables**: Default to `for...of` when looping over arrays, strings, or sets.
2. **Avoid `for...in` on Arrays**: Use `for...of` or higher-order array methods (`map`, `filter`, `forEach`).
3. **Use `Object.entries()` for Objects**: Pair `Object.entries(obj)` with `for...of` for clean key-value object iteration.
4. **Prevent Infinite Loops**: Ensure loop counters or conditions are modified properly inside `while` and `for` loops.
5. **Flatten Nested Loops**: Use early `return` or labeled breaks to exit deep loops efficiently.

---

## 8) Quick Practice & Exercises

### Exercise 1: Async Closure Output Prediction
What will be logged to the console?

```js
for (var i = 0; i < 3; i++) {
  setTimeout(() => console.log("A:", i), 10);
}

for (let j = 0; j < 3; j++) {
  setTimeout(() => console.log("B:", j), 10);
}
```

<details>
<summary>View Solution</summary>

- `A: 3`, `A: 3`, `A: 3` (`var` is function-scoped; all timeouts reference the final mutated `i` value of `3`).
- `B: 0`, `B: 1`, `B: 2` (`let` is block-scoped; a fresh `j` binding is created per iteration pass).
</details>

---

### Exercise 2: Object Transformation with `for...of`
Write a function `doublePrices(obj)` that takes an object of items and prices and returns a new object with all prices doubled using `Object.entries()` and `for...of`:

```js
const cart = { apple: 2, banana: 1, cherry: 5 };
```

<details>
<summary>View Solution</summary>

```js
function doublePrices(items) {
  const result = {};
  for (const [item, price] of Object.entries(items)) {
    result[item] = price * 2;
  }
  return result;
}

console.log(doublePrices({ apple: 2, banana: 1, cherry: 5 }));
// Output: { apple: 4, banana: 2, cherry: 10 }
```
</details>

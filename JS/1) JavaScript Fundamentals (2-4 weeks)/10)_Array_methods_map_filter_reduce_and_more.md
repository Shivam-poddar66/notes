# Higher-Order Array Methods: `map`, `filter`, `reduce`, and More

## 1) Overview of Higher-Order Array Methods

A **Higher-Order Method** is an array method that accepts a callback function as an argument. Functional array methods allow declarative data transformations without manual loop index tracking.

### The Callback Signature
Most iteration methods pass three arguments to your callback function:
1. `element`: The current item being processed.
2. `index`: The zero-based index of the current element.
3. `array`: The original array being iterated.

```js
arr.method((element, index, array) => { /* logic */ });
```

---

## 2) Transformation Methods

### `Array.prototype.map()`
Transforms every element 1-to-1 and returns a **new array** of the exact same length.

```js
const prices = [10, 20, 30];
const taxedPrices = prices.map(price => price * 1.18);
console.log(taxedPrices); // [11.8, 23.6, 35.4]

// Extracting object properties
const users = [{ name: "Alice", age: 25 }, { name: "Bob", age: 30 }];
const names = users.map(u => u.name); // ["Alice", "Bob"]
```

> **Warning**: Do not use `map` if you aren't returning a value from the callback or using the resulting array. Use `forEach` for side effects.

### `Array.prototype.flatMap()` (ES2019)
Maps each element using a mapping function, then flattens the result by 1 level into a new array.

```js
const sentences = ["Hello World", "JS Functional Programming"];

// Standard map creates nested arrays
const nested = sentences.map(s => s.split(" ")); // [["Hello", "World"], ["JS", "Functional", "Programming"]]

// flatMap maps and flattens in a single pass
const words = sentences.flatMap(s => s.split(" "));
console.log(words); // ["Hello", "World", "JS", "Functional", "Programming"]
```

---

## 3) Filtering & Search Methods

### `Array.prototype.filter()`
Evaluates every element against a predicate condition and returns a **new array** containing only elements where the predicate returned a **truthy** value.

```js
const products = [
  { name: "Laptop", price: 1000, inStock: true },
  { name: "Mouse", price: 25, inStock: false },
  { name: "Keyboard", price: 75, inStock: true }
];

const availableProducts = products.filter(p => p.inStock);
// [{ name: "Laptop", ... }, { name: "Keyboard", ... }]
```

### `Array.prototype.find()` and `findLast()`
- `find()`: Returns the **first element** that satisfies the condition, or `undefined`.
- `findLast()` (ES2022): Scans backwards and returns the **last element** satisfying the condition.

```js
const numbers = [5, 12, 8, 130, 44];

console.log(numbers.find(n => n > 10));     // 12 (First match)
console.log(numbers.findLast(n => n > 10)); // 44 (Last match)
```

---

## 4) Accumulation: `Array.prototype.reduce()`

`reduce()` executes a user-supplied "reducer" callback on each element of the array, passing in the return value from the calculation on the preceding element. It boils an entire array down to a **single value** (primitive, object, or array).

### Syntax
```js
arr.reduce((accumulator, currentValue, index, array) => {
  return updatedAccumulator;
}, initialValue);
```

> **IMPORTANT**: Always provide an `initialValue`! Omitting `initialValue` uses `arr[0]` as the initial accumulator and starts iteration at index 1. If the array is empty and no `initialValue` is provided, JS throws a `TypeError`.

### Pattern 1: Summing Numeric Arrays
```js
const cartPrices = [29.99, 9.99, 4.99];

const total = cartPrices.reduce((acc, price) => acc + price, 0);
console.log(total); // 44.97
```

### Pattern 2: Counting Frequency / Histogram
```js
const letters = ["a", "b", "a", "c", "b", "a"];

const letterCounts = letters.reduce((counts, char) => {
  counts[char] = (counts[char] ?? 0) + 1;
  return counts;
}, {});

console.log(letterCounts); // { a: 3, b: 2, c: 1 }
```

### Pattern 3: Grouping Objects by Category
```js
const people = [
  { name: "Alice", role: "Admin" },
  { name: "Bob", role: "User" },
  { name: "Charlie", role: "Admin" }
];

const groupedByRole = people.reduce((groups, person) => {
  const role = person.role;
  groups[role] ??= [];
  groups[role].push(person);
  return groups;
}, {});

/*
Output:
{
  Admin: [{ name: "Alice", ... }, { name: "Charlie", ... }],
  User:  [{ name: "Bob", ... }]
}
*/
```

---

## 5) Boolean Testing: `some()` and `every()`

- **`some()`**: Returns `true` if **at least one** element passes the condition. Short-circuits immediately upon finding a match.
- **`every()`**: Returns `true` if **all** elements pass the condition. Short-circuits immediately upon finding a failure.

```js
const scores = [45, 80, 92, 60];

const hasFailingGrade = scores.some(s => s < 50); // true (45 < 50)
const allPassed = scores.every(s => s >= 50);      // false (short-circuits at 45)
```

> **Vacuous Truth**: `[].every(condition)` returns `true` for any condition on an empty array!

---

## 6) Method Chaining (Functional Pipelines)

You can chain multiple higher-order methods together to build readable data transformation pipelines.

```js
const transactions = [
  { id: 1, type: "deposit", amount: 100 },
  { id: 2, type: "withdrawal", amount: 50 },
  { id: 3, type: "deposit", amount: 200 }
];

// Pipeline: Filter deposits -> Extract amounts -> Sum total
const totalDeposits = transactions
  .filter(t => t.type === "deposit")
  .map(t => t.amount)
  .reduce((sum, amount) => sum + amount, 0);

console.log(totalDeposits); // 300
```

---

## 7) Quick Method Summary Matrix

| Method | Purpose | Returns | Mutates Original? |
| :--- | :--- | :--- | :--- |
| **`map()`** | Transform elements 1-to-1 | New Array | **No** |
| **`flatMap()`** | Transform and flatten 1 level | New Array | **No** |
| **`filter()`** | Select elements by condition | New Array | **No** |
| **`reduce()`** | Accumulate elements into single value | Any Value | **No** |
| **`find()`** | Locate first matching element | Element / `undefined` | **No** |
| **`some()`** | Check if any element matches | Boolean | **No** |
| **`every()`** | Check if all elements match | Boolean | **No** |

---

## 8) Best Practices Checklist
1. **Always Supply `initialValue` to `reduce()`**: Avoid runtime errors when arrays are empty.
2. **Keep Callbacks Pure**: Do not perform side effects inside `map`, `filter`, or `reduce`. Use `forEach` when performing side effects (like updating DOM or logging).
3. **Chain Methods for Clarity**: Prefer chaining `filter().map()` over complex imperative loops.

---

## 9) Quick Practice & Exercises

### Exercise 1: Grouping with `reduce()`
Given an array of numbers, group them into `{ even: [...], odd: [...] }` using `reduce()`:

```js
const numbers = [1, 2, 3, 4, 5, 6];
```

<details>
<summary>View Solution</summary>

```js
const grouped = numbers.reduce((acc, num) => {
  const key = num % 2 === 0 ? "even" : "odd";
  acc[key].push(num);
  return acc;
}, { even: [], odd: [] });

console.log(grouped);
// Output: { even: [2, 4, 6], odd: [1, 3, 5] }
```
</details>

---

### Exercise 2: Chaining Pipeline
Given an array of users, write a pipeline that gets the average age of active users:

```js
const users = [
  { name: "Alice", age: 25, active: true },
  { name: "Bob", age: 30, active: false },
  { name: "Charlie", age: 35, active: true }
];
```

<details>
<summary>View Solution</summary>

```js
const activeUsers = users.filter(u => u.active);
const avgAge = activeUsers.reduce((sum, u) => sum + u.age, 0) / activeUsers.length;

console.log(avgAge); // 30
```
</details>

# Arrays: Creation, Access, Mutation, and Iteration

## 1) Array Fundamentals and Creation

An **Array** in JavaScript is an ordered list of values. Under the hood, JavaScript arrays are specialized objects with numeric keys and a dynamic `.length` property.

### Array Creation Syntax

```js
// 1. Array Literals (Preferred)
const fruits = ["Apple", "Banana", "Cherry"];

// 2. Array Constructor
const numbers = new Array(1, 2, 3); // Creates [1, 2, 3]
const emptySlots = new Array(5);    // Pitfall: Creates a sparse array with 5 empty slots!

// 3. Array.of() - Solves the Array constructor single-integer pitfall
const singleItem = Array.of(5);     // Creates [5]

// 4. Array.from() - Converts array-like objects or iterables into true Arrays
const charArray = Array.from("Hello"); // ["H", "e", "l", "l", "o"]
const range = Array.from({ length: 3 }, (_, i) => i + 1); // [1, 2, 3]
```

---

## 2) Accessing and Updating Elements

### Indexing & `Array.prototype.at()`
Arrays are zero-indexed. Accessing an index outside `[0, length - 1]` returns `undefined`.

```js
const colors = ["red", "green", "blue"];

console.log(colors[0]);  // "red"
console.log(colors[10]); // undefined

// ES2022: Array.prototype.at() supports negative relative indexing
console.log(colors.at(-1)); // "blue" (Last element)
console.log(colors.at(-2)); // "green" (Second to last)
```

### The Dynamic `.length` Property
The `.length` property reflects the highest numeric index plus one. Modifying `.length` directly can truncate or expand arrays.

```js
const items = [10, 20, 30, 40, 50];
items.length = 3; // Truncates array!
console.log(items); // [10, 20, 30]

items.length = 0; // Clears the array completely!
console.log(items); // []
```

---

## 3) Mutating vs. Non-Mutating Array Methods

Understanding whether an array method mutates the original array or returns a new array is essential for avoiding unintended side effects.

### Method Classification Table

| Category | Method | Description / Behavior | Modifies Original Array? |
| :--- | :--- | :--- | :--- |
| **Mutation** | `push(...items)` | Adds items to the **end** of array; returns new length | **Yes** |
| **Mutation** | `pop()` | Removes and returns the **last** item | **Yes** |
| **Mutation** | `unshift(...items)`| Adds items to the **beginning** of array; returns new length | **Yes** |
| **Mutation** | `shift()` | Removes and returns the **first** item | **Yes** |
| **Mutation** | `splice(start, deleteCount, ...items)` | Removes/replaces existing elements and/or adds new ones | **Yes** |
| **Mutation** | `reverse()` | Reverses array elements in place | **Yes** |
| **Mutation** | `sort(compareFn)` | Sorts array elements in place | **Yes** |
| **Immutable** | `slice(start, end)` | Extracts a shallow copy section of an array | **No** |
| **Immutable** | `concat(...arrays)` | Combines arrays into a new array | **No** |
| **Immutable** | `join(separator)` | Joins all elements into a single string | **No** |
| **Immutable** | `flat(depth)` | Flattens nested arrays | **No** |
| **Immutable (ES2023)**| `toSorted()`, `toReversed()`, `toSpliced()`, `with()` | Non-mutating equivalents of sort, reverse, splice, and index replacement | **No** |

### Modern Immutable Array Methods (ES2023)
ES2023 introduced non-mutating counterparts to popular mutating methods:

```js
const original = [3, 1, 2];

// ES2023 non-mutating sort
const sorted = original.toSorted();
console.log(original); // [3, 1, 2] (Preserved!)
console.log(sorted);   // [1, 2, 3]

// ES2023 non-mutating index update (.with(index, value))
const updated = original.with(0, 99);
console.log(updated); // [99, 1, 2]
```

---

## 4) Array Iteration Patterns

```js
const scores = [80, 90, 100];

// 1. Traditional for loop (Fastest, allows break/continue)
for (let i = 0; i < scores.length; i++) {
  if (scores[i] === 90) break;
}

// 2. for...of loop (Cleanest value iteration, allows break/continue)
for (const score of scores) {
  console.log(score);
}

// 3. Array.prototype.forEach() (Functional iteration; CANNOT break or continue!)
scores.forEach((score, index) => {
  console.log(`#${index}: ${score}`);
});
```

---

## 5) Searching & Value Verification

```js
const numbers = [10, 20, 30, 40, 50, NaN];

// 1. Existence Checks
numbers.includes(20);  // true
numbers.includes(NaN); // true (includes handles NaN properly!)
numbers.indexOf(NaN);  // -1 (indexOf FAILS on NaN!)

// 2. Finding Elements by Condition
const users = [{ id: 1, name: "Alice" }, { id: 2, name: "Bob" }];

const user = users.find(u => u.id === 2);          // { id: 2, name: "Bob" }
const userIndex = users.findIndex(u => u.id === 2); // 1

// 3. Condition Testing across Elements
const allPositive = [1, 2, 3].every(n => n > 0); // true
const hasNegative = [1, -2, 3].some(n => n < 0); // true
```

---

## 6) Common Array Pitfalls & Gotchas

### Pitfall 1: Default `.sort()` Behavior
By default, `.sort()` converts elements to **strings** and compares them lexicographically (alphabetically).

```js
const nums = [1, 10, 2, 21, 5];
nums.sort();
console.log(nums); // [1, 10, 2, 21, 5] (BUG: "10" comes before "2" alphabetically!)

// FIX: Always provide a comparison function for numeric sorting
nums.sort((a, b) => a - b);
console.log(nums); // [1, 2, 5, 10, 21] (Correct ascending order)
```

### Pitfall 2: Sparse Array Holes
Assigning to an index far beyond `.length` creates empty slots (holes), which behave inconsistently in array methods:

```js
const arr = [1, 2];
arr[5] = 6; // Creates: [1, 2, <3 empty items>, 6]
console.log(arr.length); // 6
```

---

## 7) Best Practices Checklist
1. **Prefer Immutable Operations**: Use spread `[...arr]`, `slice()`, or ES2023 `toSorted()` / `toReversed()` when updating arrays in state management (React / Redux).
2. **Use `arr.at(-1)`**: Access trailing elements with `arr.at(-1)` instead of `arr[arr.length - 1]`.
3. **Always Pass Comparator to `.sort()`**: Always supply `(a, b) => a - b` when sorting numbers.
4. **Use `Array.isArray(val)`**: Always check arrays using `Array.isArray(val)` rather than `typeof val`.

---

## 8) Quick Practice & Exercises

### Exercise 1: Fix the Sorting Bug
Fix the bug in the following code so that prices are sorted in descending order without mutating the original `prices` array:

```js
const prices = [100, 5, 25, 500];
const sortedPrices = prices.sort();
```

<details>
<summary>View Solution</summary>

```js
const prices = [100, 5, 25, 500];

// Using ES2023 toSorted() with descending comparator (b - a)
const sortedPrices = prices.toSorted((a, b) => b - a);

console.log(prices);       // [100, 5, 25, 500] (Unmutated)
console.log(sortedPrices); // [500, 100, 25, 5] (Correct descending)
```
</details>

---

### Exercise 2: Remove Duplicates Purely
Write a function `removeDuplicates(arr)` that returns a new array with all duplicate elements removed:

```js
const numbers = [1, 2, 2, 3, 4, 4, 5];
```

<details>
<summary>View Solution</summary>

```js
function removeDuplicates(arr) {
  return Array.from(new Set(arr));
  // Or using spread: return [...new Set(arr)];
}

console.log(removeDuplicates([1, 2, 2, 3, 4, 4, 5])); // [1, 2, 3, 4, 5]
```
</details>

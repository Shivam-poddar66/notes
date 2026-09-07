# Set and Map Fundamentals

## 1) `Set` Fundamentals

A **`Set`** is a built-in ES6 collection of **unique values**. Duplicate values are automatically ignored.

### Core `Set` API

```js
// 1. Initialization
const tags = new Set(["javascript", "html", "javascript", "css"]);
console.log(tags); // Set(3) { "javascript", "html", "css" }

// 2. Adding & Deleting Values
tags.add("react");    // Chainable!
tags.add("javascript"); // Ignored (Duplicate)
tags.delete("html");  // Returns true if deleted

// 3. Membership Testing (O(1) Constant Time Lookup!)
console.log(tags.has("react")); // true
console.log(tags.has("html"));  // false

// 4. Size & Clearing
console.log(tags.size); // 3
// tags.clear();        // Empties the Set
```

### Deduplication Idiom
The fastest way to remove duplicate elements from an Array:

```js
const numbers = [1, 2, 2, 3, 4, 4, 5];
const uniqueNumbers = [...new Set(numbers)];
console.log(uniqueNumbers); // [1, 2, 3, 4, 5]
```

### Modern Set Operations (ES2024)
ES2024 introduced native mathematical set operations:

```js
const setA = new Set([1, 2, 3]);
const setB = new Set([2, 3, 4]);

// 1. Intersection (Elements in BOTH sets)
console.log(setA.intersection(setB)); // Set(2) { 2, 3 }

// 2. Union (Elements in EITHER set)
console.log(setA.union(setB));        // Set(4) { 1, 2, 3, 4 }

// 3. Difference (Elements in A but NOT B)
console.log(setA.difference(setB));   // Set(1) { 1 }

// 4. Symmetric Difference (Elements in A or B, but NOT both)
console.log(setA.symmetricDifference(setB)); // Set(2) { 1, 4 }

// 5. Subset / Superset / Disjoint Checks
console.log(setA.isSubsetOf(setB));   // false
console.log(setA.isDisjointFrom(setB)); // false (they share 2 and 3)
```

---

## 2) `Map` Fundamentals

A **`Map`** is a key-value collection where **keys can be of ANY data type** (objects, functions, primitives, numbers).

### Core `Map` API

```js
// 1. Initialization
const userRoles = new Map();

// Objects or Primitives as Keys!
const userObj = { id: 101 };
const numberKey = 42;

// 2. Setting Keys (Chainable)
userRoles.set(userObj, "Admin")
         .set(numberKey, "Numeric ID")
         .set("guest", "Viewer");

// 3. Getting & Checking Keys
console.log(userRoles.get(userObj));   // "Admin"
console.log(userRoles.get(numberKey)); // "Numeric ID"
console.log(userRoles.has("guest"));   // true

// 4. Size & Deletion
console.log(userRoles.size); // 3
userRoles.delete("guest");   // Deletes key
```

### Converting Between Objects and Maps

```js
// 1. Plain Object -> Map
const obj = { a: 1, b: 2 };
const mapFromObj = new Map(Object.entries(obj));

// 2. Map -> Plain Object
const map = new Map([["x", 10], ["y", 20]]);
const objFromMap = Object.fromEntries(map);
```

---

## 3) Object vs. Map Comparison Matrix

| Feature | `Object` | `Map` |
| :--- | :--- | :--- |
| **Key Types** | Strings and Symbols only | **Any data type** (Objects, Functions, Primitives) |
| **Key Order** | Complex (Numeric keys sorted, string keys insertion order) | Guaranteed strict **insertion order** |
| **Size Retrieval** | Manual (`Object.keys(obj).length` - $O(N)$) | Direct property (`map.size` - $O(1)$) |
| **Performance** | Optimized for small, fixed structure records | Optimized for frequent additions & removals |
| **Default Keys** | Inherits prototype keys (`toString`, `valueOf`) | Clean; contains no default keys |
| **Iteration** | Indirect via `Object.keys/values/entries` | Directly iterable with `for...of` |
| **JSON Support** | Native `JSON.stringify(obj)` support | Requires custom serialization wrapper |

---

## 4) Brief Overview: `WeakSet` and `WeakMap`

`WeakSet` and `WeakMap` are garbage-collected variants:
- Keys **MUST be objects or symbols**.
- References to keys are held **weakly**—if an object key has no other references in code, it will be automatically garbage collected.
- **Non-iterable**: No `.size` property, no `.keys()`, `.values()`, or `for...of` iteration (because garbage collection is non-deterministic).

### Primary Use Case: Private Instance Metadata / Caching
```js
const privateData = new WeakMap();

class User {
  constructor(secret) {
    // Store secret metadata keyed by object instance without memory leaks
    privateData.set(this, { secret });
  }

  getSecret() {
    return privateData.get(this).secret;
  }
}
```

---

## 5) Best Practices Checklist
1. **Use `Set` for $O(1)$ Membership Checks**: Replace `arr.includes(val)` ($O(N)$ scanning) with `set.has(val)` ($O(1)$ lookup) when searching large datasets.
2. **Use `Map` when Keys are Objects or Dynamic**: Choose `Map` whenever key names aren't fixed strings or when keys are object references.
3. **Leverage Modern ES2024 Set Operations**: Use `setA.intersection(setB)` instead of manually writing array `filter` and `includes` logic.

---

## 6) Quick Practice & Exercises

### Exercise 1: Word Frequency Histogram
Write a function `countWordFrequencies(text)` that returns a `Map` counting how many times each word occurs in a sentence:

```js
const text = "apple banana apple cherry banana apple";
```

<details>
<summary>View Solution</summary>

```js
function countWordFrequencies(sentence) {
  const words = sentence.split(" ");
  const frequencyMap = new Map();

  for (const word of words) {
    const currentCount = frequencyMap.get(word) ?? 0;
    frequencyMap.set(word, currentCount + 1);
  }

  return frequencyMap;
}

console.log(countWordFrequencies("apple banana apple cherry banana apple"));
// Output: Map(3) { 'apple' => 3, 'banana' => 2, 'cherry' => 1 }
```
</details>

---

### Exercise 2: Common Array Elements with ES2024 Sets
Given two arrays, return an array containing elements present in both arrays using ES2024 Set methods:

```js
const arr1 = [1, 2, 3, 4, 5];
const arr2 = [3, 4, 5, 6, 7];
```

<details>
<summary>View Solution</summary>

```js
const set1 = new Set(arr1);
const set2 = new Set(arr2);

const common = [...set1.intersection(set2)];
console.log(common); // [3, 4, 5]
```
</details>

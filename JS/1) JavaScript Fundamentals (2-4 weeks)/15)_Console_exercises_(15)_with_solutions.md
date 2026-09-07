# 15 Essential JavaScript Console Exercises (With Solutions)

This workbook contains 15 practical coding exercises designed to reinforce core JavaScript fundamentals: variables, type system, control flow, loops, functions, array methods, objects, sets/maps, and error handling.

> **Instructions**: Attempt to solve each exercise directly in your browser's DevTools Console (`F12` $\rightarrow$ `Console`) or Node REPL before revealing the solution accordion.

---

### Exercise 1: Even or Odd Checker
**Concept Tested**: Modulo operator (`%`), strict equality (`===`).  
**Task**: Write a function `isEven(n)` that returns `true` if a number is even, and `false` if it is odd.

<details>
<summary>View Solution & Test Cases</summary>

```js
function isEven(n) {
  return n % 2 === 0;
}

// Test Cases
console.log(isEven(4));  // true
console.log(isEven(7));  // false
console.log(isEven(0));  // true
console.log(isEven(-2)); // true
```
</details>

---

### Exercise 2: Swap Two Variables In-Place
**Concept Tested**: ES6 Array Destructuring assignment.  
**Task**: Swap the values of two variables `a` and `b` without using a temporary third variable.

<details>
<summary>View Solution & Test Cases</summary>

```js
let a = 10;
let b = 20;

// Swapping using array destructuring
[a, b] = [b, a];

console.log(`a: ${a}, b: ${b}`); // Output: "a: 20, b: 10"
```
</details>

---

### Exercise 3: Reverse a String
**Concept Tested**: String splitting, array reversal, array joining.  
**Task**: Write a function `reverseString(str)` that takes a string and returns it reversed.

<details>
<summary>View Solution & Test Cases</summary>

```js
function reverseString(str) {
  return str.split("").reverse().join("");
}

// Alternative with Spread operator
const reverseStringModern = str => [...str].reverse().join("");

// Test Cases
console.log(reverseString("javascript")); // "tpircsavaj"
console.log(reverseString("hello"));      // "olleh"
```
</details>

---

### Exercise 4: Count Vowels in a String
**Concept Tested**: Strings, array filtering, `includes()`.  
**Task**: Write a function `countVowels(str)` that counts the number of vowels (`a, e, i, o, u`) in a given string, ignoring case.

<details>
<summary>View Solution & Test Cases</summary>

```js
function countVowels(str) {
  const vowels = "aeiou";
  return [...str.toLowerCase()].filter(char => vowels.includes(char)).length;
}

// Test Cases
console.log(countVowels("Hello World")); // 3 ('e', 'o', 'o')
console.log(countVowels("JavaScript"));  // 3 ('a', 'a', 'i')
```
</details>

---

### Exercise 5: Find Maximum Value in an Array
**Concept Tested**: `Math.max()` and the Spread operator (`...`).  
**Task**: Write a function `maxValue(arr)` that returns the largest numeric value in an array.

<details>
<summary>View Solution & Test Cases</summary>

```js
function maxValue(arr) {
  if (arr.length === 0) return undefined;
  return Math.max(...arr);
}

// Test Cases
console.log(maxValue([10, 45, 2, 99, 34])); // 99
console.log(maxValue([-5, -12, -2]));       // -2
```
</details>

---

### Exercise 6: Array Sum using `reduce`
**Concept Tested**: `Array.prototype.reduce()`, initial accumulator values.  
**Task**: Write a function `sumArray(arr)` that calculates the sum of all numbers in an array using `reduce()`.

<details>
<summary>View Solution & Test Cases</summary>

```js
function sumArray(arr) {
  return arr.reduce((total, num) => total + num, 0);
}

// Test Cases
console.log(sumArray([1, 2, 3, 4, 5])); // 15
console.log(sumArray([]));              // 0
```
</details>

---

### Exercise 7: Remove Duplicates from an Array
**Concept Tested**: `Set` data structure and Spread operator (`...`).  
**Task**: Write a function `removeDuplicates(arr)` that returns a new array with all duplicate values removed.

<details>
<summary>View Solution & Test Cases</summary>

```js
function removeDuplicates(arr) {
  return [...new Set(arr)];
}

// Test Cases
console.log(removeDuplicates([1, 2, 2, 3, 4, 4, 5])); // [1, 2, 3, 4, 5]
console.log(removeDuplicates(["a", "b", "a", "c"]));   // ["a", "b", "c"]
```
</details>

---

### Exercise 8: Convert Object Keys and Values to Arrays
**Concept Tested**: `Object.keys()`, `Object.values()`, `Object.entries()`.  
**Task**: Given an object, return an array of its keys and an array of its values.

<details>
<summary>View Solution & Test Cases</summary>

```js
const user = { id: 101, name: "Alice", role: "Developer" };

const keys = Object.keys(user);
const values = Object.values(user);

console.log(keys);   // ["id", "name", "role"]
console.log(values); // [101, "Alice", "Developer"]
```
</details>

---

### Exercise 9: Palindrome Checker
**Concept Tested**: String manipulation, case normalization, regex character filtering.  
**Task**: Write a function `isPalindrome(str)` that checks whether a string reads the same forwards and backwards, ignoring spaces, punctuation, and letter case.

<details>
<summary>View Solution & Test Cases</summary>

```js
function isPalindrome(str) {
  // Strip non-alphanumeric characters and normalize case
  const cleanStr = str.toLowerCase().replace(/[^a-z0-9]/g, "");
  const reversedStr = [...cleanStr].reverse().join("");
  return cleanStr === reversedStr;
}

// Test Cases
console.log(isPalindrome("A man, a plan, a canal: Panama")); // true
console.log(isPalindrome("race a car"));                     // false
console.log(isPalindrome("madam"));                          // true
```
</details>

---

### Exercise 10: Classic FizzBuzz (1 to 100)
**Concept Tested**: `for` loops, conditional `if/else`, modulo operator (`%`).  
**Task**: Print numbers from 1 to 100. For multiples of 3, print `"Fizz"`. For multiples of 5, print `"Buzz"`. For multiples of both 3 and 5, print `"FizzBuzz"`.

<details>
<summary>View Solution & Test Cases</summary>

```js
function fizzBuzz(limit = 100) {
  for (let i = 1; i <= limit; i++) {
    if (i % 15 === 0) {
      console.log("FizzBuzz");
    } else if (i % 3 === 0) {
      console.log("Fizz");
    } else if (i % 5 === 0) {
      console.log("Buzz");
    } else {
      console.log(i);
    }
  }
}

// Run FizzBuzz
fizzBuzz(15);
```
</details>

---

### Exercise 11: Find First Active User
**Concept Tested**: `Array.prototype.find()`.  
**Task**: Given an array of user objects, write a function `findFirstActive(users)` that returns the first user object where `isActive === true`.

<details>
<summary>View Solution & Test Cases</summary>

```js
function findFirstActive(users) {
  return users.find(user => user.isActive === true);
}

// Test Data
const usersList = [
  { id: 1, name: "Alice", isActive: false },
  { id: 2, name: "Bob", isActive: true },
  { id: 3, name: "Charlie", isActive: true }
];

console.log(findFirstActive(usersList)); // { id: 2, name: "Bob", isActive: true }
```
</details>

---

### Exercise 12: Build a Frequency Counter (Histogram)
**Concept Tested**: `Map` data structure, Nullish Coalescing operator (`??`).  
**Task**: Write a function `getFrequency(arr)` that returns a `Map` counting occurrences of each element in an array.

<details>
<summary>View Solution & Test Cases</summary>

```js
function getFrequency(arr) {
  const freqMap = new Map();

  for (const item of arr) {
    freqMap.set(item, (freqMap.get(item) ?? 0) + 1);
  }

  return freqMap;
}

// Test Cases
const items = ["apple", "banana", "apple", "cherry", "banana", "apple"];
console.log(getFrequency(items));
// Output: Map(3) { 'apple' => 3, 'banana' => 2, 'cherry' => 1 }
```
</details>

---

### Exercise 13: Flatten a One-Level Nested Array
**Concept Tested**: `Array.prototype.flat()` or `reduce()` with `concat()`.  
**Task**: Flatten a two-dimensional array by one level into a single flat array.

<details>
<summary>View Solution & Test Cases</summary>

```js
// Option 1: Native ES2019 flat()
function flattenOneLevel(arr) {
  return arr.flat(1);
}

// Option 2: Using reduce and concat
function flattenReduce(arr) {
  return arr.reduce((acc, item) => acc.concat(item), []);
}

// Test Cases
const nested = [[1, 2], [3, 4], [5]];
console.log(flattenOneLevel(nested)); // [1, 2, 3, 4, 5]
console.log(flattenReduce(nested));   // [1, 2, 3, 4, 5]
```
</details>

---

### Exercise 14: Safe Nested Property Access
**Concept Tested**: Optional Chaining (`?.`) and Nullish Coalescing (`??`).  
**Task**: Write a function `getUserCity(user)` that safely returns `user.address.city`, returning `"Unknown City"` if any property in the chain is missing.

<details>
<summary>View Solution & Test Cases</summary>

```js
function getUserCity(user) {
  return user?.address?.city ?? "Unknown City";
}

// Test Cases
console.log(getUserCity({ address: { city: "New York" } })); // "New York"
console.log(getUserCity({ address: {} }));                   // "Unknown City"
console.log(getUserCity(null));                              // "Unknown City"
```
</details>

---

### Exercise 15: Parse and Validate Numeric Input
**Concept Tested**: `try/catch`, `throw`, `Number()`, `Number.isNaN()`.  
**Task**: Write a function `parseAmount(raw)` that parses a string input into a number. Throw a custom `TypeError` if the input is not a valid number. Wrap calls in a `try/catch` block.

<details>
<summary>View Solution & Test Cases</summary>

```js
function parseAmount(raw) {
  const num = Number(raw);
  if (Number.isNaN(num)) {
    throw new TypeError(`Invalid numeric input: "${raw}"`);
  }
  return num;
}

// Safe execution wrapper
function processInput(input) {
  try {
    const amount = parseAmount(input);
    console.log(`Processed amount: $${amount.toFixed(2)}`);
  } catch (err) {
    console.error(`Error: ${err.message}`);
  }
}

// Test Cases
processInput("42.50"); // "Processed amount: $42.50"
processInput("abc");   // "Error: Invalid numeric input: "abc""
```
</details>

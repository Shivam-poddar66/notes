# Console Exercises (15) with Solutions

## Instructions
Solve each exercise first without looking at the solution.

## 1) Check even or odd
```js
function isEven(n) {
  return n % 2 === 0;
}
```

## 2) Swap two numbers
```js
let a = 5, b = 9;
[a, b] = [b, a];
```

## 3) Reverse a string
```js
function reverseString(s) {
  return s.split("").reverse().join("");
}
```

## 4) Count vowels in string
```js
function countVowels(s) {
  return [...s.toLowerCase()].filter(ch => "aeiou".includes(ch)).length;
}
```

## 5) Find max in array
```js
function maxValue(arr) {
  return Math.max(...arr);
}
```

## 6) Sum of array using reduce
```js
const sum = arr => arr.reduce((acc, n) => acc + n, 0);
```

## 7) Remove duplicates from array
```js
const unique = arr => [...new Set(arr)];
```

## 8) Convert object keys to array
```js
const keys = obj => Object.keys(obj);
```

## 9) Check palindrome
```js
function isPalindrome(s) {
  const clean = s.toLowerCase();
  return clean === [...clean].reverse().join("");
}
```

## 10) FizzBuzz (1-100)
```js
for (let i = 1; i <= 100; i++) {
  if (i % 15 === 0) console.log("FizzBuzz");
  else if (i % 3 === 0) console.log("Fizz");
  else if (i % 5 === 0) console.log("Buzz");
  else console.log(i);
}
```

## 11) Find first active user
```js
const firstActive = users => users.find(u => u.isActive);
```

## 12) Build frequency counter (Map)
```js
function frequency(arr) {
  const m = new Map();
  for (const item of arr) m.set(item, (m.get(item) ?? 0) + 1);
  return m;
}
```

## 13) Flatten one-level array
```js
const flat = arr => arr.reduce((acc, item) => acc.concat(item), []);
```

## 14) Safe nested access with default
```js
const city = user?.address?.city ?? "Unknown";
```

## 15) Parse and validate number input
```js
function parseAmount(raw) {
  const n = Number(raw);
  if (Number.isNaN(n)) throw new Error("Amount must be numeric");
  return n;
}
```

## Completion Target
- Re-solve all 15 from memory.
- Add 5 custom exercises from your daily work use-cases.

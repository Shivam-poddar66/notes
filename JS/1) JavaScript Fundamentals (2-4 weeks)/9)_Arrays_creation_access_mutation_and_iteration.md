# Arrays: Creation, Access, Mutation, Iteration

## 1) Array Basics

```js
const numbers = [10, 20, 30];
```

- Zero-based indexing.
- `numbers.length` gives size.

## 2) Access and Update

```js
numbers[0];      // 10
numbers[1] = 25; // update
```

## 3) Common Mutation Methods
- `push` add at end
- `pop` remove from end
- `unshift` add at start
- `shift` remove from start

```js
const items = ["a", "b"];
items.push("c");
items.shift();
```

## 4) Non-Mutating Access Patterns
- `slice` to copy part.
- spread (`[...]`) to copy full array.

```js
const copy = [...items];
const part = items.slice(0, 2);
```

## 5) Iteration Patterns
- `for`
- `for...of`
- `forEach`

```js
for (const item of items) {
  console.log(item);
}
```

## 6) Common Pitfalls
- Accessing out-of-range index returns `undefined`.
- `sort()` is lexicographic by default for strings.
- Mutating original arrays accidentally.

## 7) Quick Practice
1. Reverse an array without built-in `reverse`.
2. Find max value using loop.
3. Remove duplicates using Set and return array.

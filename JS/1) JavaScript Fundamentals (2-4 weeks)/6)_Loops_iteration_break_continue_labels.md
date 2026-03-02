# Loops, Iteration, break/continue, Labels

## 1) Loop Types

### `for`
Best when iteration count is known.

```js
for (let i = 0; i < 5; i++) {
  console.log(i);
}
```

### `while`
Best when count is unknown and condition controls loop.

```js
let i = 0;
while (i < 5) {
  i++;
}
```

### `do...while`
Runs at least once.

```js
let n = 0;
do {
  n++;
} while (n < 3);
```

### `for...of`
Iterates values of iterable (arrays, strings, maps, sets).

```js
for (const value of [10, 20, 30]) {
  console.log(value);
}
```

### `for...in`
Iterates enumerable keys of objects.

```js
for (const key in user) {
  console.log(key, user[key]);
}
```

## 2) `break` and `continue`
- `break`: exit loop immediately.
- `continue`: skip current iteration.

## 3) Labels (Use Rarely)
Useful for breaking outer loops in nested structures.

```js
outer:
for (let i = 0; i < 3; i++) {
  for (let j = 0; j < 3; j++) {
    if (i + j > 2) break outer;
  }
}
```

## 4) Loop Best Practices
- Avoid infinite loops.
- Prefer readable loop conditions.
- Avoid modifying loop array length in unsafe ways.

## 5) Quick Practice
1. Print even numbers 1-100.
2. Find first number divisible by 7 and 9.
3. Iterate object keys and values safely.

# Array Methods: map, filter, reduce, find, some, every, sort, slice, splice

## 1) `map`
Transforms each element and returns new array.

```js
const prices = [100, 200, 300];
const taxed = prices.map((p) => p * 1.18);
```

## 2) `filter`
Returns elements that match condition.

```js
const active = users.filter((u) => u.isActive);
```

## 3) `reduce`
Accumulates values to single result.

```js
const total = [1, 2, 3, 4].reduce((acc, n) => acc + n, 0);
```

## 4) `find`
Returns first matching element or `undefined`.

```js
const admin = users.find((u) => u.role === "admin");
```

## 5) `some` and `every`
- `some`: at least one match.
- `every`: all match.

```js
const hasLowStock = stocks.some((s) => s < 5);
const allPositive = nums.every((n) => n > 0);
```

## 6) `sort`
Mutates original array.

```js
const nums = [10, 2, 30];
nums.sort((a, b) => a - b); // numeric ascending
```

## 7) `slice` vs `splice`
- `slice(start, end)`: non-mutating copy.
- `splice(start, deleteCount, ...items)`: mutates array.

## 8) Best Practices
- Prefer non-mutating operations for predictability.
- Keep callback functions small and pure.
- Always pass comparator for numeric sort.

## 9) Quick Practice
1. Build cart total with `reduce`.
2. Extract active users with `filter` + `map`.
3. Remove one index using `slice` and spread, then compare with `splice`.

# Advanced Language Features Pack

## 1) Deep Destructuring

```js
const order = {
  id: 1,
  customer: { name: "Shivam", location: { city: "Pune" } }
};

const {
  customer: {
    name,
    location: { city }
  }
} = order;
```

## 2) Spread and Rest (Advanced Use)

```js
const base = { role: "user", active: true };
const profile = { ...base, name: "A" };

function log(first, ...others) {
  console.log(first, others);
}
```

## 3) Optional Chaining + Nullish Patterns

```js
const zip = user?.address?.zip ?? "NA";
```

Use to avoid unsafe access and wrong fallback behavior.

## 4) Iterators
An iterator returns `{ value, done }` from `next()`.

```js
const arr = [1, 2];
const it = arr[Symbol.iterator]();
it.next();
```

## 5) Generators
Functions that pause/resume using `yield`.

```js
function* ids() {
  let i = 1;
  while (true) {
    yield i++;
  }
}
```

## 6) Symbols
Unique primitive values often used as hidden object keys.

```js
const ID = Symbol("id");
const obj = { [ID]: 101 };
```

## 7) Practical Use Cases
- Generators for lazy sequences.
- Symbols for non-colliding internal keys.
- Deep destructuring for clean extraction in controlled scenarios.

## 8) Quick Practice
1. Implement custom iterable object.
2. Build generator for paginated IDs.
3. Use symbol key and inspect enumerable behavior.

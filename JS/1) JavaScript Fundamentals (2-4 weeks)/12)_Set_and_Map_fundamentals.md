# Set and Map Fundamentals

## 1) `Set`
Stores unique values.

```js
const ids = new Set([1, 2, 2, 3]);
// Set contains 1, 2, 3
```

### Common Set Operations
- `add(value)`
- `delete(value)`
- `has(value)`
- `size`
- `clear()`

Use case: duplicate removal.

```js
const unique = [...new Set([1, 1, 2, 3])];
```

## 2) `Map`
Stores key-value pairs with keys of any type.

```js
const userRoles = new Map();
userRoles.set("shivam", "admin");
userRoles.set(101, "editor");
```

### Common Map Operations
- `set(key, value)`
- `get(key)`
- `has(key)`
- `delete(key)`
- `size`
- `clear()`

## 3) Object vs Map
- Object keys are strings/symbols.
- Map keys can be any type.
- Map preserves insertion order and has direct size property.

## 4) Iteration

```js
for (const [key, value] of userRoles) {
  console.log(key, value);
}
```

## 5) Best Practices
- Use `Set` when uniqueness is required.
- Use `Map` when key type flexibility or frequent key operations are needed.

## 6) Quick Practice
1. Count frequency of words using `Map`.
2. Remove duplicates from array using `Set`.
3. Compare object key access vs map key access.

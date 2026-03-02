# `Object.freeze`, `Object.seal`, `Object.preventExtensions`, and Immutability

## 1) `Object.preventExtensions(obj)`
- Cannot add new properties.
- Existing properties can still be changed/deleted (if configurable).

## 2) `Object.seal(obj)`
- Cannot add or delete properties.
- Existing writable properties can be changed.

## 3) `Object.freeze(obj)`
- Cannot add/delete/change own properties.
- Highest integrity level for shallow object.

## 4) Check State
- `Object.isExtensible(obj)`
- `Object.isSealed(obj)`
- `Object.isFrozen(obj)`

## 5) Shallow Limitation
`freeze` is shallow. Nested objects can still mutate unless frozen too.

```js
const config = Object.freeze({
  app: "notes",
  nested: { mode: "dev" }
});

config.nested.mode = "prod"; // still possible unless nested frozen
```

## 6) Practical Guidance
- Freeze constant config objects in small apps.
- Use immutable update patterns in state logic.
- For deep immutability, use recursive freeze utility carefully.

## 7) Quick Practice
1. Compare behavior of the three methods.
2. Implement simple deepFreeze helper.
3. Test nested mutation edge case.

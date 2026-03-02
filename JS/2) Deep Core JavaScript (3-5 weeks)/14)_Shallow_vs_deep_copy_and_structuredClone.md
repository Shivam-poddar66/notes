# Shallow vs Deep Copy and `structuredClone`

## 1) Shallow Copy
Only first level is copied; nested references are shared.

```js
const a = { x: 1, nested: { y: 2 } };
const b = { ...a };
b.nested.y = 99;
// a.nested.y is also 99
```

## 2) Common Shallow Copy Tools
- object spread `{ ...obj }`
- array spread `[...arr]`
- `Object.assign({}, obj)`
- `slice` for arrays

## 3) Deep Copy
Copies all nested levels into independent structure.

### `structuredClone`

```js
const deep = structuredClone(a);
deep.nested.y = 500;
// a.nested.y unchanged
```

## 4) JSON Copy Trick (Limitations)

```js
const deep2 = JSON.parse(JSON.stringify(a));
```

Limitations:
- loses functions, `undefined`, `Date`, `Map`, `Set`, and custom prototypes.

## 5) Choose Strategy
- Shallow copy for flat data.
- `structuredClone` for rich nested plain data structures.
- Custom logic for class instances/special data.

## 6) Quick Practice
1. Demonstrate shallow copy bug.
2. Fix with deep copy.
3. Compare spread vs `structuredClone` with nested arrays.

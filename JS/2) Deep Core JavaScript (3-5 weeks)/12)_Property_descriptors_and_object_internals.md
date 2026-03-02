# Property Descriptors and Object Internals

## 1) Descriptor Fields
Each property can have:
- `value`
- `writable`
- `enumerable`
- `configurable`

## 2) Inspect Descriptor

```js
const user = { name: "Shivam" };
Object.getOwnPropertyDescriptor(user, "name");
```

## 3) Define Descriptor

```js
Object.defineProperty(user, "id", {
  value: 101,
  writable: false,
  enumerable: true,
  configurable: false
});
```

## 4) Behavior Impact
- `writable: false` -> value cannot be reassigned.
- `enumerable: false` -> hidden from loops/`Object.keys`.
- `configurable: false` -> cannot delete/reconfigure.

## 5) Define Multiple Properties

```js
Object.defineProperties(user, {
  role: { value: "admin", writable: true },
  active: { value: true, writable: true }
});
```

## 6) Use Cases
- Immutable IDs.
- Internal metadata properties.
- Controlled public API shapes.

## 7) Quick Practice
1. Create read-only `id` field.
2. Hide field from enumeration.
3. Observe behavior for non-configurable property deletion.

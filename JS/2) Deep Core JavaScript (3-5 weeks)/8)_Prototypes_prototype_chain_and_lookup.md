# Prototypes, Prototype Chain, and Property Lookup

## 1) Prototype Basics
Every object in JS has an internal `[[Prototype]]` link.
Property lookup follows this chain upward.

```js
const animal = { eats: true };
const dog = { barks: true };
Object.setPrototypeOf(dog, animal);

dog.eats; // true (from prototype)
```

## 2) Lookup Rule
When `obj.prop` is accessed:
1. Check own properties.
2. If not found, check prototype.
3. Continue until `null` prototype.

## 3) Shared Methods via Prototype
Prototype allows method sharing without duplicating function per object.

## 4) Built-in Prototype Chain
- Arrays inherit from `Array.prototype`.
- Functions inherit from `Function.prototype`.
- Most objects eventually inherit from `Object.prototype`.

## 5) Introspection Helpers
- `Object.getPrototypeOf(obj)`
- `obj.hasOwnProperty(key)`
- `key in obj` (checks own + prototype)

## 6) Best Practices
- Prefer composition and clear prototypes.
- Avoid changing built-in prototypes in app code.

## 7) Quick Practice
1. Build two objects with shared prototype method.
2. Print own vs inherited keys.
3. Trace lookup chain for missing property.

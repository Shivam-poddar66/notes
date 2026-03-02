# Data Types and Type System Basics

## 1) JavaScript Data Types

### Primitive Types
- `string`
- `number`
- `bigint`
- `boolean`
- `undefined`
- `null`
- `symbol`

### Non-Primitive
- `object` (includes arrays, functions, dates, maps, sets)

## 2) `typeof` Basics

```js
typeof "JS";         // "string"
typeof 42;           // "number"
typeof true;         // "boolean"
typeof undefined;    // "undefined"
typeof Symbol("x"); // "symbol"
typeof 10n;          // "bigint"
typeof {};           // "object"
typeof [];           // "object"
typeof null;         // "object" (historical JS quirk)
```

## 3) `undefined` vs `null`
- `undefined`: variable declared but not assigned, or missing property.
- `null`: intentional empty value set by developer.

## 4) Primitive vs Reference Behavior

```js
let a = 10;
let b = a;
b = 20;
// a is still 10

const obj1 = { score: 1 };
const obj2 = obj1;
obj2.score = 2;
// obj1.score is also 2 (same reference)
```

## 5) Important Type Notes
- `NaN` means "not a valid number result".
- `Number.isNaN()` is safer than global `isNaN()`.
- BigInt is used for very large integers beyond safe number range.

## 6) Safe Number Limits

```js
Number.MAX_SAFE_INTEGER;
Number.MIN_SAFE_INTEGER;
```

Use BigInt when exact large integer precision matters.

## 7) Quick Practice
1. Create one value for each primitive type.
2. Show the `typeof` output for each value.
3. Demonstrate primitive copy vs object reference copy.

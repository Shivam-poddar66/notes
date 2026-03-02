# Type Coercion, Comparisons, and Truthy/Falsy

## 1) Type Coercion
JavaScript may convert types automatically during operations.

### Example

```js
"5" + 1; // "51" (number becomes string)
"5" - 1; // 4 (string becomes number)
```

## 2) Equality: `==` vs `===`
- `==`: loose equality, allows coercion.
- `===`: strict equality, compares type and value.

Use `===` in almost all cases.

```js
0 == false;   // true
0 === false;  // false
"5" == 5;    // true
"5" === 5;   // false
```

## 3) Relational Comparison

```js
"10" > 2;   // true (string coerced)
null > 0;    // false
null == 0;   // false
null >= 0;   // true (edge case)
```

Avoid relying on edge-case coercion behavior.

## 4) Truthy and Falsy

### Falsy values
- `false`
- `0`
- `-0`
- `0n`
- `""`
- `null`
- `undefined`
- `NaN`

Everything else is truthy.

## 5) `||` vs `??`
- `||` returns right side for any falsy left value.
- `??` returns right side only for `null` or `undefined`.

```js
const a = 0 || 100;   // 100
const b = 0 ?? 100;   // 0
```

## 6) Best Practices
- Prefer explicit conversion: `Number(value)`, `String(value)`, `Boolean(value)`.
- Prefer `===` and `!==`.
- Use `??` for default values when `0` or `""` are valid.

## 7) Quick Practice
1. Write 5 comparisons and predict outcomes before running.
2. Build a table of truthy/falsy examples.
3. Replace `||` with `??` where appropriate in sample code.

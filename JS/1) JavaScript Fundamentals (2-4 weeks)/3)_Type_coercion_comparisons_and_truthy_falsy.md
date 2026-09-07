# Type Coercion, Comparisons, and Truthy/Falsy

## 1) Explicit vs. Implicit Type Conversion

JavaScript handles type conversion in two ways: **Explicitly** (written intentionally by the developer) and **Implicitly** (performed automatically by the language engine during operations).

### Explicit Conversion (Casting)
Explicit conversion uses built-in constructor functions or utility methods without the `new` keyword:

```js
// 1. To String
String(123);        // "123"
(456).toString();   // "456"

// 2. To Number
Number("42");       // 42
Number("abc");      // NaN
parseInt("42px");   // 42
parseFloat("3.14"); // 3.14

// 3. To Boolean
Boolean(1);         // true
Boolean(0);         // false
```

### Implicit Conversion (Type Coercion)
Type coercion occurs when operators receive operands of different types.

#### Addition Operator (`+`)
- If **either operand is a string**, `+` acts as string concatenation and coerces the other operand to a string.
- Otherwise, `+` performs numeric addition.

```js
"5" + 2;     // "52" (Number 2 coerced to String "2")
true + 1;    // 2    (Boolean true coerced to Number 1)
null + 5;    // 5    (null coerced to Number 0)
```

#### Other Arithmetic Operators (`-`, `*`, `/`, `%`, `**`)
- Arithmetic operators (except string `+`) always convert operands into **Numbers**.

```js
"10" - "2";  // 8  (Strings coerced to Numbers)
"5" * "3";   // 15
"6" / "2";   // 3
"foo" - 1;   // NaN ("foo" cannot be converted to Number)
```

---

## 2) Equality: Loose (`==`) vs. Strict (`===`)

### Strict Equality (`===` / `!==`)
- Checks **both type and value**.
- Performs **no type coercion**.
- Always use `===` by default.

```js
5 === 5;       // true
"5" === 5;     // false (String vs Number)
0 === false;   // false
```

### Loose Equality (`==` / `!=`)
- Checks **value only** after attempting automatic type coercion.
- Follows the complex *Abstract Equality Comparison Algorithm*.

```js
"5" == 5;      // true  ("5" coerced to Number 5)
0 == false;    // true  (false coerced to Number 0)
"" == false;   // true  ("" and false both coerced to 0)
null == undefined; // true (Special rule in JS spec)
```

### Loose Equality (`==`) Pitfall Matrix
Avoid using `==` because of counter-intuitive coercion edge cases:

| Comparison | Result | Reason for Coercion |
| :--- | :--- | :--- |
| `0 == ""` | `true` | Both coerce to number `0` |
| `0 == "0"` | `true` | Both coerce to number `0` |
| `false == "0"` | `true` | Both coerce to number `0` |
| `false == []` | `true` | `[]` coerces to `""` then `0`, `false` coerces to `0` |
| `"" == []` | `true` | `[]` coerces to primitive `""` |
| `0 == []` | `true` | Both coerce to number `0` |

> **The Only Exception**: `val == null` is a convenient shorthand to check if `val` is **either** `null` or `undefined`.
> ```js
> if (data == null) {
>   // True if data is null OR undefined
> }
> ```

---

## 3) Relational Comparisons (`<`, `>`, `<=`, `>=`)

Relational operators convert operands into primitives before comparing:
- If **both operands are strings**, JS performs **lexicographical (alphabetical)** dictionary comparison based on Unicode code points.
- If **at least one operand is not a string**, JS coerces both operands to **Numbers**.

```js
// String dictionary comparison (Character by character)
"2" > "12";    // true (Character '2' comes after '1' in Unicode)

// Numeric comparison (Coercion triggered)
"2" > 12;      // false (String "2" coerced to Number 2)
```

### The `null` & Relational Edge Case
```js
null > 0;   // false (null coerced to 0: 0 > 0 is false)
null == 0;  // false (null loose equality ONLY equals null or undefined)
null >= 0;  // true  (null coerced to 0: 0 >= 0 is true)
```

---

## 4) Truthy and Falsy Values

When a value is evaluated in a boolean context (e.g., inside an `if` statement or `Boolean()` function), JavaScript coerces it to `true` or `false`.

### The 8 Falsy Values
There are **exactly 8 falsy values** in JavaScript. Every other value in the language is **truthy**.

1. `false`
2. `0` (and `-0`)
3. `0n` (BigInt zero)
4. `""` (empty string)
5. `null`
6. `undefined`
7. `NaN`
8. `document.all` (historical browser host object quirk)

### Common Truthy Surprises
Developers often misidentify these values as falsy, but they are **truthy**:

```js
Boolean("0");          // true (non-empty string)
Boolean("false");      // true (non-empty string)
Boolean([]);           // true (empty array is an object)
Boolean({});           // true (empty object is an object)
Boolean(function(){}); // true (function reference)
Boolean(-1);           // true (any non-zero number)
```

### Double NOT (`!!`) Operator
The double logical NOT operator `!!` is a quick idiom for converting any value into a explicit boolean primitive:

```js
const input = "Hello";
const hasInput = !!input; // true
```

---

## 5) Short-Circuit Logic & Fallback Operators

### Logical OR (`||`)
Returns the **first truthy value**, or the **last operand** if all are falsy.

```js
const name = "" || "Guest"; // "Guest" ("" is falsy)
const count = 0 || 10;      // 10 (0 is falsy, which can bug if 0 is a valid score!)
```

### Logical AND (`&&`)
Returns the **first falsy value**, or the **last operand** if all are truthy.

```js
const isLoggedIn = true;
const userRole = "Admin";

const access = isLoggedIn && userRole; // "Admin"
const blocked = false && "Admin";       // false (short-circuits at false)
```

### Nullish Coalescing Operator (`??`)
Returns the right-hand side operand **only** if the left-hand side is `null` or `undefined`.
Unlike `||`, `??` preserves valid falsy values like `0`, `""`, `false`, or `0n`.

```js
const score = 0 ?? 100;       // 0 (0 is not nullish!)
const text = "" ?? "Default"; // "" ("" is not nullish!)
const missing = null ?? 50;   // 50 (null is nullish)
```

### Logical Assignment Operators (ES2021)
- `a ||= b`: Assigns `b` to `a` if `a` is falsy.
- `a &&= b`: Assigns `b` to `a` if `a` is truthy.
- `a ??= b`: Assigns `b` to `a` if `a` is nullish (`null` or `undefined`).

```js
let title;
title ??= "Untitled"; // "Untitled"
```

---

## 6) Best Practices
1. **Always Use `===` and `!==`**: Avoid loose equality `==` to prevent hidden coercion bugs.
2. **Use Explicit Conversions**: Convert types explicitly (`Number(val)`, `String(val)`) instead of relying on implicit coercion tricks like `+val` or `val + ""`.
3. **Prefer `??` over `||` for Default Values**: Use `??` when setting default configurations so valid falsy inputs like `0` or `""` aren't overridden.
4. **Be Careful with Strings in Relational Operators**: Always cast strings to numbers (`Number(str)`) before using `<`, `>`, `<=`, `>=`.

---

## 7) Quick Exercises & Practice

### Exercise 1: Coercion Prediction Challenge
Predict the result of each expression:
1. `"10" + 20`
2. `"10" - 5`
3. `true + true`
4. `[] + {}`
5. `false == "0"`

<details>
<summary>View Solutions</summary>

1. `"1020"` (String concatenation)
2. `5` (Numeric subtraction)
3. `2` (`true` coerces to `1`: `1 + 1 = 2`)
4. `"[object Object]"` (`[]` coerces to `""`, `{}` coerces to `"[object Object]"`)
5. `true` (Both coerce to number `0`)
</details>

---

### Exercise 2: `||` vs `??` Test
Determine the output of both variables:

```js
const config = {
  timeout: 0,
  title: ""
};

const timeout1 = config.timeout || 5000;
const timeout2 = config.timeout ?? 5000;

const title1 = config.title || "Default Title";
const title2 = config.title ?? "Default Title";

console.log(timeout1, timeout2);
console.log(title1, title2);
```

<details>
<summary>View Solutions</summary>

- `timeout1`: `5000` (`0` is falsy for `||`)
- `timeout2`: `0` (`0` is defined / non-nullish for `??`)
- `title1`: `"Default Title"` (`""` is falsy for `||`)
- `title2`: `""` (`""` is defined / non-nullish for `??`)
</details>

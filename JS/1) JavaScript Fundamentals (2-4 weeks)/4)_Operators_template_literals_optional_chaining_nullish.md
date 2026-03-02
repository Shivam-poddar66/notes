# Operators, Template Literals, Optional Chaining, Nullish Coalescing

## 1) Operator Groups

### Arithmetic
`+`, `-`, `*`, `/`, `%`, `**`

### Assignment
`=`, `+=`, `-=`, `*=`, `/=`, `%=`, `**=`

### Comparison
`===`, `!==`, `>`, `<`, `>=`, `<=`

### Logical
`&&`, `||`, `!`

### Ternary
`condition ? valueIfTrue : valueIfFalse`

## 2) Short-Circuiting

```js
isLoggedIn && showDashboard();
const name = inputName || "Guest";
```

## 3) Template Literals
Use backticks and interpolation.

```js
const user = "Shivam";
const msg = `Hello, ${user}. Welcome to JavaScript.`;
```

## 4) Optional Chaining `?.`
Safely access nested properties.

```js
const city = userProfile?.address?.city;
```

If any part is `null`/`undefined`, result is `undefined` instead of error.

## 5) Nullish Coalescing `??`
Set fallback only when left side is `null` or `undefined`.

```js
const pageSize = config.pageSize ?? 20;
```

## 6) Practical Pattern

```js
const displayName = user?.name ?? "Unknown User";
```

## 7) Quick Practice
1. Build expressions using each operator group.
2. Convert string concatenation into template literals.
3. Refactor unsafe nested access using `?.` and `??`.

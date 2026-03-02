# Strings, Numbers, Math, and Date Basics

## 1) Common String Methods
- `length`
- `toUpperCase()`, `toLowerCase()`
- `trim()`
- `includes()`
- `startsWith()`, `endsWith()`
- `slice()`, `substring()`
- `replace()`
- `split()`

```js
const text = "  JavaScript Notes  ";
text.trim().toLowerCase();
```

## 2) Number Parsing

```js
Number("42");      // 42
parseInt("42px", 10); // 42
parseFloat("3.14kg"); // 3.14
```

## 3) Precision Basics

```js
0.1 + 0.2; // 0.30000000000000004
```

Use formatting or integer scaling for currency-sensitive logic.

## 4) Useful `Math` APIs
- `Math.round`, `Math.floor`, `Math.ceil`
- `Math.max`, `Math.min`
- `Math.random`
- `Math.abs`, `Math.pow`

## 5) Date Basics

```js
const now = new Date();
const fromISO = new Date("2026-02-25T10:00:00Z");

now.getFullYear();
now.getMonth();      // 0-based
now.getDate();
now.getHours();
```

### Formatting
- `toISOString()` for standard output.
- `toLocaleDateString()` for user-friendly display.

## 6) Date Pitfalls
- Timezone differences.
- `getMonth()` is 0-based.
- Invalid date strings can produce `Invalid Date`.

## 7) Quick Practice
1. Normalize and validate user input name.
2. Build random OTP generator.
3. Show current date/time in local + ISO format.

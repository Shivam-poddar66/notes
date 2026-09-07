# Strings, Numbers, Math, and Date Basics

## 1) String Manipulation and Utility APIs

Strings in JavaScript are **immutable primitives**. Any method operating on a string returns a **new string** without altering the original.

### Essential String Methods

```js
const text = "  JavaScript Programming  ";

// 1. Trimming & Case Normalization
const clean = text.trim().toLowerCase(); // "javascript programming"
console.log(text.trimStart());          // "JavaScript Programming  "

// 2. Searching & Verification
clean.includes("script"); // true
clean.startsWith("java");  // true
clean.endsWith("ing");    // true

// 3. Substring Extraction
// slice(start, end) - End index is exclusive. Supports negative relative indexes!
console.log(clean.slice(0, 10)); // "javascript"
console.log(clean.slice(-11));   // "programming"

// 4. Replacing Text
const str = "foo bar foo";
console.log(str.replace("foo", "baz"));    // "baz bar foo" (Replaces FIRST match)
console.log(str.replaceAll("foo", "baz")); // "baz bar baz" (Replaces ALL matches)

// 5. Padding & Repetition
console.log("5".padStart(3, "0")); // "005" (Great for formatting IDs)
console.log("Hi!".repeat(3));       // "Hi!Hi!Hi!"

// 6. Splitting into Array
const words = clean.split(" "); // ["javascript", "programming"]
```

---

## 2) Numbers and Number Parsing

JavaScript numbers are 64-bit floating point numbers (IEEE 754).

### Number Parsing Utilities

```js
// 1. Number() vs. Unary Plus (+) - Converts entire string, returns NaN if invalid
Number("42.5"); // 42.5
+"42.5";        // 42.5
Number("42px"); // NaN

// 2. parseInt(string, radix) - Parses until non-numeric character
// ALWAYS supply radix 10 to avoid legacy octal parsing bugs!
parseInt("42.9px", 10); // 42

// 3. parseFloat(string)
parseFloat("3.14159kg"); // 3.14159
```

### Number Formatting & Validation

```js
const price = 19.9934;

// toFixed(digits) returns a STRING rounded to specified decimal places
console.log(price.toFixed(2)); // "19.99" (Type: string!)
console.log(Number(price.toFixed(2))); // 19.99 (Parsed back to number)

// Number Validation Helpers
Number.isInteger(42);         // true
Number.isSafeInteger(1000);   // true
Number.isNaN(NaN);            // true (Safer than global isNaN)
Number.isFinite(100 / 0);     // false
```

---

## 3) The `Math` Object

The `Math` built-in object provides mathematical properties and methods.

### Rounding & Truncation
```js
Math.floor(4.9); // 4 (Rounds down to nearest integer)
Math.ceil(4.1);  // 5 (Rounds up to nearest integer)
Math.round(4.5); // 5 (Rounds to nearest integer)
Math.trunc(4.9); // 4 (Simply drops decimal digits, ignoring sign)
```

### Min, Max, and Power
```js
Math.max(10, 20, 5); // 20
Math.min(10, 20, 5); // 5

// Passing an Array using Spread (...)
const scores = [88, 95, 72];
console.log(Math.max(...scores)); // 95

Math.pow(2, 3); // 8 (Or use exponentiation operator: 2 ** 3)
Math.sqrt(16);  // 4
Math.abs(-15);  // 15
```

### Random Number Generation Formula
`Math.random()` returns a floating-point pseudo-random number in the range `[0, 1)` (inclusive of 0, exclusive of 1).

```js
// Formula for inclusive random integer between min and max: [min, max]
function getRandomInt(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

console.log(getRandomInt(1, 10)); // Random integer between 1 and 10
```

---

## 4) Date & Time Operations

The `Date` object represents a single moment in time (milliseconds since Jan 1, 1970 UTC Unix Epoch).

### Creating Date Instances
```js
const now = new Date();                        // Current date & time
const specificDate = new Date("2026-09-07T12:00:00Z"); // ISO 8601 string
const customDate = new Date(2026, 8, 7);       // Year, Month Index (8 = Sept!), Day
```

### Key Getter Methods

| Method | Output | Notes |
| :--- | :--- | :--- |
| `getFullYear()` | `2026` | 4-digit year |
| `getMonth()` | `0 - 11` | **0-indexed!** (0 = Jan, 8 = Sept, 11 = Dec) |
| `getDate()` | `1 - 31` | Day of the month |
| `getDay()` | `0 - 6` | Day of the week (0 = Sunday, 6 = Saturday) |
| `getHours()` | `0 - 23` | Hour of the day |
| `getTime()` | `178877...` | Milliseconds since Unix Epoch |

### Date Math: Calculating Elapsed Days
```js
const date1 = new Date("2026-09-01");
const date2 = new Date("2026-09-07");

const diffInMs = date2 - date1; // Returns difference in milliseconds
const diffInDays = diffInMs / (1000 * 60 * 60 * 24);

console.log(`${diffInDays} days difference`); // "6 days difference"
```

### Date Formatting & Internationalization (`Intl`)
```js
const today = new Date();

console.log(today.toISOString()); // "2026-09-07T16:12:00.000Z" (Standard API format)

// Intl.DateTimeFormat for localized formatting
const formatter = new Intl.DateTimeFormat("en-US", {
  dateStyle: "full",
  timeStyle: "short"
});
console.log(formatter.format(today)); // e.g., "Monday, September 7, 2026 at 4:12 PM"
```

---

## 5) Best Practices Checklist
1. **Always Pass Radix to `parseInt`**: Use `parseInt(str, 10)` to guarantee base-10 numeric parsing.
2. **Remember `getMonth()` Zero-Indexing**: Add `+ 1` when displaying human-readable months (`date.getMonth() + 1`).
3. **Use `Intl` for Currency & Date Formatting**: Prefer `Intl.NumberFormat` and `Intl.DateTimeFormat` over manual string concatenation.
4. **Use ISO Strings for Storage/Transfer**: Store and transmit dates using `.toISOString()`.

---

## 6) Quick Practice & Exercises

### Exercise 1: Capitalize Title Words
Write a function `titleCase(str)` that capitalizes the first letter of every word in a sentence:

```js
const input = "javascript fundamentals and study guide";
```

<details>
<summary>View Solution</summary>

```js
function titleCase(str) {
  return str
    .split(" ")
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
}

console.log(titleCase("javascript fundamentals and study guide"));
// Output: "Javascript Fundamentals And Study Guide"
```
</details>

---

### Exercise 2: Format Currency using `Intl.NumberFormat`
Write a function `formatCurrency(amount, currencyCode)`:

```js
formatCurrency(123456.78, "USD"); // "$123,456.78"
formatCurrency(123456.78, "EUR"); // "€123,456.78"
```

<details>
<summary>View Solution</summary>

```js
function formatCurrency(amount, currency = "USD", locale = "en-US") {
  return new Intl.NumberFormat(locale, {
    style: "currency",
    currency: currency
  }).format(amount);
}

console.log(formatCurrency(123456.78, "USD")); // "$123,456.78"
```
</details>

# Error Handling: `try`, `catch`, `finally`, `throw`, and Stack Traces

## 1) Anatomy of JavaScript Errors

When a runtime error occurs in JavaScript, the engine stops execution and creates an **`Error` object**.

### Core Properties of an `Error` Object
- **`name`**: The type of error (e.g., `"TypeError"`, `"ReferenceError"`).
- **`message`**: A human-readable description of what went wrong.
- **`stack`**: A string track listing the call stack history at the point where the error was instantiated.
- **`cause`** (ES2022): Optional property specifying the underlying root cause of the error.

```js
const err = new Error("Failed to load user configuration", { cause: "Network Timeout" });
console.log(err.name);    // "Error"
console.log(err.message); // "Failed to load user configuration"
console.log(err.cause);   // "Network Timeout"
```

---

## 2) The `try...catch...finally` Statement

### Execution Flow

```js
try {
  // 1. Code that might throw a runtime exception
  const data = JSON.parse('{"name": "Alice"}');
  console.log(data.name);
} catch (err) {
  // 2. Executed ONLY if an error was thrown inside the try block
  console.error(`Parsing failed: ${err.message}`);
} finally {
  // 3. ALWAYS executes regardless of whether an error occurred or not
  console.log("Cleanup completed (e.g., closing file handles/spinners).");
}
```

### Optional Catch Binding (ES2019)
If you do not need to inspect the error object, you can omit the `(err)` parameter:

```js
try {
  parsePayload();
} catch {
  // Simple fallback when error details aren't needed
  useDefaultPayload();
}
```

### The `finally` Return Override Pitfall
> **WARNING**: Returning a value inside a `finally` block overrides any explicit `return` or `throw` statement executed in the `try` or `catch` blocks!

```js
function test() {
  try {
    throw new Error("Something broke!");
  } catch (err) {
    return "Caught error";
  } finally {
    return "Overridden by finally!"; // DANGER: Swallows the error/return!
  }
}

console.log(test()); // "Overridden by finally!"
```

---

## 3) Throwing Errors (`throw`)

The `throw` statement allows you to raise custom exceptions when application invariants or inputs are violated.

### Best Practice: Always Throw `Error` Objects
Always throw an instance of `Error` (or a subclass) rather than primitive values so that a stack trace is preserved.

```js
// BAD (Anti-pattern: No stack trace attached!)
// throw "Invalid age provided";

// GOOD (Always throw Error instances)
function setAge(age) {
  if (typeof age !== "number" || age < 0) {
    throw new TypeError("Age must be a positive number.");
  }
  return age;
}
```

### Creating Custom Error Subclasses (ES6)
Extend the built-in `Error` class to create domain-specific error types:

```js
class ValidationError extends Error {
  constructor(message, field) {
    super(message);
    this.name = "ValidationError";
    this.field = field;
  }
}

function registerUser(userData) {
  if (!userData.email) {
    throw new ValidationError("Email is required", "email");
  }
}

try {
  registerUser({});
} catch (err) {
  if (err instanceof ValidationError) {
    console.error(`Validation failed on field [${err.field}]: ${err.message}`);
  } else {
    console.error("Unknown system error:", err);
  }
}
```

---

## 4) Built-in JavaScript Error Types

| Error Type | Trigger Condition | Example |
| :--- | :--- | :--- |
| **`ReferenceError`** | Accessing a variable that does not exist in scope | `console.log(nonExistentVar);` |
| **`TypeError`** | Operation performed on an inappropriate data type | `null.toString();` or `const x = 5; x();` |
| **`SyntaxError`** | Invalid JS syntax or malformed JSON | `JSON.parse("invalid json")` |
| **`RangeError`** | Numeric value is outside its allowed range | `new Array(-1)` or `(10).toFixed(101)` |
| **`URIError`** | Invalid parameters passed to `encodeURI()` or `decodeURI()` | `decodeURIComponent("%")` |

---

## 5) Reading Stack Traces & Debugging Framework

A **Stack Trace** provides a snapshot of the call stack at the moment the error occurred.

### Example Stack Trace
```text
Uncaught TypeError: Cannot read properties of undefined (reading 'street')
    at displayAddress (app.js:15:24)
    at renderUserProfile (app.js:8:5)
    at main (app.js:2:3)
```

### How to Triage Stack Traces
1. **Line 1 (The What)**: Identifies the error type (`TypeError`) and message (`Cannot read properties of undefined (reading 'street')`).
2. **Top Frame (The Where)**: Look at the top-most line referencing *your project code* (`app.js:15:24`). This points directly to file `app.js`, line 15, column 24.
3. **Subsequent Frames (The How)**: Traces back how execution reached that point (`main` called `renderUserProfile`, which called `displayAddress`).

---

## 6) Asynchronous Error Handling Note

Synchronous `try...catch` **cannot** catch errors thrown inside asynchronous callbacks (like `setTimeout` or raw event handlers) because the callback executes after the `try` block has already exited.

```js
// BUG: try/catch CANNOT catch this callback error!
try {
  setTimeout(() => {
    // throw new Error("Async Error!"); // Uncaught Exception!
  }, 100);
} catch (err) {
  console.log("Will never execute!");
}

// FIX: Handle errors inside the async callback or use async/await with Promises
```

---

## 7) Best Practices Checklist
1. **Do Not Swallow Errors Silently**: Avoid empty `catch (e) {}` blocks. Always log, rethrow, or handle exceptions gracefully.
2. **Throw Custom Error Classes**: Use subclasses (`ValidationError`, `DatabaseError`) to distinguish user input errors from system failures.
3. **Clean Up Resources in `finally`**: Reset loading spinners, close database connections, or release locks inside `finally`.
4. **Never Return Values inside `finally`**: Avoid `return` statements in `finally` blocks to prevent overriding `try`/`catch` results.

---

## 8) Quick Practice & Exercises

### Exercise 1: Safe JSON Parser Pattern
Write a wrapper function `safeJSONParse(jsonString)` that returns `{ data, error }` without throwing runtime errors:

```js
const valid = safeJSONParse('{"id": 1}');   // { data: { id: 1 }, error: null }
const invalid = safeJSONParse('invalid');   // { data: null, error: SyntaxError }
```

<details>
<summary>View Solution</summary>

```js
function safeJSONParse(jsonString) {
  try {
    const data = JSON.parse(jsonString);
    return { data, error: null };
  } catch (error) {
    return { data: null, error };
  }
}

console.log(safeJSONParse('{"id": 1}'));
console.log(safeJSONParse('invalid'));
```
</details>

---

### Exercise 2: Custom Validation Class
Create a `RangeError` subclass `InvalidAgeError` and throw it if age is less than 0 or greater than 120:

<details>
<summary>View Solution</summary>

```js
class InvalidAgeError extends RangeError {
  constructor(age) {
    super(`Provided age [${age}] must be between 0 and 120.`);
    this.name = "InvalidAgeError";
    this.age = age;
  }
}

function validateAge(age) {
  if (age < 0 || age > 120) {
    throw new InvalidAgeError(age);
  }
  return true;
}

try {
  validateAge(150);
} catch (err) {
  if (err instanceof InvalidAgeError) {
    console.error(err.message); // "Provided age [150] must be between 0 and 120."
  }
}
```
</details>

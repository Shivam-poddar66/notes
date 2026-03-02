# Error Handling: try/catch/finally, throw, and Stack Traces

## 1) Why Error Handling Matters
Error handling prevents crashes and gives useful feedback to users and developers.

## 2) `try`, `catch`, `finally`

```js
try {
  const data = JSON.parse(input);
  console.log(data);
} catch (err) {
  console.error("Invalid JSON:", err.message);
} finally {
  console.log("Parsing attempt finished");
}
```

- `try`: risky code.
- `catch`: handle exceptions.
- `finally`: runs whether error happens or not.

## 3) `throw`
Create custom errors when input/state is invalid.

```js
function divide(a, b) {
  if (b === 0) throw new Error("Division by zero is not allowed");
  return a / b;
}
```

## 4) Common Runtime Errors
- `ReferenceError`: variable not defined.
- `TypeError`: invalid operation on a type (for example calling non-function).
- `SyntaxError`: invalid syntax or malformed JSON.
- `RangeError`: number out of valid range.

## 5) Reading Stack Traces
A stack trace shows:
- Error type and message.
- File and line where error occurred.
- Call path leading to error.

### Debug Steps
1. Read first line for error type.
2. Jump to top relevant stack frame in your code.
3. Check variable values and assumptions.
4. Re-run with minimal reproducible input.

## 6) Best Practices
- Throw meaningful error messages.
- Do not silently swallow errors.
- Validate external input before processing.
- Log context safely (avoid sensitive data leaks).

## 7) Quick Practice
1. Write parser with `try/catch` for JSON input.
2. Throw custom error for invalid age input.
3. Intentionally create each common runtime error and inspect stack trace.

# Mini Project 1: Calculator (Browser)

## Goal
Build a basic calculator to practice operators, functions, conditionals, events, and error handling.

## Features
- Two input fields (`number`).
- Operation select (`+`, `-`, `*`, `/`, `%`, `**`).
- Calculate button.
- Result display.
- Error for invalid values and division by zero.

## Suggested Folder Structure
```text
calculator/
  index.html
  style.css
  script.js
```

## Implementation Steps
1. Build HTML form controls and result container.
2. Add button click listener.
3. Parse values with `Number()`.
4. Use `switch` to perform operation.
5. Guard for invalid numbers and division by zero.
6. Show result in UI.

## Core Function Template
```js
function calculate(a, b, op) {
  switch (op) {
    case "+": return a + b;
    case "-": return a - b;
    case "*": return a * b;
    case "/":
      if (b === 0) throw new Error("Division by zero");
      return a / b;
    case "%": return a % b;
    case "**": return a ** b;
    default: throw new Error("Unknown operation");
  }
}
```

## Practice Extensions
- Add keyboard support.
- Add calculation history array.
- Add clear/reset button.

## Done Criteria
- All operations work.
- Invalid input and zero division handled.
- Clean function-based code structure.

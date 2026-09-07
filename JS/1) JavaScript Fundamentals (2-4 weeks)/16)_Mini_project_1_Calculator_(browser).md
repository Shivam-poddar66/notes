# Mini Project 1: Browser Calculator

## 1) Project Overview & Learning Objectives

In this project, you will build an interactive, browser-based **Calculator** that applies foundational JavaScript concepts:
- **Operators**: Arithmetic (`+`, `-`, `*`, `/`, `%`, `**`).
- **Control Flow & Decision Patterns**: `switch` statements and Guard Clauses.
- **Functions**: Pure calculation functions separated from DOM event handlers.
- **Input Validation & Parsing**: Converting string inputs via `Number()` and checking `Number.isNaN()`.
- **Error Handling**: Throwing custom errors and catching exceptions with `try...catch`.
- **Arrays & History**: Storing previous calculations in an array state.

---

## 2) Project Directory Structure

Create the following folder and files in your workspace:

```text
calculator/
├── index.html
├── style.css
└── script.js
```

---

## 3) Complete Implementation Code

### File 1: `index.html`
```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JS Fundamentals Calculator</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="calculator-card">
    <h2>JavaScript Calculator</h2>
    
    <div class="input-group">
      <input type="number" id="num1" placeholder="First Number" step="any">
      
      <select id="operator">
        <option value="+">+</option>
        <option value="-">-</option>
        <option value="*">×</option>
        <option value="/">÷</option>
        <option value="%">% (Remainder)</option>
        <option value="**">^ (Exponent)</option>
      </select>
      
      <input type="number" id="num2" placeholder="Second Number" step="any">
    </div>

    <div class="button-group">
      <button id="calcBtn" class="btn-primary">Calculate</button>
      <button id="clearBtn" class="btn-secondary">Clear</button>
    </div>

    <div id="resultDisplay" class="result-box">
      Result: <span id="resultValue">--</span>
    </div>

    <div class="history-section">
      <h3>Calculation History</h3>
      <ul id="historyList"></ul>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
```

---

### File 2: `style.css`
```css
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f4f7f6;
  padding: 20px;
}

.calculator-card {
  background: #ffffff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 420px;
}

h2 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 20px;
}

input, select {
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
  outline: none;
  transition: border-color 0.2s;
}

input:focus, select:focus {
  border-color: #4a90e2;
}

.button-group {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

button {
  flex: 1;
  padding: 12px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: opacity 0.2s;
}

.btn-primary {
  background-color: #4a90e2;
  color: white;
}

.btn-secondary {
  background-color: #e0e0e0;
  color: #333;
}

button:hover {
  opacity: 0.9;
}

.result-box {
  background-color: #eef5fc;
  border-left: 4px solid #4a90e2;
  padding: 15px;
  border-radius: 4px;
  font-size: 18px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 20px;
}

.result-box.error {
  background-color: #fde8e8;
  border-left-color: #e74c3c;
  color: #c0392b;
}

.history-section {
  border-top: 1px solid #eee;
  padding-top: 15px;
}

.history-section h3 {
  font-size: 14px;
  color: #7f8c8d;
  margin-bottom: 10px;
}

#historyList {
  list-style: none;
  max-height: 120px;
  overflow-y: auto;
  font-size: 14px;
  color: #555;
}

#historyList li {
  padding: 6px 0;
  border-bottom: 1px dashed #eee;
}
```

---

### File 3: `script.js`
```js
// --- 1. STATE MANAGEMENT ---
const calculationHistory = [];

// --- 2. DOM ELEMENT SELECTIONS ---
const num1Input = document.getElementById("num1");
const num2Input = document.getElementById("num2");
const operatorSelect = document.getElementById("operator");
const calcBtn = document.getElementById("calcBtn");
const clearBtn = document.getElementById("clearBtn");
const resultDisplay = document.getElementById("resultDisplay");
const resultValue = document.getElementById("resultValue");
const historyList = document.getElementById("historyList");

// --- 3. PURE CALCULATION ENGINE ---
/**
 * Performs arithmetic operations safely.
 * Throws errors for division by zero or invalid inputs.
 */
function calculate(a, b, op) {
  if (Number.isNaN(a) || Number.isNaN(b)) {
    throw new TypeError("Please enter valid numeric values.");
  }

  switch (op) {
    case "+":
      return a + b;
    case "-":
      return a - b;
    case "*":
      return a * b;
    case "/":
      if (b === 0) {
        throw new RangeError("Division by zero is not allowed.");
      }
      return a / b;
    case "%":
      if (b === 0) {
        throw new RangeError("Modulo by zero is not allowed.");
      }
      return a % b;
    case "**":
      return a ** b;
    default:
      throw new Error(`Unsupported operator: "${op}"`);
  }
}

// --- 4. EVENT HANDLERS ---
function handleCalculate() {
  // Read and parse input values
  const val1 = Number(num1Input.value);
  const val2 = Number(num2Input.value);
  const op = operatorSelect.value;

  try {
    // Validate empty input strings explicitly
    if (num1Input.value.trim() === "" || num2Input.value.trim() === "") {
      throw new Error("Both number fields are required.");
    }

    // Perform calculation
    const rawResult = calculate(val1, val2, op);

    // Format result (round to 4 decimal places if floating point)
    const formattedResult = Number.isInteger(rawResult)
      ? rawResult
      : Number(rawResult.toFixed(4));

    // Update UI for success
    renderResult(`${val1} ${op} ${val2} = ${formattedResult}`, false);

    // Record calculation in history state
    addHistory(`${val1} ${op} ${val2} = ${formattedResult}`);

  } catch (err) {
    // Handle thrown exceptions gracefully in UI
    renderResult(err.message, true);
  }
}

function handleClear() {
  num1Input.value = "";
  num2Input.value = "";
  operatorSelect.value = "+";
  renderResult("--", false);
}

// --- 5. DOM UI RENDER HELPERS ---
function renderResult(message, isError) {
  resultValue.textContent = message;
  if (isError) {
    resultDisplay.classList.add("error");
  } else {
    resultDisplay.classList.remove("error");
  }
}

function addHistory(entry) {
  // Add to top of history array
  calculationHistory.unshift(entry);

  // Render to DOM
  const li = document.createElement("li");
  li.textContent = entry;
  historyList.prepend(li);
}

// --- 6. EVENT LISTENERS ATTACHMENT ---
calcBtn.addEventListener("click", handleCalculate);
clearBtn.addEventListener("click", handleClear);

// Enable "Enter" key trigger from inputs
num1Input.addEventListener("keyup", (e) => e.key === "Enter" && handleCalculate());
num2Input.addEventListener("keyup", (e) => e.key === "Enter" && handleCalculate());
```

---

## 4) Architecture & Code Walkthrough

1. **Separation of Concerns**:
   - `calculate(a, b, op)` is a **pure function** that performs numeric logic and throws exceptions. It has zero DOM dependencies, making it easy to unit test.
   - `handleCalculate()` handles DOM event binding, string parsing with `Number()`, exception catching via `try...catch`, and UI updating.
2. **Robust Input Validation**:
   - Handles empty input fields using `.trim() === ""`.
   - Validates `NaN` via `Number.isNaN()`.
   - Prevents division/modulo by zero with a `RangeError`.
3. **Array State Tracking**:
   - Uses `calculationHistory.unshift()` and `historyList.prepend()` to maintain chronological order of calculations.

---

## 5) Done Criteria & Self-Verification Checklist
- [x] All 6 operations (`+`, `-`, `*`, `/`, `%`, `**`) return mathematically correct results.
- [x] Division by zero throws a `RangeError` and renders a red error message in the UI without crashing the page.
- [x] Empty inputs throw a validation error.
- [x] Previous calculations append to the calculation history list.
- [x] Clear button resets inputs and display state.

---

## 6) Practice Extensions
Try implementing these enhancements on your own:
1. **Memory Functions**: Add `M+` (Add to memory), `MR` (Memory Recall), and `MC` (Memory Clear) buttons using a global `memory` variable.
2. **History Clear**: Add a "Clear History" button to empty `calculationHistory` array and clear the `<ul>` element.
3. **Decimal Precision Selector**: Add a dropdown allowing the user to select desired decimal rounding places (0, 2, 4, 6).

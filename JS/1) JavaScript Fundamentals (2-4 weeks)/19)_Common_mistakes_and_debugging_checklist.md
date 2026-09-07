# Common Mistakes and Debugging Checklist

## 1) Top 10 JavaScript Fundamentals Pitfalls

### Pitfall 1: Using Loose Equality (`==`) Instead of Strict Equality (`===`)
- **Problem**: Loose equality triggers automatic type coercion leading to counter-intuitive evaluations (e.g., `0 == ""`, `false == []`).
- **Fix**: Always use `===` and `!==`. The only acceptable exception is `val == null` to check for both `null` and `undefined`.

---

### Pitfall 2: `var` Scope Leakage & Hoisting Bugs
- **Problem**: `var` is function-scoped (not block-scoped) and gets hoisted as `undefined`, causing scope leaks out of `if` blocks and `for` loops.
- **Fix**: Use `const` by default and `let` only when reassignment is required. Completely avoid `var` in modern JavaScript.

---

### Pitfall 3: Confusing Mutation with Reassignment under `const`
- **Problem**: Assuming `const obj = {}` makes the object immutable. `const` prevents reassigning the variable reference, but object properties and array elements can still be mutated.
- **Fix**: Use non-mutating array methods (`map`, `filter`, `slice`, `toSorted`, spread `[...]`) or `Object.freeze()` / `structuredClone()` when immutability is required.

---

### Pitfall 4: Using `for...in` Instead of `for...of` on Arrays
- **Problem**: `for...in` iterates over object **keys / indexes as strings** (and picks up prototype properties), while `for...of` iterates over array **values**.
- **Fix**: Use `for...of` for Arrays/Iterables and `for...in` (or `Object.entries()`) for Objects.

---

### Pitfall 5: Default `.sort()` Lexicographical Sorting
- **Problem**: `[1, 10, 2].sort()` yields `[1, 10, 2]` because `.sort()` converts elements to strings before sorting.
- **Fix**: Always pass a comparison callback for numeric sorting: `.sort((a, b) => a - b)`.

---

### Pitfall 6: Using `||` Instead of `??` for Default Fallbacks
- **Problem**: `0 || 10` returns `10` because `0` is falsy, overriding valid zero or empty string values.
- **Fix**: Use Nullish Coalescing (`??`), which only falls back when the left operand is `null` or `undefined`.

---

### Pitfall 7: Comparing `NaN === NaN` directly
- **Problem**: `NaN` is the only value in JavaScript that is not equal to itself (`NaN === NaN` evaluates to `false`).
- **Fix**: Use `Number.isNaN(val)` to reliably test for `NaN`.

---

### Pitfall 8: Losing `this` Context in Arrow Function Object Methods
- **Problem**: Arrow functions use lexical `this` (inheriting `this` from enclosing scope), causing `this.prop` inside object methods to return `undefined`.
- **Fix**: Use standard method shorthand `greet() {}` inside object literals.

---

### Pitfall 9: Silently Swallowing Exceptions (`catch (err) {}`)
- **Problem**: Catching errors without logging or handling them masks critical application bugs and makes debugging impossible.
- **Fix**: Always log `console.error(err)`, throw custom errors, or provide UI feedback inside `catch` blocks.

---

### Pitfall 10: Unintentional Fall-Through in `switch` Statements
- **Problem**: Omitting `break;` causes execution to fall through into subsequent `case` blocks.
- **Fix**: Include `break;` or `return` at the end of every `case` block unless fall-through is explicitly intended.

---

## 2) The 10-Step Debugging Checklist

Follow this systematic checklist whenever your JavaScript code produces unexpected behavior or throws an error:

```text
[ ] Step 1: Read the exact Error Name, Message, and Line Number in the stack trace.
[ ] Step 2: Jump directly to the first stack frame in YOUR source file.
[ ] Step 3: Inspect variable types using typeof or Array.isArray().
[ ] Step 4: Check for null or undefined before property access (use optional chaining ?.).
[ ] Step 5: Verify input string trimming (.trim()) and number parsing (Number.isNaN()).
[ ] Step 6: Inspect loop bounds (off-by-one errors: < vs <=).
[ ] Step 7: Check if mutating methods (push, splice, sort) altered state unexpectedly.
[ ] Step 8: Verify default value fallbacks (prefer ?? over ||).
[ ] Step 9: Isolate failing logic into a minimal code snippet or DevTools Console.
[ ] Step 10: Test boundary edge cases (0, "", null, undefined, empty array []).
```

---

## 3) Essential DevTools & Console Debugging Techniques

### 1. `console` Method Toolkit
```js
// 1. console.table() - Formats arrays of objects into a clean tabular UI
const users = [{ id: 1, name: "Alice" }, { id: 2, name: "Bob" }];
console.table(users);

// 2. console.dir() - Displays interactive JSON representation of DOM nodes/objects
console.dir(document.body);

// 3. console.group() & console.groupEnd() - Groups related log messages
console.group("Processing Payment");
console.log("Validating card...");
console.log("Charging amount...");
console.groupEnd();
```

### 2. Setting Programmatic Breakpoints (`debugger;`)
Insert the `debugger;` statement directly in your code. When browser DevTools is open, execution will pause automatically at that line, allowing you to inspect variables and step through code.

```js
function calculateTax(amount) {
  debugger; // DevTools pauses here!
  const rate = 0.18;
  return amount * rate;
}
```

---

## 4) Preventive Code Quality Habits

1. **Enable Strict Mode**: Always run JavaScript in ES modules or add `'use strict';` at the top of scripts to catch implicit global variables.
2. **Use Guard Clauses**: Return early from functions to handle invalid inputs before running main logic.
3. **Keep Functions Pure**: Reduce side effects by passing dependencies as arguments and returning new values.
4. **Use ESLint and Prettier**: Automate syntax error detection and formatting.

---

## 5) Quick Debugging Practice Challenge

Spot and fix all 3 bugs in the following function:

```js
// GOAL: Return total price for active items
function calculateActiveTotal(items) {
  var total = 0;
  for (var i in items) {
    if (items[i].active = true) {
      total += items[i].price || 5;
    }
  }
  return total;
}
```

<details>
<summary>View Solution & Bug Breakdown</summary>

### The 3 Bugs Identified:
1. **Bug 1 (`items[i].active = true`)**: Assignment operator `=` used instead of strict comparison `===`. This mutates `active` to `true` for all items!
2. **Bug 2 (`for (var i in items)`)**: `for...in` on arrays yields string indexes (`"0"`, `"1"`) and pollutes prototype properties.
3. **Bug 3 (`price || 5`)**: If an item price is legitimately `$0`, `||` treats `0` as falsy and overrides it with `$5`.

### Refactored & Fixed Solution:
```js
function calculateActiveTotal(items) {
  return items
    .filter(item => item.active === true)
    .reduce((sum, item) => sum + (item.price ?? 5), 0);
}

// Test Case
const cart = [
  { name: "Item 1", price: 0, active: true },
  { name: "Item 2", price: 20, active: false },
  { name: "Item 3", price: 10, active: true }
];

console.log(calculateActiveTotal(cart)); // Output: 10 (Item 1 price $0 + Item 3 price $10)
```
</details>

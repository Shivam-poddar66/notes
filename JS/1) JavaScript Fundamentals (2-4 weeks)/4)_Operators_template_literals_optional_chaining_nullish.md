# Operators, Template Literals, Optional Chaining, and Nullish Coalescing

## 1) Comprehensive Overview of JavaScript Operators

Operators allow you to manipulate values, combine expressions, and perform logical evaluations.

### Operator Categories Table

| Category | Operators | Examples / Behavior |
| :--- | :--- | :--- |
| **Arithmetic** | `+`, `-`, `*`, `/`, `%`, `**` | `10 % 3` $\rightarrow$ `1` (Remainder), `2 ** 3` $\rightarrow$ `8` (Exponentiation) |
| **Increment / Decrement** | `++`, `--` | **Prefix** (`++x` increments then returns value), **Postfix** (`x++` returns value then increments) |
| **Assignment** | `=`, `+=`, `-=`, `*=`, `/=`, `%=`, `**=` | `x += 5` is shorthand for `x = x + 5` |
| **Logical Assignment** | `||=`, `&&=`, `??=` | `x ??= 10` (Assigns `10` only if `x` is `null` or `undefined`) |
| **Comparison** | `===`, `!==`, `>`, `<`, `>=`, `<=` | `5 !== "5"` $\rightarrow$ `true` |
| **Relational / Testing** | `in`, `instanceof` | `"name" in user`, `arr instanceof Array` |
| **Logical** | `&&` (AND), `||` (OR), `!` (NOT) | `isLoggedIn && hasPermission` |
| **Ternary** | `condition ? val1 : val2` | `const status = age >= 18 ? "Adult" : "Minor";` |
| **Unary** | `typeof`, `delete`, `void`, `+`, `-` | `+str` (Unary plus coerces string to number) |

### Prefix vs. Postfix Increment
```js
let a = 5;
let b = ++a; // Prefix: 'a' becomes 6, then 'b' is assigned 6

let x = 5;
let y = x++; // Postfix: 'y' is assigned 5, then 'x' becomes 6

console.log(a, b); // 6, 6
console.log(x, y); // 6, 5
```

### Operator Precedence Summary (Highest to Lowest)
1. Grouping `( )`
2. Member Access `.`, Optional Chaining `?.`, Array Index `[]`
3. Exponentiation `**`
4. Logical NOT `!`, Unary `+`, `typeof`, Increment `++`
5. Multiplication `*`, Division `/`, Modulo `%`
6. Addition `+`, Subtraction `-`
7. Relational `<`, `>`, `<=`, `>=`
8. Equality `===`, `!==`
9. Logical AND `&&`
10. Logical OR `||`, Nullish Coalescing `??`
11. Assignment `=`, `+=`, `??=`

---

## 2) Template Literals (ES6)

Template literals use **backticks** (``` ` ```) instead of single or double quotes, unlocking string interpolation and multiline strings.

### Basic Interpolation
Embed any valid JavaScript expression inside `${expression}`:

```js
const name = "Shivam";
const age = 24;

// Expression interpolation
const greeting = `Hello, my name is ${name} and next year I will be ${age + 1}.`;
```

### Multiline Strings
No need for `\n` or string concatenation `+`:

```js
const htmlSnippet = `
  <div class="card">
    <h2>${name}</h2>
    <p>Status: ${age >= 18 ? "Active" : "Pending"}</p>
  </div>
`;
```

### Tagged Template Literals (Advanced)
A tag function allows you to parse and process template literals before returning the final string. Tag functions receive array of string literals as the first argument, followed by evaluated expressions.

```js
function sanitize(strings, ...values) {
  return strings.reduce((result, str, i) => {
    const val = values[i - 1] ? String(values[i - 1]).replace(/</g, "&lt;") : "";
    return result + val + str;
  });
}

const userInput = "<script>alert('hack')</script>";
const safeHTML = sanitize`User sent: ${userInput}`;
// Result: "User sent: &lt;script>alert('hack')&lt;/script>"
```

---

## 3) Optional Chaining Operator (`?.`)

The optional chaining operator (`?.`) reads the value of a property located deep within a chain of connected objects without throwing an error if a reference in the chain is `null` or `undefined`.

### Syntax Variations

```js
const user = {
  profile: {
    name: "Alice",
    getDetails() { return "Active User"; }
  },
  orders: ["Order1", "Order2"]
};

// 1. Property Access: obj?.prop
const city = user?.address?.city; // undefined (No error thrown!)

// 2. Bracket Access: obj?.[key] or arr?.[index]
const firstOrder = user?.orders?.[0]; // "Order1"
const thirdOrder = user?.orders?.[2]; // undefined

// 3. Method Call: obj.method?.()
const details = user?.profile?.getDetails?.(); // "Active User"
const missingMethod = user?.profile?.logData?.(); // undefined
```

### Critical Rules for `?.`
- **Short-circuiting**: If the expression before `?.` evaluates to `null` or `undefined`, the entire rest of the expression chain is skipped and evaluates to `undefined`.
- **Cannot be used on Left-Hand Assignment**:
  ```js
  // user?.address?.city = "New York"; // SyntaxError: Invalid left-hand side in assignment
  ```

---

## 4) Nullish Coalescing Operator (`??`)

The nullish coalescing operator (`??`) is a logical operator that returns its right-hand side operand when its left-hand side operand is **`null` or `undefined`**; otherwise, it returns its left-hand side operand.

### `||` vs `??` Comparison

```js
const config = {
  volume: 0,
  title: "",
  showSidebar: false
};

// --- Using Logical OR (||) --- Falsy check (Overrides 0, "", false)
const vol1 = config.volume || 50;        // 50 (Bug: 0 is overwritten!)
const title1 = config.title || "Untitled"; // "Untitled" (Bug: "" is overwritten!)

// --- Using Nullish Coalescing (??) --- Nullish check (Preserves 0, "", false)
const vol2 = config.volume ?? 50;        // 0 (Correct!)
const title2 = config.title ?? "Untitled"; // "" (Correct!)
const sidebar2 = config.showSidebar ?? true; // false (Correct!)
```

### Precedence Syntax Rule
For clarity, JavaScript prohibits combining `??` directly with `&&` or `||` without explicit parenthetical grouping:

```js
// Invalid:
// const res = a && b ?? c; // SyntaxError

// Valid:
const res = (a && b) ?? c;
```

---

## 5) Defensive Coding Patterns

Combining `?.` and `??` provides a powerful, concise pattern for safely dealing with optional data structures and default fallbacks.

### Pattern: Safe Deep Property Extraction with Fallback
```js
const apiResponse = {
  data: {
    user: {
      preferences: {
        theme: "dark"
      }
    }
  }
};

// Safe access + default fallback
const theme = apiResponse?.data?.user?.preferences?.theme ?? "light";
console.log(theme); // "dark"

const fontSize = apiResponse?.data?.user?.preferences?.fontSize ?? 14;
console.log(fontSize); // 14 (Fallback used safely)
```

### Pattern: Safe Optional Callback Execution
```js
function fetchData(onSuccess) {
  const data = { id: 101, status: "OK" };
  
  // Safely execute callback only if passed as a function
  onSuccess?.(data);
}

fetchData(); // Runs safely without error even if onSuccess is omitted!
```

---

## 6) Best Practices Checklist
1. **Combine `?.` and `??`**: Always use `user?.profile?.name ?? "Guest"` when reading nested API payload properties.
2. **Prefer `??` over `||` for Fallbacks**: Avoid `||` when default values could legitimately be numeric `0`, empty strings `""`, or booleans `false`.
3. **Use Template Literals over Concatenation**: Prefer `` `Hello ${name}` `` over `"Hello " + name`.
4. **Guard Optional Functions**: Use `cb?.()` before executing optional callbacks passed as function arguments.

---

## 7) Quick Practice & Exercises

### Exercise 1: Refactor Legacy Code
Refactor the following unsafe legacy code into modern ES6+ syntax using template literals, `?.`, and `??`:

```js
// Legacy Code
var user = getResponse();
var street = (user && user.address && user.address.street) ? user.address.street : "No Street";
var message = "User lives on " + street + " in city " + (user.address ? user.address.city : "Unknown");
```

<details>
<summary>View Solution</summary>

```js
const user = getResponse();
const street = user?.address?.street ?? "No Street";
const city = user?.address?.city ?? "Unknown";
const message = `User lives on ${street} in city ${city}`;
```
</details>

---

### Exercise 2: Predict the Output
What will be logged to the console?

```js
let count = 0;
const a = count++ + ++count;
console.log(count, a);

const settings = { speed: 0 };
const currentSpeed = settings?.speed ?? 100;
console.log(currentSpeed);
```

<details>
<summary>View Solution</summary>

- `count, a`: `2, 2` (First `count++` evaluates to `0`, then `count` becomes `1`. Next `++count` increments `count` to `2` and evaluates to `2`. Result: `0 + 2 = 2`).
- `currentSpeed`: `0` (`settings.speed` is `0`, which is not nullish (`null`/`undefined`), so `0` is returned).
</details>

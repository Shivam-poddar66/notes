# Control Flow: `if`, `switch`, and Decision Patterns

## 1) `if`, `else if`, and `else` Statements

The `if` statement executes a block of code if a specified condition evaluates to **truthy**.

```js
const score = 85;

if (score >= 90) {
  console.log("Grade: A");
} else if (score >= 75) {
  console.log("Grade: B");
} else if (score >= 60) {
  console.log("Grade: C");
} else {
  console.log("Grade: F");
}
```

### Best Practice: Always Use Curly Braces `{}`
JavaScript allows single-line statements without curly braces, but this is error-prone and leads to accidental logic bugs (e.g., Apple's famous *goto fail* vulnerability).

```js
// BAD: Prone to maintenance errors
if (isLoggedIn) redirectUser();

// GOOD: Always wrap blocks in curly braces
if (isLoggedIn) {
  redirectUser();
}
```

---

## 2) The `switch` Statement

A `switch` statement evaluates an expression and matches its result against a series of `case` clauses.

### Key Rules
- **Strict Equality (`===`)**: `switch` compares the target expression with `case` values using strict equality without type coercion.
- **Fall-Through Behavior**: If you omit `break`, execution "falls through" into the next `case` block regardless of whether that condition matches.
- **`default` Clause**: Executes if no `case` matches (best practice: always include `default`).

```js
const userRole = "editor";
let permissions;

switch (userRole) {
  case "admin":
    permissions = ["read", "write", "delete"];
    break;
  case "editor":
    permissions = ["read", "write"];
    break;
  case "viewer":
    permissions = ["read"];
    break;
  default:
    permissions = [];
}
```

### Intentional Case Grouping (Multiple Cases, Single Action)
```js
const day = "Saturday";

switch (day) {
  case "Saturday":
  case "Sunday":
    console.log("It's the weekend!");
    break;
  case "Monday":
  case "Tuesday":
  case "Wednesday":
  case "Thursday":
  case "Friday":
    console.log("It's a weekday.");
    break;
  default:
    console.log("Invalid day.");
}
```

### Block Scoping Inside `switch` Cases
Declaring `let` or `const` directly inside a `case` can throw a `SyntaxError` or `ReferenceError` because all cases share a single block scope. Use curly braces `{}` to isolate variable declarations per `case`:

```js
switch (action) {
  case "create": {
    const item = { id: 1 }; // Scoped inside this case block
    console.log(item);
    break;
  }
  case "update": {
    const item = { id: 2 }; // No redeclaration conflict!
    console.log(item);
    break;
  }
}
```

### Advanced: `switch (true)` Pattern for Range Matching
```js
const temp = 28;

switch (true) {
  case temp > 30:
    console.log("Hot");
    break;
  case temp >= 20 && temp <= 30:
    console.log("Warm");
    break;
  default:
    console.log("Cold");
}
```

---

## 3) Guard Clauses & Early Returns (Clean Code Pattern)

Deeply nested `if` statements create "Arrow Code" / "Pyramid of Doom", making code difficult to read and maintain.

### Anti-Pattern: Deeply Nested Code
```js
function processPayment(user, amount) {
  if (user) {
    if (user.isActive) {
      if (amount > 0) {
        if (user.balance >= amount) {
          user.balance -= amount;
          return "Payment Successful";
        } else {
          return "Insufficient Balance";
        }
      } else {
        return "Invalid Amount";
      }
    } else {
      return "User Inactive";
    }
  } else {
    return "User Not Found";
  }
}
```

### Refactored: Guard Clauses (Flat, Readable Code)
Handle invalid cases or edge conditions early with returns, leaving the happy path unindented at the bottom.

```js
function processPayment(user, amount) {
  // 1. Guard clauses
  if (!user) return "User Not Found";
  if (!user.isActive) return "User Inactive";
  if (amount <= 0) return "Invalid Amount";
  if (user.balance < amount) return "Insufficient Balance";

  // 2. Happy path
  user.balance -= amount;
  return "Payment Successful";
}
```

---

## 4) Object Maps / Lookup Tables (Modern Alternative to `switch`)

When matching discrete string/number keys, an **Object Map** or `Map` is cleaner, faster, and easier to extend than a long `switch` statement.

### Refactoring `switch` to Object Lookup
```js
// --- Object Map ---
const ROLE_PERMISSIONS = {
  admin: ["read", "write", "delete"],
  editor: ["read", "write"],
  viewer: ["read"]
};

function getPermissions(role) {
  // Use Nullish Coalescing (??) for default fallback
  return ROLE_PERMISSIONS[role] ?? [];
}

console.log(getPermissions("editor")); // ["read", "write"]
console.log(getPermissions("guest"));  // []
```

### Strategy Pattern with Functions in Object Maps
```js
const actionHandlers = {
  START: () => "Engine Started",
  STOP: () => "Engine Stopped",
  PAUSE: () => "Engine Paused"
};

function handleAction(action) {
  const handler = actionHandlers[action] ?? (() => "Unknown Action");
  return handler();
}
```

---

## 5) Decision Pattern Selection Matrix

| Pattern | Best Used For | Avoid When |
| :--- | :--- | :--- |
| **`if` / `else if`** | Range checks (`score >= 90`), complex boolean combinations | Matching many fixed string/numeric values |
| **`switch`** | Multiple fixed discrete values, grouped cases | Complex boolean logic or range comparisons |
| **Guard Clauses** | Function input validations, returning early | Simple 2-branch decisions |
| **Object / Map Lookup** | Dynamic string/action dispatching, config resolution | Range checks or multi-variable logic |
| **Ternary (`? :`)** | Simple 2-branch inline expression assignments | Multi-branch nesting (`a ? b : c ? d : e`) |

---

## 6) Best Practices Checklist
1. **Flatten Nested Logic**: Replace nested `if` statements with Guard Clauses.
2. **Isolate `case` Scopes**: Always wrap `case` blocks in `{}` if declaring `let` or `const` variables inside a `switch`.
3. **Use Object Maps for Key-Value Dispatching**: Prefer Object Maps over large `switch` statements when mapping discrete inputs to outputs.
4. **Avoid Nested Ternaries**: Use standard `if/else` or Guard Clauses instead of chaining multiple ternary operators (`condition1 ? val1 : condition2 ? val2 : val3`).

---

## 7) Quick Practice & Exercises

### Exercise 1: Refactor Nested Code into Guard Clauses
Refactor the following nested function using Guard Clauses:

```js
function calculateDiscount(user, cartTotal) {
  let discount = 0;
  if (user) {
    if (user.isVIP) {
      if (cartTotal > 100) {
        discount = 0.20;
      } else {
        discount = 0.10;
      }
    } else {
      if (cartTotal > 100) {
        discount = 0.05;
      }
    }
  }
  return discount;
}
```

<details>
<summary>View Solution</summary>

```js
function calculateDiscount(user, cartTotal) {
  if (!user) return 0;
  
  if (user.isVIP) {
    return cartTotal > 100 ? 0.20 : 0.10;
  }
  
  return cartTotal > 100 ? 0.05 : 0;
}
```
</details>

---

### Exercise 2: Refactor `switch` to Object Map
Convert this `switch` into an Object Lookup Table:

```js
function HTTPStatusMessage(code) {
  switch (code) {
    case 200: return "OK";
    case 400: return "Bad Request";
    case 404: return "Not Found";
    case 500: return "Internal Server Error";
    default: return "Unknown Status";
  }
}
```

<details>
<summary>View Solution</summary>

```js
const HTTP_MESSAGES = {
  200: "OK",
  400: "Bad Request",
  404: "Not Found",
  500: "Internal Server Error"
};

function HTTPStatusMessage(code) {
  return HTTP_MESSAGES[code] ?? "Unknown Status";
}
```
</details>

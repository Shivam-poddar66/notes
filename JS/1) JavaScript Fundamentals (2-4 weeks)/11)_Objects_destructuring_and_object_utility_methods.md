# Objects, Destructuring, and Object Utility Methods

## 1) Object Literals and Fundamentals

In JavaScript, an **Object** is a standalone collection of key-value pairs. Keys are always **Strings** or **Symbols**, and values can be any valid JavaScript data type (primitives, objects, arrays, or functions).

### Access Notation: Dot vs. Bracket

```js
const user = {
  id: 1,
  "user-name": "Shivam",
  age: 25
};

// 1. Dot Notation (Used when key name is a valid JS identifier)
console.log(user.name); // undefined
console.log(user.age);  // 25

// 2. Bracket Notation (Required for keys with hyphens/spaces, or dynamic variable lookup)
console.log(user["user-name"]); // "Shivam"

const keyToRead = "age";
console.log(user[keyToRead]);   // 25 (Dynamic key access)
```

### Modern ES6 Object Enhancements

```js
const name = "Alice";
const role = "Admin";
const dynamicField = "status";

const userProfile = {
  // 1. Property Shorthand (Key and variable share the same name)
  name,
  role,

  // 2. Computed Property Names (Dynamic key calculation inside [])
  [dynamicField]: "Active",

  // 3. Method Shorthand
  greet() {
    return `Hello, ${this.name}`;
  }
};
```

---

## 2) Destructuring Assignment

Destructuring allows unpacking values from objects or arrays into distinct variables cleanly.

### Object Destructuring Basics

```js
const user = {
  id: 101,
  firstName: "Alice",
  role: "Editor"
};

// Basic destructuring
const { firstName, role } = user;
console.log(firstName, role); // "Alice", "Editor"
```

### Advanced Destructuring Patterns

```js
const settings = {
  theme: "dark",
  fontSize: 16
};

// 1. Variable Renaming (originalKey: newVariableName)
const { theme: appTheme } = settings;
console.log(appTheme); // "dark"

// 2. Default Values (fallback if property is undefined)
const { language = "en" } = settings;
console.log(language); // "en"

// 3. Renaming AND Default Values Combined
const { fontColor: textColor = "#000" } = settings;
console.log(textColor); // "#000"

// 4. Nested Object Destructuring
const employee = {
  id: 1,
  contact: {
    email: "alice@example.com",
    city: "New York"
  }
};

const { contact: { email, city } } = employee;
console.log(email, city); // "alice@example.com", "New York"

// 5. Rest Properties (...rest)
const { id, ...contactInfo } = employee;
console.log(contactInfo); // { contact: { email: "...", city: "..." } }
```

---

## 3) Object Immutability and Copying

### The Spread Operator (`...`) for Objects
The object spread operator creates a shallow copy and allows immutably overriding or extending properties.

```js
const originalUser = { id: 1, name: "Bob", role: "User" };

// Immutable property update
const updatedUser = {
  ...originalUser,
  role: "Admin",    // Overrides 'role'
  isLoggedIn: true  // Adds new property
};

console.log(originalUser.role); // "User" (Original untouched!)
console.log(updatedUser.role);  // "Admin"
```

### Shallow Copy vs. Deep Copy

```js
const original = {
  name: "Alice",
  details: { city: "London" }
};

// 1. Shallow Copy (Spread or Object.assign)
const shallow = { ...original };
shallow.details.city = "Paris";
console.log(original.details.city); // "Paris" (BUG: Nested object reference is shared!)

// 2. Deep Copy (structuredClone API - ES2022)
const deep = structuredClone(original);
deep.details.city = "Tokyo";
console.log(original.details.city); // "Paris" (Original untouched!)
```

---

## 4) Essential Object Utility Methods

### 1. `Object.keys()`, `Object.values()`, and `Object.entries()`
```js
const product = { id: 1, name: "Phone", price: 699 };

console.log(Object.keys(product));   // ["id", "name", "price"]
console.log(Object.values(product)); // [1, "Phone", 699]
console.log(Object.entries(product));// [["id", 1], ["name", "Phone"], ["price", 699]]
```

### 2. `Object.fromEntries()` (ES2019)
Transforms a list of key-value pairs (like an array or `Map`) back into an Object. Excellent for filtering or transforming object properties.

```js
const prices = { apple: 2, banana: 1, cherry: 5 };

// Filter object to only items costing more than $1.50
const expensiveItems = Object.fromEntries(
  Object.entries(prices).filter(([_, price]) => price > 1.50)
);

console.log(expensiveItems); // { apple: 2, cherry: 5 }
```

### 3. Safe Property Checking: `Object.hasOwn()` (ES2022)
Replaces legacy `Object.prototype.hasOwnProperty.call(obj, prop)`. Checks if a property exists directly on the object (not inherited from prototype).

```js
const user = { name: "Alice" };

console.log(Object.hasOwn(user, "name"));     // true
console.log(Object.hasOwn(user, "toString")); // false (Inherited from Object prototype)
console.log("toString" in user);              // true ('in' checks prototype chain!)
```

### 4. Locking Objects: `Object.freeze()` vs. `Object.seal()`

| Feature | `Object.freeze(obj)` | `Object.seal(obj)` |
| :--- | :--- | :--- |
| **Add New Properties?** | **No** | **No** |
| **Delete Properties?** | **No** | **No** |
| **Modify Existing Values?**| **No** (Fully Read-Only) | **Yes** (Existing properties can be changed) |

```js
const config = Object.freeze({ apiURL: "https://api.com" });
// config.apiURL = "https://hack.com"; // Fails silently or throws TypeError in strict mode!
```

---

## 5) Best Practices Checklist
1. **Use `Object.hasOwn(obj, key)`**: Always prefer `Object.hasOwn()` over `hasOwnProperty` or `"key" in obj` when validating own properties.
2. **Use `structuredClone()` for Deep Copies**: Avoid `JSON.parse(JSON.stringify(obj))` for deep copies as it drops functions, undefined, and Symbols.
3. **Use Object Destructuring in Parameters**: Make function signatures explicit by destructuring options objects in parameters.
4. **Transform Objects with `entries` + `fromEntries`**: Avoid mutating objects during filtering or property modifications.

---

## 6) Quick Practice & Exercises

### Exercise 1: Transform Object Values
Write a function `uppercaseValues(obj)` that returns a new object where all string values are capitalized:

```js
const data = { name: "alice", city: "paris", score: 100 };
```

<details>
<summary>View Solution</summary>

```js
function uppercaseValues(obj) {
  return Object.fromEntries(
    Object.entries(obj).map(([key, val]) => [
      key,
      typeof val === "string" ? val.toUpperCase() : val
    ])
  );
}

console.log(uppercaseValues({ name: "alice", city: "paris", score: 100 }));
// Output: { name: "ALICE", city: "PARIS", score: 100 }
```
</details>

---

### Exercise 2: Destructuring with Renaming and Defaults
Extract `title` as `jobTitle`, `department` with a default of `"General"`, and gather remaining properties into `rest`:

```js
const employee = {
  id: 404,
  title: "Frontend Engineer",
  salary: 90000
};
```

<details>
<summary>View Solution</summary>

```js
const {
  title: jobTitle,
  department = "General",
  ...rest
} = employee;

console.log(jobTitle);   // "Frontend Engineer"
console.log(department); // "General"
console.log(rest);       // { id: 404, salary: 90000 }
```
</details>

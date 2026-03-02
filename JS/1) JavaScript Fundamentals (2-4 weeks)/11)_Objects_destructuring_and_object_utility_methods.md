# Objects, Destructuring, and Object Utility Methods

## 1) Object Basics

```js
const user = {
  id: 1,
  name: "Shivam",
  address: { city: "Pune" }
};
```

Access:

```js
user.name;      // dot notation
user["name"];  // bracket notation
```

## 2) Create and Update

```js
user.age = 25;
user.name = "SK";
```

## 3) Destructuring

```js
const { name, age } = user;
const { city } = user.address;
```

Rename + default:

```js
const { name: fullName, phone = "NA" } = user;
```

## 4) Useful Object Methods

### `Object.keys(obj)`
Returns key array.

### `Object.values(obj)`
Returns value array.

### `Object.entries(obj)`
Returns `[key, value]` pairs.

### `Object.assign(target, source)`
Copies properties.

### Spread syntax

```js
const updatedUser = { ...user, role: "admin" };
```

## 5) Shallow Copy Warning
`Object.assign` and spread create shallow copy only.
Nested objects still share references.

## 6) Best Practices
- Use destructuring for cleaner code.
- Use immutable update pattern where possible.
- Check property existence safely with `in` or `hasOwnProperty` patterns.

## 7) Quick Practice
1. Convert object to entries and back.
2. Copy object and update one field immutably.
3. Extract nested values with destructuring.

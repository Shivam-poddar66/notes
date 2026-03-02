# Control Flow: if, switch, and Decision Patterns

## 1) `if`, `else if`, `else`
Use when conditions are range-based or complex.

```js
if (score >= 90) {
  grade = "A";
} else if (score >= 75) {
  grade = "B";
} else {
  grade = "C";
}
```

## 2) `switch`
Use for exact value matching.

```js
switch (role) {
  case "admin":
    access = "all";
    break;
  case "editor":
    access = "partial";
    break;
  default:
    access = "read";
}
```

## 3) Guard Clauses
Reduce nesting by returning early.

```js
function checkout(cart) {
  if (!cart || cart.length === 0) return "Cart empty";
  return "Proceed";
}
```

## 4) Pattern Selection
- Use `if/else` for ranges and boolean expressions.
- Use `switch` for many fixed-value branches.
- Keep branches short and clear.

## 5) Common Mistakes
- Missing `break` in `switch`.
- Deep nested `if` blocks.
- Complex conditions without helper variables.

## 6) Quick Practice
1. Build a grade calculator using `if/else`.
2. Build a day-name mapper using `switch`.
3. Refactor nested logic using guard clauses.

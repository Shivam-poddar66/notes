# Closures: Core Concepts and Use Cases

## 1) Closure Definition
A closure is a function that remembers variables from its lexical scope even after outer function has finished.

```js
function makeCounter() {
  let count = 0;
  return function () {
    count += 1;
    return count;
  };
}

const counter = makeCounter();
counter(); // 1
counter(); // 2
```

## 2) Why Closures Matter
Closures power:
- Data privacy and encapsulation
- Function factories
- Memoization
- Event handlers with preserved state

## 3) Function Factory Example

```js
function makeMultiplier(factor) {
  return function (value) {
    return value * factor;
  };
}

const double = makeMultiplier(2);
```

## 4) Encapsulation Pattern

```js
function createBankAccount(initial) {
  let balance = initial;

  return {
    deposit(amount) {
      balance += amount;
    },
    getBalance() {
      return balance;
    }
  };
}
```

## 5) Best Practices
- Use closures intentionally, not accidentally.
- Keep captured state minimal.
- Document closure-heavy logic.

## 6) Quick Practice
1. Build private counter module.
2. Build memoized square function.
3. Explain closure behavior line by line.

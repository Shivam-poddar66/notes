# ES6 Classes vs Prototype Model

## 1) Key Idea
ES6 `class` is syntactic sugar over prototype-based inheritance.

## 2) Class Syntax Example

```js
class User {
  constructor(name) {
    this.name = name;
  }

  greet() {
    return `Hi, ${this.name}`;
  }
}
```

Methods are stored on `User.prototype`, not copied per instance.

## 3) Inheritance with `extends`

```js
class Admin extends User {
  constructor(name, role) {
    super(name);
    this.role = role;
  }
}
```

## 4) Class Features
- `constructor`
- instance methods
- static methods
- private fields (`#field`)

## 5) Class vs Prototype Tradeoff
- Class syntax: cleaner and team-friendly.
- Prototype APIs: deeper control and internal understanding.

You should know both.

## 6) Quick Practice
1. Rewrite constructor-function code using class syntax.
2. Convert class back to prototype style manually.
3. Explain where class methods are stored.

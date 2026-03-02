# `Object.create` and Manual Inheritance Patterns

## 1) `Object.create(proto)`
Creates a new object with specific prototype.

```js
const personProto = {
  greet() {
    return `Hello, ${this.name}`;
  }
};

const user = Object.create(personProto);
user.name = "Shivam";
```

## 2) Add Property Descriptors at Creation

```js
const account = Object.create({}, {
  id: { value: 101, writable: false, enumerable: true }
});
```

## 3) Delegation Pattern
Objects delegate behavior to prototype object instead of class hierarchy.

## 4) Pros and Cons
Pros:
- Explicit control over prototype.
- Simple delegation model.

Cons:
- Less familiar to some teams.
- Can be misused with deep prototype chains.

## 5) Quick Practice
1. Build object factory using `Object.create`.
2. Add shared methods in prototype object.
3. Compare this pattern with constructor + `new`.

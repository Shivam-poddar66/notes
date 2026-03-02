# Constructor Functions and Prototypal Inheritance

## 1) Constructor Function Pattern

```js
function User(name) {
  this.name = name;
}

User.prototype.greet = function () {
  return `Hi, ${this.name}`;
};

const u1 = new User("Shivam");
```

## 2) What `new` Does
1. Creates empty object.
2. Links object prototype to constructor's prototype.
3. Binds `this` to new object.
4. Returns object (unless explicit object returned).

## 3) Prototypal Inheritance

```js
function Admin(name, role) {
  User.call(this, name);
  this.role = role;
}

Admin.prototype = Object.create(User.prototype);
Admin.prototype.constructor = Admin;
```

## 4) Why Use Prototype Methods
- Memory efficient.
- Shared behavior across instances.

## 5) Pitfalls
- Forgetting `new`.
- Forgetting to reset constructor after prototype assignment.

## 6) Quick Practice
1. Build `Vehicle` and `Car` constructor hierarchy.
2. Add shared method on prototype.
3. Verify `instanceof` behavior.

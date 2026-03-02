# `this` Keyword by Invocation Context

## 1) Core Rule
`this` is determined by how a function is called, not where it is defined.

## 2) Global Context
In browser scripts, `this` at top level usually points to `window` (module mode differs).
In Node module files, top-level `this` is module-scoped behavior.

## 3) Function Call

```js
function show() {
  console.log(this);
}
show();
```

- Non-strict mode: global object.
- Strict mode: `undefined`.

## 4) Method Call

```js
const user = {
  name: "Shivam",
  show() {
    console.log(this.name);
  }
};
user.show();
```

`this` points to object before dot (`user`).

## 5) Arrow Function Behavior
Arrow functions do not create their own `this`.
They capture `this` from surrounding lexical scope.

## 6) Class Methods
Inside class instance methods, `this` points to instance when called as `instance.method()`.
Detached method references can lose binding.

## 7) Constructor Function Call with `new`
`new` creates a new object and binds `this` to it.

## 8) Quick Practice
1. Predict `this` in 8 different call patterns.
2. Fix a detached method bug with `bind`.
3. Convert incorrect arrow method usage to regular method.

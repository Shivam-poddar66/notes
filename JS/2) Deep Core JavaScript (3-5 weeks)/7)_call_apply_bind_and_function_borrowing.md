# `call`, `apply`, `bind`, and Function Borrowing

## 1) `call`
Invokes function immediately with explicit `this` and comma-separated args.

```js
function greet(city) {
  return `${this.name} from ${city}`;
}

greet.call({ name: "A" }, "Delhi");
```

## 2) `apply`
Like `call`, but args are passed as array.

```js
greet.apply({ name: "B" }, ["Pune"]);
```

## 3) `bind`
Returns new function with fixed `this` (and optional partial args).

```js
const bound = greet.bind({ name: "C" }, "Mumbai");
bound();
```

## 4) Function Borrowing
Use one object's method for another by changing `this`.

```js
const person1 = { name: "A" };
const person2 = { name: "B" };

function intro() {
  return `Hi, I am ${this.name}`;
}

intro.call(person2);
```

## 5) Common Uses
- Event handlers needing stable context.
- Partial application patterns.
- Method reuse across objects.

## 6) Pitfalls
- Overusing `bind` can reduce readability.
- Avoid binding inside hot loops repeatedly.

## 7) Quick Practice
1. Implement custom `myBind` skeleton.
2. Convert `call` example to `apply`.
3. Fix callback `this` bug in class.

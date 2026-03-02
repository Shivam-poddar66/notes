# Mini Project 3: Polyfills for `map`, `filter`, `reduce`

## Goal
Rebuild key array methods to understand callbacks, `thisArg`, iteration, and accumulator logic.

## 1) `myMap`
```js
Array.prototype.myMap = function (callback, thisArg) {
  const result = [];
  for (let i = 0; i < this.length; i++) {
    if (!(i in this)) continue;
    result[i] = callback.call(thisArg, this[i], i, this);
  }
  return result;
};
```

## 2) `myFilter`
```js
Array.prototype.myFilter = function (callback, thisArg) {
  const result = [];
  for (let i = 0; i < this.length; i++) {
    if (!(i in this)) continue;
    if (callback.call(thisArg, this[i], i, this)) result.push(this[i]);
  }
  return result;
};
```

## 3) `myReduce`
```js
Array.prototype.myReduce = function (callback, initialValue) {
  let i = 0;
  let acc;

  if (arguments.length > 1) {
    acc = initialValue;
  } else {
    while (i < this.length && !(i in this)) i++;
    if (i >= this.length) throw new TypeError("Reduce of empty array with no initial value");
    acc = this[i++];
  }

  for (; i < this.length; i++) {
    if (!(i in this)) continue;
    acc = callback(acc, this[i], i, this);
  }

  return acc;
};
```

## Required Test Cases
- Empty array behavior.
- Sparse array behavior.
- `thisArg` support.
- Initial accumulator present/absent for reduce.

## Done Criteria
- Behavior matches native methods for tested cases.
- Edge cases handled with correct errors.

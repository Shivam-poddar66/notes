# Promise Chaining and Error Propagation

## 1) Chaining Basics
Each `.then()` returns a new promise.

```js
fetchData()
  .then(parse)
  .then(validate)
  .then(save)
  .catch(handleError);
```

## 2) Error Propagation Rule
If an error is thrown in any `.then`, control moves to nearest `.catch`.

## 3) Return Discipline
Inside `.then`, always return value/promise if next step depends on it.

Bad:
```js
.then(() => {
  fetchMore(); // missing return
})
```

Good:
```js
.then(() => {
  return fetchMore();
})
```

## 4) `finally`
`finally` runs regardless of success/failure.
Use it for cleanup, not value transformation.

## 5) Quick Practice
1. Build 4-step chain with one failure branch.
2. Add retry in chain before final catch.
3. Identify missing-return bugs in sample code.

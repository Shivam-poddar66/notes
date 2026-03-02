# Mini Project 3: Promise Utility Library

## Goal
Build reusable async utilities to deeply understand promise orchestration.

## Required Utilities
1. `delay(ms)`
2. `timeout(promise, ms)`
3. `retry(fn, options)`
4. `parallelLimit(tasks, limit)`
5. `settleMap(items, mapper)`

## Example: `delay`
```js
const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
```

## Example: `timeout`
```js
function timeout(promise, ms) {
  return Promise.race([
    promise,
    new Promise((_, reject) => setTimeout(() => reject(new Error("Timeout")), ms))
  ]);
}
```

## Testing Checklist
- Success and failure paths.
- Edge cases (`ms = 0`, empty tasks, invalid inputs).
- Correct error propagation.

## Packaging Suggestion
```text
promise-utils/
  src/
    delay.js
    timeout.js
    retry.js
    parallelLimit.js
    settleMap.js
  tests/
  index.js
```

## Done Criteria
- Utilities are composable and documented.
- Behavior verified with tests/examples.
- Clean module exports.

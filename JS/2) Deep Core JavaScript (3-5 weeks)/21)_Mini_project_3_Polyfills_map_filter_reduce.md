# Mini-Project 3: Production-Grade Polyfills for `map`, `filter`, and `reduce`

---

## 1) Core Mental Model: The Polyfill Challenge

A **Polyfill** is code that replicates a native browser/runtime feature conforming to the official ECMAScript specification.

Writing array iteration polyfills from scratch is one of the most common senior JavaScript interview questions because it tests mastery over:
1. **Execution Contexts & `thisArg`**: Dynamically binding `this` inside callbacks.
2. **Sparse Arrays (Array Holes)**: Correctly skipping unallocated memory slots (e.g. `[1, , 3]`).
3. **Spec-Compliant Error Handling**: Throwing exact `TypeError` messages when inputs are invalid.
4. **Accumulator Transition Mechanics**: Managing optional initial values in `reduce`.
5. **Function Borrowing**: Allowing polyfills to run over array-like objects (`arguments`, `NodeList`, `{ length: 2, 0: 'a' }`).

```
┌────────────────────────────────────────────────────────────────────────┐
│                   ARRAY ITERATION SPEC PROTOCOL                        │
│                                                                        │
│   1. Validate Target:      Convert `this` to Object (Reject null/undef)│
│   2. Read Length:          Convert `this.length` to unsigned 32-bit int│
│   3. Validate Callback:    Ensure `typeof callback === "function"`     │
│   4. Handle Sparse Slots:  Check `i in O` before executing callback    │
│   5. Context Binding:      Execute `callback.call(thisArg, val, i, O)` │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Polyfill 1: `Array.prototype.myMap`

### ECMAScript Requirements:
- Returns a brand-new array of identical length.
- Invokes `callback(element, index, array)` with `thisArg` as `this`.
- **Skips holes in sparse arrays** without invoking the callback, but preserves empty slots in the output array.

```js
/**
 * Spec-Compliant Array.prototype.map Polyfill
 */
Array.prototype.myMap = function (callback, thisArg) {
  // 1. Guard against null or undefined this
  if (this == null) {
    throw new TypeError("Array.prototype.myMap called on null or undefined");
  }

  // 2. Validate that callback is a callable function
  if (typeof callback !== "function") {
    throw new TypeError(`${callback} is not a function`);
  }

  // 3. Convert target to object and clamp length to unsigned 32-bit integer
  const O = Object(this);
  const len = O.length >>> 0;
  const result = new Array(len);

  // 4. Iterate over indices
  for (let i = 0; i < len; i++) {
    // Check if property key exists in object (Skips sparse array holes!)
    if (i in O) {
      result[i] = callback.call(thisArg, O[i], i, O);
    }
  }

  return result;
};
```

---

## 3) Polyfill 2: `Array.prototype.myFilter`

### ECMAScript Requirements:
- Returns a new array containing only elements where `callback` returns a truthy value.
- Skips holes in sparse arrays.
- Does not mutate the original array.

```js
/**
 * Spec-Compliant Array.prototype.filter Polyfill
 */
Array.prototype.myFilter = function (callback, thisArg) {
  if (this == null) {
    throw new TypeError("Array.prototype.myFilter called on null or undefined");
  }

  if (typeof callback !== "function") {
    throw new TypeError(`${callback} is not a function`);
  }

  const O = Object(this);
  const len = O.length >>> 0;
  const result = [];

  for (let i = 0; i < len; i++) {
    if (i in O) {
      const element = O[i];
      // Truthiness check using Boolean coercion
      if (Boolean(callback.call(thisArg, element, i, O))) {
        result.push(element);
      }
    }
  }

  return result;
};
```

---

## 4) Polyfill 3: `Array.prototype.myReduce`

### ECMAScript Requirements:
- **Case 1: `initialValue` provided**: `accumulator` starts as `initialValue`; loop begins at index `0`.
- **Case 2: `initialValue` omitted**: `accumulator` starts as the **first non-empty slot** in the array; loop begins at the subsequent index.
- **Case 3: Empty Array with no `initialValue`**: Throws `TypeError: Reduce of empty array with no initial value`.

```js
/**
 * Spec-Compliant Array.prototype.reduce Polyfill
 */
Array.prototype.myReduce = function (callback, initialValue) {
  if (this == null) {
    throw new TypeError("Array.prototype.myReduce called on null or undefined");
  }

  if (typeof callback !== "function") {
    throw new TypeError(`${callback} is not a function`);
  }

  const O = Object(this);
  const len = O.length >>> 0;
  let accumulator;
  let k = 0;

  // Check if initialValue was explicitly supplied (arguments.length >= 2)
  if (arguments.length >= 2) {
    accumulator = initialValue;
  } else {
    // Find the first defined element in sparse array
    let kPresent = false;
    while (k < len) {
      if (k in O) {
        accumulator = O[k];
        kPresent = true;
        k++;
        break;
      }
      k++;
    }

    // If array is completely empty/sparse and no initialValue was passed -> Throw error!
    if (!kPresent) {
      throw new TypeError("Reduce of empty array with no initial value");
    }
  }

  // Iterate remaining elements
  for (; k < len; k++) {
    if (k in O) {
      accumulator = callback(accumulator, O[k], k, O);
    }
  }

  return accumulator;
};
```

---

## 5) Bonus Polyfills: `myForEach`, `myFind`, `mySome`, `myEvery`

```js
// --- forEach Polyfill ---
Array.prototype.myForEach = function (callback, thisArg) {
  if (this == null) throw new TypeError("called on null/undefined");
  if (typeof callback !== "function") throw new TypeError("Callback must be a function");

  const O = Object(this);
  const len = O.length >>> 0;
  for (let i = 0; i < len; i++) {
    if (i in O) callback.call(thisArg, O[i], i, O);
  }
};

// --- find Polyfill ---
Array.prototype.myFind = function (predicate, thisArg) {
  if (this == null) throw new TypeError("called on null/undefined");
  if (typeof predicate !== "function") throw new TypeError("Predicate must be a function");

  const O = Object(this);
  const len = O.length >>> 0;
  for (let i = 0; i < len; i++) {
    if (predicate.call(thisArg, O[i], i, O)) return O[i];
  }
  return undefined;
};

// --- some Polyfill ---
Array.prototype.mySome = function (predicate, thisArg) {
  const O = Object(this);
  const len = O.length >>> 0;
  for (let i = 0; i < len; i++) {
    if (i in O && predicate.call(thisArg, O[i], i, O)) return true;
  }
  return false;
};

// --- every Polyfill ---
Array.prototype.myEvery = function (predicate, thisArg) {
  const O = Object(this);
  const len = O.length >>> 0;
  for (let i = 0; i < len; i++) {
    if (i in O && !predicate.call(thisArg, O[i], i, O)) return false;
  }
  return true;
};
```

---

## 6) Comprehensive Test Suite & Edge Case Verification

Execute this test suite to verify spec compliance:

```js
console.log("=== TEST SUITE: ARRAY POLYFILLS ===");

// 1. myMap with thisArg
const multiplier = {
  factor: 10,
  multiply(val) { return val * this.factor; }
};
const nums = [1, 2, 3];
const mapped = nums.myMap(function (n) { return this.multiply(n); }, multiplier);
console.log("[Test 1: myMap with thisArg]", mapped); // [10, 20, 30]

// 2. myMap with Sparse Array
const sparseArr = [1, , 3]; // Index 1 is empty hole
const mappedSparse = sparseArr.myMap((x) => x * 2);
console.log("[Test 2: myMap Sparse Array]", mappedSparse); // [2, <empty>, 6]
console.log("Sparse index 1 in mappedSparse?", 1 in mappedSparse); // false (Hole preserved!)

// 3. myFilter
const mixed = [10, 0, false, "Hello", null, undefined, 42];
const filtered = mixed.myFilter(Boolean);
console.log("[Test 3: myFilter Truthy]", filtered); // [10, "Hello", 42]

// 4. myReduce with initialValue
const sum = [1, 2, 3, 4].myReduce((acc, curr) => acc + curr, 0);
console.log("[Test 4: myReduce Sum with initialValue]", sum); // 10

// 5. myReduce without initialValue on Sparse Array
const sparseReduce = [, , 5, 10, 15].myReduce((acc, curr) => acc + curr);
console.log("[Test 5: myReduce Sparse Array without init]", sparseReduce); // 30

// 6. myReduce Empty Array Error Handling
try {
  [].myReduce((a, b) => a + b);
} catch (err) {
  console.log("[Test 6: myReduce Empty Array Exception]", err.message);
  // "Reduce of empty array with no initial value"
}

// 7. Function Borrowing on Array-Like Object
function testArgumentsBorrowing() {
  const upper = Array.prototype.myMap.call(arguments, (str) => str.toUpperCase());
  console.log("[Test 7: Function Borrowing on arguments]", upper);
}
testArgumentsBorrowing("js", "node", "esm"); // ["JS", "NODE", "ESM"]
```

---

## 7) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                         ARRAY POLYFILLS SUMMARY                            |
+───────────────────────────+────────────────────────────────────────────────+
| Sparse Array Handling     | Use `if (i in O)` to skip holes.               |
| `thisArg` Binding         | `callback.call(thisArg, element, index, array)`|
| Length Clamping           | `O.length >>> 0` (Converts to 32-bit uint).    |
| `myReduce` Init Value     | Check `arguments.length >= 2`.                 |
| Empty `myReduce` Guard    | Throws `TypeError` if no initial value passed. |
| Function Borrowing        | Works on array-likes via `Object(this)`.       |
+───────────────────────────+────────────────────────────────────────────────+
```

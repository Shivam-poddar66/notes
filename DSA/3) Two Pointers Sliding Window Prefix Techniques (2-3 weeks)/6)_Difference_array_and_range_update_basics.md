# Difference Array and Range Update Basics

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Apply many range updates efficiently by marking only boundaries, then reconstruct the final array with one prefix pass.

## Core Idea
For update: add `val` to inclusive range `[l..r]`.
- Instead of touching all `r - l + 1` elements:
  - `diff[l] += val`
  - `diff[r + 1] -= val` (if within bounds)
- Final values come from prefix sum of `diff`.

This turns repeated range updates from `O(q * n)` or `O(total range length)` into `O(q + n)`.

## Relationship to Prefix Sum
- Prefix sum answers fast range queries on static data.
- Difference array applies fast range updates, then reconstructs data.
- They are inverse-style operations:
  - `diff` marks change points.
  - prefix over `diff` rebuilds actual values.

## 1D Difference Array Model
Given base array `arr` length `n`:
- Build diff:
  - `diff[0] = arr[0]`
  - `diff[i] = arr[i] - arr[i - 1]` for `i > 0`
- After updates, rebuild:
  - `arr[0] = diff[0]`
  - `arr[i] = arr[i - 1] + diff[i]`

In many problems, you start with zeros and only apply updates, so direct diff initialization is enough.

## Template 1: Batch Range Add on Zero Array
```javascript
function applyRangeAdds(n, updates) {
  const diff = new Array(n + 1).fill(0); // n+1 handles r+1 safely

  for (const [l, r, val] of updates) {
    diff[l] += val;
    if (r + 1 < diff.length) diff[r + 1] -= val;
  }

  const result = new Array(n).fill(0);
  let running = 0;
  for (let i = 0; i < n; i++) {
    running += diff[i];
    result[i] = running;
  }
  return result;
}
```

Complexity:
- Updates: `O(q)`
- Rebuild: `O(n)`
- Space: `O(n)`

## Template 2: Apply Updates on Existing Array
```javascript
function applyRangeAddsToBase(arr, updates) {
  const n = arr.length;
  const diff = new Array(n + 1).fill(0);

  for (let i = 0; i < n; i++) {
    diff[i] += arr[i];
    if (i + 1 < n) diff[i + 1] -= arr[i];
  }

  for (const [l, r, val] of updates) {
    diff[l] += val;
    if (r + 1 <= n - 1) diff[r + 1] -= val;
  }

  const out = new Array(n).fill(0);
  let run = 0;
  for (let i = 0; i < n; i++) {
    run += diff[i];
    out[i] = run;
  }
  return out;
}
```

## Common Pattern: Corporate Flight Bookings
Each booking `[first, last, seats]` is a range add.
- Convert to 0-based if needed.
- Use diff updates, then one prefix reconstruction.

## 2D Difference Array (Rectangle Updates)
For adding `val` to rectangle `(r1,c1)` to `(r2,c2)` inclusive:
- `diff[r1][c1] += val`
- `diff[r1][c2 + 1] -= val`
- `diff[r2 + 1][c1] -= val`
- `diff[r2 + 1][c2 + 1] += val`

After all updates, take 2D prefix sums to reconstruct final matrix.

## Template 3: 2D Range Add Skeleton
```javascript
function apply2DUpdates(rows, cols, updates) {
  const diff = Array.from({ length: rows + 1 }, () => new Array(cols + 1).fill(0));

  for (const [r1, c1, r2, c2, val] of updates) {
    diff[r1][c1] += val;
    if (c2 + 1 < cols) diff[r1][c2 + 1] -= val;
    if (r2 + 1 < rows) diff[r2 + 1][c1] -= val;
    if (r2 + 1 < rows && c2 + 1 < cols) diff[r2 + 1][c2 + 1] += val;
  }

  const out = Array.from({ length: rows }, () => new Array(cols).fill(0));
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      const top = r > 0 ? out[r - 1][c] : 0;
      const left = c > 0 ? out[r][c - 1] : 0;
      const diag = r > 0 && c > 0 ? out[r - 1][c - 1] : 0;
      out[r][c] = diff[r][c] + top + left - diag;
    }
  }
  return out;
}
```

## Why It Works (Invariant)
- `diff[i]` stores net change that starts at `i`.
- Prefix accumulation at `i` includes all updates whose ranges cover `i`.
- Boundary subtraction at `r + 1` cancels effect after range end.

## Difference Array vs Segment Tree/Fenwick
- Difference array:
  - Best for offline/batch updates then one final materialization.
  - Very simple and fast constants.
- Fenwick/segment tree:
  - Better when updates and queries are interleaved online.
  - Higher implementation complexity.

## Common Mistakes
- Forgetting bounds guard for `r + 1`.
- Mixing 0-based and 1-based indices in updates.
- Reconstructing from `diff` incorrectly (missing running accumulation).
- Using difference array when immediate query after each update is required.
- Off-by-one in inclusive range interpretation.

## Debug Checklist
- [ ] Is range update inclusive `[l..r]` in your formula?
- [ ] Did you apply both boundary operations (+ at `l`, - at `r+1`)?
- [ ] Are index conversions (1-based to 0-based) consistent?
- [ ] Did reconstruction use prefix accumulation exactly once?
- [ ] Did you test updates touching first and last index?

## JavaScript Notes
- For large `n`, preallocate arrays once to avoid repeated reallocations.
- Keep update records as tuples `[l, r, val]` for clarity and speed.
- If values can exceed safe integer limits, use `BigInt` consistently.

## Mastery Checklist
- [ ] I can derive diff updates for inclusive ranges without memorizing.
- [ ] I can reconstruct final array/matrix correctly.
- [ ] I can decide when diff array is better than Fenwick/segment tree.
- [ ] I can avoid indexing errors under interview pressure.

## Practice Targets
- 15 to 22 problems:
  - 8 one-dimensional range-add reconstruction
  - 4 two-dimensional rectangle updates
  - 4 comparison problems (diff vs prefix vs Fenwick choice)
- Re-solve at least 5 failed problems after 7 days.

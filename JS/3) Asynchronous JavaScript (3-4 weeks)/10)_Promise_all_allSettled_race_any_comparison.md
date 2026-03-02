# `Promise.all`, `allSettled`, `race`, `any` Comparison

## 1) `Promise.all`
- Resolves when all fulfill.
- Rejects immediately on first rejection.
- Use when all results are required.

## 2) `Promise.allSettled`
- Resolves after all settle.
- Returns status for each promise.
- Use when partial failures are acceptable.

## 3) `Promise.race`
- Settles with first settled promise (fulfill or reject).
- Useful for timeout races.

## 4) `Promise.any`
- Resolves with first fulfillment.
- Rejects only if all reject.
- Useful for fallback providers.

## 5) Selection Guide
- Need all successful: `all`
- Need all outcomes: `allSettled`
- Need first outcome: `race`
- Need first success: `any`

## 6) Quick Practice
1. Run same promise set with all four combinators.
2. Implement timeout using `race`.
3. Build first-success strategy using `any`.

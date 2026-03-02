# Stack Design and Classic Stack Problems

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Master stack design and the top interview patterns where LIFO processing gives the cleanest `O(n)` solution.

## Stack Mental Model
- Stack is a LIFO (last in, first out) structure.
- Core operations:
  - `push(x)`: add to top
  - `pop()`: remove top
  - `peek()/top()`: view top without removing
  - `isEmpty()`: check if stack has no elements
- Most stack algorithms are about maintaining an invariant on stack contents.

## When Stack Is the Right Tool
- You need to process nested structures (parentheses, tags, paths).
- You need nearest previous/next greater or smaller element.
- You need to defer work until a condition is satisfied later.
- You need to simulate recursion iteratively.

## Complexity Cheat Sheet

| Operation | Array-based Stack | Linked-list Stack |
|---|---|---|
| `push` | Amortized `O(1)` | `O(1)` |
| `pop` | `O(1)` | `O(1)` |
| `peek` | `O(1)` | `O(1)` |
| Extra pointer overhead | Low | Higher |
| Cache locality | Better | Worse |

JavaScript note:
- Use `push` and `pop`.
- Avoid `shift` and `unshift` for stack behavior (`O(n)`).

## Stack Implementations

### 1) Array-Based (Most practical in JS)
```js
class Stack {
  constructor() {
    this.a = [];
  }
  push(x) { this.a.push(x); }
  pop() { return this.a.pop(); }
  top() { return this.a[this.a.length - 1]; }
  isEmpty() { return this.a.length === 0; }
  size() { return this.a.length; }
}
```

### 2) Linked-List-Based
- Useful when interviewer asks explicit node-based stack.
- `head` acts as top.
- `push` inserts at head, `pop` removes head.

## Core Stack Pattern Families

### 1) Bracket / Parentheses Validation
Invariant:
- Stack contains unmatched opening brackets in order.

```js
function isValid(s) {
  const st = [];
  const pair = new Map([
    [')', '('],
    [']', '['],
    ['}', '{']
  ]);

  for (const ch of s) {
    if (!pair.has(ch)) st.push(ch);
    else {
      if (!st.length || st[st.length - 1] !== pair.get(ch)) return false;
      st.pop();
    }
  }
  return st.length === 0;
}
```

### 2) Monotonic Stack (Indices)
Used for:
- Next Greater Element
- Previous Smaller Element
- Daily Temperatures
- Stock Span
- Largest Rectangle in Histogram

Invariant:
- Stack keeps indices in monotonic order (increasing or decreasing values).
- Each index is pushed once and popped at most once => total `O(n)`.

```js
// Next greater element index for each position; -1 if none
function nextGreaterIndices(nums) {
  const n = nums.length;
  const ans = Array(n).fill(-1);
  const st = []; // decreasing stack of indices by value

  for (let i = 0; i < n; i++) {
    while (st.length && nums[st[st.length - 1]] < nums[i]) {
      ans[st.pop()] = i;
    }
    st.push(i);
  }
  return ans;
}
```

### 3) Min Stack / Max Stack Design
Idea:
- Maintain main stack + helper stack for current minimum/maximum.
- Each push updates helper with new min/max so `getMin`/`getMax` is `O(1)`.

```js
class MinStack {
  constructor() {
    this.st = [];
    this.mn = [];
  }

  push(x) {
    this.st.push(x);
    if (!this.mn.length) this.mn.push(x);
    else this.mn.push(Math.min(x, this.mn[this.mn.length - 1]));
  }

  pop() {
    if (!this.st.length) return undefined;
    this.mn.pop();
    return this.st.pop();
  }

  top() {
    return this.st[this.st.length - 1];
  }

  getMin() {
    return this.mn[this.mn.length - 1];
  }
}
```

### 4) Expression Handling (Stack-based)
High-level tasks:
- Infix to postfix conversion.
- Postfix evaluation.
- Basic calculator with parentheses and precedence.

Core idea:
- Operators go to stack.
- Output or operand stack handles values.
- Pop operators based on precedence and associativity rules.

## Classic Problem Template: Largest Rectangle in Histogram

Key insight:
- For each bar, find first smaller on left and right.
- Monotonic increasing stack gives these boundaries in linear time.

```js
function largestRectangleArea(heights) {
  const h = [...heights, 0]; // flush stack at end
  const st = []; // increasing indices
  let best = 0;

  for (let i = 0; i < h.length; i++) {
    while (st.length && h[st[st.length - 1]] > h[i]) {
      const mid = st.pop();
      const left = st.length ? st[st.length - 1] : -1;
      const width = i - left - 1;
      best = Math.max(best, h[mid] * width);
    }
    st.push(i);
  }
  return best;
}
```

## Design Questions You Should Be Ready For
- Implement stack using queues.
- Implement queue using stacks.
- Design browser back/forward history (two stacks).
- Design text editor undo/redo stacks.

## Common Mistakes
- Using values instead of indices when boundaries are required.
- Forgetting final cleanup pass for monotonic stack.
- Mixing strict/non-strict inequality and getting duplicate handling wrong.
- Popping from empty stack without guard checks.
- Assuming recursion and explicit stack have identical memory behavior without analysis.

## Debugging Checklist
- [ ] Did I define stack invariant before coding?
- [ ] Is each element pushed/popped at most once (for `O(n)` proof)?
- [ ] Are edge cases covered: empty input, one element, all equal, strictly increasing/decreasing?
- [ ] Did I flush remaining stack entries if required?
- [ ] For index stacks, are width/boundary calculations correct?

## Problem Mapping (High Frequency)
- Valid Parentheses -> bracket matching stack.
- Min Stack -> dual-stack design.
- Daily Temperatures -> monotonic decreasing stack.
- Next Greater Element I/II -> monotonic stack with/without circular pass.
- Largest Rectangle in Histogram -> monotonic increasing stack.
- Evaluate Reverse Polish Notation -> operand stack.
- Basic Calculator variants -> operator + value stacks.

## Mastery Checklist
- [ ] I can implement stack ADT from scratch in under 5 minutes.
- [ ] I can identify monotonic stack opportunities quickly.
- [ ] I can explain why monotonic solutions are `O(n)`.
- [ ] I can handle duplicates and boundary conditions correctly.
- [ ] I can transition between recursion and explicit stack models.

## Practice Targets
- 25 stack-focused problems total.
- Minimum split:
  - 8 monotonic stack
  - 5 bracket/expression
  - 4 design (min stack, queue via stacks, etc.)
  - 8 mixed medium/hard stack applications
- Re-solve all failed problems on D+7 and D+30.

# Fast Slow Pointer and Cycle Detection

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Use fast/slow pointers to solve linked-list and sequence problems in linear time with constant extra space.

## Core Idea
- Move `slow` by 1 step and `fast` by 2 steps.
- If a cycle exists, they must meet inside the cycle.
- If no cycle exists, `fast` reaches `null`.

## When to Use This Pattern
- Detect cycle in linked list or implicit state transitions.
- Find middle node in one pass.
- Find cycle entry point and cycle length.
- Split list into two halves for reordering/palindrome checks.

## Why Floyd's Algorithm Works
- Inside a cycle, relative speed of `fast` vs `slow` is 1 node per move.
- Relative movement guarantees eventual meeting within cycle length steps.
- After first meeting, resetting one pointer to head and moving both by 1 reaches cycle start.

## Template 1: Cycle Detection (Floyd)
```javascript
function hasCycle(head) {
  let slow = head;
  let fast = head;

  while (fast && fast.next) {
    slow = slow.next;
    fast = fast.next.next;
    if (slow === fast) return true;
  }
  return false;
}
```

Complexity:
- Time: `O(n)`
- Space: `O(1)`

## Template 2: Find Cycle Start Node
```javascript
function detectCycleStart(head) {
  let slow = head;
  let fast = head;

  while (fast && fast.next) {
    slow = slow.next;
    fast = fast.next.next;
    if (slow === fast) {
      let p1 = head;
      let p2 = slow;
      while (p1 !== p2) {
        p1 = p1.next;
        p2 = p2.next;
      }
      return p1;
    }
  }
  return null;
}
```

Invariant:
- Distance from head to cycle start equals distance from meeting point to cycle start along cycle path.

## Template 3: Find Middle Node
```javascript
function middleNode(head) {
  let slow = head;
  let fast = head;

  while (fast && fast.next) {
    slow = slow.next;
    fast = fast.next.next;
  }
  return slow;
}
```

Note:
- For even length, this returns second middle.
- To get first middle, use loop condition `while (fast.next && fast.next.next)`.

## Template 4: Cycle Length
```javascript
function cycleLength(head) {
  let slow = head;
  let fast = head;

  while (fast && fast.next) {
    slow = slow.next;
    fast = fast.next.next;
    if (slow === fast) {
      let len = 1;
      let cur = slow.next;
      while (cur !== slow) {
        len++;
        cur = cur.next;
      }
      return len;
    }
  }
  return 0;
}
```

## High-Value Problems
### 1) Linked List Cycle (easy)
- Return true/false for cycle presence.

### 2) Linked List Cycle II (medium)
- Return cycle entry node.

### 3) Middle of the Linked List (easy)
- One-pass middle extraction.

### 4) Happy Number (medium)
- Treat next-number transformation as implicit linked list.
- Detect loop on generated states with fast/slow pointers.

### 5) Palindrome Linked List (medium)
- Use fast/slow to reach middle.
- Reverse second half.
- Compare halves and optionally restore list.

## Pointer Movement Rules
- Never move `fast = fast.next.next` without checking `fast && fast.next`.
- Equality check should happen after both pointers move at least once in loop.
- For cycle-entry logic, do not reset both pointers, reset only one to head.

## Common Mistakes
- Missing null checks before two-step jump.
- Comparing values instead of node identity (`slow === fast` for nodes).
- Infinite loop from incorrect loop condition.
- Off-by-one middle node for even-length lists.
- Forgetting to break when meeting occurs in cycle-start solution.

## Debug Checklist
- [ ] Did I guard `fast` and `fast.next` before 2-step move?
- [ ] Am I comparing node references, not node values?
- [ ] Do my loop conditions match first-middle vs second-middle requirement?
- [ ] Did I test no-cycle, self-cycle, and short-list cases?

## Edge Cases to Test
- Empty list
- Single node without cycle
- Single node with self cycle
- Two nodes with/without cycle
- Long list with cycle near head
- Long list with cycle near tail

## JavaScript Notes
- Use strict reference comparison for nodes: `a === b`.
- Keep helpers pure where possible, except in-place operations like reverse.
- In interviews, ask whether list must be restored after transformations.

## Mastery Checklist
- [ ] I can implement Floyd detection from memory.
- [ ] I can derive cycle start proof and explain it clearly.
- [ ] I can switch between first/second middle variants intentionally.
- [ ] I can solve linked-list palindrome with `O(1)` extra space.

## Practice Targets
- 12 to 20 problems:
  - 5 cycle detection/entry variants
  - 4 middle/split/reorder variants
  - 3 implicit-cycle state problems (happy number style)
- Re-solve 4 failed problems after 7 days.

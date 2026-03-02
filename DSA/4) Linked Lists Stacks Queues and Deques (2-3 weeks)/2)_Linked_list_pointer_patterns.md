# Linked List Pointer Patterns

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Learn a small set of pointer patterns that solve most linked-list interview problems in `O(n)` time with clean, safe rewiring.

## How to Use This Note
- Step 1: Identify which pattern family the question belongs to.
- Step 2: Write the invariant before writing code.
- Step 3: Use a tested template and adapt minimally.
- Step 4: Validate with edge-case checklist before final answer.

## Pattern Recognition Cheat Table

| Problem Signal | Best Pattern |
|---|---|
| "Delete/insert near head safely" | Dummy/sentinel node |
| "Nth from end in one pass" | Fixed gap (fast ahead by `k`) |
| "Middle node / cycle check" | Slow-fast pointers |
| "Reverse all or part of list" | `prev-curr-next` rewiring |
| "Reverse between positions" | Head-insertion sublist reversal |
| "Merge sorted lists / weave lists" | Tail builder merge pattern |
| "Check palindrome list" | Slow-fast split + reverse second half |
| "Find intersection of two lists" | Pointer switching pattern |

## Core Pointer Patterns

### 1) Dummy/Sentinel Node
Use a fake node before head to avoid special handling when answer touches the first real node.

Invariant:
- `dummy.next` always points to current logical head.
- `prev` is always node before candidate being modified.

Best for:
- Remove elements by value.
- Remove nth node from end.
- Merge and partition style operations.

```js
function removeElements(head, target) {
  const dummy = { val: 0, next: head };
  let prev = dummy, curr = head;

  while (curr) {
    if (curr.val === target) prev.next = curr.next;
    else prev = curr;
    curr = curr.next;
  }
  return dummy.next;
}
```

### 2) Fixed-Gap Two Pointers
Place `fast` exactly `k` nodes ahead of `slow`, then move together.

Invariant:
- Distance between `fast` and `slow` stays `k`.
- When `fast` reaches end, `slow` is target predecessor/position.

Best for:
- Remove/find kth node from end.
- One-pass tail-relative operations.

```js
function removeNthFromEnd(head, n) {
  const dummy = { val: 0, next: head };
  let fast = dummy, slow = dummy;

  for (let i = 0; i < n; i++) fast = fast.next;
  while (fast.next) {
    fast = fast.next;
    slow = slow.next;
  }

  slow.next = slow.next.next;
  return dummy.next;
}
```

### 3) Slow-Fast (Tortoise-Hare)
Move `slow` by 1 and `fast` by 2.

Invariant:
- Before cycle detection collision, `fast` always moves twice as quickly.
- In no-cycle case, `fast` hits null first.

Best for:
- Middle node.
- Cycle detection and cycle entry.
- Splitting list into halves.

```js
function hasCycle(head) {
  let slow = head, fast = head;
  while (fast && fast.next) {
    slow = slow.next;
    fast = fast.next.next;
    if (slow === fast) return true;
  }
  return false;
}
```

### 4) `prev-curr-next` Rewiring (Full Reverse)
Classic in-place reversal template.

Invariant:
- Segment before `prev` is already reversed.
- `curr` is first node not yet processed.

```js
function reverseList(head) {
  let prev = null, curr = head;
  while (curr) {
    const next = curr.next;
    curr.next = prev;
    prev = curr;
    curr = next;
  }
  return prev;
}
```

### 5) Head-Insertion Sublist Reversal
Reverse `[left, right]` by moving each next node to the front of sublist window.

Invariant:
- `prev` stays fixed at node before sublist.
- `curr` stays tail of reversed portion during inner loop.

```js
function reverseBetween(head, left, right) {
  if (!head || left === right) return head;

  const dummy = { val: 0, next: head };
  let prev = dummy;
  for (let i = 1; i < left; i++) prev = prev.next;

  let curr = prev.next;
  for (let i = 0; i < right - left; i++) {
    const move = curr.next;
    curr.next = move.next;
    move.next = prev.next;
    prev.next = move;
  }

  return dummy.next;
}
```

### 6) Merge with Tail Builder
Use a dedicated `tail` pointer to build output incrementally.

Invariant:
- Result list is always valid from `dummy.next` to `tail`.
- Remaining nodes are untouched and still connected.

```js
function mergeTwoLists(a, b) {
  const dummy = { val: 0, next: null };
  let tail = dummy;

  while (a && b) {
    if (a.val <= b.val) {
      tail.next = a;
      a = a.next;
    } else {
      tail.next = b;
      b = b.next;
    }
    tail = tail.next;
  }

  tail.next = a || b;
  return dummy.next;
}
```

### 7) Intersection by Pointer Switching
Traverse both lists; when one pointer hits null, switch it to other head.

Invariant:
- Each pointer travels exactly `lenA + lenB`.
- They meet at intersection or both become null.

```js
function getIntersectionNode(headA, headB) {
  let p = headA, q = headB;
  while (p !== q) {
    p = p ? p.next : headB;
    q = q ? q.next : headA;
  }
  return p;
}
```

## Pattern Compositions You Must Know
- Palindrome Linked List:
  - slow-fast to find middle
  - reverse second half
  - compare halves
- Reorder List:
  - split in middle
  - reverse second half
  - weave two lists alternately
- Reverse Nodes in k-Group:
  - find block end
  - reverse block
  - reconnect boundaries

## Safety Rules for Pointer Rewiring
- Save `next` before changing `curr.next`.
- Reconnect boundary nodes immediately after sub-operations.
- Avoid mixing traversal pointer and structural boundary pointer roles.
- In doubly list tasks, update both directions (`next` and `prev`) together.

## Edge Cases That Break Most Solutions
- Empty list.
- Single node.
- Two nodes.
- Entire list deleted.
- `left == right` in sublist reverse.
- `n == list length` in remove-from-end.
- Cycle at head.

## Debugging Checklist
- [ ] Can I traverse final list without infinite loop?
- [ ] Did I return correct head (`dummy.next` when using dummy)?
- [ ] Are all expected nodes still reachable?
- [ ] Did I lose nodes by overwriting `next` too early?
- [ ] Are off-by-one indices for positional problems correct?
- [ ] Did I handle empty and single-node inputs?

## Common Mistakes
- Using `while (fast.next)` before ensuring `fast` is non-null.
- Advancing `prev` during deletion when current node was removed.
- Forgetting to cut list before reversing half in palindrome/reorder.
- Not reconnecting tail after local reversal.
- Returning original `head` after operation that changes head.

## Problem Mapping (High Frequency)
- Reverse Linked List -> full reverse template.
- Reverse Linked List II -> head-insertion sublist reverse.
- Remove Nth Node from End -> fixed-gap + dummy.
- Linked List Cycle / Cycle II -> slow-fast.
- Palindrome Linked List -> slow-fast + reverse + compare.
- Merge Two Sorted Lists -> merge tail builder.
- Partition List -> dual dummy lists + stitching.
- Intersection of Two Linked Lists -> pointer switching.

## Mastery Checklist
- [ ] I can identify the correct pattern in under 30 seconds.
- [ ] I can state one invariant before coding each pattern.
- [ ] I can implement all core templates without reference.
- [ ] I can debug pointer bugs with a repeatable checklist.

## Practice Targets
- Total: 25 pointer-pattern problems.
- Minimum split:
  - 6 slow-fast problems
  - 6 reversal problems
  - 5 dummy/safe-delete problems
  - 4 merge/weave problems
  - 4 mixed composition problems
- Re-solve all failed questions on D+7 and D+30.

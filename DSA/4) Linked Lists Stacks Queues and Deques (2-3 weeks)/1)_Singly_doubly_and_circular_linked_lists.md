# Singly Doubly and Circular Linked Lists

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Build a strong linked-list foundation so insertion, deletion, traversal, and pointer rewiring become reliable under interview pressure.

## Linked List Mental Model
- A linked list is a sequence of nodes stored non-contiguously in memory.
- Each node stores data and pointer references (`next`, and optionally `prev`).
- Access is sequential, not random index based like arrays.
- Performance is dominated by pointer movement, not shifting elements.

## Types at a Glance

| Type | Node Fields | Direction | Best Use Cases | Main Limitation |
|---|---|---|---|---|
| Singly Linked List | `value, next` | Forward only | Simple stacks, streaming append, light memory usage | Cannot move backward directly |
| Doubly Linked List | `value, prev, next` | Forward + backward | Fast delete with node reference, LRU cache, undo/redo | Extra memory and pointer maintenance |
| Circular Linked List | `value, next` (or `prev, next`) with loop | Wrap-around traversal | Round-robin scheduling, cyclic buffers, Josephus-type logic | Easy to create infinite loops by mistake |

## Complexity Cheat Sheet

Assume list stores `head` and `tail` pointers.

| Operation | Singly | Doubly | Circular Singly |
|---|---|---|---|
| Traverse/Search by value | `O(n)` | `O(n)` | `O(n)` |
| Insert at head | `O(1)` | `O(1)` | `O(1)` |
| Insert at tail | `O(1)` (with tail) | `O(1)` | `O(1)` |
| Delete head | `O(1)` | `O(1)` | `O(1)` |
| Delete tail | `O(n)` | `O(1)` | `O(n)` |
| Delete known node pointer | Usually `O(n)` (need prev) | `O(1)` | Usually `O(n)` |
| Extra space per node | Low | Higher | Low |

## Singly Linked List Essentials

### Node structure
- `node.value`
- `node.next`

### Core operations
- Insert at head: point new node to current head, then move head.
- Insert at tail: `tail.next = newNode`, then move tail.
- Delete by value/index: keep `prev` and `curr`, relink `prev.next = curr.next`.
- Reverse list: iterative 3-pointer pattern (`prev`, `curr`, `next`).

### High-value pattern
- Dummy/sentinel node reduces head-edge-case branching in merge/remove problems.

## Doubly Linked List Essentials

### Node structure
- `node.value`
- `node.prev`
- `node.next`

### Why it is powerful
- Deleting a known node is `O(1)` because predecessor is directly available.
- Supports bidirectional traversal.

### Production pattern
- Use dummy head and dummy tail.
- Insert/remove always happen "between two valid nodes."
- This avoids many null checks and is ideal for LRU cache design.

## Circular Linked List Essentials

### Core idea
- Last node points back to first node.
- With `tail` pointer, head is `tail.next`.

### Typical operations
- Insert after tail in `O(1)` and optionally move tail.
- Round-robin traversal continues until pointer returns to start node.

### Loop safety rule
- Never traverse circular list with only `while (curr != null)`.
- Use a `do...while` with stop condition `curr === start`.

## Must-Know Pointer Invariants
- Empty list: `head == null` and `tail == null`.
- Non-empty singly: tail exists and `tail.next == null`.
- Doubly consistency: if `x.next = y`, then `y.prev` must become `x`.
- Circular consistency: `tail.next` must always be `head`.
- After deletion, detached node pointers should be cleared when practical.

## Core Templates (JavaScript)

```js
// Iterative reverse of singly linked list
function reverse(head) {
  let prev = null;
  let curr = head;
  while (curr) {
    const next = curr.next;
    curr.next = prev;
    prev = curr;
    curr = next;
  }
  return prev;
}
```

```js
// Floyd cycle detection: O(n) time, O(1) space
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

## Common Interview Problems from This Topic
- Reverse Linked List.
- Middle of Linked List (slow/fast pointers).
- Detect Cycle / Find Cycle Start.
- Merge Two Sorted Lists.
- Remove Nth Node from End.
- Palindrome Linked List.
- Rotate List / Partition List.
- LRU Cache (HashMap + Doubly Linked List).

## Frequent Mistakes
- Losing part of list by overwriting `next` too early.
- Not updating `tail` when deleting last node.
- Incorrect empty/single-node handling.
- Missing symmetric updates in doubly list (`prev` and `next` both).
- Infinite loops in circular list traversal.

## Debugging Checklist
- [ ] Did I save `next` before pointer rewiring?
- [ ] Did I handle empty list and one-node list?
- [ ] Are `head` and `tail` correct after operation?
- [ ] In doubly list, are both directions valid?
- [ ] In circular list, does traversal terminate at intended node?

## Mastery Checklist
- [ ] I can implement singly, doubly, and circular lists from scratch.
- [ ] I can derive exact complexity for each operation with assumptions.
- [ ] I can solve reversal, cycle, merge, and remove-from-end quickly.
- [ ] I can explain when array is better than linked list and why.

## Practice Targets
- 20 to 25 linked-list problems total.
- At least 8 problems focused on pointer rewiring.
- Re-solve all failed problems on D+7 and D+30.

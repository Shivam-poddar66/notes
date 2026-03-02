# Implementation from Scratch and Test Design

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Build each linear data structure from scratch and validate correctness with a repeatable, high-signal test strategy.

## Why This Module Matters
- Interview success depends on writing bug-free pointer/index code quickly.
- Most failures come from weak testing, not missing algorithm ideas.
- A structured test design process catches edge-case regressions early.

## Structures You Should Implement
- Singly linked list
- Doubly linked list
- Circular linked list or circular queue
- Stack (array-backed)
- Queue (head-index or linked-list)
- Deque (design-level API at minimum)
- LRU cache (hashmap + doubly linked list)

## Implementation Blueprint (Use for Every DS)

### Step 1: Define API First
Write exact method signatures before coding internals.

Example API checklist:
- construction/init
- insert/push/enqueue
- remove/pop/dequeue
- peek/front/back/top
- size/isEmpty
- optional: clear/toArray/debugState

### Step 2: Define Invariants
State 3 to 6 invariants and keep them true after every operation.

Examples:
- Linked list: if empty, `head == null` and `tail == null`.
- Doubly list: for every link `x.next = y`, must have `y.prev = x`.
- Queue head-index: logical size is `arr.length - head`.
- LRU: map size equals count of real DLL nodes.

### Step 3: Write Small Core Helpers
Encapsulate dangerous rewiring/index logic:
- `_remove(node)`
- `_insertAfter(node, prev)`
- `_moveToFront(node)`
- `_growOrCompactArrayIfNeeded()`

### Step 4: Build Public Methods via Helpers
Keep public methods short and composed from tested helpers.

### Step 5: Validate with Unit + Sequence + Edge Tests
Do not rely only on happy-path tests.

## Test Design Framework

### 1) Deterministic Unit Tests
Each method tested independently:
- normal case
- boundary case
- invalid/empty behavior

### 2) Operation Sequence Tests
Run realistic operation mixes:
- many inserts + deletes interleaved
- repeated access patterns (especially for LRU)
- long monotonic push/pop patterns for stack/deque

### 3) Invariant Assertions After Every Step
After each operation, assert structure invariants.

Examples:
- no broken `prev/next` links
- size matches traversed node count
- front/back references are correct

### 4) Differential Testing (Optional but Powerful)
Compare custom structure behavior against trusted reference:
- stack/queue against simple JS array model
- for random operation stream, outputs must match

## Minimal Reusable JS Test Harness

```js
function assert(cond, msg) {
  if (!cond) throw new Error(msg);
}

function runTest(name, fn) {
  try {
    fn();
    console.log("PASS:", name);
  } catch (e) {
    console.log("FAIL:", name, "-", e.message);
  }
}
```

## Example: Queue Test Cases

```js
runTest("queue basic enqueue/dequeue", () => {
  const q = new Queue();
  q.enqueue(10);
  q.enqueue(20);
  assert(q.front() === 10, "front should be 10");
  assert(q.dequeue() === 10, "dequeue 10");
  assert(q.dequeue() === 20, "dequeue 20");
  assert(q.isEmpty(), "queue should be empty");
});

runTest("queue empty dequeue", () => {
  const q = new Queue();
  assert(q.dequeue() === undefined, "empty dequeue should be undefined");
});
```

## Example: Linked List Invariant Checker

```js
function assertDLLIntegrity(head, tail, expectedSize) {
  let count = 0;
  let prev = null;
  let curr = head.next; // if sentinels used

  while (curr !== tail) {
    assert(curr.prev === prev || curr.prev === head || curr.prev.next === curr, "broken prev link");
    prev = curr;
    curr = curr.next;
    count++;
    assert(count <= expectedSize + 2, "possible cycle/corruption");
  }
}
```

## High-Value Edge Case Matrix

| Structure | Critical Edge Cases |
|---|---|
| Singly/Doubly List | empty, one node, delete head, delete tail, delete missing key |
| Circular Structure | single element loop, wrap-around traversal, full vs empty distinction |
| Stack | pop/top on empty, long push-pop sequence |
| Queue | enqueue/dequeue to empty transitions, head index compaction |
| Deque | push/pop both ends with alternating operations |
| LRU | capacity 0, capacity 1, repeated key updates, frequent gets |

## Property Ideas for Randomized Testing
- Sequence equivalence: custom queue output == reference model output.
- Idempotence of no-op behaviors: popping empty does not corrupt state.
- Size consistency: tracked size equals actual traversed/derived size.
- Monotonic conditions remain true for monotonic structures.

## Common Implementation Bugs
- Not updating both `head` and `tail` on empty/non-empty transitions.
- Off-by-one errors in head-index queue.
- Forgotten pointer cleanup causing accidental cycles.
- Inconsistent size tracking after failed operations.
- LRU map and list going out of sync.

## Debugging Playbook
- Log operation sequence with step number.
- Print compact internal state after each step.
- Add assertions inside helpers, not only in tests.
- Reproduce with smallest failing sequence (delta debugging mindset).
- Fix invariant violation at first bad step, not where crash appears.

## Interview Execution Strategy
- Start with API + invariants out loud.
- Implement safe helpers first.
- Manually dry-run 5 to 8 operations.
- Mention test cases proactively before finalizing.

## Mastery Checklist
- [ ] I can implement each Phase 4 structure without reference.
- [ ] I can define invariants before coding.
- [ ] I can design tests that catch pointer/index edge cases.
- [ ] I can debug by shrinking failing operation sequences.
- [ ] I can explain complexity and tradeoffs clearly.

## Practice Targets
- Build 6 structures from scratch end-to-end.
- For each structure:
  - write at least 12 deterministic test cases
  - write at least 1 long mixed-operation sequence test
  - add invariant checks after each operation
- Re-implement full set on D+7 and D+30.

# Common Mistakes and Debugging Checklist

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Reduce avoidable bugs by using a repeatable debugging process for pointer-heavy and index-heavy linear-structure problems.

## Mistake Taxonomy (Most Frequent)

### 1) Pointer Rewiring Errors (Linked List)
Typical mistakes:
- Overwriting `curr.next` before saving `next`.
- Updating only one side in doubly linked list (`next` but not `prev`).
- Losing head/tail during insert/delete near boundaries.
- Returning old head after operations that change head.

Symptoms:
- Missing nodes after operation.
- Infinite loop during traversal.
- Null reference errors after delete/merge.

Fix rule:
- Always write: `next = curr.next` before rewiring.
- Use dummy node when head might change.
- Validate full traversal count after operation.

### 2) Off-by-One and Boundary Mistakes
Typical mistakes:
- Wrong loop bounds in k-distance or sublist ranges.
- Incorrect width formula in histogram/window problems.
- Misinterpreting 0-indexed vs 1-indexed constraints.

Symptoms:
- Correct on most tests but fails edge cases.
- Wrong result only for smallest/largest input sizes.

Fix rule:
- Test `n=0`, `n=1`, `n=2`, `k=1`, `k=n`, and `left==right`.
- Derive boundary formulas on paper before coding.

### 3) Empty/Single-Element State Bugs
Typical mistakes:
- Pop/dequeue without empty check.
- Delete last node but forget to reset both head and tail.
- Queue/deque transitions fail when size becomes 0.

Symptoms:
- Crash on tiny input.
- Stale data returned after structure should be empty.

Fix rule:
- Explicitly handle transitions:
  - empty -> one
  - one -> empty
  - one -> many

### 4) Monotonic Stack/Deque Condition Errors
Typical mistakes:
- Wrong inequality (`<` vs `<=`) with duplicates.
- Storing values when indices are needed for ranges.
- Forgetting final flush pass.

Symptoms:
- Works on distinct values, fails on duplicates.
- Right shape but wrong boundary indices.

Fix rule:
- Define strictness policy before coding.
- Store indices by default for boundary problems.
- Add sentinel or cleanup loop.

### 5) Queue and BFS Process Errors
Typical mistakes:
- Marking visited on dequeue instead of enqueue.
- Not freezing queue size for level-order traversal.
- Using `shift` heavily and causing hidden `O(n^2)`.

Symptoms:
- Duplicate work, TLE, or incorrect level counts.
- Dramatic slowdown on large input.

Fix rule:
- Mark visited when enqueuing.
- Capture `levelSize = queue.size()` before level loop.
- Use head-index queue implementation in JS.

### 6) Cache Design Sync Bugs (LRU/LFU)
Typical mistakes:
- Map and linked list out of sync after eviction/update.
- Not moving accessed node to MRU side.
- Evicting wrong node direction.

Symptoms:
- Random wrong answers after long operation sequence.
- Size mismatch between map and list traversal.

Fix rule:
- Assert `map.size == listNodeCount`.
- Keep `remove`/`insertFront` helper methods centralized.

## Symptom -> Root Cause Quick Map

| Symptom | Probable Root Cause |
|---|---|
| Infinite loop in list traversal | cycle introduced accidentally or pointer not advanced |
| Missing nodes after reverse/delete | `next` lost before rewiring |
| Correct on random tests, fails duplicates | monotonic strict/non-strict mismatch |
| TLE in queue/window problem | frequent `shift` or repeated rescans |
| Wrong nth-from-end result | gap setup off-by-one |
| LRU wrong eviction | MRU/LRU side confusion |

## Universal Debugging Procedure
1. Reproduce with smallest failing input.
2. Print state after each operation:
  - pointers (`prev/curr/next`)
  - stack/deque contents (indices + values)
  - queue head/tail indices
3. Check invariants after each step.
4. Locate first invariant break.
5. Fix root step, then rerun full edge suite.

## Invariant Checklist by Structure

### Linked List
- [ ] If empty, `head == null` and `tail == null`.
- [ ] If non-empty singly, `tail.next == null`.
- [ ] Doubly links are symmetric.
- [ ] Traversal count matches tracked size.

### Stack
- [ ] Top reflects last pushed non-popped element.
- [ ] Pop/top on empty follows defined behavior.

### Queue
- [ ] Front is oldest non-removed element.
- [ ] Size updates correctly on enqueue/dequeue.
- [ ] Empty/full transitions are correct.

### Monotonic Stack/Deque
- [ ] Container order remains monotonic.
- [ ] Out-of-range indices are evicted first.
- [ ] Each index pushed and popped at most once.

### LRU Cache
- [ ] Every map node exists in DLL exactly once.
- [ ] `head.next` is MRU and `tail.prev` is LRU.
- [ ] On overflow, only LRU is evicted.

## Pre-Submit Dry-Run Set (Mandatory)
- Case 1: empty input.
- Case 2: single element.
- Case 3: two elements.
- Case 4: all equal values.
- Case 5: strictly increasing values.
- Case 6: strictly decreasing values.
- Case 7: extreme boundary parameters (`k=1`, `k=n`, `capacity=1`, `capacity=0`).

## Logging Tips During Debugging
- Log concise structured lines:
  - `step`, `op`, `state`, `result`
- For linked lists, log first 10 nodes only to avoid infinite-loop flood.
- For monotonic structures, log both indices and mapped values.

Example log line:
```text
step=7 op=pop idx=4 val=2 stack=[1,3] ans[4]=6
```

## Common Interview Recovery Strategy
If stuck mid-coding:
1. Pause and state invariant aloud.
2. Rebuild using dummy/sentinel or helper functions.
3. Run one short manual trace.
4. Confirm complexity and edge handling.

## Personal Mistake Tracker Template

```text
Date:
Problem:
Category:
Bug Trigger:
Invariant Broken:
Minimal Failing Case:
Fix Rule:
Re-solve on:
```

## Mastery Checklist
- [ ] I can classify my bug into one of the 6 categories quickly.
- [ ] I can isolate first failing step instead of patching late symptoms.
- [ ] I can enforce invariant checks while coding.
- [ ] I can avoid my top 5 recurring mistakes consistently.
- [ ] I can debug under interview time pressure.

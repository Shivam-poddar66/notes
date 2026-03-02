# Queue and Deque Design Patterns

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Master FIFO queue and deque patterns so you can solve scheduling, window, and BFS-style problems in linear time.

## Mental Model
- Queue is FIFO (first in, first out):
  - `enqueue` at back
  - `dequeue` from front
- Deque supports both ends:
  - push/pop front
  - push/pop back
- Choose deque when you need efficient front and back updates together.

## Queue vs Deque vs Stack

| Structure | Order | Fast Ends | Common Use |
|---|---|---|---|
| Stack | LIFO | One end | Parsing, monotonic stack, undo |
| Queue | FIFO | Back insert + front remove | BFS, task scheduling |
| Deque | Flexible | Both ends | Sliding window max/min, 0-1 BFS |

## Complexity Cheat Sheet

| Operation | Queue | Deque |
|---|---|---|
| Push back | `O(1)` | `O(1)` |
| Pop front | `O(1)` | `O(1)` |
| Push front | N/A | `O(1)` |
| Pop back | N/A | `O(1)` |
| Peek front/back | `O(1)` | `O(1)` |

JavaScript caution:
- Native array `shift`/`unshift` are `O(n)`.
- For heavy operations, use:
  - linked-list queue, or
  - array + head index trick.

## Queue Implementations

### 1) Queue with Array + Head Pointer (Practical in JS)
```js
class Queue {
  constructor() {
    this.a = [];
    this.head = 0;
  }

  enqueue(x) {
    this.a.push(x);
  }

  dequeue() {
    if (this.head >= this.a.length) return undefined;
    const val = this.a[this.head++];

    // Optional cleanup to avoid unbounded memory growth
    if (this.head > 1024 && this.head * 2 > this.a.length) {
      this.a = this.a.slice(this.head);
      this.head = 0;
    }
    return val;
  }

  front() {
    return this.head < this.a.length ? this.a[this.head] : undefined;
  }

  isEmpty() {
    return this.head >= this.a.length;
  }

  size() {
    return this.a.length - this.head;
  }
}
```

### 2) Circular Queue (Fixed Capacity)
Use array ring buffer for guaranteed `O(1)` and bounded memory.

```js
class CircularQueue {
  constructor(k) {
    this.q = Array(k);
    this.k = k;
    this.head = 0;
    this.tail = 0;
    this.count = 0;
  }

  enQueue(x) {
    if (this.isFull()) return false;
    this.q[this.tail] = x;
    this.tail = (this.tail + 1) % this.k;
    this.count++;
    return true;
  }

  deQueue() {
    if (this.isEmpty()) return false;
    this.head = (this.head + 1) % this.k;
    this.count--;
    return true;
  }

  Front() {
    return this.isEmpty() ? -1 : this.q[this.head];
  }

  Rear() {
    return this.isEmpty() ? -1 : this.q[(this.tail - 1 + this.k) % this.k];
  }

  isEmpty() { return this.count === 0; }
  isFull() { return this.count === this.k; }
}
```

## Deque Pattern Families

### 1) Monotonic Deque for Sliding Window Max/Min
Invariant for max window:
- Deque stores indices.
- Values are in decreasing order from front to back.
- Front index is always inside current window.

Each index is pushed once and popped at most once -> total `O(n)`.

```js
function maxSlidingWindow(nums, k) {
  const dq = []; // indices, values decreasing
  const ans = [];

  for (let i = 0; i < nums.length; i++) {
    while (dq.length && dq[0] <= i - k) dq.shift();
    while (dq.length && nums[dq[dq.length - 1]] <= nums[i]) dq.pop();
    dq.push(i);
    if (i >= k - 1) ans.push(nums[dq[0]]);
  }

  return ans;
}
```

### 2) 0-1 BFS Deque Pattern (Advanced)
For graph edges weighted only `0` or `1`:
- Weight `0` edge -> push front
- Weight `1` edge -> push back
- Computes shortest path in `O(V + E)`

## Queue Pattern Families

### 1) Level-Order / BFS Queue
Invariant:
- Queue holds frontier nodes in processing order.
- Process one level at a time using `size = queue.size()` pattern.

Typical uses:
- Binary tree level order.
- Shortest path in unweighted graph.
- Multi-source BFS (all sources enqueued first).

### 2) Scheduling / Cooldown Simulation
Use queue to model:
- task arrival times
- cooldown expiry
- round-robin behavior

### 3) Topological Sort (Kahn's Algorithm)
Queue stores nodes with indegree `0`.
Repeatedly pop, reduce neighbors, enqueue newly zero indegree nodes.

## Classic Queue/Deque Problems
- Implement Queue using Stacks.
- Design Circular Queue / Circular Deque.
- Number of Recent Calls.
- Moving Average from Data Stream.
- Sliding Window Maximum.
- Shortest Subarray with Sum at Least K (monotonic deque on prefix sums).
- Rotting Oranges / Minimum steps BFS grid.
- Course Schedule II (topological order).

## Common Mistakes
- Using `shift` in tight loops and getting hidden `O(n^2)` behavior.
- Storing values instead of indices in window problems.
- Forgetting to evict out-of-window indices first.
- Wrong inequality for monotonic deque with duplicates.
- Mixing queue size during level-order traversal without freezing level size.

## Debugging Checklist
- [ ] Is operation order correct: evict old, maintain monotonicity, push current, read answer?
- [ ] For BFS, did I mark visited at enqueue time (not dequeue time)?
- [ ] For level traversal, did I snapshot current queue size first?
- [ ] Are circular queue wrap-around formulas correct?
- [ ] Did I handle empty/full queue transitions safely?

## Problem-to-Pattern Mapping
- Sliding Window Maximum -> monotonic deque.
- First negative in each window -> deque of candidate indices.
- Shortest path unweighted -> BFS queue.
- Multi-source shortest spread -> BFS queue initialized with all sources.
- Course scheduling order -> Kahn queue.
- 0/1 edge shortest path -> deque 0-1 BFS.

## Mastery Checklist
- [ ] I can implement queue and deque from scratch without `shift`.
- [ ] I can derive and maintain monotonic deque invariants.
- [ ] I can identify BFS-level and topological queue patterns quickly.
- [ ] I can explain why monotonic deque solutions are linear.
- [ ] I can test boundary cases: empty input, `k=1`, `k=n`, duplicates.

## Practice Targets
- Total: 25 queue/deque-focused problems.
- Minimum split:
  - 8 BFS/level-order problems
  - 8 sliding-window deque problems
  - 4 circular queue/deque design problems
  - 5 mixed medium/hard scheduling or graph-queue problems
- Re-solve all failed problems on D+7 and D+30.

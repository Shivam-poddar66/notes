# Designing LRU Cache with HashMap and Doubly Linked List

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Implement LRU cache with `O(1)` average `get` and `put` using a hashmap plus doubly linked list.

## Problem Definition
Design a cache with fixed `capacity`:
- `get(key)`:
  - return value if key exists, else `-1`
  - accessed key becomes most recently used
- `put(key, value)`:
  - insert/update key-value
  - inserted/updated key becomes most recently used
  - if size exceeds capacity, evict least recently used key

## Why One Structure Is Not Enough
- HashMap alone:
  - `O(1)` lookup/update by key
  - cannot evict least recently used in `O(1)`
- Doubly linked list alone:
  - can move/remove nodes in `O(1)` if node pointer is known
  - cannot find node by key in `O(1)`
- Combined:
  - Map gives node pointer by key
  - DLL maintains usage order

## Standard Architecture
- HashMap: `key -> node`
- Doubly linked list:
  - front (right after head sentinel) = most recently used (MRU)
  - back (right before tail sentinel) = least recently used (LRU)
- Sentinel nodes (`head`, `tail`) remove null-edge branching.

## Invariants (Must Always Hold)
- Every key in map points to exactly one live node in DLL.
- DLL order is recency order: MRU near head, LRU near tail.
- `size === map.size`.
- Sentinel links are always valid:
  - `head.next.prev === head`
  - `tail.prev.next === tail`

## Operation Flows

### `get(key)`
1. If key not in map, return `-1`.
2. Get node from map.
3. Move node to front (MRU position).
4. Return node value.

### `put(key, value)`
1. If key exists:
  - update value
  - move node to front
2. Else:
  - create node
  - insert at front
  - add to map
3. If size exceeds capacity:
  - remove node at back (LRU)
  - delete its key from map

## Complexity

| Operation | Time | Space |
|---|---|---|
| `get` | `O(1)` average |  |
| `put` | `O(1)` average |  |
| Total |  | `O(capacity)` |

Map operations are average-case `O(1)` for standard hash table assumptions.

## JavaScript Implementation Template

```js
class Node {
  constructor(key, val) {
    this.key = key;
    this.val = val;
    this.prev = null;
    this.next = null;
  }
}

class LRUCache {
  constructor(capacity) {
    this.capacity = capacity;
    this.map = new Map();

    this.head = new Node(-1, -1); // sentinel head (MRU side)
    this.tail = new Node(-1, -1); // sentinel tail (LRU side)
    this.head.next = this.tail;
    this.tail.prev = this.head;
  }

  // Remove a node from current position
  _remove(node) {
    node.prev.next = node.next;
    node.next.prev = node.prev;
  }

  // Insert node right after head => MRU
  _insertFront(node) {
    node.next = this.head.next;
    node.prev = this.head;
    this.head.next.prev = node;
    this.head.next = node;
  }

  // Move existing node to MRU position
  _moveToFront(node) {
    this._remove(node);
    this._insertFront(node);
  }

  // Remove LRU node (node before tail)
  _evictLRU() {
    const lru = this.tail.prev;
    this._remove(lru);
    this.map.delete(lru.key);
  }

  get(key) {
    if (!this.map.has(key)) return -1;

    const node = this.map.get(key);
    this._moveToFront(node);
    return node.val;
  }

  put(key, value) {
    if (this.capacity <= 0) return;

    if (this.map.has(key)) {
      const node = this.map.get(key);
      node.val = value;
      this._moveToFront(node);
      return;
    }

    const node = new Node(key, value);
    this.map.set(key, node);
    this._insertFront(node);

    if (this.map.size > this.capacity) {
      this._evictLRU();
    }
  }
}
```

## Dry Run Example
Capacity = 2
- `put(1,1)` -> [1]
- `put(2,2)` -> [2,1] (2 is MRU)
- `get(1)` -> returns 1, order [1,2]
- `put(3,3)` -> evict 2, order [3,1]
- `get(2)` -> returns -1

## Design Variants
- Timestamp + min-heap + map:
  - easier conceptually for some
  - but eviction/update not strict `O(1)`
- Ordered dict / linked hash map library:
  - concise in languages with built-in ordered hash maps
  - interview often expects explicit hashmap + DLL design

## Common Mistakes
- Forgetting to move node to MRU on `get`.
- Updating value in `put` but not moving node to MRU.
- Evicting wrong side of list.
- Deleting DLL node but forgetting map deletion.
- Pointer corruption from wrong unlink/relink sequence.
- Not handling capacity `0`.

## Debugging Checklist
- [ ] After every operation, do `head` and `tail` links remain valid?
- [ ] Is map size equal to number of real DLL nodes?
- [ ] On `get` hit, does node move to front?
- [ ] On overflow, is tail-prev node evicted?
- [ ] Are duplicate keys updated instead of duplicated?

## Interview Explanation Script
- "I use a hashmap for `O(1)` key lookup and a doubly linked list for `O(1)` recency updates."
- "`get` and `put` both move touched nodes to the front (MRU)."
- "When capacity is exceeded, I remove tail-prev, which is LRU, and delete it from map."
- "Sentinel head/tail nodes simplify edge cases and keep operations constant time."

## Mastery Checklist
- [ ] I can derive this architecture without memorized code.
- [ ] I can implement DLL helpers (`remove`, `insertFront`) correctly.
- [ ] I can prove `get` and `put` are `O(1)` average.
- [ ] I can explain recency order and eviction direction clearly.
- [ ] I can handle edge cases: capacity `0`, repeated keys, single capacity.

## Practice Targets
- Implement LRU cache from scratch 3 times without reference.
- Add tests for:
  - overwrite existing key
  - frequent `get` on same key
  - repeated evictions
  - capacity `1` and `0`
- Re-implement on D+7 and D+30 for retention.

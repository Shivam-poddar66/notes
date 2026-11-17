# 3) Cache Eviction Policies: LRU, LFU, and TTL

## Executive Overview
RAM is expensive and finite. When a cache reaches its memory limit (`maxmemory` in Redis), an eviction algorithm must determine which keys to prune to free space for new items.

---

## 1. Eviction Algorithms Breakdown

### A. LRU (Least Recently Used)
- Evicts keys that have not been read or written for the longest time.
- **Classic Data Structure**: **Hash Map + Doubly Linked List**.
  - `Map<Key, Node>` provides $O(1)$ key lookup.
  - Doubly Linked List keeps nodes ordered by recency.
  - When a key is accessed: Detach node from current position and move to the **Head** in $O(1)$.
  - When memory is full: Delete the node at the **Tail** in $O(1)$.

```text
[ Head (Most Recent) ] ◄──▶ [ Node A ] ◄──▶ [ Node B ] ◄──▶ [ Tail (Evicted First) ]
```

- **Redis Approximated LRU**:
  - A real doubly linked list requires 24 bytes of pointer overhead per key.
  - Instead, Redis samples 5 random keys (configurable via `maxmemory-samples`) and evicts the one with the oldest idle time. This achieves ~99% accuracy of true LRU while saving gigabytes of memory.

### B. LFU (Least Frequently Used)
- Tracks a counter of how often each key is accessed.
- Protects persistently popular keys from being prematurely evicted by a temporary burst of new keys.
- **Logarithmic Decay**: Redis uses an 8-bit logarithmic counter that increments probabilistically and decays over time to prevent old hot keys from staying in memory forever.

### C. Volatile vs. Allkeys Eviction in Redis
- `noeviction`: Returns errors when memory limit is reached (default).
- `allkeys-lru`: Evicts least recently used keys across all keys.
- `volatile-lru`: Evicts least recently used keys only among keys with an explicit `EXPIRE` set.
- `volatile-ttl`: Evicts keys with the shortest remaining Time-To-Live.

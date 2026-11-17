# 8) Chapter 3 Summary & Self-Assessment Checklist

## Chapter Summary
1. **Multi-Tier Caching**: Caching spans from browser HTTP headers and CDN edges down to Redis and database buffer pools, reducing request latencies from milliseconds to microseconds.
2. **Access Patterns**: Cache-Aside is the default for general web APIs. When DB data changes, **delete** the cache key rather than updating it to avoid concurrent race conditions.
3. **Eviction Mechanics**: LRU uses a Doubly Linked List + Hash Map to prune stale keys in $O(1)$. Redis optimizes this using approximated sampling to save memory.
4. **Cache Disasters**:
   - Cache Stampede $\to$ Mutex locks (`SETNX`) or XFetch early expiration.
   - Cache Penetration $\to$ Bloom filters or caching null objects.
   - Cache Avalanche $\to$ Random TTL jittering.
5. **Redis Architecture**: High throughput is achieved through single-threaded command execution over non-blocking I/O multiplexing (`epoll`).
6. **Distributed Locks**: Implemented safely via `SET NX PX` and released using atomic Lua scripts verifying holder UUIDs.

---

## 🎯 Chapter 3 Self-Assessment Checklist

- [ ] Explain why deleting a cache key on DB update is safer than updating the cache key.
- [ ] What is the internal data structure used to implement true $O(1)$ LRU cache eviction?
- [ ] How does Redis Approximated LRU differ from standard LRU, and why does it save memory?
- [ ] Detail the failure chain of a Cache Stampede and describe two production defenses.
- [ ] What are the two core guarantees of a Bloom Filter regarding false positives and false negatives?
- [ ] How does TTL jitter prevent a Cache Avalanche?
- [ ] Why is Redis single-threaded for execution, and what OS syscalls enable its I/O multiplexing?
- [ ] What is the underlying data structure of a Redis Sorted Set (ZSET)?
- [ ] What is the formula used by Redis Cluster to assign keys to 16,384 hash slots?
- [ ] Why is `DEL lock_key` dangerous when releasing a distributed lock without a Lua script?

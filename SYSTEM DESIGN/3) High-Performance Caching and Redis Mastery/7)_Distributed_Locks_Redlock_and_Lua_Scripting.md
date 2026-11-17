# 7) Distributed Locks, Redlock, and Lua Scripting

## Executive Overview
When multiple distributed microservice instances need to coordinate access to a shared resource (e.g., reserving the last seat on a flight, preventing double withdrawal), an in-memory distributed lock in Redis provides sub-millisecond mutual exclusion.

---

## 1. Single-Instance Distributed Lock Pattern

### Acquiring the Lock:
```bash
SET lock:order_99 "unique_token_worker_A" NX PX 10000
```
- `NX`: Set only if Not eXists (ensures mutual exclusion).
- `PX 10000`: Expire lock after 10,000 ms (prevents permanent deadlocks if Worker A crashes).
- `unique_token`: A random UUID identifying the holder.

### Releasing the Lock (Must be Atomic):
Never run `DEL lock:order_99` blindly! If Worker A takes 11 seconds (longer than TTL), the lock expires and Worker B acquires it. If Worker A then calls `DEL`, it accidentally destroys Worker B's lock!

```lua
-- Lua script executed atomically in Redis
if redis.call("get", KEYS[1]) == ARGV[1] then
    return redis.call("del", KEYS[1])
else
    return 0
end
```

---

## 2. Redlock: Distributed Lock across Multiple Masters
Designed by Salvatore Sanfilippo (Redis creator) for fault-tolerant locking without depending on asynchronous replication failover.

1. Deploy **5 independent Redis master nodes** (no replication).
2. Client gets current timestamp $T_1$.
3. Tries to acquire lock on all 5 nodes sequentially with a small timeout (e.g., 5–50 ms).
4. Client considers lock **acquired** if:
   - Acquired on at least $\lfloor 5/2 \rfloor + 1 = \mathbf{3\text{ nodes (Majority Quorum)}}$, AND
   - Total time elapsed $T_2 - T_1 < \text{Lock Validity Time}$.
5. If failed: Client immediately unlocks all nodes.

---

## 3. Atomic Multi-Operation via Redis Lua Scripting
Because Redis executes Lua scripts as a single atomic unit, no other Redis command can interleave during script execution.

### Example: Sliding Window Rate Limiter in Lua
```lua
-- KEYS[1]: User rate limit key (e.g. rate:user_42)
-- ARGV[1]: Current timestamp in ms
-- ARGV[2]: Window size in ms (e.g. 60000)
-- ARGV[3]: Max allowed requests (e.g. 100)

local key = KEYS[1]
local now = tonumber(ARGV[1])
local window = tonumber(ARGV[2])
local limit = tonumber(ARGV[3])
local clear_before = now - window

-- 1. Remove old timestamps outside current sliding window
redis.call('ZREMRANGEBYSCORE', key, 0, clear_before)

-- 2. Count remaining requests in current window
local current_requests = redis.call('ZCARD', key)

if current_requests < limit then
    -- 3. Add current request timestamp
    redis.call('ZADD', key, now, now)
    redis.call('PEXPIRE', key, window)
    return 1 -- Allowed
else
    return 0 -- Rejected (HTTP 429)
end
```

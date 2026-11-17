# 3) Case Study 2: Distributed Rate Limiter

## 1. Requirements & Scope
- **Functional**: Limit client API requests based on User ID or IP address (e.g., 100 requests per minute); Return standard HTTP 429 Too Many Requests with `Retry-After` header when exceeded.
- **Non-Functional**: Ultra-low latency overhead (< 2 ms per API call); High availability; Distributed across cluster without race conditions.

---

## 2. Architecture: Edge vs. Gateway Placement

```text
[ Client Request ]
       │
       ▼
[ Cloudflare / AWS WAF ] ──▶ Global volumetric IP rate limiting (DDoS defense)
       │
       ▼
[ Envoy / Kong API Gateway ]
       │
       ├── Calls Rate Limiting Filter Plugin (< 1 ms)
       │         │
       │         ▼ (Atomic Lua Script)
       │   [ Redis Cluster ]
       │
       ├── Allowed ──▶ Forward to [ Backend Microservices ]
       │
       └── Denied  ──▶ Return HTTP 429 "Too Many Requests"
```

---

## 3. Atomic Redis Sliding Window Implementation

Because checking a limit and incrementing a counter involves multiple steps, running individual Redis commands (`GET` followed by `INCR`) causes severe **concurrency race conditions**.

### Atomic Lua Script:
```lua
-- KEYS[1]: rate_limit:{user_id}
-- ARGV[1]: Current UNIX timestamp in ms
-- ARGV[2]: Window size in ms (e.g., 60,000 for 1 min)
-- ARGV[3]: Max allowed requests (e.g., 100)

local key = KEYS[1]
local now = tonumber(ARGV[1])
local window = tonumber(ARGV[2])
local max_limit = tonumber(ARGV[3])
local clear_before = now - window

-- 1. Remove expired timestamps outside sliding window
redis.call('ZREMRANGEBYSCORE', key, 0, clear_before)

-- 2. Count requests currently in sliding window
local current_requests = redis.call('ZCARD', key)

if current_requests < max_limit then
    -- 3. Add current timestamp to Sorted Set (ZSET)
    redis.call('ZADD', key, now, now)
    redis.call('PEXPIRE', key, window)
    return {1, max_limit - current_requests - 1} -- Allowed, remaining quota
else
    return {0, 0} -- Denied (HTTP 429)
end
```

### Rate Limiter Response Headers (RFC 6585 Standard):
```http
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1773000060
Retry-After: 42
Content-Type: application/json

{ "error": "API rate limit exceeded. Please retry in 42 seconds." }
```

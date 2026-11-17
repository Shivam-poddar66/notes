# 6) Case Study 5: High-Concurrency Flash Sale Reservation

## 1. The Challenge & Engineering Hazards
- **Scenario**: 10,000 units of a limited-edition sneaker or iPhone go on sale at 12:00:00 PM. 500,000 users click "Buy Now" in the first 5 seconds.
- **Hazard 1: Database Locking Collapse**:
  - If 500,000 requests execute:
    `UPDATE inventory SET stock = stock - 1 WHERE item_id = 1;`
  - The database row locks, creating a 500,000-deep queue of blocked connection threads. Memory and CPU spike to 100%, and the database crashes within 3 seconds.
- **Hazard 2: Overselling (Race Condition)**:
  - If locking is absent, multiple threads read `stock = 1` concurrently, both decrement, and stock drops to negative numbers (oversold).

---

## 2. Multi-Layer Defensive Architecture

```text
[ 500,000 Concurrent Users ]
             │
             ▼
      [ Cloudflare CDN / WAF ]
      - Rate limiting: Max 1 request per 3 seconds per IP
      - Static assets and product details served 100% from Edge
             │
             ▼
      [ API Gateway (Envoy) ]
      - User Token / CAPTCHA Verification
             │
             ▼
[ Redis Cluster (In-Memory Inventory Pre-Allocation) ]
             │
             │ Executes Atomic Lua Script:
             │ Check stock > 0 and decrement atomically in RAM!
             │
             ├── IF Stock == 0 (Sold Out):
             │   └── Return Immediate HTTP 200 "Sold Out" (< 3 ms)
             │       (Eliminates 490,000 requests from ever touching backend!)
             │
             └── IF Stock > 0 (Success - Reservation Granted):
                     │
                     ▼
           [ Kafka Order Queue ] (Buffers 10,000 winning order events)
                     │
                     ▼
           [ Order Worker Pool ] (Consumes at steady 500 orders/sec)
                     │
                     ▼
           [ MySQL Database (Optimistic Concurrency Control) ]
           UPDATE inventory 
           SET stock = stock - 1, version = version + 1 
           WHERE item_id = 1 AND stock > 0 AND version = :v;
```

---

## 3. Atomic Redis Lua Script for Pre-Allocation

```lua
-- KEYS[1]: stock:item_101
-- KEYS[2]: buyers:item_101
-- ARGV[1]: user_id

-- 1. Check if user already purchased (Prevent double purchase)
if redis.call('SISMEMBER', KEYS[2], ARGV[1]) == 1 then
    return -1 -- Already purchased
end

-- 2. Check remaining stock
local stock = tonumber(redis.call('GET', KEYS[1]) or '0')

if stock > 0 then
    -- 3. Decrement stock and record buyer atomically
    redis.call('DECRBY', KEYS[1], 1)
    redis.call('SADD', KEYS[2], ARGV[1])
    return 1 -- Allocation Success!
else
    return 0 -- Sold Out!
end
```

### Key Architectural Takeaway:
By handling inventory deduction in **Redis RAM via atomic Lua scripts**, 490,000 losing requests receive an instant response in under 3 ms, and **only exactly 10,000 successful orders** are ever queued to Kafka and written to the persistent database.

# 4) The Three Cache Disasters: Stampede, Penetration, Avalanche

## Executive Overview
When caching systems fail, the blast radius directly impacts the persistent database tier. Three catastrophic failure modes account for over 90% of production database crashes behind caches.

---

## 1. Disaster 1: Cache Stampede (Thundering Herd)

### The Failure Mode:
A high-traffic key (e.g., product catalog page with 50,000 QPS) expires in Redis. At that millisecond, thousands of concurrent requests all get a cache miss and query the database simultaneously to rebuild the cache, exhausting database CPU and connection pools.

```text
Time = 0s: [ Hot Key Expires in Redis ]
Time = 0.01s: 5,000 concurrent client threads miss cache
               ├── All 5,000 threads query DB simultaneously!
               └── Database CPU hits 100%, connection pool exhausted, DB crashes!
```

### Production Defenses:
1. **Distributed Mutex Lock (`SETNX`)**:
   - Only the first thread that misses acquires a short-lived lock (`SET lock:key "1" NX PX 5000`).
   - Only the lock owner queries the database and populates Redis.
   - All other threads sleep for 50 ms and retry reading the cache.
2. **Probabilistic Early Expiration (XFetch Algorithm)**:
   - Compute a formula before actual key expiration:
     $$-\beta \times \Delta \times \ln(\text{random}()) > \text{remaining TTL}$$
   - Where $\Delta$ is computation time and $\beta > 0$. If true, a background worker proactively refreshes the key before it expires.
3. **Never-Expire Keys with Async Background Refresh**:
   - Store the key with no TTL; use a Kafka event or cron job to refresh it in the background.

---

## 2. Disaster 2: Cache Penetration

### The Failure Mode:
Clients (either buggy or malicious attackers) request IDs that do not exist in the database (e.g., `id = -999999` or random UUIDs). Because the records do not exist, the database returns `null`, nothing is cached, and every request penetrates straight to the database.

### Production Defenses:
1. **Cache Null Values**:
   - When the DB returns `null`, store `key -> NULL` in Redis with a short TTL (e.g., 60 seconds).
2. **Bloom Filter (Primary Defense)**:
   - Place a Bloom filter at the API gateway / cache layer containing all valid IDs.
   - **Properties of Bloom Filter**:
     - False Positives: Possible (may say an invalid ID exists).
     - False Negatives: **IMPOSSIBLE** (if it says an ID does NOT exist, it definitely does not exist).
   - If the Bloom filter returns `false`, reject the request immediately with HTTP 404 without querying Redis or the DB.

---

## 3. Disaster 3: Cache Avalanche

### The Failure Mode:
A massive batch of cached items is inserted with the identical expiration time (e.g., a batch job runs at 00:00 setting a 2-hour TTL). At 02:00:00, hundreds of thousands of keys expire simultaneously, shifting all traffic to the database at once.

### Production Defenses:
1. **Add Random TTL Jitter**:
   - Never set a fixed TTL for batch items:
   ```python
   base_ttl = 3600  # 1 hour
   jitter = random.randint(0, 300)  # Random 0 to 5 minutes
   redis.set(key, value, ex=base_ttl + jitter)
   ```
2. **Multi-Level Caching**:
   - Use a local application heap cache (Caffeine) in front of Redis with independent TTLs.
3. **Rate Limiting & Circuit Breakers**:
   - Trip circuit breakers to protect the DB from being overwhelmed during cache warming.

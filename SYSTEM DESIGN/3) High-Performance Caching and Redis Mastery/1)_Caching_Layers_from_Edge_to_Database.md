# 1) Caching Layers from Edge to Database

## Executive Overview
Caching is the practice of storing precomputed or frequently queried data in fast, transient storage (RAM) to minimize latency and relieve pressure on downstream databases and storage engines.

---

## 1. The Multi-Tier Caching Stack

```text
┌────────────────────────────────────────────────────────┐
│ 1. Client / Browser Cache                              │
│    - HTTP Headers: Cache-Control, ETag, Last-Modified  │
│    - Latency: 0 ms (In-device memory / local disk)     │
├────────────────────────────────────────────────────────┤
│ 2. CDN (Content Delivery Network - Edge)               │
│    - Cloudflare, Fastly, AWS CloudFront                │
│    - Latency: 10 – 30 ms (Geographically close PoP)    │
├────────────────────────────────────────────────────────┤
│ 3. API Gateway / Reverse Proxy Micro-Cache             │
│    - NGINX fastcgi_cache, Envoy, Kong                  │
│    - Latency: 1 – 2 ms                                 │
├────────────────────────────────────────────────────────┤
│ 4. Application Process In-Memory Cache (Heap)          │
│    - Caffeine (Java), Guava, Node.js LRU               │
│    - Latency: < 1 µs (Direct pointer reference)        │
├────────────────────────────────────────────────────────┤
│ 5. Distributed Cache Tier                              │
│    - Redis Cluster, Memcached                          │
│    - Latency: 500 µs – 2 ms (Network round trip)       │
├────────────────────────────────────────────────────────┤
│ 6. Database Engine Buffer Pool                         │
│    - MySQL InnoDB Buffer Pool, Postgres Shared Buffers │
│    - Latency: 5 – 20 µs (Server RAM)                   │
├────────────────────────────────────────────────────────┤
│ 7. Persistent Disk Storage                             │
│    - NVMe SSD / Hard Disk tablespaces                  │
│    - Latency: 100 µs – 10 ms                           │
└────────────────────────────────────────────────────────┘
```

---

## 2. In-Memory (Heap) vs. Distributed Cache Trade-offs

| Dimension | Application Heap Cache (e.g. Caffeine) | Distributed Cache (e.g. Redis) |
| :--- | :--- | :--- |
| **Speed** | Sub-microsecond (< 1 µs; zero network) | ~1 millisecond (incurs TCP network hop) |
| **Data Consistency**| Inconsistent: Node A might have stale key while Node B has fresh key | Globally consistent: All app nodes share the same Redis cluster |
| **Memory Limit** | Limited by process heap size and JVM/V8 GC pauses | Scalable to hundreds of gigabytes/terabytes across cluster |
| **Survives Restarts**| No: Cache disappears on process restart/deploy | Yes: Persisted in Redis memory independently of deployments |

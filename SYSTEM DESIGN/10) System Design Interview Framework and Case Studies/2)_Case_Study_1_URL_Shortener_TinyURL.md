# 2) Case Study 1: URL Shortener (TinyURL / Bitly)

## 1. Requirements & Scope
- **Functional**: Shorten a long URL to a unique short alias; Redirect short link (`tiny.com/xyz123`) to original destination with HTTP 301/302; Custom alias support; Expiring links.
- **Scale**: 100 Million URLs created/month; 10:1 read-to-write ratio $\implies$ 1 Billion redirects/month.
- **Calculations**:
  - Write QPS: $\frac{100\text{M}}{86400 \times 30} \approx \mathbf{40\text{ writes/sec}}$.
  - Read QPS: $\mathbf{400\text{ reads/sec}}$ (Peak ~1,000 QPS).
  - Storage: $100\text{M} \times 500\text{ Bytes} = 50\text{ GB/month} \implies \mathbf{3\text{ TB in 5 years}}$.

---

## 2. Core Technical Mechanics: Base62 Encoding vs. Hashing

Characters used: `[0-9, a-z, A-Z]` ($10 + 26 + 26 = 62\text{ characters}$).
- 7-character string provides:
  $$62^7 \approx \mathbf{3.52\text{ Trillion unique URLs}}$$

### Why NOT `MD5(long_url)`?
- MD5 yields 128 bits. Truncating to 7 characters produces high collision rates requiring slow database collision lookup loops.

### The Production Solution: Snowflake ID / KGS + Base62
Convert a unique 64-bit auto-incrementing ID into Base62:
$$\text{ID } 11,157 \implies \text{Base62: } '2TX'$$

```python
BASE62 = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"

def encode_base62(num: int) -> str:
    if num == 0:
        return BASE62[0]
    result = []
    while num > 0:
        num, rem = divmod(num, 62)
        result.append(BASE62[rem])
    return "".join(reversed(result))
```

---

## 3. High-Level Architecture Diagram

```text
[ User Client ]
       │
   1. GET /aB3x9Zq
       ▼
 [ Cloudflare CDN Edge ] ── Cache Hit: Return 301 ──▶ [ Destination URL ]
       │
   Cache Miss
       ▼
 [ Layer 7 Load Balancer ]
       │
 [ URL Shortener App Cluster ]
       │
       ├── 2. Check Redis Cache (`GET url:aB3x9Zq`)
       │      └── Cache Hit: Return HTTP 301 Redirect (< 1 ms)
       │
       └── 3. Cache Miss: Query Database (PostgreSQL / DynamoDB)
              ├── Write to Redis: `SET url:aB3x9Zq "https://..." EX 86400`
              └── Return HTTP 301 Permanent Redirect
```

### HTTP 301 vs. HTTP 302 Redirection:
- **301 Permanent Redirect**: Browser caches redirect locally forever. Next visits bypass our servers entirely. Reduces server load to near zero, but **prevents real-time click tracking/analytics**.
- **302 Temporary Redirect**: Browser always queries server first before redirecting. Enables accurate click analytics at the cost of slight server load.

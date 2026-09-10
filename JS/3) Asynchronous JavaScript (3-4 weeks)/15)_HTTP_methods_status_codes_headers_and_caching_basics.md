# HTTP Methods, Status Codes, Headers, and Caching Basics: Production Guide

Building resilient, scalable web applications requires an exact understanding of the **HTTP protocol contract**. Choosing the proper HTTP verb, handling semantic status codes, managing headers, and leveraging modern browser/CDN caching strategies directly determines the reliability, security, and speed of your systems.

---

## 1. HTTP Methods: Semantics, Idempotency & Safety

The HTTP/1.1 and HTTP/2 RFC specifications define methods with two foundational guarantees:
- **Safe Methods**: Operations that do NOT modify server state (read-only; can be pre-fetched or cached aggressively).
- **Idempotent Methods**: Operations where executing the request $N$ times ($N \ge 1$) produces the identical server state as executing it once.

```
+---------+--------+------------+-----------------------------------------------------------+
| Method  | Safe?  | Idempotent?| Semantic Responsibility                                   |
+---------+--------+------------+-----------------------------------------------------------+
| GET     |  YES   |    YES     | Retrieve resource representation without side effects.    |
| HEAD    |  YES   |    YES     | Same as GET, but server returns ONLY headers (no body).   |
| OPTIONS |  YES   |    YES     | Query server capabilities / CORS preflight check.         |
| PUT     |   NO   |    YES     | Complete replacement / upsert of target resource.        |
| DELETE  |   NO   |    YES     | Delete target resource. Subsequent deletes return 404/204.|
| POST    |   NO   |     NO     | Create new sub-resource or process arbitrary command.     |
| PATCH   |   NO   |     NO*    | Apply partial delta/diff modifications (*can be made idemp)|
+---------+--------+------------+-----------------------------------------------------------+
```

### Deep Dive: `PUT` vs `PATCH` vs `POST`

```javascript
// PUT: Complete replacement (Client sends entire new resource representation)
// Idempotent: Sending 5 times leaves user 101 with exactly this state.
await fetch('/api/users/101', {
  method: 'PUT',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    name: 'Alice Smith',
    email: 'alice@example.com',
    role: 'Admin',
    preferences: { theme: 'dark' } // All missing fields will be cleared/reset!
  })
});

// PATCH: Partial Delta Modification (Only specified keys are updated)
// Non-idempotent if using JSON Patch ops like { op: "add", path: "/items", value: "x" }
await fetch('/api/users/101', {
  method: 'PATCH',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'alice.new@example.com' // Only updates email; preserves rest of user object
  })
});

// POST: Non-idempotent creation
// Sending 5 times creates 5 distinct order records.
await fetch('/api/orders', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ itemId: 42, qty: 1 })
});
```

---

## 2. HTTP Status Code Taxonomy

Status codes are 3-digit integers categorized into 5 functional blocks:

```
1xx (Informational) -> Request received, continuing process (e.g. 101 Switching Protocols for WebSockets)
2xx (Success)       -> Action successfully received, understood, and accepted
3xx (Redirection)   -> Further action must be taken to complete request
4xx (Client Error)  -> Request contains bad syntax or cannot be fulfilled by client
5xx (Server Error)  -> Server failed to fulfill an apparently valid request
```

### Production Status Code Reference

| Code | Text | Common Use Case | Client Action |
| :--- | :--- | :--- | :--- |
| **200** | `OK` | Standard successful response with body (`GET`, `PUT`, `PATCH`) | Process body payload |
| **201** | `Created` | New resource created (`POST`); returns `Location` header | Extract new resource ID |
| **204** | `No Content` | Action succeeded, zero body returned (`DELETE`, empty `PUT`) | Do not attempt `.json()` parse |
| **301** | `Moved Permanently` | Resource has permanent new URL | Update saved bookmarks/URLs |
| **304** | `Not Modified` | Cached version is fresh (conditional GET with `ETag`) | Serve from client cache |
| **400** | `Bad Request` | Malformed JSON, missing fields, schema validation failure | Fix payload and do not retry blindly |
| **401** | `Unauthorized` | Missing or invalid authentication credentials | Prompt login or refresh token |
| **403** | `Forbidden` | Authenticated, but user lacks permissions (RBAC) | Display "Access Denied" screen |
| **404** | `Not Found` | Target endpoint or resource does not exist | Show 404 UI |
| **409** | `Conflict` | State conflict (e.g. duplicate email, version conflict) | Prompt user to resolve collision |
| **422** | `Unprocessable Entity`| Syntactically valid JSON, but business logic validation failed | Highlight form field errors |
| **429** | `Too Many Requests` | Rate limit exceeded; check `Retry-After` header | Back off and wait |
| **500** | `Internal Server Error`| Unhandled server-side exception | Alert on-call / generic error UI |
| **502** | `Bad Gateway` | Reverse proxy (Nginx) cannot reach backend app server | Retry with exponential backoff |
| **503** | `Service Unavailable` | Server temporarily overloaded or down for maintenance | Check `Retry-After` header |
| **504** | `Gateway Timeout` | Upstream service took too long to respond to proxy | Retry idempotent requests |

---

## 3. Essential HTTP Headers Matrix

Headers carry metadata for content negotiation, authentication, security, and cache policies.

### A. Representation & Content Negotiation Headers
- `Content-Type`: Format of the payload being sent (e.g. `application/json; charset=utf-8`, `multipart/form-data`).
- `Accept`: Formats the client is capable of parsing (e.g. `application/json`, `text/html`).
- `Content-Encoding`: Compression algorithm applied to body (e.g. `br` (Brotli), `gzip`).

### B. Authentication & Security Headers
- `Authorization`: Credentials for authenticating user agent (`Bearer <token>`, `Basic <base64>`).
- `Origin`: Sent by browser in CORS requests indicating the domain making the call.
- `Access-Control-Allow-Origin`: Server response header specifying allowed client origins.

### C. Rate Limiting & Conditional Headers
- `Retry-After`: Seconds or HTTP date to wait before making next request after a `429` or `503`.
- `ETag`: Unique entity tag hash representing resource version (e.g. `W/"686892-b25"`).
- `If-None-Match`: Client sends saved `ETag`; if unchanged, server returns `304 Not Modified`.

---

## 4. HTTP Caching Architecture: Strong vs Revalidation

Browser and CDN caching operates in two primary modes:

```
                         [ Client makes GET request ]
                                      |
                     Is there a unexpired local cache?
                     (Age < max-age in Cache-Control)
                                /            \
                              YES             NO
                              /                \
            [ STRONG CACHE HIT ]       Does cache have an ETag / Last-Modified?
           (Loaded from memory/disk)             /                 \
              (HTTP 200 from cache)            YES                  NO
                                               /                     \
                             [ CONDITIONAL REQUEST ]         [ FULL NETWORK FETCH ]
                             (Sends If-None-Match)               (HTTP 200 OK)
                                     /       \
                          Has data changed?
                            /         \
                          NO          YES
                          /             \
             [ 304 Not Modified ]     [ 200 OK (New Data) ]
             (Zero payload transfer)  (Payload downloaded)
```

### The `Cache-Control` Directive Breakdown

```http
Cache-Control: public, max-age=31536000, immutable
```
- `max-age=<seconds>`: Duration for which the response is fresh.
- `no-cache`: **Misunderstood!** Does NOT mean "don't cache". Means "Cache it, but **revalidate with server** (`If-None-Match`) before using it".
- `no-store`: Do NOT cache anywhere (neither browser nor CDN). Use for sensitive banking/PII data.
- `public`: Can be cached by intermediate proxies, CDNs, and browsers.
- `private`: Can only be cached by the user's end browser, not intermediate CDNs.
- `stale-while-revalidate=<seconds>`: Serves stale cached content immediately while asynchronously revalidating in background.

---

## 5. Production HTTP Client with Status Routing & ETag Cache

```javascript
/**
 * In-Memory ETag-aware Client Cache
 */
class HttpCacheManager {
  constructor() {
    this.cache = new Map(); // url -> { etag, data }
  }

  get(url) {
    return this.cache.get(url);
  }

  set(url, etag, data) {
    this.cache.set(url, { etag, data });
  }
}

const clientCache = new HttpCacheManager();

/**
 * Intelligent HTTP client handling ETag revalidation and status branching
 */
async function smartFetch(url, options = {}) {
  const headers = new Headers(options.headers || {});
  
  // 1. Attach ETag for conditional revalidation if cached
  const cachedEntry = clientCache.get(url);
  if (cachedEntry?.etag && (!options.method || options.method === 'GET')) {
    headers.set('If-None-Match', cachedEntry.etag);
  }

  const response = await fetch(url, { ...options, headers });

  // 2. Handle Conditional 304 Not Modified
  if (response.status === 304 && cachedEntry) {
    console.log(`[Cache 304] Serving ${url} from client cache.`);
    return cachedEntry.data;
  }

  // 3. Handle 204 No Content
  if (response.status === 204) {
    return null;
  }

  // 4. Handle Error Status Codes Semantically
  if (!response.ok) {
    switch (response.status) {
      case 401:
        throw new Error("Session expired. Please log in again.");
      case 403:
        throw new Error("Forbidden: You do not have permission to access this resource.");
      case 404:
        throw new Error("Resource not found.");
      case 409:
        throw new Error("Conflict: Resource was modified by another process.");
      case 429: {
        const retrySec = response.headers.get('Retry-After') || '5';
        throw new Error(`Rate limit reached. Please wait ${retrySec} seconds.`);
      }
      case 500:
      case 502:
      case 503:
      case 504:
        throw new Error(`Server error (${response.status}). Please try again later.`);
      default:
        throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
    }
  }

  // 5. Parse Data & Store ETag
  const data = await response.json();
  const etag = response.headers.get('ETag');
  if (etag && (!options.method || options.method === 'GET')) {
    clientCache.set(url, etag, data);
  }

  return data;
}
```

---

## 6. Real-World Gotchas & Edge Cases

### Gotcha 1: `no-cache` vs `no-store` Confusion
Developers frequently set `Cache-Control: no-cache` believing it prevents all caching. A browser will still store the file on disk, sending an `If-None-Match` request on every visit.
- *Fix*: If data is confidential (e.g. credit cards), you **must** use `Cache-Control: no-store`.

### Gotcha 2: The `204 No Content` JSON Parse Trap
Calling `await response.json()` on a `204 No Content` or `HEAD` request throws `SyntaxError: Unexpected end of JSON input`.
- *Fix*: Always verify `if (response.status === 204) return null;` before parsing.

---

## 7. Senior Interview Challenges

### Challenge: Implement a Client-Side `stale-while-revalidate` Cache
Write a cache layer that immediately returns stale data from memory if present (for instant UI render), while simultaneously firing a background fetch to update the cache and invoke a subscriber callback.

```javascript
class StaleWhileRevalidateClient {
  constructor() {
    this.memoryCache = new Map(); // url -> data
  }

  async query(url, onUpdate) {
    const cached = this.memoryCache.get(url);

    // 1. Background revalidation task
    const revalidatePromise = fetch(url)
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(freshData => {
        this.memoryCache.set(url, freshData);
        if (onUpdate && JSON.stringify(cached) !== JSON.stringify(freshData)) {
          onUpdate(freshData);
        }
        return freshData;
      })
      .catch(err => console.warn("Background revalidation failed:", err));

    // 2. If cached, return immediately; otherwise wait for fresh data
    if (cached !== undefined) {
      return { data: cached, isStale: true };
    }

    const initialData = await revalidatePromise;
    return { data: initialData, isStale: false };
  }
}

// Verification Test
const swr = new StaleWhileRevalidateClient();
// 1st Call: Fetches fresh data
const res1 = await swr.query('https://jsonplaceholder.typicode.com/todos/1', updated => {
  console.log("Background update received:", updated);
});
console.log("First load:", res1);

// 2nd Call: Instantly returns cached, revalidates in background
const res2 = await swr.query('https://jsonplaceholder.typicode.com/todos/1', updated => {
  console.log("Background update received:", updated);
});
console.log("Second load (instant):", res2);
```

---

## 8. Summary & Quick Reference

```
                             [ CRUD to HTTP Mapping ]
                   Create   --> POST   /resources
                   Read     --> GET    /resources/:id
                   Replace  --> PUT    /resources/:id
                   Update   --> PATCH  /resources/:id
                   Delete   --> DELETE /resources/:id

                            [ Cache Policy Matrix ]
              Static Assets (Vite/Webpack hashed):
                -> Cache-Control: public, max-age=31536000, immutable
              Dynamic HTML (index.html):
                -> Cache-Control: no-cache (always revalidate ETag)
              Sensitive Financial / Auth Data:
                -> Cache-Control: no-store, private
```

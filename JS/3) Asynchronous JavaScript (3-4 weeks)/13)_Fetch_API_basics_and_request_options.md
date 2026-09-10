# `fetch` API Basics and Request Options: Production Masterclass

The Fetch API provides a modern, Promise-based interface for fetching resources across networks. Unlike legacy `XMLHttpRequest`, `fetch` is built upon standard specifications (`Request`, `Response`, `Headers`, and `Body` mixin) and is natively supported in modern browsers, Node.js (v18+), Deno, and Cloudflare Workers.

---

## 1. The Core Mental Model of Fetch

A crucial architectural distinction of `fetch()`:

> **`fetch()` only rejects on catastrophic network failures** (e.g., DNS resolution failure, network offline, CORS rejection, or aborted signals).
> **HTTP 4xx (Client Errors) and HTTP 5xx (Server Errors) DO NOT reject the promise.** They resolve normally with `response.ok === false`.

```
                  +-------------------------------------------------+
                  |                 fetch(url, options)             |
                  +-------------------------------------------------+
                                           |
                    +----------------------+----------------------+
                    |                                             |
           [ Network Failure ]                           [ Server Responds ]
         - DNS lookup failed                            - HTTP 200 OK (ok: true)
         - CORS blocked                                 - HTTP 404 Not Found (ok: false)
         - Connection refused                           - HTTP 500 Server Error (ok: false)
                    |                                             |
            [ PROMISE REJECTS ]                          [ PROMISE RESOLVES ]
            (catches in catch)                           (must inspect response.ok)
```

---

## 2. The `RequestInit` Configuration Matrix

The second argument to `fetch(url, init)` accepts a comprehensive configuration object:

```javascript
const response = await fetch('https://api.example.com/v1/orders', {
  // 1. HTTP Method
  method: 'POST', // 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE' | 'HEAD' | 'OPTIONS'

  // 2. Request Headers (can be plain Object, Headers instance, or Array of tuples)
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer eyJhbGciOi...',
    'Accept': 'application/json',
    'X-Client-Version': '2.4.0'
  },

  // 3. Payload Body (Cannot be sent with GET or HEAD requests)
  // Supports: string, Blob, BufferSource, FormData, URLSearchParams, ReadableStream
  body: JSON.stringify({
    productId: 'prod_987',
    quantity: 2
  }),

  // 4. Mode & Security Policies
  mode: 'cors', // 'cors' | 'no-cors' | 'same-origin' | 'navigate'
  credentials: 'same-origin', // 'omit' | 'same-origin' | 'include' (sends cookies/auth headers)

  // 5. Caching Strategy
  cache: 'default', // 'default' | 'no-store' | 'reload' | 'no-cache' | 'force-cache' | 'only-if-cached'

  // 6. Redirect Handling
  redirect: 'follow', // 'follow' | 'error' | 'manual'

  // 7. Referrer Policy
  referrerPolicy: 'no-referrer-when-downgrade',

  // 8. Cancellation & Timeout Signal
  signal: AbortSignal.timeout(5000), // Aborts request if not resolved within 5000ms

  // 9. Integrity Verification (Subresource Integrity - SRI)
  // integrity: 'sha384-oqVuAfXRKap7fdgcCY5uykM6+R9GqQ8K/uxy9rx7HNQlGYl1kPzQho1wx4JwY8wC',

  // 10. Node.js / Server specific (Duplex for streaming bodies)
  // duplex: 'half'
});
```

---

## 3. Working with Request Bodies & Encodings

The `body` property accepts multiple data types depending on the content type:

### A. JSON Body (`application/json`)
```javascript
const postJSON = async (url, payload) => {
  return fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
};
```

### B. Multipart Form Data (`multipart/form-data` for File Uploads)
> **Crucial Rule**: NEVER manually set `'Content-Type': 'multipart/form-data'` when passing a `FormData` object! The browser automatically appends the required boundary string (e.g. `boundary=----WebKitFormBoundaryXYZ`).

```javascript
const uploadAvatar = async (url, file, userId) => {
  const formData = new FormData();
  formData.append('avatar', file);
  formData.append('userId', userId);

  return fetch(url, {
    method: 'POST',
    // Do NOT set headers: { 'Content-Type': 'multipart/form-data' } !
    body: formData
  });
};
```

### C. URL Encoded Form (`application/x-www-form-urlencoded`)
```javascript
const postFormUrlEncoded = async (url, params) => {
  const searchParams = new URLSearchParams(params);

  return fetch(url, {
    method: 'POST',
    body: searchParams // Automatically sets Content-Type to application/x-www-form-urlencoded;charset=UTF-8
  });
};
```

---

## 4. Inspecting and Consuming the `Response` Object

A `Response` instance contains metadata and a **single-use readable stream body**.

### Key Response Properties
- `response.ok`: Boolean (`true` if HTTP status is in range $200-299$).
- `response.status`: Number (e.g., `200`, `404`, `500`).
- `response.statusText`: String (e.g., `"OK"`, `"Not Found"`).
- `response.headers`: `Headers` instance (methods: `get()`, `has()`, `entries()`).
- `response.url`: String (the final URL after any redirects).
- `response.redirected`: Boolean (`true` if redirected).
- `response.bodyUsed`: Boolean (`true` if body stream has already been read).

### Consuming Response Body (Stream Consumption Rule)
> **The body stream can only be read ONCE.** Calling `.json()` and then `.text()` on the same response will throw `TypeError: Failed to execute 'text' on 'Response': body stream already read`.

```javascript
// Methods to consume body:
const json = await response.json();       // Parses JSON
const text = await response.text();       // Returns raw UTF-8 text
const blob = await response.blob();       // Binary Large Object (images, files)
const buffer = await response.arrayBuffer(); // Low-level binary ArrayBuffer
const stream = response.body;            // ReadableStream<Uint8Array>

// If you need to read the body multiple times, CLONE the response first:
const clonedResponse = response.clone();
const textData = await response.text();
const jsonData = await clonedResponse.json();
```

---

## 5. Production-Ready Fetch Wrapper (HTTP Client)

A robust API client must handle HTTP errors, parse content types safely, format error responses, and apply default timeouts.

```javascript
/**
 * Custom Network and HTTP Application Error
 */
class ApiError extends Error {
  constructor(message, status, data = null, url = '') {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.data = data;
    this.url = url;
  }
}

/**
 * Enterprise HTTP Request Client
 */
async function httpClient(url, options = {}) {
  const {
    timeout = 8000,
    headers = {},
    body,
    ...restOptions
  } = options;

  // Setup timeout abort signal
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeout);

  // Merge signals if caller provided custom signal
  const signal = options.signal 
    ? AbortSignal.any([options.signal, controller.signal])
    : controller.signal;

  const defaultHeaders = {
    'Accept': 'application/json',
    ...(body && typeof body === 'object' && !(body instanceof FormData) && !(body instanceof URLSearchParams)
      ? { 'Content-Type': 'application/json' }
      : {})
  };

  const formattedBody = (body && typeof body === 'object' && !(body instanceof FormData) && !(body instanceof URLSearchParams))
    ? JSON.stringify(body)
    : body;

  try {
    const response = await fetch(url, {
      ...restOptions,
      signal,
      headers: { ...defaultHeaders, ...headers },
      body: formattedBody
    });

    clearTimeout(timeoutId);

    // Extract response data safely
    const contentType = response.headers.get('content-type') || '';
    let payload = null;

    if (contentType.includes('application/json')) {
      // Guard against empty 204 No Content responses
      payload = response.status === 204 ? null : await response.json();
    } else if (contentType.includes('text/')) {
      payload = await response.text();
    } else {
      payload = await response.blob();
    }

    if (!response.ok) {
      throw new ApiError(
        payload?.message || `HTTP ${response.status}: ${response.statusText}`,
        response.status,
        payload,
        url
      );
    }

    return payload;

  } catch (error) {
    clearTimeout(timeoutId);

    if (error.name === 'AbortError') {
      throw new ApiError(`Request to ${url} timed out after ${timeout}ms`, 408, null, url);
    }
    if (error instanceof ApiError) {
      throw error;
    }
    // Low-level network failure (DNS, offline, CORS)
    throw new ApiError(`Network failure: ${error.message}`, 0, null, url);
  }
}

// Example Usage
try {
  const user = await httpClient('https://api.example.com/users/123', {
    method: 'GET',
    timeout: 5000
  });
  console.log("Fetched User:", user);
} catch (err) {
  console.error(`Request Failed [${err.status}]:`, err.message, err.data);
}
```

---

## 6. Real-World Gotchas & Edge Cases

### Gotcha 1: The CORS "Opaque" Response Trap (`mode: 'no-cors'`)
Setting `mode: 'no-cors'` does **not** bypass CORS server restrictions to view private data. It causes the browser to return an **opaque response** (`status: 0`, empty headers, body inaccessible).
- *Rule*: You cannot inspect or parse JSON from a `no-cors` response; it is only useful for fire-and-forget ping beacons or caching CDN scripts via Service Workers.

### Gotcha 2: Authentication Cookies with `credentials`
By default in modern browsers, `credentials: 'same-origin'` is active. If making requests to an external sub-domain (e.g. from `app.example.com` to `api.example.com`), cookies and authorization headers are omitted unless you specify `credentials: 'include'` and the server sends `Access-Control-Allow-Credentials: true`.

---

## 7. Senior Interview Challenges

### Challenge 1: Construct a Type-Safe Interceptor Pipeline for `fetch`
Build a composable wrapper around `fetch` that allows attaching pre-request middlewares (e.g., auto-injecting JWT tokens) and post-response middlewares (e.g., auto-refreshing expired tokens on 401).

```javascript
class FetchInterceptorManager {
  constructor() {
    this.requestInterceptors = [];
    this.responseInterceptors = [];
  }

  useRequest(fn) {
    this.requestInterceptors.push(fn);
  }

  useResponse(onSuccess, onError) {
    this.responseInterceptors.push({ onSuccess, onError });
  }

  async fetch(url, options = {}) {
    let reqConfig = { url, options: { ...options, headers: { ...options.headers } } };

    // 1. Run Request Interceptors
    for (const interceptor of this.requestInterceptors) {
      reqConfig = await interceptor(reqConfig);
    }

    // 2. Perform Fetch
    let responsePromise = fetch(reqConfig.url, reqConfig.options);

    // 3. Run Response Interceptors Chain
    for (const { onSuccess, onError } of this.responseInterceptors) {
      responsePromise = responsePromise.then(
        res => (onSuccess ? onSuccess(res, reqConfig, this.fetch.bind(this)) : res),
        err => (onError ? onError(err, reqConfig, this.fetch.bind(this)) : Promise.reject(err))
      );
    }

    return responsePromise;
  }
}

// Verification: Auto-Token Refresh Middleware
const api = new FetchInterceptorManager();

api.useRequest(async (config) => {
  config.options.headers['Authorization'] = 'Bearer current_access_token';
  return config;
});

api.useResponse(
  async (response, config, retryFetch) => {
    if (response.status === 401 && !config._isRetry) {
      console.log("Token expired! Refreshing...");
      // Simulate token refresh
      config._isRetry = true;
      config.options.headers['Authorization'] = 'Bearer refreshed_new_token';
      return retryFetch(config.url, config.options);
    }
    return response;
  }
);
```

---

## 8. Summary & Quick Reference

| Feature / Scenario | Syntax / Recommendation |
| :--- | :--- |
| **Error Handling** | Always check `if (!res.ok) throw new Error(...)` |
| **JSON POST** | `headers: { 'Content-Type': 'application/json' }`, `body: JSON.stringify(data)` |
| **File Upload** | Pass `body: new FormData()`. **Do not** manually set `Content-Type` header |
| **Cancellation** | Pass `signal: AbortSignal.timeout(ms)` or custom `AbortController.signal` |
| **Stream Body** | Body can be consumed only once unless `.clone()` is called prior |
| **Credentials** | Set `credentials: 'include'` for cross-origin cookie transmission |

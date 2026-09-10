# Runtime Environments: Web APIs and Node.js APIs in JavaScript

---

## 1) Core Mental Model: Engine vs. Runtime Environment

A common misconception among developers is that timers (`setTimeout`), network requests (`fetch`), and file operations (`fs`) are built directly into the JavaScript language specification (ECMAScript).

**They are not.**

- **The JavaScript Engine (e.g., Google V8, Mozilla SpiderMonkey, Apple JavaScriptCore)**: Implements purely the ECMAScript standard. It provides the **Call Stack**, the **Memory Heap**, and the **Garbage Collector**. The engine has zero capability to talk to a network card, access a file system, or listen to hardware clock ticks.
- **The Host Runtime Environment (e.g., Web Browser, Node.js, Deno, Bun)**: Wraps around the JavaScript engine, supplying **Host APIs (Web APIs or Node APIs)**, OS-level thread pools, and the **Event Loop** mechanism to manage asynchronous operations.

```
┌────────────────────────────────────────────────────────────────────────┐
│                     THE JAVASCRIPT RUNTIME STACK                       │
│                                                                        │
│   +────────────────────────────────────────────────────────────────+   │
│   │                 HOST RUNTIME ENVIRONMENT                       │   │
│   │  (Google Chrome / Firefox / Safari OR Node.js / Deno / Bun)   │   │
│   │                                                                │   │
│   │   +───────────────────────────+   +─────────────────────────+  │   │
│   │   │     JAVASCRIPT ENGINE     │   │      HOST / C++ APIs    │  │   │
│   │   │         (V8 / JSC)        │   │                         │  │   │
│   │   │                           │   │  Browser: Web APIs      │  │   │
│   │   │  ┌──────────────────────┐ │   │  • fetch(), setTimeout()│  │   │
│   │   │  │      CALL STACK      │ │   │  • DOM, localStorage    │  │   │
│   │   │  └──────────────────────┘ │   │                         │  │   │
│   │   │  ┌──────────────────────┐ │   │  Node.js: Libuv / C++   │  │   │
│   │   │  │     MEMORY HEAP      │ │   │  • fs, net, crypto      │  │   │
│   │   │  └──────────────────────┘ │   │  • Thread Pool (4-128)  │  │   │
│   │   +─────────────┬─────────────+   +────────────┬────────────+  │   │
│   │                 │                              │               │   │
│   │                 ▼                              ▼               │   │
│   │         +──────────────────────────────────────────────+       │   │
│   │         │           TASK & MICROTASK QUEUES            │       │   │
│   │         +───────────────────────┬──────────────────────+       │   │
│   │                                 │                              │   │
│   │                                 ▼                              │   │
│   │         +──────────────────────────────────────────────+       │   │
│   │         │                  EVENT LOOP                  │       │   │
│   │         +──────────────────────────────────────────────+       │   │
│   +────────────────────────────────────────────────────────────────+   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The Browser Runtime & Web APIs

In a browser, the host environment exposes a rich suite of C++ browser sub-systems via the global `window` object (also accessible via `globalThis`).

```
┌────────────────────────────────────────────────────────────────────────┐
│                        BROWSER WEB API ECOSYSTEM                       │
│                                                                        │
│   • Timers:        setTimeout, setInterval, requestAnimationFrame      │
│   • Networking:    fetch(), XMLHttpRequest, WebSocket, WebRTC, SSE     │
│   • DOM / UI:      document, querySelector, addEventListener, Events   │
│   • Observers:     IntersectionObserver, MutationObserver, Resize      │
│   • Storage:       localStorage, sessionStorage, IndexedDB, Cache      │
│   • Concurrency:   Web Workers, Service Workers, SharedArrayBuffer     │
│   • Hardware / OS: Geolocation, Web Bluetooth, Clipboard, Camera       │
└────────────────────────────────────────────────────────────────────────┘
```

---

### Key Browser Web APIs in Action:

```js
// 1. Networking via Fetch API (Delegated to browser network stack)
async function loadUserData(userId) {
  const response = await fetch(`https://api.example.com/users/${userId}`);
  return response.json();
}

// 2. High-Performance Screen Repaint Sync (Syncs with monitor refresh rate: 60Hz/120Hz)
function animateBox(element, position = 0) {
  element.style.transform = `translateX(${position}px)`;
  if (position < 500) {
    requestAnimationFrame(() => animateBox(element, position + 5));
  }
}

// 3. Viewport Intersection Observer (Lazy Loading images without scroll listeners)
const imageObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      const img = entry.target;
      img.src = img.dataset.src;
      imageObserver.unobserve(img);
    }
  });
});
```

---

## 3) The Node.js Runtime & Libuv Architecture

Node.js is built by binding the **Google V8 Engine** with **Libuv** (a high-performance, cross-platform asynchronous I/O library written in C) and a core layer of JavaScript/C++ standard libraries.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        NODE.JS RUNTIME ARCHITECTURE                    │
│                                                                        │
│   Your JavaScript Code                                                 │
│            │                                                           │
│            ▼                                                           │
│   Node.js Standard Library (`node:fs`, `node:http`, `node:crypto`)     │
│            │                                                           │
│            ▼                                                           │
│   Node.js C++ Bindings (V8 Bridges)                                    │
│            │                                                           │
│            ▼                                                           │
│   ┌────────────────────────────────────────────────────────────────┐   │
│   │                             LIBUV                              │   │
│   │  • Event Loop (Timers, Poll, Check, Close)                     │   │
│   │  • OS Asynchronous I/O (epoll / kqueue / IOCP for Sockets)    │   │
│   │  • Worker Thread Pool (Default: 4 threads for Disk & Crypto)   │   │
│   └────────────────────────────────────────────────────────────────┘   │
└────────────────────────────────────────────────────────────────────────┘
```

---

### What Does Libuv Manage?
1. **Non-Blocking OS Sockets**: Network connections (HTTP, TCP, UDP) use native OS non-blocking notifications (`epoll` on Linux, `kqueue` on macOS, `IOCP` on Windows) on a single thread.
2. **The Libuv Thread Pool (`UV_THREADPOOL_SIZE`)**: The OS does not support true non-blocking file system calls on all platforms. Libuv spawns a **pool of 4 background worker threads** (configurable up to 128) to handle:
   - File System operations (`fs.readFile`, `fs.writeFile`)
   - CPU-intensive cryptography (`crypto.pbkdf2`, `crypto.scrypt`)
   - DNS lookups (`dns.lookup`)
   - Compression (`zlib`)

---

### Key Node.js Core APIs in Action:

```js
import fs from "node:fs/promises";
import crypto from "node:crypto";
import http from "node:http";

// 1. Asynchronous File I/O (Offloaded to Libuv Worker Thread Pool)
async function readConfigFile(path) {
  try {
    const rawContent = await fs.readFile(path, "utf-8");
    return JSON.parse(rawContent);
  } catch (err) {
    console.error("Config file missing:", err.message);
  }
}

// 2. High-Performance Password Hashing (Offloaded to Libuv Thread Pool)
function hashPasswordAsync(password, salt) {
  return new Promise((resolve, reject) => {
    crypto.scrypt(password, salt, 64, (err, derivedKey) => {
      if (err) reject(err);
      resolve(derivedKey.toString("hex"));
    });
  });
}

// 3. HTTP Server (Managed by Libuv non-blocking network socket)
const server = http.createServer((req, res) => {
  res.writeHead(200, { "Content-Type": "application/json" });
  res.end(JSON.stringify({ status: "Server healthy" }));
});
// server.listen(3000);
```

---

## 4) Universal (Isomorphic) APIs via `globalThis`

Historically, writing code that ran in both browsers and Node.js was painful because the global object had different names (`window` vs `global`).

ECMAScript standardized **`globalThis`** (ES2020), and modern runtimes have aligned on standard universal APIs:

| Universal API | Purpose | Browser Supported | Node.js Supported |
| :--- | :--- | :--- | :--- |
| **`fetch()` / `Headers` / `Request`** | HTTP Networking | ✅ All browsers | ✅ Native in Node 18+ |
| **`setTimeout()` / `setInterval()`** | Timers | ✅ Window | ✅ Global |
| **`AbortController` / `AbortSignal`** | Async cancellation | ✅ Native | ✅ Native (Node 15+) |
| **`URL` / `URLSearchParams`** | URL parsing | ✅ Native | ✅ Native |
| **`structuredClone()`** | Native deep copy | ✅ Native | ✅ Native (Node 17+) |
| **`crypto.randomUUID()`** | UUID v4 generator | ✅ `crypto` | ✅ Native (Node 15+) |
| **`TextEncoder` / `TextDecoder`** | String byte conversion | ✅ Native | ✅ Native |
| **`console.log()`** | Terminal/Console output | ✅ Console | ✅ Console |

---

## 5) Master Comparison: Browser vs. Node.js Runtime

| Dimension | Browser Environment | Node.js Environment |
| :--- | :--- | :--- |
| **Global Object** | `window`, `self`, `globalThis` | `global`, `globalThis` |
| **DOM Manipulation** | ✅ `document`, HTML elements, CSS | ❌ None (Use JSDOM if needed) |
| **File System Access** | ❌ Blocked by sandbox (OPFS limited) | ✅ Full direct disk access (`node:fs`) |
| **Network Capabilities** | Restricted by CORS and browser policies | Unrestricted raw TCP/UDP/HTTP sockets |
| **Multi-threading** | `Web Workers` (Message passing) | `worker_threads`, `child_process`, Cluster |
| **Module Defaults** | ES Modules (`<script type="module">`) | CommonJS or ESM (`"type": "module"`) |
| **Process Control** | ❌ Cannot exit browser tab directly | ✅ `process.exit()`, `process.env` |

---

## 6) Common Real-World Pitfalls: The SSR Hazard

In modern Full-Stack frameworks (Next.js, Remix, Nuxt, SvelteKit), JavaScript code runs on **both the server (Node.js/Bun) and the client (Browser)** during Server-Side Rendering (SSR).

```js
// ❌ CRITICAL SSR BUG:
function getSavedTheme() {
  // During SSR in Node.js, 'window' and 'localStorage' do not exist!
  // Throws: ReferenceError: window is not defined
  return localStorage.getItem("theme") || "dark";
}

// ✅ SAFE ISOMORPHIC PATTERN:
function getSavedThemeSafe() {
  if (typeof window !== "undefined" && typeof window.localStorage !== "undefined") {
    return localStorage.getItem("theme") || "dark";
  }
  return "dark"; // Default fallback for SSR server phase
}
```

---

## 7) Summary Cheat Sheet

```
+────────────────────────────────────────────────────────────────────────────+
|                     RUNTIME ENVIRONMENTS QUICK MATRIX                      |
+───────────────────────────+────────────────────────────────────────────────+
| JS Engine Role            | Executes ECMAScript (Stack, Heap, GC only).    |
| Host Runtime Role         | Provides Web/Node APIs, Thread Pool, Loop.     |
| Web APIs                  | DOM, fetch, setTimeout, Workers, IndexedDB.    |
| Libuv (Node.js)           | Asynchronous OS Sockets + 4-thread worker pool.|
| Libuv Thread Pool Tasks   | File I/O, Crypto (`scrypt/pbkdf2`), DNS, Zlib. |
| `globalThis`              | Standard universal global across all runtimes. |
| SSR Guard                 | Check `typeof window !== "undefined"` in SSR.  |
+───────────────────────────+────────────────────────────────────────────────+
```

# 3) Raw HTTP and HTTPS Core Architecture

## Executive Overview
The Node.js `node:http` and `node:https` core modules form the bedrock of the entire Node.js backend ecosystem. Frameworks like Express and NestJS are abstraction layers built directly on top of these native classes.

Mastery of the core HTTP layer requires understanding how the **`llhttp` C-parser** decodes incoming TCP packets, how **`IncomingMessage` (Readable)** and **`ServerResponse` (Writable)** manage stream lifecycles, how **Chunked Transfer Encoding** operates, and how to configure enterprise **TLS 1.3 & Mutual TLS (mTLS)**.

---

## 1. Internal HTTP Architecture & `llhttp`

When an HTTP request arrives on a TCP port:
1. The OS kernel delivers raw TCP byte packets to Libuv.
2. Node.js invokes the C library **`llhttp`** (ultra-fast, zero-memory allocation HTTP/1.1 parser).
3. `llhttp` parses the HTTP Request Line and Headers, instantiating an `http.IncomingMessage`.
4. The server creates an `http.ServerResponse` attached to the underlying TCP socket.
5. The `'request'` event fires in JavaScript: `(req, res) => {}`.

```text
Incoming TCP Packets
       │
       ▼
[llhttp C-Parser Engine] ──> Parses Headers & HTTP Method
       │
       ├──> Instantiates req (http.IncomingMessage - Readable Stream)
       └──> Instantiates res (http.ServerResponse - Writable Stream)
       │
       ▼
http.createServer((req, res) => { ... })
```

---

## 2. Low-Level HTTP Request/Response Stream Mechanics

```typescript
import http from 'node:http';

const server = http.createServer((req: http.IncomingMessage, res: http.ServerResponse) => {
  const { method, url, headers } = req;
  console.log(`[HTTP INCOMING] ${method} ${url}`);

  // 1. Reading Body as a Readable Stream (Buffer Chunks)
  const bodyChunks: Buffer[] = [];

  req.on('data', (chunk: Buffer) => {
    bodyChunks.push(chunk);
  });

  req.on('end', () => {
    const rawBody = Buffer.concat(bodyChunks).toString('utf-8');
    
    // 2. Writing Response as a Writable Stream
    res.statusCode = 200;
    res.setHeader('Content-Type', 'application/json');
    res.setHeader('X-Powered-By', 'Node.js Core HTTP');

    const responsePayload = JSON.stringify({
      status: 'SUCCESS',
      echoReceivedBody: rawBody,
      timestamp: new Date().toISOString(),
    });

    // Write and complete response
    res.end(responsePayload);
  });

  req.on('error', (err) => {
    console.error('[REQ STREAM ERROR]', err);
    res.statusCode = 400;
    res.end('Bad Request');
  });
});

server.listen(3000, () => {
  console.log('[HTTP SERVER] Running on port 3000');
});
```

---

## 3. Chunked Transfer Encoding (`Transfer-Encoding: chunked`)

When serving dynamically generated content or large file streams where the total byte length is not known ahead of time, HTTP/1.1 uses **Chunked Transfer Encoding**:
- Omit the `Content-Length` header.
- Node.js automatically emits `Transfer-Encoding: chunked`.
- Each `.write(chunk)` sends the chunk byte length in hex followed by `\r\n` and the payload.
- Calling `.end()` sends the terminating `0\r\n\r\n` chunk.

```typescript
import http from 'node:http';

const server = http.createServer((req, res) => {
  res.writeHead(200, { 'Content-Type': 'text/plain' });

  // Stream data progressively every 500ms
  let count = 0;
  const interval = setInterval(() => {
    count++;
    res.write(`Progress chunk #${count} at ${new Date().toISOString()}\n`);

    if (count >= 5) {
      clearInterval(interval);
      res.end('Final chunk. Stream completed.\n');
    }
  }, 500);
});
```

---

## 4. HTTPS & TLS Architecture (Transport Layer Security)

HTTPS encrypts HTTP traffic over **TLS (Transport Layer Security)** via OpenSSL bindings (`node:tls`).

### 4.1 Production HTTPS Server Configuration

```typescript
import https from 'node:https';
import fs from 'node:fs';
import path from 'node:path';

const httpsOptions: https.ServerOptions = {
  key: fs.readFileSync(path.resolve('certs/server.key')),
  cert: fs.readFileSync(path.resolve('certs/server.crt')),
  ca: fs.readFileSync(path.resolve('certs/ca.crt')), // Certificate Authority Chain
  
  // Modern Security Hardening:
  minVersion: 'TLSv1.3', // Enforce modern TLS 1.3 (eliminates weak ciphers)
  ciphers: 'TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:TLS_AES_128_GCM_SHA256',
  honorCipherOrder: true,
};

const httpsServer = https.createServer(httpsOptions, (req, res) => {
  res.writeHead(200, { 'Content-Type': 'text/plain' });
  res.end('Secure HTTPS Response over TLS 1.3\n');
});

httpsServer.listen(8443, () => {
  console.log('[HTTPS SERVER] Running on https://localhost:8443');
});
```

---

## 5. Mutual TLS (mTLS) for Zero-Trust Microservices

In standard TLS, only the server proves its identity to the client. In **Mutual TLS (mTLS)**, both the client and server present X.509 cryptographic certificates to authenticate each other:

```typescript
import https from 'node:https';
import fs from 'node:fs';

const mTcpOptions: https.ServerOptions = {
  key: fs.readFileSync('certs/server.key'),
  cert: fs.readFileSync('certs/server.crt'),
  ca: fs.readFileSync('certs/internal-ca.crt'),
  
  // mTLS Enforcement:
  requestCert: true,        // Request client certificate during TLS handshake
  rejectUnauthorized: true, // Reject connection immediately if client certificate is invalid
};

const mTcpServer = https.createServer(mTcpOptions, (req, res) => {
  // Retrieve verified client certificate metadata
  const clientCert = (req.socket as any).getPeerCertificate();
  console.log(`[mTLS AUTHENTICATED] Client Subject: ${clientCert.subject.CN}`);

  res.writeHead(200, { 'Content-Type': 'application/json' });
  res.end(JSON.stringify({ authenticatedService: clientCert.subject.CN }));
});
```

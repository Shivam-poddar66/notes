# 4) HTTP/2 and HTTP/3 (QUIC) Modern Web Protocols

## Executive Overview
HTTP/1.1 served the internet for two decades but suffers from structural performance bottlenecks: **Head-of-Line (HoL) blocking**, verbose uncompressed plaintext headers, and the need to open 6+ concurrent TCP connections per domain.

**HTTP/2** solved these issues by introducing a **Binary Framing Layer** that multiplexes hundreds of concurrent requests over a single TCP connection with **HPACK header compression**. **HTTP/3** further refines this by replacing TCP with **QUIC over UDP**, eliminating transport-level packet stall bottlenecks.

---

## 1. HTTP/1.1 vs HTTP/2 Architectural Evolution

```text
HTTP/1.1 (6 Separate TCP Connections):
TCP Conn 1: [ Request A ] ──────> [ Response A ]
TCP Conn 2: [ Request B ] ──────> [ Response B ]  (Heavy overhead, 6 TCP handshakes)

HTTP/2 (1 TCP Connection with Multiplexed Binary Streams):
            ┌── Stream 1: [ HEADERS ] ── [ DATA Chunk 1 ] ──┐
Single TCP ─┼── Stream 3: [ HEADERS ] ── [ DATA Chunk 1 ] ──┼─> All Interleaved!
Connection  └── Stream 5: [ HEADERS ] ──────────────────────┘
```

---

## 2. HTTP/2 Core Mechanisms

1. **Binary Framing Layer**: Replaces plaintext ASCII HTTP with small, structured binary frames (`DATA`, `HEADERS`, `SETTINGS`, `RST_STREAM`, `PING`, `GOAWAY`).
2. **Multiplexing**: Requests and responses are assigned unique Stream IDs (odd numbers for client-initiated, even numbers for server-initiated) and interleaved asynchronously across a single TCP socket.
3. **HPACK Header Compression**: Maintains static and dynamic compression dictionaries across the connection lifetime, reducing header payload size by up to **85%–90%**.
4. **Stream Prioritization & Flow Control**: Clients can assign dependency weights to prioritize critical CSS/JS assets over background images.

---

## 3. Building an HTTP/2 Server in Node.js (`node:http2`)

Because web browsers require TLS encryption (ALPN `h2`) for HTTP/2, an HTTP/2 server is typically created using `http2.createSecureServer`:

```typescript
import http2 from 'node:http2';
import fs from 'node:fs';
import path from 'node:path';

const server = http2.createSecureServer({
  key: fs.readFileSync(path.resolve('certs/server.key')),
  cert: fs.readFileSync(path.resolve('certs/server.crt')),
  allowHTTP1: true, // Fallback to HTTP/1.1 if client does not support h2 (ALPN negotiation)
});

// Stream Event Handler (Replaces standard req/res event in HTTP/2)
server.on('stream', (stream: http2.ServerHttp2Stream, headers: http2.IncomingHttpHeaders) => {
  const method = headers[':method'];
  const pathname = headers[':path'];

  console.log(`[HTTP/2 STREAM ${stream.id}] ${method} ${pathname}`);

  // Send HTTP/2 Headers Frame
  stream.respond({
    ':status': 200,
    'content-type': 'application/json',
    'x-protocol': 'HTTP/2.0',
  });

  // Send HTTP/2 Data Frame & Close Stream
  const responseData = JSON.stringify({
    message: 'Hello from Node.js Native HTTP/2 Multiplexed Server',
    streamId: stream.id,
    timestamp: Date.now(),
  });

  stream.end(responseData);
});

server.listen(8443, () => {
  console.log('[HTTP/2 SERVER] Listening on https://localhost:8443');
});
```

---

## 4. HTTP/3 and QUIC (The UDP Revolution)

While HTTP/2 eliminated application-layer Head-of-Line blocking, it still suffers from **Transport-Layer Head-of-Line Blocking**:
- Because HTTP/2 multiplexes all streams over a single TCP connection, if **one single TCP packet is dropped** by the network, the OS kernel stalls **all multiplexed streams** until the missing packet is retransmitted.

```text
HTTP/2 TCP Packet Drop:
[ Packet 1 (Stream 1) ] [ Packet 2 (Stream 3) - LOST! ] [ Packet 3 (Stream 5) ]
                             │
                             ▼
TCP STALLS ALL STREAMS until Packet 2 is retransmitted!

HTTP/3 QUIC (over UDP):
[ Datagram 1 (Stream 1) ] [ Datagram 2 (Stream 3) - LOST! ] [ Datagram 3 (Stream 5) ]
                                 │
                                 ▼
Stream 1 and Stream 5 continue processing with ZERO DELAY! Only Stream 3 waits.
```

### Key Advantages of HTTP/3 & QUIC:
1. **Zero Transport Head-of-Line Blocking**: Each stream is an independent byte stream inside UDP.
2. **0-RTT Connection Establishment**: Combines cryptographic handshake and transport connection into a single round trip.
3. **Connection Migration**: Connections are identified by a 64-bit Connection ID rather than the IP address 4-tuple. When a mobile user switches from Wi-Fi to 5G Cellular, the connection stays alive without re-negotiating.

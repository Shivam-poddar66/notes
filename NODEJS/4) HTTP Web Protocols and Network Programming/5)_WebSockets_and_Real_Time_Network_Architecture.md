# 5) WebSockets and Real-Time Network Architecture

## Executive Overview
While HTTP is request-response oriented, modern applications (chat platforms, live trading tickers, collaborative document editors) require full-duplex, bidirectional, ultra-low-latency real-time communication.

The **WebSocket Protocol (RFC 6455)** establishes a persistent TCP connection initiated over HTTP, allowing client and server to push messages independently with minimal frame header overhead (2–10 bytes).

---

## 1. The WebSocket Upgrade Handshake

Every WebSocket connection begins as a standard HTTP/1.1 request:

```text
Client Request:
GET /chat HTTP/1.1
Host: server.example.com
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==
Sec-WebSocket-Version: 13

Server Response (HTTP 101 Switching Protocols):
HTTP/1.1 101 Switching Protocols
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Accept: s3pPLMBiTxaQ9kYGzzhZRbK+xOo=
```

### The SHA-1 Handshake Math:
To compute `Sec-WebSocket-Accept`, the server concatenates the client's `Sec-WebSocket-Key` with the standardized magic GUID string (`258EAFA5-E914-47DA-95CA-C5AB0DC85B11`), hashes it using SHA-1, and base64 encodes the result:

```typescript
import crypto from 'node:crypto';

export function calculateWebSocketAccept(clientKey: string): string {
  const GUID = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
  return crypto
    .createHash('sha1')
    .update(clientKey + GUID)
    .digest('base64');
}
```

---

## 2. High-Performance WebSocket Server (`ws`)

The `ws` library is the industry standard for high-performance WebSocket servers in Node.js.

```typescript
import { WebSocketServer, WebSocket } from 'ws';
import http from 'node:http';

const server = http.createServer();
const wss = new WebSocketServer({ server });

interface ExtWebSocket extends WebSocket {
  isAlive: boolean;
  userId?: string;
}

wss.on('connection', (ws: ExtWebSocket, req: http.IncomingMessage) => {
  ws.isAlive = true;
  console.log(`[WS CONNECTED] Client from: ${req.socket.remoteAddress}`);

  // Heartbeat Pong Listener
  ws.on('pong', () => {
    ws.isAlive = true;
  });

  ws.on('message', (data: Buffer, isBinary: boolean) => {
    const messageStr = isBinary ? data : data.toString('utf-8');
    console.log('[WS MESSAGE RECEIVED]:', messageStr);

    // Broadcast to all connected clients
    wss.clients.forEach((client) => {
      if (client !== ws && client.readyState === WebSocket.OPEN) {
        client.send(data, { binary: isBinary });
      }
    });
  });

  ws.on('close', (code, reason) => {
    console.log(`[WS CLOSED] Code: ${code}, Reason: ${reason}`);
  });
});

// 3. Heartbeat Ping Interval (Terminates dead/zombie sockets every 30s)
const heartbeatInterval = setInterval(() => {
  wss.clients.forEach((client) => {
    const extWs = client as ExtWebSocket;
    if (extWs.isAlive === false) {
      console.log('[WS HEARTBEAT] Terminating inactive zombie client.');
      return extWs.terminate();
    }
    extWs.isAlive = false;
    extWs.ping(); // Client will reply with pong automatically
  });
}, 30000);

wss.on('close', () => clearInterval(heartbeatInterval));

server.listen(8080, () => console.log('[WS SERVER] Running on port 8080'));
```

---

## 3. Horizontal Scaling: Multi-Node WebSocket Sync via Redis Pub/Sub

When scaling across multiple Node.js instances behind a Load Balancer, a client connected to **Node A** cannot broadcast directly to a client connected to **Node B**.

### The Redis Pub/Sub Architecture

```text
               Load Balancer (Sticky Sessions / Round Robin)
                               │
                ┌──────────────┴──────────────┐
                ▼                             ▼
        [ Node.js Instance A ]        [ Node.js Instance B ]
          ├── WebSocket Client 1        ├── WebSocket Client 3
          └── WebSocket Client 2        └── WebSocket Client 4
                │                             │
                └──────────────┬──────────────┘
                               │
                               ▼
                    [ Redis Pub/Sub Cluster ]
                     Channel: 'chat:global'
```

```typescript
import Redis from 'ioredis';
import { WebSocketServer, WebSocket } from 'ws';

const pubClient = new Redis();
const subClient = new Redis();
const wss = new WebSocketServer({ port: 8080 });

// 1. Subscribe to Redis Channel
subClient.subscribe('chat:global');

subClient.on('message', (channel, message) => {
  if (channel === 'chat:global') {
    // Forward message to all local WebSocket connections on this specific Node instance
    wss.clients.forEach((client) => {
      if (client.readyState === WebSocket.OPEN) {
        client.send(message);
      }
    });
  }
});

// 2. Publish Incoming WebSocket Messages to Redis Channel
wss.on('connection', (ws) => {
  ws.on('message', (data) => {
    pubClient.publish('chat:global', data.toString());
  });
});
```

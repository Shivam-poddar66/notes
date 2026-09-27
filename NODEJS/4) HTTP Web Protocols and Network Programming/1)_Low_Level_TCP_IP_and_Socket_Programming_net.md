# 1) Low-Level TCP/IP and Socket Programming (`node:net`)

## Executive Overview
All modern web application protocols (HTTP/1.1, HTTP/2, WebSockets, PostgreSQL wire protocol, Redis RESP) are transport-layer abstractions built on top of **Transmission Control Protocol (TCP)**. The core `node:net` module provides the direct programming interface for creating asynchronous TCP servers, opening raw duplex socket connections, and managing local Inter-Process Communication (IPC) via Unix Domain Sockets and Windows Named Pipes.

---

## 1. TCP Fundamentals & The Node.js Socket Model

A TCP connection is a reliable, ordered, full-duplex byte stream established via a **3-Way Handshake** (`SYN` -> `SYN-ACK` -> `ACK`). In the operating system kernel, every active TCP connection is uniquely identified by a **4-Tuple**:
$$\text{Connection 4-Tuple} = (\text{Source IP}, \text{Source Port}, \text{Destination IP}, \text{Destination Port})$$

In Node.js, an active TCP socket (`net.Socket`) is an instance of **`Duplex` stream**—it can read and write binary chunks independently across the underlying OS network buffer:

```text
┌────────────────────────────────────────────────────────┐
│                   Node.js net.Socket                   │
│                                                        │
│  Readable Side (Incoming Packets)                      │
│  [ Kernel Socket Recv Buffer ] ──> socket.on('data')   │
│                                                        │
│  Writable Side (Outgoing Packets)                      │
│  socket.write(chunk) ──> [ Kernel Socket Send Buffer ] │
└────────────────────────────────────────────────────────┘
```

---

## 2. Building a Raw TCP Server & Client

```typescript
import net from 'node:net';

// 1. Create TCP Server
const server = net.createServer({ pauseOnConnect: false }, (socket: net.Socket) => {
  const remoteAddress = `${socket.remoteAddress}:${socket.remotePort}`;
  console.log(`[TCP SERVER] Client connected from: ${remoteAddress}`);

  // Configure Socket Options
  socket.setNoDelay(true); // Disable Nagle's algorithm for low latency
  socket.setKeepAlive(true, 60000); // 60s Keep-Alive probe

  // Handle Incoming Data Stream
  socket.on('data', (chunk: Buffer) => {
    console.log(`[TCP SERVER] Received ${chunk.length} bytes:`, chunk.toString('utf-8'));
    
    // Echo back to client
    const canAcceptMore = socket.write(`ECHO: ${chunk}`);
    if (!canAcceptMore) {
      console.log('[TCP SERVER] Backpressure detected on socket send buffer.');
    }
  });

  socket.on('end', () => {
    console.log(`[TCP SERVER] Client disconnected: ${remoteAddress}`);
  });

  socket.on('error', (err) => {
    console.error(`[TCP SERVER ERROR] ${remoteAddress}:`, err.message);
  });
});

server.listen(8080, '0.0.0.0', () => {
  console.log('[TCP SERVER] Listening on port 8080');
});
```

---

## 3. TCP Socket Performance Tuning

### 3.1 Nagle's Algorithm (`socket.setNoDelay(true)`)
- **Nagle's Algorithm**: Default OS behavior designed to prevent network congestion by buffering small outgoing packets until a full TCP segment (MSS ~1460 bytes) is accumulated or an ACK is received.
- **Problem**: Causes 40ms–200ms latency spikes in real-time APIs and RPC protocols sending small JSON/binary payloads.
- **Solution**: Call `socket.setNoDelay(true)` to set the `TCP_NODELAY` socket option, forcing immediate transmission.

### 3.2 TCP Keep-Alive (`socket.setKeepAlive(true, initialDelay)`)
- Detects zombie/dead TCP connections caused by network cable disconnects, router crashes, or dead client machines without proper TCP FIN packets.
- OS automatically transmits periodic ACK probes after `initialDelay` ms of inactivity.

---

## 4. Local High-Speed IPC: Unix Domain Sockets & Named Pipes

When two Node.js processes communicate on the **same physical server** (e.g. an NGINX reverse proxy forwarding to a Node.js process, or communication between Microservice sidecars), routing traffic through the TCP network stack (`localhost:8080`) introduces unnecessary overhead (TCP checksums, port allocations, routing tables).

**Unix Domain Sockets (POSIX)** and **Named Pipes (Windows)** communicate directly through kernel memory buffers, providing **up to 2x lower latency and higher throughput**:

```typescript
import net from 'node:net';
import fs from 'node:fs';
import path from 'node:path';

const isWindows = process.platform === 'win32';
const PIPE_PATH = isWindows
  ? '\\\\.\\pipe\\mastery-ipc-pipe'
  : path.resolve('/tmp/mastery-ipc.sock');

// Cleanup stale socket file on POSIX systems
if (!isWindows && fs.existsSync(PIPE_PATH)) {
  fs.unlinkSync(PIPE_PATH);
}

const ipcServer = net.createServer((socket) => {
  console.log('[IPC SERVER] Fast kernel socket client connected.');
  socket.write('IPC_HANDSHAKE_OK\n');
});

ipcServer.listen(PIPE_PATH, () => {
  console.log(`[IPC SERVER] High-speed local IPC listening on: ${PIPE_PATH}`);
});
```

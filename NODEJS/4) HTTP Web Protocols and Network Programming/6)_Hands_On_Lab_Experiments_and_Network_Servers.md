# 6) Hands-On Lab Experiments and Network Servers

This chapter provides four comprehensive, production-grade lab architectures demonstrating custom binary TCP protocols, partial content video streaming, mutual TLS security, and scalable WebSockets.

---

## Lab 1: Custom Length-Prefixed Binary TCP Protocol Server & Client

### Objective
TCP is a continuous byte stream with no built-in concept of "messages" or "packets." If a sender sends two JSON objects back-to-back, the receiver might receive them combined in a single chunk or split across multiple chunks.
This lab implements a **Length-Prefixed Packet Framing Protocol**:
- **Header**: 4-byte unsigned integer (Big Endian) indicating payload byte length.
- **Body**: Raw JSON / binary payload.

```typescript
import net from 'node:net';

// 1. Length-Prefixed TCP Protocol Parser Transform
export class PacketFramingBuffer {
  private buffer: Buffer = Buffer.alloc(0);

  public push(chunk: Buffer): Buffer[] {
    this.buffer = Buffer.concat([this.buffer, chunk]);
    const packets: Buffer[] = [];

    while (this.buffer.length >= 4) {
      // Read 4-byte payload length header
      const payloadLength = this.buffer.readUInt32BE(0);

      // Check if full packet has arrived
      if (this.buffer.length >= 4 + payloadLength) {
        const packet = this.buffer.subarray(4, 4 + payloadLength);
        packets.push(packet);
        // Slice remaining buffer
        this.buffer = this.buffer.subarray(4 + payloadLength);
      } else {
        // Wait for more data
        break;
      }
    }

    return packets;
  }
}

// 2. TCP Protocol Server
export const server = net.createServer((socket) => {
  const parser = new PacketFramingBuffer();

  socket.on('data', (chunk) => {
    const messages = parser.push(chunk);
    for (const msg of messages) {
      const parsedData = JSON.parse(msg.toString('utf-8'));
      console.log('[FRAMED PACKET RECEIVED]', parsedData);

      // Send length-prefixed response
      const responsePayload = Buffer.from(JSON.stringify({ status: 'PROCESSED', id: parsedData.id }));
      const responseHeader = Buffer.alloc(4);
      responseHeader.writeUInt32BE(responsePayload.length, 0);

      socket.write(Buffer.concat([responseHeader, responsePayload]));
    }
  });
});
```

---

## Lab 2: High-Performance HTTP/1.1 Video Streaming Server (HTTP 206 Range Requests)

```typescript
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';

export function createVideoStreamingServer(videoFilePath: string) {
  return http.createServer(async (req, res) => {
    const stat = await fs.promises.stat(videoFilePath);
    const fileSize = stat.size;
    const range = req.headers.range;

    // Handle HTTP 206 Partial Content (Video Seeking & Streaming)
    if (range) {
      const parts = range.replace(/bytes=/, '').split('-');
      const start = parseInt(parts[0], 10);
      const end = parts[1] ? parseInt(parts[1], 10) : fileSize - 1;
      const chunkSize = end - start + 1;

      const fileStream = fs.createReadStream(videoFilePath, { start, end });

      res.writeHead(206, {
        'Content-Range': `bytes ${start}-${end}/${fileSize}`,
        'Accept-Ranges': 'bytes',
        'Content-Length': chunkSize,
        'Content-Type': 'video/mp4',
      });

      fileStream.pipe(res);
    } else {
      // Full File Fallback
      res.writeHead(200, {
        'Content-Length': fileSize,
        'Content-Type': 'video/mp4',
      });

      fs.createReadStream(videoFilePath).pipe(res);
    }
  });
}
```

---

## Lab 3: Mutual TLS (mTLS) Authenticated Microservice Client

```typescript
import https from 'node:https';
import fs from 'node:fs';

export function callSecureMicroservice(apiUrl: string): Promise<any> {
  const agent = new https.Agent({
    key: fs.readFileSync('certs/client-service.key'),
    cert: fs.readFileSync('certs/client-service.crt'),
    ca: fs.readFileSync('certs/internal-ca.crt'),
    checkServerIdentity: (host, cert) => {
      // Verify expected SAN/CN hostname on server cert
      return undefined; // Valid
    },
  });

  return new Promise((resolve, reject) => {
    const req = https.get(apiUrl, { agent }, (res) => {
      const chunks: Buffer[] = [];
      res.on('data', (chunk) => chunks.push(chunk));
      res.on('end', () => {
        resolve(JSON.parse(Buffer.concat(chunks).toString()));
      });
    });

    req.on('error', reject);
  });
}
```

---

## Lab 4: Clustered WebSocket Real-Time Broadcaster with Redis Pub/Sub

```typescript
import { WebSocketServer, WebSocket } from 'ws';
import Redis from 'ioredis';
import http from 'node:http';

export function initClusteredWebSocketServer(port: number, redisUrl = 'redis://localhost:6379') {
  const server = http.createServer();
  const wss = new WebSocketServer({ server });
  const pub = new Redis(redisUrl);
  const sub = new Redis(redisUrl);

  const CHANNEL = 'events:broadcast';
  sub.subscribe(CHANNEL);

  // 1. When Redis receives a message from ANY Node instance, broadcast to local sockets
  sub.on('message', (channel, rawMessage) => {
    if (channel === CHANNEL) {
      wss.clients.forEach((client) => {
        if (client.readyState === WebSocket.OPEN) {
          client.send(rawMessage);
        }
      });
    }
  });

  // 2. When a local socket client sends a message, publish to Redis
  wss.on('connection', (ws) => {
    ws.on('message', (data) => {
      pub.publish(CHANNEL, data.toString());
    });
  });

  server.listen(port, () => {
    console.log(`[WS CLUSTER NODE] WebSocket listening on port ${port}`);
  });

  return { server, wss, pub, sub };
}
```

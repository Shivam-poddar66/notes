# 2) UDP / Datagram Sockets (`node:dgram`)

## Executive Overview
While TCP provides a reliable, ordered, connection-oriented stream, **User Datagram Protocol (UDP)** provides a lightweight, connectionless, message-oriented transport protocol. In Node.js, the `node:dgram` module provides the primitives to send and receive UDP datagrams.

UDP is the transport of choice for real-time multiplayer gaming, audio/video streaming, high-throughput metrics emission (StatsD/Prometheus push), DNS lookups, and modern HTTP/3 (QUIC).

---

## 1. TCP vs UDP Architectural Tradeoffs

```text
┌─────────────────────────┬───────────────────────────────┬───────────────────────────────┐
│ Feature                 │ TCP (Transmission Control)    │ UDP (User Datagram Protocol)  │
├─────────────────────────┼───────────────────────────────┼───────────────────────────────┤
│ Connection State        │ Connection-oriented (3-way)   │ Connectionless (Send & forget)│
│ Delivery Guarantee      │ Guaranteed (Retransmission)   │ Best-effort (Packets can drop)│
│ Packet Ordering         │ Strict in-order delivery      │ Out-of-order arrival possible │
│ Flow & Congestion Ctrl  │ Yes (Windowing, Backpressure) │ None (Maximum speed)          │
│ Overhead                │ 20-60 byte header, Handshake  │ 8-byte header, Zero handshake │
│ Head-of-Line Blocking   │ Yes (1 dropped packet stalls) │ No (Each packet independent)  │
└─────────────────────────┴───────────────────────────────┴───────────────────────────────┘
```

---

## 2. UDP Server & Client Implementation

```typescript
import dgram from 'node:dgram';

// 1. Create UDP Socket Server
const server = dgram.createSocket('udp4');

server.on('message', (msg: Buffer, rinfo: dgram.RemoteInfo) => {
  console.log(`[UDP SERVER] Received ${msg.length} bytes from ${rinfo.address}:${rinfo.port}: ${msg.toString()}`);

  // Send response datagram back to client
  const response = Buffer.from(`ACK: ${msg}`);
  server.send(response, rinfo.port, rinfo.address, (err) => {
    if (err) console.error('[UDP SEND ERROR]', err);
  });
});

server.on('listening', () => {
  const address = server.address();
  console.log(`[UDP SERVER] Listening on ${address.address}:${address.port}`);
});

server.bind(41234);

// 2. Create UDP Client (Producer)
const client = dgram.createSocket('udp4');
const message = Buffer.from('METRIC:user.login:1|c');

client.send(message, 41234, 'localhost', (err) => {
  if (err) console.error(err);
  else console.log('[UDP CLIENT] Metric packet dispatched.');
  client.close();
});
```

---

## 3. Broadcasting and Multicasting

### 3.1 Network Broadcasting (`socket.setBroadcast(true)`)
Broadcasting sends a datagram to **all devices** on the local subnet (`255.255.255.255`):

```typescript
import dgram from 'node:dgram';

const broadcastSocket = dgram.createSocket('udp4');

broadcastSocket.bind(() => {
  broadcastSocket.setBroadcast(true);
  const msg = Buffer.from('DISCOVERY_BEACON_NODE_1');
  broadcastSocket.send(msg, 41234, '255.255.255.255', () => {
    console.log('[UDP BROADCAST] Discovery beacon transmitted.');
  });
});
```

### 3.2 IP Multicasting (`socket.addMembership`)
Multicasting delivers a single datagram to a specific **multicast group** (IP range `224.0.0.0` to `239.255.255.255`), reducing network bandwidth compared to broadcasting:

```typescript
import dgram from 'node:dgram';

const MULTICAST_ADDR = '230.1.1.1';
const PORT = 41235;

const multicastReceiver = dgram.createSocket({ type: 'udp4', reuseAddr: true });

multicastReceiver.on('message', (msg, rinfo) => {
  console.log(`[MULTICAST MSG] Received from ${rinfo.address}: ${msg.toString()}`);
});

multicastReceiver.bind(PORT, () => {
  multicastReceiver.addMembership(MULTICAST_ADDR);
  console.log(`[MULTICAST] Joined multicast group: ${MULTICAST_ADDR}`);
});
```

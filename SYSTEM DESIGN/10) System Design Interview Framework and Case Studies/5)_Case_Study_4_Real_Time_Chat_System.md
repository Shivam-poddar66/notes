# 5) Case Study 4: Real-Time Chat System (WhatsApp / Slack)

## 1. Requirements & Scope
- **Functional**: 1-on-1 private messaging; Group chats (up to 500 members); Online/offline presence status; Message delivery receipts (Sent, Delivered, Read); Persistent message history.
- **Scale**: 50 Million DAU; Average 40 messages/user/day $\implies$ **2 Billion messages/day**.
- **Calculations**:
  - Message Write QPS: $\frac{2,000,000,000}{86,400} \approx \mathbf{23,000\text{ msgs/sec}}$ (Peak: ~50,000 msgs/sec).
  - Storage: $2\text{B} \times 200\text{ Bytes} = 400\text{ GB/day} \implies \mathbf{146\text{ TB/year}}$.

---

## 2. High-Level Architecture Diagram

```text
[ User Alice (Mobile) ]                      [ User Bob (Mobile) ]
       │ (WebSocket Connection)                     │ (WebSocket Connection)
       ▼                                            ▼
[ Chat Gateway Server 1 ]                    [ Chat Gateway Server 2 ]
       │                                            │
       └──────────────┬─────────────────────────────┘
                      ▼
            [ Redis Presence & Session Map ]
         (User ID -> Gateway Server ID: `user:bob -> srv_2`)
                      │
                      ▼
            [ Kafka Message Bus ]
       (Partitioned by Conversation ID)
                      │
                      ├──────────────────────────┐
                      ▼                          ▼
           [ Chat Consumer Cluster ]    [ Push Notification Svc ]
                      │                  (If Bob is offline)
                      ▼
         [ Message History Database ]
         (Apache Cassandra / ScyllaDB)
```

---

## 3. Core Component Mechanics

### A. WebSockets for Real-Time Bidirectional Communication
- HTTP polling incurs excessive header overhead and latency.
- WebSockets maintain an open, full-duplex TCP connection with a lightweight 2-to-6 byte frame overhead.

### B. Message Routing Across Distributed Gateways
- If Alice is connected to `Gateway 1` and Bob is connected to `Gateway 2`, how does `Gateway 1` forward Alice's message to Bob?
1. Alice sends message payload to `Gateway 1`.
2. `Gateway 1` queries **Redis Session Map**: `GET user:bob:server` $\implies$ returns `Gateway 2`.
3. `Gateway 1` publishes message to a dedicated Redis Pub/Sub channel or internal Kafka topic: `channel:gateway_2`.
4. `Gateway 2` receives the packet and pushes it down Bob's open WebSocket connection.

### C. Why Apache Cassandra / ScyllaDB for Message History?
- **Data Model**:
  ```sql
  CREATE TABLE messages (
      conversation_id UUID,
      message_id TIMEUUID,
      sender_id BIGINT,
      content TEXT,
      created_at TIMESTAMP,
      PRIMARY KEY ((conversation_id), message_id)
  ) WITH CLUSTERING ORDER BY (message_id DESC);
  ```
- **Why this works brilliantly**:
  - Partition Key: `(conversation_id)`. All messages within a single conversation are physically stored together sequentially on the same disk node.
  - Clustering Key: `message_id (TIMEUUID)`. Automatically sorted in chronological order.
  - Fetching the last 50 messages of a chat is a single, blazing-fast sequential disk seek!

# 2) Replication Lag and Read-After-Write Consistency

## Executive Overview
In asynchronous Primary-Replica architectures, network delays and heavy write volumes cause replicas to lag behind the primary by milliseconds or even seconds (**Replication Lag**). This creates severe user-facing consistency bugs.

---

## 1. The Classic Replication Lag Disaster

```text
Time 0 ms:   User updates bio: "Senior Engineer at Acme" ──▶ [ Primary DB ] (Written!)
Time 10 ms:  User browser auto-refreshes page
Time 12 ms:  Read query routed to [ Read Replica ] (Lagging by 200 ms!)
Time 15 ms:  User sees old bio: "Student at University"
* User thinks their update was lost, submits again, and files a bug report!
```

---

## 2. Production Consistency Guarantees & Solutions

### A. Read-Your-Own-Writes Consistency (Client Pinning):
- Guarantees that if a user reloads the page, they will always see any updates they submitted themselves.
- **Implementation**:
  1. When a user submits an update, write to the Primary DB and record a timestamp in the user's session: `last_write_timestamp = NOW()`.
  2. For the next **5 to 10 seconds** following any write by that user, route **all read requests from that user directly to the Primary DB**.
  3. Other users' reads continue routing to Read Replicas.

### B. Monotonic Reads:
- Guarantees that if a user sees a particular state, they will never subsequently see an older state.
- **Problem**: User refreshes page at $T_1$, reading from Replica A (lag 50 ms). Refreshes at $T_2$, reading from Replica B (lag 500 ms). Time appears to move backwards!
- **Solution**: Hash the `user_id` to route a specific user consistently to the same replica instance (`hash(user_id) % replica_count`).

### C. Log Sequence Number (LSN) Tracking:
- When the Primary commits a write, it returns the current **Log Sequence Number (LSN)** to the client application.
- When the client issues a subsequent read to a replica, it includes `min_lsn = X`.
- If the replica has not yet caught up to that LSN, it either waits up to 50 ms for replication to arrive or transparently delegates the read to the Primary.

# 5) The Event Loop: Six Phases in Execution Order

## Executive Overview
The Event Loop is the central heartbeat of Node.js. It continuously coordinates non-blocking I/O operations, timers, and callbacks across 6 distinct, sequential phases managed by Libuv. Mastering the exact operational semantics of each phase allows you to reason precisely about asynchronous execution flow, latency, and callback scheduling.

---

## 1. The 6 Event Loop Phases

Each turn (iteration) of the Event Loop is called a **Tick**. During a tick, Libuv steps through 6 distinct phases in exact order:

```text
               ┌───────────────────────────┐
            ┌─>│          timers           │
            │  └─────────────┬─────────────┘
            │  ┌─────────────┴─────────────┐
            │  │     pending callbacks     │
            │  └─────────────┬─────────────┘
            │  ┌─────────────┴─────────────┐
            │  │    idle, prepare phase    │
            │  └─────────────┬─────────────┘
            │  ┌─────────────┴─────────────┐
            │  │         poll phase        │
            │  └─────────────┬─────────────┘
            │  ┌─────────────┴─────────────┐
            │  │        check phase        │
            │  └─────────────┬─────────────┘
            │  ┌─────────────┴─────────────┐
            └──│      close callbacks      │
               └───────────────────────────┘
```

---

## 2. Deep Dive into Each Phase

### Phase 1: Timers Phase
- **What executes**: Callbacks scheduled by `setTimeout()` and `setInterval()`.
- **How it works**: Timers are stored in an internal min-heap ordered by expiry timestamp. Libuv checks if the current time $\ge$ scheduled expiration.
- **Key Caveat**: Node.js does *not* guarantee exact execution at millisecond $X$; it guarantees execution *no earlier than* millisecond $X$, subject to OS scheduling and poll phase delays.

### Phase 2: Pending Callbacks (I/O Callbacks) Phase
- **What executes**: Callbacks deferred from previous loop iterations.
- **Examples**: System-level network errors. For example, if a TCP socket receives `ECONNREFUSED` during an asynchronous connection attempt, some UNIX systems queue the error callback in this phase.

### Phase 3: Idle, Prepare Phase
- **What executes**: Used exclusively by internal Node.js C++ subsystems for housekeeping.
- No user JavaScript callbacks execute here.

### Phase 4: Poll Phase (The Core Engine)
- **Two Primary Functions**:
  1. Calculate how long to block and poll for new OS I/O events.
  2. Process events in the poll queue (incoming HTTP connections, reading file chunks, database responses).
- **Poll Phase Decision Logic**:
  ```text
  Is the Poll Queue non-empty?
  ├── YES: Execute callbacks synchronously in order until queue is empty or system limit reached.
  └── NO:
      ├── Are there setImmediate() scripts in Check Phase?
      │   └── YES: Exit Poll Phase immediately and proceed to Check Phase.
      └── NO:
          ├── Are there expired timers?
          │   └── YES: Wrap around to Timers Phase.
          └── NO: Block (sleep) and wait for incoming OS I/O events up to the next timer's threshold.
  ```

### Phase 5: Check Phase
- **What executes**: Callbacks scheduled specifically by `setImmediate()`.
- **Purpose**: Allows developers to execute code immediately after the poll phase finishes processing all active I/O events.

### Phase 6: Close Callbacks Phase
- **What executes**: Cleanup and teardown callbacks registered with `.on('close', ...)`.
- **Example**: If a socket is abruptly destroyed (`socket.destroy()`), the `'close'` event callback is executed here to release underlying file descriptors.

---

## 3. Loop Termination: Active Handles & Active Requests

How does Node.js know when to keep running and when to exit the process?

Libuv tracks two internal counters:
1. **Active Handles (`uv_handle_t`)**: Long-lived resources that represent persistent work (e.g., an open TCP server listening on port 3000, active `setInterval` timer, open file descriptor).
2. **Active Requests (`uv_req_t`)**: Short-lived operations (e.g., a pending `fs.readFile` request, an in-flight DNS query).

```javascript
// Keeping the process alive vs allowing it to exit
const timer = setInterval(() => {
  console.log('Heartbeat');
}, 1000);

// .unref() decrements the active handle count:
// The timer will keep running, BUT if no other work exists, Node will exit!
timer.unref();

// .ref() adds it back to the active handle count:
timer.ref();
```

When `active_handles == 0` and `active_requests == 0`, the Event Loop ends and the Node.js process terminates with exit code `0`.

---

## 4. Phase Execution Summary Table

| Phase | Purpose | Callback Sources |
| :--- | :--- | :--- |
| **1. Timers** | Executes expired timer callbacks | `setTimeout()`, `setInterval()` |
| **2. Pending** | Executes deferred I/O callbacks | OS socket errors (`ECONNREFUSED`, `EPIPE`) |
| **3. Idle/Prepare**| Internal Node/Libuv coordination | Internal C++ only |
| **4. Poll** | Retrieves I/O events; executes I/O code | Network data, File reads, DB responses |
| **5. Check** | Runs immediately after I/O poll completes | `setImmediate()` |
| **6. Close** | Runs cleanup on closed resources | `socket.on('close')`, `server.close()` |

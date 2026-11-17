# 5) Redis Architecture, Internals, and Data Structures

## Executive Overview
Redis (Remote Dictionary Server) is an open-source, in-memory key-value data structure store used as a database, cache, message broker, and streaming engine.

---

## 1. Why is Redis Single-Threaded Yet Exceptionally Fast (100k+ QPS)?

```text
┌────────────────────────────────────────────────────────┐
│                   Redis Event Loop                     │
│                                                        │
│  [ Client 1 Socket ] ──┐                               │
│  [ Client 2 Socket ] ──┼──▶ [ I/O Multiplexer ]        │
│  [ Client 3 Socket ] ──┘     (epoll / kqueue)          │
│                                     │                  │
│                                     ▼                  │
│                             [ Event Queue ]            │
│                                     │                  │
│                                     ▼                  │
│                         [ Single-Threaded Core ]       │
│                         - Command Dispatcher           │
│                         - RAM Data Structure Ops       │
└────────────────────────────────────────────────────────┘
```

1. **In-Memory Operations**: RAM access time (~100 ns) has zero disk seek overhead.
2. **I/O Multiplexing (Reactor Pattern)**: Uses non-blocking OS system calls (`epoll` on Linux, `kqueue` on macOS) to listen to tens of thousands of active socket file descriptors without dedicating a thread per socket.
3. **Zero Lock Contention**: A single thread executing commands sequentially means no mutex locking, semaphores, or context switching overhead. *(Redis 6.0+ introduced multi-threaded network I/O for socket reading/writing, but command execution remains strictly single-threaded and atomic).*

---

## 2. Core Redis Data Structures Deep Dive

```text
┌─────────────────┬──────────────────────┬─────────────┬────────────────────────────────────┐
│ Data Structure  │ Underlying C Type    │ Time Comp.  │ Real-World System Design Use Case  │
├─────────────────┼──────────────────────┼─────────────┼────────────────────────────────────┤
│ String          │ SDS (Simple Dynamic) │ O(1)        │ Session tokens, HTML caching, locks│
│ Hash            │ Listpack / HashTable │ O(1) field  │ User profile entities, cart items  │
│ List            │ Quicklist (ziplist)  │ O(1) ends   │ FIFO queues, activity timelines    │
│ Set             │ IntSet / HashTable   │ O(1)        │ Unique tags, IP blacklists, mutuals│
│ Sorted Set(ZSET)│ SkipList + Hash Table│ O(log N)    │ Leaderboards, sliding rate limiters│
│ Bitmap          │ String bit offsets   │ O(1)        │ DAU/MAU user activity flags        │
│ HyperLogLog     │ Probabilistic matrix │ O(1)        │ Counting 100M unique visitors (12KB│
│ Stream          │ Radix Tree           │ O(1) append │ Message streaming, Kafka-lite logs │
└─────────────────┴──────────────────────┴─────────────┴────────────────────────────────────┘
```

### Deep Dive: Sorted Set (ZSET) & SkipList Mechanics
A **SkipList** is a probabilistic alternative to balanced trees. It maintains multiple layers of linked lists where higher layers "skip" over elements:

```text
Layer 2: [ 10 ] ─────────────────────────▶ [ 50 ] ─────────▶ NULL
Layer 1: [ 10 ] ──────────▶ [ 30 ] ──────▶ [ 50 ] ─────────▶ NULL
Layer 0: [ 10 ] ──▶ [ 20 ] ──▶ [ 30 ] ──▶ [ 40 ] ──▶ [ 50 ] ──▶ NULL
```
- Inserting or searching an element takes **$O(\log N)$** time.
- Used in gaming leaderboards (`ZADD leaderboard 950 "Alice"`) and sliding-window rate limiters.

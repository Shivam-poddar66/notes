# 4) Consistent Hashing with Virtual Nodes

## Executive Overview
When routing requests to distributed caches (Memcached/Redis) or partitioning data across database shards, naive modulo hashing ($\text{key} \pmod N$) suffers a catastrophic re-shuffling failure when servers are added or removed. **Consistent Hashing** resolves this by bounding key migrations to at most $\frac{1}{N}$.

---

## 1. The Naive Modulo Hashing Flaw

Suppose we distribute keys across $N = 4$ cache servers using $\text{Server} = \text{hash}(\text{key}) \pmod 4$:
- Key `user_100` $\implies \text{hash} = 100 \implies 100 \pmod 4 = \mathbf{\text{Server } 0}$.
- Key `user_101` $\implies \text{hash} = 101 \implies 101 \pmod 4 = \mathbf{\text{Server } 1}$.

Now suppose Server 3 crashes, reducing the pool to $N = 3$:
- Key `user_100` $\implies 100 \pmod 3 = \mathbf{\text{Server } 1}$ (Re-mapped!)
- Key `user_101` $\implies 101 \pmod 3 = \mathbf{\text{Server } 2}$ (Re-mapped!)

> **Result**: Changing $N \to N-1$ or $N \to N+1$ invalidates nearly **100% of all cached keys**, causing a system-wide Cache Avalanche that crashes the primary database.

---

## 2. Consistent Hashing Mechanics

### The Hash Ring $[0, 2^{32}-1]$:
1. Both **Servers** (using server IP/name) and **Keys** (user ID, cache key) are mapped to positions on a 32-bit circular hash ring using a uniform hash function (e.g., MD5 or MurmurHash3).
2. **Key Assignment Rule**: To locate the server for a key, traverse clockwise on the ring until encountering the first server node.

```text
                           Server A (0°)
                                 ●
                           .           .
                       .                   .
                Key 3                         Key 1
                  *                             *
                .                                 .
       Server D (270°)                       Server B (90°)
           ●                                     ●
             .                                 .
               .                             *
                 .                         Key 2
                   .                   .
                         ●
                   Server C (180°)

Rule:
- Key 1 traverses clockwise -> Handled by Server B
- Key 2 traverses clockwise -> Handled by Server C
- Key 3 traverses clockwise -> Handled by Server D
```

### Impact of Removing a Server:
- If Server B fails, **only the keys between Server A and Server B** (Key 1) are re-routed to Server C.
- Keys on Server C and Server D remain completely unaffected!
- On average, only **$\frac{1}{N}$ fraction of keys move**, where $N$ is the number of servers.

---

## 3. The Non-Uniform Skew Problem & Virtual Nodes (VNodes)

### The Problem:
With only 3 or 4 physical machines on the ring, standard hashing rarely distributes points symmetrically. One server might end up owning 70% of the ring circumference (Hotspot), while another owns only 10%.

### The Solution: Virtual Nodes:
Instead of placing each physical server once on the ring, map each physical server to **100 to 300 Virtual Tokens** (e.g., `ServerA#1`, `ServerA#2`, ..., `ServerA#200`):

```text
[ Ring with Virtual Nodes ]
ServerA#1 ── ServerB#1 ── ServerC#1 ── ServerA#2 ── ServerB#2 ── ServerC#2 ...
```

### Benefits of Virtual Nodes:
1. **Statistically Uniform Distribution**: The standard deviation of load across servers drops to below 5%.
2. **Heterogeneous Hardware Weighting**: A powerful 64-core machine can be assigned 300 virtual nodes, while a 16-core machine is assigned 100 virtual nodes.
3. **Smooth Capacity Expansion**: When a new node is added, it claims a tiny slice of virtual tokens from *all* existing physical nodes evenly, avoiding burdening a single neighbor.

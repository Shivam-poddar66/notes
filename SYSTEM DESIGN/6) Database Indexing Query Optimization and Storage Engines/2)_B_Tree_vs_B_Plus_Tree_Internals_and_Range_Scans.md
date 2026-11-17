# 2) B-Tree vs. B+ Tree Internals and Range Scans

## Executive Overview
Relational database indexes are almost universally implemented as **B+ Trees** rather than standard Binary Search Trees (BST) or basic B-Trees. The physical properties of B+ trees match the hardware characteristics of page-based block storage.

---

## 1. Why Binary Search Trees Fail on Disk

A balanced Binary Search Tree (e.g., Red-Black Tree, AVL) has a branching factor of 2.
- Indexing $1,000,000,000$ (1 Billion) rows requires:
  $$\text{Tree Height} = \log_2(10^9) \approx \mathbf{30\text{ levels}}$$
- In a disk-based system where each node lives on a different page, searching for a single record requires **30 random disk reads** (approx. 300 ms on HDD, 3 ms on SSD)!

---

## 2. The B+ Tree Architecture: Massive Fan-out

A **B+ Tree** is a self-balancing, multi-way search tree with a massive branching factor (fan-out of 100 to 1,000):

```text
                                [ 50 | 100 ]                     <── Level 0 (Root Page: 16 KB)
                                /     |     \
               ┌───────────────┘      │      └───────────────┐
               ▼                      ▼                      ▼
         [ 20 | 35 ]            [ 70 | 85 ]            [ 120 | 140 ]     <── Level 1 (Internal Nodes)
          /    |    \            /    |    \            /     |     \
      ┌──┘     │     └──┐    ...     ...    ...     ...      ...     ...
      ▼        ▼        ▼
   [10,15] ──▶[20,25] ──▶[35,40] ────────────────────────────────────▶   <── Level 2 (Leaf Nodes)
    (All leaf nodes form a Doubly Linked List of actual data / pointers!)
```

### Why B+ Trees are Superior to Standard B-Trees:
1. **Shallow Tree Height (3–4 I/Os Max)**:
   - Since internal nodes only store keys and child pointers (no data rows), hundreds of keys fit in a single 16 KB page.
   - Branching factor $B = 1,000$:
     - Level 1: $1,000$ keys.
     - Level 2: $1,000,000$ keys.
     - Level 3: $\mathbf{1,000,000,000\text{ (1 Billion) records}}$ indexed in **only 3 tree levels**!
   - Every lookup takes at most **3 to 4 page reads**.
2. **Internal Nodes are Pure Navigational Routers**:
   - Storing row data strictly in leaf nodes maximizes the key density of internal nodes, keeping the tree flat.
3. **Doubly Linked Leaf Nodes for Fast Range Queries**:
   - In a standard B-tree, range queries (`WHERE age BETWEEN 25 AND 35`) require costly in-order tree traversals back and forth through root and internal nodes.
   - In a B+ Tree, the engine traverses to the first leaf node matching `age = 25` in $O(\log N)$, and then simply **walks the doubly linked list linearly** across leaf pages until `age > 35`.

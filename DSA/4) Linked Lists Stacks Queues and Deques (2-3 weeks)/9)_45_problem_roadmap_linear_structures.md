# 45 Problem Roadmap: Linear Structures

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Complete a balanced set of 45 high-yield problems so linked lists, stacks, queues, and deques become interview-ready patterns.

## How to Use This Roadmap
- Solve in order unless a problem is too hard for current level.
- For each problem, record:
  - pattern used
  - first failed idea
  - final invariant
  - final complexity
- Re-solve failed problems on D+3, D+7, and D+30.

## Difficulty and Time Targets

| Difficulty | First Attempt Target |
|---|---|
| Easy | 15 to 20 minutes |
| Medium | 30 to 40 minutes |
| Hard | 45 to 60 minutes |

If time exceeds target:
- write the correct approach
- re-code from memory within 24 hours

## Distribution Plan

| Category | Count |
|---|---|
| Linked List Core + Pointer Patterns | 15 |
| Stack + Expression + Monotonic Stack | 15 |
| Queue + Deque + BFS + Topological | 11 |
| Design Focus (LRU/LFU and mixed) | 4 |
| Total | 45 |

## 45-Problem Ordered List

### A) Linked List Core and Pointer Patterns (1-15)
1. Reverse Linked List  
2. Middle of the Linked List  
3. Linked List Cycle  
4. Linked List Cycle II  
5. Merge Two Sorted Lists  
6. Remove Linked List Elements  
7. Remove Nth Node From End of List  
8. Palindrome Linked List  
9. Intersection of Two Linked Lists  
10. Reverse Linked List II  
11. Swap Nodes in Pairs  
12. Odd Even Linked List  
13. Reorder List  
14. Sort List  
15. Reverse Nodes in k-Group  

### B) Stack, Expression, and Monotonic Stack (16-30)
16. Valid Parentheses  
17. Implement Stack using Queues  
18. Min Stack  
19. Evaluate Reverse Polish Notation  
20. Basic Calculator II  
21. Basic Calculator  
22. Daily Temperatures  
23. Next Greater Element I  
24. Next Greater Element II  
25. Online Stock Span  
26. Asteroid Collision  
27. Decode String  
28. Remove K Digits  
29. Largest Rectangle in Histogram  
30. Maximal Rectangle  

### C) Queue, Deque, and BFS-Style Processing (31-41)
31. Implement Queue using Stacks  
32. Design Circular Queue  
33. Number of Recent Calls  
34. Moving Average from Data Stream  
35. Rotting Oranges  
36. 01 Matrix  
37. Open the Lock  
38. Course Schedule II  
39. Sliding Window Maximum  
40. Shortest Subarray with Sum at Least K  
41. Jump Game VI (deque optimization)  

### D) Design and Mixed Advanced (42-45)
42. LRU Cache  
43. LFU Cache  
44. Design Browser History  
45. Flatten Nested List Iterator  

## Weekly Execution Plan (2-3 Weeks)

### Week 1 (Days 1-7)
- Solve problems 1-18.
- Focus:
  - pointer safety
  - dummy node usage
  - stack basics
- End-of-week checkpoint:
  - re-solve 5 failed problems without notes

### Week 2 (Days 8-14)
- Solve problems 19-35.
- Focus:
  - monotonic stack invariants
  - expression parsing
  - queue/deque operations
- End-of-week checkpoint:
  - timed mixed set of 4 medium problems

### Week 3 (Days 15-21, if needed)
- Solve problems 36-45.
- Focus:
  - BFS and topological queue patterns
  - design-heavy structures (LRU/LFU)
- Final checkpoint:
  - solve 3 random medium + 1 hard under interview timing

## Pattern Milestones by Problem Number
- By problem 10:
  - comfortable with slow/fast and dummy nodes
- By problem 20:
  - solid with stack parsing and min stack design
- By problem 30:
  - strong monotonic stack intuition
- By problem 40:
  - confident with queue/deque and BFS layers
- By problem 45:
  - ready for combined design + implementation interviews

## Attempt Protocol (Per Problem)
1. Identify pattern in under 2 minutes.
2. Write invariant before coding.
3. Code and dry-run on 1 normal + 1 edge case.
4. Record:
  - time taken
  - bug type
  - correction rule

## Error Log Template

```text
Problem:
Pattern:
Missed invariant:
Bug type (pointer/index/order/edge):
Fix rule:
Re-solve date:
```

## Common Failure Triggers to Watch
- Pointer rewiring without storing `next`.
- Off-by-one boundaries in stack/queue windows.
- Wrong strict/non-strict inequality in monotonic structures.
- Forgetting to update recency order in cache design.
- BFS visited marking at wrong moment.

## Completion Standard
- [ ] All 45 solved once.
- [ ] At least 15 re-solved from memory.
- [ ] All hard problems re-attempted after 7 days.
- [ ] Personal bug log has fix rules for top 10 mistakes.
- [ ] You can explain each major pattern without code reference.

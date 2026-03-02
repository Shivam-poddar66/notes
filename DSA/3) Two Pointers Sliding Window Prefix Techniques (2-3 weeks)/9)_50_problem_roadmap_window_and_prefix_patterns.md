# 50 Problem Roadmap: Window and Prefix Patterns

Last updated: February 27, 2026  
Phase: Two Pointers Sliding Window Prefix Techniques (Phase 3)

## Goal
Build interview-ready speed and accuracy across two pointers, sliding window, prefix sum, difference array, modulo-prefix, and monotonic deque patterns.

## How to Use This Roadmap
- Solve in listed order.
- Time limit per problem:
  - Easy: 20-25 minutes
  - Medium: 35-45 minutes
  - Hard: 60-75 minutes
- If stuck beyond limit:
  - write approach attempt
  - study editorial
  - re-solve from memory within 24 hours
- Revisit all wrong/skipped problems on Day +7 and Day +30.

## Problem Sequence (1 to 50)
## A) Two Pointers: Same and Opposite Direction (1-12)
1. `LC 26` - Remove Duplicates from Sorted Array (Easy)  
Pattern: same-direction read/write
2. `LC 27` - Remove Element (Easy)  
Pattern: same-direction compaction
3. `LC 283` - Move Zeroes (Easy)  
Pattern: stable in-place movement
4. `LC 344` - Reverse String (Easy)  
Pattern: opposite-direction swap
5. `LC 977` - Squares of a Sorted Array (Easy)  
Pattern: opposite-direction fill from end
6. `LC 125` - Valid Palindrome (Easy)  
Pattern: opposite-direction with filtering
7. `LC 167` - Two Sum II - Input Array Is Sorted (Medium)  
Pattern: sorted pair convergence
8. `LC 11` - Container With Most Water (Medium)  
Pattern: opposite-direction greedy
9. `LC 15` - 3Sum (Medium)  
Pattern: sort + fixed index + two pointers
10. `LC 16` - 3Sum Closest (Medium)  
Pattern: sorted pair adjustment
11. `LC 18` - 4Sum (Medium)  
Pattern: nested loops + two pointers + dedup
12. `LC 42` - Trapping Rain Water (Hard)  
Pattern: opposite-direction with running bounds

## B) Fast/Slow Pointer and Cycle Detection (13-18)
13. `LC 141` - Linked List Cycle (Easy)  
Pattern: Floyd meeting detection
14. `LC 142` - Linked List Cycle II (Medium)  
Pattern: cycle entry after meet
15. `LC 876` - Middle of the Linked List (Easy)  
Pattern: fast/slow midpoint
16. `LC 234` - Palindrome Linked List (Easy)  
Pattern: middle + reverse half + compare
17. `LC 143` - Reorder List (Medium)  
Pattern: middle split + reverse + merge
18. `LC 202` - Happy Number (Easy)  
Pattern: cycle detection on implicit state graph

## C) Fixed-Size Sliding Window (19-26)
19. `LC 643` - Maximum Average Subarray I (Easy)  
Pattern: rolling sum fixed `k`
20. `LC 1343` - Number of Sub-arrays of Size K and Average >= Threshold (Medium)  
Pattern: fixed-window count
21. `LC 1456` - Maximum Number of Vowels in a Substring of Given Length (Medium)  
Pattern: rolling character count
22. `LC 1052` - Grumpy Bookstore Owner (Medium)  
Pattern: fixed boost window
23. `LC 1423` - Maximum Points You Can Obtain from Cards (Medium)  
Pattern: complement fixed window
24. `LC 438` - Find All Anagrams in a String (Medium)  
Pattern: fixed-size frequency matching
25. `LC 567` - Permutation in String (Medium)  
Pattern: fixed-size inclusion check
26. `LC 239` - Sliding Window Maximum (Hard)  
Pattern: fixed window + monotonic deque

## D) Variable-Size Sliding Window (27-36)
27. `LC 3` - Longest Substring Without Repeating Characters (Medium)  
Pattern: at-most window with map/set
28. `LC 159` - Longest Substring with At Most Two Distinct Characters (Medium)  
Pattern: at-most-`k` distinct
29. `LC 340` - Longest Substring with At Most K Distinct Characters (Medium)  
Pattern: generalized distinct window
30. `LC 424` - Longest Repeating Character Replacement (Medium)  
Pattern: window + max-frequency slack
31. `LC 76` - Minimum Window Substring (Hard)  
Pattern: satisfy-and-shrink minimum valid window
32. `LC 209` - Minimum Size Subarray Sum (Medium)  
Pattern: positive-sum shrink
33. `LC 904` - Fruit Into Baskets (Medium)  
Pattern: at-most-2 distinct
34. `LC 1004` - Max Consecutive Ones III (Medium)  
Pattern: at-most-`k` flips
35. `LC 1208` - Get Equal Substrings Within Budget (Medium)  
Pattern: budget-constrained variable window
36. `LC 713` - Subarray Product Less Than K (Medium)  
Pattern: multiplicative window (positive numbers)

## E) Prefix Sum, Modulo, Frequency Map, Difference Array (37-50)
37. `LC 303` - Range Sum Query - Immutable (Easy)  
Pattern: 1D prefix queries
38. `LC 304` - Range Sum Query 2D - Immutable (Medium)  
Pattern: 2D prefix inclusion-exclusion
39. `LC 724` - Find Pivot Index (Easy)  
Pattern: left/right sum via prefix
40. `LC 560` - Subarray Sum Equals K (Medium)  
Pattern: prefix sum + frequency map
41. `LC 525` - Contiguous Array (Medium)  
Pattern: prefix state earliest index
42. `LC 974` - Subarray Sums Divisible by K (Medium)  
Pattern: remainder frequency map
43. `LC 523` - Continuous Subarray Sum (Medium)  
Pattern: repeated remainder + length >= 2
44. `LC 930` - Binary Subarrays With Sum (Medium)  
Pattern: prefix frequency on binary arrays
45. `LC 1248` - Count Number of Nice Subarrays (Medium)  
Pattern: exact-`k` via prefix/atMost
46. `LC 1658` - Minimum Operations to Reduce X to Zero (Medium)  
Pattern: prefix target or longest kept subarray
47. `LC 1524` - Number of Sub-arrays With Odd Sum (Medium)  
Pattern: prefix parity counts
48. `LC 1109` - Corporate Flight Bookings (Medium)  
Pattern: difference array range add
49. `LC 1094` - Car Pooling (Medium)  
Pattern: line sweep / difference array
50. `LC 2536` - Increment Submatrices by One (Medium)  
Pattern: 2D difference array + reconstruction

## 3-Week Execution Plan
### Week 1
- Solve 1-18.
- Focus: pointer invariants, dedup rules, fast/slow proofs.

### Week 2
- Solve 19-36.
- Focus: fixed vs variable window templates, validity and shrink conditions.

### Week 3
- Solve 37-50.
- Focus: prefix state modeling, modulo normalization, range-update tricks.

## Attempt Log Template
Use this for each problem:
- Problem:
- Pattern chosen:
- First wrong assumption:
- Invariant used:
- Final complexity:
- Retry date (D+7):

## Mastery Exit Criteria
- [ ] I can classify problem pattern within 60 seconds.
- [ ] I can write a correct baseline template from memory.
- [ ] I can pass edge cases without repeated debugging loops.
- [ ] I can explain why the solution is `O(n)` (or `O(n log n)` when sorting is needed).
- [ ] I have re-solved all failed problems at least once.

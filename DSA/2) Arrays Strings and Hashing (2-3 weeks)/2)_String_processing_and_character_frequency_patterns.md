# String Processing and Character Frequency Patterns

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Solve string problems quickly using frequency models, indexing tricks, and robust character handling.

## High-Frequency String Problem Types
- Anagram checks and grouping.
- Longest/shortest substring with constraints.
- Palindrome variants.
- Frequency-based transformations.
- Pattern matching with counts and windows.

## Frequency Representation Choices
- Fixed-size array (`26`, `52`, `128`, or `256`) when charset is known.
- `Map` when charset can be large or Unicode-sensitive.
- Bitmask for limited alphabet state compression.

## Core Templates
### Anagram equality
- Count chars in first string.
- Subtract counts using second string.
- All zero -> anagram.

### Character frequency map
- Single pass with `Map` count updates.
- Track max/min frequency conditions.

### Sliding frequency window
- Maintain counts for current window.
- Add right char, remove left char.
- Check validity condition each step.

## Important JavaScript Character Notes
- Strings are UTF-16; not all user-visible characters are one code unit.
- For interview ASCII/lowercase tasks, direct index mapping is usually fine.
- Normalize assumptions early: lowercase only or full Unicode?

## Complexity Rules of Thumb
- Single pass over string length `n`: `O(n)`.
- Frequency array/hash map extra space: `O(k)` where `k` is charset size.
- Sorting-based anagram/grouping methods: `O(n log n)` on each sortable unit.

## Edge Cases to Always Test
- Empty string and single character.
- Repeated single character (`"aaaa..."`).
- Mixed case if not normalized.
- Non-letter symbols if allowed.
- Window size equal to 0 or `n`.

## Common Mistakes
- Using object keys and hitting prototype-related surprises.
- Forgetting to decrement/remove frequency when sliding window shrinks.
- Comparing maps incorrectly without proper key traversal.
- Assuming case-insensitive logic without converting input.

## Mastery Checklist
- [ ] I can implement frequency maps from memory.
- [ ] I can choose array vs map based on charset constraints.
- [ ] I can solve substring frequency-window problems in linear time.
- [ ] I can explain complexity and edge handling clearly.

## Practice Targets
- 25 problems covering anagrams, windows, palindromes, and counts.
- At least 10 medium problems with sliding window frequency constraints.

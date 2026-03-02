# Array Operations and In Place Techniques

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Master array manipulation patterns and know when in-place operations are correct, safe, and optimal.

## Core Operations and Typical Complexity
- Access by index: `O(1)`.
- Update by index: `O(1)`.
- Append (`push`): amortized `O(1)`.
- Remove last (`pop`): `O(1)`.
- Insert/remove at front (`unshift`/`shift`): `O(n)`.
- Insert/remove in middle: `O(n)`.

## What "In Place" Means
- You modify input array using `O(1)` or very small extra memory.
- Usually allowed when problem statement does not require preserving input.
- In interviews, always confirm mutation is acceptable.

## High-Value In-Place Patterns
### Two-pointer overwrite
- Remove duplicates from sorted array.
- Move non-target elements to front.
- Stable compaction of valid entries.

### Swap-based partition
- Dutch National Flag style partitioning.
- Segregate negatives/positives or even/odd values.

### Reverse and rotation
- Reverse full array or subarray.
- Rotate via reverse-three-step method.

### Cyclic placement
- Place value `x` at index `x-1` when domain permits.
- Used in missing/duplicate number style problems.

## Invariant Examples
- "Elements before `write` index are valid and finalized."
- "All elements left of `low` satisfy condition A."
- "Current window `[l..r]` maintains property P."

## Safety Checklist Before In-Place Mutation
- [ ] Are you allowed to modify input?
- [ ] Are pointer bounds always valid?
- [ ] Can swap/update break unprocessed data?
- [ ] Do you need stable order or not?

## JavaScript-Specific Notes
- Avoid `shift` in loops for queue-like use cases.
- Destructuring swap is clear but may have overhead in tight loops.
- Be explicit about integer boundaries and index checks.

## Common Mistakes
- Off-by-one in loop end conditions.
- Forgetting to return new logical length after compaction.
- Double-processing swapped elements.
- Mutating input when caller expects original order preserved.

## Mastery Checklist
- [ ] I can apply two-pointer in-place transformation correctly.
- [ ] I can derive invariants before coding.
- [ ] I can choose between in-place and extra-space approach with reason.
- [ ] I can test edge cases: empty, single, all-equal, all-target.

## Practice Targets
- 20 problems: compaction, partition, rotation, cyclic index placement.
- Re-solve 5 failed in-place problems after D+7.

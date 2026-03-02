# Edge Case Design and Counterexamples

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Build the habit of breaking weak solutions before submission.

## Edge Case Categories
- Empty input.
- Single element.
- Minimum and maximum constraints.
- All values equal.
- Strictly increasing or strictly decreasing order.
- Duplicate-heavy input.
- Negative values and zero handling.
- Overflow-prone values.
- Disconnected/unreachable graph states.

## Counterexample Design Strategy
1. Identify your algorithm assumption.
2. Ask what smallest input breaks that assumption.
3. Construct minimal failing case.
4. Confirm expected output manually.

## Typical Assumption Failures
- "Sorted property always holds."
- "There is always an answer."
- "No duplicates."
- "Greedy local choice always works."
- "Recursion depth is safe."

## Adversarial Input Patterns
- Zig-zag sequences for pointer logic.
- Repeated boundary values.
- Large equal blocks for partition logic.
- Graphs with isolated nodes and cycles.

## Counterexample Notebook Format
- Problem.
- Incorrect assumption.
- Failing input.
- Expected output.
- Why approach failed.
- Fix added.

## Test Design Checklist Before Submit
- [ ] One minimal case.
- [ ] One maximal stress-style case.
- [ ] One structure-specific adversarial case.
- [ ] One random small case verified manually.

## JavaScript-Specific Edge Concerns
- Number precision near `2^53 - 1`.
- Undefined access from out-of-range indexing.
- Mutation of shared references in nested arrays.
- Incorrect default lexical sort for numbers.

## Mastery Checklist
- [ ] I can generate 5 edge tests for a new problem quickly.
- [ ] I can produce at least one counterexample for rejected approaches.
- [ ] I use failing tests to drive fixes systematically.
- [ ] I do not submit without a boundary test pass.

## Practice Targets
- For next 20 problems, write 3 custom edge cases before final submission.
- Weekly challenge: create adversarial tests for 5 old accepted solutions.

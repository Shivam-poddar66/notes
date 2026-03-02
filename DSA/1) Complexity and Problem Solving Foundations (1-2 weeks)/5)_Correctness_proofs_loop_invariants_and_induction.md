# Correctness Proofs, Loop Invariants, and Induction

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Prove that your algorithm is correct, not just fast.

## Why Proof Skills Matter
- Interviewers evaluate reasoning quality, not only final code.
- Proof habits reduce hidden bugs in edge cases.
- Strong proofs speed up debugging by clarifying expected behavior.

## Loop Invariant Framework
For iterative algorithms, prove three parts:
1. Initialization: invariant true before loop starts.
2. Maintenance: if true before an iteration, true after iteration.
3. Termination: when loop ends, invariant implies correctness.

## Typical Invariant Examples
- Binary search: target, if present, is always inside current search range.
- Two pointers on sorted array: discarded pairs cannot contain valid answer.
- Prefix sum loop: `prefix[i]` equals sum of first `i` elements.

## Induction Framework
For recursive definitions or recurrence-based algorithms:
1. Base case is correct.
2. Assume correct for smaller input.
3. Prove correct for current input using assumption.

## Short Proof Template for Interviews
- "I maintain invariant X throughout the loop."
- "Initially X is true because..."
- "Each update preserves X because..."
- "When loop ends, X implies the desired result."

## Correctness vs Complexity
- Correctness answers: "Does it always return the right output?"
- Complexity answers: "How costly is it?"
- You need both for a complete solution defense.

## Common Proof Mistakes
- Using intuition words like "obviously" without argument.
- Forgetting termination condition impact.
- Assuming sortedness/uniqueness without proving or checking.
- Mixing example-based explanation with proof.

## Proof-Driven Debug Checklist
- [ ] What invariant should hold after each iteration?
- [ ] Which update can violate it?
- [ ] What is the smallest input that breaks it?
- [ ] Does termination guarantee final condition?

## Mastery Checklist
- [ ] I can state one clear invariant before coding.
- [ ] I can prove maintenance step for my updates.
- [ ] I can use induction for recursive algorithm correctness.
- [ ] I can defend correctness in under 60 seconds verbally.

## Practice Targets
- 8 problems solved with explicit invariant written first.
- 5 recursive problems with written induction proof.
- 1 peer-style review session where you only validate correctness arguments.

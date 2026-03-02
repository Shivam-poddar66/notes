# Pseudocode and Dry Run Execution

Last updated: February 26, 2026  
Phase: Complexity and Problem Solving Foundations (Phase 1)

## Goal
Reduce implementation bugs by formalizing logic before coding.

## Pseudocode Standards
Your pseudocode should contain:
- Input and output definition.
- Main steps in order.
- Loop boundaries and conditions.
- State updates.
- Return condition.

Keep it language-neutral but precise enough to code directly.

## Recommended Pseudocode Skeleton
1. Parse and validate input.
2. Initialize state/data structures.
3. Iterate through data with explicit invariants.
4. Update answer/state.
5. Return final result.

## Dry Run Method
Use a table with columns:
- Step/line number.
- Key variables.
- Data structure state.
- Invariant status.
- Expected next action.

Run on:
- Small normal case.
- Boundary case.
- Adversarial case.

## Dry Run Signals That Catch Bugs Early
- Variable changes not matching intended invariant.
- Off-by-one loop boundaries.
- Missing update in one branch.
- Incorrect initialization for min/max/answer variables.

## Interview Workflow
1. Write short pseudocode on paper/whiteboard.
2. Dry run one sample input aloud.
3. Convert pseudocode to code with same structure.
4. Re-run dry run against code mentally.

## JavaScript Implementation Cautions
- Ensure comparator in sort.
- Confirm map lookups handle absent keys correctly.
- Avoid mutation confusion when reusing arrays or objects.
- Verify integer division assumptions when using `Math.floor`.

## Common Mistakes
- Skipping pseudocode under time pressure.
- Dry-running only happy path.
- Not tracking all state variables in dry run.
- Changing algorithm structure while coding without revalidation.

## Mastery Checklist
- [ ] I can produce pseudocode for medium problems in under 5 minutes.
- [ ] I can dry-run and catch at least one bug before coding.
- [ ] My final code structure closely follows pseudocode.
- [ ] I use dry run to debug WA before random edits.

## Practice Targets
- 10 medium problems with mandatory pseudocode first.
- For each, maintain one dry-run table in notes.

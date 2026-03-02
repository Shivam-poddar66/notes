# Hash Collision Considerations and Custom Keys

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Understand practical hashing caveats and design robust keys for composite states.

## Collision Basics
- Different inputs may map to same hash bucket.
- Hash tables handle this internally, but worst-case complexity can degrade.
- Interview settings assume average-case `O(1)` with good hashing.

## Why This Topic Still Matters
- Poor key design causes subtle bugs.
- Composite states require deterministic key encoding.
- Language-specific key behavior can break assumptions.

## JavaScript Key Behavior You Must Know
- `Map` keys compare objects by reference, not deep value.
- `{x:1,y:2}` and another `{x:1,y:2}` are different keys unless same object reference.
- For value-based composite keys, serialize deterministically.

## Custom Key Strategies
### Delimited string key
- Example: `key = row + '#' + col`.
- Use delimiter that cannot collide with value format.

### Tuple-to-string canonicalization
- Sort if order-insensitive.
- Join with delimiter after normalization.

### Nested map structure
- `Map<k1, Map<k2, value>>` when structure is naturally hierarchical.

## Key Design Rules
- Deterministic for equal states.
- Distinguishable for different states.
- Cheap to compute in hot loops.
- Reversible if debugging requires decoding.

## Collision-Risk Mistakes
- Concatenating numbers without delimiter (`1,23` vs `12,3` collision).
- Ignoring case/whitespace normalization in string keys.
- Using floating values as exact keys when precision issues exist.

## Security Note (Practical Awareness)
- Adversarial collision attacks exist in real systems.
- For DSA interviews, focus on correctness and average complexity assumptions.

## Mastery Checklist
- [ ] I can choose safe key encoding for composite states.
- [ ] I understand object reference vs value equality in JS maps.
- [ ] I can prevent accidental key collisions from serialization.
- [ ] I can justify hash approach and discuss worst-case caveat briefly.

## Practice Targets
- 8 to 10 problems with composite-state hashing.
- 5 grouping problems using canonical signatures.

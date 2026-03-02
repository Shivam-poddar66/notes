# Matrix and 2D Array Patterns

Last updated: February 26, 2026  
Phase: Arrays Strings and Hashing (Phase 2)

## Goal
Handle matrix traversal and transformation problems without boundary and indexing errors.

## Core 2D Skills
- Row-major and column-major traversal.
- Boundary-safe neighbor exploration.
- In-place transpose/rotation logic.
- Prefix sums in 2D.

## Common Traversal Patterns
- Full scan: nested loops.
- Layer-by-layer traversal (spiral/boundaries).
- Direction-array traversal for grid problems.
- BFS/DFS-compatible neighbor generation.

## Direction Array Standard
Use fixed direction vectors:
- Up, right, down, left.
- Validate bounds before visiting cell.

## In-Place Matrix Transformations
### Transpose square matrix
- Swap `matrix[i][j]` and `matrix[j][i]` for `j > i`.

### Rotate 90 degrees clockwise
- Transpose, then reverse each row.

### Rotate 90 degrees anticlockwise
- Reverse each row/column appropriately, then transpose.

## 2D Prefix Sum
`ps[r][c]` stores sum of rectangle from origin to `(r,c)`.
Range sum query becomes inclusion-exclusion:
- `A - B - C + D`.

## Common Mistakes
- Confusing row count and column count on non-square matrices.
- Re-visiting nodes/cells without seen tracking where needed.
- Off-by-one errors in boundary loops.
- Modifying matrix while still needing original neighbor values.

## JavaScript Notes
- Create independent rows correctly (`Array.from`) to avoid shared references.
- Avoid deep copying large matrices unless required.
- In tight loops, minimize repeated property lookups.

## Mastery Checklist
- [ ] I can implement spiral, transpose, and rotate patterns from memory.
- [ ] I can avoid bounds and dimension mismatch errors.
- [ ] I can apply 2D prefix sums for region queries.
- [ ] I can explain time and extra space complexity for matrix transforms.

## Practice Targets
- 15 matrix traversal/transformation problems.
- 10 grid counting/search problems with robust boundary handling.

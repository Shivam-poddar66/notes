# Fast Input Output and Execution Environment

Last updated: February 26, 2026  
Phase: Setup and DSA Learning System (Phase 0)

## Goal
Make local execution fast and predictable so timed practice is not interrupted by environment issues.

## Fast I/O Checklist
### C++
- Use `ios::sync_with_stdio(false);` and `cin.tie(nullptr);`.
- Avoid `endl` in loops; use `'\n'`.
- Prefer preallocated vectors when size is known.

### Java
- Use `BufferedInputStream` or custom fast scanner.
- Use `StringBuilder` for output accumulation.
- Avoid per-line `System.out.println` in heavy output cases.

### Python
- Use `sys.stdin.buffer.readline` for input.
- Buffer output in a list and `'\n'.join(...)`.
- Avoid unnecessary recursion depth in deep DFS unless handled.

## Local Run Flow
1. Compile (if needed).
2. Run against sample input.
3. Run against custom edge-case input.
4. If available, run random stress test on small constraints.

## Required Scripts
- `run_local`: quick compile + run command.
- `test_samples`: run all sample files and compare expected output.
- `stress_test`: brute-force vs optimized comparison on generated tests.

## Performance Sanity Checks
- A problem with input size around `10^5` should run comfortably under local limits.
- No avoidable TLE causes: slow I/O, repeated allocations, deep copies.
- Memory stays below common platform limits (often 256MB to 1GB).

## Exit Criteria
- [ ] One command runs compile + execute.
- [ ] Sample tests pass.
- [ ] Edge-case tests pass.
- [ ] Stress test is available for at least one template problem.

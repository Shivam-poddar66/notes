# Language Setup and Template Repository

Last updated: February 26, 2026  
Phase: Setup and DSA Learning System (Phase 0)

## Goal
Prepare one reliable coding environment and one reusable template repository for daily DSA practice.

## Decide Your Primary Language
- Pick one main language for all problem solving (C++, Java, or Python).
- Keep one backup language only for interviews where company preference differs.
- Do not switch the primary language during a weekly cycle.

## Minimum Tooling
- Compiler/interpreter installed and version checked.
- Editor with run/build shortcuts.
- Git initialized for snippets and notes.
- Local unit test or quick input-output runner script.

## Suggested Repository Structure
```text
dsa-templates/
  README.md
  templates/
    main_template.*
    fast_io.*
    graph_template.*
    binary_search_template.*
  snippets/
    ds/
    algorithms/
  scripts/
    run_local.*
    stress_test.*
  practice/
    platform_problem_name/
```

## What to Keep in the Main Template
- Fast input/output setup.
- Common type aliases or helper functions.
- Debug macro or logger that can be disabled quickly.
- Empty solve() function plus standard main() wrapper.

## Repository Rules
- One commit per meaningful improvement.
- Include a short note for every new snippet: when to use, complexity, common bug.
- Never paste a full final solution into templates; keep templates generic.

## Day 1 Deliverables
- [ ] Primary language confirmed.
- [ ] Template repo created and pushed (local or remote).
- [ ] Main template compiles and runs with sample input.
- [ ] At least 3 useful snippets added (example: BFS, DSU, binary search helper).

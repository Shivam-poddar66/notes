# Mini Project 2: Simple Module-Based App

## Goal
Build a small app split into clean ESM modules.

## Suggested App
Task manager with modules:
- state module
- validation module
- storage module
- UI/render module
- controller module

## Suggested Structure
```text
module-app/
  index.html
  src/
    main.js
    state.js
    validation.js
    storage.js
    ui.js
    controller.js
```

## Requirements
- Use named exports where possible.
- Keep each module single-purpose.
- No hidden top-level side effects.
- Avoid circular dependencies.

## Learning Outcome
- Understand import/export flow.
- Design small stable module boundaries.
- Debug module resolution and API mismatch issues.

## Done Criteria
- App runs with clear module boundaries.
- Imports are minimal and explicit.
- Feature additions do not require large rewrites.

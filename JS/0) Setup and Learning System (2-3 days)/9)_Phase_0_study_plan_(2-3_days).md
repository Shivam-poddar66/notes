# Phase 0 Study Plan (2-3 Days)

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Executive Summary & Goal

**Phase Goal**: Convert a blank development machine into a production-ready, fully verified JavaScript learning environment with automated tooling, Git version control, browser debugging workflows, and a repeatable 90-minute daily study system within **2 to 3 days** (6–8 total focus hours).

---

## 2. Master Day-by-Day Execution Schedule

```
[DAY 1: Tooling & Terminal]  --->  [DAY 2: DevTools, Git & Architecture]  --->  [DAY 3: Verification & Routine]
```

### Day 1: Runtime Setup, Terminal & Editor Configuration (2.5 – 3 Hours)

#### Session 1.1: Node.js, npm, and nvm Setup (60 mins)
- **Target Note**: [1)_Node_npm_nvm_and_editor_setup.md](./1)_Node_npm_nvm_and_editor_setup.md)
- **Tasks**:
  1. Install `nvm-windows` (Windows) or `nvm` (macOS/Linux).
  2. Install Node.js LTS (`nvm install lts` && `nvm use`).
  3. Verify versions: `node -v` (>= v18/v22 LTS), `npm -v`.
  4. Create test directory and run `npm init -y` with `"type": "module"`.

#### Session 1.2: Terminal Basics & Command Line Navigation (45 mins)
- **Target Note**: [2)_Terminal_basics_for_JS_workflow.md](./2)_Terminal_basics_for_JS_workflow.md)
- **Tasks**:
  1. Practice directory navigation (`cd`, `pwd`, `ls`/`dir`) and file operations (`mkdir`, `rm`).
  2. Execute Node scripts (`node script.js`) and live-reloading watcher (`node --watch`).
  3. Configure environment variables (`process.env`) and resolve a simulated port conflict (`EADDRINUSE`).

#### Session 1.3: VS Code Configuration & Extensions (45 mins)
- **Target Note**: [1)_Node_npm_nvm_and_editor_setup.md](./1)_Node_npm_nvm_and_editor_setup.md)
- **Tasks**:
  1. Install VS Code extensions: ESLint, Prettier, Code Runner, GitLens, Path Intellisense.
  2. Create workspace `.vscode/settings.json` configuring format-on-save and Prettier default formatter.

---

### Day 2: DevTools Debugging, Git Control & Architecture (2.5 – 3 Hours)

#### Session 2.1: Browser DevTools Fundamentals (60 mins)
- **Target Note**: [3)_Browser_DevTools_fundamentals.md](./3)_Browser_DevTools_fundamentals.md)
- **Tasks**:
  1. Practice console logging APIs (`console.table()`, `console.group()`, `console.time()`).
  2. Practice DevTools shortcuts: `$0`, `$_`, `$$()`.
  3. Set line, conditional, and `debugger;` breakpoints in Sources panel. Step through execution (`F10`, `F11`).
  4. Inspect API requests in Network tab and test network throttling.

#### Session 2.2: Git Version Control Setup (60 mins)
- **Target Note**: [4)_Git_basics_for_learning_projects.md](./4)_Git_basics_for_learning_projects.md)
- **Tasks**:
  1. Configure global Git identity (`user.name`, `user.email`) and `core.autocrlf`.
  2. Initialize local repository (`git init`).
  3. Create JavaScript `.gitignore` (ignoring `node_modules/`, `.env`, build outputs).
  4. Practice feature branching (`git switch -c feature/demo`), merging, and Conventional Commits.

#### Session 2.3: Project Structure & Casing Conventions (30 mins)
- **Target Note**: [5)_Project_structure_and_naming_conventions.md](./5)_Project_structure_and_naming_conventions.md)
- **Tasks**:
  1. Review `camelCase`, `PascalCase`, `UPPER_SNAKE_CASE`, and `kebab-case` standards.
  2. Create standard `src/` directory layout with `index.js`, `utils/`, and `tests/`.

---

### Day 3: Note System, Verification & Transition (1.5 – 2 Hours)

#### Session 3.1: Markdown System & Daily Routine (45 mins)
- **Target Notes**: [6)_Markdown_note_taking_system.md](./6)_Markdown_note_taking_system.md) & [7)_Daily_weekly_learning_routine.md](./7)_Daily_weekly_learning_routine.md)
- **Tasks**:
  1. Configure Markdown preview shortcuts in VS Code (`Ctrl+K V`).
  2. Create `MISTAKE_LOG.md` in notes workspace.
  3. Commit to the 90-minute daily study schedule (Active Recall -> Concept -> Code -> Document).

#### Session 3.2: Automated System Verification & Sign-Off (45 mins)
- **Target Notes**: [8)_Environment_verification_checklist.md](./8)_Environment_verification_checklist.md) & [10)_Quick_revision_sheet.md](./10)_Quick_revision_sheet.md)
- **Tasks**:
  1. Write and execute `verify-env.js` diagnostic script. Ensure all tests pass.
  2. Audit all Phase 0 exit checklists.
  3. Complete Phase 0 sign-off and prepare for **Phase 1: JavaScript Fundamentals**.

---

## 3. Deliverables & Required Artifacts

By the end of Day 3, your workspace must contain these 5 verified artifacts:

| Artifact | Location | Purpose |
| :--- | :--- | :--- |
| **Workspace Settings** | `.vscode/settings.json` | Automated formatting & linting rules |
| **Git Configuration** | `.gitignore` & `.git/` | Clean version control excluding `node_modules` |
| **Diagnostic Script** | `verify-env.js` | Automated environment runtime test |
| **Mistake Log** | `MISTAKE_LOG.md` | Long-term bug tracking registry |
| **Notes Repository** | `JS/0) Setup and Learning System/` | Completed topic notes (1 through 10) |

---

## 4. Bottleneck Elimination & Risk Management

If you hit a roadblock during setup, apply these timeboxed fixes:

- **nvm / Node Path Issue**: If command fails after installation, restart terminal session immediately.
- **PowerShell Permission Denied**: Run `Set-ExecutionPolicy RemoteSigned -Scope CurrentUser` in PowerShell Admin.
- **30-Minute Rule**: If stuck on an installation error for >30 minutes, check the troubleshooting table in [8)_Environment_verification_checklist.md](./8)_Environment_verification_checklist.md).

---

## 5. Phase 0 Exit Checklist

- [ ] Node.js LTS (>= v18/v22) and npm installed via `nvm`.
- [ ] Terminal navigation and `process.env` commands tested.
- [ ] VS Code configured with ESLint, Prettier, and format-on-save.
- [ ] DevTools Console and Sources debugger tested with breakpoints.
- [ ] Git repository initialized with identity, `.gitignore`, and commit history.
- [ ] Naming conventions (`camelCase`, `PascalCase`, `kebab-case`) understood.
- [ ] Markdown note-taking workflow and `MISTAKE_LOG.md` established.
- [ ] Daily 90-minute study schedule integrated into routine.
- [ ] `node verify-env.js` executed with 100% passing results.

---

## 6. Quick Phase Summary

```
Total Duration : 2 to 3 Days (6 - 8 Total Hours)
Core Goal      : Verified, Production-Ready JS Learning Environment
Next Phase     : Phase 1: JavaScript Fundamentals (2-4 weeks)
```

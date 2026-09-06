# Setup and Learning System (Phase 0 Master Overview)

Last updated: September 6, 2026  
Target Duration: 2–3 Days (6–8 Total Hours)  
Status: Complete & Fully Documented  

---

## 1. Executive Summary & Chapter Goal

Welcome to **Phase 0: Setup and Learning System**. The goal of this phase is to establish a production-ready, fully verified local JavaScript development environment, master essential developer tooling, and set up a disciplined 90-minute daily study routine before writing complex application code.

### The Four Pillars of Phase 0
1. **Runtime & Tooling**: Node.js LTS, nvm version control, npm package management, and VS Code auto-formatting.
2. **Command Line & Debugging**: Terminal navigation, environment variables, stream redirection, and browser DevTools breakpoint debugging.
3. **Version Control & Architecture**: Git repositories, Conventional Commits, `.gitignore` hygiene, and modular project casing standards.
4. **Learning System**: Plain-text Markdown notes, `MISTAKE_LOG.md` bug tracking, and the 70-20-10 deliberate practice framework.

---

## 2. Topic Navigation & Folder Map

Click any topic link below to access its comprehensive technical guide:

| Topic File | Title | Key Learning Objectives | Target Time |
| :--- | :--- | :--- | :---: |
| [1)_Node_npm_nvm_and_editor_setup.md](./1)_Node_npm_nvm_and_editor_setup.md) | Node, npm, nvm & Editor Setup | Install Node LTS via nvm, configure VS Code ESLint/Prettier, set up `package.json` with `"type": "module"`. | 60 mins |
| [2)_Terminal_basics_for_JS_workflow.md](./2)_Terminal_basics_for_JS_workflow.md) | Terminal Basics for JS Workflow | Master POSIX vs PowerShell commands, `node --watch`, `process.argv`, `process.env`, and port management. | 45 mins |
| [3)_Browser_DevTools_fundamentals.md](./3)_Browser_DevTools_fundamentals.md) | Browser DevTools Fundamentals | Master Elements, Console REPL (`$0`, `$_`), Sources debugger (breakpoints, `F10`/`F11`), and Network throttling. | 60 mins |
| [4)_Git_basics_for_learning_projects.md](./4)_Git_basics_for_learning_projects.md) | Git Basics for Learning Projects | Configure global Git identity, line endings (`autocrlf`), `.gitignore` template, feature branches, and Conventional Commits. | 60 mins |
| [5)_Project_structure_and_naming_conventions.md](./5)_Project_structure_and_naming_conventions.md) | Project Structure & Naming | Master `camelCase`, `PascalCase`, `UPPER_SNAKE_CASE`, `kebab-case`, layered app directories, and path aliases (`@/...`). | 30 mins |
| [6)_Markdown_note_taking_system.md](./6)_Markdown_note_taking_system.md) | Markdown Note-Taking System | Write structured technical documentation with language-tagged code blocks, callouts, tables, and VS Code preview. | 30 mins |
| [7)_Daily_weekly_learning_routine.md](./7)_Daily_weekly_learning_routine.md) | Daily & Weekly Learning Routine | Establish the 90-minute focus block, 70-20-10 practice framework, 7-day schedule, and active `MISTAKE_LOG.md`. | 30 mins |
| [8)_Environment_verification_checklist.md](./8)_Environment_verification_checklist.md) | Environment Verification Checklist | Run automated `verify-env.js` diagnostic script and complete manual topic verification checklists. | 45 mins |
| [9)_Phase_0_study_plan_(2-3_days).md](./9)_Phase_0_study_plan_%282-3_days%29.md) | Phase 0 Study Plan (2-3 Days) | Execute master day-by-day study schedule (Day 1: Tooling, Day 2: DevTools & Git, Day 3: Verification & Routine). | 30 mins |
| [10)_Quick_revision_sheet.md](./10)_Quick_revision_sheet.md) | Quick Revision Sheet | High-yield cheat sheets for nvm, npm, CLI commands, DevTools, Git, casing rules, and emergency troubleshooting. | 30 mins |

---

## 3. Technology Stack & Workspace Configuration

```
                             +-------------------------------+
                             |    DEVELOPMENT WORKSPACE      |
                             +-------------------------------+
                                             |
            +--------------------------------+--------------------------------+
            |                                |                                |
            v                                v                                v
   +------------------+             +------------------+             +------------------+
   |  Node.js & NVM   |             | VS Code & Code   |             |   Git & GitHub   |
   | (v18/v22 LTS,    |             | (ESLint, Prettier|             | (Conventional    |
   | npm 10+, ESM)    |             | formatOnSave)    |             | Commits, .git)   |
   +------------------+             +------------------+             +------------------+
```

### Core Stack Requirements
- **Runtime**: Node.js LTS (v18+ / v22+) managed via `nvm` / `nvm-windows`.
- **Package Manager**: `npm` with native ES Modules (`"type": "module"` in `package.json`).
- **Editor**: Visual Studio Code with ESLint (`dbaeumer.vscode-eslint`), Prettier (`esbenp.prettier-vscode`), and workspace `.vscode/settings.json`.
- **Version Control**: Git with global `user.name`, `user.email`, `core.autocrlf` configured, and standard `.gitignore`.
- **Debugging Suite**: Browser DevTools (Console, Sources, Network, Application panels).

---

## 4. The 90-Minute Daily Study System

To ensure continuous progress without burnout, adhere strictly to the daily 90-minute study pipeline:

```
[00-15m] Active Recall  -->  [15-45m] Concept Study  -->  [45-75m] Hands-On Coding  -->  [75-90m] Documentation & Push
```

1. **Active Recall (00–15m)**: Review yesterday's note summary and recent entries in `MISTAKE_LOG.md`.
2. **Concept Study (15–45m)**: Read targeted MDN documentation and manually type out code snippets.
3. **Hands-On Coding (45–75m)**: Build runnable examples, solve exercises, and enforce the 30-Minute Debugging Rule.
4. **Documentation & Commit (75–90m)**: Summarize key findings in Markdown notes, log bugs in `MISTAKE_LOG.md`, and execute `git push`.

---

## 5. Required Workspace Deliverables & Sign-Off

Before concluding Phase 0, verify that the following 5 workspace artifacts are present and fully operational:

- [x] **`.vscode/settings.json`**: Prettier default formatter and format-on-save configured.
- [x] **`.gitignore`**: Shields `node_modules/`, `.env`, build outputs, and OS noise.
- [x] **`verify-env.js`**: Automated diagnostic script passing 100% of runtime tests.
- [x] **`MISTAKE_LOG.md`**: Active bug tracking registry initialized.
- [x] **Phase 0 Notes**: All 10 topic notes fully documented in `JS/0) Setup and Learning System (2-3 days)/`.

---

## 6. Phase 0 Milestone & Next Step

```
+-----------------------------------------------------------------------------------+
|                           PHASE 0 COMPLETED & VERIFIED                            |
+-----------------------------------------------------------------------------------+
|  Environment Setup : Node.js LTS, NVM, npm, VS Code Configured                    |
|  Dev Tooling       : Terminal Commands, DevTools Debugger, Git Workflows Mastered |
|  Learning System   : Markdown Notes, Mistake Log, 90-Minute Routine Established   |
+-----------------------------------------------------------------------------------+
```

**Next Milestone**: Advance to **Phase 1: JavaScript Fundamentals (2-4 weeks)** beginning with `1)_Statements_expressions_and_variables.md`.

# Quick Revision Sheet - Phase 0 Setup & Learning System

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Node.js, npm & nvm Quick Reference

### NVM (Node Version Manager) Commands
```bash
nvm install --lts      # Download & install latest LTS Node.js version
nvm list               # List all locally installed Node versions
nvm use <version>      # Switch active Node version for current shell session
```

### NPM (Node Package Manager) Commands
```bash
npm init -y            # Initialize project with default package.json
npm i <package>        # Install production dependency
npm i -D <package>     # Install development dependency (linters, test tools)
npm ci                 # Strict clean install matching exact package-lock.json
npx <package>          # Execute package binary on-the-fly without global install
```

### Package Manifest Key Rules
- `"type": "module"` in `package.json` enables native ES Modules (`import`/`export`).
- `^1.2.3` allows patch and minor updates (`< 2.0.0`). `~1.2.3` allows patch updates only (`< 1.3.0`).
- Always commit `package-lock.json`; NEVER commit `node_modules/`.

---

## 2. Terminal Commands & Navigation Matrix

| Action | Bash / Zsh (Linux/macOS) | PowerShell (Windows) |
| :--- | :--- | :--- |
| **Print Directory** | `pwd` | `pwd` or `Get-Location` |
| **List Files** | `ls -la` | `ls` or `dir` |
| **Create Directory** | `mkdir -p src/utils` | `mkdir src\utils` |
| **Create File** | `touch index.js` | `New-Item index.js` |
| **Remove Directory** | `rm -rf node_modules` | `rm -Recurse -Force node_modules` |
| **View File Content** | `cat package.json` | `cat package.json` |
| **Clear Terminal** | `clear` or `Ctrl+L` | `cls` or `clear` |

### Terminal Execution Shortcuts
- `node script.js` : Execute JavaScript file in Node runtime.
- `node --watch script.js` : Execute with automatic file watcher (Node 18.11+).
- `Ctrl + C` : Cancel / interrupt currently running process.
- `Ctrl + R` : Reverse search command history.

---

## 3. Browser DevTools Cheat Sheet

### Console APIs & Utilities
```js
console.table(arrayOfObjects);  // Render array of objects as a table
console.group('Category');       // Group console logs into collapsible section
console.groupEnd();
console.time('TimerName');      // Benchmark execution duration
console.timeEnd('TimerName');
console.assert(condition, msg); // Log error only if condition evaluates to false
```

- `$0` : Reference currently selected DOM element in Elements panel.
- `$_` : Result of the last console evaluation.
- `$$('selector')` : Array shortcut for `document.querySelectorAll()`.

### Debugger Navigation (Sources Panel)
- **`F8` (Cmd+\\)** : Resume execution until next breakpoint.
- **`F10` (Cmd+')** : Step Over current line.
- **`F11` (Cmd+;)** : Step Into function call on current line.
- **`Shift+F11`** : Step Out of current function to caller.

---

## 4. Git Version Control Cheat Sheet

### Global Setup (Run Once)
```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
git config --global init.defaultBranch main
git config --global core.autocrlf true   # Windows (use 'input' on Mac/Linux)
```

### Daily Git Commands
```bash
git status                           # View workspace & staging state
git add .                            # Stage all modified files
git commit -m "feat: add feature"    # Commit with Conventional Commit prefix
git switch -c feature/login          # Create & switch to feature branch
git switch main                      # Switch back to main branch
git merge feature/login              # Merge feature branch into current branch
git push -u origin main              # Push branch to remote GitHub repo
```

### Undoing Mistakes
- Unstage file: `git restore --staged <file>`
- Discard local edits: `git restore <file>`
- Untrack file without deleting from disk: `git rm --cached <file>`
- Undo last commit (keep edits staged): `git reset --soft HEAD~1`

---

## 5. Naming Conventions & Project Architecture

| Symbol Type | Convention | Example |
| :--- | :--- | :--- |
| **Variables & Functions** | `camelCase` | `userAge`, `fetchUserData()` |
| **Booleans** | `camelCase` (`is/has/should`) | `isLoggedIn`, `hasPermission` |
| **Classes & Components** | `PascalCase` | `UserAccount`, `HeaderNavbar.jsx` |
| **Constants & Env Vars** | `UPPER_SNAKE_CASE` | `MAX_RETRIES`, `PORT` |
| **File & Folder Names** | `kebab-case` | `string-utils.js`, `user-services/` |

---

## 6. Emergency Troubleshooting Matrix

| Error / Symptom | Root Cause | 1-Line Solution |
| :--- | :--- | :--- |
| `SyntaxError: Cannot use import statement` | Missing ESM configuration | Add `"type": "module"` to `package.json`. |
| `EADDRINUSE: address already in use :::3000` | Port 3000 occupied by orphaned process | `lsof -i :3000` -> `kill -9 <PID>` (or `Stop-Process` on Win). |
| `nvm: command not found` | Terminal session not restarted after setup | Close and reopen terminal window. |
| PowerShell script execution error | Windows execution policy restrictions | Run `Set-ExecutionPolicy RemoteSigned -Scope CurrentUser`. |
| `MODULE_NOT_FOUND` on Linux CI/CD | Case sensitivity mismatch in import path | Match file casing exactly in `import` string (use `kebab-case`). |

---

## 7. Phase 0 Final Verification Checklist

- [ ] Node.js LTS (v18/v20/v22) and npm verified via `node -v` & `npm -v`.
- [ ] VS Code configured with Prettier, ESLint, and format-on-save.
- [ ] Terminal navigation & environment variables (`process.env`) tested.
- [ ] DevTools breakpoint debugging & `console.table()` tested.
- [ ] Git repository initialized with `.gitignore` shielding `node_modules/` and `.env`.
- [ ] Standard project layout (`src/`, `utils/`, `tests/`) established.
- [ ] Daily 90-minute study routine & `MISTAKE_LOG.md` active.
- [ ] `node verify-env.js` diagnostic script returns 100% PASS.
- [ ] Ready for **Phase 1: JavaScript Fundamentals**!

# Environment Verification Checklist

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Purpose & Overview

Before advancing to Phase 1 (JavaScript Fundamentals), you must verify that your local development environment is fully functional, properly configured, and free of silent errors. A verified environment guarantees that your coding sessions focus on learning JavaScript, not fighting tooling issues.

---

## 2. Automated Environment Diagnostic Script

Run this custom Node.js script to automatically verify your runtime version, module resolution, and file system capabilities.

### `verify-env.js`
```js
import fs from 'node:fs';
import path from 'node:path';

console.log('==================================================');
console.log('   ENVIRONMENT DIAGNOSTIC & VERIFICATION TOOL    ');
console.log('==================================================\n');

const checks = [];

// Check 1: Node Version Test
const nodeVersion = process.version;
const majorVersion = parseInt(nodeVersion.slice(1).split('.')[0], 10);
const isLtsOrModern = majorVersion >= 18;

checks.push({
  Category: 'Node.js Runtime',
  Detail: `Version ${nodeVersion}`,
  Passed: isLtsOrModern,
  Requirement: 'Node.js >= v18.0.0 (LTS recommended)'
});

// Check 2: ES Module Resolution
let esmSupported = false;
try {
  esmSupported = typeof import.meta.url === 'string';
} catch {
  esmSupported = false;
}
checks.push({
  Category: 'Module Resolution',
  Detail: 'ES Modules (import/export)',
  Passed: esmSupported,
  Requirement: 'Native ESM Support'
});

// Check 3: File System I/O Permissions
let fsWritable = false;
const tempFile = path.join(process.cwd(), '.env-test-temp');
try {
  fs.writeFileSync(tempFile, 'verification_pass');
  const content = fs.readFileSync(tempFile, 'utf-8');
  fs.unlinkSync(tempFile);
  fsWritable = content === 'verification_pass';
} catch {
  fsWritable = false;
}
checks.push({
  Category: 'File System I/O',
  Detail: 'Read/Write/Delete in Workspace',
  Passed: fsWritable,
  Requirement: 'User Write Permissions'
});

// Check 4: Async Event Loop Execution
let asyncPassed = false;
const startTime = Date.now();
await new Promise((resolve) => setTimeout(resolve, 50));
const elapsed = Date.now() - startTime;
asyncPassed = elapsed >= 45 && elapsed <= 150;

checks.push({
  Category: 'Event Loop Timers',
  Detail: `setTimeout resolution (${elapsed}ms)`,
  Passed: asyncPassed,
  Requirement: 'Non-blocking I/O Event Loop'
});

// Print Results Table
console.table(checks);

const allPassed = checks.every((c) => c.Passed);

if (allPassed) {
  console.log('\nSUCCESS: All environment diagnostics passed! Your system is ready for Phase 1.');
  process.exit(0);
} else {
  console.error('\nFAILURE: One or more environment checks failed. Review issues above.');
  process.exit(1);
}
```

### Execution Command
```bash
node verify-env.js
```

---

## 3. Comprehensive Verification Matrix

Use this manual checklist to audit your local tools across all Phase 0 topics:

### 1. Node.js, npm, and nvm (`1)_Node_npm_nvm_and_editor_setup.md`)
- [ ] `nvm --version` (or `nvm version` on Windows) returns installed NVM version.
- [ ] `nvm list` shows installed Node versions with an active LTS release highlighted.
- [ ] `node -v` prints `v18.x.x`, `v20.x.x`, or `v22.x.x` (LTS).
- [ ] `npm -v` returns `v9.x.x` or `v10.x.x` without permission warnings.
- [ ] `npm init -y` creates a valid `package.json` with `"type": "module"`.

---

### 2. Terminal Workflow (`2)_Terminal_basics_for_JS_workflow.md`)
- [ ] Able to navigate directories (`cd`, `pwd`), list files (`ls` / `dir`), and create/delete test folders via CLI.
- [ ] Executed Node scripts via `node index.js` and `node --watch index.js`.
- [ ] Read environment variables (`process.env.PORT`) passed via terminal.
- [ ] Checked running processes and resolved port conflicts (`EADDRINUSE`).
- [ ] Mastered shortcuts: `Tab` (autocompletion), `Ctrl+C` (cancel), `Ctrl+L` (clear), `Ctrl+R` (history search).

---

### 3. Browser DevTools (`3)_Browser_DevTools_fundamentals.md`)
- [ ] Opened DevTools using shortcut (`F12` or `Ctrl+Shift+I`).
- [ ] Evaluated console methods: `console.table()`, `console.group()`, `console.time()`.
- [ ] Evaluated DevTools REPL shortcuts: `$0` (selected element) and `$_` (last output).
- [ ] Set line, conditional, and `debugger;` breakpoints in Sources panel.
- [ ] Stepped through execution using `F10` (Step Over) and `F11` (Step Into).
- [ ] Inspected API calls in Network tab and simulated slow network throttling.

---

### 4. Git Version Control (`4)_Git_basics_for_learning_projects.md`)
- [ ] `git --version` returns installed Git version.
- [ ] `git config user.name` and `git config user.email` display your identity.
- [ ] `git config core.autocrlf` is configured (`true` on Windows, `input` on macOS/Linux).
- [ ] Created local repository (`git init`) and committed changes (`git add .`, `git commit`).
- [ ] `.gitignore` contains `node_modules/`, `.env`, and OS noise files.
- [ ] Switched and merged feature branches (`git switch -c feature/test`).

---

### 5. Project Structure & Naming (`5)_Project_structure_and_naming_conventions.md`)
- [ ] Verified file names use `kebab-case.js` and variables use `camelCase`.
- [ ] Boolean variables prefix with `is`, `has`, or `should`.
- [ ] `src/` directory created with `index.js`, `utils/`, and `tests/` subfolders.
- [ ] Path aliases configured in `jsconfig.json` (`@/...`).

---

### 6. Markdown & Learning System (`6)_Markdown_note_taking_system.md` & `7)_Daily_weekly_learning_routine.md`)
- [ ] VS Code configured with *Markdown All in One* and side-by-side preview shortcut (`Ctrl+K V`).
- [ ] Note files use GitHub callout syntax (`> [!NOTE]`, `> [!WARNING]`).
- [ ] Daily 90-minute study window scheduled.
- [ ] `MISTAKE_LOG.md` created in notes workspace.

---

## 4. Troubleshooting Checklist Failures

| Failure Symptom | Cause | Instant Fix |
| :--- | :--- | :--- |
| `nvm: command not found` | PATH variable missing or terminal not restarted | Restart terminal session. Check NVM installation directory. |
| `ERR_REQUIRE_ESM` | Missing `"type": "module"` in `package.json` | Add `"type": "module"` to `package.json` or use `.mjs` file extension. |
| `EACCES: permission denied` | Running npm global commands with system Node | Use `nvm` to manage Node versions within user workspace. |
| PowerShell script execution error | Restrictive Windows PowerShell execution policy | Run `Set-ExecutionPolicy RemoteSigned -Scope CurrentUser` in Admin PowerShell. |
| Prettier not formatting on save | Conflicting default formatters in VS Code | Set `"editor.defaultFormatter": "esbenp.prettier-vscode"` in `.vscode/settings.json`. |

---

## 5. Phase 0 Exit Criteria

You are ready to proceed to **Phase 1: JavaScript Fundamentals** if and only if:
- [ ] All checks in `verify-env.js` output `Passed: true`.
- [ ] You have completed all 8 manual topic verification checklists above.
- [ ] Your learning workspace repository has clean Git history with no untracked `node_modules` or `.env` files.
- [ ] You can explain your daily 90-minute learning routine and mistake log system.

---

## 6. Diagnostic Quick Reference

```bash
# Terminal One-Liner Diagnostics
node -v && npm -v && git --version && git config user.name

# Run Environment Script
node verify-env.js
```

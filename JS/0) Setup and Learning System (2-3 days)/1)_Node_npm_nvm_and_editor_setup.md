# Node, npm, nvm, and Editor Setup

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Overview & Architecture

### What is Node.js?
Node.js is an open-source, cross-platform JavaScript runtime built on Google Chrome's **V8 JavaScript engine** and **libuv**. It enables execution of JavaScript code outside web browsers (server-side apps, CLI tooling, build pipelines).

- **V8 Engine**: Compiles JavaScript code directly into native machine code for fast execution.
- **libuv**: C library that provides asynchronous I/O capabilities based on event loops and OS thread pools.
- **Single-Threaded Event Loop**: Handles non-blocking I/O operations efficiently without spawning OS threads for every incoming request.

### What is npm?
**npm** (Node Package Manager) is the default package manager bundled with Node.js. It consists of:
1. **CLI Tool**: Used to install, update, and manage project dependencies.
2. **Online Registry**: Public/private repository hosting over 2 million JavaScript packages (npmjs.com).
3. **Task Runner**: Executes scripts defined in `package.json`.

### What is nvm (Node Version Manager)?
**nvm** (or `nvm-windows` on Windows) is a tool that allows you to install multiple versions of Node.js on a single machine and switch between them effortlessly.
- **Why use nvm?** Different projects often require different Node.js versions (e.g., a legacy app requiring Node 18 vs a new project using Node 22 LTS). Installing Node directly via binary installers locks you into one global version and often causes system permission errors.

### VS Code Editor Setup
Visual Studio Code is the industry-standard code editor for modern JavaScript development. Configured properly with ESLint, Prettier, and Debugger extensions, it provides instant error reporting, auto-formatting on save, and seamless interactive execution.

---

## 2. Installation & Configuration Guide

### Step 1: Install nvm / nvm-windows
- **Windows**: Download `nvm-setup.exe` from [coreybutler/nvm-windows releases](https://github.com/coreybutler/nvm-windows/releases).
- **macOS / Linux**: Run the official install script:
  ```bash
  curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
  ```

### Step 2: Install Node.js LTS
Open Terminal / PowerShell (as Administrator on Windows for initial setup):
```bash
# Check nvm version
nvm version

# Install latest LTS (Long Term Support) release
nvm install lts

# Or install a specific version
nvm install 22.14.0

# View installed Node versions
nvm list

# Set active Node version for current session/system
nvm use 22.14.0
```

### Step 3: Verify Node.js and npm
```bash
node -v   # Output example: v22.14.0
npm -v    # Output example: 10.9.0
which node # Linux/macOS path
where node # Windows PowerShell path
```

---

## 3. npm Deep Dive: Package Management & Workflows

### Project Initialization
Create a new project directory and initialize `package.json`:
```bash
mkdir my-js-project
cd my-js-project
npm init -y
```

### Understanding `package.json`
```json
{
  "name": "my-js-project",
  "version": "1.0.0",
  "type": "module",
  "main": "index.js",
  "scripts": {
    "start": "node index.js",
    "dev": "node --watch index.js",
    "test": "echo \"Error: no test specified\" && exit 1"
  },
  "dependencies": {},
  "devDependencies": {}
}
```

> **Note**: Setting `"type": "module"` enables native ES Modules (`import`/`export`) instead of CommonJS (`require`).

### Installing Packages
```bash
# Production dependencies (included in production bundle/runtime)
npm install lodash express

# Development dependencies (linters, formatters, test tools)
npm install -D prettier eslint vitest

# Running CLI tools without global installation
npx prettier --write .
```

### Dependency Versioning Rules (SemVer)
| Syntax | Version Rule | Meaning |
| :--- | :--- | :--- |
| `^1.2.3` | **Caret (Default)** | Allows updates to patch and minor versions (`1.x.x`, up to `< 2.0.0`). |
| `~1.2.3` | **Tilde** | Allows updates to patch versions only (`1.2.x`, up to `< 1.3.0`). |
| `1.2.3` | **Exact** | Fixes installation strictly to this exact version. |
| `*` | **Latest** | Installs latest version (not recommended for stability). |

### `package-lock.json` vs `package.json`
- **`package.json`**: Declares direct dependencies and version ranges (`^`, `~`).
- **`package-lock.json`**: Locks down exact nested dependency trees and cryptographic hashes (`integrity`) to ensure deterministic builds across all team machines.
- **Golden Rule**: Always commit `package-lock.json` to version control.

### `npm install` vs `npm ci`
- `npm install`: Computes dependency graph and updates `package-lock.json` if needed.
- `npm ci` (Clean Install): Deletes `node_modules` and strictly installs exact versions from `package-lock.json`. Faster and reproducible in CI/CD environments.

---

## 4. Hands-On Code Examples

### Example 1: Modern Node.js Script (ES Modules)

**`utils.js`**:
```js
export const getSystemInfo = () => {
  return {
    nodeVersion: process.version,
    platform: process.platform,
    arch: process.arch,
    uptimeSeconds: Math.floor(process.uptime()),
  };
};
```

**`index.js`**:
```js
import { getSystemInfo } from './utils.js';

console.log('=== Node.js Runtime Verification ===');
const info = getSystemInfo();

console.table(info);
console.log(`\nStatus: Node.js setup is functioning correctly!`);
```

**Execution Command**:
```bash
node index.js
```

---

### Example 2: CommonJS vs ES Modules Comparison

| Feature | CommonJS (CJS) | ES Modules (ESM) |
| :--- | :--- | :--- |
| **Syntax** | `const fs = require('fs')` / `module.exports = ...` | `import fs from 'fs'` / `export default ...` |
| **Environment** | Legacy Node.js default | Modern JS Standard (Browser & Node 12+) |
| **Loading** | Synchronous (at runtime) | Asynchronous / Static tree-shakable |
| **Config** | `.cjs` extension or default | `.mjs` extension or `"type": "module"` in `package.json` |

---

## 5. VS Code Configuration & Essential Extensions

### Recommended Extensions
1. **ESLint** (`dbaeumer.vscode-eslint`): Real-time code syntax and style checking.
2. **Prettier - Code formatter** (`esbenp.prettier-vscode`): Automatic code formatting on save.
3. **Code Runner** (`formulahendry.code-runner`): Quick single-file script execution via shortcut.
4. **GitLens** (`eamodio.gitlens`): Git blame and line history inline annotations.
5. **Path Intellisense** (`christian-kohler.path-intellisense`): Auto-completes filenames in import statements.

### Recommended Workspace Settings (`.vscode/settings.json`)
Create `.vscode/settings.json` in your project root:
```json
{
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "editor.formatOnSave": true,
  "editor.tabSize": 2,
  "editor.codeActionsOnSave": {
    "source.fixAll.eslint": "explicit"
  },
  "javascript.suggest.completeFunctionCalls": true,
  "files.exclude": {
    "**/node_modules": true
  }
}
```

---

## 6. Common Failure Cases & Debugging Flow

### Case 1: `ERR_REQUIRE_ESM` or `Cannot use import statement outside a module`
- **Symptom**: `SyntaxError: Cannot use import statement outside a module`.
- **Cause**: Using `import` syntax in Node.js without specifying `"type": "module"`.
- **Fix**: Add `"type": "module"` to `package.json` or rename `.js` to `.mjs`.

### Case 2: PowerShell Script Execution Policy Error (Windows)
- **Symptom**: `nvm : File C:\...\nvm.ps1 cannot be loaded because running scripts is disabled on this system.`
- **Fix**: Open PowerShell as Administrator and execute:
  ```powershell
  Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
  ```

### Case 3: Permission Errors (`EACCES` / `EPERM`)
- **Symptom**: `Error: EACCES: permission denied, access '/usr/local/lib/node_modules'` when installing global packages.
- **Fix**: Use `nvm` instead of installing Node via system package managers or installer directly. Avoid running `sudo npm install -g`.

### Case 4: Corrupted `node_modules` / Version Conflicts
- **Symptom**: Bizarre runtime failures or missing modules despite package listed in `package.json`.
- **Fix**: Perform a clean reinstall:
  ```bash
  # Remove node_modules and lockfile
  npx rimraf node_modules package-lock.json
  # Reinstall dependencies
  npm install
  ```

---

## 7. Personal Setup Checklist & Exit Criteria

- [ ] Installed `nvm` (`nvm-windows` on Win / `nvm` on macOS & Linux).
- [ ] Installed latest Node.js LTS via `nvm install lts` and set active (`nvm use`).
- [ ] Verified `node -v` returns LTS version (e.g., v22.x+).
- [ ] Verified `npm -v` runs without permission warnings.
- [ ] Created test project with `npm init -y` and enabled `"type": "module"`.
- [ ] Installed VS Code with ESLint and Prettier extensions configured to format on save.
- [ ] Created `.gitignore` containing `node_modules/` and `.env`.
- [ ] Executed hands-on test script using `node index.js` successfully.

---

## 8. Quick Reference Sheet

```bash
# NVM Commands
nvm install --lts      # Download & install latest LTS Node version
nvm list               # List installed Node versions
nvm use <version>      # Switch active Node version for current shell

# NPM Commands
npm init -y            # Generate default package.json
npm i <pkg>            # Install production package
npm i -D <pkg>         # Install development package
npm ci                 # Clean install exact lockfile versions
npx <package>          # Execute package binary directly without global install
```

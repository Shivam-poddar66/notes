# Terminal Basics for JavaScript Workflow

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Overview & Terminology

### Why Terminal Fluency Matters
Modern JavaScript development relies heavily on the terminal. Whether running dev servers (`node index.js`, `vite`), managing packages (`npm`, `yarn`, `pnpm`), executing git commands, or setting up environment variables, terminal mastery speeds up your feedback loop and enables effective debugging.

### Core Terminology
- **Terminal (Emulator)**: The visual app window where you type commands (e.g., Windows Terminal, VS Code Integrated Terminal, iTerm2, Alacritty).
- **Shell**: The underlying command interpreter that processes your keystrokes and executes commands (e.g., PowerShell, Bash, Zsh, Fish).
- **CLI (Command Line Interface)**: Programs designed to be controlled via command-line text arguments rather than graphical user interfaces (e.g., `node`, `npm`, `git`, `docker`).

---

## 2. Command Cross-Reference Matrix

Different operating systems use different default shells. Below is a handy translation matrix between POSIX shells (Bash / Zsh on macOS/Linux/WSL) and PowerShell (Windows).

| Action | Bash / Zsh (Linux/macOS) | PowerShell (Windows) | Description |
| :--- | :--- | :--- | :--- |
| **Print Directory** | `pwd` | `pwd` or `Get-Location` | Prints absolute path of current directory |
| **List Directory** | `ls -la` | `ls` or `dir` or `Get-ChildItem` | Lists all files including hidden ones |
| **Change Directory** | `cd path/to/dir` | `cd path\to\dir` | Navigates to target directory |
| **Create Directory** | `mkdir -p src/utils` | `mkdir src\utils` | Creates folder structure |
| **Create Empty File** | `touch index.js` | `New-Item index.js` or `echo $null > index.js` | Creates new file |
| **Remove File** | `rm file.txt` | `rm file.txt` or `Remove-Item file.txt` | Deletes file |
| **Remove Folder** | `rm -rf node_modules` | `rm -Recurse -Force node_modules` | Recursively deletes folder |
| **Copy File/Folder** | `cp -r src dist` | `Copy-Item -Recurse src dist` | Copies directory recursively |
| **Move / Rename** | `mv old.js new.js` | `Move-Item old.js new.js` | Renames or moves file |
| **View File Content** | `cat package.json` | `cat package.json` or `Get-Content` | Output contents of file |
| **Search in File** | `grep "main" package.json` | `Select-String "main" package.json` | Filters matching lines |
| **Clear Screen** | `clear` or `Ctrl+L` | `cls` or `clear` | Clears terminal screen |

> **Pro Tip for VS Code**: You can set your default terminal shell in VS Code via `Ctrl+Shift+P` -> `Terminal: Select Default Profile` (e.g., Git Bash, PowerShell, or Zsh).

---

## 3. Key JavaScript Terminal Workflows

### 1. Executing Scripts & Passing Arguments
Run JavaScript files directly outside the browser:
```bash
# Run script directly
node script.js

# Pass custom CLI arguments
node script.js --env=development --port=3000

# Enable built-in file watcher (Node 18.11+)
node --watch script.js
```

### 2. Environment Variables (`process.env`)
Environment variables store configuration parameters outside source code (API keys, ports, DB URLs).

**Setting env variables inline (Bash / Zsh)**:
```bash
PORT=4000 NODE_ENV=development node server.js
```

**Setting env variables in PowerShell**:
```powershell
$env:PORT="4000"; $env:NODE_ENV="development"; node server.js
```

**Cross-platform solution (`cross-env`)**:
To ensure npm scripts run identically on Windows, macOS, and Linux:
```bash
npm install -D cross-env
```
In `package.json`:
```json
{
  "scripts": {
    "start": "cross-env NODE_ENV=production PORT=8080 node server.js"
  }
}
```

### 3. Redirection & Piping Streams
- `>` : Redirect stdout to a file (overwrites file).
  ```bash
  node build.js > build.log
  ```
- `>>` : Append stdout to a file.
  ```bash
  node build.js >> build.log
  ```
- `|` (Pipe) : Send output of one command as input to another.
  ```bash
  cat package.json | grep "version"
  ```

---

## 4. Hands-On Code Examples

### Example 1: Reading CLI Arguments and Env Variables in Node.js

Create a test script `cli_demo.js`:

```js
// cli_demo.js

// Access environment variables
const env = process.env.NODE_ENV || 'development';
const port = process.env.PORT || 3000;

// Access command line arguments (process.argv array)
// Index 0 = node binary path, Index 1 = script file path, Index 2+ = actual arguments
const userArgs = process.argv.slice(2);

console.log('=== Terminal CLI Script Demo ===');
console.log(`Environment : ${env}`);
console.log(`Port        : ${port}`);
console.log(`Arguments   :`, userArgs);

if (userArgs.includes('--help')) {
  console.log('\nUsage: node cli_demo.js [--help] [--version]');
} else {
  console.log('\nScript executed successfully from terminal!');
}
```

**Test execution in terminal**:
```bash
# Test 1: Standard execution
node cli_demo.js

# Test 2: Passing arguments
node cli_demo.js --help

# Test 3: Setting environment variables inline (Bash / Git Bash)
NODE_ENV=production PORT=5000 node cli_demo.js --verbose
```

---

### Example 2: Terminal Task Automation via `package.json`

Define custom scripts in `package.json`:
```json
{
  "name": "terminal-workflow-demo",
  "version": "1.0.0",
  "type": "module",
  "scripts": {
    "clean": "node -e \"import fs from 'fs'; fs.rmSync('./dist', { recursive: true, force: true }); console.log('Cleaned dist folder');\"",
    "build": "npm run clean && mkdir dist && echo \"console.log('Bundle built');\" > dist/bundle.js",
    "start": "node dist/bundle.js"
  }
}
```

**Run scripts via npm**:
```bash
npm run build
npm start
```

---

## 5. Terminal Productivity & Keyboard Shortcuts

Mastering keyboard shortcuts dramatically speeds up CLI operations:

| Shortcut | Action |
| :--- | :--- |
| `Tab` | Auto-complete file/folder paths |
| `Up / Down` Arrows | Navigate through command history |
| `Ctrl + C` | Cancel / Interrupt current running process (SIGINT) |
| `Ctrl + L` | Clear terminal screen (same as `clear` or `cls`) |
| `Ctrl + R` | Interactive reverse history search (type keyword to find past commands) |
| `Ctrl + A` | Move cursor to beginning of line (POSIX) |
| `Ctrl + E` | Move cursor to end of line (POSIX) |
| `Ctrl + W` | Delete word behind cursor (POSIX) |

---

## 6. Common Failure Cases & Debugging Flow

### Case 1: `Command not found` / `Is not recognized as an internal or external command`
- **Symptom**: `bash: nvm: command not found` or `\'node\' is not recognized as an internal or external command`.
- **Cause**: Executable directory is not in your system `$PATH` environment variable, or terminal session was not restarted after installation.
- **Fix**: Restart your terminal window. If issue persists, inspect PATH:
  - **Linux/macOS**: `echo $PATH`
  - **PowerShell**: `$env:PATH`

### Case 2: Port Conflict (`EADDRINUSE: address already in use :::3000`)
- **Symptom**: Dev server fails to start because port 3000 is occupied by a orphaned Node process.
- **Fix (Windows PowerShell)**:
  ```powershell
  # Find process using port 3000
  Get-NetTCPConnection -LocalPort 3000
  # Kill process by PID
  Stop-Process -Id <PID> -Force
  ```
- **Fix (macOS / Linux / Git Bash)**:
  ```bash
  # Find PID using port 3000
  lsof -i :3000
  # Kill process by PID
  kill -9 <PID>
  ```

### Case 3: Space Escaping in File Paths
- **Symptom**: `cd Setup and Learning System` throws error `bash: cd: Setup: No such file or directory`.
- **Cause**: Spaces separate arguments in terminal commands.
- **Fix**: Wrap path in quotes or escape spaces with backslash:
  ```bash
  cd "Setup and Learning System"
  # OR (POSIX)
  cd Setup\ and\ Learning\ System
  ```

---

## 7. Personal Setup Checklist & Exit Criteria

- [ ] Navigated through directories using `cd`, `pwd`, and `ls` / `dir`.
- [ ] Created, renamed, viewed, and removed files/folders via CLI.
- [ ] Executed Node.js scripts using `node script.js` and `node --watch`.
- [ ] Passed command-line flags and read `process.argv` in a Node script.
- [ ] Configured and read `process.env` environment variables.
- [ ] Resolved a port collision (`EADDRINUSE`) by finding and stopping the occupying process.
- [ ] Mastered essential keyboard shortcuts (`Tab`, `Ctrl+C`, `Ctrl+L`, `Ctrl+R`).

---

## 8. Quick Reference Sheet

```bash
# General Navigation
pwd                           # Print current working directory
cd path/to/folder             # Change directory
cd ..                         # Go up one level

# File Management
mkdir project && cd project   # Make folder and enter it
touch app.js                  # Create file (POSIX)
rm -rf node_modules           # Delete folder recursively (POSIX)

# JavaScript / Node Execution
node app.js                   # Run script
node --watch app.js           # Run with auto-restarting watcher
npm run <script-name>         # Run npm script target
npx <tool-name>               # Execute npm tool on the fly
```

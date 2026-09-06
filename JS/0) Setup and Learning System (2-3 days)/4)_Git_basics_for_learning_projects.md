# Git Basics for Learning Projects

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Overview & Architecture

### What is Git?
Git is a distributed version control system (DVCS) that tracks changes in source code over time. Unlike centralized systems, every developer's local machine holds a full, self-contained copy of the repository history.

### The Three States & Three Trees of Git
Understanding Git requires understanding where your code lives at any moment:

```
+-------------------+       git add       +-------------------+     git commit    +-------------------+
| Working Directory | ------------------> |   Staging Area    | ----------------> | Local Repository  |
|  (Unstaged Files) | <------------------ |      (Index)      |                   |   (.git folder)   |
+-------------------+     git restore     +-------------------+                   +-------------------+
                                                                                            |
                                                                                            | git push
                                                                                            v
                                                                                  +-------------------+
                                                                                  | Remote Repository |
                                                                                  | (GitHub / GitLab) |
                                                                                  +-------------------+
```

1. **Working Directory**: Your actual project files on disk where you write code.
2. **Staging Area (Index)**: A pre-commit buffer file holding snapshots of changes staged to be included in the next commit.
3. **Local Repository (`.git`)**: The internal database storing all committed snapshots, branches, tags, and HEAD pointer history.
4. **Remote Repository**: Host on GitHub/GitLab for backup, code sharing, and collaboration.

---

## 2. Initial Setup & Global Configuration

Run these setup commands once when installing Git:

```bash
# 1. Set Identity (appears in commit logs)
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# 2. Set Default Branch Name to 'main'
git config --global init.defaultBranch main

# 3. Configure Line Endings (CRLF vs LF)
# On Windows (converts CRLF on checkout, LF on commit):
git config --global core.autocrlf true

# On macOS / Linux (converts LF on commit):
git config --global core.autocrlf input

# 4. Verify Configuration
git config --list
```

---

## 3. Daily Git Workflow

### 1. Initialize a Repository
```bash
mkdir my-project
cd my-project
git init
```

### 2. The Core Commit Cycle
```bash
# Check status of Working Directory & Staging Area
git status

# Stage specific files (or stage all with 'git add .')
git add index.js package.json

# Commit staged changes with a descriptive message
git commit -m "feat: add user authentication module"
```

### 3. Writing Standard Commit Messages (Conventional Commits)
Using standard prefixes makes your project history clear and professional:

| Prefix | Usage Description | Example |
| :--- | :--- | :--- |
| `feat:` | Adding a new feature | `feat: implement login validation` |
| `fix:` | Fixing a bug | `fix: resolve null check crash in navbar` |
| `docs:` | Documentation updates | `docs: add setup instructions to README` |
| `style:` | Formatting, missing semicolons, no code changes | `style: format utils with prettier` |
| `refactor:` | Code changes without adding features or fixing bugs | `refactor: simplify array processing loop` |
| `test:` | Adding or updating unit tests | `test: add tests for user model` |
| `chore:` | Updating build scripts, npm packages | `chore: update lodash to v4.17.21` |

---

## 4. `.gitignore` Best Practices for JavaScript

Never commit generated dependencies, local configuration secrets, or temporary OS files into version control. Create a `.gitignore` file in your root folder immediately after `git init`.

### Production JavaScript `.gitignore` Template

```gitignore
# Dependencies (NEVER commit node_modules!)
node_modules/
.pnpm-store/

# Environment Variables & Secrets
.env
.env.local
.env.*.local
*.pem

# Build & Dist Outputs
dist/
build/
out/
.next/
coverage/

# OS Noise
.DS_Store
Thumbs.db

# Editor & Tooling Logs
.vscode/*
!.vscode/settings.json
npm-debug.log*
yarn-debug.log*
yarn-error.log*
```

> **Rule of Thumb**: `package.json` and `package-lock.json` MUST be committed, but `node_modules/` must ALWAYS be ignored!

---

## 5. Branching, Merging & Remote Synchronization

Branching allows you to experiment safely without breaking working code on the `main` branch.

### 1. Branch Management Commands
```bash
# Create and switch to a new feature branch (modern syntax)
git switch -c feature/user-profile

# Equivalent legacy command
git checkout -b feature/user-profile

# List all local branches
git branch

# Switch back to main branch
git switch main
```

### 2. Merging Feature Branches
```bash
# 1. Switch to target branch where code will be merged into
git switch main

# 2. Merge feature branch into main
git merge feature/user-profile

# 3. Delete feature branch after successful merge
git branch -d feature/user-profile
```

### 3. Remote Repository (GitHub / GitLab)
```bash
# Link local repository to remote GitHub repository
git remote add origin https://github.com/username/my-project.git

# Push local main branch and set upstream tracking (-u)
git push -u origin main

# Subsequent pushes
git push

# Fetch and merge latest changes from remote
git pull origin main
```

---

## 6. Hands-On End-to-End Walkthrough

```bash
# Step 1: Create project and initialize Git
mkdir js-git-demo && cd js-git-demo
git init

# Step 2: Add .gitignore and README
echo "node_modules/" > .gitignore
echo "# JS Git Demo Project" > README.md
git add .gitignore README.md
git commit -m "chore: initial project setup"

# Step 3: Create feature branch for new code
git switch -c feature/calculator

# Step 4: Write feature code
echo "export const add = (a, b) => a + b;" > math.js
git add math.js
git commit -m "feat: add addition helper in math.js"

# Step 5: Merge feature into main
git switch main
git merge feature/calculator

# Step 6: Verify clean log graph
git log --oneline --graph --all
```

---

## 7. Troubleshooting & Common Failure Cases

### Case 1: Accidental Commit of `node_modules` or `.env`
- **Symptom**: `node_modules` was committed before adding to `.gitignore`.
- **Fix**: Remove tracked files from Git index without deleting local disk copies:
  ```bash
  # Remove from Git tracking
  git rm -r --cached node_modules
  git rm --cached .env

  # Ensure they are listed in .gitignore, then commit
  git add .gitignore
  git commit -m "chore: untrack node_modules and .env"
  ```

### Case 2: Resolving Merge Conflicts
- **Symptom**: `CONFLICT (content): Merge conflict in index.js. Automatic merge failed`.
- **Understanding Conflict Markers**:
  ```js
  <<<<<<< HEAD (Current Branch Changes)
  const port = 3000;
  =======
  const port = 8080;
  >>>>>>> feature/new-port (Incoming Branch Changes)
  ```
- **Fix**:
  1. Open `index.js`, delete conflict markers, and choose the correct code.
  2. Stage resolved file: `git add index.js`.
  3. Complete merge commit: `git commit -m "fix: resolve merge conflict in port config"`.

### Case 3: Undoing Recent Mistakes
- **Unstage a file staged by mistake**:
  ```bash
  git restore --staged index.js
  ```
- **Discard local uncommitted edits in working directory**:
  ```bash
  git restore index.js
  ```
- **Amend last commit (fix message or add forgotten file)**:
  ```bash
  git add forgotten-file.js
  git commit --amend -m "feat: update complete feature code"
  ```
- **Soft Reset (Undo last commit but keep changes in Staging Area)**:
  ```bash
  git reset --soft HEAD~1
  ```
- **Hard Reset (DANGER: Completely wipe last commit & uncommitted changes)**:
  ```bash
  git reset --hard HEAD~1
  ```

---

## 8. Personal Checklist & Exit Criteria

- [ ] Configured global `user.name`, `user.email`, and `core.autocrlf`.
- [ ] Initialized a repository with `git init` and checked status with `git status`.
- [ ] Staged and committed changes using conventional commit messages.
- [ ] Created a valid `.gitignore` ignoring `node_modules/` and `.env`.
- [ ] Created, switched, merged, and deleted feature branches (`git switch -c`).
- [ ] Untracked accidentally staged files using `git rm --cached`.
- [ ] Resolved a merge conflict manually by editing conflict markers.
- [ ] Pushed commits to a remote GitHub repository (`git push -u origin main`).

---

## 9. Quick Reference Cheat Sheet

```bash
# Inspection
git status                           # View workspace state
git log --oneline --graph            # View clean visual commit history
git diff                             # View unstaged changes vs HEAD

# Branching & Merging
git switch -c <branch-name>          # Create and switch to new branch
git switch main                      # Switch back to main branch
git merge <branch-name>              # Merge branch into current branch
git branch -d <branch-name>          # Delete merged branch

# Remote Syncing
git remote add origin <url>          # Attach remote repo
git push -u origin main              # Initial push with upstream tracking
git pull                             # Fetch and merge from remote
```

# Markdown Note-Taking System

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Overview & Core Philosophy

As a software engineer, your notes are your personal documentation system. Storing notes in plain-text **Markdown (`.md`)** ensures that your technical knowledge remains lightweight, future-proof, portable, and version-controlled alongside your code in Git.

### Why Markdown for Technical Learning?
- **Git Native**: Track your learning progress over time using standard commit histories.
- **Fast Searchability**: Search instantly across hundreds of notes using terminal tools (`grep`, `ripgrep`) or VS Code (`Ctrl+Shift+F`).
- **Zero Lock-In**: Plain text files can be opened in any text editor, IDE, or knowledge base (VS Code, Obsidian, Neovim, GitHub).
- **Rich Technical Formatting**: Built-in support for syntax-highlighted code blocks, mathematical equations, tables, and visual callouts.

---

## 2. Markdown Syntax & Formatting Standards

### 1. Headings Hierarchy
Maintain strict heading hierarchy for clean table-of-contents parsing. Never skip levels (e.g. do not jump from `# H1` to `### H3`).

```markdown
# Topic Title (H1 - Reserved for File Title)
## Major Section (H2)
### Sub-topic / Subset (H3)
#### Specific Detail (H4 - Optional)
```

---

### 2. Code Blocks & Syntax Highlighting
Always specify the language identifier (e.g., `js`, `bash`, `json`, `html`, `css`) on fenced code blocks.

````markdown
```js
// Always specify language tag for proper syntax highlighting
const user = { name: "Alex", role: "Developer" };
console.log(`Hello, ${user.name}`);
```
````

---

### 3. GitHub-Style Alert Callouts
Highlight critical warnings, important notes, and tips visually:

```markdown
> [!NOTE]
> Useful context, background information, or underlying mechanisms.

> [!TIP]
> Best practices, shorthand tricks, or performance optimizations.

> [!IMPORTANT]
> Key constraints, essential rules, or required setup steps.

> [!WARNING]
> Common pitfalls, potential runtime errors, or breaking changes.
```

---

### 4. Interactive Task Lists & Tables

```markdown
### Completion Checklist
- [x] Installed Node.js LTS
- [ ] Configured Git user credentials

### Feature Comparison Matrix
| Feature | Var | Let | Const |
| :--- | :---: | :---: | :---: |
| Scope | Function | Block | Block |
| Reassignable | Yes | Yes | No |
```

---

## 3. Standard Developer Note Template

Every topic note in this repository adheres to a unified structure:

```markdown
# [Topic Title]

Last updated: [Date]
Phase: [Phase Name]

---

## 1. Overview & Core Concepts
- High-level mental model and definitions.
- Why this topic matters in real-world applications.

---

## 2. Syntax & Key APIs
- Method signatures, parameters, and return types.
- Code patterns and structural diagrams.

---

## 3. Hands-On Code Examples
- Runnable JavaScript code blocks.
- Input/output logs and step-by-step trace comments.

---

## 4. Common Edge Cases & Debugging
- Real-world failure cases, syntax gotchas, and error messages.
- How to diagnose and resolve errors step-by-step.

---

## 5. Personal Checklist & Exit Criteria
- [ ] Actionable verification checklist items.

---

## 6. Quick Reference Cheat Sheet
- Concise summary table or command cheat sheet.
```

---

## 4. VS Code Tooling & Productivity Workflow

Enhance your Markdown writing efficiency with these recommended VS Code extensions and settings:

### Recommended Extensions
1. **Markdown All in One** (`yzhang.markdown-all-in-one`):
   - Auto-generates Table of Contents (`Ctrl+Shift+P` -> *Create Table of Contents*).
   - Formatting shortcuts (`Ctrl+B` for bold, `Ctrl+I` for italic).
   - Auto-formats list continuations on `Enter`.
2. **markdownlint** (`DavidAnson.vscode-markdownlint`):
   - Lints Markdown syntax for consistency (prevents trailing spaces, bad heading structures).
3. **Foam / Dendron** (Optional):
   - Enables bi-directional wiki-links (`[[another-note]]`) for connecting related concepts.

### Useful VS Code Shortcuts
- `Ctrl + K V` (macOS: `Cmd + K V`): Open Markdown Preview to the side.
- `Ctrl + Shift + V` (macOS: `Cmd + Shift + V`): Toggle full Markdown Preview tab.

---

## 5. Anti-Patterns & Common Note Mistakes

### Anti-Pattern 1: Copy-Pasting Code Without Explanation
- **Bad**: Pasting 100 lines of raw code without comments or context.
- **Good**: Include concise bullet points explaining *why* the code works, highlighting key parameters and edge cases.

### Anti-Pattern 2: Missing Language Identifiers
- **Bad**: Bare fenced code blocks (```) rendering as plain text without syntax highlighting.
- **Good**: Always use explicit tags (```js, ```bash, ```json).

### Anti-Pattern 3: Broken Relative Links
- **Bad**: Hardcoding absolute Windows file paths (`E:\...\note.md`).
- **Good**: Use relative Markdown links (`[Terminal Guide](./2)_Terminal_basics_for_JS_workflow.md)`).

---

## 6. Personal Setup Checklist & Exit Criteria

- [ ] Configured VS Code with Markdown preview shortcuts (`Ctrl+K V`).
- [ ] Applied language tags (`js`, `bash`, `json`) to all code blocks in notes.
- [ ] Used GitHub callouts (`> [!NOTE]`, `> [!WARNING]`) for key takeaways.
- [ ] Maintained relative file links between related topic notes.
- [ ] Ensured heading levels follow strict `H1 -> H2 -> H3` ordering.
- [ ] Verified notes render cleanly in both editor preview and GitHub repository UI.

---

## 7. Quick Reference Syntax Cheat Sheet

```markdown
# Heading 1
## Heading 2
### Heading 3

**Bold Text**
*Italic Text*
`inline code`

[Link Text](path/to/file.md)

- Unordered item
1. Ordered item
- [ ] Task list item

> Quote block / Callout alert

| Table Header 1 | Table Header 2 |
| :--- | :--- |
| Cell Value 1 | Cell Value 2 |
```

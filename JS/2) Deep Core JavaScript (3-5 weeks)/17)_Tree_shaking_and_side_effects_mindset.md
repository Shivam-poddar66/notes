# Tree-Shaking and Side-Effects Mindset

## 1) Tree-Shaking
Bundlers remove unused code when module graph is statically analyzable.

Works best with:
- ESM syntax
- Pure module design
- Minimal side effects

## 2) Side Effects
Side effect means module does work during import.

Example side effect:

```js
console.log("module loaded");
globalState.theme = "dark";
```

## 3) Why Side Effects Hurt
- Harder to predict behavior.
- Harder to optimize bundles.
- Can cause hidden runtime bugs.

## 4) Better Pattern
Export functions and call them explicitly where needed.

```js
export function initTheme(state) {
  state.theme = "dark";
}
```

## 5) Package-Level Hint
`package.json` can include `"sideEffects": false` when safe.
Use only if modules are truly side-effect free.

## 6) Practical Checklist
- Avoid top-level mutation.
- Avoid top-level network calls.
- Keep imports deterministic.

## 7) Quick Practice
1. Refactor side-effect module to pure exported APIs.
2. Compare bundle output with unused exports.
3. Mark safe modules for side-effect free builds.

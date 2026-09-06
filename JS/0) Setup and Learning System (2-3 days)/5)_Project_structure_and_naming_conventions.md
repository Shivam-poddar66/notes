# Project Structure and Naming Conventions

Last updated: September 6, 2026  
Phase: Setup and Learning System (Phase 0)

---

## 1. Overview & Core Philosophy

As JavaScript projects grow from single-file scripts into multi-module applications, consistent folder organization and intuitive naming conventions become vital. 

### Why Structure & Naming Matter
- **Cognitive Efficiency**: Developers can locate files and understand variable roles in seconds without reading implementations.
- **Cross-Platform Compatibility**: Prevents case-sensitivity bugs between Windows (case-insensitive) and Linux servers (case-sensitive).
- **Dependency Health**: Clear structural boundaries prevent circular dependencies (`A -> B -> A`), which cause runtime `undefined` errors.
- **Maintainability**: Makes codebase refactoring predictable and safe.

---

## 2. Universal JavaScript Naming Conventions

Follow these industry-standard casing rules across your codebase:

| Symbol Type | Convention | Example | Notes |
| :--- | :--- | :--- | :--- |
| **Variables & Functions** | `camelCase` | `userAge`, `calculateTotal()` | Standard JS style |
| **Booleans** | `camelCase` (Prefix with `is/has/should/can`) | `isLoggedIn`, `hasPermission` | Clearly indicates boolean state |
| **Classes & Constructors** | `PascalCase` | `UserAccount`, `DatabaseConnection` | Represents instantiable types |
| **Constants & Environment Vars** | `UPPER_SNAKE_CASE` | `MAX_RETRY_COUNT`, `API_BASE_URL` | Immutable global primitives |
| **File Names (Utilities/Modules)**| `kebab-case.js` or `camelCase.js` | `string-utils.js`, `dateFormatter.js` | Consistent lowercase avoids OS mismatch |
| **Component Files** | `PascalCase.jsx` or `PascalCase.js` | `UserProfileCard.jsx` | Matches component class/function name |
| **Test Files** | `[name].test.js` or `[name].spec.js` | `math-utils.test.js` | Recognized automatically by test runners |
| **Folder Names** | `kebab-case` or lowercase | `user-management`, `services` | Avoid spaces and special characters |

---

## 3. Standard Project Directory Layouts

### 1. Small / Learning Project Layout
Best for Phase 0 and Phase 1 fundamental scripts and mini-projects:

```
my-learning-project/
├── src/
│   ├── index.js          # Entry point
│   ├── math-utils.js     # Helper modules
│   └── validator.js      # Validation logic
├── tests/
│   └── validator.test.js # Test suite
├── .gitignore
├── package.json
└── README.md
```

---

### 2. Standard Production App Layout (Layered Architecture)
Separates concerns by technical responsibility (controllers, services, models, utils):

```
my-node-app/
├── .vscode/
│   └── settings.json       # Editor workspace settings
├── dist/                   # Compiled/bundled output (ignored by git)
├── node_modules/           # Installed packages (ignored by git)
├── src/
│   ├── config/             # App configuration & env setup
│   │   ├── db.js
│   │   └── env.js
│   ├── controllers/        # Request handlers / HTTP entry points
│   │   └── user-controller.js
│   ├── middleware/         # Custom server middleware
│   │   └── auth-middleware.js
│   ├── models/             # Database schemas & data models
│   │   └── user-model.js
│   ├── routes/             # API endpoint declarations
│   │   └── user-routes.js
│   ├── services/           # Business logic execution
│   │   └── user-service.js
│   ├── utils/              # Pure utility functions
│   │   ├── date-utils.js
│   │   └── logger.js
│   ├── app.js              # Server app configuration
│   └── index.js            # Main entry point (starts listener)
├── tests/                  # Automated test files
│   ├── unit/
│   └── integration/
├── .env.example            # Environment template file
├── .gitignore              # Ignored files list
├── package.json            # Project manifest
└── README.md               # Documentation
```

---

## 4. Module Export & Import Architecture

### Named Exports vs Default Exports

#### 1. Prefer Named Exports for Utilities and Services
Named exports enforce exact import names, improving auto-complete and tree-shaking while preventing accidental renaming.

**`src/utils/math-utils.js`**:
```js
// GOOD: Named export
export const calculateTax = (amount, rate) => amount * rate;
export const formatCurrency = (val) => `$${val.toFixed(2)}`;
```

**`src/index.js`**:
```js
// GOOD: Explicit named import
import { calculateTax, formatCurrency } from './utils/math-utils.js';
```

#### 2. Reserve Default Exports for Single-Primary-Entity Files
Use default exports primarily for React components or main class modules where one file equals one primary entity.

```js
// src/services/Logger.js
export default class Logger {
  log(msg) { console.log(msg); }
}
```

### Path Aliases (`jsconfig.json`)
Avoid ugly relative import paths (`../../../utils/math.js`) by configuring root path aliases in `jsconfig.json`:

```json
{
  "compilerOptions": {
    "baseUrl": ".",
    "paths": {
      "@/*": ["src/*"]
    }
  }
}
```

Now you can import cleanly from anywhere:
```js
import { calculateTax } from '@/utils/math-utils.js';
```

---

## 5. Anti-Patterns & Common Failure Cases

### Failure Case 1: Case Sensitivity Bug Across Operating Systems
- **Symptom**: Code imports `./Utils/StringHelper.js`. Runs perfectly on Windows local machine, but fails on Linux deployment pipeline with `MODULE_NOT_FOUND`.
- **Cause**: Windows file system is case-insensitive (`utils` == `Utils`), while Linux is case-sensitive!
- **Fix**: Use strictly lowercase `kebab-case` for file names across all operating systems.

### Failure Case 2: The "God File" (`utils.js` / `helpers.js`)
- **Symptom**: A single `utils.js` file grows to 3,000 lines containing database helpers, string formatting, array algorithms, and DOM manipulators.
- **Fix**: Break monolithic utility files into domain-specific files (`string-utils.js`, `date-utils.js`, `array-utils.js`).

### Failure Case 3: Circular Dependencies
- **Symptom**: `user-service.js` imports `auth-service.js`, which in turn imports `user-service.js`. Variable values suddenly evaluate to `undefined` during execution.
- **Fix**: Extract shared types/helpers into a third independent module (`common-types.js`) imported by both.

### Failure Case 4: Ambiguous / Vague Variable Names
- **Bad**: `const data = fetch(); const d = new Date(); const fn = (x) => x * 2;`
- **Good**: `const userProfile = fetch(); const currentDate = new Date(); const doubleValue = (num) => num * 2;`

---

## 6. Personal Setup Checklist & Exit Criteria

- [ ] Applied `camelCase` for variables/functions and `PascalCase` for classes/components.
- [ ] Used boolean prefixes (`is`, `has`, `should`) for all boolean variables.
- [ ] Named global constants in `UPPER_SNAKE_CASE`.
- [ ] Enforced consistent lowercase `kebab-case` or `camelCase` file naming.
- [ ] Separated code into clear directories (`src/`, `config/`, `services/`, `utils/`, `tests/`).
- [ ] Favored named exports for helper utility functions.
- [ ] Configured `jsconfig.json` path aliases for clean imports (`@/...`).
- [ ] Ensured `node_modules` and `.env` are excluded in `.gitignore`.

---

## 7. Quick Reference Summary

```bash
# File Casing Standards
kebab-case.js         : Standard for helper utilities & scripts
PascalCase.jsx        : Standard for UI Components & Classes
[name].test.js        : Standard for automated test files

# Variable Casing Standards
const userAge = 25;                  # camelCase (variables)
const isLoggedIn = true;             # camelCase with boolean prefix
const MAX_LIMIT = 100;               # UPPER_SNAKE_CASE (constants)
class DatabaseConnection {}          # PascalCase (classes)
```

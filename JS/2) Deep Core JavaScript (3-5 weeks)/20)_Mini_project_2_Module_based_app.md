# Mini-Project 2: Modular Architecture Application (ESM Task Manager)

---

## 1) Project Overview & Architectural Goal

The objective of this project is to build a real-world, production-ready application using **pure ECMAScript Modules (ESM)** without relying on heavy frameworks or bundlers.

### Architectural Principles Practiced:
- **Separation of Concerns (SoC)**: Each module handles exactly one domain responsibility.
- **Unidirectional Data Flow**: State flows down into UI; User Actions trigger Controllers to dispatch State updates.
- **Pure Functions & Immutability**: Business logic and validation remain pure and easily testable.
- **Zero Global Pollution**: All dependencies are explicitly imported; nothing leaks into `window`.
- **Side-Effect Free Modules**: Importing a module does not execute unwanted runtime logic.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                     UNIDIRECTIONAL DATA FLOW (ESM)                      │
│                                                                         │
│   [ User Interaction (DOM) ]                                            │
│               │                                                         │
│               ▼                                                         │
│   [ controller.js ] ──▶ [ validation.js ] (Sanitize & Validate)         │
│               │                                                         │
│               ▼                                                         │
│   [ state.js ] ───────▶ [ storage.js ] (Persist to LocalStorage)        │
│          │                                                              │
│          ▼ (Notify Subscribers)                                         │
│   [ ui.js ] (Re-render clean DOM)                                       │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2) Project Directory Tree

```text
modular-task-app/
├── index.html            # Entry HTML loading <script type="module">
├── styles.css            # Clean UI styles
└── src/
    ├── main.js           # Bootstrapper / Application Entry Point
    ├── state.js          # Reactive State Store & Subscriber Registry
    ├── validation.js     # Pure Input Validation & Sanitization
    ├── storage.js        # LocalStorage Persistence Adapter
    ├── ui.js             # DOM Rendering & Template Generators
    └── controller.js     # Action Orchestrator connecting UI & State
```

---

## 3) Module Implementations

---

### Module 1: `src/validation.js` (Pure Input Validation)
Responsible solely for verifying and sanitizing user inputs.

```js
/**
 * src/validation.js
 * Pure validation and string sanitization utilities.
 */

export function sanitizeText(text) {
  if (typeof text !== "string") return "";
  return text
    .trim()
    .replace(/[&<>"']/g, (char) => {
      const map = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" };
      return map[char];
    });
}

export function validateTaskInput(title, priority = "medium") {
  const sanitizedTitle = sanitizeText(title);
  const validPriorities = ["low", "medium", "high"];

  const errors = [];

  if (!sanitizedTitle) {
    errors.push("Task title cannot be empty.");
  } else if (sanitizedTitle.length < 3) {
    errors.push("Task title must be at least 3 characters long.");
  } else if (sanitizedTitle.length > 80) {
    errors.push("Task title cannot exceed 80 characters.");
  }

  if (!validPriorities.includes(priority.toLowerCase())) {
    errors.push("Invalid priority level selected.");
  }

  return {
    isValid: errors.length === 0,
    errors,
    cleanData: {
      title: sanitizedTitle,
      priority: priority.toLowerCase()
    }
  };
}
```

---

### Module 2: `src/storage.js` (Persistence Layer)
Handles reading from and writing to browser storage with error recovery.

```js
/**
 * src/storage.js
 * Storage adapter with JSON serialization and schema safety.
 */

const STORAGE_KEY = "MODULAR_APP_TASKS_V1";

export function loadTasksFromStorage() {
  try {
    const rawData = localStorage.getItem(STORAGE_KEY);
    if (!rawData) return [];
    const parsed = JSON.parse(rawData);
    return Array.isArray(parsed) ? parsed : [];
  } catch (error) {
    console.error("[Storage] Failed to read from localStorage:", error);
    return [];
  }
}

export function saveTasksToStorage(tasks) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(tasks));
    return true;
  } catch (error) {
    console.error("[Storage] Quota exceeded or write failed:", error);
    return false;
  }
}
```

---

### Module 3: `src/state.js` (Reactive State Store)
Maintains application state immutably and notifies subscribers when state changes.

```js
/**
 * src/state.js
 * Centralized immutable store with subscriber notification.
 */
import { loadTasksFromStorage, saveTasksToStorage } from "./storage.js";

// Private module state
let state = {
  tasks: loadTasksFromStorage(),
  filter: "all" // "all" | "active" | "completed"
};

const subscribers = new Set();

export function getState() {
  // Return structured clone to prevent external direct mutations
  return structuredClone(state);
}

export function subscribe(listener) {
  subscribers.add(listener);
  return () => subscribers.delete(listener);
}

function notify() {
  const currentState = getState();
  saveTasksToStorage(currentState.tasks);
  subscribers.forEach((listener) => listener(currentState));
}

// --- Action Reducers / State Mutations ---

export function addTask({ title, priority }) {
  const newTask = {
    id: crypto.randomUUID(),
    title,
    priority,
    completed: false,
    createdAt: new Date().toISOString()
  };

  state = {
    ...state,
    tasks: [newTask, ...state.tasks]
  };

  notify();
  return newTask;
}

export function toggleTask(taskId) {
  state = {
    ...state,
    tasks: state.tasks.map((task) =>
      task.id === taskId ? { ...task, completed: !task.completed } : task
    )
  };

  notify();
}

export function deleteTask(taskId) {
  state = {
    ...state,
    tasks: state.tasks.filter((task) => task.id !== taskId)
  };

  notify();
}

export function setFilter(filterType) {
  state = {
    ...state,
    filter: filterType
  };

  notify();
}
```

---

### Module 4: `src/ui.js` (DOM Rendering & Component Templates)
Responsible solely for converting state into DOM elements.

```js
/**
 * src/ui.js
 * DOM rendering engine and template builders.
 */

export function renderTaskList(container, tasks, currentFilter) {
  container.innerHTML = "";

  const filteredTasks = tasks.filter((task) => {
    if (currentFilter === "active") return !task.completed;
    if (currentFilter === "completed") return task.completed;
    return true;
  });

  if (filteredTasks.length === 0) {
    container.innerHTML = `<div class="empty-state">No tasks found for "${currentFilter}".</div>`;
    return;
  }

  const fragment = document.createDocumentFragment();

  filteredTasks.forEach((task) => {
    const item = document.createElement("li");
    item.className = `task-card ${task.completed ? "completed" : ""} priority-${task.priority}`;
    item.dataset.id = task.id;

    item.innerHTML = `
      <div class="task-info">
        <input type="checkbox" class="task-checkbox" ${task.completed ? "checked" : ""} />
        <span class="task-title">${task.title}</span>
        <span class="priority-badge">${task.priority.toUpperCase()}</span>
      </div>
      <button class="delete-btn" title="Delete Task">&times;</button>
    `;

    fragment.appendChild(item);
  });

  container.appendChild(fragment);
}

export function showValidationErrors(errorContainer, errors = []) {
  errorContainer.innerHTML = "";
  if (errors.length === 0) {
    errorContainer.classList.add("hidden");
    return;
  }

  errorContainer.classList.remove("hidden");
  errorContainer.innerHTML = errors.map((err) => `<p class="error-msg">${err}</p>`).join("");
}
```

---

### Module 5: `src/controller.js` (Action Controller)
Binds user DOM events to state actions.

```js
/**
 * src/controller.js
 * Orchestrates user inputs, validations, and state dispatches.
 */
import { validateTaskInput } from "./validation.js";
import { addTask, toggleTask, deleteTask, setFilter } from "./state.js";
import { showValidationErrors } from "./ui.js";

export function initController({ form, titleInput, priorityInput, errorBox, listContainer, filterButtons }) {
  // 1. Handle Form Submit (Add Task)
  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const result = validateTaskInput(titleInput.value, priorityInput.value);

    if (!result.isValid) {
      showValidationErrors(errorBox, result.errors);
      return;
    }

    showValidationErrors(errorBox, []);
    addTask(result.cleanData);
    titleInput.value = "";
    titleInput.focus();
  });

  // 2. Handle Task Interactions via Event Delegation
  listContainer.addEventListener("click", (event) => {
    const taskCard = event.target.closest(".task-card");
    if (!taskCard) return;

    const taskId = taskCard.dataset.id;

    if (event.target.classList.contains("delete-btn")) {
      deleteTask(taskId);
    } else if (event.target.classList.contains("task-checkbox")) {
      toggleTask(taskId);
    }
  });

  // 3. Handle Filter Buttons
  filterButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      filterButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      setFilter(btn.dataset.filter);
    });
  });
}
```

---

### Module 6: `src/main.js` (Application Bootstrap)
The root entry point that wires all modules together upon `DOMContentLoaded`.

```js
/**
 * src/main.js
 * Application bootstrap and subscriber wiring.
 */
import { getState, subscribe } from "./state.js";
import { renderTaskList } from "./ui.js";
import { initController } from "./controller.js";

document.addEventListener("DOMContentLoaded", () => {
  const elements = {
    form: document.querySelector("#task-form"),
    titleInput: document.querySelector("#task-title"),
    priorityInput: document.querySelector("#task-priority"),
    errorBox: document.querySelector("#error-box"),
    listContainer: document.querySelector("#task-list"),
    filterButtons: document.querySelectorAll(".filter-btn")
  };

  // Wire UI Re-render to State Subscription
  subscribe((state) => {
    renderTaskList(elements.listContainer, state.tasks, state.filter);
  });

  // Initialize Event Controller
  initController(elements);

  // Initial Paint
  const initialState = getState();
  renderTaskList(elements.listContainer, initialState.tasks, initialState.filter);

  console.log("[App] Modular Task Manager initialized successfully.");
});
```

---

### Entry HTML: `index.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modular ESM Task Manager</title>
  <link rel="stylesheet" href="styles.css" />
  <!-- Native ES Module Entry Point -->
  <script type="module" src="./src/main.js"></script>
</head>
<body>
  <main class="app-container">
    <h1>Task Board</h1>

    <div id="error-box" class="hidden"></div>

    <form id="task-form">
      <input type="text" id="task-title" placeholder="Enter task title..." />
      <select id="task-priority">
        <option value="low">Low</option>
        <option value="medium" selected>Medium</option>
        <option value="high">High</option>
      </select>
      <button type="submit">Add Task</button>
    </form>

    <div class="filters">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="active">Active</button>
      <button class="filter-btn" data-filter="completed">Completed</button>
    </div>

    <ul id="task-list"></ul>
  </main>
</body>
</html>
```

---

## 4) Why You Need a Local HTTP Server to Run ESM

If you double-click `index.html` to open it locally via `file://`, the browser will throw a **CORS security error**:
`Access to script at '...' from origin 'null' has been blocked by CORS policy`.

### How to Run Locally:
```bash
# Option 1: npx serve
npx serve .

# Option 2: Python built-in server
python -m http.server 8000

# Option 3: VS Code Live Server extension
```

---

## 5) Summary & Architectural Checklist

```
+────────────────────────────────────────────────────────────────────────────+
|                     MODULAR APP ARCHITECTURAL CHECKLIST                    |
+───────────────────────────+────────────────────────────────────────────────+
| Single Responsibility     | Every file has exactly ONE clear purpose.      |
| Zero Global Leaks         | No variables or functions attached to `window`.|
| Immutable State           | State is read via `structuredClone(state)`.    |
| Dependency Inversion      | Controller receives DOM elements via config.   |
| Pure UI Functions         | UI rendering functions produce clean DOM nodes.|
| LocalStorage Isolation    | Storage errors do not crash application state. |
+───────────────────────────+────────────────────────────────────────────────+
```

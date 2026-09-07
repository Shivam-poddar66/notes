# Mini Project 2: To-Do List Application

## 1) Project Overview & Learning Objectives

In this project, you will build a feature-rich, interactive **To-Do List Application** with LocalStorage persistence. 

### Key Concepts Practiced
- **Arrays & Objects**: Managing state as an array of task objects.
- **Higher-Order Array Methods**: Using `.map()`, `.filter()`, `.find()`, and `.findIndex()`.
- **Immutable State Updates**: Using array spread `[...]` and non-mutating transformation methods.
- **LocalStorage API**: Persisting app data across browser reloads via `JSON.stringify()` and `JSON.parse()`.
- **Event Handling & Delegation**: Efficiently listening for click events on dynamic list items.

---

## 2) Task Object Schema

Each task in the application is represented by an object with the following structure:

```js
{
  id: 1788771234567,               // Unique timestamp ID
  title: "Master JS Fundamentals", // String task title
  completed: false,                // Boolean completion flag
  createdAt: "2026-09-07T21:46:00.000Z" // ISO timestamp string
}
```

---

## 3) Project Directory Structure

```text
todo-app/
├── index.html
├── style.css
└── script.js
```

---

## 4) Complete Implementation Code

### File 1: `index.html`
```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JS Fundamentals - Todo App</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="todo-card">
    <h2>Task Manager</h2>

    <!-- Input Form -->
    <form id="todoForm" class="input-form">
      <input type="text" id="todoInput" placeholder="Add a new task..." required>
      <button type="submit" class="btn-add">Add Task</button>
    </form>

    <!-- Search & Filter Controls -->
    <div class="controls-bar">
      <input type="text" id="searchInput" placeholder="Search tasks..." class="search-input">
      
      <div class="filter-tabs">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="pending">Pending</button>
        <button class="filter-btn" data-filter="completed">Completed</button>
      </div>
    </div>

    <!-- Task Counters -->
    <div class="stats-bar">
      <span>Total: <strong id="totalCount">0</strong></span>
      <span>Completed: <strong id="completedCount">0</strong></span>
    </div>

    <!-- Dynamic Todo List -->
    <ul id="todoList" class="todo-list"></ul>

    <!-- Empty State Message -->
    <div id="emptyState" class="empty-state hidden">No tasks found.</div>
  </div>

  <script src="script.js"></script>
</body>
</html>
```

---

### File 2: `style.css`
```css
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background-color: #f0f2f5;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 20px;
}

.todo-card {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  width: 100%;
  max-width: 500px;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #1a202c;
}

.input-form {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}

#todoInput {
  flex: 1;
  padding: 12px;
  border: 1px solid #cbd5e0;
  border-radius: 6px;
  font-size: 15px;
  outline: none;
}

#todoInput:focus {
  border-color: #3182ce;
}

.btn-add {
  padding: 12px 18px;
  background: #3182ce;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-add:hover {
  background: #2b6cb0;
}

.controls-bar {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 15px;
}

.search-input {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
}

.filter-tabs {
  display: flex;
  gap: 5px;
  background: #edf2f7;
  padding: 4px;
  border-radius: 6px;
}

.filter-btn {
  flex: 1;
  padding: 6px 12px;
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 600;
  color: #4a5568;
  border-radius: 4px;
  cursor: pointer;
}

.filter-btn.active {
  background: white;
  color: #3182ce;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.stats-bar {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: #718096;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #edf2f7;
}

.todo-list {
  list-style: none;
  max-height: 250px;
  overflow-y: auto;
}

.todo-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  border-bottom: 1px solid #f7fafc;
  transition: background 0.2s;
}

.todo-item:hover {
  background: #f7fafc;
}

.todo-content {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
}

.todo-text {
  font-size: 15px;
  color: #2d3748;
}

.todo-item.completed .todo-text {
  text-decoration: line-through;
  color: #a0aec0;
}

.btn-delete {
  background: #feb2b2;
  color: #9b2c2c;
  border: none;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  cursor: pointer;
}

.btn-delete:hover {
  background: #fc8181;
}

.empty-state {
  text-align: center;
  padding: 20px;
  color: #a0aec0;
  font-size: 14px;
}

.hidden {
  display: none;
}
```

---

### File 3: `script.js`
```js
// --- 1. STATE MANAGEMENT ---
const STORAGE_KEY = "js_fundamentals_todos";

let todos = loadFromStorage();
let currentFilter = "all";
let searchQuery = "";

// --- 2. DOM ELEMENT SELECTIONS ---
const todoForm = document.getElementById("todoForm");
const todoInput = document.getElementById("todoInput");
const searchInput = document.getElementById("searchInput");
const todoList = document.getElementById("todoList");
const filterBtns = document.querySelectorAll(".filter-btn");
const totalCountEl = document.getElementById("totalCount");
const completedCountEl = document.getElementById("completedCount");
const emptyStateEl = document.getElementById("emptyState");

// --- 3. STORAGE HELPERS ---
function saveToStorage() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(todos));
}

function loadFromStorage() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (err) {
    console.error("Failed to load todos from LocalStorage:", err);
    return [];
  }
}

// --- 4. CORE STATE OPERATIONS ---
function addTodo(title) {
  const newTodo = {
    id: Date.now(),
    title: title.trim(),
    completed: false,
    createdAt: new Date().toISOString()
  };

  // Immutable addition (spread operator)
  todos = [newTodo, ...todos];
  saveToStorage();
  render();
}

function toggleTodo(id) {
  // Immutable update using .map()
  todos = todos.map(todo => 
    todo.id === id ? { ...todo, completed: !todo.completed } : todo
  );
  saveToStorage();
  render();
}

function deleteTodo(id) {
  // Immutable deletion using .filter()
  todos = todos.filter(todo => todo.id !== id);
  saveToStorage();
  render();
}

function getFilteredTodos() {
  return todos.filter(todo => {
    // Filter by completion status
    const matchesFilter = 
      currentFilter === "all" ? true :
      currentFilter === "completed" ? todo.completed :
      !todo.completed;

    // Filter by search query
    const matchesSearch = todo.title
      .toLowerCase()
      .includes(searchQuery.toLowerCase());

    return matchesFilter && matchesSearch;
  });
}

// --- 5. RENDER CONTROLLER ---
function render() {
  const filteredTodos = getFilteredTodos();

  // Clear list
  todoList.innerHTML = "";

  // Render items
  filteredTodos.forEach(todo => {
    const li = document.createElement("li");
    li.className = `todo-item ${todo.completed ? "completed" : ""}`;
    li.innerHTML = `
      <div class="todo-content">
        <input 
          type="checkbox" 
          class="toggle-checkbox" 
          data-id="${todo.id}" 
          ${todo.completed ? "checked" : ""}
        >
        <span class="todo-text">${escapeHTML(todo.title)}</span>
      </div>
      <button class="btn-delete" data-id="${todo.id}">Delete</button>
    `;
    todoList.appendChild(li);
  });

  // Update counters
  const total = todos.length;
  const completed = todos.filter(t => t.completed).length;
  totalCountEl.textContent = total;
  completedCountEl.textContent = completed;

  // Toggle empty state
  if (filteredTodos.length === 0) {
    emptyStateEl.classList.remove("hidden");
  } else {
    emptyStateEl.classList.add("hidden");
  }
}

// Helper to prevent XSS vulnerability when rendering user strings
function escapeHTML(str) {
  return str.replace(/[&<>'"]/g, 
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}

// --- 6. EVENT LISTENERS & DELEGATION ---

// Add Task Form Submit
todoForm.addEventListener("submit", (e) => {
  e.preventDefault();
  if (todoInput.value.trim() !== "") {
    addTodo(todoInput.value);
    todoInput.value = "";
  }
});

// Event Delegation on Task List (Toggle and Delete)
todoList.addEventListener("click", (e) => {
  const target = e.target;
  const id = Number(target.dataset.id);

  if (!id) return;

  if (target.classList.contains("toggle-checkbox")) {
    toggleTodo(id);
  } else if (target.classList.contains("btn-delete")) {
    deleteTodo(id);
  }
});

// Filter Tab Click Handlers
filterBtns.forEach(btn => {
  btn.addEventListener("click", (e) => {
    filterBtns.forEach(b => b.classList.remove("active"));
    e.target.classList.add("active");
    currentFilter = e.target.dataset.filter;
    render();
  });
});

// Live Search Input Handler
searchInput.addEventListener("input", (e) => {
  searchQuery = e.target.value;
  render();
});

// --- 7. INITIALIZATION ---
render();
```

---

## 5) Done Criteria & Self-Verification Checklist
- [x] Adding a task updates the UI and adds an object to `todos`.
- [x] Toggling a checkbox immutably flips `completed: true/false`.
- [x] Deleting a task immutably removes the object via `.filter()`.
- [x] Filter tabs ("All", "Pending", "Completed") accurately filter displayed items.
- [x] Tasks persist across page refreshes via LocalStorage.

---

## 6) Practice Extensions
Try implementing these features:
1. **Clear Completed**: Add a button to delete all `completed: true` tasks at once using `todos.filter(t => !t.completed)`.
2. **Task Priority**: Add a priority selector (Low, Medium, High) to the data model and sort tasks by priority.
3. **DueDate Expiration**: Add a due date property and highlight overdue pending tasks in red.

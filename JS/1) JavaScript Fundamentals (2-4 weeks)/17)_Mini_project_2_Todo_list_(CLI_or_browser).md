# Mini Project 2: To-Do List (CLI or Browser)

## Goal
Practice arrays, objects, loops, array methods, and state updates.

## Features
- Add task.
- List tasks.
- Mark task complete.
- Delete task.
- Filter: all/completed/pending.

## Data Model
```js
{
  id: 1,
  title: "Read JS chapter",
  completed: false,
  createdAt: "2026-02-25T11:00:00.000Z"
}
```

## Suggested Structure (Browser)
```text
todo/
  index.html
  style.css
  script.js
```

## Core Operations
- `addTask(title)`
- `toggleTask(id)`
- `deleteTask(id)`
- `getFilteredTasks(filter)`

## Key Concepts Used
- `map`, `filter`, `find`, `some`.
- Immutable updates with spread.
- Event handling and rendering loop.

## Extensions
- Persist tasks in `localStorage`.
- Add due date.
- Add simple search.

## Done Criteria
- CRUD operations stable.
- Filter behavior correct.
- Works after refresh (if storage enabled).

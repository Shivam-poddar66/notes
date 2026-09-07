# Mini Project 3: Personal Expense Tracker

## 1) Project Overview & Learning Objectives

In this project, you will build a complete **Personal Expense Tracker Application** that manages income and expenses, calculates live financial statistics, and persists data across browser sessions.

### Key Concepts Practiced
- **Data Reduction (`Array.prototype.reduce`)**: Computing net balance, total income, and total expenses in a single pass.
- **Data Validation & Error Throwing**: Enforcing positive amounts, required categories, and catching invalid user inputs.
- **Internationalization (`Intl.NumberFormat`)**: Formatting numbers into localized currency strings (e.g., `$1,250.00` or `₹1,250.00`).
- **Date Handling (`Date` / `Intl.DateTimeFormat`)**: Formatting ISO timestamp strings into readable date displays.
- **LocalStorage Synchronization**: Persisting financial transaction histories across browser refreshes.

---

## 2) Transaction Object Schema

Each financial entry is stored as an object matching this schema:

```js
{
  id: 1788771234567,              // Unique timestamp ID
  type: "income",                  // "income" or "expense"
  amount: 1500.50,                 // Positive numeric amount
  category: "Salary",              // String category name
  note: "Monthly salary payment",  // Optional description note
  createdAt: "2026-09-07T21:46:00.000Z" // ISO Date string
}
```

---

## 3) Project Directory Structure

```text
expense-tracker/
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
  <title>JS Fundamentals - Expense Tracker</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="tracker-container">
    <h2>Expense Tracker</h2>

    <!-- Financial Dashboard Cards -->
    <div class="summary-cards">
      <div class="card balance-card">
        <h3>Net Balance</h3>
        <p id="netBalance">$0.00</p>
      </div>
      <div class="card income-card">
        <h3>Total Income</h3>
        <p id="totalIncome">$0.00</p>
      </div>
      <div class="card expense-card">
        <h3>Total Expenses</h3>
        <p id="totalExpense">$0.00</p>
      </div>
    </div>

    <!-- Transaction Form -->
    <form id="transactionForm" class="transaction-form">
      <h3>Add Transaction</h3>
      
      <div class="form-row">
        <select id="type" required>
          <option value="expense">Expense (-)</option>
          <option value="income">Income (+)</option>
        </select>
        
        <input type="number" id="amount" placeholder="Amount (e.g. 45.00)" step="0.01" required>
      </div>

      <div class="form-row">
        <select id="category" required>
          <option value="" disabled selected>Select Category</option>
          <option value="Salary">Salary</option>
          <option value="Freelance">Freelance</option>
          <option value="Food">Food & Dining</option>
          <option value="Rent">Rent & Housing</option>
          <option value="Utilities">Utilities & Bills</option>
          <option value="Entertainment">Entertainment</option>
          <option value="Other">Other</option>
        </select>

        <input type="text" id="note" placeholder="Description / Note">
      </div>

      <button type="submit" class="btn-submit">Add Transaction</button>
      <p id="errorMessage" class="error-msg hidden"></p>
    </form>

    <!-- History & Filtering -->
    <div class="history-container">
      <div class="history-header">
        <h3>Transaction History</h3>
        
        <select id="categoryFilter">
          <option value="all">All Categories</option>
          <option value="Salary">Salary</option>
          <option value="Freelance">Freelance</option>
          <option value="Food">Food & Dining</option>
          <option value="Rent">Rent & Housing</option>
          <option value="Utilities">Utilities & Bills</option>
          <option value="Entertainment">Entertainment</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <ul id="transactionList" class="transaction-list"></ul>
      <div id="emptyState" class="empty-state hidden">No transactions recorded.</div>
    </div>
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
  background-color: #f4f6f9;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 20px;
}

.tracker-container {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  width: 100%;
  max-width: 550px;
}

h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 20px;
}

.summary-cards {
  display: flex;
  gap: 12px;
  margin-bottom: 25px;
}

.card {
  flex: 1;
  padding: 15px;
  border-radius: 8px;
  text-align: center;
  background: #f8f9fa;
  border: 1px solid #e9ecef;
}

.card h3 {
  font-size: 12px;
  text-transform: uppercase;
  color: #6c757d;
  margin-bottom: 5px;
}

.card p {
  font-size: 18px;
  font-weight: bold;
}

.balance-card p { color: #2b6cb0; }
.income-card p { color: #276749; }
.expense-card p { color: #c53030; }

.transaction-form {
  background: #f8fafc;
  padding: 15px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  margin-bottom: 25px;
}

.transaction-form h3 {
  font-size: 14px;
  color: #4a5568;
  margin-bottom: 12px;
}

.form-row {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}

input, select {
  flex: 1;
  padding: 10px;
  border: 1px solid #cbd5e0;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
}

input:focus, select:focus {
  border-color: #3182ce;
}

.btn-submit {
  width: 100%;
  padding: 10px;
  background: #2b6cb0;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-submit:hover {
  background: #2c5282;
}

.error-msg {
  color: #e53e3e;
  font-size: 13px;
  margin-top: 8px;
  text-align: center;
}

.history-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.history-header h3 {
  font-size: 16px;
  color: #2d3748;
}

#categoryFilter {
  width: auto;
  padding: 6px 10px;
  font-size: 13px;
}

.transaction-list {
  list-style: none;
  max-height: 220px;
  overflow-y: auto;
}

.transaction-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border-bottom: 1px solid #edf2f7;
  border-left: 4px solid transparent;
}

.transaction-item.income { border-left-color: #38a169; }
.transaction-item.expense { border-left-color: #e53e3e; }

.item-info {
  display: flex;
  flex-direction: column;
}

.item-title {
  font-weight: 600;
  font-size: 14px;
  color: #2d3748;
}

.item-date {
  font-size: 11px;
  color: #a0aec0;
}

.item-amount-action {
  display: flex;
  align-items: center;
  gap: 12px;
}

.amount {
  font-weight: bold;
  font-size: 14px;
}

.amount.income { color: #276749; }
.amount.expense { color: #c53030; }

.btn-delete {
  background: transparent;
  color: #a0aec0;
  border: none;
  font-size: 16px;
  cursor: pointer;
}

.btn-delete:hover {
  color: #e53e3e;
}

.empty-state {
  text-align: center;
  padding: 20px;
  color: #a0aec0;
  font-size: 14px;
}

.hidden { display: none; }
```

---

### File 3: `script.js`
```js
// --- 1. STATE & STORAGE MANAGEMENT ---
const STORAGE_KEY = "js_fundamentals_expenses";

let transactions = loadFromStorage();
let selectedCategoryFilter = "all";

// --- 2. DOM SELECTIONS ---
const transactionForm = document.getElementById("transactionForm");
const typeInput = document.getElementById("type");
const amountInput = document.getElementById("amount");
const categoryInput = document.getElementById("category");
const noteInput = document.getElementById("note");
const errorMessage = document.getElementById("errorMessage");
const categoryFilter = document.getElementById("categoryFilter");

const netBalanceEl = document.getElementById("netBalance");
const totalIncomeEl = document.getElementById("totalIncome");
const totalExpenseEl = document.getElementById("totalExpense");
const transactionListEl = document.getElementById("transactionList");
const emptyStateEl = document.getElementById("emptyState");

// --- 3. CURRENCY & DATE FORMATTERS ---
const currencyFormatter = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD"
});

function formatDate(isoString) {
  const date = new Date(isoString);
  return new Intl.DateTimeFormat("en-US", {
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit"
  }).format(date);
}

// --- 4. STORAGE HELPERS ---
function saveToStorage() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(transactions));
}

function loadFromStorage() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (err) {
    console.error("Failed to load transactions:", err);
    return [];
  }
}

// --- 5. SUMMARY CALCULATION ENGINE ---
/**
 * Uses Array.prototype.reduce to compute balance, income, and expense totals in a single pass.
 */
function calculateSummary(data) {
  return data.reduce((acc, t) => {
    if (t.type === "income") {
      acc.income += t.amount;
    } else if (t.type === "expense") {
      acc.expense += t.amount;
    }
    acc.balance = acc.income - acc.expense;
    return acc;
  }, { income: 0, expense: 0, balance: 0 });
}

// --- 6. TRANSACTION OPERATIONS ---
function addTransaction(type, amount, category, note) {
  // Input Validation Rules
  const numericAmount = Number(amount);
  if (Number.isNaN(numericAmount) || numericAmount <= 0) {
    throw new TypeError("Amount must be a positive number greater than 0.");
  }
  if (!category) {
    throw new Error("Please select a category.");
  }

  const newTransaction = {
    id: Date.now(),
    type,
    amount: numericAmount,
    category,
    note: note.trim() || category,
    createdAt: new Date().toISOString()
  };

  transactions = [newTransaction, ...transactions];
  saveToStorage();
  render();
}

function deleteTransaction(id) {
  transactions = transactions.filter(t => t.id !== id);
  saveToStorage();
  render();
}

// --- 7. RENDER CONTROLLER ---
function render() {
  // 1. Calculate Summary Totals
  const { income, expense, balance } = calculateSummary(transactions);

  // 2. Render Cards
  netBalanceEl.textContent = currencyFormatter.format(balance);
  totalIncomeEl.textContent = currencyFormatter.format(income);
  totalExpenseEl.textContent = currencyFormatter.format(expense);

  // 3. Filter Transactions for List
  const filtered = transactions.filter(t => 
    selectedCategoryFilter === "all" ? true : t.category === selectedCategoryFilter
  );

  // 4. Render History Items
  transactionListEl.innerHTML = "";

  filtered.forEach(t => {
    const li = document.createElement("li");
    li.className = `transaction-item ${t.type}`;
    const sign = t.type === "income" ? "+" : "-";

    li.innerHTML = `
      <div class="item-info">
        <span class="item-title">${escapeHTML(t.note)} <small>(${t.category})</small></span>
        <span class="item-date">${formatDate(t.createdAt)}</span>
      </div>
      <div class="item-amount-action">
        <span class="amount ${t.type}">${sign}${currencyFormatter.format(t.amount)}</span>
        <button class="btn-delete" data-id="${t.id}" title="Delete">✕</button>
      </div>
    `;
    transactionListEl.appendChild(li);
  });

  // 5. Toggle Empty State
  if (filtered.length === 0) {
    emptyStateEl.classList.remove("hidden");
  } else {
    emptyStateEl.classList.add("hidden");
  }
}

function escapeHTML(str) {
  return str.replace(/[&<>'"]/g, 
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}

// --- 8. EVENT LISTENERS ---
transactionForm.addEventListener("submit", (e) => {
  e.preventDefault();
  errorMessage.classList.add("hidden");

  try {
    addTransaction(
      typeInput.value,
      amountInput.value,
      categoryInput.value,
      noteInput.value
    );

    // Reset Form on Success
    amountInput.value = "";
    categoryInput.value = "";
    noteInput.value = "";
  } catch (err) {
    errorMessage.textContent = err.message;
    errorMessage.classList.remove("hidden");
  }
});

// Delete Transaction Event Delegation
transactionListEl.addEventListener("click", (e) => {
  if (e.target.classList.contains("btn-delete")) {
    const id = Number(e.target.dataset.id);
    if (id) deleteTransaction(id);
  }
});

// Category Filter Listener
categoryFilter.addEventListener("change", (e) => {
  selectedCategoryFilter = e.target.value;
  render();
});

// --- 9. INITIAL RENDER ---
render();
```

---

## 5) Architecture & Code Walkthrough

1. **`calculateSummary` via `.reduce()`**:
   - Accurately reduces the transaction list into a single stats object (`{ income, expense, balance }`) in $O(N)$ time.
2. **Robust Validation**:
   - `addTransaction()` throws errors if amounts are negative, `NaN`, or if no category is selected. The form submit listener catches these with `try...catch` and displays user-friendly red error feedback.
3. **Formatters (`Intl`)**:
   - `currencyFormatter` ensures standard formatting (`$1,250.00`).
   - `formatDate` formats ISO timestamps into readable localized dates (`Sep 7, 09:46 PM`).

---

## 6) Done Criteria & Verification Checklist
- [x] Adding income increases Net Balance and Total Income.
- [x] Adding expense decreases Net Balance and increases Total Expenses.
- [x] Amounts are strictly positive numbers (> 0).
- [x] Category dropdown filters the transaction list in real time.
- [x] Data persists across page reloads via LocalStorage.

---

## 7) Practice Extensions
Try building these features:
1. **CSV Export**: Add an "Export to CSV" button that generates a downloadable `.csv` file using `Blob` and `URL.createObjectURL()`.
2. **Monthly Summary Filter**: Add a date filter dropdown to filter transactions by month (e.g. "September 2026").
3. **Category Budget Alert**: Set a max budget per category (e.g., $200 for Food) and display a warning banner if expenses exceed the budget limit.

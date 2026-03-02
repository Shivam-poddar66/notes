# Mini Project 3: Expense Tracker

## Goal
Practice objects, arrays, reduce, date handling, validation, and error paths.

## Features
- Add income/expense transaction.
- List transactions.
- Show totals:
  - balance
  - total income
  - total expense
- Delete transaction.
- Filter by category/date (optional).

## Data Model
```js
{
  id: 1,
  type: "expense", // or income
  amount: 450,
  category: "Food",
  note: "Lunch",
  createdAt: "2026-02-25T10:30:00.000Z"
}
```

## Core Logic
```js
const totals = transactions.reduce((acc, t) => {
  if (t.type === "income") acc.income += t.amount;
  else acc.expense += t.amount;
  acc.balance = acc.income - acc.expense;
  return acc;
}, { income: 0, expense: 0, balance: 0 });
```

## Validation Rules
- Amount must be positive number.
- Type must be `income` or `expense`.
- Category cannot be empty.

## Extensions
- Persist in `localStorage`.
- Export CSV.
- Monthly summary chart.

## Done Criteria
- Totals always correct after add/delete.
- Invalid inputs handled with clear error messages.
- Code split into reusable functions.

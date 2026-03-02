# Callback Hell and Refactoring Patterns

## 1) Callback Hell Pattern
Deep nested callbacks reduce readability and reliability.

```js
getUser(id, (err, user) => {
  if (err) return handle(err);
  getOrders(user.id, (err2, orders) => {
    if (err2) return handle(err2);
    getInvoice(orders[0].id, (err3, invoice) => {
      if (err3) return handle(err3);
      console.log(invoice);
    });
  });
});
```

## 2) Problems
- Pyramid shape and nesting.
- Hard error handling.
- Difficult testing and maintenance.

## 3) Refactor Strategies
- Split into named functions.
- Convert to promises.
- Use `async/await` for linear flow.

## 4) Refactor Target
Prefer this style:
```js
const user = await getUser(id);
const orders = await getOrders(user.id);
const invoice = await getInvoice(orders[0].id);
```

## 5) Quick Practice
1. Refactor nested callbacks into promises.
2. Refactor same flow into async/await.
3. Add centralized error handling.

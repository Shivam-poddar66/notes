# Sequential vs Parallel Async Flows

## 1) Sequential Flow
Each async step waits for previous step.
Use when steps depend on prior outputs.

```js
const user = await getUser();
const orders = await getOrders(user.id);
const report = await getReport(orders);
```

## 2) Parallel Flow
Independent tasks run together.

```js
const [users, products, config] = await Promise.all([
  getUsers(),
  getProducts(),
  getConfig()
]);
```

## 3) Tradeoffs
Sequential:
- simpler dependency logic
- slower when tasks independent

Parallel:
- faster for independent tasks
- needs stronger failure strategy

## 4) Mixed Flow
Use sequential groups with parallel internal steps.

## 5) Quick Practice
1. Refactor sequential independent calls to parallel.
2. Measure time difference.
3. Design mixed flow for dependent + independent tasks.

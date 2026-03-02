# Concurrency Limits and Batch Processing

## 1) Why Limit Concurrency
Unbounded parallel requests can:
- hit API rate limits
- overload network/runtime
- increase memory pressure

## 2) Batch Pattern
Process large lists in chunks.

```js
async function processInBatches(items, size, worker) {
  for (let i = 0; i < items.length; i += size) {
    const batch = items.slice(i, i + size);
    await Promise.all(batch.map(worker));
  }
}
```

## 3) Worker Pool Pattern
Run fixed number of concurrent workers for queue items.

## 4) Retry Strategy Integration
Batch + retry can improve reliability without overload.

## 5) Practical Defaults
- small APIs: concurrency 3-5
- internal services: tune based on latency and rate limits

## 6) Quick Practice
1. Build batch processor for 100 mock requests.
2. Add concurrency cap.
3. Track success/failure counts.

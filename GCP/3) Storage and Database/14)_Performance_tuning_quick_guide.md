# 14) Performance Tuning Quick Guide (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Practical tuning checklist across storage and database services.
- Bottleneck-first approach for faster diagnosis.

## 1. Baseline metrics to watch
- Latency (`p50`, `p95`, `p99`).
- Throughput (queries/ops per second).
- Error rate and timeout rate.
- Saturation (CPU, memory, connections, queue depth, scan volume).

## 2. Bottleneck-first workflow
1. Identify user-impact symptom.
2. Isolate tier (app, network, storage, DB, analytics query).
3. Apply targeted tuning.
4. Re-test with representative load.

## 3. Cloud Storage tuning
- Co-locate compute and buckets.
- Parallelize transfer for large objects.
- Use resumable upload for reliability and throughput.

## 4. Cloud SQL tuning
- Optimize schema and indexes.
- Tune slow queries.
- Use connection pooling.
- Right-size instance class and storage.

## 5. Firestore tuning
- Optimize document structure for read path.
- Design indexes around query patterns.
- Avoid hotspot write patterns.

## 6. Bigtable tuning
- Improve row key distribution.
- Benchmark key-range performance.
- Watch hotspot behavior and rebalance key strategy.

## 7. BigQuery tuning
- Use partitioning and clustering.
- Filter early and avoid scanning unnecessary columns.
- Review expensive query patterns and rewrite.

## 8. Cache optimization (Memorystore)
- Cache high-frequency reads.
- Set TTL and invalidation strategy.
- Avoid stale-critical data caching without correctness controls.

## 9. Common mistakes
- Scaling hardware before fixing query/model issues.
- Ignoring downstream dependency bottlenecks.
- Running heavy analytics on transactional systems.

## 10. One-line memory hook
Tune data systems by measured bottleneck, not by guessing or blanket scaling.

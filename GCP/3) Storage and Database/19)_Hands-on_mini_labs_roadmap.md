# 19) Hands-on Mini Labs Roadmap (Detailed)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## Lab strategy
- Use one sandbox project.
- Keep all labs documented with screenshots, commands, and observations.
- Clean up resources after each lab block.

## Lab 1: Cloud Storage lifecycle and governance
### Objective
Build bucket policy and lifecycle automation.

### Tasks
1. Create bucket in chosen region.
2. Enable versioning.
3. Add lifecycle transition rule for old objects.
4. Configure retention policy.
5. Verify behavior with test objects.

### Validation
- Lifecycle and governance settings applied correctly.

## Lab 2: Secure object access
### Objective
Control object sharing without public bucket exposure.

### Tasks
1. Configure IAM least privilege for bucket access.
2. Generate signed URL for temporary access.
3. Validate unauthorized direct access is blocked.

### Validation
- Access works only through authorized path.

## Lab 3: Cloud SQL private relational backend
### Objective
Deploy secure managed SQL with recovery readiness.

### Tasks
1. Create Cloud SQL instance.
2. Configure private connectivity.
3. Create schema and sample data.
4. Enable backups and test restore workflow.

### Validation
- App connectivity works and restore is verified.

## Lab 4: Firestore document workload
### Objective
Implement flexible schema and query/index behavior.

### Tasks
1. Create collection/document structure.
2. Execute read/write operations.
3. Add index for target query pattern.
4. Validate query latency and behavior.

### Validation
- Query results and indexing behave as expected.

## Lab 5: BigQuery analytics workflow
### Objective
Build efficient analytical query patterns.

### Tasks
1. Create dataset and tables.
2. Load sample data.
3. Partition table and run filtered queries.
4. Compare scan volume before/after optimization.

### Validation
- Query performance and scan reduction observed.

## Lab 6: Cache layer with Memorystore
### Objective
Reduce primary DB read pressure.

### Tasks
1. Add cache-aside read path.
2. Configure TTL policy.
3. Measure latency and DB read reduction.
4. Simulate cache miss/failure fallback.

### Validation
- Cache improves read latency without correctness break.

## Lab 7: Integrated architecture mini-project
### Objective
Combine multiple services in one realistic flow.

### Example architecture
- Cloud Run app.
- Cloud SQL primary transactions.
- Cloud Storage for file assets.
- BigQuery for analytics export.
- Memorystore for hot reads.

### Validation
- End-to-end flow works with secure access and monitoring.

## Cleanup checklist
1. Delete temporary buckets, datasets, DB instances, and cache resources.
2. Remove test service accounts and keys if created.
3. Confirm no idle billable resources remain.

## Lab completion criteria
- Each lab has objective, commands/config, output, and lessons learned.
- You can explain service choice and tradeoffs for each design.

## One-line memory hook
Labs make selection logic real: if you can deploy and troubleshoot it, you can answer exam scenarios faster.

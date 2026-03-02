# 21) Practice Questions With Answers (40, Detailed)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## How to use this set
- Try to answer each question in 20 to 30 seconds first.
- Then verify with the answer and "Why" line.
- Focus on constraints and elimination, not memorization only.

1. Which service is best for storing images, backups, and static website files?
   - Correct answer: Cloud Storage.
   - Why: It is managed object storage designed for durable file/object data.

2. Which service is the default choice for VM boot disks?
   - Correct answer: Persistent Disk.
   - Why: It is managed block storage used by Compute Engine.

3. Which service provides managed shared NFS file storage for VMs?
   - Correct answer: Filestore.
   - Why: It is purpose-built for shared filesystem semantics.

4. Which managed service is typically used for MySQL or PostgreSQL transactional app workloads?
   - Correct answer: Cloud SQL.
   - Why: It is the standard managed relational OLTP option.

5. Which service is commonly chosen for global-scale relational workloads with strong consistency requirements?
   - Correct answer: Cloud Spanner.
   - Why: Spanner is designed for horizontally scalable relational consistency.

6. Which database is serverless document NoSQL in GCP?
   - Correct answer: Firestore.
   - Why: Firestore uses collections/documents and supports flexible schema patterns.

7. Which service is best for very high-throughput key-range and time-series style workloads?
   - Correct answer: Bigtable.
   - Why: Bigtable is a wide-column store optimized for large key-based throughput.

8. Which service is best for SQL analytics over very large datasets?
   - Correct answer: BigQuery.
   - Why: BigQuery is a serverless analytical data warehouse.

9. Which service is commonly added to reduce repeated database read latency?
   - Correct answer: Memorystore.
   - Why: It is the managed in-memory caching layer.

10. For frequently accessed object data, which Cloud Storage class is usually appropriate?
    - Correct answer: Standard.
    - Why: Standard class is intended for frequent access patterns.

11. Which Cloud Storage class is usually associated with long-term archival use?
    - Correct answer: Archive.
    - Why: It is intended for very infrequent access and long retention.

12. A scenario says "managed SQL database with minimal operations." Which answer is most likely?
    - Correct answer: Cloud SQL.
    - Why: It matches relational SQL requirements with managed operations.

13. A scenario says "mobile app backend with flexible document model." Which answer is most likely?
    - Correct answer: Firestore.
    - Why: Document model and serverless scaling are aligned with this pattern.

14. A scenario says "PB-scale analytics for dashboards and reporting." Which answer is most likely?
    - Correct answer: BigQuery.
    - Why: BigQuery is designed for large analytical SQL workloads.

15. A scenario says "shared filesystem for multiple VM instances." Which answer is most likely?
    - Correct answer: Filestore.
    - Why: Shared NFS behavior is a file storage requirement.

16. A scenario says "store logs, media, and backups with lifecycle controls." Which answer is most likely?
    - Correct answer: Cloud Storage.
    - Why: Lifecycle and object class controls are core Cloud Storage features.

17. What is the first step before choosing any data service?
    - Correct answer: Clarify data model and access pattern constraints.
    - Why: Correct service selection is constraint-driven.

18. Which service is usually wrong for high-frequency OLTP app transactions?
    - Correct answer: BigQuery.
    - Why: BigQuery is analytics-first, not transactional OLTP.

19. Which design element is most critical in Bigtable performance?
    - Correct answer: Row key design.
    - Why: Key design drives distribution and hotspot risk.

20. Which pattern is best for temporary external file access without making bucket public?
    - Correct answer: Signed URL.
    - Why: It grants short-lived controlled object access.

21. Which Cloud Storage control helps protect against accidental overwrite/delete?
    - Correct answer: Object versioning.
    - Why: Versioning preserves prior object states.

22. Which reliability statement is correct?
    - Correct answer: Backups are not enough; restore testing is also required.
    - Why: Recovery readiness is proven only by restore drills.

23. Which statement about security is most correct across storage/database services?
    - Correct answer: Least-privilege IAM and managed secrets should be baseline.
    - Why: Identity scope and secret handling are core risk controls.

24. Which service comparison is correct for transactional relational workloads?
    - Correct answer: Cloud SQL is common default; Spanner is for higher global-scale consistency needs.
    - Why: Spanner is typically chosen only when scale/consistency constraints justify it.

25. Which comparison is correct for Firestore vs Bigtable?
    - Correct answer: Firestore fits document app patterns; Bigtable fits massive key-range throughput patterns.
    - Why: They solve different NoSQL workload shapes.

26. Which BigQuery optimization usually reduces query cost?
    - Correct answer: Partitioning and filtering to reduce scanned data.
    - Why: Cost is strongly related to query scan volume.

27. A team runs the same read-heavy query repeatedly on app traffic and DB CPU is high. Best first architecture addition?
    - Correct answer: Add Memorystore cache layer.
    - Why: Caching hot reads reduces repetitive database load.

28. Which pattern often lowers both latency and network cost?
    - Correct answer: Co-locate compute and data in the same region when possible.
    - Why: It minimizes cross-region transfer and round-trip distance.

29. Which is a common security anti-pattern?
    - Correct answer: One shared high-privilege service account across many workloads.
    - Why: It increases blast radius and violates least privilege.

30. Which service should you choose for durable unstructured object archives?
    - Correct answer: Cloud Storage.
    - Why: Object storage is the correct model for archive files.

31. If a scenario explicitly requires relational joins and ACID semantics, which family should you prefer first?
    - Correct answer: Relational services (Cloud SQL or Spanner).
    - Why: SQL transactional requirements indicate relational model fit.

32. If a scenario explicitly requires ad-hoc analytics queries over historical data, which service should you prefer first?
    - Correct answer: BigQuery.
    - Why: That is analytics warehouse workload shape.

33. Which statement about cache correctness is most accurate?
    - Correct answer: Cache is a performance layer, not source of truth; TTL/invalidation design is required.
    - Why: Correctness depends on controlled cache refresh/invalidation behavior.

34. Which is the better default for storing application credentials?
    - Correct answer: Secret Manager.
    - Why: It centralizes secret access control and rotation workflows.

35. Which common mistake leads to inflated Cloud Storage bills?
    - Correct answer: Wrong class/lifecycle strategy for real access frequency.
    - Why: Access patterns should drive class and lifecycle decisions.

36. Which common mistake leads to inflated BigQuery costs?
    - Correct answer: Repeated wide scans without partition/filter optimization.
    - Why: Inefficient query patterns increase scanned bytes.

37. Which is the best tie-breaker when multiple services seem to work?
    - Correct answer: Choose the least-complex service that meets all hard constraints.
    - Why: This is usually both exam-correct and operationally safer.

38. Which reliability planning step should be documented before service rollout?
    - Correct answer: RTO/RPO targets and tested recovery runbook.
    - Why: Continuity design should map to explicit business requirements.

39. Which answer is most aligned with "global relational consistency at very large scale"?
    - Correct answer: Spanner.
    - Why: This is Spanner's strongest fit pattern.

40. Final chapter rule for storage/database selection?
    - Correct answer: Match data model and access pattern first, then apply security, reliability, performance, and cost controls.
    - Why: This framework consistently avoids most architecture and exam mistakes.

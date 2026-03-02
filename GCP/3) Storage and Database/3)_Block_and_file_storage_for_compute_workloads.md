# 3) Block And File Storage For Compute Workloads (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this topic covers
- Persistent Disk, Local SSD, and Filestore decision boundaries.
- Performance, durability, and operational tradeoffs.
- Exam patterns for VM storage questions.

## 1. Block vs file storage in compute design
- Block storage: attached volume model for VM OS/data (Persistent Disk, Local SSD).
- File storage: shared filesystem semantics across clients (Filestore with NFS).

## 2. Persistent Disk
Use for durable VM boot and application data volumes.

Strengths:
- Managed durability.
- Snapshot support.
- Fits most VM persistent storage needs.

Patterns:
- Separate OS disk and data disk for easier lifecycle operations.
- Use snapshots for backup and recovery workflows.

## 3. Local SSD
Use for very high IOPS and low-latency temporary storage.

Key tradeoff:
- Ephemeral behavior means data persistence guarantees differ from durable disk services.

Best fit:
- Caching, temp processing, scratch data, and performance-critical intermediate workloads.

## 4. Filestore
Managed NFS for shared file storage across multiple VM clients.

Best fit:
- Legacy apps needing shared file semantics.
- Workloads needing POSIX-like shared filesystem behavior.

Tradeoffs:
- Not a drop-in replacement for object storage or relational databases.

## 5. How to choose quickly
1. Need VM boot/data persistence? -> Persistent Disk.
2. Need ultra-fast temporary local storage? -> Local SSD.
3. Need shared NFS filesystem for multiple VMs? -> Filestore.

## 6. Security patterns
- Restrict network-level access to file services.
- Use least-privilege IAM and service-account driven access controls around compute.
- Encrypt sensitive data and protect backup artifacts.

## 7. Reliability patterns
- Snapshot schedules for Persistent Disk.
- Recovery runbook and restore drills.
- Avoid single-point dependency on one unmanaged storage path for critical workloads.

## 8. Performance patterns
- Match disk type and size to IOPS and throughput needs.
- Benchmark under realistic workload.
- Keep storage and compute in same region/zone alignment pattern as required.

## 9. Cost patterns
- Right-size volumes.
- Remove orphaned disks and snapshots.
- Use Local SSD only where performance gain is justified.

## 10. Common exam traps
- Choosing Filestore when object storage is requested.
- Choosing Local SSD for data requiring long-term persistence.
- Ignoring backup strategy for persistent VM data.

## 11. One-line memory hook
Block storage solves VM durability/performance needs; file storage solves shared filesystem needs.

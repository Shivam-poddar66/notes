# Compute Engine (Deep Dive Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Full VM architecture and operations in GCP.
- Compute Engine components, lifecycle, and reliability design.
- Security hardening and cost optimization basics.
- Exam-focused usage patterns and mistakes.

## 1) What Compute Engine is

Compute Engine is GCP's VM service where you control:
- OS image,
- machine type,
- disk setup,
- networking options,
- boot/runtime configuration.

It is best for workloads that need:
- strong runtime customization,
- legacy compatibility,
- full infrastructure-level control.

## 2) Core building blocks

### VM instance
- Running virtual machine with selected CPU/memory and disk/network settings.

### Machine type
- Defines vCPU and memory profile.
- Options include predefined and custom machine types.

### Disks
- Boot disk: OS and boot files.
- Additional persistent disks for data.
- Local SSD for high-performance ephemeral storage patterns.

### Network interface
- Connects VM to VPC subnet.
- Includes internal IP and optional external IP.

### Service account
- Workload identity used for API/resource access.

## 3) Instance lifecycle and operations

Typical flow:
1. Create instance.
2. Configure startup metadata/scripts.
3. Run workload.
4. Stop/suspend/restart as needed.
5. Delete when no longer required.

Operational note:
- Stopping instance may still retain disk costs.
- Cleanup is required for full cost stop.

## 4) Machine type and sizing strategy

### Selection principles
- CPU-bound app -> compute-optimized profile.
- Memory-heavy app -> memory-focused profile.
- Mixed workload -> general-purpose profile.

### Right-sizing loop
1. start with expected baseline,
2. monitor CPU/memory utilization,
3. adjust machine type,
4. re-check latency and cost.

## 5) Disk strategy basics

### Persistent disk
- Durable storage attached to VM.
- Balance performance and cost by disk type.

### Local SSD
- Very high IOPS and low latency.
- Data is not persistent like regular persistent disk.

### Snapshot and image strategy
- Snapshots for backup and recovery point capture.
- Images/templates for consistent provisioning.

## 6) Networking on Compute Engine

### Core controls
- VPC and subnet placement.
- Firewall rules for ingress/egress.
- Internal-first communication for private workloads.

### Exposure minimization
- Use external IP only when necessary.
- Restrict management ports.
- Prefer controlled ingress paths.

## 7) Automation and bootstrap

### Startup scripts
- Automate package install and service configuration at boot.

### Instance templates
- Standardize VM configuration for repeatable deployment.

### Infrastructure as code mindset
- Avoid manual drift by using repeatable provisioning patterns.

## 8) Reliability architecture with VMs

### Single VM pattern
- Suitable for dev/test or non-critical workload.

### Production pattern
- MIG across multiple zones.
- Load balancing with health checks.
- Autohealing for unhealthy instances.

### DR progression
- Single-region multi-zone baseline.
- Add cross-region DR only when business requirement demands.

## 9) Security responsibilities for Compute Engine

You are responsible for:
- OS patching and hardening,
- secure SSH/admin access controls,
- service account least privilege,
- vulnerability and package management.

Recommended controls:
- minimal public exposure,
- strict firewall rules,
- secret storage via Secret Manager.

## 10) Performance practices

- Right-size CPU/RAM to workload profile.
- Reduce noisy-neighbor style bottlenecks through monitoring.
- Place VM near dependent services (DB/cache) for lower latency.
- Use load balancing and scaling patterns for peak handling.

## 11) Cost optimization practices

- Stop idle non-production VMs.
- Remove unused disks and static resources.
- Use commitments or lower-cost capacity options when workload pattern allows.
- Monitor egress and cross-region traffic paths.

## 12) Common use cases

- Legacy enterprise applications.
- Custom runtime or OS-dependent workloads.
- Lift-and-shift migration stages.
- Stateful workloads requiring VM-level control.

## 13) Common mistakes (exam relevant)

- Using single-zone VM for critical production service.
- Granting excessive IAM privileges to VM service account.
- Ignoring patching responsibility.
- Oversizing machine types and leaving them idle.
- Choosing VMs when scenario explicitly asks for minimum ops.

## 14) Exam scenario patterns

1. Need OS-level control and custom runtime.
   - Compute Engine is common best fit.

2. Need VM fleet that autoheals and autoscales.
   - Compute Engine with MIG and health checks.

3. Need quick migration with minimal app change.
   - Rehost on Compute Engine first.

4. Need strict low ops and stateless API.
   - Usually not VM-first; evaluate Cloud Run/App Engine.

## 15) Hands-on checklist

1. Launch VM with minimal public exposure.
2. Attach service account with least privilege.
3. Add startup script for automated bootstrap.
4. Create snapshot and test restore.
5. Build instance template and MIG.
6. Add health check and verify autohealing behavior.
7. Monitor utilization and right-size machine type.

## 16) Quick revision sheet

- Compute Engine gives maximum control.
- You own OS patching/hardening responsibilities.
- Production VM reliability often needs MIG + LB + multi-zone.
- Cost optimization depends on right-sizing and cleanup discipline.

## 17) Practice questions with answers

1. Which GCP service gives full VM control?
   - Compute Engine.

2. Who patches guest OS in Compute Engine?
   - Customer.

3. What is instance template used for?
   - Consistent VM configuration for repeatable fleet deployment.

4. What is common high-availability VM pattern?
   - MIG across multiple zones with load balancing.

5. Why use startup scripts?
   - Boot-time automation of VM setup.

6. Why avoid unnecessary external IPs?
   - Reduces attack surface.

7. What is one common VM cost anti-pattern?
   - Oversized idle instances.

8. What should be used for workload identity?
   - Service account with least privilege.

9. What is a common exam clue for Compute Engine?
   - Requirement for OS-level customization.

10. What is the final Compute Engine decision rule?
    - Use it when control requirements justify operational overhead.

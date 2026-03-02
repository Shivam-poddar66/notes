# Managed Instance Groups (MIG) - Detailed Notes

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- MIG fundamentals and architecture role.
- Autoscaling, autohealing, updates, and high availability patterns.
- Zonal vs regional MIG decisions.
- Operational best practices and exam scenarios.

## 1) What MIG is

Managed Instance Group is a managed fleet of VM instances built from an instance template.

Key purpose:
- run multiple similar VMs,
- scale automatically,
- heal unhealthy instances,
- update fleet safely.

## 2) Core capabilities

### Autoscaling
- Adjusts number of instances based on demand signals.

### Autohealing
- Recreates unhealthy instances using health checks.

### Template consistency
- New instances use the same baseline configuration.

### Rolling updates
- Controlled rollout of template/image changes.

## 3) Zonal vs regional MIG

### Zonal MIG
- Instances live in one zone.
- Simpler setup.
- Lower zone-failure resilience.

### Regional MIG
- Instances distributed across zones in one region.
- Better zone-failure resilience.
- Preferred for production availability needs.

## 4) MIG architecture pattern

Typical production VM web/API pattern:
1. Instance template for stateless app.
2. Regional MIG across 2+ zones.
3. Health check for service path.
4. Load balancer routes only to healthy backends.
5. Autoscaler reacts to demand.

## 5) Autoscaling design guidance

- Choose scaling signals aligned with workload behavior.
- Set sensible min/max bounds.
- Avoid aggressive scaling that causes instability.
- Validate behavior under peak and burst tests.

## 6) Autohealing design guidance

- Health check should reflect actual app health, not just VM alive.
- Avoid false positives by selecting proper endpoint and thresholds.
- Ensure startup time and readiness are considered.

## 7) Update and rollout strategy

- Use rolling updates to minimize risk.
- Control update pace and disruption.
- Monitor error rate and latency during rollout.
- Keep rollback plan ready.

## 8) Security practices for MIG workloads

- Use dedicated service account with least privilege.
- Minimize open ports in firewall.
- Bake hardened base image/template.
- Keep patching pipeline and image refresh cycle.

## 9) Cost practices

- Right-size template machine type.
- Set autoscaler minimums carefully to avoid idle waste.
- Remove unused test MIGs/templates.
- Monitor scale behavior for overreaction.

## 10) Common use cases

- Stateless API tier.
- Web frontend fleet.
- Batch worker pool (if VM model required).
- Legacy app scale-out architecture.

## 11) Common mistakes (exam relevant)

- Using zonal MIG for critical production when zone resilience is required.
- Health check configured too shallow (misses app failure).
- No autoscaling limits causing runaway scale.
- Treating MIG as stateful workload manager by default.

## 12) Scenario answer patterns

1. Requirement: VM fleet with autohealing and autoscaling.
   - Typical answer: MIG.

2. Requirement: survive zone failure in VM architecture.
   - Typical answer: regional MIG + load balancing.

3. Requirement: consistent VM config across instances.
   - Typical answer: MIG with instance template.

## 13) Hands-on checklist

1. Create instance template.
2. Create regional MIG with two zones.
3. Attach health check and observe autohealing.
4. Configure autoscaling and generate traffic.
5. Perform rolling template update and validate no major outage.

## 14) Quick revision sheet

- MIG = autoscaling + autohealing + fleet consistency.
- Regional MIG preferred for better availability.
- Health checks define reliability behavior.
- MIG + load balancer is common VM production answer.

## 15) Practice questions with answers

1. What is MIG used for?
   - Managing scalable and self-healing VM fleets.

2. Which MIG type is better for zone resilience?
   - Regional MIG.

3. Why are instance templates important?
   - Ensure consistent VM configuration.

4. What enables autohealing decisions?
   - Health checks.

5. What is a common exam pair with MIG?
   - Load balancing.

6. What is one autoscaling risk?
   - Unbounded aggressive scale causing cost or instability.

7. Why use rolling updates?
   - Reduce deployment risk across fleet.

8. Can MIG eliminate all reliability design needs by itself?
   - No, dependency and architecture design still matter.

9. What workload fits MIG best?
   - Stateless scalable VM services.

10. Top exam takeaway for MIG?
    - For VM HA scale-out, think template + MIG + health checks + load balancing.

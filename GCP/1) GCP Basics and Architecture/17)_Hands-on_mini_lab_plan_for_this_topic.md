# Hands-On Mini Lab Plan for This Topic (Detailed)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1) Goal of this lab track

Build practical skill for GCP Basics and Architecture so scenario answers are based on real hands-on understanding, not memory only.

## 2) Lab structure

- Total labs: 12
- Total estimated duration: 12 to 16 hours
- Mode: beginner-friendly but production-thinking
- Coverage:
  - regions and zones,
  - projects and hierarchy,
  - IAM and service accounts,
  - networking baseline,
  - reliability and DR basics,
  - monitoring, cost, and governance checks.

## 3) Prerequisites

- One Google account with billing enabled.
- Permission to create projects and resources.
- `gcloud` CLI installed (optional but recommended).
- Basic command line familiarity.

## 4) Cost safety guardrails (do this first)

1. Create budget alert at 50%, 75%, 90%, and 100%.
2. Create a label standard:
   - `owner`
   - `env`
   - `app`
   - `cost_center`
3. Use smallest practical machine sizes for labs.
4. Delete resources after each lab.
5. Keep a cleanup checklist and verify at session end.

## 5) Suggested schedule

- Day 1: Labs 1-3
- Day 2: Labs 4-6
- Day 3: Labs 7-9
- Day 4: Labs 10-12 and recap

## 6) Lab 1 - Region and zone basics

### Objective
- Understand region vs zone and zonal resource placement.

### Steps
1. Create one VM in `zone-a` and one VM in `zone-b` in same region.
2. Tag/label both VMs.
3. Record region/zone metadata.

### Validate
- Confirm both instances are same region, different zones.

### Cleanup
- Stop and delete both VMs if not needed for next lab.

## 7) Lab 2 - Multi-zone resilience concept

### Objective
- Understand why multi-zone improves availability.

### Steps
1. Create an instance group across two zones (or two backends manually).
2. Place behind HTTP load balancing path.
3. Simulate one backend unavailability.

### Validate
- Service remains reachable via healthy backend.

### Cleanup
- Remove load balancer and instance backends if not reused.

## 8) Lab 3 - Project separation and governance baseline

### Objective
- Separate environments for blast-radius control.

### Steps
1. Create `gcp-lab-dev` project.
2. Create `gcp-lab-prod` project.
3. Enable required APIs in each.
4. Attach labels and billing account.

### Validate
- Project-level separation is visible in IAM/API/quota/billing scope.

### Cleanup
- Keep projects for remaining labs if needed.

## 9) Lab 4 - IAM role scoping and least privilege

### Objective
- Practice least-privilege assignment.

### Steps
1. Create test principal (or use secondary account/group).
2. Grant viewer-like access at project level.
3. Grant additional narrow role at resource level.
4. Test allowed and denied actions.

### Validate
- Principal can do only expected actions.

### Cleanup
- Remove temporary role bindings.

## 10) Lab 5 - Service account practical access

### Objective
- Understand workload identity usage.

### Steps
1. Create service account for sample workload.
2. Attach minimal role needed.
3. Run a workload operation that requires that role.
4. Confirm operation works; unauthorized operations fail.

### Validate
- Service account permissions are minimal and functional.

### Cleanup
- Remove unused service accounts and keys.

## 11) Lab 6 - Network baseline and firewall controls

### Objective
- Build secure network defaults.

### Steps
1. Create custom VPC and subnet.
2. Add explicit firewall rules for required ports only.
3. Deploy VM in subnet and test access controls.

### Validate
- Allowed traffic works, unauthorized traffic blocked.

### Cleanup
- Remove test rules and resources if not reused.

## 12) Lab 7 - Control plane vs data plane observation

### Objective
- Differentiate management events from runtime traffic.

### Steps
1. Deploy simple web service.
2. Trigger a config/deploy change (control plane event).
3. Generate request traffic (data plane).
4. Observe logs/metrics for both.

### Validate
- You can identify control-plane and data-plane signals separately.

### Cleanup
- Remove temporary services.

## 13) Lab 8 - Reliability objective and backup test

### Objective
- Connect architecture to RTO/RPO thinking.

### Steps
1. Create data store with backup enabled.
2. Document target RTO/RPO values.
3. Perform one restore simulation.

### Validate
- Recovery time and data point are documented against target.

### Cleanup
- Delete test restored resources.

## 14) Lab 9 - Performance and latency check

### Objective
- Measure basic latency behavior and dependency impact.

### Steps
1. Deploy simple endpoint.
2. Capture baseline p50/p95 latency.
3. Introduce dependency call and compare latency.
4. Apply one optimization (cache or locality adjustment).

### Validate
- Improvement is measurable and recorded.

### Cleanup
- Remove test components.

## 15) Lab 10 - Cost visibility and optimization

### Objective
- Make cost architecture measurable.

### Steps
1. Ensure labels on all active resources.
2. Review billing by project/label.
3. Identify one waste item (idle resource, large instance, extra egress path).
4. Optimize and re-check spend trend.

### Validate
- One measurable cost optimization is documented.

### Cleanup
- Delete temporary workload resources.

## 16) Lab 11 - Organization policy style governance drill

### Objective
- Understand preventive guardrails.

### Steps
1. Apply one constraint (for example location restriction) in test hierarchy.
2. Attempt disallowed deployment.
3. Observe policy enforcement behavior.

### Validate
- Constraint blocks disallowed action as expected.

### Cleanup
- Revert test constraint if needed.

## 17) Lab 12 - End-to-end architecture mini project

### Objective
- Bring all concepts together in one design.

### Build
- Multi-zone stateless app tier,
- managed data layer with backups,
- IAM least privilege for workload identity,
- budget alerts and labels,
- monitoring dashboard and one alert.

### Validate
- System works,
- governance controls visible,
- one failure scenario and one recovery step tested.

### Cleanup
- Destroy all created resources after screenshots/notes.

## 18) Lab documentation template (use per lab)

- Lab name:
- Date:
- Objective:
- Services used:
- Architecture diagram (simple):
- Steps executed:
- Validation result:
- Cost observed:
- Issues faced:
- Fix applied:
- What to remember for exam:

## 19) Minimum completion checklist for exam readiness

- Can explain region vs zone using your own deployment.
- Can create separate projects and apply IAM correctly.
- Can build secure basic VPC/firewall setup.
- Can describe control plane vs data plane from observed logs.
- Can define RTO/RPO and map to architecture choice.
- Can show one cost optimization and one governance control.

## 20) Final review activity after labs

1. Revisit each lab and write one architecture lesson.
2. Map each lesson to one likely scenario question.
3. Build a personal "mistake list" from failed lab attempts.
4. Re-do weakest 3 labs before mock test week.

---

If you complete these 12 labs with notes, your architecture answers in ACE scenarios become significantly stronger and faster.

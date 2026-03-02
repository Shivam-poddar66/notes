# Hands-on Mini Lab Plan for Compute Chapter (Detailed)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## Lab goals
- Build practical familiarity with Compute Engine, MIG, App Engine, Cloud Run, and Cloud Run functions.
- Compare operational effort, scaling behavior, and security setup across services.
- Produce exam-relevant artifacts: deployment notes, decision logs, and cleanup checklist.

## Total time and format
- Estimated total: 5 to 7 hours.
- Suggested format:
  - Session 1 (2.5 to 3.5 hours): VM + MIG + load balancing.
  - Session 2 (2.5 to 3.5 hours): App Engine + Cloud Run + functions + comparison.

## Prerequisites
- One sandbox GCP project with billing enabled.
- IAM permissions for compute, networking, serverless deploy, logs, and monitoring.
- `gcloud` CLI configured locally (or use Cloud Shell).
- Basic repo with sample app code (HTTP API and event handler).

## Lab 0) Environment setup (20 minutes)

### Objective
- Create consistent naming, region, and IAM baseline.

### Tasks
1. Choose primary region and two zones.
2. Enable required APIs (Compute Engine, Cloud Run, Cloud Functions/Cloud Run functions, App Engine, Logging, Monitoring).
3. Create dedicated service accounts:
   - `sa-vm-app`,
   - `sa-cloudrun-app`,
   - `sa-function-app`,
   - `sa-appengine-app`.
4. Grant least-privilege roles needed for each lab only.
5. Create a simple tracking document for settings and observations.

### Validation
- All service accounts exist and have scoped roles.
- APIs are enabled.

## Lab 1) Single VM baseline (35 minutes)

### Objective
- Deploy one Compute Engine VM and validate access/configuration flow.

### Tasks
1. Create VM with startup script to install a small web server.
2. Attach `sa-vm-app` service account.
3. Configure firewall rule for test HTTP access (restricted source range).
4. Connect via SSH and verify startup script output.
5. Send test requests and record latency baseline.

### Validation
- VM serves traffic.
- Startup automation succeeded.
- Logs show request handling.

### Exam mapping
- Understand VM control model and operational responsibility.

## Lab 2) MIG with autoscaling and autohealing (50 minutes)

### Objective
- Convert single VM architecture to resilient VM fleet pattern.

### Tasks
1. Create instance template from known-good VM config.
2. Create regional MIG across two zones.
3. Add health check.
4. Enable autoscaling policy.
5. Simulate instance failure and verify autohealing.

### Validation
- MIG replaces unhealthy instance automatically.
- Instance count changes under synthetic load.

### Exam mapping
- "VM autoscale + autoheal" pattern => MIG + health checks.

## Lab 3) Load balancing for MIG (45 minutes)

### Objective
- Add resilient traffic entry point for VM fleet.

### Tasks
1. Create HTTP(S) load balancer backend with MIG.
2. Attach health checks and verify backend status.
3. Test traffic distribution and behavior during backend failures.
4. Record failover and recovery observations.

### Validation
- Requests stay available after backend instance disruption.
- Health checks correctly remove unhealthy targets.

### Exam mapping
- Production VM architecture should not depend on direct instance IPs.

## Lab 4) Cloud Run service deployment (40 minutes)

### Objective
- Deploy stateless containerized API with autoscaling.

### Tasks
1. Build or pull sample container image.
2. Deploy Cloud Run service with:
   - explicit service account (`sa-cloudrun-app`),
   - controlled ingress,
   - configured concurrency/min/max instances.
3. Run burst traffic test.
4. Observe scaling and request latency in logs/metrics.

### Validation
- Service scales under burst load.
- IAM and ingress settings match intended exposure.

### Exam mapping
- "Low-ops scalable HTTP API" -> Cloud Run.

## Lab 5) Cloud Run functions event workflow (35 minutes)

### Objective
- Implement event-driven processing.

### Tasks
1. Deploy function with `sa-function-app`.
2. Use object upload or Pub/Sub trigger.
3. Add idempotency check in function logic.
4. Trigger multiple events, including retry simulation.

### Validation
- Function executes on trigger.
- Duplicate/retry behavior does not create incorrect outputs.

### Exam mapping
- Event-driven lightweight automation patterns.

## Lab 6) App Engine service and version rollout (35 minutes)

### Objective
- Deploy managed app platform workload and test version controls.

### Tasks
1. Initialize App Engine app in selected region.
2. Deploy version `v1`, then deploy `v2`.
3. Shift partial traffic to `v2`, observe behavior.
4. Roll back to `v1` if regression detected.

### Validation
- Version traffic splitting works.
- Rollback path is confirmed.

### Exam mapping
- Managed platform and controlled version rollout pattern.

## Lab 7) Security baseline comparison (30 minutes)

### Objective
- Compare identity and secret access patterns across compute models.

### Tasks
1. Store test secret in Secret Manager.
2. Grant secret accessor to only needed service accounts.
3. Verify each workload can access only intended resources.
4. Review Cloud Audit Logs for IAM and deploy actions.

### Validation
- Least-privilege boundaries hold.
- Secret access is controlled and auditable.

### Exam mapping
- Security overlay after service selection.

## Lab 8) Cost and operations comparison (30 minutes)

### Objective
- Build service selection intuition based on traffic and ops profile.

### Tasks
1. Record rough cost and effort observations for:
   - single VM,
   - MIG + LB,
   - Cloud Run,
   - Cloud Run functions,
   - App Engine.
2. Compare:
   - deployment speed,
   - scaling complexity,
   - operational burden,
   - reliability controls needed.
3. Write final recommendation by workload type.

### Validation
- You can explain which service is best for each scenario and why.

## Required artifacts to keep

- Architecture diagram (simple is fine).
- IAM/service account mapping table.
- Scaling test notes with observed results.
- One-page compute service decision cheat sheet.
- Cleanup checklist and confirmation log.

## Cleanup checklist (mandatory)

1. Delete load balancer resources.
2. Delete MIG and instance templates.
3. Delete standalone VMs.
4. Delete Cloud Run services/jobs and functions.
5. Remove unused App Engine versions/services.
6. Remove temporary storage buckets, Pub/Sub topics, and secrets.
7. Confirm no unexpected billable resources remain.

## Self-assessment questions

1. Can you justify Compute Engine vs Cloud Run using hard constraints?
2. Can you explain why MIG + LB is the default resilient VM pattern?
3. Can you configure least-privilege service accounts per workload?
4. Can you diagnose a scaling issue using logs and monitoring metrics?
5. Can you pick the lowest-complexity option that still meets requirements?

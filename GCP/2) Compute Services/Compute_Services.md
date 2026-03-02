# Compute Services (Complete Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Full compute service overview for exam and real projects.
- Compute Engine, App Engine, Cloud Run, Cloud Run functions.
- Managed Instance Groups (MIG) and scaling patterns.
- Service selection framework for scenario questions.
- Security, reliability, cost, and operations best practices.
- Hands-on checklist and practice questions.

## 1) Compute service landscape in GCP

Compute services let you run code with different levels of control vs operational effort.

Main options:
- Compute Engine (IaaS VMs): highest control, more operations.
- App Engine (PaaS): managed app platform, less infrastructure control.
- Cloud Run (serverless containers): low ops, container-based HTTP services and jobs.
- Cloud Run functions (event-driven functions): function-style event handling.

Simple rule:
- More control usually means more operational work.
- More managed usually means less control and faster delivery.

## 2) Service model comparison

### IaaS - Compute Engine
- You manage guest OS, runtime, patches, app stack.
- Best when workload needs custom OS/runtime behavior.

### PaaS - App Engine
- You focus mostly on code and app config.
- Platform handles much of runtime and scaling management.

### Serverless containers - Cloud Run
- Deploy container image, platform handles scaling and serving.
- Great for APIs and microservices with variable traffic.

### Event-driven functions - Cloud Run functions
- Small function units triggered by events.
- Great for automation hooks and background reactions.

## 3) Compute Engine (deep dive)

### What it is
- Managed VM service where you choose machine type, OS image, disks, network, and metadata.

### Core components
- VM instance: running compute node.
- Machine type: CPU/memory profile.
- Boot disk and optional data disks.
- Network interface and firewall context.
- Service account for workload identity.

### VM lifecycle
- Provision -> start -> run -> stop/suspend -> start -> terminate/delete.
- Stopped VM usually still keeps persistent disk storage cost.

### Machine type strategy
- General purpose for common workloads.
- Compute-optimized for CPU-heavy workloads.
- Memory-optimized for RAM-intensive apps.
- Custom machine types for fine cost/performance tuning.

### Disk options (high-level)
- Standard persistent disks for lower-cost workloads.
- SSD persistent disks for higher performance.
- Local SSD for very high IOPS and low latency (ephemeral behavior considerations).

### Networking basics for VM
- Internal IP for private communication.
- External IP only when required.
- Firewall and route behavior controlled by VPC settings.

### Startup and metadata
- Startup scripts automate configuration at boot.
- Metadata can pass configuration values to VM workloads.

### Compute Engine reliability patterns
- Single VM for non-critical or dev/test.
- MIG across multiple zones for production high availability.
- Load balancing + health checks for resilient traffic routing.

### Spot/interruptible compute pattern
- Lower-cost option for fault-tolerant batch workloads.
- Not ideal for strict availability services.

### Security basics for VMs
- Use service accounts with least privilege.
- Restrict SSH/admin access.
- Keep OS and packages patched.
- Minimize public exposure and open ports.

### Cost controls for VMs
- Right-size machine type.
- Stop non-prod VMs when idle.
- Use autoscaling or schedules.
- Monitor sustained idle utilization.

## 4) Managed Instance Groups (MIG)

### What MIG provides
- Automatic scaling based on load.
- Autohealing based on health checks.
- Template-based consistent VM fleet.
- Rolling updates and versioned rollout patterns.

### Types
- Zonal MIG: single-zone fleet management.
- Regional MIG: multi-zone fleet for stronger availability.

### Typical use case
- Stateless web/API tier requiring predictable scaling and self-healing.

### Key exam point
- For VM-based scalable highly available architecture, MIG + load balancer is a common answer.

## 5) App Engine (deep dive)

### What it is
- Fully managed platform for deploying applications without managing most infrastructure details.

### Environment options
- Standard environment:
  - faster scale behavior and strong platform management.
- Flexible environment:
  - more runtime flexibility, container-based under the hood style.

### App Engine strengths
- Fast deployment.
- Built-in versioning and traffic splitting.
- Reduced ops overhead for many app patterns.

### App Engine watchouts
- Less control than VMs.
- Runtime/environment limitations depend on chosen environment.

### Common use cases
- Web apps and APIs where team wants platform productivity.
- Business apps with moderate custom infrastructure requirements.

## 6) Cloud Run (deep dive)

### What it is
- Fully managed platform to run stateless containers.
- Supports HTTP services and jobs.

### Core concepts
- Service: long-running endpoint that handles requests.
- Revision: immutable deployed version.
- Job: finite-run task for batch/one-off execution.

### Scaling behavior
- Automatic scale-up with traffic.
- Can scale to zero when idle (service configuration dependent).
- Concurrency controls impact latency and cost.

### Cloud Run strengths
- Very low ops.
- Fast deployment from container images.
- Good for microservices and APIs.

### Cloud Run watchouts
- Requires containerized app.
- Cold-start and concurrency tuning may be needed for strict latency needs.

### Security model highlights
- IAM-based invoker controls.
- Service account identity for downstream access.
- Works well with private service communication patterns.

## 7) Cloud Run functions (event-driven functions)

### What it is
- Function-style model for event-driven and lightweight service logic.
- Cloud Functions direction aligns with Cloud Run functions model.

### Typical triggers
- Storage object events.
- Pub/Sub messages.
- HTTP invocation.
- Other platform event sources.

### Best use cases
- File processing after upload.
- Notification and automation workflows.
- Lightweight data transformation triggers.

### Watchouts
- Keep functions focused and small in scope.
- Avoid turning one function into a monolithic workflow.

## 8) Compute service selection guide (exam-focused)

### Choose Compute Engine when
- Need OS-level control.
- Need custom software stack or agent/kernel-level behavior.
- Lift-and-shift migration with minimal initial app change.

### Choose App Engine when
- Need managed app platform.
- Want fast delivery with less infra management.
- App fits supported runtime model.

### Choose Cloud Run when
- Need scalable API/microservice with low ops.
- App can run in stateless container model.
- Traffic is variable or spiky.

### Choose Cloud Run functions when
- Workload is event-driven and function-sized.
- Need trigger-based automation logic.

## 9) Reliability patterns across compute services

### Compute Engine reliability
- Use MIG + load balancing + multi-zone deployment.
- Add cross-region DR for stricter business continuity.

### App Engine reliability
- Platform-managed scaling helps service continuity.
- Use region-aware architecture planning and dependency resilience.

### Cloud Run reliability
- Multi-region strategy possible for high continuity scenarios.
- Dependency design (DB, queue, external APIs) often determines reliability outcomes.

### Functions reliability
- Design idempotent handlers for retry-safe behavior.
- Handle duplicate events gracefully.

## 10) Security patterns across compute services

- Use least-privilege IAM and dedicated service accounts.
- Keep secrets in Secret Manager.
- Restrict inbound exposure and use private paths when possible.
- Enable logging and alerting for privileged changes and failures.

Compute-specific:
- VM patch management is customer responsibility.
- Managed services reduce platform patch burden but not app/data access responsibility.

## 11) Performance and scaling guidance

### VM workloads
- Right-size CPU/memory.
- Use autoscaling in MIG where applicable.
- Tune app/runtime for predictable latency.

### Cloud Run workloads
- Tune concurrency and min instances based on latency SLO.
- Reduce cold-start-sensitive paths.
- Keep hot dependencies close and efficient.

### App Engine workloads
- Use proper scaling config and monitor latency/error trends.

### Function workloads
- Keep handler logic quick.
- Move heavy tasks to job/queue pipeline when needed.

## 12) Cost tradeoffs across compute services

### Compute Engine
- Good control; can be costly if oversized or left running idle.

### App Engine
- Managed productivity; cost depends on scaling/runtime behavior.

### Cloud Run
- Strong for variable demand; pay aligns with usage.

### Functions
- Good for event spikes and small units; avoid overusing for long-running complex flows.

Cost rule:
- Match compute model to traffic and operational needs, then optimize.

## 13) Common mistakes (high exam relevance)

- Choosing VM when question asks minimum operational overhead.
- Choosing Cloud Run for workload requiring deep OS customization.
- Deploying critical VM app without multi-zone resilience.
- Ignoring service account least privilege.
- Assuming serverless always cheapest without workload analysis.
- Forgetting that data layer choice can dominate app performance/reliability.

## 14) Scenario answer patterns

1. Requirement: scalable HTTP API with minimal server management.
   - Typical answer: Cloud Run.

2. Requirement: strict OS-level dependency.
   - Typical answer: Compute Engine.

3. Requirement: event processing on object upload.
   - Typical answer: Cloud Run functions.

4. Requirement: VM fleet with autohealing and autoscaling.
   - Typical answer: MIG (often with load balancing).

5. Requirement: fast app platform deployment with less infra work.
   - Typical answer: App Engine.

## 15) Hands-on mini lab plan for compute chapter

1. Create one VM manually and connect via SSH.
2. Create instance template and MIG across two zones.
3. Put MIG behind load balancing path and test health behavior.
4. Deploy one sample API to Cloud Run.
5. Deploy one event-driven function for storage trigger.
6. Deploy one basic app to App Engine.
7. Compare IAM/service account setup across VM, Run, and function.
8. Compare cost and ops effort for each compute model.

## 16) Quick revision sheet

- Compute Engine = control.
- App Engine = managed app platform.
- Cloud Run = serverless containers for APIs/services/jobs.
- Cloud Run functions = event-driven function model.
- MIG = VM autoscaling + autohealing.
- "minimum ops" keyword usually points to Cloud Run/App Engine.
- "OS customization" keyword usually points to Compute Engine.

## 17) Practice questions with answers (40)

1. Which GCP compute service gives full OS control?  
   - Compute Engine.

2. Which service is serverless containers for HTTP APIs?  
   - Cloud Run.

3. Which service is best for event-triggered small logic?  
   - Cloud Run functions.

4. Which service is PaaS style managed app platform?  
   - App Engine.

5. What is MIG used for?  
   - Autoscaling and autohealing VM fleets.

6. Which is better for strict OS-level dependencies?  
   - Compute Engine.

7. Which is better for low-ops scalable web service?  
   - Cloud Run.

8. Which is usually better for lift-and-shift start?  
   - Compute Engine.

9. What does multi-zone MIG improve?  
   - Availability during zone failures.

10. What should be paired with MIG for resilient traffic routing?  
    - Health checks and load balancing.

11. In Cloud Run, what is a revision?  
    - Immutable deployed version.

12. In Cloud Run, what is a job?  
    - Finite-run batch/task execution.

13. Why use service accounts for compute workloads?  
    - Secure workload identity with scoped access.

14. Is VM patching provider responsibility or customer responsibility?  
    - Customer responsibility.

15. What is a common VM cost issue?  
    - Idle oversized instances.

16. Why is App Engine attractive for small teams?  
    - Reduced infra operations.

17. What is a common Cloud Run use case?  
    - Stateless API microservice.

18. What is a common Cloud Run functions use case?  
    - Triggering logic on storage/pubsub events.

19. Which keyword often indicates Cloud Run in exam question?  
    - "minimum server management."

20. Which keyword often indicates Compute Engine in exam question?  
    - "custom OS/runtime dependency."

21. What should be minimized in compute IAM design?  
    - Excessive permissions.

22. What is a poor security pattern for compute apps?  
    - Shared high-privilege service account across many apps.

23. What helps Cloud Run performance under burst traffic?  
    - Concurrency/min-instance tuning and dependency optimization.

24. What helps VM resilience in one region?  
    - Multi-zone deployment.

25. Is serverless always the cheapest option?  
    - Not always; depends on sustained traffic and workload profile.

26. What is the first step before selecting compute service?  
    - Clarify constraints (ops, control, scale, compliance, cost).

27. Which service typically has highest infra management burden?  
    - Compute Engine.

28. Which service typically has lowest infra management burden for containerized API?  
    - Cloud Run.

29. Which model fits simple event automation?  
    - Cloud Run functions.

30. What can startup scripts do in VMs?  
    - Boot-time automation and configuration.

31. What is a reliability anti-pattern in VM architecture?  
    - Single critical VM in one zone.

32. What is a likely answer for "autoheal failed VM instances"?  
    - Managed instance group with health checks.

33. Which service style supports version traffic splitting features strongly?  
    - App Engine.

34. What is needed for Cloud Run deployments?  
    - Container image and service configuration.

35. Why keep compute and database in same region when possible?  
    - Lower latency and lower transfer cost.

36. What should you monitor across compute models?  
    - Latency, errors, throughput, saturation, and deployment health.

37. What is the compute chapter exam trap?  
    - Confusing low-ops needs with full-control VM solutions.

38. For batch job that can fail and retry, what cost approach may help?  
    - Lower-cost interruptible compute pattern where applicable.

39. Which compute option best matches "just run this code on event" mindset?  
    - Cloud Run functions.

40. Final compute service decision rule?  
    - Choose the service with the least complexity that still meets control, reliability, security, and cost constraints.

---

Use this chapter with networking, IAM/security, and storage/database chapters for complete service-selection readiness.

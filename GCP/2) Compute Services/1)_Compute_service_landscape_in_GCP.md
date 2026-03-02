# Compute Service Landscape in GCP (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- How GCP compute options are positioned.
- Control vs operations tradeoff across services.
- Where each service fits in common architectures.
- Quick exam mapping for service selection.

## 1) Why compute landscape matters

Most ACE scenario questions are not about "what is this service."  
They test whether you can choose the right compute model for:
- operations effort,
- scalability needs,
- control requirements,
- reliability targets,
- cost constraints.

## 2) Core compute options in GCP

### Compute Engine (IaaS VM)
- You manage OS, runtime, patching, and many host-level choices.
- Best when deep control is required.

### App Engine (PaaS)
- Managed application platform.
- Faster delivery with reduced infrastructure management.

### Cloud Run (serverless containers)
- Run stateless containers.
- Strong fit for HTTP APIs, microservices, and jobs.

### Cloud Run functions (event-driven)
- Function-style execution triggered by events or HTTP.
- Strong for automation and event pipelines.

## 3) Compute continuum: control vs management

From highest control to lowest operations:
1. Compute Engine
2. App Engine / Cloud Run (depends on workload pattern)
3. Cloud Run functions

Rule of thumb:
- More control -> more ops responsibility.
- More managed -> less ops burden and faster iteration.

## 4) How architecture goals map to compute choices

### Goal: strict runtime/OS control
- Typical choice: Compute Engine.

### Goal: rapid app delivery with platform abstraction
- Typical choice: App Engine.

### Goal: scalable API with minimal server management
- Typical choice: Cloud Run.

### Goal: react to event triggers with small code units
- Typical choice: Cloud Run functions.

## 5) Common workload categories

### Legacy enterprise app
- Often starts on Compute Engine (rehost/replatform path).

### Modern API microservice
- Often ideal for Cloud Run.

### Web app with strong managed platform preference
- Often fits App Engine.

### Event automation
- Often fits Cloud Run functions.

## 6) Reliability view across landscape

- Compute Engine: reliability depends on your architecture (MIG, multi-zone, LB).
- App Engine: platform handles much scaling/fleet behavior.
- Cloud Run: managed scaling, service-level design still matters.
- Cloud Run functions: retries/idempotency design is critical.

## 7) Security responsibility view

Across all compute options, customer still owns:
- IAM design,
- workload identity,
- data access controls,
- secret handling,
- secure application behavior.

Managed services reduce infrastructure work, not security accountability.

## 8) Cost behavior summary

- Compute Engine: can be efficient with rightsizing but costly if idle/oversized.
- App Engine: managed productivity cost profile.
- Cloud Run/functions: strong cost fit for bursty and variable workloads.

Always match service to traffic and operations model.

## 9) Exam signals to recognize quickly

- "minimum ops" -> Cloud Run or App Engine direction.
- "custom OS or agent requirement" -> Compute Engine direction.
- "trigger on upload/message" -> Cloud Run functions direction.
- "autoheal VM fleet" -> MIG direction (Compute Engine pattern).

## 10) Common mistakes

- Choosing VMs for every workload by habit.
- Choosing serverless when workload needs deep OS customization.
- Ignoring reliability and security constraints while selecting compute.
- Selecting service by name familiarity instead of constraints.

## 11) Quick revision sheet

- Compute Engine = control-first.
- App Engine = managed app platform.
- Cloud Run = serverless containers for APIs/services/jobs.
- Cloud Run functions = event-driven function model.
- Service choice depends on control, ops, traffic, reliability, and cost.

## 12) Practice questions with answers

1. Which service provides highest OS control?
   - Compute Engine.

2. Which service is best known for serverless containerized APIs?
   - Cloud Run.

3. Which service model is function/event oriented?
   - Cloud Run functions.

4. Which service is PaaS style in GCP compute?
   - App Engine.

5. What is the main tradeoff axis in compute selection?
   - Control vs operational burden.

6. If requirement says "minimum operations," what direction is likely?
   - Managed/serverless services.

7. If requirement says "custom kernel module," what direction is likely?
   - Compute Engine.

8. Does managed compute remove need for IAM design?
   - No.

9. What is a common anti-pattern in compute selection?
   - One-service-fits-all decision.

10. What is the exam-safe selection strategy?
    - Choose least complex service that meets all constraints.

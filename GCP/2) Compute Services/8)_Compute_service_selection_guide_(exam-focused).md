# Compute Service Selection Guide (Exam-Focused Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- Fast decision framework for compute scenario questions.
- Keyword-to-service mapping.
- Elimination strategy for tricky options.
- High-frequency scenario bank with answer logic.

## 1) 5-step compute decision framework

1. Identify workload type
- web/API service,
- event trigger,
- legacy VM app,
- batch processing.

2. Identify control requirement
- full OS/runtime control needed or not.

3. Identify operations requirement
- minimum management or team can manage infra.

4. Identify traffic pattern
- steady,
- bursty,
- event-driven.

5. Identify reliability/compliance constraints
- zone or region resilience,
- location rules,
- security posture needs.

Then choose least complex compute service meeting all constraints.

## 2) Quick keyword decoder

- "minimum operational overhead" -> Cloud Run/App Engine direction.
- "custom OS/agent/kernel" -> Compute Engine direction.
- "trigger on upload/message/event" -> Cloud Run functions direction.
- "containerized API" -> Cloud Run direction.
- "autohealing VM fleet" -> MIG on Compute Engine direction.

## 3) Primary service selection rules

### Compute Engine when
- OS customization is required.
- legacy/lift-and-shift migration required.
- host-level runtime control is mandatory.

### App Engine when
- managed application platform is preferred.
- fast deployment and version traffic controls are important.

### Cloud Run when
- stateless containerized HTTP service is needed.
- low ops and automatic scaling are required.

### Cloud Run functions when
- small trigger-driven code execution is needed.

## 4) Tie-breaker rules

If two options seem valid:
1. prefer one matching explicit operational constraint,
2. prefer one matching workload shape (service vs event function),
3. prefer least complexity that still meets reliability/security.

## 5) Common wrong choices and corrections

- Wrong: Compute Engine for simple scalable API with low ops requirement.
  - Correct: Cloud Run is often better.

- Wrong: Cloud Run functions for long-running heavy process.
  - Correct: Cloud Run job/service or VM depending on requirement.

- Wrong: App Engine when workload needs deep OS customization.
  - Correct: Compute Engine.

- Wrong: Cloud Run for stateful workload requiring local persistent state assumptions.
  - Correct: redesign stateless service pattern or choose appropriate model.

## 6) Reliability overlay for service choice

After service selection, verify:
- zone-level resilience requirement met?
- region-level DR requirement met?
- rollout/rollback strategy available?

Example:
- Cloud Run + regional dependency bottleneck may still fail reliability target.

## 7) Security overlay for service choice

Always validate:
- least-privilege service account,
- secrets managed securely,
- public access only when explicitly required,
- audit/monitoring enabled.

## 8) Cost overlay for service choice

Always validate:
- workload traffic pattern matches service cost behavior,
- no obvious overprovisioning,
- architecture avoids unnecessary cross-region chatter.

## 9) Exam elimination method

1. Remove options violating explicit requirement.
2. Remove options that increase ops burden when low-ops requested.
3. Remove options that fail workload model fit.
4. Select simplest valid remaining option.

## 10) Scenario bank (25 quick patterns)

1. Scalable API, minimum server management -> Cloud Run.
2. OS-level package dependency -> Compute Engine.
3. Process image on bucket upload -> Cloud Run functions.
4. Managed app platform preference -> App Engine.
5. VM autoscale + autoheal needed -> MIG on Compute Engine.
6. Lift-and-shift first, modernize later -> Compute Engine.
7. Stateless microservice container -> Cloud Run.
8. Trigger code from Pub/Sub message -> Cloud Run functions.
9. Need deep host tuning -> Compute Engine.
10. Need gradual traffic shift between versions -> App Engine or Cloud Run revisions.
11. Event-driven lightweight notifications -> Cloud Run functions.
12. Batch container task -> Cloud Run job.
13. Legacy agent only supported on VM OS -> Compute Engine.
14. Team has small ops bandwidth -> Cloud Run/App Engine.
15. Strong VM control compliance standard -> Compute Engine.
16. Fast prototype with managed runtime -> App Engine.
17. HTTP microservice with burst traffic -> Cloud Run.
18. Single-purpose webhook receiver -> Cloud Run functions or Cloud Run (based on complexity).
19. Need low-level system package access -> Compute Engine.
20. Serverless low-ops requirement + container artifact available -> Cloud Run.
21. Event fan-out workflow step -> Cloud Run functions.
22. Existing monolith not containerized, urgent migration -> Compute Engine first.
23. Release control using revision traffic split -> Cloud Run.
24. Need strongest VM-level fleet reliability -> Regional MIG + LB.
25. Requirement mentions "no server management" explicitly -> avoid VM-first unless unavoidable.

## 11) Common exam traps

- Selecting the most familiar service, not best-fit service.
- Ignoring words like "minimum management."
- Ignoring workload shape (event vs service).
- Ignoring hidden security and reliability constraints.

## 12) Quick revision sheet

- Compute Engine = max control.
- App Engine = managed app platform.
- Cloud Run = serverless container services/jobs.
- Cloud Run functions = event-driven function handlers.
- MIG = scalable self-healing VM fleet pattern.
- Choose by constraints, not by preference.

## 13) Practice questions with answers

1. Which service is usually best for containerized stateless API with low ops?
   - Cloud Run.

2. Which service is usually best for custom OS dependency?
   - Compute Engine.

3. Which service is best for trigger-based processing?
   - Cloud Run functions.

4. Which service model is PaaS in this compute set?
   - App Engine.

5. Which pattern is best for autohealing VM fleet?
   - MIG.

6. What should you do first in scenario questions?
   - Extract hard constraints.

7. If question says "minimum operations," which option is less likely?
   - Raw VM-first architecture.

8. If options include compliant and non-compliant region choices, choose?
   - Compliant choice.

9. What is tie-breaker when two options seem valid?
   - Least complexity that meets all constraints.

10. Top exam takeaway for compute selection?
    - Service choice must align with workload type, control needs, and ops constraints.

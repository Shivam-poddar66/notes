# 7) Load Balancing (Deep Dive)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Why load balancing is central
Load balancing provides:
- traffic distribution,
- health-aware failover,
- scalable entry points,
- safer deployment strategies.

## 2. Layer selection
### Application Load Balancer (L7)
- HTTP/HTTPS aware.
- Host/path routing.
- Suitable for web and API routing logic.

### Network Load Balancer (L4)
- TCP/UDP focused traffic distribution.
- Suitable when protocol-level balancing is needed.

## 3. External vs internal
- External load balancing: internet-facing services.
- Internal load balancing: private service-to-service traffic.

## 4. Core components
- Frontend (IP, protocol, port).
- Backend services/targets.
- Health checks.
- Routing policies.

## 5. Reliability patterns
- Multi-zone backends.
- Health-check based auto failover.
- Regional or multi-region backend strategy based on RTO/RPO needs.

## 6. Release patterns
- Canary rollout through weighted or controlled traffic strategies.
- Blue/green style cutover with rollback plan.
- Gradual ramp with error/latency guardrails.

## 7. Security patterns
- TLS termination and certificate lifecycle.
- Controlled ingress and edge protections.
- Restrict backend access to load balancer paths where applicable.

## 8. Performance patterns
- Keep backends near users/dependencies where possible.
- Tune health checks to detect failures quickly but avoid false positives.
- Monitor p95/p99 latency and backend saturation.

## 9. Common mistakes
- Single-zone backend for critical app.
- Missing or misconfigured health checks.
- Using L4 when L7 routing logic is required.

## 10. Exam cues
- "Path-based web routing" -> L7/application load balancer.
- "TCP/UDP distribution" -> L4/network load balancer.
- "High availability web tier" -> LB + health checks + multi-zone backends.

## One-line memory hook
Load balancing is both traffic distribution and reliability control plane.

## Practical example
- Requirement: `/api` and `/admin` should go to different backend pools.
- Design: Use external L7 Application Load Balancer with path-based rules.
- Why: L7 supports HTTP path routing and health-aware failover.


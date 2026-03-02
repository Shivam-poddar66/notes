# 1) Core Networking Mental Model (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## Why this topic matters
Most networking mistakes come from tool-first thinking. Correct design starts with traffic intent and constraints.

## 1. The 5-question model
Ask these first:
1. Who needs to talk to whom?
2. Over which protocol and port?
3. Should traffic be private, public, or hybrid?
4. What reliability target is required (zone, region, hybrid continuity)?
5. What security/compliance restrictions must be enforced?

## 2. The networking control planes
- Addressing plane: VPC, subnet, CIDR boundaries.
- Reachability plane: routes, peering, VPN/interconnect.
- Policy plane: firewall rules, org guardrails, private access controls.
- Traffic distribution plane: load balancing and health checks.
- Operations plane: logging, monitoring, alerting, runbooks.

## 3. Connectivity-first architecture pattern
Design order:
1. Communication matrix.
2. IP and subnet plan.
3. Route path and boundary design.
4. Security policy enforcement.
5. Availability and failover behavior.
6. Observability and troubleshooting flow.

## 4. Common design tradeoffs
- Simplicity vs flexibility: fewer moving parts reduce operational risk.
- Private-by-default vs internet exposure: expose only required entry points.
- Performance vs cost: cross-region traffic may improve resilience but increase latency and egress spend.

## 5. Exam answer framework
When multiple options seem valid:
1. Eliminate options violating explicit requirement terms.
2. Eliminate options with unnecessary public exposure.
3. Eliminate options with avoidable complexity.
4. Choose the simplest design that satisfies connectivity, security, and reliability targets.

## 6. Frequent exam cues
- "minimum management" -> managed networking pattern over custom complexity.
- "private communication only" -> private IP paths, internal services, private DNS.
- "high availability" -> multi-zone backend with health checks and failover.
- "hybrid requirement" -> VPN/Interconnect + routing model.

## 7. Quick checklist
- Traffic matrix documented.
- CIDR plan non-overlapping.
- Route intent validated.
- Firewall least privilege applied.
- Monitoring and alerting configured.

## One-line memory hook
Networking is traffic intent + controlled path + least-privilege policy + tested failover.

## Practical example
- Requirement: Build a shopping app where users access web tier, web tier calls payment API, and only DB stays private.
- Design: External LB -> web/app subnet -> private DB subnet with strict firewall.
- Why: Communication intent is clear first, then security and resilience are layered.


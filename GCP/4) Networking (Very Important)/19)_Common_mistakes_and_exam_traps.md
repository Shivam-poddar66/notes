# 19) Common Mistakes And Exam Traps (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Fundamental traps
- Thinking subnets are global.
- Assuming peering is transitive.
- Ignoring CIDR overlap constraints.

## 2. Security traps
- Broad ingress on management ports.
- Unnecessary public exposure for private workloads.
- No firewall rule logging for critical paths.

## 3. Routing traps
- Misreading longest prefix behavior.
- Overlapping custom routes with unintended precedence.
- Missing dynamic routing context in hybrid questions.

## 4. Load balancing traps
- Choosing L4 when HTTP path routing is required.
- Single-zone backends with no failover design.
- Health check misconfiguration ignored.

## 5. Hybrid traps
- Single VPN tunnel for critical connectivity.
- No failover testing.
- Choosing interconnect when requirements and timeline fit VPN.

## 6. Observability traps
- Collecting logs without alerting plan.
- Monitoring infrastructure only and missing user-impact indicators.

## 7. Cost traps
- Ignoring cross-region transfer cost.
- Over-logging without retention strategy.
- Idle network resources left active.

## 8. Exam survival checklist
- Confirm layer (L3/L4/L7) requirement.
- Confirm private/public exposure requirement.
- Confirm reliability domain (zone/region/hybrid).
- Confirm governance requirement (centralized vs local).

## One-line memory hook
Most networking mistakes are assumption mistakes: verify every requirement word before picking services.

## Practical example
- Trap: A-B peering and B-C peering are configured, question asks if A can reach C.
- Correct answer: No, peering is non-transitive.
- Why: Each peering link is pairwise and does not relay transit traffic.


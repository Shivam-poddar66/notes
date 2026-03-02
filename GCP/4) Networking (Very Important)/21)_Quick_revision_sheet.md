# 21) Quick Revision Sheet (Detailed)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## Must-remember facts
- VPC is global.
- Subnets are regional.
- Firewall rules are stateful and priority-based.
- Peering is non-transitive and requires non-overlapping CIDRs.
- Cloud NAT provides outbound internet for private resources without external IP.
- L7 load balancing is for HTTP/HTTPS logic; L4 for TCP/UDP distribution.
- Shared VPC centralizes network management across projects.
- Cloud Router supports dynamic routing in hybrid designs.

## Fast keyword map
- "private outbound no public IP" -> Cloud NAT.
- "central network control" -> Shared VPC.
- "connect two VPCs privately" -> VPC Peering.
- "on-prem encrypted tunnel" -> VPN/HA VPN.
- "enterprise private high-throughput hybrid" -> Interconnect.
- "path-based routing" -> L7/application LB.
- "internal DNS" -> private Cloud DNS zone.
- "private access to Google APIs" -> Private Google Access.

## Decision order
1. Connectivity requirement.
2. Exposure model (private/public/hybrid).
3. Security policy.
4. Reliability target.
5. Cost and operations simplicity.

## Top traps to avoid
- Assuming subnet is global.
- Assuming peering transitivity.
- Ignoring CIDR overlap.
- Missing health checks.
- Overly broad firewall permissions.

## Final chapter rule
Design private-by-default, validate route/policy behavior, and prove resilience with tests.

## Practical example
- 60-second exam flow:
1. Identify private/public/hybrid requirement.
2. Map to core service (NAT, LB, Peering, VPN, etc.).
3. Add security and HA checks.
4. Eliminate complex options not required.
- Why: Fast structured reasoning reduces trap mistakes.


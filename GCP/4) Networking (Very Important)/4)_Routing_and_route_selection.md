# 4) Routing And Route Selection (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Why routing matters
Routing determines whether traffic reaches the correct destination and follows the expected security and performance path.

## 2. Route sources
- System routes (subnet-local and default behaviors).
- Custom static routes.
- Dynamic routes from Cloud Router in hybrid/BGP scenarios.

## 3. Route decision behavior (high level)
1. Longest prefix match wins.
2. If prefix equal, route priority decides.
3. Next hop type controls actual path behavior.

## 4. Design patterns
- Keep route tables minimal and intentional.
- Avoid broad custom catch-all unless required and well documented.
- Validate route impact before production rollout.

## 5. Hybrid dynamic routing
- Cloud Router exchanges routes via BGP.
- Useful for automated path updates in VPN/interconnect architectures.
- Reduces manual static route management burden.

## 6. Common route scenarios
- Private egress control path.
- On-prem route advertisement.
- Segmented route policy between trust boundaries.

## 7. Troubleshooting flow
1. Confirm destination IP and subnet.
2. Check effective route table.
3. Validate next hop health.
4. Validate firewall policy.
5. Validate dependency endpoint availability.

## 8. Common mistakes
- Overlapping routes with unintended precedence.
- Assuming transitive routing via peering.
- Forgetting route changes in DR/failover plans.

## 9. Exam cues
- "Traffic should follow specific path" -> route specificity and priority.
- "Hybrid dynamic updates" -> Cloud Router/BGP pattern.

## One-line memory hook
Routing logic is deterministic, so troubleshooting starts with effective route visibility.

## Practical example
- Requirement: Traffic to on-prem range `10.50.0.0/16` must go via VPN, everything else uses default route.
- Design: Add specific custom route for `10.50.0.0/16` to VPN next hop.
- Why: Longest prefix match sends only on-prem traffic through VPN.


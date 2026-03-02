# 3) Subnets And IP Addressing Strategy (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Subnet fundamentals
- Subnet is regional.
- It provides private IP range for resources in that region.
- Good subnet planning prevents routing, peering, and hybrid conflicts.

## 2. Address planning workflow
1. Inventory current network ranges.
2. Forecast 1 to 3 year growth by environment.
3. Reserve non-overlapping CIDRs by region.
4. Document reserved blocks for hybrid and future expansions.

## 3. Sizing strategy
- Avoid very small ranges that block autoscaling.
- Avoid random oversized ranges without plan.
- Use predictable sizing by environment tier.

## 4. Public vs private address model
- Private IP for internal traffic by default.
- External IP only for explicit inbound internet requirements.
- Use controlled egress paths for private workloads needing outbound internet.

## 5. Cross-project and peering implications
- Overlapping CIDRs block peering and complicate hybrid route exchange.
- Standardize IP governance across teams.

## 6. Subnet segmentation patterns
- App subnet.
- Data subnet.
- Management/admin subnet.
- Shared services subnet.

Segmentation improves both security and operational clarity.

## 7. Troubleshooting signals
- Unexpected reachability failures often trace back to overlap or incorrect route assumptions.
- Validate effective routes and firewall after subnet changes.

## 8. Common mistakes
- Reusing same CIDR in multiple environments.
- Mixing sensitive and public-facing services in same flat subnet without policy controls.
- Forgetting capacity for future region expansion.

## 9. Exam cues
- "Need private scalable network addressing" -> subnet/CIDR planning focus.
- "Peering not working" -> check overlapping ranges first.

## One-line memory hook
Good IP planning is invisible when done right and painful when skipped.

## Practical example
- Requirement: Team plans peering with another VPC next quarter.
- Design: Reserve non-overlapping blocks (`10.10.0.0/16` and `10.20.0.0/16`) now.
- Why: Peering fails with overlap, so early IP planning avoids redesign.


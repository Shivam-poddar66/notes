# 10) VPC Peering And Shared VPC (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. VPC Peering fundamentals
- Enables private connectivity between two VPCs.
- Requires non-overlapping CIDR ranges.
- Peering is non-transitive.

## 2. Shared VPC fundamentals
- Host project owns centralized network resources.
- Service projects consume shared network.
- Supports enterprise governance with centralized network control.

## 3. Peering vs Shared VPC
### Use VPC Peering when:
- You need direct private connectivity between specific VPC pairs.
- Teams/projects remain relatively independent.

### Use Shared VPC when:
- Central network team manages many projects.
- Standardized controls are required across organization units.

## 4. Governance and security patterns
- Standardize CIDR and naming processes.
- Centralize core firewall/security policies.
- Limit who can create high-risk network paths.

## 5. Operational patterns
- Document connectivity matrix between projects.
- Validate route behavior after peering changes.
- Keep ownership model clear for shared resources.

## 6. Common mistakes
- Assuming transitive peering connectivity.
- Attempting peering with overlapping CIDRs.
- Shared VPC without clear host/service project governance model.

## 7. Exam cues
- "Central network administration for many projects" -> Shared VPC.
- "Need private connectivity between two VPCs" -> Peering.

## One-line memory hook
Peering connects networks pairwise; Shared VPC centralizes network control at scale.

## Practical example
- Requirement: 15 application projects need one central networking team.
- Design: Use Shared VPC host project and attach app projects as service projects.
- Why: Centralized subnet and firewall governance is easier than many peer links.


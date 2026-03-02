# 2) VPC Architecture And Modes (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. VPC fundamentals
- VPC is global.
- Subnets are regional.
- One VPC can host resources in multiple regions with internal connectivity patterns.

## 2. VPC network modes
### Auto mode
- Google creates regional subnets automatically.
- Faster to start, weaker long-term IP governance.

### Custom mode
- You create and control subnet ranges.
- Preferred for production, enterprise, and hybrid planning.

## 3. IP architecture strategy
- Reserve CIDR ranges per environment and region.
- Avoid overlap with existing and future hybrid networks.
- Keep growth headroom for scaling and service expansion.

## 4. Environment segmentation patterns
- Separate prod and non-prod network boundaries.
- Use project and folder boundaries for governance.
- Apply centralized controls for common security baselines.

## 5. Secondary range planning
- Needed for workloads requiring extra address pools (for example container and service ranges in some architectures).
- Plan early to avoid migration complexity.

## 6. Design examples
- Small team startup:
  - custom VPC with per-region subnet and tight firewall policy.
- Enterprise:
  - custom VPC + Shared VPC governance + strict CIDR reservation process.

## 7. Operational best practices
- Naming convention with environment and region.
- Infra as code for VPC/subnet consistency.
- Periodic CIDR utilization review.
- Documented change approval for network updates.

## 8. Common mistakes
- Using auto mode in regulated enterprise environment.
- Overlapping CIDRs across projects.
- Flat single-network design with no trust separation.

## 9. Exam cues
- "Centralized IP planning and control" -> custom mode.
- "Global network with regional segmentation" -> VPC plus subnet model.

## One-line memory hook
Custom mode VPC is the production default when governance and scalability matter.

## Practical example
- Requirement: A company runs dev, stage, and prod across 2 regions with future hybrid plans.
- Design: Use custom mode VPC and create planned regional subnets per environment.
- Why: Custom mode prevents random CIDR allocation and avoids future overlap.


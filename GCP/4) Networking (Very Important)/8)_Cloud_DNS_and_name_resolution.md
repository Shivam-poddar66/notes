# 8) Cloud DNS And Name Resolution (Detailed Notes)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Role of DNS in cloud architecture
DNS is control-plane critical. Service reachability, failover patterns, and latency optimization often depend on correct name resolution.

## 2. Cloud DNS zone types
- Public zone: internet-resolvable records.
- Private zone: internal name resolution within authorized VPC scope.

## 3. Internal service discovery patterns
- Use private DNS names for internal APIs and shared services.
- Keep naming standard predictable by environment, team, and service.
- Avoid hardcoding IPs in application config.

## 4. TTL strategy
- Lower TTL for records that may change during migration/failover.
- Higher TTL for stable records to reduce query overhead.
- Choose TTL based on operational change frequency.

## 5. Reliability and DR considerations
- Document DNS ownership and change process.
- Keep rollback-ready record updates for production changes.
- Test name resolution from workload context, not only admin machine.

## 6. Security considerations
- Limit who can edit critical zones.
- Audit DNS record changes.
- Avoid exposing internal endpoints via public zones.

## 7. Common issues
- Wrong zone type selected.
- Missing private zone authorization for required VPC.
- Stale records and TTL-related propagation confusion.

## 8. Exam cues
- "Internal-only service discovery" -> private DNS zone.
- "Public website domain management" -> public DNS zone.

## One-line memory hook
Correct DNS design is foundational for reachability, stability, and controlled change management.

## Practical example
- Requirement: Internal app should resolve `orders.internal.company` privately.
- Design: Create private Cloud DNS zone and authorize required VPCs.
- Why: Internal services resolve without exposing records publicly.


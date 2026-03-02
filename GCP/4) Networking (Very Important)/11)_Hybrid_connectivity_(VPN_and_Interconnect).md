# 11) Hybrid Connectivity (VPN And Interconnect) - Detailed Notes

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Hybrid connectivity objective
Secure and reliable communication between on-premises environments and Google Cloud.

## 2. Cloud VPN and HA VPN
- Encrypted tunnels over internet paths.
- Faster to set up compared with dedicated link options.
- HA designs use redundancy for resilience.

## 3. Interconnect overview
- Dedicated or partner private connectivity options.
- Higher throughput and predictable network profile for enterprise hybrid workloads.

## 4. Cloud Router role
- Dynamic route exchange via BGP.
- Supports automated route updates and failover behavior in hybrid designs.

## 5. Selection guidelines
### VPN first when:
- Time-to-implement is critical.
- Throughput needs are moderate.
- Budget and operational simplicity are prioritized.

### Interconnect first when:
- High sustained throughput and predictable private connectivity are required.
- Enterprise scale hybrid traffic is core architecture requirement.

## 6. Reliability patterns
- Redundant tunnels/paths.
- Route failover testing.
- Clear runbooks for hybrid outage events.

## 7. Security patterns
- Encrypted transport.
- Least-privilege admin and route control permissions.
- Audit route and connectivity changes.

## 8. Common mistakes
- Single tunnel design for critical workloads.
- No failover testing.
- Underestimating operational complexity of large hybrid estates.

## 9. Exam cues
- "Encrypted connection quickly" -> VPN.
- "Enterprise private high-throughput link" -> Interconnect.
- "Dynamic route exchange" -> Cloud Router/BGP.

## One-line memory hook
Hybrid success requires redundancy, dynamic routing clarity, and tested failover behavior.

## Practical example
- Requirement: Move moderate on-prem traffic quickly to GCP in 2 weeks.
- Design: Deploy HA VPN + Cloud Router for dynamic route exchange.
- Why: Faster rollout than Interconnect with resilient encrypted tunnels.


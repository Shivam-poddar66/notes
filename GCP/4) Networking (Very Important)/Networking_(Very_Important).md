# Networking (Very Important) - Brief but Detailed Notes With Examples

Last updated: February 25, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## What this chapter covers
- End-to-end networking design flow for Google Cloud.
- Core services: VPC, subnets, routing, firewalls, NAT, load balancing, DNS.
- Private and hybrid connectivity patterns: PGA, PSC, Peering, Shared VPC, VPN, Interconnect.
- Security, reliability, performance, observability, and cost tradeoffs.
- Exam-oriented scenario mapping and elimination strategy.

## 1) Core networking mental model
Networking decisions should follow this order:
1. Connectivity: who talks to whom.
2. Exposure: private, public, or hybrid.
3. Policy: allow/deny boundaries.
4. Resilience: failover and recovery targets.
5. Operations: monitoring, alerts, troubleshooting.

Use a communication matrix first (source, destination, protocol/port, sensitivity, availability requirement).  
Exam rule: choose the simplest architecture that satisfies all constraints.

Example:
- Requirement: users access web app, app talks to private database.
- Recommended setup: external LB -> app VMs in private subnet -> private DB subnet.
- Why: only web entry is public, app and DB stay private.

## 2) VPC architecture and modes
- VPC is global; subnets are regional.
- Auto mode creates predefined subnets quickly but reduces IP planning control.
- Custom mode gives full CIDR planning control and is preferred for production/enterprise.

Design guidance:
- Separate environments and trust boundaries.
- Reserve CIDR space for future growth and hybrid connectivity.
- Plan secondary ranges early for containerized workloads.

Exam cue: "centralized IP planning" usually means custom mode VPC.

Example:
- Requirement: company has dev, stage, prod across 2 regions.
- Recommended setup: one custom VPC with separate regional subnets per environment.
- Why: easier governance, no random auto-mode ranges.

## 3) Subnets and IP addressing strategy
- Subnets are regional address pools.
- Non-overlapping CIDRs are mandatory for peering and smooth hybrid expansion.
- Private IP by default; external IP only when inbound internet access is required.

Quick IP planning checklist:
1. Capacity forecast by region and environment.
2. CIDR allocation and gap reservation.
3. Naming standards.
4. Future hybrid reserved blocks.

Exam cue: peering failures often point to CIDR overlap.

Example:
- Requirement: connect VPC-A and VPC-B later using peering.
- Recommended setup: allocate `10.10.0.0/16` to VPC-A and `10.20.0.0/16` to VPC-B.
- Why: non-overlapping ranges avoid peering conflict.

## 4) Routing and route selection
Route behavior (high level):
1. Longest prefix match wins.
2. If prefix is equal, lower priority value wins.
3. Next-hop type determines actual path.

Route sources:
- System routes.
- Custom static routes.
- Dynamic BGP routes via Cloud Router (hybrid scenarios).

Best practice: keep route intent explicit, avoid unnecessary catch-all custom routes, and validate effective routes before production changes.

Example:
- Requirement: `10.30.5.8` should go to on-prem over VPN.
- Recommended setup: add specific route `10.30.0.0/16` via VPN tunnel.
- Why: more specific route wins over default internet route.

## 5) Firewall rules and hierarchical policies
- VPC firewall rules are stateful and priority based.
- Rules are ingress/egress and can target by tag or service account.
- Hierarchical firewall policies enforce baseline controls at org/folder scope.

Secure pattern:
- Deny broad paths first.
- Allow minimum required protocols/ports/sources.
- Log critical allow/deny rules.

Exam cue: "central security governance across projects" maps to hierarchical policy + Shared VPC style governance.

Example:
- Requirement: allow SSH only from office IPs.
- Recommended setup: deny-all admin ingress, then allow TCP 22 from office CIDR only.
- Why: least privilege and clear priority control.

## 6) Cloud NAT and internet egress patterns
Cloud NAT provides outbound internet for private resources without external IPs.

Important limits:
- No inbound internet access.
- Not a firewall replacement.

Recommended pattern:
- Private workloads + Cloud NAT + restrictive egress firewall + monitoring.

Exam cue: "private VM needs outbound internet, no public IP" means Cloud NAT.

Example:
- Requirement: private VM must download OS updates.
- Recommended setup: private subnet VM + Cloud NAT + egress allow for update endpoints.
- Why: outbound works, VM remains non-public.

## 7) Load balancing (deep dive)
Why it matters:
- Traffic distribution.
- Health-aware failover.
- Safer rollout patterns.

Selection:
- L7/Application LB for HTTP/HTTPS with host/path routing.
- L4/Network LB for TCP/UDP traffic distribution.
- External LB for internet-facing entry.
- Internal LB for private east-west traffic.

Design essentials: frontend config, backend services/NEGs, health checks, failover policy.

Example:
- Requirement: `/api` goes to backend-A, `/admin` goes to backend-B.
- Recommended setup: external L7 Application LB with path rules.
- Why: path-aware routing is an L7 feature.

## 8) Cloud DNS and name resolution
- Public zones for internet domains.
- Private zones for internal service discovery in authorized VPC scope.

Operational guidance:
- Keep ownership and change process clear.
- Use TTL values based on change frequency and failover plans.
- Validate DNS from workload context, not only admin laptops.

Exam cue: "internal-only resolution" means private Cloud DNS zone.

Example:
- Requirement: app VMs should resolve `db.internal.company` privately.
- Recommended setup: private Cloud DNS zone mapped to the VPC.
- Why: no public DNS exposure for internal endpoints.

## 9) Private access to Google APIs (PGA and PSC)
Private Google Access (PGA):
- Lets private resources without external IP reach supported Google APIs/services.

Private Service Connect (PSC):
- Enables controlled private service connectivity between consumers and endpoints.

Selection rule:
- Need private API egress from private subnet resources -> PGA.
- Need private service-consumption boundaries and endpoint control -> PSC.

Example:
- Requirement: private VM must call Cloud Storage API without external IP.
- Recommended setup: enable PGA on subnet.
- Why: API access stays private from VM side.

## 10) VPC Peering and Shared VPC
VPC Peering:
- Private VPC-to-VPC connectivity.
- Non-transitive.
- Requires non-overlapping CIDRs.

Shared VPC:
- Host project centralizes networking.
- Service projects consume centrally managed subnets/policies.
- Better for large organizations with governance requirements.

Exam cue: "many projects, central network team" means Shared VPC.

Example:
- Requirement: 20 app projects need one central network/security team.
- Recommended setup: Shared VPC host project + service projects.
- Why: centralized subnet and firewall governance.

## 11) Hybrid connectivity (VPN and Interconnect)
Cloud VPN / HA VPN:
- Encrypted tunnels over internet.
- Fast to implement.
- HA requires redundancy and failover design.

Cloud Interconnect:
- Private dedicated/partner connectivity.
- Better for high-throughput, predictable enterprise hybrid traffic.

Cloud Router:
- BGP-based dynamic route exchange and automatic path updates.

Selection shortcut: speed and simplicity -> VPN; sustained enterprise throughput -> Interconnect.

Example:
- Requirement: connect data center to GCP in 2 weeks for moderate traffic.
- Recommended setup: HA VPN + Cloud Router.
- Why: quick deployment with encrypted and resilient paths.

## 12) Serverless and compute networking integration
Common requirement: serverless services must reach private dependencies securely.

Key controls:
- Restrictive ingress model.
- Explicit private egress/integration path.
- Service identity and network policy alignment.

Operational focus:
- Validate latency overhead.
- Monitor dependency reachability.
- Keep rollback path for networking changes.

Example:
- Requirement: Cloud Run service must access private Redis in VPC.
- Recommended setup: private integration path + least-privilege service account.
- Why: no public exposure while keeping identity-based control.

## 13) Network monitoring and observability
Core telemetry:
- VPC Flow Logs.
- Firewall logs.
- Load balancer metrics and health checks.
- VPN/Interconnect tunnel and route health.
- DNS signal where relevant.

Alert on:
- Backend unhealthy spikes.
- Tunnel down events.
- Deny spikes on critical paths.
- Abnormal egress patterns.

Troubleshooting order: DNS -> route -> firewall -> backend health -> dependencies.

Example:
- Requirement: sudden app timeout spike.
- Recommended setup: check LB health, flow logs, and firewall denies in that order.
- Why: quickly isolates whether issue is backend, path, or policy.

## 14) Network security patterns
Security baseline:
- Private-by-default paths.
- Least-privilege rules.
- Strong segmentation by trust boundary.
- Logging and auditability on critical controls.

High-value practices:
- Limit public endpoints.
- Protect admin paths tightly.
- Use service-account-aware policy targeting when possible.
- Review stale broad rules regularly.

Example:
- Requirement: secure production admin access.
- Recommended setup: no public SSH, use controlled bastion or private admin path + strict firewall.
- Why: reduces attack surface significantly.

## 15) Reliability and DR patterns
Zonal reliability:
- Multi-zone backends + health checks + load balancing.

Regional continuity:
- Multi-region design for strict outage tolerance.

Hybrid reliability:
- Redundant tunnels/links.
- Dynamic routing and failover testing.

DR readiness:
- Runbooks with clear owner, trigger criteria, failover steps, and validation checks.

Example:
- Requirement: service must survive zone failure.
- Recommended setup: backend in 2+ zones behind LB with health checks.
- Why: traffic shifts automatically from unhealthy zone.

## 16) Performance and latency optimization
Primary latency drivers:
- User-to-service distance.
- Cross-region chatter.
- Extra network hops and TLS/session overhead.
- Saturated backends.

Optimization pattern:
- Place compute close to users and data.
- Minimize unnecessary hops and cross-region calls.
- Track p95/p99, not just averages.
- Use edge caching/CDN for static repeatable content.

Example:
- Requirement: India users see slow response from US-only deployment.
- Recommended setup: regional deployment closer to users + CDN for static assets.
- Why: distance and origin load are both reduced.

## 17) Cost optimization and tradeoffs
Big cost drivers:
- Internet egress.
- Inter-region data transfer.
- Load balancer usage.
- NAT and logging volume.
- Hybrid link design.

Cost control pattern:
- Co-locate dependent services.
- Reduce avoidable cross-region traffic.
- Remove unused public endpoints/LB resources.
- Tune logging retention and volume.

Rule: never trade away required security/reliability for minor cost savings.

Example:
- Requirement: network bill increased 35 percent this month.
- Recommended setup: find top inter-region flows and move chatty services into same region.
- Why: cross-region transfer is often a hidden major cost.

## 18) Scenario answer patterns (high exam relevance)
High-frequency mappings:
1. Private VM outbound internet, no public IP -> Cloud NAT.
2. Central control across many projects -> Shared VPC.
3. Private VPC-to-VPC link -> VPC Peering.
4. Fast encrypted on-prem connectivity -> VPN / HA VPN.
5. High-throughput private hybrid -> Interconnect (+ Cloud Router).
6. HTTP host/path routing -> L7 Application LB.
7. TCP/UDP distribution -> L4 Network LB.
8. Internal service name resolution -> private Cloud DNS.
9. Private resources to Google APIs -> PGA.

Elimination strategy:
- Remove options violating explicit requirements.
- Remove unnecessary public exposure.
- Remove extra complexity not required.

Example:
- Requirement: "private VM without public IP needs Google API access".
- Best answer: PGA (not Cloud NAT if requirement is specifically Google APIs).
- Why: wording points to private Google API path.

## 19) Common mistakes and exam traps
- Treating subnets as global (they are regional).
- Assuming peering is transitive.
- Ignoring CIDR overlap constraints.
- Expecting Cloud NAT to support inbound internet.
- Choosing L4 LB for path-based HTTP requirements.
- Missing health checks/failover tests.
- Broad firewall rules on admin ports.

Example:
- Trap question: choose peering to connect A-B and B-C and expect A-C reachability.
- Correct logic: peering is non-transitive, so A-C still not connected.
- Why: direct connectivity must be designed explicitly.

## 20) Full roadmap (study and implementation)
Day 1 to 3:
- VPC/subnet/CIDR/routes/firewall fundamentals.

Day 4 to 8:
- NAT, load balancing, DNS, private API access labs.

Day 9 to 12:
- Peering, Shared VPC, VPN/HA VPN, Interconnect, Cloud Router.

Day 13 to 15:
- Security baselines, observability dashboards, runbook workflow.

Day 16 to 19:
- Timed scenario practice + troubleshooting drills.

Day 20 to 21:
- Final revision sheet + weak-area closure.

Example weekly flow:
- Week 1: foundations + one hands-on lab daily.
- Week 2: hybrid + reliability + observability labs.
- Week 3: timed scenario practice and weak-area revisions.

## 21) Quick revision sheet
Must remember:
- VPC global, subnet regional.
- Firewall rules stateful + priority based.
- Peering non-transitive + no overlapping CIDRs.
- Cloud NAT = outbound-only internet for private resources.
- L7 for HTTP/HTTPS logic; L4 for TCP/UDP.
- Shared VPC for centralized multi-project governance.
- Cloud Router for dynamic hybrid routing.
- Private DNS for internal name resolution.

Fast keyword map:
- "No public IP outbound" -> Cloud NAT.
- "Central network governance" -> Shared VPC.
- "Private VPC-to-VPC connectivity" -> Peering.
- "Encrypted hybrid quickly" -> VPN.
- "High-throughput hybrid private link" -> Interconnect.
- "Path-based web routing" -> L7 LB.
- "Private Google API access" -> PGA.

Final chapter rule:
Pick the path by connectivity first, then apply security, reliability, operations, and cost filters.

Example 60-second answer method:
1. Read constraint words twice.
2. Pick core service.
3. Add security and HA layer.
4. Remove overly complex options.

# 2) DNS Load Balancing, GeoDNS, and Anycast BGP

## Executive Overview
Before a client's TCP packet reaches a physical load balancer, global routing mechanisms direct traffic to the optimal datacenter or Point of Presence (PoP) across the globe.

---

## 1. DNS Load Balancing

### A. Round-Robin DNS
A DNS server responds to queries for `api.example.com` with a rotating list of IP addresses:
```text
Query 1: 198.51.100.1, 198.51.100.2, 198.51.100.3
Query 2: 198.51.100.2, 198.51.100.3, 198.51.100.1
```
- **Flaws**:
  1. **DNS Caching**: Client OS, browsers, and ISP recursive resolvers cache DNS records for hours (ignoring TTLs), causing traffic imbalances.
  2. **No Health Awareness**: If IP `198.51.100.1` dies, the DNS server continues handing it out until manually updated, blackholing user traffic.

### B. GeoDNS (Geolocation Routing)
- The authoritative DNS server inspects the resolver's subnet (via EDNS Client Subnet - RFC 7871).
- Maps user to the closest physical datacenter:
  - User in Berlin $\to$ IP of Frankfurt datacenter.
  - User in Tokyo $\to$ IP of Tokyo datacenter.
- Used extensively in AWS Route 53 Geolocation / Latency-based routing.

---

## 2. Anycast BGP Routing (The Modern Edge Standard)

Unlike Unicast (where each IP address maps to exactly one physical server), **Anycast** assigns the **exact same public IP address** to hundreds of routers across global datacenters.

```text
                                  Client in London
                                         │
                                         ▼
                             BGP Router in Amsterdam
                                         │ (Shortest AS Path)
                                         ▼
                           [ Datacenter: Frankfurt ]
                                (IP: 1.1.1.1)
                                
                                  Client in Sydney
                                         │ (Shortest AS Path)
                                         ▼
                            [ Datacenter: Sydney ]
                                (IP: 1.1.1.1)
```

### Key Architectural Benefits:
1. **Zero DNS Propagation Lag**: Users connect to the same IP. Routing changes are handled in seconds at the internet router level via BGP.
2. **DDoS Absorption**: A volumetric 500 Gbps DDoS attack launched from compromised IoT devices in North America is absorbed exclusively by North American edge PoPs, leaving European and Asian datacenters completely unaffected.
3. **Automatic Failover**: If a datacenter loses power, its BGP daemon stops announcing the route. Internet routers automatically withdraw the route and send traffic to the next closest healthy datacenter. (Used by Cloudflare, Google Cloud, AWS Global Accelerator).

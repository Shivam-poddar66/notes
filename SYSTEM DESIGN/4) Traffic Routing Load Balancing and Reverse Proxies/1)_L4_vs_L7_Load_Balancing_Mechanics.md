# 1) L4 vs. L7 Load Balancing Mechanics

## Executive Overview
A Load Balancer acts as the reverse-proxy traffic controller in a distributed architecture, distributing incoming client requests across pools of healthy backend servers.

---

## 1. The OSI Layer Division

```text
┌────────────────────────────────────────────────────────┐
│ OSI Layer 7: Application Layer                         │
│ - Protocols: HTTP, HTTPS, WebSockets, gRPC             │
│ - AWS ALB, NGINX, Envoy Proxy, Traefik, HAProxy (HTTP) │
├────────────────────────────────────────────────────────┤
│ OSI Layer 4: Transport Layer                           │
│ - Protocols: TCP, UDP                                  │
│ - AWS NLB, HAProxy (TCP mode), Linux IPVS              │
├────────────────────────────────────────────────────────┤
│ OSI Layer 3: Network Layer                             │
│ - Protocols: IP, ICMP, Anycast BGP                     │
└────────────────────────────────────────────────────────┘
```

---

## 2. Technical Comparison: Layer 4 vs. Layer 7

```text
LAYER 4 (L4) LOAD BALANCING (Transport Routing):
Client Packet: [Src IP: 1.2.3.4, Port: 54321 | Dest IP: 10.0.0.1, Port: 443 | ENCRYPTED DATA...]
L4 Balancer: Modifies Dest IP to 10.0.1.5 (App Server A) without decrypting or inspecting payload!

LAYER 7 (L7) LOAD BALANCING (Application Routing):
Client Request: [GET /api/v1/checkout HTTP/2 | Host: shop.com | Cookie: session=xyz | Headers...]
L7 Balancer: Terminates TLS, parses URL path `/api/v1/checkout`, and routes to Checkout Microservice!
```

| Dimension | Layer 4 (L4) Load Balancing | Layer 7 (L7) Load Balancing |
| :--- | :--- | :--- |
| **Inspection Depth** | Header only (IP and Port numbers). | Deep inspection (HTTP headers, URL paths, cookies). |
| **SSL/TLS Handling** | Pass-through (no decryption overhead). | Terminates TLS; inspects plaintext payload. |
| **Throughput** | Millions of packets/sec (kernel-level forwarding). | Hundreds of thousands of HTTP requests/sec. |
| **Routing Smarts** | Purely IP/Port based. | Path routing (`/users` vs `/billing`), gRPC method routing. |
| **CPU Consumption** | Minimal (very low memory & CPU footprint). | High (cryptographic handshakes, string parsing). |
| **Session Stickiness**| Source IP hash only. | Cookie injection (`Set-Cookie: SERVERID=3`). |
| **Examples** | AWS Network Load Balancer (NLB), Linux IPVS. | AWS Application Load Balancer (ALB), NGINX, Envoy. |

---

## 3. Two-Tier Load Balancing Architecture (Enterprise Standard)

```text
[ Incoming Internet Traffic ]
              │
              ▼
    [ Layer 4 Balancer ]  (AWS NLB / Anycast BGP)
    - Ultra-fast TCP packet distribution
    - Absorbs multi-gigabit DDoS traffic
              │
              ▼
    [ Layer 7 Balancer ]  (Envoy / NGINX / AWS ALB)
    - Decrypts TLS certificates
    - Enforces WAF security rules
    - Routes `/api/v1/orders` to Order Service Pods
              │
              ▼
    [ Stateless App Pods / Microservices ]
```

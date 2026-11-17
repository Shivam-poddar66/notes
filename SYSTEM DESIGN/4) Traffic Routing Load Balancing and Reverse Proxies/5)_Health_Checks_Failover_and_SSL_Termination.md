# 5) Health Checks, Failover, and SSL/TLS Termination

## Executive Overview
A load balancer must dynamically verify backend node health to avoid routing traffic to degraded or crashed instances, and terminate cryptographic handshakes to maximize backend throughput.

---

## 1. Active vs. Passive Health Checks

```text
ACTIVE HEALTH CHECKS (Proactive Probing):
[ Load Balancer ] ──── Periodic HTTP GET /healthz ────▶ [ App Server ]
* Every 5s, checks for HTTP 200 OK within 2000 ms timeout.
* If 3 consecutive probes fail -> Mark server UNHEALTHY and stop routing.
* If 2 consecutive probes succeed -> Mark server HEALTHY and restore.

PASSIVE HEALTH CHECKS (Inline Observation):
[ Load Balancer ] ──── Real User Traffic ────▶ [ App Server ]
* LB observes live responses. If 5 consecutive 5xx errors or TCP timeouts occur,
  the server is temporarily quarantined for 30 seconds.
```

### Health Check Endpoint (`/healthz`) Best Practices:
1. **Liveness Probe**: Confirms the process is running and not deadlocked (checks local process state only; takes < 1 ms).
2. **Readiness Probe**: Confirms the service can accept traffic (verifies DB connectivity, Redis connection, and local cache warming).
3. **Avoid Cascading Database Meltdowns**: Never run an expensive `SELECT COUNT(*) FROM users` inside `/healthz`. If 50 load balancers probe 100 app instances every 5 seconds, an expensive health check query can crash your database on its own!

---

## 2. SSL/TLS Termination & Offloading

```text
[ Client (Browser) ]
         │
         │ Encrypted HTTPS (TLS Handshake, Cipher Exchange)
         ▼
[ Layer 7 Load Balancer / Reverse Proxy ]
         │ - Decrypts TLS using Private Key
         │ - Centralized Certificate Renewal (Let's Encrypt / ACM)
         │ - Injects HTTP Headers: X-Forwarded-For, X-Forwarded-Proto
         │
         ▼ Plaintext HTTP (High Speed, Low CPU over Private AWS VPC)
[ Backend App Instances ]
```

### Advantages of SSL Termination:
1. **CPU Offload**: Relieves backend application servers from asymmetric RSA/ECDHE key exchanges, saving up to 30% CPU capacity.
2. **Centralized Certificate Management**: Update or rotate wildcard certificates in one place (at the LB) rather than deploying certs to hundreds of individual Docker containers.
3. **WAF & Deep Packet Inspection**: Allows Web Application Firewalls (ModSecurity, Cloudflare WAF, AWS WAF) to inspect plaintext HTTP bodies for SQL injection and XSS before passing requests inward.

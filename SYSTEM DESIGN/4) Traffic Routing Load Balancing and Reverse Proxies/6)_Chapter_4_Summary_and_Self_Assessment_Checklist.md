# 6) Chapter 4 Summary & Self-Assessment Checklist

## Chapter Summary
1. **L4 vs. L7 Trade-offs**: Layer 4 balances raw TCP/UDP packets at kernel speeds with minimal CPU consumption, while Layer 7 balances HTTP/gRPC traffic with deep payload inspection, path routing, and TLS termination.
2. **Global Edge Routing**: Anycast BGP advertises the identical public IP address globally, routing users to the nearest internet point of presence without DNS propagation delays and isolating volumetric DDoS attacks.
3. **Balancing Algorithms**: Use Round Robin for fast, uniform stateless APIs; Least Connections for stateful WebSockets; and Least Response Time (EWMA) for heterogeneous workloads.
4. **Consistent Hashing**: Maps servers and keys to a $2^{32}$ circular ring. Introducing Virtual Nodes ensures uniform distribution and guarantees that adding or removing nodes migrates only $\frac{1}{N}$ fraction of keys.
5. **Operational Health**: Differentiating between Liveness and Readiness probes avoids cascading server flapping. SSL offloading centralizes certificate renewal and conserves backend CPU resources.

---

## 🎯 Chapter 4 Self-Assessment Checklist

- [ ] What information does a Layer 4 load balancer inspect versus a Layer 7 load balancer?
- [ ] Why is Anycast BGP superior to Round-Robin DNS for global traffic distribution and DDoS defense?
- [ ] Under what conditions will Round Robin load balancing cause server overload?
- [ ] Why does naive modulo hashing ($\text{key} \pmod N$) cause a disaster during cache node scaling?
- [ ] Explain the step-by-step mechanics of Consistent Hashing and clockwise traversal on a $2^{32}$ ring.
- [ ] What critical problem do Virtual Nodes (VNodes) solve in Consistent Hashing?
- [ ] What is the difference between an Active health check and a Passive health check?
- [ ] Why should a `/healthz` endpoint never execute a heavy database query?
- [ ] What are the 3 major benefits of terminating SSL/TLS at the load balancer level?

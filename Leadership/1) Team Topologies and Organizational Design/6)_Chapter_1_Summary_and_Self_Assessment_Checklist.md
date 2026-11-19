# 6) Chapter 1 Summary & Self-Assessment Checklist

## Chapter Summary
- **4 Team Topologies**: Standardize organization around Stream-Aligned (core value), Enabling (upskilling), Complicated-Subsystem (deep specialist), and Platform (cognitive load reduction) teams.
- **Cognitive Load Optimization**: Aggressively eliminate extraneous friction (flaky pipelines, manual deploys) through self-service developer platforms.
- **Conway's Law & Inverse Maneuver**: Software systems mirror communication links. Design the desired decoupled architecture first, then organize autonomous teams around those domain boundaries.
- **3 Interaction Modes**: Use Collaboration (temporary discovery), X-as-a-Service (default stable API consumption), and Facilitating (temporary coaching).
- **Two-Pizza Teams**: Cap individual squad sizes at 5–8 engineers to keep communication links ($N(N-1)/2$) small and velocity high.

---

## 10-Point Self-Assessment Checklist

Score each item from **0 (Never)** to **2 (Consistently Mastered)**:

1. [ ] **Stream-Aligned Squads**: My team owns a clear customer or business domain from end to end.
2. [ ] **Cross-Functional Composition**: My squad includes the necessary frontend, backend, and QA skills to ship without external team blockers.
3. [ ] **Cognitive Load Bounding**: My team does not own more than one complex or two simple bounded contexts.
4. [ ] **Self-Service Platform**: My engineers can deploy, monitor, and test code using self-service platforms without raising tickets.
5. [ ] **Inverse Conway Alignment**: Team reporting lines and boundaries match our desired microservice or domain architecture.
6. [ ] **Clear Interaction Modes**: When collaborating with neighboring teams, we explicitly define whether the mode is Collaboration, X-as-a-Service, or Facilitating.
7. [ ] **Temporary Collaboration Discipline**: We do not allow "temporary collaboration" between squads to linger indefinitely without merging or decoupling.
8. [ ] **Two-Pizza Team Sizing**: Our squads do not exceed 8 engineers; we proactively split squads when they hit 9–10 members.
9. [ ] **Healthy Span of Control**: Frontline managers do not have more than 8 direct reports, ensuring weekly 1-on-1s are protected.
10. [ ] **Dependency Tracking**: We measure and actively minimize the number of cross-team pull requests and deployment dependencies.

**Scoring Guide**:
- **17–20**: Modern, decoupled, high-velocity engineering organization.
- **11–16**: Strong organizational architecture; occasional friction from legacy dependencies or bloated team sizes.
- **0–10**: Heavy monolithic coupling, high ticketing overhead, and developer burnout; re-architect teams around Team Topologies immediately.

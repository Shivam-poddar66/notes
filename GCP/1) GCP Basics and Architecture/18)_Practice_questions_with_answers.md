# Practice Questions with Answers (100 Questions)

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## Section A: Cloud model and core architecture (1-20)

1. What does "on-demand self-service" mean in cloud computing?  
   - You can provision resources when needed without manual provider intervention.

2. What is the primary GCP boundary for IAM, API enablement, quota, and billing linkage?  
   - Project.

3. What is the correct resource hierarchy order in GCP?  
   - Organization -> Folder -> Project -> Resource.

4. What is a region?  
   - A geographic area containing multiple zones.

5. What is a zone?  
   - An isolated deployment and failure domain inside a region.

6. Why is multi-zone design common for production?  
   - It improves availability during zone failures.

7. What is one key reason to use folders?  
   - To group projects and apply governance/policies at scale.

8. What is the blast radius in architecture terms?  
   - The scope of impact when a component/location fails.

9. What does "design for failure" mean?  
   - Assume failures happen and build architecture to continue or recover quickly.

10. Why is project separation useful for dev and prod?  
    - Better isolation for security, quota, and cost control.

11. What is one risk of placing all workloads in one project?  
    - Larger blast radius and weaker governance separation.

12. Which factor should usually be checked first in regulated workloads?  
    - Compliance and data residency.

13. What does "measured service" imply?  
    - Cloud usage is metered and billed by consumption.

14. What is a core cloud architecture tradeoff set in ACE scenarios?  
    - Latency, resilience, compliance, and cost.

15. What is the difference between high availability and disaster recovery?  
    - HA keeps service running during local failures; DR restores service after larger disruptions.

16. Why are labels important in architecture operations?  
    - Ownership and cost visibility.

17. What does hierarchy-aware governance improve?  
    - Consistent policy enforcement and reduced operational drift.

18. What is one anti-pattern in architecture selection?  
    - Choosing the most complex design without requirement.

19. What should drive architecture selection first: service preference or constraints?  
    - Constraints.

20. What is the simplest rule for scenario questions?  
    - Choose the least complex solution that meets all explicit requirements.

## Section B: Regions, zones, and global infrastructure (21-40)

21. Region vs zone in one line?  
    - Region is geography; zone is isolated failure domain inside it.

22. What resource scope is VPC network usually considered?  
    - Global scope.

23. Subnet scope in GCP?  
    - Regional.

24. VM instance scope in Compute Engine?  
    - Typically zonal.

25. Which architecture mostly handles zone outage?  
    - Multi-zone in one region.

26. Which architecture mostly handles region outage?  
    - Multi-region DR architecture.

27. Why can cross-region calls be problematic?  
    - Increased latency and egress cost.

28. What is a common production baseline for many apps?  
    - Single-region multi-zone deployment.

29. What should be evaluated before selecting multi-region?  
    - Business RTO/RPO and continuity requirement.

30. What is an edge location/PoP used for?  
    - Bringing network entry closer to users for better delivery performance.

31. Why does private backbone matter?  
    - More consistent global network performance.

32. If question asks "survive one zone failure with minimal complexity," likely answer?  
    - Multi-zone same-region architecture.

33. If question asks "must tolerate region outage," likely answer direction?  
    - Cross-region DR strategy.

34. Which requirement can override lowest-latency region choice?  
    - Compliance/data residency policy.

35. What is a poor default for global users?  
    - Serving all traffic from one distant region.

36. What does location strategy influence directly?  
    - Performance, cost, and compliance posture.

37. What does failure-domain isolation in zones provide?  
    - Better fault containment.

38. Is multi-region always required for production?  
    - No, only when business continuity requirements justify it.

39. Which is usually operationally simpler: multi-zone or multi-region?  
    - Multi-zone.

40. What is a key hidden cost in global designs?  
    - Cross-region data transfer.

## Section C: Deployment and financial models (41-60)

41. What is the default deployment model in GCP?  
    - Public cloud.

42. Why choose hybrid cloud?  
    - Legacy, compliance, or integration constraints.

43. One billing account can fund how many projects?  
    - Multiple.

44. One project can link to how many billing accounts at the same time?  
    - One.

45. What is a major reason budgets are essential?  
    - Early spend visibility and control.

46. Do budget alerts automatically stop all spend by default?  
    - No.

47. Why apply labels across resources?  
    - Cost attribution and operational ownership.

48. What is rightsizing?  
    - Matching resource size to actual demand.

49. Why are non-prod schedules useful?  
    - Reduce idle cost.

50. Which workload usually fits serverless cost model well?  
    - Spiky or intermittent traffic workloads.

51. Which workload pattern may justify commitment-style savings?  
    - Predictable long-running baseline load.

52. What cost area is often underestimated?  
    - Network egress.

53. Why should architecture include FinOps early?  
    - Prevent uncontrolled growth and poor cost accountability.

54. What is a common cost anti-pattern?  
    - Overprovisioning "just in case."

55. Why separate projects by environment from cost perspective?  
    - Cleaner spend tracking and control.

56. What is a common migration starting pattern for legacy apps?  
    - Rehost on VMs, then modernize.

57. What must be validated after cost optimization changes?  
    - Reliability and security are not degraded.

58. Why can multi-region increase recurring cost?  
    - Replication and standby footprint.

59. Which is better than one-time optimization effort?  
    - Continuous optimization cycle.

60. What is the exam-safe cost rule?  
    - Meet required outcomes first, then minimize spend.

## Section D: Shared responsibility, governance, and security (61-80)

61. Shared responsibility in one line?  
    - Google secures the cloud; customer secures usage in the cloud.

62. In IaaS VMs, who patches guest OS?  
    - Customer.

63. In managed/serverless services, who manages IAM and data access policy?  
    - Customer.

64. What is a role in IAM?  
    - A collection of permissions.

65. What is a permission?  
    - A single allowed action.

66. Why is least privilege important?  
    - Reduces blast radius and misuse risk.

67. Why avoid broad basic roles in production?  
    - They grant excessive permissions.

68. What identity should workloads use?  
    - Service account.

69. Why avoid long-lived service account keys?  
    - Higher credential exposure risk.

70. What does Organization Policy provide?  
    - Preventive governance constraints.

71. Name one common org policy use case.  
    - Restrict allowed regions.

72. What kind of control is audit logging?  
    - Detective control.

73. Why monitor privileged IAM changes?  
    - Detect unauthorized or risky access changes quickly.

74. What does "policy inheritance" mean?  
    - Parent-level permissions/policies can apply to child resources.

75. Why is high-level broad IAM grant risky?  
    - It can overexpose many projects/resources through inheritance.

76. What is a security anti-pattern for secrets?  
    - Hardcoding secrets in code.

77. What should be used for secret storage?  
    - Secret Manager.

78. Does fully managed service remove customer compliance responsibility?  
    - No.

79. What must be reviewed periodically in IAM governance?  
    - Effective permissions and stale privileges.

80. Best one-line governance rule for exam answers?  
    - Apply centralized guardrails, then delegate least-privilege operations.

## Section E: Reliability, performance, cost tradeoffs, and scenarios (81-100)

81. What does RTO measure?  
    - Target recovery time after disruption.

82. What does RPO measure?  
    - Acceptable data loss window.

83. Which DR model is lowest cost but slower recovery?  
    - Backup and restore.

84. Which DR model is strongest but most complex/costly?  
    - Active-active multi-region.

85. Why test restore regularly?  
    - To validate actual recoverability.

86. What are the core performance signals?  
    - Latency, throughput, errors, saturation.

87. Why are p95/p99 latencies important?  
    - They reflect tail user experience.

88. What is a common cause of latency spikes in distributed systems?  
    - Cross-region dependency calls in critical path.

89. Why can deeper synchronous chains hurt reliability?  
    - Latency compounding and failure propagation.

90. What is the first step in incident triage using control/data plane mindset?  
    - Classify whether issue is control plane, data plane, or both.

91. If deployments fail but existing user traffic works, likely issue type?  
    - Control plane issue.

92. If deployments succeed but users time out, likely issue type?  
    - Data plane issue.

93. Which answer usually fits "minimum ops, scalable API"?  
    - Cloud Run.

94. Which answer usually fits "custom OS package required"?  
    - Compute Engine.

95. Which answer usually fits "process file upload event"?  
    - Cloud Run functions.

96. Which answer usually fits "transactional SQL database"?  
    - Cloud SQL.

97. Which answer usually fits "petabyte analytics SQL"?  
    - BigQuery.

98. Which answer usually fits "document NoSQL backend"?  
    - Firestore.

99. If an option is cheaper but fails compliance requirement, should you choose it?  
    - No.

100. What is the final decision rule for scenario questions?  
     - Choose the simplest compliant architecture that satisfies reliability, security, performance, and cost constraints.

---

Study method:
1. Attempt all questions without seeing answers.
2. Mark weak areas by chapter number.
3. Re-read weak chapter notes.
4. Repeat until you consistently score above 85 percent on mixed sets.

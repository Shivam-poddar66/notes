# 8) IAM Conditions and context-based access

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

### What conditions provide
- Conditional grants based on context (for example resource attributes, time windows, request context patterns where supported).

### Use cases
- Time-bound elevated access.
- Restricting role usage to specific resource names.
- Controlled temporary access for migration windows.

### Design advice
- Keep condition logic readable and documented.
- Test policy outcomes before critical rollout.

Exam cues:
- "Temporary or context-specific access" -> IAM Conditions pattern.


## Practical example
- Requirement: Database admin access allowed only during maintenance window.
- Design: Add IAM Condition with time-bound rule on elevated role.
- Why: Access is automatically limited to approved context.


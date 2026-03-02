# 9) Private Access To Google APIs (PGA And PSC) - Detailed Notes

Last updated: February 24, 2026  
Target: Google Cloud Associate Cloud Engineer (ACE)

## 1. Why private API access matters
Private workloads often need Google API access without exposing resources to public internet ingress.

## 2. Private Google Access (PGA)
- Enables eligible private resources to reach Google APIs/services without external IP assignment.
- Common in private subnet environments with strict exposure policies.

## 3. Private Service Connect (PSC)
- Provides private service connectivity model between consumers and service endpoints.
- Useful for controlled private consumption patterns and service boundaries.

## 4. Design selection guidance
- Use PGA when private resources need Google API egress.
- Use PSC when private service connectivity/control boundaries are primary requirement.

## 5. Security and governance
- Keep least-privilege IAM for API calls.
- Monitor API access and unusual usage patterns.
- Combine with egress policy controls.

## 6. Operational checks
- Confirm subnet and network settings support intended private access path.
- Validate service-specific connectivity behavior.
- Include monitoring for connectivity failures.

## 7. Common mistakes
- Assuming private subnet can reach APIs without proper access configuration.
- Confusing private API access with general internet access design.
- Missing policy controls around who can use private endpoints.

## 8. Exam cues
- "Private VM/resource needs Google API access without external IP" -> PGA.
- "Private service endpoint connectivity boundary" -> PSC.

## One-line memory hook
PGA and PSC reduce public exposure while preserving controlled access to services.

## Practical example
- Requirement: Private VM must call Cloud Storage API without public IP.
- Design: Enable Private Google Access on subnet.
- Why: API access works from private resources without assigning external IPs.


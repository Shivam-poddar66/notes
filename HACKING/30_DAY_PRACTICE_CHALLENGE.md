# 30-Day Hands-On Hacking Practice Challenge (Zero to Foothold)

This is an **action-oriented, day-by-day practical training schedule**. Do not just read—execute the exact drills, solve the corresponding free labs, and capture the flags.

---

## 🎯 Ground Rules for Daily Practice
1. **Rule of Hands-On**: Spend at least **80% of your time in the terminal, browser debugger, or Burp Suite**, and at most 20% reading theory.
2. **Document Everything**: Keep a personal markdown log for every machine or challenge you solve with your exact command history and PoC.
3. **Never Look at Walkthroughs First**: Spend at least 45 minutes trying different inputs, encodings, and payloads before checking a hint.

---

## 📅 Week 1: Networking, Linux CLI & Reconnaissance Muscle Memory

| Day | Topic | Hands-On Mission & Exercises | Verification / Flag |
| :--- | :--- | :--- | :--- |
| **Day 01** | Linux CLI & File Navigation | Complete **OverTheWire: Bandit (Levels 0 to 15)**.<br>Practice commands: `find`, `grep`, `xargs`, `tar`, `base64`, `openssl s_client`. | Retrieve password for Bandit Level 16. |
| **Day 02** | Linux Permissions & Text Wrangling | Complete **OverTheWire: Bandit (Levels 16 to 25)**.<br>Inspect SUID binaries, cron jobs, localhost ports via `nc` and `netstat`. | Retrieve password for Bandit Level 26. |
| **Day 03** | TCP/IP & Wireshark Packet Inspection | Download Wireshark. Capture your own traffic while running `curl http://example.com`.<br>Filter for HTTP, DNS, and TCP 3-way handshake (`tcp.flags.syn == 1`).<br>Inspect raw bytes in packet details. | Extract cleartext password from a sample HTTP POST capture. |
| **Day 04** | Fast Nmap Port Scanning Drills | Launch local practice target or TryHackMe machine.<br>Run 2-stage Nmap scan: fast SYN scan (`-sS -p- --min-rate 1500`) followed by service fingerprinting (`-sC -sV`). | Export clean output using `-oA` and parse open ports with `grep`. |
| **Day 05** | Subdomain & Asset Discovery | Install `subfinder` & `httpx` (or use online tools).<br>Target a bug bounty wildcard domain with permission (e.g. `*.hackerone.com` or `*.yahoo.com`).<br>Run CT log search on `crt.sh` and extract all live HTTP/HTTPS services. | Generate a clean list of 100+ resolving unique hosts. |
| **Day 06** | Web Fuzzing with FFUF | Run [Local Practice Lab 05](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_05_LFI_and_Path_Traversal/LAB_WALKTHROUGH.md).<br>Fuzz directories using SecLists `raft-medium-directories.txt`.<br>Fuzz GET parameters using `burp-parameter-names.txt`. | Discover hidden endpoint and hidden query parameter. |
| **Day 07** | Burp Suite Setup & Interception | Install Burp Suite Community & CA certificate in browser.<br>Intercept requests, modify headers (`User-Agent`, `X-Forwarded-For`), practice sending requests to Repeater and Intruder. | Complete PortSwigger Academy Lab: *Apprentice - Intercepting requests*. |

---

## 📅 Week 2: Web Exploitation Core (OWASP Top 10 Hands-on)

| Day | Topic | Hands-On Mission & Exercises | Verification / Flag |
| :--- | :--- | :--- | :--- |
| **Day 08** | SQLi Part 1: Authentication Bypass | Run [Local Practice Lab 01: SQL Injection](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_01_SQL_Injection/LAB_WALKTHROUGH.md).<br>Bypass the admin login form without credentials using `' OR 1=1-- -`. | Capture `FLAG{SQLI_AUTH_BYPASS_PWNED}`. |
| **Day 09** | SQLi Part 2: Union-Based Exfiltration | In [Local Practice Lab 01](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_01_SQL_Injection/LAB_WALKTHROUGH.md):<br>1. Determine column count using `ORDER BY`.<br>2. Determine string columns.<br>3. Extract table names and user passwords using `UNION SELECT`. | Capture `FLAG{UNION_MASTER_EXTRACTED}`. |
| **Day 10** | Command Injection & Filter Evasion | Run [Local Practice Lab 02: Command Injection](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_02_Command_Injection/LAB_WALKTHROUGH.md).<br>Bypass space filters using `${IFS}`, chain commands with `;` and `\|`, read `/etc/passwd`. | Capture `FLAG{CMD_INJECTION_RCE_ROOT}`. |
| **Day 11** | Reflected & Stored XSS | Run [Local Practice Lab 03: XSS & CSRF](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_03_XSS_and_DOM/LAB_WALKTHROUGH.md).<br>Inject JavaScript payload `<script>` and event handlers `<img onerror=...>` into comment fields.<br>Extract document cookies. | Trigger alert with `document.domain` and capture flag. |
| **Day 12** | IDOR & Broken Access Control | Run [Local Practice Lab 04: IDOR](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_04_IDOR_and_Broken_Access/LAB_WALKTHROUGH.md).<br>Log in as low-privilege user `alice`. Tamper with invoice IDs and user profile parameters to view user `admin` sensitive data. | Capture `FLAG{IDOR_ACCESS_CONTROL_LEAK}`. |
| **Day 13** | Path Traversal & LFI to RCE | Run [Local Practice Lab 05: LFI](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_05_LFI_and_Path_Traversal/LAB_WALKTHROUGH.md).<br>Traverse directories using `../../`. Use PHP stream wrapper `php://filter/convert.base64-encode/resource=index.php` to dump PHP source code. | Capture `FLAG{LFI_WRAPPER_SOURCE_EXTRACTED}`. |
| **Day 14** | SSRF & Cloud Metadata Extraction | Complete PortSwigger Academy Lab: *Basic SSRF against the local server* & *SSRF against backend system*.<br>Query simulated cloud metadata endpoint `169.254.169.254`. | Access admin backend via loopback SSRF. |

---

## 📅 Week 3: Python Offensive Scripting & Automation

| Day | Topic | Hands-On Mission & Exercises | Verification / Flag |
| :--- | :--- | :--- | :--- |
| **Day 15** | Python Sockets: Port Scanner | Run [Local Practice Lab 06: Python Hacking Tools](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_06_Python_Hacking_Tools/EXERCISES.md).<br>Write a script using `socket` library to scan open ports on `127.0.0.1`. Add multi-threading with `concurrent.futures`. | Successfully detect open ports under 3 seconds. |
| **Day 16** | Python HTTP: Web Directory Fuzzer | Write a Python tool using `requests` to read a wordlist, send HTTP GET requests, and print discovered URLs with status `200` or `301`. | Discover all endpoints in local practice labs. |
| **Day 17** | Python Exploits: Automated SQLi | Write a Python exploit script that automatically extracts database tables from [Lab 01](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_01_SQL_Injection/LAB_WALKTHROUGH.md) via blind boolean search or union query. | Script automatically dumps the secret flag into console. |
| **Day 18** | Brute Force Automation | Write a Python script to brute-force a login form with a wordlist using `requests.post()` and checking response text or redirects. | Extract correct admin credentials. |
| **Day 19** | Reverse Shell Architecture & TTY | Establish reverse shells using Bash, Netcat, and Python.<br>Practice full TTY upgrade: `python3 -c 'import pty; pty.spawn("/bin/bash")'`, background with `Ctrl+Z`, `stty raw -echo; fg`, `reset`. | Stable terminal with working Tab completion and Ctrl+C. |
| **Day 20** | Password Cracking Drills | Practice with `hashcat` and `john`.<br>Crack MD5 (`-m 0`), NTLM (`-m 1000`), SHA-512 crypt (`-m 1800`), and Kerberos TGS (`-m 13100`) using `rockyou.txt`. | Crack 5 distinct sample hashes in under 10 minutes. |
| **Day 21** | Challenge Day: Web CTF Box | Solve 1 complete Easy Web machine on TryHackMe (e.g., *Pickle Rick* or *RootMe*). | Capture user and root flags independently. |

---

## 📅 Week 4: Privilege Escalation & Active Directory Mastery

| Day | Topic | Hands-On Mission & Exercises | Verification / Flag |
| :--- | :--- | :--- | :--- |
| **Day 22** | Linux PrivEsc: Sudo & SUID | Practice scenarios in [Linux PrivEsc Drills](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_07_Privilege_Escalation_Drills/LINUX_DRILLS.md).<br>Exploit GTFOBins binaries in `sudo -l`, search SUID binaries with `find / -perm -u=s`. | Elevate to UID 0 (root). |
| **Day 23** | Linux PrivEsc: Cron & Capabilities | Exploit writable cron job script.<br>Inspect capabilities with `getcap -r / 2>/dev/null` and exploit `cap_setuid` on Python binary. | Elevate to root via capability bypass. |
| **Day 24** | Windows PrivEsc: Service Misconfigs | Practice scenarios in [Windows PrivEsc Drills](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_07_Privilege_Escalation_Drills/WINDOWS_DRILLS.md).<br>Exploit Unquoted Service Paths and weak service binary permissions with `sc config`. | Elevate to `NT AUTHORITY\SYSTEM`. |
| **Day 25** | Windows PrivEsc: Token Abuse | Check `whoami /priv`.<br>Download and run `GodPotato` or `PrintSpoofer` to elevate from service account to SYSTEM. | Execute `cmd.exe` as SYSTEM. |
| **Day 26** | Active Directory: Kerberoasting | Complete TryHackMe room *Attacktive Directory* or local GOAD lab.<br>Request service tickets for SPNs using `impacket-GetUserSPNs` and crack offline with Hashcat. | Crack plaintext password of AD service account. |
| **Day 27** | Active Directory: AS-REP Roasting | Enumerate accounts without Kerberos preauthentication with `GetNPUsers.py`. Dump and crack AS-REP ticket hashes. | Obtain domain user credentials. |
| **Day 28** | Active Directory: BloodHound & DCSync | Ingest domain JSON into BloodHound.<br>Find shortest path to Domain Admin.<br>Execute `secretsdump.py` to perform DCSync and dump all domain hashes. | Extract `krbtgt` NTLM hash. |
| **Day 29** | Multi-Hop Pivoting with Ligolo-ng | Set up a 2-network VM lab.<br>Establish a TUN adapter with Ligolo-ng proxy and route an isolated 172.16.x.x internal subnet. Scan internal DC with Nmap through the tunnel. | Successfully map internal network without proxychains. |
| **Day 30** | The Final Boss: Complete Pentest Simulation | Solve 1 full unguided standalone box on Hack The Box or Proving Grounds (e.g., *Lame*, *Devel*, or *Blue*).<br>Write a formal 5-page penetration testing report with CVSS scores and remediation steps. | Complete professional deliverable submitted to yourself. |

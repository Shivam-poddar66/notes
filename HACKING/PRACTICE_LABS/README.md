# Offline Practical Hacking Labs Suite

Welcome to your **local hands-on practice playground**.

Every lab in this folder is **fully runnable on your machine right now** using your installed PHP runtime and SQLite (no external dependencies, no internet required, no database installation needed!).

---

## ⚡ Quick Start: How to Run the Labs

### Option A: One-Click Launch (Recommended)
Double-click:
```text
E:\application_install\xampp\htdocs\notes\HACKING\PRACTICE_LABS\start_all_labs.bat
```
This starts the local web server and automatically opens your browser to:
👉 **`http://127.0.0.1:8888`**

### Option B: From Any Terminal / PowerShell
Open PowerShell or Command Prompt in this folder and run:
```powershell
cd E:\application_install\xampp\htdocs\notes\HACKING\PRACTICE_LABS
php -S 127.0.0.1:8888
```
Then navigate in your browser to: `http://127.0.0.1:8888`

---

## 🎯 Labs Included in This Suite

| Lab | Vulnerability Class | Difficulty | Objective | Walkthrough Guide |
| :--- | :--- | :--- | :--- | :--- |
| **Lab 01** | **SQL Injection (SQLi)** | Easy → Medium | Bypass login, extract users & passwords via UNION query | [Walkthrough](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_01_SQL_Injection/LAB_WALKTHROUGH.md) |
| **Lab 02** | **Command Injection (RCE)** | Medium | Break out of network utility ping tool, bypass filters, read flag | [Walkthrough](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_02_Command_Injection/LAB_WALKTHROUGH.md) |
| **Lab 03** | **XSS & DOM Injection** | Easy → Medium | Exploit Reflected & Stored XSS, steal simulated session cookie | [Walkthrough](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_03_XSS_and_DOM/LAB_WALKTHROUGH.md) |
| **Lab 04** | **IDOR & Broken Access** | Easy | Tamper with object IDs to view confidential invoices of other users | [Walkthrough](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_04_IDOR_and_Broken_Access/LAB_WALKTHROUGH.md) |
| **Lab 05** | **LFI & Path Traversal** | Medium | Traverse directories and use PHP stream wrappers to dump code | [Walkthrough](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_05_LFI_and_Path_Traversal/LAB_WALKTHROUGH.md) |
| **Lab 06** | **Python Hacking Tools** | Hands-On Coding | Code your own port scanner, web directory fuzzer, and brute-forcer | [Exercises](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_06_Python_Hacking_Tools/EXERCISES.md) |
| **Lab 07** | **PrivEsc Drills** | Tactical | 20 real-world Linux & Windows privilege escalation puzzle drills | [Drills](file:///E:/application_install/xampp/htdocs/notes/HACKING/PRACTICE_LABS/Lab_07_Privilege_Escalation_Drills/LINUX_DRILLS.md) |

---

## 🏆 Flag Format
Every lab contains one or more hidden flags formatted as:
```text
FLAG{...}
```
When you exploit the vulnerability, the flag will be revealed in the response, in a dumped database row, or in an extracted file!

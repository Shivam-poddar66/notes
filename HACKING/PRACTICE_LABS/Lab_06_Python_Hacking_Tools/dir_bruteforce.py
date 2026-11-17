#!/usr/bin/env python3
"""
Offensive Security Practice Tool 2: Web Directory & File Fuzzer
Usage: python dir_bruteforce.py http://127.0.0.1:8888/
"""

import sys
import requests
import concurrent.futures

DEFAULT_WORDLIST = [
    "admin", "login", "dashboard", "api", "uploads", "backup", "db", "config",
    "secret", "test", "private", "robots.txt", ".git", ".env", "console",
    "Lab_01_SQL_Injection", "Lab_02_Command_Injection", "Lab_03_XSS_and_DOM",
    "Lab_04_IDOR_and_Broken_Access", "Lab_05_LFI_and_Path_Traversal"
]

def check_endpoint(base_url, word):
    url = f"{base_url.rstrip('/')}/{word}"
    headers = {'User-Agent': 'Security-Practice-Scanner/1.0'}
    try:
        r = requests.get(url, headers=headers, timeout=3, allow_redirects=False)
        code = r.status_code
        if code in [200, 204, 301, 302, 307, 401, 403]:
            return (word, code, len(r.content), url)
    except requests.RequestException:
        pass
    return None

def main():
    target = sys.argv[1] if len(sys.argv) > 1 else "http://127.0.0.1:8888/"
    print(f"[*] Starting Directory Fuzzing on: {target}")
    print(f"[*] Words to test: {len(DEFAULT_WORDLIST)}")
    print("-" * 65)

    with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
        futures = [executor.submit(check_endpoint, target, word) for word in DEFAULT_WORDLIST]
        for f in concurrent.futures.as_completed(futures):
            res = f.result()
            if res:
                word, code, length, url = res
                color = "\033[92m" if code == 200 else "\033[93m" if code in [301, 302] else "\033[91m"
                reset = "\033[0m"
                print(f"[{color}{code}{reset}] - {length:>6} bytes - /{word:<25} -> {url}")

    print("-" * 65)
    print("[*] Fuzzing complete.")

if __name__ == "__main__":
    main()

# Lab 06: Python Offensive Tooling Coding Exercises

True hackers do not rely solely on third-party tools; they write their own scripts to automate exploitation, parse custom protocols, and bypass restrictions.

Here are **3 Hands-On Python Coding Drills** to build your scripting muscle.

---

## 🛠️ Drill 1: Build a Multi-Threaded TCP Port Scanner

### Mission Objective
Write a Python script from scratch using the built-in `socket` library that scans a range of ports on a target IP and prints which ports are open.

### Requirements
1. Accept Target IP, Start Port, and End Port as command-line arguments (`sys.argv`).
2. Use non-blocking sockets or set a timeout of `0.5` seconds.
3. Accelerate the scan using `concurrent.futures.ThreadPoolExecutor(max_workers=50)`.
4. Grab the HTTP server banner if the port is an open HTTP port (`80`, `8888`, `8080`).

### Starter Template
```python
import socket
import sys

def check_port(ip, port):
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.settimeout(0.5)
    # TODO: Connect to port and return whether it is open
    s.close()

# Test against: python port_scanner.py 127.0.0.1 80 9000
```
*(A complete working solution is provided in `port_scanner.py`!)*

---

## 🛠️ Drill 2: Build a Web Directory & Endpoint Fuzzer

### Mission Objective
Recreate the core logic of `ffuf` / `dirb` in under 60 lines of Python using the `requests` library.

### Requirements
1. Accept a target Base URL (e.g. `http://127.0.0.1:8888/`).
2. Read a list of words from a Python list or external file (`wordlist.txt`).
3. Send HTTP GET requests to `{target}/{word}`.
4. Filter out HTTP `404 Not Found` responses and display:
   - Status code (`200 OK`, `301 Redirect`, `403 Forbidden`).
   - Content length in bytes.
   - The discovered URL.

*(A complete working solution is provided in `dir_bruteforce.py`!)*

---

## 🛠️ Drill 3: Automated Form Credential Brute-Forcer

### Mission Objective
Write an automated dictionary attack script against a login form with CSRF tokens or raw POST fields.

### Requirements
1. Maintain a persistent HTTP session using `requests.Session()`.
2. Iterate through candidate usernames and passwords.
3. Inspect the response body or HTTP status code to distinguish a failed login from a successful login.
4. When access is granted, stop the attack and print the captured flag.

*(A complete working solution targeting Lab 01 is provided in `brute_force_login.py`!)*

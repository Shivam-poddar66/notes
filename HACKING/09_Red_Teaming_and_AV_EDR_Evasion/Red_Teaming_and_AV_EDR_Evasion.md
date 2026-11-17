# Module 09: Red Teaming, C2 Operations & AV/EDR Evasion

Tactical architecture for red team engagements, resilient Command and Control (C2), and defeating modern endpoint detection and response (EDR) agents.

---

## 1. How Modern EDRs Detect Threats

Endpoint Detection and Response agents (e.g., CrowdStrike Falcon, Microsoft Defender for Endpoint, SentinelOne) rely on four primary telemetry sources:

1. **Userland API Hooking**:
   - The EDR injects an inspection DLL into every new process.
   - It replaces the first few bytes of sensitive Win32 / Native APIs in `ntdll.dll` (e.g., `NtAllocateVirtualMemory`, `NtWriteVirtualMemory`, `NtCreateThreadEx`) with `jmp <edr_sensor>` instructions.
2. **Kernel Callbacks**:
   - EDR kernel drivers subscribe to OS events via `PsSetCreateProcessNotifyRoutine`, `ObRegisterCallbacks` to monitor thread creation and memory modifications.
3. **Event Tracing for Windows (ETW)**:
   - Built-in OS logging for .NET assembly loads, AMSI calls, and threat intelligence events.
4. **Antimalware Scan Interface (AMSI)**:
   - Scans dynamic scripts in memory (PowerShell, VBScript, Office macros) before execution.

---

## 2. Core EDR Evasion Primitives

```
                  Normal Execution Path (Detected by EDR)
  User Code ──> ntdll.dll (HOOKED BY EDR) ──> [EDR Inspection JMP] ──> Kernel
                                                        │ (Detected!)
                                                        ▼
                                                  Alert / Blocked

                  Direct System Call (Evasion Technique)
  User Code ──> Custom Assembly (Syscall Stub) ─────────────────────> Kernel
                               (No EDR Hooks Touched!)
```

### 1. Direct System Calls (Syscalls)
Instead of calling `VirtualAlloc()` or `NtAllocateVirtualMemory()` through `ntdll.dll` (which is hooked), invoke the kernel syscall number directly in assembly:
```asm
; Direct Syscall Assembly Stub for x64
mov r10, rcx
mov eax, <SSN_System_Service_Number>
syscall
ret
```
Tools for automated dynamic syscall generation:
- **SysWhispers3**
- **Hell's Gate / Halo's Gate** (Dynamically parses SSN numbers directly from memory to bypass hardcoded version variations).

### 2. DLL Unhooking (Refreshing `ntdll.dll`)
Because EDR hooks reside exclusively in the process's loaded copy of `ntdll.dll`, read a fresh, unhooked copy of `ntdll.dll` from disk (`C:\Windows\System32\ntdll.dll`) or from `\KnownDlls\ntdll.dll` and overwrite the `.text` section of the hooked DLL in memory.

### 3. In-Memory AMSI Bypass
Patch the `AmsiScanBuffer` function in `amsi.dll` to return `E_INVALIDARG` (0x80070057) or `S_OK`:
```powershell
# Classic reflection-based AMSI patch in PowerShell
$a = [Ref].Assembly.GetType('System.Management.Automation.AmsiUtils')
$f = $a.GetField('amsiInitFailed','NonPublic,Static')
$f.SetValue($null,$true)
```

---

## 3. Resilient Command and Control (C2) Infrastructure

Never point a payload directly to your real teamserver IP. Use multi-tier redirectors.

```
Target Workstation ──HTTPS──> [ Cloudflare CDN / Domain Front ]
                                            │
                                            ▼
                              [ NGINX Reverse Proxy Redirector ]
                                            │ (Valid User-Agent & URI filter)
                                            ▼
                              [ C2 Teamserver (Sliver / Havoc) ]
```

### Open Source C2 Frameworks to Master:
- **Sliver (Bishop Fox)**: Written in Go, supports mTLS, HTTP(S), WireGuard, and DNS beacons.
- **Havoc Framework**: Modern C++ / Python C2 with sleek UI, sleeping evasion, and AMSI/ETW bypass integration.
- **Mythic (SpecterOps)**: Dockerized, multi-agent C2 framework designed for red teams.

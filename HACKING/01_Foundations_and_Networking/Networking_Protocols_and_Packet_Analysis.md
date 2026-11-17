# Module 01: Networking Protocols, Packet Analysis & Core Internals

A deep-dive technical reference on computer networking, protocol mechanics, packet inspection, and operating system internals required for offensive security operations.

---

## 1. The TCP/IP & OSI Models in Offensive Operations

Understanding the exact layer at which an attack or detection occurs is crucial for troubleshooting and stealth.

```
OSI Layer           Protocol Examples                  Offensive Focus / Attacks
--------------------------------------------------------------------------------------------------
7. Application      HTTP, DNS, SMB, SSH, Kerberos     Web exploits, SQLi, Kerberoasting, RPC abuse
6. Presentation     TLS, SSL, MIME, XDR               SSL Stripping, Certificate spoofing
5. Session          NetBIOS, RPC, SOCKS               Session hijacking, Token reuse
4. Transport        TCP, UDP                          SYN Flooding, Port scanning, RST injection
3. Network          IPv4, IPv6, ICMP, IPsec           IP Spoofing, ICMP tunneling, BGP hijacking
2. Data Link        Ethernet, ARP, MAC, 802.11        ARP Poisoning, MAC flooding, Evil Twin
1. Physical         Cables, Radio Frequencies         Hardware implants, Rubber Ducky, Tap devices
```

---

## 2. Core Protocols Deep-Dive

### 1. Address Resolution Protocol (ARP) & Poisoning
- **Mechanics**: Layer 2 protocol mapping 32-bit IPv4 addresses to 48-bit physical MAC addresses. ARP is stateless; nodes accept ARP replies even without sending a request.
- **ARP Poisoning / Man-In-The-Middle (MITM)**:
  - An attacker sends gratuitous ARP replies telling the Gateway that the attacker's MAC owns the Victim's IP, and telling the Victim that the attacker's MAC owns the Gateway's IP.
  - Commands:
    ```bash
    # Enable kernel IP forwarding
    echo 1 > /proc/sys/net/ipv4/ip_forward
    # Arpspoof
    arpspoof -i eth0 -t 192.168.1.50 -r 192.168.1.1
    # Modern tool
    bettercap -eval "net.probe on; set arp.spoof.targets 192.168.1.50; arp.spoof on"
    ```

### 2. Transmission Control Protocol (TCP)
- **3-Way Handshake**:
  1. Client -> Server: `SYN` (Flags: `[S]`, Sequence = `seq_c`)
  2. Server -> Client: `SYN-ACK` (Flags: `[S.]`, Sequence = `seq_s`, Ack = `seq_c + 1`)
  3. Client -> Server: `ACK` (Flags: `[.]`, Ack = `seq_s + 1`)
- **Offensive Implication in Scanning**:
  - **TCP Connect Scan (`-sT`)**: Completes full 3-way handshake (`SYN` -> `SYN-ACK` -> `ACK`). Logged in application level logs.
  - **SYN Stealth Scan (`-sS`)**: Sends `SYN`, receives `SYN-ACK`, immediately responds with `RST` to tear down the connection before the OS application layer completes the handshake. Requires root/raw socket privileges.

### 3. Domain Name System (DNS)
- **Port**: 53 UDP (Queries) / 53 TCP (Zone transfers & responses > 512 bytes).
- **DNS Records**:
  - `A` (IPv4), `AAAA` (IPv6), `CNAME` (Alias), `MX` (Mail), `TXT` (SPF, DKIM, verification tokens), `PTR` (Reverse DNS).
- **DNS Zone Transfer (AXFR)**:
  - An administrator mistake permitting anyone to download the entire internal DNS zone database.
  ```bash
  # Attempt zone transfer against all nameservers of a target domain
  dig axfr @ns1.target.com target.com
  dnsrecon -d target.com -t axfr
  ```

---

## 3. Wireshark & Tcpdump Mastery

### Essential Wireshark Display Filters
```text
# Filter specific host IP
ip.addr == 192.168.1.100

# Filter HTTP POST requests (great for finding submitted credentials)
http.request.method == "POST"

# Filter TCP Handshake SYN packets only
tcp.flags.syn == 1 && tcp.flags.ack == 0

# Find DNS queries for a specific domain
dns.qry.name contains "target.com"

# Filter Kerberos authentication requests
kerberos.msg.type == 10  # AS-REQ
kerberos.msg.type == 12  # TGS-REQ

# Filter SMB2 file read/write operations
smb2.cmd == 5 || smb2.cmd == 6
```

### Tcpdump Command Reference
```bash
# Capture full packet payload (-s 0) on interface eth0, write to file
tcpdump -i eth0 -nn -vv -s 0 -w capture.pcap

# Capture only traffic on port 80 or 443 excluding attacker's own IP
tcpdump -i eth0 'not host 192.168.1.5 and (port 80 or port 443)'

# Read pcap and print ASCII payload (view cleartext passwords)
tcpdump -A -r capture.pcap 'tcp port 21 or tcp port 80'
```

---

## 4. Linux & Windows OS Internals Reference

### Linux Process Execution & Syscalls
- When a command like `/bin/ls` is executed:
  1. The shell calls `fork()` to create a child process.
  2. The child process calls `execve("/bin/ls", argv, envp)`.
  3. The Linux Kernel checks permissions: Read & Execute (`rx`) on the binary, and UID/EUID.
  4. If the binary has **SUID bit set (`chmod u+s`)**, the kernel sets the Effective UID (`EUID`) of the process to the binary owner (often `root`), allowing privilege escalation if the binary can be manipulated.

### Windows Security Architecture
- **LSASS (Local Security Authority Subsystem Service)**:
  - Handles authentication and holds active security tokens, Kerberos tickets, and cached credentials in memory.
  - Protected Process Light (PPL) in modern Windows prevents non-whitelisted processes from reading LSASS memory directly without kernel drivers or bypasses.
- **Access Tokens**:
  - Created at logon. Contains: User SID, Group SIDs, Privileges (e.g., `SeDebugPrivilege`, `SeImpersonatePrivilege`).
  - **Primary Token**: Assigned to a process upon creation.
  - **Impersonation Token**: Enables a server thread to execute actions on behalf of a client with that client's privileges.

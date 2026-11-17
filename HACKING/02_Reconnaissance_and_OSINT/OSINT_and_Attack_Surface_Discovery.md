# Module 02: OSINT, Attack Surface Discovery & Active Scanning

Comprehensive guide to mapping external enterprise attack surfaces, continuous subdomain enumeration, asset discovery, port scanning, and automated directory fuzzing.

---

## 1. Passive Reconnaissance Pipeline

A professional reconnaissance pipeline proceeds from broad organizational mapping to specific live host resolution.

```
[ Organization / Domain ]
         │
         ▼
[ ASN & IP Ranges ] ──────────> (BGPview, whois, Hurricane Electric)
         │
         ▼
[ Subdomain Discovery ] ──────> (crt.sh, Subfinder, Amass, Assetfinder)
         │
         ▼
[ DNS Resolution ] ───────────> (puredns, dnsx with trusted resolvers)
         │
         ▼
[ Web Port Probing ] ─────────> (httpx: status codes, title, tech stack)
         │
         ▼
[ Active Directory Fuzzing ] ──> (ffuf, dirsearch, feroxbuster)
```

---

## 2. Subdomain Enumeration Methodology

### 1. Certificate Transparency (CT) Logs
Every modern public TLS certificate is logged in append-only public logs.
```bash
# Query crt.sh via curl and jq
curl -s "https://crt.sh/?q=%25.target.com&output=json" | \
  jq -r '.[].name_value' | sed 's/\*\.//g' | sort -u > crt_subs.txt
```

### 2. Multi-Engine Passive Enumeration
```bash
# Subfinder (Passive sources: VirusTotal, Shodan, Censys, ThreatCrowd, etc.)
subfinder -d target.com -all -silent -o subfinder_subs.txt

# Assetfinder
assetfinder --subs-only target.com >> assetfinder_subs.txt

# Combine and deduplicate
cat crt_subs.txt subfinder_subs.txt assetfinder_subs.txt | sort -u > raw_subdomains.txt
```

### 3. High-Speed DNS Resolution & Filtering Live Hosts
Never waste time sending HTTP requests to dead domains. Resolve them first:
```bash
# Filter live resolving hosts using dnsx
dnsx -l raw_subdomains.txt -r /path/to/resolvers.txt -a -resp -o resolved_hosts.txt

# Probe HTTP/HTTPS services and extract technology headers
cat resolved_hosts.txt | httpx -silent -title -tech-detect -status-code -follow-redirects -o live_web_assets.txt
```

---

## 3. Active Port Scanning & Nmap Playbook

### Standard Penetration Testing Nmap Strategy
Never run full `-A` or `-sC -sV` on all 65,535 ports at once—it will take hours and get blocked. Use a 2-stage scanning process:

#### Stage 1: Blazing Fast Discovery of Open Ports
```bash
# SYN scan across all 65,535 TCP ports with packet rate of 1500 pkts/sec
nmap -sS -p- --min-rate 1500 -T4 -n -Pn -v 10.10.10.100 -oG nmap_all_ports.gnmap

# Extract open ports into comma-separated list
PORTS=$(cat nmap_all_ports.gnmap | grep -oP '\d+/open' | cut -d '/' -f 1 | tr '\n' ',' | sed 's/,$//')
echo "Discovered Ports: $PORTS"
```

#### Stage 2: Deep Fingerprinting on Open Ports Only
```bash
# Run default NSE scripts and banner grabbing on discovered ports
nmap -sC -sV -p $PORTS -Pn -oA target_deep_scan 10.10.10.100
```

### Essential Nmap NSE Scripts by Service
```bash
# SMB vulnerabilities & shares
nmap -p 445 --script smb-vuln-*,smb-enum-shares,smb-enum-users 10.10.10.100

# HTTP vulnerability scanning
nmap -p 80,443 --script http-enum,http-vuln-cve* 10.10.10.100

# SSL/TLS cipher strength & certificate information
nmap -p 443 --script ssl-enum-ciphers,ssl-cert 10.10.10.100
```

---

## 4. Web Fuzzing with FFUF

### Directory & File Brute-Forcing
```bash
# Recursively find directories and files matching extensions
ffuf -w /usr/share/seclists/Discovery/Web-Content/raft-medium-directories.txt \
     -u https://target.com/FUZZ \
     -e .php,.html,.js,.json,.txt,.bak \
     -mc 200,204,301,302,307,401,403 \
     -o ffuf_dirs.json
```

### Virtual Host (VHost) / Subdomain Fuzzing
Useful when testing an IP hosting multiple virtual hosts:
```bash
# Fuzz the Host header, filtering out the default page size
ffuf -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-110000.txt \
     -u http://10.10.10.100/ \
     -H "Host: FUZZ.target.local" \
     -fs 15124
```

### Parameter Fuzzing (GET / POST)
```bash
# Find hidden GET parameters on an endpoint
ffuf -w /usr/share/seclists/Discovery/Web-Content/burp-parameter-names.txt \
     -u "https://target.com/profile.php?FUZZ=1" \
     -fs 4520
```

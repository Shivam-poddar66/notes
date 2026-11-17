#!/usr/bin/env python3
"""
Offensive Security Practice Tool 1: Multi-Threaded TCP Port Scanner
Usage: python port_scanner.py 127.0.0.1 1 1000
"""

import sys
import socket
from datetime import datetime
import concurrent.futures

def scan_port(target_ip, port):
    try:
        # Create TCP stream socket
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.settimeout(0.8) # Timeout in seconds
        
        # Connect to target port
        result = s.connect_ex((target_ip, port))
        if result == 0:
            # Try basic service banner grab
            banner = ""
            try:
                s.send(b"HEAD / HTTP/1.0\r\n\r\n")
                banner = s.recv(1024).decode('utf-8', errors='ignore').split('\r\n')[0]
            except:
                pass
            s.close()
            return (port, True, banner)
        s.close()
        return (port, False, "")
    except Exception:
        return (port, False, "")

def main():
    target = sys.argv[1] if len(sys.argv) > 1 else "127.0.0.1"
    start_port = int(sys.argv[2]) if len(sys.argv) > 2 else 1
    end_port = int(sys.argv[3]) if len(sys.argv) > 3 else 1024

    try:
        target_ip = socket.gethostbyname(target)
    except socket.gaierror:
        print(f"[!] Hostname could not be resolved: {target}")
        sys.exit(1)

    print("=" * 60)
    print(f" Scanning target: {target} ({target_ip})")
    print(f" Port range: {start_port} to {end_port}")
    print(f" Time started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 60)

    open_ports = []
    # Use ThreadPoolExecutor for fast concurrent scanning
    with concurrent.futures.ThreadPoolExecutor(max_workers=50) as executor:
        futures = {executor.submit(scan_port, target_ip, port): port for port in range(start_port, end_port + 1)}
        for future in concurrent.futures.as_completed(futures):
            port, is_open, banner = future.result()
            if is_open:
                banner_str = f" - Banner: {banner}" if banner else ""
                print(f"[+] Port {port:<5} is OPEN{banner_str}")
                open_ports.append(port)

    print("=" * 60)
    print(f"[✓] Scan Complete! Found {len(open_ports)} open port(s): {sorted(open_ports)}")

if __name__ == "__main__":
    main()

# 3) OWASP Top 10 Defenses for Node.js Applications

## Executive Overview
Node.js web applications are subject to specialized attack vectors targeting JavaScript runtime characteristics (Prototype Pollution, Event Loop ReDoS starvation) and distributed web architecture vulnerabilities (SSRF, NoSQL Injection, Command Injection).

Hardening a production Node.js application requires embedding defensive controls across every layer of the software stack.

---

## 1. Injection Attack Vectors & Defenses

```text
┌───────────────────────┬────────────────────────────────────┬──────────────────────────────────┐
│ Injection Vector      │ Attack Payload Example             │ Defensive Mitigation             │
├───────────────────────┼────────────────────────────────────┼──────────────────────────────────┤
│ SQL Injection         │ ' OR 1=1; DROP TABLE users;--      │ Strict Parameterized Queries ($1)│
├───────────────────────┼────────────────────────────────────┼──────────────────────────────────┤
│ NoSQL Injection       │ { "password": { "$gt": "" } }      │ Schema sanitization / MongoDB    │
│                       │                                    │ operator stripping (`express-    │
│                       │                                    │ mongo-sanitize`)                 │
├───────────────────────┼────────────────────────────────────┼──────────────────────────────────┤
│ OS Command Injection  │ `file.png; rm -rf /` passed to     │ Never use `child_process.exec()`;│
│                       │ `exec()`                           │ Use `execFile()` with argv array.│
└───────────────────────┴────────────────────────────────────┴──────────────────────────────────┘
```

### 1.1 Safe OS Command Execution Pattern

```typescript
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';

const execFileAsync = promisify(execFile);

// DANGEROUS: exec(`convert ${userInput} output.png`) executes inside /bin/sh shell!

// SAFE: execFile invokes binary directly without invoking a command shell
export async function convertImageSafe(inputFileName: string, outputFileName: string) {
  // Arguments are passed as an isolated string array; shell meta-characters (;, &&, |) are treated as literals
  const args = [inputFileName, '-resize', '800x600', outputFileName];
  
  const { stdout, stderr } = await execFileAsync('/usr/bin/convert', args, {
    timeout: 5000,       // 5s execution timeout
    maxBuffer: 1024 * 1024, // 1MB buffer limit
  });

  return stdout;
}
```

---

## 2. Server-Side Request Forgery (SSRF) Defense

SSRF occurs when a backend server fetches a remote URL provided by a user (e.g. webhook URLs, avatar downloaders) and an attacker points the URL to internal private infrastructure or cloud metadata services:
- `http://169.254.169.254/latest/meta-data/` (AWS IAM credentials)
- `http://127.0.0.1:6379/` (Internal Redis)
- `http://10.0.0.5:8080/admin` (Internal Kubernetes cluster microservices)

### 2.1 SSRF-Safe Webhook Fetcher with DNS Resolution Check

```typescript
import dns from 'node:dns/promises';
import ipaddr from 'ipaddr.js';

export async function validateSafeOutboundUrl(targetUrl: string): Promise<string> {
  const parsed = new URL(targetUrl);

  // 1. Enforce HTTP/HTTPS protocols only
  if (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') {
    throw new Error('Invalid protocol: Only HTTP/HTTPS allowed');
  }

  // 2. Resolve DNS hostname to IP addresses
  const addresses = await dns.lookup(parsed.hostname, { all: true });

  for (const { address } of addresses) {
    const ip = ipaddr.parse(address);
    const range = ip.range();

    // 3. Block Private, Loopback, Carrier-Grade NAT, and Link-Local (Cloud Metadata) IP ranges
    const forbiddenRanges = [
      'loopback',        // 127.0.0.1
      'private',         // 10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16
      'linkLocal',       // 169.254.0.0/16 (AWS / GCP Metadata)
      'carrierGradeNat', // 100.64.0.0/10
      'uniqueLocal',     // IPv6 fc00::/7
    ];

    if (forbiddenRanges.includes(range)) {
      throw new Error(`SECURITY ALERT: Outbound request to private IP address blocked: ${address} (${range})`);
    }
  }

  return targetUrl;
}
```

---

## 3. Regular Expression Denial of Service (ReDoS)

V8's built-in regular expression engine uses **backtracking Nondeterministic Finite Automata (NFA)**. Poorly constructed regex patterns with nested quantifiers (e.g. `^(a+)+$`) execute in $O(2^N)$ exponential time, freezing the single-threaded Event Loop when processing malicious payloads:

```text
Evaluating: /^(a+)+$/ on "aaaaaaaaaaaaaaaaaaaaaaaaaaaa!"
- 30 characters: ~1 second of 100% CPU lockup!
- 35 characters: ~35 seconds of 100% CPU lockup!
- 40 characters: 18 MINUTES of Event Loop FREEZE!
```

### 3.1 Defense: Non-Backtracking RE2 Engine
For user-provided or dynamic regex searches, use **`re2`** (linear $O(N)$ matching):

```typescript
import RE2 from 're2';

// RE2 uses Google's linear-time automata engine with zero backtracking guarantee
export function safeRegexMatch(pattern: string, input: string): boolean {
  const re = new RE2(pattern);
  return re.test(input);
}
```

---

## 4. Prototype Pollution Defense

Prototype Pollution occurs when recursive object merge functions allow modifying `Object.prototype`, injecting malicious properties across every JavaScript object in the process heap.

```typescript
// Attacker JSON Payload:
const payload = JSON.parse('{ "__proto__": { "isAdmin": true } }');

// VULNERABLE MERGE:
// targetObject[key] = payload[key] -> Sets Object.prototype.isAdmin = true!
// Result: ({}).isAdmin === true for ALL users in the runtime!
```

### 4.1 Defensive Patterns
1. **Dictionary Objects**: Use `Object.create(null)` or `new Map()` which have no prototype chain.
2. **Safe JSON Parsing**: Validate and strip `__proto__`, `constructor`, and `prototype` keys during object hydration.
3. **Freeze Prototype**: Call `Object.freeze(Object.prototype)` during application startup.

```typescript
export function safeObjectMerge<T extends object, U extends object>(target: T, source: U): T & U {
  const output = { ...target } as any;

  for (const key of Object.keys(source)) {
    // Drop prototype pollution vectors
    if (key === '__proto__' || key === 'constructor' || key === 'prototype') {
      continue;
    }

    const value = (source as any)[key];
    if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
      output[key] = safeObjectMerge(output[key] || {}, value);
    } else {
      output[key] = value;
    }
  }

  return output;
}
```

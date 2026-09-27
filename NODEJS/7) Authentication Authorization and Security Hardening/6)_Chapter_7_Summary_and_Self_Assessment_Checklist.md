# 6) Chapter 7 Summary and Self-Assessment Checklist

## Chapter 7 Quick Revision Summary

### 1. Authentication Architecture
- **Dual-Token Architecture**: Short-lived Access Token (10–15m) + Long-lived Refresh Token (30 days).
- **Refresh Token Rotation**: Rotate the refresh token on every usage. If an already-used refresh token is presented, detect reuse and immediately revoke the entire user token family.
- **Password Hashing**: Use **`Argon2id`** with memory-hard parameters (`memoryCost: 65536, timeCost: 3`). Never use fast hashes (MD5, SHA-256).

### 2. Authorization (RBAC & ABAC)
- **RBAC**: Maps users to roles with static permission lists.
- **ABAC**: Dynamic policy checks evaluating subjects, resources, and attributes (e.g. `@casl/ability`). Prevents Broken Object Level Authorization (BOLA/IDOR).

### 3. OWASP Top 10 Defenses
- **Injection**: Use parameterized SQL queries (`$1`), sanitize NoSQL query operators, and use `execFile()` instead of `exec()` for OS commands.
- **SSRF**: Resolve hostnames to IP addresses before sending outbound requests and block private/loopback/cloud metadata IP ranges (`169.254.169.254`, `10.0.0.0/8`, `127.0.0.1`).
- **ReDoS**: Detect exponential backtracking regex patterns or use Google RE2 linear-time automata.
- **Prototype Pollution**: Sanitize `__proto__` / `constructor` properties or use `Object.create(null)`.

### 4. Edge Security & Headers
- **Security Headers**: Use `helmet` for HSTS, CSP, and `X-Frame-Options: DENY`.
- **CORS**: Never reflect wildcard origins with credentials (`Access-Control-Allow-Credentials: true`).
- **Rate Limiting**: Enforce sliding window rate limiters backed by Redis (`rate-limiter-flexible`).

---

## Self-Assessment Challenge Questions

### Q1: Why is storing JWTs in browser `localStorage` considered a security vulnerability compared to `HttpOnly` cookies?
**Answer**: Any JavaScript code executing on the page (including third-party scripts, analytics tools, or code injected via an XSS vulnerability) has full read access to `localStorage`. An attacker can steal the JWT and impersonate the user. `HttpOnly` cookies cannot be accessed or read by client-side JavaScript (`document.cookie`), neutralizing token theft via XSS.

### Q2: How does Refresh Token Reuse Detection mitigate the risk of stolen credentials?
**Answer**: When a legitimate client exchanges a refresh token for a new pair, the old refresh token is marked as invalidated. If an attacker later presents that same stolen old refresh token, the backend recognizes that an invalidated token was submitted. Because it cannot determine whether the attacker or the legitimate user holds the newer token, the server immediately revokes all refresh tokens belonging to that user family, forcing a re-authentication and cutting off the attacker.

### Q3: What is Server-Side Request Forgery (SSRF), and why is checking `parsedUrl.hostname !== 'localhost'` insufficient?
**Answer**: Attackers can bypass simple hostname checks using DNS rebinding, alternative IP encodings (`0177.0.0.1`, `2130706433`), private IP subnets (`10.0.0.1`, `172.16.0.1`), or cloud provider metadata endpoints (`169.254.169.254` to steal IAM instance credentials). Robust defense requires resolving the hostname to actual IP addresses using `dns.lookup()` and validating against all private/link-local CIDR ranges using `ipaddr.js`.

### Q4: Why is `child_process.exec()` vulnerable to command injection while `child_process.execFile()` is secure?
**Answer**: `exec()` spawns a subshell (`/bin/sh` on Unix, `cmd.exe` on Windows) and passes the command as a raw string. Shell meta-characters like `;`, `&&`, and `|` are parsed by the shell to execute secondary injected commands. `execFile()` invokes the target binary executable directly via kernel syscalls without a shell, passing arguments as a discrete array of literal strings where shell operators are treated as harmless text.

### Q5: What is Prototype Pollution in Node.js, and how does it compromise application security?
**Answer**: JavaScript objects inherit properties from `Object.prototype`. If an un-sanitized recursive merge function modifies properties on `__proto__`, the injected properties become visible across *all* objects in the V8 heap. Attackers can inject properties like `isAdmin = true` to bypass authentication checks or overwrite methods like `toString` to cause process denial of service.

---

## Chapter 7 Mastery Verification Checklist

Check off each item once you can explain or implement it with confidence:

- [ ] Implement a dual-token JWT authentication architecture with access and refresh tokens.
- [ ] Implement refresh token family rotation with automatic breach reuse detection.
- [ ] Hash and verify passwords using memory-hard Argon2id with production parameters.
- [ ] Configure secure cookies with `HttpOnly`, `Secure`, `SameSite: 'Strict'`, and `__Host-` prefixes.
- [ ] Implement an OAuth 2.0 Authorization Code flow with PKCE verification.
- [ ] Build a type-safe Role-Based Access Control (RBAC) authorization matrix.
- [ ] Implement fine-grained Attribute-Based Access Control (ABAC) using `@casl/ability`.
- [ ] Prevent OS command injection by replacing `exec()` with `execFile()`.
- [ ] Build an SSRF-safe HTTP request dispatcher with DNS resolution and private IP filtering.
- [ ] Detect and eliminate ReDoS regex patterns using the RE2 engine.
- [ ] Prevent Prototype Pollution using safe object merge functions and `Object.create(null)`.
- [ ] Harden Express/Fastify applications with the `helmet` security header suite.
- [ ] Configure secure CORS policies without wildcard credential reflections.
- [ ] Enforce distributed sliding-window rate limiting using Redis and `rate-limiter-flexible`.
- [ ] Protect software supply chains with lockfile freezing and automated vulnerability audits.

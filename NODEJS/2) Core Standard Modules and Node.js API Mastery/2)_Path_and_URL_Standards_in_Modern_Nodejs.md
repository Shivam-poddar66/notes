# 2) Path and URL Standards in Modern Node.js

## Executive Overview
Filesystem paths and uniform resource locators (URLs) represent the two primary addressing standards in backend computing. Writing robust, cross-platform Node.js applications requires deep familiarity with how operating systems handle directory separators (POSIX `/` vs Windows `\`), relative vs absolute path calculations, and the conversion bridges between the WHATWG URL standard and the host filesystem.

---

## 1. Cross-Platform Path Handling (`node:path`)

Operating systems differ fundamentally in how paths are formatted:
- **POSIX (Linux, macOS, BSD)**: Forward slash delimiter (`/`), single root (`/`), case-sensitive filesystems (usually).
- **Windows (NTFS, FAT32)**: Backslash delimiter (`\`), drive letters (`C:\`), volume names, case-insensitive filesystems.

The `node:path` module automatically adapts to the host OS. It also provides explicit namespaces: `path.posix` and `path.win32` when cross-platform consistency (e.g. tarballs, URLs, zip archives) is required regardless of host OS.

```text
┌────────────────────────────────────────────────────────────┐
│                    node:path Architecture                  │
├─────────────────────────────┬──────────────────────────────┤
│         path.posix          │          path.win32          │
│   - Separator: '/'          │   - Separator: '\\'          │
│   - Delimiter: ':'          │   - Delimiter: ';'           │
│   - Root: '/'               │   - Root: 'C:\\' or '\\\\'   │
└─────────────────────────────┴──────────────────────────────┘
```

---

## 2. Core Path Arithmetic Operations

### 2.1 `path.join()` vs `path.resolve()`
Understanding the exact difference between `join` and `resolve` is essential for every backend engineer:

| API | Behavior | Mental Model |
|---|---|---|
| `path.join([...paths])` | Joins all given segments together using the platform-specific separator and normalizes the resulting path. | String concatenation with normalization. |
| `path.resolve([...paths])` | Resolves a sequence of paths or path segments into an **absolute path**, processing from right to left until an absolute path is formed. | Simulates running `cd <segment>` sequentially in a shell. |

```typescript
import path from 'node:path';

// Assume current working directory is: /usr/local/app (or E:\app on Windows)

// path.join
path.join('/foo', 'bar', 'baz/asdf', 'quux', '..');
// Output POSIX: '/foo/bar/baz/asdf'
// Output Win32: '\\foo\\bar\\baz\\asdf'

// path.resolve (Treats leading slash as root)
path.resolve('/foo/bar', './baz');
// Output: '/foo/bar/baz'

path.resolve('/foo/bar', '/tmp/file/');
// Output: '/tmp/file' (because /tmp/file/ is already absolute, previous segments discarded)

path.resolve('wwwroot', 'static_files/png/', '../gif/image.gif');
// If cwd is /usr/local/app:
// Output: '/usr/local/app/wwwroot/static_files/gif/image.gif'
```

### 2.2 `path.normalize()`, `path.parse()`, and `path.relative()`

```typescript
import path from 'node:path';

// 1. Normalization: Resolves '..' and '.' and duplicate slashes
const messyPath = '/app/users/../config//database.json';
console.log(path.normalize(messyPath)); 
// Output: '/app/config/database.json'

// 2. Parsing: Deconstructs path into structured components
const parsed = path.parse('/var/www/html/index.html');
console.log(parsed);
/*
{
  root: '/',
  dir: '/var/www/html',
  base: 'index.html',
  ext: '.html',
  name: 'index'
}
*/

// 3. Relative calculation: Determines relative path from A to B
const from = '/data/oranges/sub';
const to = '/data/apples/red.png';
console.log(path.relative(from, to));
// Output: '../../apples/red.png'
```

---

## 3. ESM vs CommonJS Environment Paths

In CommonJS (`.cjs`), Node.js automatically injected global variables `__dirname` and `__filename`. In ECMAScript Modules (`.mjs`), these variables **do not exist**.

### Modern ESM Path Idioms (Node.js v20.11.0+)
Node.js v20.11+ introduced native standard helpers directly on `import.meta`:

```typescript
// Modern ESM (Node.js 20.11+)
const currentDir = import.meta.dirname;
const currentFile = import.meta.filename;

console.log('Current directory:', currentDir);
console.log('Current file:', currentFile);
```

### Universal ESM Path Translation (All Node Versions)
For backward compatibility with earlier Node versions:

```typescript
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
```

---

## 4. WHATWG URL Standards in Node.js

Node.js standard library includes full compliance with the [WHATWG URL Standard](https://url.spec.whatwg.org/). Legacy `url.parse()` is deprecated due to security ambiguities.

### 4.1 URL Parsing & Query Manipulation

```typescript
import { URL, URLSearchParams } from 'node:url';

const myUrl = new URL('https://api.example.com:8080/v1/users?role=admin&active=true#profile');

console.log(myUrl.protocol); // 'https:'
console.log(myUrl.hostname); // 'api.example.com'
console.log(myUrl.port);     // '8080'
console.log(myUrl.pathname); // '/v1/users'
console.log(myUrl.hash);     // '#profile'

// Structured Query Parameter Operations
myUrl.searchParams.append('sort', 'desc');
myUrl.searchParams.set('role', 'superadmin'); // Overwrite

console.log(myUrl.searchParams.get('sort')); // 'desc'
console.log(myUrl.toString()); 
// 'https://api.example.com:8080/v1/users?role=superadmin&active=true&sort=desc#profile'
```

### 4.2 Converting Between File URLs and File System Paths

```typescript
import { fileURLToPath, pathToFileURL } from 'node:url';
import path from 'node:path';

// Path -> File URL
const absolutePath = path.resolve('data/records.csv');
const fileUrl = pathToFileURL(absolutePath);
console.log(fileUrl.href); 
// POSIX: 'file:///data/records.csv'
// Win32: 'file:///E:/app/data/records.csv'

// File URL -> Path
const convertedBack = fileURLToPath(fileUrl);
console.log(convertedBack);
// Matches exact OS host filesystem path representation
```

---

## 5. Security Defense: Directory Traversal (Path Traversal) Attacks

A critical security flaw in web servers is **Arbitrary Directory Traversal** (Path Traversal / Zip Slip), where attackers supply inputs like `../../../../etc/passwd` or `..%2F..%2Fwindows%2Fsystem32` to escape the public root.

### Vulnerability Vector & Defensive Sanitization

```typescript
import path from 'node:path';
import fs from 'node:fs/promises';

const SAFE_ROOT_DIR = path.resolve('/var/www/public_uploads');

/**
 * Safely resolves user-supplied relative path against a strict root directory.
 * Throws an error if directory traversal is attempted.
 */
export function safeResolvePath(userSuppliedPath: string): string {
  // 1. Resolve candidate path against safe root
  const safeTarget = path.resolve(SAFE_ROOT_DIR, userSuppliedPath);

  // 2. Enforce boundary check
  // The resolved path MUST start with SAFE_ROOT_DIR + path.sep
  if (!safeTarget.startsWith(SAFE_ROOT_DIR + path.sep) && safeTarget !== SAFE_ROOT_DIR) {
    throw new Error(`SECURITY ALERT: Path traversal attempt detected: ${userSuppliedPath}`);
  }

  return safeTarget;
}

// Example usage in API handler
async function serveUserFile(userInput: string) {
  try {
    const validatedPath = safeResolvePath(userInput);
    return await fs.readFile(validatedPath);
  } catch (err: any) {
    console.error(`Blocked unauthorized access: ${err.message}`);
    throw new Error('Access denied');
  }
}
```

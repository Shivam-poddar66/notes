# 1) File System (`fs` and `fs/promises`) Mastery

## Executive Overview
The Node.js File System (`fs`) module provides an interface to the host operating system's filesystem, wrapping standard POSIX functions. Because POSIX filesystem operations are fundamentally blocking at the operating system level across most platforms, Libuv routes file I/O operations through its internal **Worker Threadpool** (`UV_THREADPOOL_SIZE`), preventing the JavaScript single thread from stalling during disk access.

Mastery of the `fs` module requires understanding the tradeoffs between high-level convenience methods (`readFile`, `writeFile`), descriptor-based low-level operations (`fs.open`, `fs.read`, `fs.write`), memory-efficient streaming, atomic transactional mutations, and operating system permission models.

---

## 1. Operating System Mechanics & Libuv Threadpool Dispatch

When an asynchronous `fs` function is called in JavaScript:
1. Node.js standard library validates parameters and encodes strings to path buffers.
2. The internal C++ binding (`src/node_file.cc`) packages the request into a Libuv `uv_fs_t` work struct.
3. Libuv submits the task to the **Libuv Threadpool** (default size: 4).
4. An OS worker thread issues the synchronous system call (`open(2)`, `read(2)`, `write(2)`, `stat(2)`).
5. Upon completion, the worker thread notifies the Libuv Event Loop via an internal loop pipe/async handle.
6. The Event Loop triggers the JavaScript callback or resolves the Promise during the **Poll Phase**.

```text
┌────────────────────────────────────────────────────────┐
│               JavaScript Call: fs.readFile()           │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│           Node.js C++ Layer (node_file.cc)             │
│            Allocates uv_fs_t work request              │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                 Libuv Worker Threadpool                │
│    Thread 1         Thread 2         Thread 3          │
│ ┌────────────┐   ┌────────────┐   ┌────────────┐       │
│ │  open(2)   │   │  read(2)   │   │  stat(2)   │       │
│ └────────────┘   └────────────┘   └────────────┘       │
└───────────────────────────┬────────────────────────────┘
                            │ Notification
                            ▼
┌────────────────────────────────────────────────────────┐
│          Event Loop (Poll Phase Callback Queue)        │
│          Resolves Promise / Runs Callback              │
└────────────────────────────────────────────────────────┘
```

> [!WARNING]
> Synchronous `fs.*Sync()` methods bypass the Libuv threadpool entirely and execute directly on the main V8 JavaScript thread. Calling synchronous file methods in request handlers completely blocks the Event Loop, preventing concurrent HTTP requests from being processed.

---

## 2. Low-Level File Descriptors (`fs.open`, `read`, `write`, `close`)

A **File Descriptor (FD)** is a non-negative integer assigned by the operating system kernel to uniquely identify an open file in a process's file table. Working directly with file descriptors gives granular control over byte offsets, flags, and memory reuse.

### 2.1 File System Flags
| Flag | Description | Positioning |
|---|---|---|
| `'r'` | Open for reading. Fails if file does not exist. | Offset 0 |
| `'r+'` | Open for reading and writing. Fails if file does not exist. | Offset 0 |
| `'w'` | Open for writing. Truncates file to 0 bytes or creates it. | Offset 0 |
| `'wx'` / `'w+x'` | Exclusive write. Fails if target file path already exists. | Offset 0 |
| `'a'` | Open for appending. Creates file if missing. | End of file |
| `'a+'` | Open for reading and appending. Creates file if missing. | End of file |

### 2.2 Low-Level Chunked Reader using `FileHandle` (`fs/promises`)

```typescript
import fs from 'node:fs/promises';
import { Buffer } from 'node:buffer';

async function readBinaryHeader(filePath: string): Promise<Buffer> {
  let fileHandle: fs.FileHandle | null = null;
  try {
    // Open file descriptor with read-only flag
    fileHandle = await fs.open(filePath, 'r');
    
    // Allocate a reusable 64-byte buffer
    const buffer = Buffer.alloc(64);
    const offset = 0;         // Offset in buffer to write to
    const length = 64;        // Number of bytes to read
    const position = 0;      // File offset to start reading from

    const { bytesRead } = await fileHandle.read(buffer, offset, length, position);
    
    console.log(`Successfully read ${bytesRead} bytes from descriptor ${fileHandle.fd}`);
    return buffer.subarray(0, bytesRead);
  } finally {
    // Crucial: Always release file descriptors to prevent OS FD leaks (EMFILE / ENFILE)
    if (fileHandle) {
      await fileHandle.close();
    }
  }
}
```

---

## 3. Safe Atomic File Operations (Write & Rename Pattern)

Directly writing to an active file (`fs.writeFile('config.json', data)`) is **not atomic**. If the process crashes, the server loses power, or a concurrent process reads the file midway through a write, the file is corrupted with partial data.

### 3.1 The POSIX Atomic Rename Guarantee
On POSIX filesystems (and modern NTFS implementations), `rename(2)` is an atomic operation within the same filesystem boundary (same physical disk/mount point). If a rename succeeds, readers will either see the old version or the completely written new version—never a partial write.

### 3.2 Production-Grade Atomic Write Implementation

```typescript
import fs from 'node:fs/promises';
import path from 'node:path';
import crypto from 'node:crypto';

export async function writeAtomic(filePath: string, data: string | Uint8Array): Promise<void> {
  const resolvedPath = path.resolve(filePath);
  const dir = path.dirname(resolvedPath);
  
  // Create unique temp file in the SAME directory to ensure same filesystem mount
  const uniqueId = crypto.randomBytes(8).toString('hex');
  const tempPath = path.join(dir, `.${path.basename(resolvedPath)}.${uniqueId}.tmp`);

  let fileHandle: fs.FileHandle | null = null;
  try {
    // 1. Open temp file with exclusive creation ('wx')
    fileHandle = await fs.open(tempPath, 'wx', 0o600);

    // 2. Write payload
    await fileHandle.writeFile(data);

    // 3. Flush dirty OS page caches to physical storage (fsync)
    await fileHandle.sync();

    // 4. Close file descriptor before renaming
    await fileHandle.close();
    fileHandle = null;

    // 5. Atomically replace target file
    await fs.rename(tempPath, resolvedPath);
  } catch (error) {
    // Cleanup temporary file on any error
    try {
      await fs.unlink(tempPath);
    } catch {}
    throw error;
  } finally {
    if (fileHandle) {
      await fileHandle.close().catch(() => {});
    }
  }
}
```

---

## 4. Directory Traversal: High-Performance Recursive Scanning

Modern Node.js (`v20+`) includes native recursive directory traversal in `fs.readdir`, drastically outperforming manual recursive JS implementations by avoiding unnecessary intermediate `stat` syscalls.

```typescript
import fs from 'node:fs/promises';
import path from 'node:path';

interface ScannedFile {
  name: string;
  absolutePath: string;
  size: number;
}

export async function scanDirectoryFast(targetDir: string): Promise<ScannedFile[]> {
  // withFileTypes: true returns Dirent objects containing file type info from readdir
  // avoiding additional stat() calls per entry
  const entries = await fs.readdir(targetDir, {
    recursive: true,
    withFileTypes: true
  });

  const files: ScannedFile[] = [];

  for (const entry of entries) {
    if (entry.isFile()) {
      // In recursive mode, entry.parentPath (v20.1.0+) provides the immediate directory
      const baseDir = entry.parentPath ?? targetDir;
      const fullPath = path.join(baseDir, entry.name);
      
      const stats = await fs.stat(fullPath);
      files.push({
        name: entry.name,
        absolutePath: fullPath,
        size: stats.size
      });
    }
  }

  return files;
}
```

---

## 5. File System Monitoring: `fs.watch` vs `fs.watchFile` vs Native Watchers

| Feature | `fs.watch` | `fs.watchFile` | `chokidar` (Library) |
|---|---|---|---|
| **Mechanism** | OS Kernel Notifications (`inotify`, `FSEvents`, `ReadDirectoryChangesW`) | Stat Polling (`fs.stat` interval) | Hybrid OS events with debouncing & fallbacks |
| **CPU / I/O Cost** | Extremely Low (Event-driven) | High (Continuous disk polling) | Low / Optimized |
| **Reliability** | May emit duplicate events; platform discrepancies | Highly consistent across OS | Enterprise-grade consistency |
| **Subdirectories** | Recursive supported on macOS/Windows, partial on Linux | Must attach poll to every single file | Full recursive support everywhere |

### Modern Native Async Watcher (`node:fs/promises`)

```typescript
import { watch } from 'node:fs/promises';

export async function monitorDirectory(dirPath: string, abortSignal: AbortSignal) {
  try {
    const watcher = watch(dirPath, { recursive: true, signal: abortSignal });
    console.log(`[WATCHER] Monitoring started on: ${dirPath}`);

    for await (const event of watcher) {
      console.log(`[EVENT] Type: ${event.eventType} | Filename: ${event.filename}`);
    }
  } catch (error: any) {
    if (error.name === 'AbortError') {
      console.log('[WATCHER] Monitoring stopped gracefully.');
    } else {
      console.error('[WATCHER] Monitoring error:', error);
    }
  }
}
```

---

## 6. Permissions, POSIX Modes & Security

File permissions control access (Read, Write, Execute) for three categories of users: **Owner (User)**, **Group**, and **Others**.

### 6.1 Octal Representation
Each digit is the sum of permissions:
- `4`: Read (`r`)
- `2`: Write (`w`)
- `1`: Execute (`x`)

Common Octal Modes:
- `0o644`: Owner Read/Write (`4+2=6`), Group Read (`4`), Others Read (`4`) — Standard files.
- `0o755`: Owner Read/Write/Exec (`7`), Group Read/Exec (`5`), Others Read/Exec (`5`) — Standard executables and directories.
- `0o600`: Owner Read/Write only (`6`), Group none (`0`), Others none (`0`) — Private keys, secret tokens, configs.

### 6.2 Pre-flight Permission Checks (`fs.access`)
Never use `fs.access()` to check permissions right before calling `fs.open()` or `fs.readFile()`. This introduces a **Time-of-Check to Time-of-Use (TOCTOU)** race condition.

> [!CAUTION]
> **Anti-Pattern (TOCTOU Race Condition)**:
> ```typescript
> // BAD: Another process can delete or alter permissions between access and readFile
> if (await fs.access(filePath, fs.constants.R_OK).then(() => true).catch(() => false)) {
>   const content = await fs.readFile(filePath);
> }
> ```
>
> **Best Practice**:
> Attempt the operation directly and handle permission errors (`EACCES`, `ENOENT`, `EPERM`) in the `catch` block.

# 5) Cryptography and Security Utilities (`node:crypto`)

## Executive Overview
The `node:crypto` module encapsulates OpenSSL cryptographic primitives, providing robust implementations of secure hashing, HMAC generation, password derivation, symmetric authenticated ciphers (AES-GCM), asymmetric public-key cryptography (RSA, ECDSA, Ed25519), and cryptographically secure pseudorandom number generators (CSPRNG).

Security errors in backend services often stem from weak algorithms, hardcoded initialization vectors (IVs), unauthenticated ciphers, and timing side-channel vulnerabilities.

---

## 1. Cryptographic Hashes & HMACs

A cryptographic hash is a one-way mathematical function transforming arbitrary data into a fixed-length digest.

### 1.1 Fast Data Hashing & Streaming Hashes
```typescript
import crypto from 'node:crypto';
import fs from 'node:fs';

// 1. One-shot Hash
const checksum = crypto
  .createHash('sha256')
  .update('sensitive-payload-string', 'utf8')
  .digest('hex');

console.log('SHA-256 Checksum:', checksum);

// 2. High-Performance Streaming Hash (Zero RAM overhead for gigabyte files)
export function computeFileSha256(filePath: string): Promise<string> {
  return new Promise((resolve, reject) => {
    const hash = crypto.createHash('sha256');
    const stream = fs.createReadStream(filePath);

    stream.on('data', (chunk) => hash.update(chunk));
    stream.on('end', () => resolve(hash.digest('hex')));
    stream.on('error', (err) => reject(err));
  });
}
```

### 1.2 Hash-Based Message Authentication Code (HMAC)
HMAC provides message integrity **and** authenticity verification using a shared secret key (e.g. Stripe webhook verification, GitHub webhook signatures).

```typescript
import crypto from 'node:crypto';

export function signWebhookPayload(payload: string, secretKey: string): string {
  return crypto
    .createHmac('sha256', secretKey)
    .update(payload, 'utf8')
    .digest('hex');
}
```

---

## 2. Timing Attack Prevention: `crypto.timingSafeEqual()`

Standard string comparison operators (`===`, `==`, `localeCompare`) use short-circuit evaluation—returning `false` immediately on the first non-matching byte. Attackers can measure response latencies down to nanoseconds to incrementally guess signatures, API keys, or authentication tokens.

```text
Standard Comparison: "secretA" === "secretB"
Match 's' -> Match 'e' -> Match 'c' -> Match 'r' -> Match 'e' -> Match 't' -> Fail 'A' vs 'B' (Takes ~70ns)

Standard Comparison: "xecretA" === "secretB"
Fail 'x' vs 's' (Takes ~10ns) -> Leaks that 1st character was wrong!

timingSafeEqual: Always inspects EVERY byte in constant time regardless of mismatches!
```

```typescript
import crypto from 'node:crypto';

export function verifySignatureConstantTime(providedSig: string, expectedSig: string): boolean {
  const bufA = Buffer.from(providedSig, 'hex');
  const bufB = Buffer.from(expectedSig, 'hex');

  // Buffers MUST have equal byte lengths before calling timingSafeEqual
  if (bufA.length !== bufB.length) {
    return false;
  }

  return crypto.timingSafeEqual(bufA, bufB);
}
```

---

## 3. Password Hashing: `crypto.scrypt` & `crypto.pbkdf2`

> [!CAUTION]
> Never hash passwords using raw SHA-256, SHA-512, or MD5. Modern GPUs can calculate billions of SHA-256 hashes per second. Always use **memory-hard** and **computationally-heavy** Key Derivation Functions (KDF) like `scrypt` or `argon2`.

```typescript
import crypto from 'node:crypto';
import { promisify } from 'node:util';

const scryptAsync = promisify(crypto.scrypt);

export class PasswordHasher {
  private static readonly KEY_LENGTH = 64;
  private static readonly SCRYPT_PARAMS: crypto.ScryptOptions = {
    N: 16384, // CPU/memory cost parameter (must be power of 2)
    r: 8,     // Block size
    p: 1,     // Parallelization parameter
  };

  /**
   * Hashes a plaintext password with a unique CSPRNG salt.
   * Returns format: `salt:derivedKeyHex`
   */
  public static async hashPassword(password: string): Promise<string> {
    const salt = crypto.randomBytes(16).toString('hex');
    const derivedKey = (await scryptAsync(
      password,
      salt,
      this.KEY_LENGTH,
      this.SCRYPT_PARAMS
    )) as Buffer;

    return `${salt}:${derivedKey.toString('hex')}`;
  }

  /**
   * Verifies a candidate password against stored salt and hash in constant time.
   */
  public static async verifyPassword(password: string, storedHash: string): Promise<boolean> {
    const [salt, originalKeyHex] = storedHash.split(':');
    if (!salt || !originalKeyHex) return false;

    const originalKey = Buffer.from(originalKeyHex, 'hex');
    const derivedKey = (await scryptAsync(
      password,
      salt,
      this.KEY_LENGTH,
      this.SCRYPT_PARAMS
    )) as Buffer;

    if (derivedKey.length !== originalKey.length) return false;
    return crypto.timingSafeEqual(derivedKey, originalKey);
  }
}
```

---

## 4. Symmetric Encryption: AES-256-GCM (Authenticated Encryption)

**AES-256-GCM (Galois/Counter Mode)** is the gold standard for symmetric encryption. Unlike legacy CBC mode, GCM provides **Authenticated Encryption with Associated Data (AEAD)**. It generates an **Authentication Tag** (`authTag`) ensuring the ciphertext has not been tampered with or corrupted in transit.

```typescript
import crypto from 'node:crypto';

export interface EncryptedPayload {
  ivHex: string;
  authTagHex: string;
  ciphertextHex: string;
}

export class AesGcmCipher {
  private static readonly ALGORITHM = 'aes-256-gcm';
  private static readonly IV_LENGTH_BYTES = 12; // 96-bit IV recommended for GCM

  /**
   * Encrypts plaintext using a 256-bit (32-byte) secret key.
   */
  public static encrypt(plaintext: string, keyBuffer32: Buffer): EncryptedPayload {
    // 1. Generate a cryptographically secure random Initialization Vector (IV) for EVERY encryption
    const iv = crypto.randomBytes(this.IV_LENGTH_BYTES);

    const cipher = crypto.createCipheriv(this.ALGORITHM, keyBuffer32, iv);
    
    let ciphertext = cipher.update(plaintext, 'utf8', 'hex');
    ciphertext += cipher.final('hex');

    // 2. Extract the 16-byte authentication tag
    const authTag = cipher.getAuthTag();

    return {
      ivHex: iv.toString('hex'),
      authTagHex: authTag.toString('hex'),
      ciphertextHex: ciphertext,
    };
  }

  /**
   * Decrypts and verifies authenticity of ciphertext.
   */
  public static decrypt(payload: EncryptedPayload, keyBuffer32: Buffer): string {
    const iv = Buffer.from(payload.ivHex, 'hex');
    const authTag = Buffer.from(payload.authTagHex, 'hex');

    const decipher = crypto.createDecipheriv(this.ALGORITHM, keyBuffer32, iv);
    decipher.setAuthTag(authTag); // Set expected authentication tag

    let plaintext = decipher.update(payload.ciphertextHex, 'hex', 'utf8');
    // Throws an error if authentication tag does not match (tampering detected)
    plaintext += decipher.final('utf8');

    return plaintext;
  }
}
```

---

## 5. Asymmetric Cryptography: Key Pairs & Digital Signatures

Asymmetric encryption uses a **Public Key** for encryption/verification and a **Private Key** for decryption/signing.

```typescript
import crypto from 'node:crypto';

// 1. Generate Ed25519 (Modern, ultra-fast, high-security curve) Key Pair
export function generateAsymmetricKeys() {
  const { publicKey, privateKey } = crypto.generateKeyPairSync('ed25519', {
    publicKeyEncoding: { type: 'spki', format: 'pem' },
    privateKeyEncoding: { type: 'pkcs8', format: 'pem' },
  });

  return { publicKey, privateKey };
}

// 2. Create Digital Signature with Private Key
export function signData(data: string, privateKeyPem: string): string {
  const sign = crypto.createSign('SHA256');
  sign.update(data);
  sign.end();
  return sign.sign(privateKeyPem, 'hex');
}

// 3. Verify Digital Signature with Public Key
export function verifyDataSignature(data: string, signatureHex: string, publicKeyPem: string): boolean {
  const verify = crypto.createVerify('SHA256');
  verify.update(data);
  verify.end();
  return verify.verify(publicKeyPem, signatureHex, 'hex');
}
```

---

## 6. Cryptographically Secure Random Number Generation (CSPRNG)

Never use `Math.random()` for tokens, session IDs, salts, or passwords. `Math.random()` uses the xoshiro128+ PRNG, which is completely predictable.

```typescript
import crypto from 'node:crypto';

// 1. Secure Random Buffer (Salts, Keys)
const randomKey = crypto.randomBytes(32); // 256 bits of pure entropy

// 2. High-Performance UUID v4
const uuid = crypto.randomUUID(); // e.g. "c9a646d3-9c61-4cd7-bf15-99881bb950cf"

// 3. Secure Random Integer within Range [min, max)
const diceRoll = crypto.randomInt(1, 7); // Generates 1..6 uniformly without modulo bias
```

# Security Policy

## Supported Versions

Security updates are provided for the latest stable release of `json-to-toon`.  
Older versions may not receive security patches or maintenance.

| Version | Supported |
|----------|------------|
| 1.x      | ✅ Yes     |

---

## Reporting a Vulnerability

If you discover a security vulnerability, please **do not create a public issue**.

Instead, contact the maintainer directly via:

**Email:** samuel.txd@gmail.com

Please include:

- A detailed description of the issue.
- Steps to reproduce or proof-of-concept (if applicable).
- Any potential impact or exploit scenario you have identified.

---

## Disclosure Process

1. The maintainer will acknowledge receipt of your report within **48 hours**.
2. You will receive a status update within **5 business days**, including:
   1. Confirmation of the issue and risk classification. 
   2. Expected timeline for a fix or patch release.
3. Once the vulnerability is fixed, we will:
   1. Release a patched version on Packagist and GitHub. 
   2. Credit the reporter (if consent is given). 
   3. Publish a summary in the release notes.

---

## Scope

This policy applies to all source code and published packages within the **json-to-toon** repository.

Out of scope:
- Vulnerabilities in dependencies (report those upstream).
- Issues related to environment configuration or PHP runtime itself.
- Denial-of-service or performance degradation from unrealistic payloads.

---

## Security Best Practices

- Always use the latest version of PHP (8.2 or newer).
- Avoid running untrusted data through `Toon::encode()` without validation.
- Never expose encoded or decoded data directly to the public without context sanitization.
- Use secure Composer dependencies (`composer audit` before deployment).

---

## Responsible Disclosure

We strongly support responsible disclosure.  
If you believe a discovered issue could be severe, please coordinate disclosure with us before publishing any details.  
Your efforts to improve the security of open-source software are deeply appreciated.

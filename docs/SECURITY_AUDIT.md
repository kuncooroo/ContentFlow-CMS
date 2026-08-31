# Security Audit — ContentFlow CMS

| Field | Value |
|---|---|
| **Audit date** | 2026-08-31 |
| **Scope** | Full application (MVP v1.0.0) |
| **Method** | Static code review, configuration review, existing security test suite, `composer audit` |
| **Auditor role** | Senior Application Security Engineer (Laravel) |
| **Related docs** | [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md), [DEPLOYMENT.md](DEPLOYMENT.md), [TROUBLESHOOTING.md](TROUBLESHOOTING.md) |

---

## Executive summary

ContentFlow CMS demonstrates **mature baseline security** for an MVP Laravel CMS: policy-based authorization, CSRF on all web routes, escaped output (no raw Blade), parameterized queries, upload allowlists, login throttling, inactive-user login blocking, audit log redaction, and security headers.

**No CRITICAL application-layer vulnerabilities** were found in production code paths when the installer is locked and production environment variables are set correctly.

The highest residual risks are **deployment/configuration** (open installer window, debug mode, session cookie flags) and **defense-in-depth gaps** (dashboard permission scope, MIME trust on uploads before this audit).

**Fixes applied in this audit:**

| ID | Severity | Fix |
|---|---|---|
| SEC-001 | High | Install POST routes rate-limited |
| SEC-002 | High | Deactivated users lose DB sessions + blocked by middleware |
| SEC-003 | High | Upload MIME verified via `finfo` + `getimagesize()` |
| SEC-004 | Medium | `ForgotPasswordController` defense-in-depth `DemoGuard` |
| SEC-005 | Low | `Users\Create::save()` re-authorizes |

**Dependency scan:** `composer audit` — **0 known advisories** (2026-08-31).

---

## Methodology

1. Reviewed authentication, authorization, input validation, output encoding, file handling, session config, and business logic across `app/`, `routes/`, `config/`, `resources/views/`.
2. Cross-referenced `docs/PRD.md`, `docs/SRS.md`, `CURSOR.md` security rules.
3. Ran existing tests under `tests/Feature/Security/`.
4. Ran `composer audit` for dependency vulnerabilities.

**Out of scope for this MVP (documented as N/A):**

- Multi-tenant isolation (single-tenant product)
- Webhooks (not implemented)
- Public REST/GraphQL API (not implemented)
- Financial transactions / billing (not in MVP)
- WAF / DDoS / CDN edge protection (infrastructure)

---

## Findings index

| ID | Severity | Category | Status |
|---|---|---|---|
| SEC-001 | High | Rate limiting (installer) | **Fixed** |
| SEC-002 | High | Session security (deactivation) | **Fixed** |
| SEC-003 | High | File uploads (MIME bypass) | **Fixed** |
| SEC-004 | Medium | Password handling (demo) | **Fixed** |
| SEC-005 | Low | Authorization (Users create) | **Fixed** |
| SEC-006 | High | Sensitive data (open installer) | Open — operational |
| SEC-007 | Medium | Debug mode defaults | Open — configuration |
| SEC-008 | Medium | Session encryption / secure cookie | Open — configuration |
| SEC-009 | Medium | Authorization (dashboard scope) | Open — design |
| SEC-010 | Medium | Install SSRF (DB host probing) | Open — partial mitigation |
| SEC-011 | Medium | Mass assignment latent risk | Open — hardening |
| SEC-012 | Low | Content-Security-Policy header | Open |
| SEC-013 | Low | Public media disk | Open — by design |
| SEC-014 | Info | XSS posture (positive) | Acceptable |
| SEC-015 | Info | SQL injection posture (positive) | Acceptable |
| SEC-016 | Info | CSRF posture (positive) | Acceptable |

---

## Detailed findings

### SEC-001 — Installer POST endpoints lacked rate limiting

| Field | Detail |
|---|---|
| **Severity** | High |
| **Affected component** | `routes/install.php`, `InstallController` |
| **Attack scenario** | Attacker discovers an unlocked installer (`install.lock` missing) and floods POST `/install/database` with credential probes, `.env` writes, or migration attempts. |
| **Impact** | Resource exhaustion, credential stuffing against MySQL, partial `.env` corruption, denial of service during setup window. |
| **Recommended fix** | Apply `throttle` middleware to all installer POST routes; stricter limit on database step. |
| **Status** | **Fixed** — `throttle:6,1` on database store; `throttle:10,1` on other POST steps. |

---

### SEC-002 — Deactivated users retained valid sessions

| Field | Detail |
|---|---|
| **Severity** | High |
| **Affected component** | `ChangeUserStatus`, admin middleware stack |
| **Attack scenario** | Administrator deactivates a compromised account. Attacker's existing session cookie remains valid until expiry; attacker continues admin access. |
| **Impact** | Privilege retention after intended revocation; violates SRS inactive-user rule beyond login. |
| **Recommended fix** | Delete user's rows from `sessions` table on deactivation; add middleware that logs out inactive users on every authenticated request. |
| **Status** | **Fixed** — `EnsureUserIsActive` on admin routes; `ChangeUserStatus::invalidateUserSessions()`. |

---

### SEC-003 — Upload MIME validation trusted client-reported type

| Field | Detail |
|---|---|
| **Severity** | High |
| **Affected component** | `app/Actions/Media/StoreUploadedMedia.php` |
| **Attack scenario** | Attacker uploads a polyglot or executable file with spoofed `Content-Type` while using an allowed extension; client MIME passes allowlist. |
| **Impact** | Malicious file stored on public disk; potential XSS if served with wrong content-type (mitigated by extension blocklist, but bypass risk remains). |
| **Recommended fix** | Verify MIME with `finfo(FILEINFO_MIME_TYPE)` on file contents; require `getimagesize()` success for image-only allowlist. |
| **Status** | **Fixed** — server-side `finfo` + image structure validation. |

---

### SEC-004 — Forgot-password lacked controller-level demo guard

| Field | Detail |
|---|---|
| **Severity** | Medium |
| **Affected component** | `ForgotPasswordController` |
| **Attack scenario** | Demo middleware removed or mis-ordered; password reset emails sent for protected demo accounts. |
| **Impact** | Demo account takeover vector on misconfigured hosts. |
| **Recommended fix** | Mirror `ResetPasswordController` — call `DemoGuard::assertCanResetPassword()` in controller. |
| **Status** | **Fixed** |

---

### SEC-005 — User create save() missing re-authorization

| Field | Detail |
|---|---|
| **Severity** | Low |
| **Affected component** | `app/Livewire/Admin/Users/Create.php` |
| **Attack scenario** | Livewire component lifecycle edge case bypasses `mount()` authorization. |
| **Impact** | Unauthorized user creation if authorization only in `mount()`. |
| **Recommended fix** | Call `$this->authorize('create', User::class)` in `save()`. |
| **Status** | **Fixed** |

---

### SEC-006 — Unauthenticated installer when lock file absent

| Field | Detail |
|---|---|
| **Severity** | High (conditional on deployment) |
| **Affected component** | `routes/install.php`, `EnsureInstallerUnlocked`, `InstallLock` |
| **Attack scenario** | Production deploy without `storage/app/install.lock`; attacker completes wizard, writes `.env`, runs migrations, creates Super Admin. |
| **Impact** | Full site takeover on fresh/misconfigured host. |
| **Recommended fix** | **Operational:** ensure lock exists before DNS cutover; restrict `/install` by IP or basic auth during setup; remove installer route exposure after go-live monitoring. **Application:** rate limiting (SEC-001), reject DB with existing users (implemented). |
| **Status** | Open — by design for source-code installs; requires deployment discipline. |

---

### SEC-007 — Debug mode enabled in environment template

| Field | Detail |
|---|---|
| **Severity** | Medium |
| **Affected component** | `.env.example`, deployment process |
| **Attack scenario** | Production copy-paste from `.env.example` leaves `APP_DEBUG=true`. |
| **Impact** | Stack traces, environment details, and query information exposed to attackers. |
| **Recommended fix** | Set `APP_DEBUG=false` in production `.env`; document in [DEPLOYMENT.md](DEPLOYMENT.md); add release checklist verification. Runtime default in `config/app.php` is already `false` when env unset. |
| **Status** | Open — configuration |

---

### SEC-008 — Session cookie hardening not enforced in template

| Field | Detail |
|---|---|
| **Severity** | Medium |
| **Affected component** | `config/session.php`, `.env.example` |
| **Attack scenario** | Production runs without `SESSION_SECURE_COOKIE=true` over HTTPS, or stores session payloads unencrypted in DB (`SESSION_ENCRYPT=false`). |
| **Impact** | Session cookie interception on mixed HTTP; readable session payloads if database compromised. |
| **Recommended fix** | Production: `SESSION_SECURE_COOKIE=true`, consider `SESSION_ENCRYPT=true`, `SESSION_SAME_SITE=lax` (default OK). |
| **Status** | Open — configuration |

---

### SEC-009 — Dashboard accessible to any authenticated user

| Field | Detail |
|---|---|
| **Severity** | Medium |
| **Affected component** | `routes/admin.php`, `Dashboard\Overview` Livewire |
| **Attack scenario** | Author authenticates and accesses `/admin/dashboard` without module-specific permission. |
| **Impact** | Information disclosure (aggregate counts); broader admin surface than role matrix implies (individual modules still policy-protected). |
| **Recommended fix** | Add dashboard-specific permission or `$this->authorize()` in Overview component; or redirect Authors to first permitted module. |
| **Status** | Open — design decision |

---

### SEC-010 — Installer database host SSRF / internal probing

| Field | Detail |
|---|---|
| **Severity** | Medium |
| **Affected component** | `InstallDatabaseTester`, `InstallController` |
| **Attack scenario** | During unlocked install, attacker supplies `host=169.254.169.254` or internal hostname to probe cloud metadata or internal MySQL. |
| **Impact** | Network reconnaissance from application server; potential credential leakage if internal DB accepts connection. |
| **Recommended fix** | Block private/reserved IP ranges and link-local addresses; optional allowlist for install phase. Semicolon/newline injection mitigated (prior fix). |
| **Status** | Open — partial mitigation |

---

### SEC-011 — Sensitive fields in `$fillable` (latent mass assignment)

| Field | Detail |
|---|---|
| **Severity** | Medium (latent) |
| **Affected component** | `User`, `Post`, `Page`, `Media`, `Role`, `Comment` models |
| **Attack scenario** | Future endpoint passes broad validated input to `Model::create()` / `update()`. |
| **Impact** | Status escalation, author reassignment, storage path tampering, system role flag manipulation. |
| **Recommended fix** | Continue explicit field assignment in Actions (current pattern). Consider removing `status`, `author_id`, `disk`, `path`, `is_system` from `$fillable` and set via dedicated methods. |
| **Status** | Open — no current exploit path found |

---

### SEC-012 — No Content-Security-Policy header

| Field | Detail |
|---|---|
| **Severity** | Low |
| **Affected component** | `SecurityHeaders` middleware |
| **Attack scenario** | XSS via future template mistake or third-party script injection. |
| **Impact** | Reduced defense-in-depth against inline script execution. |
| **Recommended fix** | Add restrictive CSP compatible with Vite/Livewire; start with report-only mode. |
| **Status** | Open |

---

### SEC-013 — Media stored on public disk by default

| Field | Detail |
|---|---|
| **Severity** | Low |
| **Affected component** | `config/media.php`, `config/filesystems.php` |
| **Attack scenario** | Any uploaded image is directly URL-accessible under `/storage`. |
| **Impact** | Expected for public CMS media; risk if upload validation ever fails. |
| **Recommended fix** | Keep strict upload validation (SEC-003); for sensitive assets use private disk + authorized download route. |
| **Status** | Open — by design for MVP |

---

## Category assessments

### Authentication — **Strong**

| Control | Status |
|---|---|
| Password hashing (`hashed` cast) | ✅ |
| Login rate limit (route + `LoginRequest`) | ✅ |
| Inactive users blocked at login | ✅ |
| Session regeneration on login | ✅ |
| Logout invalidates session + CSRF token | ✅ |
| Password reset anti-enumeration | ✅ |
| Inactive users blocked mid-session | ✅ (fixed SEC-002) |

### Authorization — **Strong with gaps**

| Control | Status |
|---|---|
| Policies on Livewire mount/actions | ✅ |
| Post Author ownership in `PostPolicy` | ✅ |
| Super Admin protection rules | ✅ |
| Preview controllers authorize | ✅ |
| Dashboard global auth-only access | ⚠️ SEC-009 |
| Media library not uploader-scoped | By design (role-based) |

### IDOR — **Mitigated**

- Admin resources use route model binding + policy checks on mount and mutations.
- Public routes use slug binding + `isPubliclyVisible()` / published-only comment submission.
- No direct object reference bypass found in reviewed paths.

### Mass assignment — **Controlled (latent risk SEC-011)**

- All models use `$fillable`; no `$request->all()` → model patterns in `app/`.
- Actions assign fields explicitly.

### SQL injection — **Not vulnerable**

- Eloquent/query builder with bindings throughout `app/`.
- `SearchTerm::likePattern()` escapes LIKE wildcards.
- Raw SQL limited to static migration statements.

### XSS — **Mitigated**

- Zero `{!! !!}` in application views.
- `RichContentPolicy` enforces plain escaped text.
- Comments, posts, SEO meta use `{{ }}`.
- Menu URLs block `javascript:` / `data:` schemes.
- Regression tests in `tests/Feature/Security/XssOutputTest.php`.

### CSRF — **Mitigated**

- All routes under `web` middleware group.
- Forms include `@csrf`; layouts expose CSRF meta token.
- Tests confirm 419 without token.

### File uploads — **Strong (after SEC-003 fix)**

| Control | Status |
|---|---|
| Extension allowlist + blocklist | ✅ |
| Double-extension rejection | ✅ |
| UUID filenames (no path traversal) | ✅ |
| Size limit | ✅ |
| Authorization via `MediaPolicy` | ✅ |
| Server-side MIME + image validation | ✅ (fixed) |

### Path traversal — **Not vulnerable**

- Storage paths generated server-side; user input not concatenated into paths.

### Rate limiting — **Adequate**

| Endpoint | Limit |
|---|---|
| Login POST | 6/min (route) + 5 attempts (LoginRequest) |
| Forgot / reset password | 6/min |
| Public comments | 5/min |
| Installer POST | 6–10/min (fixed SEC-001) |

### Brute force protection — **Adequate**

- Login and password reset throttled.
- Password reset broker throttle: 60s (`config/auth.php`).

### Password handling — **Strong**

- `Password::defaults()` on install, reset, admin forms.
- `BCRYPT_ROUNDS=12` in `.env.example`.
- Demo password reset restrictions.
- Install wizard encrypts admin password in session between steps.

### Session security — **Adequate (config-dependent)**

- Database sessions recommended; `http_only` default true; `same_site=lax`.
- Production hardening depends on SEC-008 env values.

### Sensitive data exposure — **Controlled**

| Item | Status |
|---|---|
| `.env` gitignored | ✅ |
| User `$hidden` for password/token | ✅ |
| Audit logger strips sensitive keys | ✅ |
| Mail failure log redaction | ✅ |
| Password reset no email enumeration | ✅ |
| Custom 404 without stack trace (when debug off) | ✅ |

### Environment configuration — **Good pattern**

- Zero `env()` calls in `app/` — configuration via `config/*.php` only.
- Demo production safety guard on boot.

### Debug mode — **Config risk SEC-007**

- `config/app.php` defaults `APP_DEBUG` to `false`.
- `.env.example` ships `APP_DEBUG=true` for local dev.

### API security — **N/A**

- No public API endpoints in MVP.

### Webhook security — **N/A**

- Not implemented per PRD/SRS.

### Business logic vulnerabilities — **Reviewed**

| Rule | Status |
|---|---|
| Inactive users cannot authenticate | ✅ |
| Last Super Admin cannot be deactivated | ✅ |
| Draft/scheduled content not public | ✅ |
| Only approved comments public | ✅ |
| Author cannot publish by default | ✅ (permission matrix) |
| Referenced media deletion rules | ✅ (Actions enforce) |
| Demo mode restrictions | ✅ |

### Privilege escalation — **Mitigated**

- Role assignment requires `UsersManage` permission.
- Super Admin role assignable by Administrators with that permission (RBAC design).
- No horizontal escalation found for Author editing others' posts (policy + query scope).

### Tenant isolation — **N/A**

- Single-tenant CMS; no tenant tables.

### Financial transaction integrity — **N/A**

- No payment/billing in MVP.

### Logging of sensitive data — **Strong**

- `ActivityLogger` filters password/token/secret keys.
- `LogMailDeliveryFailure` redacts sensitive substrings.
- No password logging observed in install/auth flows.

### Backup exposure — **Operational**

| Risk | Mitigation |
|---|---|
| Database dumps contain password hashes, session rows | Encrypt backups at rest; restrict access |
| `storage/` includes uploads and install lock | Exclude dev `install.lock` from packages (documented in RELEASE.md) |
| `.env` in backups | Never commit; secure backup storage |

### Dependency vulnerabilities — **Clean**

```
composer audit (2026-08-31): 0 advisories
```

Re-run before each release: `composer audit`

---

## Positive security controls (reference)

| Area | Implementation |
|---|---|
| Security headers | `SecurityHeaders` — X-Frame-Options, X-Content-Type-Options, Referrer-Policy, HSTS on HTTPS |
| Demo safety | Production boot failure when `DEMO_MODE` + `APP_ENV=production` without override |
| Install lock | 404 when locked; secrets not re-displayed |
| CSRF | Laravel web stack on all routes |
| Upload tests | `UploadSecurityTest` — double extension, blocked types |
| Authorization tests | `AdminAuthorizationMatrixTest`, policy feature tests |

---

## Verification

```bash
# Security-focused tests
php artisan test --filter=Security

# Auth & authorization
php artisan test tests/Feature/Auth
php artisan test tests/Feature/Authorization

# Dependencies
composer audit
```

**Pre-production checklist:**

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] `SESSION_SECURE_COOKIE=true` (HTTPS)
- [ ] `storage/app/install.lock` exists
- [ ] Web root = `public/` only
- [ ] `DEMO_MODE=false` on customer sites
- [ ] `composer audit` clean
- [ ] Full test suite green

---

## Revision history

| Date | Change |
|---|---|
| 2026-08-31 | Initial audit; SEC-001 through SEC-005 fixed |

# Security Checklist — ContentFlow CMS MVP

Signed off as part of **TASK-022** (Security Hardening). See [SECURITY_AUDIT.md](SECURITY_AUDIT.md) for full findings. Re-run before each production release.

## Authentication & sessions

- [x] Login rate limiting (`LoginRequest` + route throttle on auth POST routes)
- [x] Inactive users cannot authenticate
- [x] Deactivated users are logged out on next request (`EnsureUserIsActive`)
- [x] Database sessions purged when user is deactivated
- [x] Session regenerated on successful login
- [x] Logout invalidates session and regenerates CSRF token
- [x] Password reset uses queued notification; generic responses (no email enumeration)
- [ ] Production: `SESSION_SECURE_COOKIE=true` when serving over HTTPS
- [ ] Production: `APP_DEBUG=false`

## Authorization

- [x] Livewire admin actions call `$this->authorize()` with policies
- [x] Role/permission matrix seeded; negative tests for major modules
- [x] Super Admin protections tested
- [x] Unauthorized routes return 403 (not only hidden UI)

## CSRF

- [x] All state-changing web forms include `@csrf`
- [x] Admin and public layouts expose `csrf-token` meta for JS clients
- [x] CSRF failure returns 419 (tested)

## XSS & output

- [x] No `{!! !!}` in application views (audit: none found)
- [x] Rich content policy: **plain escaped text only** (`RichContentPolicy`, `<x-rich-content>`)
- [x] Comments rendered with Blade escaping
- [x] XSS regression tests for post titles and comment bodies

## Uploads

- [x] MIME + extension allowlist (`config/media.php`)
- [x] Blocked executable/script extensions (php, svg, html, js, etc.)
- [x] Double-extension filenames rejected (e.g. `shell.php.jpg`)
- [x] Max upload size enforced
- [x] Upload authorization via `MediaPolicy`

## Transport & headers

- [x] `SecurityHeaders` middleware: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`
- [x] HSTS header when request is secure
- [ ] Production: terminate TLS at load balancer or web server; enforce HTTPS redirect
- [ ] Production: web server document root must be `public/` only

## Secrets & logging

- [x] `.env` not committed; `.env.example` has no real secrets
- [x] Audit logger strips passwords/tokens from properties
- [x] Mail failure logs redact sensitive substrings
- [ ] Rotate `APP_KEY` if compromised; never log SMTP/API credentials

## Queue & errors

- [x] Failed jobs stored in `failed_jobs` (`docs/QUEUE_OPERATIONS.md`)
- [x] Custom 404 page (no stack traces when `APP_DEBUG=false`)
- [ ] Monitor `storage/logs/laravel.log` and failed queue jobs in production

## Verification commands

```bash
php artisan test --filter=Security
php artisan test tests/Feature/Auth
php artisan test tests/Feature/Authorization
php artisan test tests/Feature/Admin/Media
```

## Out of scope (MVP)

- In-app notification center
- WAF / DDoS protection
- HTML Purifier for rich text (future phase; currently plain escaped only)
- Two-factor authentication

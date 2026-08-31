# TASK-028 — Installer Enhancements

| Field | Value |
|---|---|
| **Task ID** | TASK-028 |
| **Title** | Installer Enhancements & Hardening |
| **Phase** | Post TASK-024 / Pre-release hardening |
| **Estimate focus** | Close gaps in `docs/INSTALLER.md` spec |
| **Depends on** | TASK-024 (core wizard), TASK-025 (DemoSeeder), TASK-026 (docs) |

---

## Objective

Complete the commercial installer specification in `docs/INSTALLER.md`: optional demo seeding, post-install hardening, recovery UX, and security controls so the installer **cannot remain exploitable after installation**.

---

## Background

TASK-024 delivered the core 6-step wizard and lock file. This task closes remaining product gaps identified in installer, security, and performance audits without rewriting the working flow.

---

## Subtasks

### TASK-028-A — Requirements: `storage/app` writability

**Priority:** P1

**Files:**
- `app/Support/Install/InstallRequirements.php`
- `resources/views/install/requirements.blade.php` (if messaging needed)
- `tests/Feature/Install/InstallWizardFlowTest.php`

**Acceptance:**
- [ ] Requirement check verifies `storage/app` exists and is writable (lock file target)
- [ ] Test covers failed check blocks advance

---

### TASK-028-B — Optional demo data step

**Priority:** P1

**Files:**
- `resources/views/install/settings.blade.php`
- `app/Http/Controllers/Install/InstallController.php`
- `app/Support/Install/InstallService.php`
- `tests/Feature/Install/InstallWizardTest.php`

**Behavior:**
- Checkbox: “Seed demo content (for staging/demo hosts only)”
- When checked during finalize:
  - Set `DEMO_MODE=true` in `.env` (only if not production, or require explicit confirm copy)
  - Run `DemoSeeder` inside same transaction boundary as admin creation **or** immediately after commit with rollback documentation
- When unchecked: ensure `DEMO_MODE=false` (default)

**Acceptance:**
- [ ] Demo opt-in creates demo users/posts when selected
- [ ] Demo opt-out leaves production-clean database
- [ ] Wizard never enables demo on production without explicit operator acknowledgment (copy + env guard)

---

### TASK-028-C — Run `storage:link` during finalize

**Priority:** P1

**Files:**
- `app/Support/Install/InstallService.php`
- `tests/Feature/Install/InstallWizardTest.php`

**Behavior:**
- After successful finalize, run `Artisan::call('storage:link')` if link missing
- Surface non-fatal warning on complete page if symlink cannot be created (Windows/shared hosting)

**Acceptance:**
- [ ] Fresh install on Linux creates `public/storage` link
- [ ] Failure does not falsely report install success if media URLs would break (document operator fallback)

---

### TASK-028-D — Production-safe environment defaults on finalize

**Priority:** P1

**Files:**
- `app/Support/Install/InstallService.php`
- `InstallEnvironmentWriter.php`
- `tests/Feature/Install/InstallWizardTest.php`

**Behavior:**
- When `APP_URL` is not localhost, write:
  - `APP_DEBUG=false`
  - `LOG_LEVEL=warning` (optional)
- Do not override explicit operator choices on local dev URLs

**Acceptance:**
- [ ] Production-like URL results in `APP_DEBUG=false` in `.env`
- [ ] Local `http://localhost` installs remain debug-friendly

---

### TASK-028-E — Locale validation alignment

**Priority:** P2

**Files:**
- `app/Http/Controllers/Install/InstallController.php`
- `resources/views/install/settings.blade.php`
- `tests/Feature/Install/InstallWizardTest.php`

**Behavior:**
- Match `UpdateSiteSettings` locale rule: `regex:/^[a-z]{2}(_[A-Z]{2})?$/`
- Optional: locale select instead of free text

**Acceptance:**
- [ ] Invalid locale rejected at install settings step

---

### TASK-028-F — Partial install recovery UX

**Priority:** P2

**Files:**
- `app/Support/Install/InstallService.php`
- `app/Http/Controllers/Install/InstallController.php`
- `docs/INSTALLATION.md`, `docs/TROUBLESHOOTING.md`

**Behavior:**
- If Super Admin already exists but lock missing, show guided recovery (manual lock + login) instead of duplicate-email dead end
- Document clearly in operator docs

**Acceptance:**
- [ ] Admin-created-but-unlocked state has documented recovery path
- [ ] Test or documented manual procedure for support

---

### TASK-028-G — Installer SSRF hardening (DB host)

**Priority:** P2

**Files:**
- `app/Support/Install/InstallDatabaseTester.php`
- `tests/Unit/Support/Install/InstallDatabaseTesterTest.php` (new)

**Behavior:**
- Reject private/reserved IP ranges and link-local hosts for `DB_HOST` during install window
- Allow `127.0.0.1` and `localhost` for local installs

**Acceptance:**
- [ ] `169.254.x.x`, `10.x.x.x`, etc. rejected with clear message
- [ ] Local dev hosts still work

---

### TASK-028-H — Installer security test pack

**Priority:** P1

**Files:**
- `tests/Feature/Install/InstallRateLimitTest.php` (new)
- `tests/Feature/Install/InstallSessionRecoveryTest.php` (new)
- `docs/TESTING.md` (coverage map update)

**Tests:**
- [ ] POST `/install/database` returns 429 after throttle threshold
- [ ] Locked installer returns 404 for all wizard routes
- [ ] Tampered administrator session password redirects with error (DecryptException path)

---

### TASK-028-I — Documentation sync

**Priority:** P2

**Files:**
- `docs/INSTALLATION.md`
- `docs/DEPLOYMENT.md`
- `docs/README.md`

**Acceptance:**
- [ ] Operator docs reference `docs/INSTALLER.md` for architecture
- [ ] Post-install security checklist includes lock verification
- [ ] Demo opt-in documented separately from production path

---

## Security checklist (must pass)

- [ ] All wizard POST routes remain rate-limited
- [ ] Lock written only after successful finalize
- [ ] Wizard routes return 404 when locked (no stack traces)
- [ ] Secrets never re-displayed
- [ ] CSRF enforced on all steps
- [ ] Release packages exclude `storage/app/install.lock`
- [ ] Optional: web server IP restriction documented for install window

---

## Definition of Done

- All P1 subtasks complete with tests
- `docs/INSTALLER.md` status table updated to ✅ for implemented items
- `php artisan test --filter=Install` green on clean database
- Security audit SEC-006 mitigations documented and verified

---

## Out of scope

- Composer/npm execution inside wizard
- Multi-tenant install profiles
- Remote install API
- Automatic SSL certificate provisioning

# Installer System Design — ContentFlow CMS

Product engineering specification for the commercial source-code installation wizard.

| Field | Value |
|---|---|
| **Audience** | Product engineers, implementers, QA, deployment ops |
| **User guide** | [INSTALLATION.md](INSTALLATION.md) — operator-facing setup steps |
| **Security** | [SECURITY_AUDIT.md](SECURITY_AUDIT.md) — SEC-006 installer exposure |
| **Task baseline** | TASK-024 (core wizard implemented) |
| **Lock file** | `storage/app/install.lock` |

---

## 1. Purpose

ContentFlow CMS ships as **source code**, not a one-click SaaS. Customers deploy on their own VPS or shared hosting. The installer removes manual Artisan tribal knowledge while preserving:

- Repeatable fresh installs
- Safe handling of secrets
- Clear failure modes
- **Permanent lockout of install routes after completion**

Manual installation (`docs/INSTALLATION.md` Option B) remains supported for advanced operators and CI.

---

## 2. Design principles

| Principle | Rationale |
|---|---|
| **Wizard, not runtime module** | Installer is a bootstrap path, not a permanent admin feature |
| **Fail closed** | Migration/seed/admin failures never report success |
| **Secrets write-once** | DB and admin passwords are not re-displayed after save |
| **Lock is authoritative** | When locked, wizard routes are inaccessible (404) |
| **Idempotent seeds** | Role/permission and settings seeders safe to re-run |
| **No post-install exploit surface** | Rate limits, CSRF, lock file, and route middleware combined |

---

## 3. Preconditions (before `/install`)

These steps happen **outside** the wizard and are documented for operators:

| Step | Command / action | Required |
|---|---|---|
| PHP dependencies | `composer install` | Yes |
| Frontend assets | `npm install && npm run build` | Yes (admin/public UI) |
| Web document root | Point vhost to `public/` | Yes |
| `.env.example` present | Shipped in repo | Yes |
| MySQL database created | Empty database on server | Yes (wizard can test connection) |

The wizard does **not** run Composer or npm. It assumes a deployable Laravel tree.

---

## 4. Installation flow

```text
┌─────────────────┐
│  Requirements   │  PHP version, extensions, writable paths, .env writable
└────────┬────────┘
         ▼
┌─────────────────┐
│  Application    │  APP_NAME, APP_URL → .env; ensure APP_KEY
└────────┬────────┘
         ▼
┌─────────────────┐
│  Database       │  Test connection → write DB_* → migrate → seed baseline
└────────┬────────┘
         ▼
┌─────────────────┐
│  Administrator  │  Super Admin name/email/password (session-encrypted until finalize)
└────────┬────────┘
         ▼
┌─────────────────┐
│  Settings       │  Site name, timezone, locale; optional demo data
└────────┬────────┘
         ▼
┌─────────────────┐
│  Finalize       │  Create admin + settings (transaction) → lock → complete page
└─────────────────┘
```

### Step 1 — System requirements

**Checks:**

| Check | Rule |
|---|---|
| PHP version | ≥ 8.3.0 (8.4 recommended) |
| Extensions | `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo` |
| Writable paths | `storage/`, `bootstrap/cache/` |
| Environment file | `.env` writable or creatable from `.env.example` |

**Recommended additions (not yet implemented):**

- `storage/app/` writable (lock file target)
- Optional: `public/storage` symlink existence or ability to create via `storage:link`

**Gate:** User cannot advance until all checks pass.

**Implementation:** `App\Support\Install\InstallRequirements`

---

### Step 2 — Application / environment configuration

**Collects:**

| Field | Env key | Validation |
|---|---|---|
| Application name | `APP_NAME` | Required, max 150, no newlines |
| Application URL | `APP_URL` | Required, valid URL |

**Actions:**

1. Write values to `.env` via `InstallEnvironmentWriter`
2. Generate `APP_KEY` if empty (`base64:` + 32 random bytes)
3. Run `config:clear`

**Security:** Reject newline characters in env values to prevent `.env` corruption.

**Implementation:** `InstallService::saveApplicationConfiguration()`, `InstallEnvironmentWriter`

---

### Step 3 — Database connection and schema

**Collects:**

| Field | Env key | Validation |
|---|---|---|
| Host | `DB_HOST` | Required; no `;` or control chars |
| Port | `DB_PORT` | 1–65535 |
| Database | `DB_DATABASE` | Required; safe identifier chars |
| Username | `DB_USERNAME` | Required |
| Password | `DB_PASSWORD` | Optional |

**Actions:**

1. Test PDO connection (`InstallDatabaseTester`) **before** persisting
2. Write DB credentials to `.env`
3. Purge/rebind DB connection
4. Run `php artisan migrate --force`
5. Seed baseline:
   - `RolePermissionSeeder` (idempotent)
   - `SiteSettingsSeeder` (singleton row)

**Guards:**

- Reject install if `users` table exists **and** contains rows (non-empty DB)
- Migration exception → user-safe error, no success redirect
- Failed migration ≠ lock written

**Implementation:** `InstallService::configureDatabase()`, `migrateAndSeed()`

---

### Step 4 — Initial administrator

**Collects:**

| Field | Validation |
|---|---|
| Name | Required, max 255 |
| Email | Required, valid email |
| Password | Required, confirmed, `Password::defaults()` |

**Actions:**

- Store name/email in session
- Store password as `Crypt::encryptString()` in session (not plain text)
- Do **not** create user until finalize (allows settings step first)

**Implementation:** `InstallController::storeAdministrator()`

---

### Step 5 — Basic application settings (+ optional demo)

**Collects:**

| Field | Validation |
|---|---|
| Site name | Required, max 150 |
| Timezone | Required, `timezone_identifiers_list()` |
| Locale | Required; recommend `regex:/^[a-z]{2}(_[A-Z]{2})?$/` |
| Seed demo data | Optional checkbox ( **planned** — not in current wizard) |

**Finalize actions (atomic where possible):**

1. `DB::transaction`:
   - Create Super Admin user (`UserStatus::Active`)
   - Attach Super Admin role
   - Update `site_settings` singleton
2. Optional: run `DemoSeeder` when demo checkbox selected ( **planned** )
3. Optional: run `php artisan storage:link` ( **planned** )
4. Optional: set production-safe env defaults (`APP_DEBUG=false`) when appropriate ( **planned** )
5. Write install lock file
6. Clear install session keys
7. Redirect to `/install/complete`

**Implementation:** `InstallService::finalizeInstallation()`, `InstallController::storeSettings()`

---

### Step 6 — Installation complete

**Behavior:**

- `/install/complete` accessible when lock exists **or** session flag set once after finalize
- Shows success message and link to `/login`
- Wizard POST/GET routes return **404** when locked

**Implementation:** `InstallController::complete()`, `InstallLock`

---

## 5. Installer lock

### Mechanism

| Property | Value |
|---|---|
| Path | `storage/app/install.lock` |
| Disk | Laravel `local` (`storage/app/`) |
| Content | `installed_at=<ISO8601 timestamp>` |
| Check | `InstallLock::isLocked()` |

### Enforcement layers

| Layer | Behavior |
|---|---|
| `EnsureInstallerUnlocked` middleware | Returns empty **404** on all wizard routes when locked |
| `InstallService::migrateAndSeed()` | Rejects non-empty user database |
| `InstallService::finalizeInstallation()` | Writes lock only after successful admin + settings |
| Deployment | Do not ship `install.lock` in release packages (`docs/RELEASE.md`) |

### Re-install policy

Re-install is **intentional and destructive**:

1. Back up database and files
2. Use empty/fresh database
3. Remove `storage/app/install.lock`
4. Open `/install`

Never remove lock on production without understanding data loss risk.

---

## 6. Security model

**Primary requirement:** *The installer must not remain exploitable after installation.*

### Threat: Unauthenticated site takeover via open installer

| Control | Status |
|---|---|
| Lock file blocks wizard when installed | ✅ Implemented |
| 404 response (no information leak) | ✅ Implemented |
| CSRF on all POST steps | ✅ `web` middleware |
| Rate limiting on POST routes | ✅ `throttle:6,1` (DB), `10,1` (others) |
| DB credential injection hardening | ✅ Host/user/db validation |
| Secrets not echoed in forms | ✅ Password fields excluded from `old()` |
| Admin password encrypted in session | ✅ `Crypt::encryptString` |
| No password logging | ✅ |
| Non-empty DB guard | ✅ |
| Atomic finalize (admin + settings) | ✅ `DB::transaction` |

### Residual risks and mitigations

| Risk | Severity | Mitigation |
|---|---|---|
| Lock file deleted or never written | High | Deployment checklist; monitor for `/install` 200 responses post-go-live |
| Installer exposed during DNS cutover window | High | IP allowlist / basic auth at web server until lock confirmed |
| Partial install leaves DB populated, wizard unlocked | Medium | Document recovery; improve retry UX (TASK-028) |
| SSRF via DB host during install window | Medium | Block private/reserved IPs (TASK-028) |
| `.env` world-readable | Medium | Document `chmod 640` in DEPLOYMENT.md |

### Post-install verification

```bash
# Should return 404 when locked
curl -I https://example.com/install

# Lock file exists
test -f storage/app/install.lock && echo OK
```

---

## 7. Architecture

```text
routes/install.php
    └── EnsureInstallerUnlocked (404 if locked)
            └── InstallController (thin)
                    ├── InstallRequirements
                    ├── InstallService
                    │     ├── InstallEnvironmentWriter
                    │     ├── InstallDatabaseTester
                    │     └── InstallLock
                    └── Session step gating
```

| Class | Responsibility |
|---|---|
| `InstallController` | HTTP wizard, validation, session flow |
| `InstallRequirements` | Environment preflight checks |
| `InstallEnvironmentWriter` | Safe `.env` read/modify/write |
| `InstallDatabaseTester` | PDO connection test |
| `InstallService` | Orchestration: env, migrate, seed, admin, settings, lock |
| `InstallLock` | Lock file create/check |
| `EnsureInstallerUnlocked` | Middleware gate |

**Registration:** `bootstrap/app.php` loads `routes/install.php` under `web` middleware.

---

## 8. Session state machine

| Session key | Set after step |
|---|---|
| `install.requirements_passed` | Requirements POST |
| `install.application` | Application POST |
| `install.database_configured` | Database POST (migrate success) |
| `install.administrator` | Administrator POST |
| `install.administrator_password` | Administrator POST (encrypted) |
| `install.completed` | Settings POST (cleared on complete page view) |

Each GET step redirects to earliest incomplete step if session chain broken.

---

## 9. Optional demo data

**Product intent:** Demo content is optional at install time for sales/staging hosts, never default on customer production.

| Mode | Behavior |
|---|---|
| Production customer | Demo checkbox **unchecked**; `DEMO_MODE=false` in `.env` |
| Demo/staging host | User opts in → run `DemoSeeder` during finalize; set `DEMO_MODE=true` |

**Current state:** Demo seeding is **manual** post-install (`php artisan db:seed --class=DemoSeeder`). Wizard integration is **planned** (TASK-028).

---

## 10. Comparison: wizard vs manual install

| Step | Wizard | Manual |
|---|---|---|
| Requirements check | Automatic | Operator responsibility |
| `.env` / APP_KEY | Written by wizard | `cp .env.example .env` + `key:generate` |
| DB migrate/seed | Automatic | `migrate` + seeders |
| Super Admin | Wizard form | Tinker or custom script |
| Site settings | Wizard form | Admin UI or seeder defaults |
| Demo data | Planned optional | `DemoSeeder` |
| Lock | Automatic | Create `install.lock` manually or run wizard once |
| `storage:link` | Planned | `php artisan storage:link` |

---

## 11. Testing requirements

| Test | Location |
|---|---|
| Happy path full install | `tests/Feature/Install/InstallWizardTest.php` |
| Invalid DB credentials | `tests/Feature/Install/InstallWizardFlowTest.php` |
| Migration failure | `tests/Feature/Install/InstallWizardFlowTest.php` |
| Lock blocks wizard | `tests/Feature/Install/InstallAccessTest.php` |
| Secrets not re-shown | `tests/Feature/Install/InstallWizardTest.php` |
| Duplicate install rejected | `tests/Feature/Install/InstallWizardTest.php` |
| Lock unit tests | `tests/Unit/Support/Install/InstallLockTest.php` |

**Planned tests (TASK-028):** rate-limit behavior, session expiry recovery, optional demo seed path, `storage:link` step.

---

## 12. Implementation status

| Capability | Status | Notes |
|---|---|---|
| System requirement check | ✅ Done | TASK-024 |
| PHP extension check | ✅ Done | |
| Folder permission check | ✅ Partial | Add `storage/app` check |
| Database connection setup | ✅ Done | |
| Environment configuration | ✅ Done | |
| Application key generation | ✅ Done | |
| Database migrations | ✅ Done | |
| Initial administrator | ✅ Done | |
| Basic application settings | ✅ Done | |
| Optional demo data | ⬜ Planned | TASK-028 |
| Installation completion | ✅ Done | |
| Installer lock | ✅ Done | |
| Rate limiting | ✅ Done | Security hardening |
| Atomic finalize | ✅ Done | Transaction + lock after commit |
| `storage:link` in wizard | ⬜ Planned | TASK-028 |
| Production env defaults on finalize | ⬜ Planned | TASK-028 |

---

## 13. Related documentation

- [INSTALLATION.md](INSTALLATION.md) — operator setup guide
- [DEPLOYMENT.md](DEPLOYMENT.md) — production VPS configuration
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) — installer 404, migration failures
- [RELEASE.md](RELEASE.md) — package must exclude dev `install.lock`
- [SECURITY_AUDIT.md](SECURITY_AUDIT.md) — installer exposure SEC-006

---

## 14. Revision history

| Date | Change |
|---|---|
| 2026-08-31 | Initial installer system design document |

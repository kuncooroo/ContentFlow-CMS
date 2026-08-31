# Demo Mode — ContentFlow CMS

Product architecture for a **secure public demo** that lets prospects explore the CMS while blocking destructive baseline changes.

| Field | Value |
|---|---|
| **Audience** | Product, sales engineering, ops, QA |
| **Operator guide** | [INSTALLATION.md](INSTALLATION.md#demo-environment), [DEPLOYMENT.md](DEPLOYMENT.md) |
| **Implementation** | TASK-025; enhancements in TASK-028-B |
| **Config** | `config/demo.php`, `.env` `DEMO_MODE` |

---

## 1. Purpose

Commercial source-code products need a **safe, resettable sandbox** where buyers can:

- Sign in as different roles (Super Admin, Administrator, Editor)
- Create and publish content, moderate comments, explore media and admin UX
- See the public site with realistic sample data

Without allowing them to:

- Break the permission matrix or system roles
- Change site-wide configuration permanently
- Hijack demo accounts via password reset
- Mistakenly run demo mode on customer production sites

Demo mode is **opt-in** via `DEMO_MODE=true`. Customer installs default to `DEMO_MODE=false` with zero runtime overhead.

---

## 2. Design principles

| Principle | Rationale |
|---|---|
| **Visible** | Persistent banner on admin and public layouts |
| **Read-mostly baseline** | Protect accounts, roles, and settings; allow editorial play |
| **Resettable** | `demo:reset` restores known baseline |
| **Additive security** | Demo guards sit **on top of** policies, never replace them |
| **Production-safe default** | Boot fails if demo enabled in production without explicit override |
| **No billing/SaaS** | Demo is not a tenant or payment sandbox (out of MVP scope) |

---

## 3. Activation

### Environment

```env
# Demo host only
DEMO_MODE=true

# Required only when APP_ENV=production on an intentional public demo VM
DEMO_ALLOW_IN_PRODUCTION=true
```

| Variable | Default | Purpose |
|---|---|---|
| `DEMO_MODE` | `false` | Master switch |
| `DEMO_ALLOW_IN_PRODUCTION` | `false` | Override boot guard for dedicated demo servers |

### Boot guard

When `DEMO_MODE=true` and `APP_ENV=production` without override, the application **refuses to boot** (`DemoGuard::ensureSafeConfiguration()` in `AppServiceProvider`).

### Seeding

```bash
php artisan db:seed --class=DemoSeeder
```

Optional future: opt-in during browser install (TASK-028-B).

### Banner

Rendered via `<x-demo-banner />` on admin and public layouts when `config('demo.enabled')` is true.

---

## 4. Demo accounts

Three fixed accounts represent the primary buyer personas. All are **protected** — identity, password, roles, and activation state cannot be changed in demo mode.

| Display name | Email | Role | Purpose |
|---|---|---|---|
| Demo Super Admin | `demo-superadmin@contentflow.test` | Super Admin | Full product tour |
| Demo Administrator | `demo-admin@contentflow.test` | Administrator | User/role admin (within demo limits) |
| Demo Editor | `demo-editor@contentflow.test` | Editor | Editorial workflow |

**Not seeded:** Author-only account (buyers can create one manually to test Author restrictions, or add in future seeder).

Protected emails are defined in:

- `config/demo.php` → `protected_user_emails`
- `DemoSeeder` (must stay in sync)

---

## 5. Demo roles

Demo uses the **standard system roles** from `RolePermissionSeeder`:

| Role | Demo account | Explore focus |
|---|---|---|
| Super Admin | `demo-superadmin@…` | All modules, settings UI (save blocked), audit |
| Administrator | `demo-admin@…` | Users, roles UI (baseline mutations blocked), content |
| Editor | `demo-editor@…` | Posts, pages, media, comments, taxonomy |

**System roles** (`is_system = true`): Super Admin, Administrator, Editor, Author — permission matrix sync is **blocked** in demo mode.

**Custom roles** (if created during exploration): permission sync is **allowed** — they are not part of the protected baseline.

---

## 6. Demo credentials strategy

### Public demo hosts (sales sandbox)

| Aspect | Policy |
|---|---|
| Password | Shared known password documented **only** in demo-specific docs and demo host README |
| Current value | `DemoPass123!` (`DemoSeeder::DEMO_PASSWORD`) |
| Production customer sites | **Never** ship this password; `DEMO_MODE=false` |
| `.env.example` | Does not include demo password or `DEMO_MODE=true` |
| Rotation | Change `DEMO_PASSWORD` in seeder + docs together; run `demo:reset` |

### Credential exposure rules

- Document credentials on demo landing page or sales collateral, not in generic installation guides for production.
- Do not embed passwords in compiled frontend assets.
- Password reset is **disabled** for all users in demo mode (middleware), with additional per-email guard for protected accounts.

### Buyer-created accounts

Prospects may create additional users (e.g. test Author). Those accounts:

- Use passwords they choose
- Are **not** protected by `DemoGuard`
- Are **removed** on `demo:reset`

---

## 7. Restricted operations (summary)

Demo mode uses **`DemoGuard`** in Actions plus **`EnsureNotDemoRestricted`** middleware for auth routes.

### Blocked in demo mode

| Category | Operation | Enforcement |
|---|---|---|
| **Users** | Deactivate protected demo users | `ChangeUserStatus` → `assertCanChangeUserStatus` |
| **Users** | Change password on protected users | `UpdateUser` → `assertCanUpdateUser` |
| **Users** | Change email on protected users | `UpdateUser` → `assertCanUpdateUser` |
| **Users** | Reassign roles on protected users | `AssignRole` → `assertCanAssignRoles` |
| **Roles** | Sync permissions on **system** roles | `SyncRolePermissions` → `assertCanSyncRolePermissions` |
| **Settings** | Any site settings save | `UpdateSiteSettings` → `assertCanUpdateSiteSettings` |
| **Auth** | Password reset (all routes) | `EnsureNotDemoRestricted` middleware |
| **Auth** | Password reset for protected emails | `ResetPasswordController`, `ForgotPasswordController` → `assertCanResetPassword` |
| **CLI** | `demo:reset` when demo off | `DemoReset` validation |

### Allowed in demo mode (exploration)

| Category | Operations |
|---|---|
| **Posts** | Create, update, publish, schedule, archive, unpublish, delete |
| **Pages** | Full CRUD and lifecycle |
| **Comments** | Public submit; admin moderation (approve/reject/spam) |
| **Media** | Upload, edit metadata, delete (subject to normal policies) |
| **Taxonomy** | Categories and tags CRUD |
| **Menus** | Create menus, update structure |
| **Users** | Create non-protected users; edit non-protected users; deactivate non-protected users |
| **Roles** | View; sync permissions on **non-system** custom roles |
| **Audit** | View activity log |
| **Dashboard** | View summaries |
| **Public site** | Browse posts, pages, archives, comment form |

Editorial and moderation flows remain fully interactive so buyers experience real CMS behavior. Baseline **platform configuration** stays fixed until reset.

---

## 8. Complete action matrix

All application Actions and sensitive surfaces, and demo behavior:

| Action / surface | Demo mode | Notes |
|---|---|---|
| **Auth: login** | ✅ Allowed | Normal throttling |
| **Auth: logout** | ✅ Allowed | |
| **Auth: forgot password** | ❌ Blocked | All users; middleware redirect |
| **Auth: reset password** | ❌ Blocked | Middleware + controller guard |
| **Users: create** | ✅ Allowed | New users wiped on reset |
| **Users: update (protected)** | ⚠️ Partial | Name change allowed; email/password blocked |
| **Users: update (other)** | ✅ Allowed | |
| **Users: activate/deactivate (protected)** | ❌ Blocked | |
| **Users: activate/deactivate (other)** | ✅ Allowed | Super Admin rules still apply |
| **Users: assign roles (protected)** | ❌ Blocked | |
| **Users: assign roles (other)** | ✅ Allowed | |
| **Roles: sync permissions (system)** | ❌ Blocked | |
| **Roles: sync permissions (custom)** | ✅ Allowed | |
| **Settings: update** | ❌ Blocked | All fields |
| **Posts: CRUD + lifecycle** | ✅ Allowed | |
| **Pages: CRUD + lifecycle** | ✅ Allowed | |
| **Comments: submit (public)** | ✅ Allowed | Rate limited |
| **Comments: moderate** | ✅ Allowed | |
| **Media: upload** | ✅ Allowed | See upload restrictions |
| **Media: update metadata** | ✅ Allowed | |
| **Media: delete** | ✅ Allowed | Reset restores baseline media |
| **Categories / tags: CRUD** | ✅ Allowed | |
| **Menus: create / update structure** | ✅ Allowed | |
| **Audit: record** | ✅ Allowed | Logs cleared on reset |
| **Audit: view index** | ✅ Allowed | |
| **Installer `/install`** | N/A | Separate lock; not demo-specific |
| **CLI: `demo:reset`** | ✅ Allowed | Requires `DEMO_MODE=true` |
| **CLI: `migrate`, `db:wipe`** | ⚠️ Ops only | Not blocked in app; restrict at server/SSH level |
| **Scheduled publish command** | ✅ Allowed | Runs if scheduler enabled on demo host |

---

## 9. Database reset strategy

### Command

```bash
php artisan demo:reset --force
```

Confirmation prompt when `--force` omitted.

### Behavior

1. Validates `DEMO_MODE=true`
2. Single **database transaction**:
   - Deletes pivot rows, comments, menu items, posts, pages, categories, tags, media, activity logs, **all users**
   - Re-runs `DemoSeeder` (roles, settings, menus, demo users, sample content)
3. Invalidates site settings cache

### What reset preserves

| Preserved | Reason |
|---|---|
| Schema / migrations | Not touched |
| System roles & permissions | Re-seeded idempotently via `RolePermissionSeeder` |
| `site_settings` row | Updated by seeder, not dropped |
| Menu definitions (keys) | Re-seeded via `MenuSeeder` |
| `.env` / `DEMO_MODE` | Environment unchanged |

### What reset removes

- All user-created content and users (including buyer-created test accounts)
- Uploaded media files **DB rows** (orphaned files on disk may remain — ops should periodic `storage:clean` if needed)
- Activity logs

### Failure handling

If seeding fails inside the transaction, the entire reset rolls back (no empty database state).

---

## 10. Reset schedule

Demo mode does **not** include an in-app automatic reset timer. Use **operations scheduling** on the demo host:

| Schedule | Method | Recommended for |
|---|---|---|
| **Nightly** | Cron: `php artisan demo:reset --force` | Public sales demo with heavy traffic |
| **Weekly** | Cron | Low-traffic evaluation server |
| **On demand** | Manual after sales demos | Internal staging |
| **Never auto** | Manual only | Controlled private demos |

Example cron (02:00 UTC daily):

```cron
0 2 * * * cd /var/www/contentflow && php artisan demo:reset --force >> /var/log/contentflow-demo-reset.log 2>&1
```

Document the schedule on the demo login page so visitors know content may refresh.

**Future enhancement (optional):** `demo:reset --schedule` documentation or Laravel scheduler entry in `routes/console.php` behind `DEMO_MODE` check.

---

## 11. Demo data

`DemoSeeder` creates a **minimal but representative** dataset:

| Entity | Sample content |
|---|---|
| **Users** | 3 protected demo accounts |
| **Site settings** | Name “ContentFlow CMS Demo”, comments enabled |
| **Categories** | News, Guides |
| **Tags** | CMS, Demo |
| **Posts** | Welcome (published), Editorial workflow (published), Scheduled preview (scheduled) |
| **Pages** | About, Contact (published) |
| **Comments** | One approved, one pending on welcome post |
| **Menu** | Primary: Home, About, News |

**Planned:** Sample `Media` records (TASK-025 gap / TASK-028).

Content uses plain/marketing copy suitable for public demo; no real PII.

---

## 12. Upload restrictions

| Restriction | Demo mode | Production |
|---|---|---|
| Max file size | 5120 KB (`MEDIA_MAX_UPLOAD_KB`) | Same |
| Allowed types | JPEG, PNG, GIF, WebP | Same |
| MIME validation | `finfo` + image structure check | Same |
| Demo-specific quota | **None implemented** | — |
| Reset impact | Uploaded media DB rows deleted on reset | N/A |

**Recommendation for public demo:** Keep default limits; rely on reset to clear abuse. Optional future: lower `MEDIA_MAX_UPLOAD_KB` via demo-specific config when `DEMO_MODE=true`.

**Not restricted:** Upload authorization still follows `MediaPolicy` — demo Editors can upload like production.

---

## 13. Configuration restrictions

| Configuration | Demo behavior |
|---|---|
| Site settings (UI) | **All saves blocked** |
| `.env` / environment | Not modified by demo guards; ops responsibility |
| `DEMO_MODE` itself | Requires deploy access; boot guard prevents accidental production enable |
| Mail / queue / cache drivers | Unchanged; demo typically uses `MAIL_MAILER=log` |
| Installer | Independent of demo; lock file separate |

Prospects can **view** settings screens but cannot persist changes — errors surface via Livewire validation bags (`demo` key).

---

## 14. Email restrictions

| Email action | Demo mode |
|---|---|
| Password reset notification | ❌ Blocked (routes disabled) |
| Comment notifications | N/A (not in MVP) |
| Queued mail from other features | Follows `MAIL_MAILER`; use `log` on demo hosts |
| User-created mail triggers | Should not send real mail on public demo |

**Ops recommendation:** Set `MAIL_MAILER=log` on public demo servers to prevent accidental outbound mail.

---

## 15. Password restrictions

| Rule | Detail |
|---|---|
| Protected account password change | ❌ Blocked in admin user edit |
| Protected account password reset | ❌ Blocked |
| Login with shared demo password | ✅ Allowed |
| Password strength on new users | Normal `Password::defaults()` |
| Buyer changes own password (if logged in as non-protected user) | ✅ Allowed |

---

## 16. Admin protection

### Protected demo accounts

Emails listed in `config/demo.php` `protected_user_emails`:

- Cannot be deactivated
- Cannot have password or email changed
- Cannot have roles reassigned

### Super Admin baseline

`SuperAdminProtection` still applies in demo:

- Cannot deactivate the **last active** Super Admin (including demo Super Admin if alone)

### System role baseline

- Permission matrix for system roles cannot be altered in demo
- Role **names** and assignments to non-protected users remain policy-governed

### What is not admin-protected

- Deleting or editing **content** created by demo accounts (by design — reset restores)
- Creating/deleting **non-protected** users
- Uploading/deleting **media**

---

## 17. Architecture

```text
config/demo.php (DEMO_MODE, protected emails)
        │
        ▼
DemoGuard::ensureSafeConfiguration()  ← boot
        │
        ├── EnsureNotDemoRestricted middleware (password routes)
        │
        └── Action hooks:
              ChangeUserStatus, UpdateUser, AssignRole,
              SyncRolePermissions, UpdateSiteSettings
              ForgotPasswordController, ResetPasswordController
        │
        ▼
DemoReset + demo:reset command
DemoSeeder
<x-demo-banner />
```

Demo guards throw `ValidationException` with friendly messages; Livewire components catch and display via `addError('demo', …)` or `setErrorBag()`.

When `DEMO_MODE=false`, all guard methods no-op immediately — **zero behavioral change** on customer sites.

---

## 18. Security checklist (demo host)

- [ ] `DEMO_MODE=true` only on dedicated demo VM
- [ ] `DEMO_ALLOW_IN_PRODUCTION=true` only if demo runs with `APP_ENV=production`
- [ ] `APP_DEBUG=false` on public demo
- [ ] `MAIL_MAILER=log` (or sink) to prevent spam
- [ ] Installer locked (`storage/app/install.lock`)
- [ ] HTTPS enabled
- [ ] SSH/cron access restricted for `demo:reset` and artisan destructive commands
- [ ] Demo credentials documented for sales, not in production install docs
- [ ] Scheduled or manual reset documented for visitors

See [SECURITY_AUDIT.md](SECURITY_AUDIT.md) for demo-related findings.

---

## 19. Testing

```bash
php artisan test --filter=Demo
```

| Test file | Coverage |
|---|---|
| `DemoRestrictionsTest` | Blocked user/settings/role actions; editorial allowed; demo off = inert |
| `DemoResetTest` | Baseline restore; rejects when demo off |
| `DemoBannerTest` | Admin banner visibility |
| `DemoPasswordResetTest` | Reset route blocked |
| `DemoGuardTest` | Config guard, protected emails, production boot fail |

---

## 20. Known gaps and roadmap

| Gap | Task | Priority |
|---|---|---|
| Optional demo seed in installer wizard | TASK-028-B | P1 |
| Sample media in `DemoSeeder` | TASK-025 follow-up | P2 |
| Protected user **name** change allowed | Consider blocking | P3 |
| Single source for protected emails (config-driven seeder) | Refactor | P2 |
| Scheduled auto-reset in app scheduler | Document cron only for now | P3 |
| Upload quota reduction in demo | Optional config | P3 |
| Public demo login page with role buttons | Sales UX | P3 |

---

## 21. Related documentation

- [INSTALLATION.md](INSTALLATION.md) — enabling demo after install
- [DEPLOYMENT.md](DEPLOYMENT.md) — demo vs production table
- [ADMIN_GUIDE.md](ADMIN_GUIDE.md) — banner and restrictions for staff
- [SECURITY_AUDIT.md](SECURITY_AUDIT.md) — demo password and reset hardening
- [INSTALLER.md](INSTALLER.md) — optional demo opt-in at install (planned)

---

## 22. Revision history

| Date | Change |
|---|---|
| 2026-08-31 | Initial demo mode architecture document |

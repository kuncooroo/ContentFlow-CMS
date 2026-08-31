# Update & Versioning Strategy — ContentFlow CMS

Release engineering policy for versioning, upgrades, rollbacks, and customer data safety.

| Field | Value |
|---|---|
| **Audience** | Release engineers, maintainers, customer ops |
| **Current version** | `1.0.0` (`config/contentflow.php`) |
| **Related docs** | [UPGRADE.md](UPGRADE.md), [CHANGELOG.md](../CHANGELOG.md), [RELEASE.md](RELEASE.md) |

---

## 1. Goals

1. Customers can **upgrade without losing application data** (content, users, media, settings).
2. Version numbers communicate **risk and compatibility** clearly.
3. Database changes are **forward-only, additive, and reversible via backup** — not via destructive auto-rollback in production.
4. Breaking changes are **rare, documented, and version-gated** (major releases only).
5. Every release ships with **changelog + release notes + upgrade steps**.

---

## 2. Semantic Versioning (SemVer)

ContentFlow CMS follows [Semantic Versioning 2.0.0](https://semver.org/):

```text
MAJOR.MINOR.PATCH
  │      │     └── Bug fixes, security patches (backward compatible)
  │      └──────── New features (backward compatible)
  └─────────────── Breaking changes (incompatible API/schema/behavior)
```

### Version format

| Component | Format | Example |
|---|---|---|
| **Release tag** | `vMAJOR.MINOR.PATCH` | `v1.2.3` |
| **Composer package** | `MAJOR.MINOR.PATCH` | `"version": "1.2.3"` in `composer.json` |
| **Runtime constant** | `MAJOR.MINOR.PATCH` | `config('contentflow.version')` |
| **Release date** | ISO 8601 date | `config('contentflow.release_date')` |
| **Pre-release** | `-label.N` | `1.1.0-rc.1`, `2.0.0-beta.2` |
| **Build metadata** | `+build` (optional) | `1.0.0+20260831` — not used for ordering |

**Single source of truth for product version:**

1. Tag in git: `v1.2.3`
2. Update `config/contentflow.php` → `version`, `release_date`
3. Update `composer.json` → `"version"`
4. Add section to `CHANGELOG.md`
5. Publish `docs/RELEASE.md` (or version-specific release notes)

---

## 3. Release types

### 3.1 Patch releases (`x.y.Z`)

**Purpose:** Bug fixes, security patches, documentation corrections that do not change intended behavior.

| Aspect | Policy |
|---|---|
| **Schema changes** | None preferred; if required, must be **additive only** (new nullable column, new index) |
| **Migrations** | New migration files only; never edit shipped migrations |
| **Seeders** | Usually not re-run; exception: idempotent permission hotfix via release notes |
| **Config** | New optional `.env` keys only |
| **Customer data** | Preserved; no manual data migration |
| **Downtime** | Zero-downtime target for single-node VPS |

**Examples:** XSS fix, upload validation hardening, session invalidation bug, installer rate limit.

**Customer action:** Standard upgrade procedure ([UPGRADE.md](UPGRADE.md)); read patch section in changelog.

---

### 3.2 Minor releases (`x.Y.z`)

**Purpose:** New backward-compatible features, modules, permissions, or operational improvements.

| Aspect | Policy |
|---|---|
| **Schema changes** | Allowed — additive columns/tables/indexes |
| **Migrations** | Forward migrations required; document any post-migrate steps |
| **Seeders** | May require idempotent seeder re-run (documented in release notes) |
| **Permissions** | New permissions added via `RolePermissionSeeder` (idempotent) |
| **Config** | New keys in `.env.example`; defaults safe when unset |
| **Customer data** | Preserved; new features opt-in or default-off |
| **Deprecations** | May announce; removal only in next major |

**Examples:** New admin filter, export feature, optional module, performance cache layer.

**Customer action:** Standard upgrade + review release notes for seeder/config steps.

---

### 3.3 Major releases (`X.y.z`)

**Purpose:** Breaking changes that require customer action.

| Aspect | Policy |
|---|---|
| **Breaking changes** | Allowed with migration guide |
| **Schema** | May rename/drop columns **only** with documented data migration and major bump |
| **Permissions** | Renamed/removed permissions require role re-sync instructions |
| **PHP/Laravel** | Framework major bumps typically coincide with major product version |
| **Customer data** | Preserved when possible; destructive schema requires explicit backup + migration script |
| **Support window** | Previous major receives security patches for defined period (see §12) |

**Examples:** Remove deprecated API, change post status enum, drop legacy table, require PHP 9.

**Customer action:** Read major upgrade guide; test on staging; backup mandatory; may require manual steps.

---

## 4. Database migration strategy

### 4.1 Golden rules

1. **Never edit migrations** that have been released to customers (`CURSOR.md` §27).
2. **Always add** new migrations for schema changes.
3. **Prefer additive changes:** new columns nullable or with safe defaults; new tables; new indexes.
4. **Avoid destructive changes** in patch/minor releases (drop column, change enum semantics without migration).
5. **Migrations must be deterministic** and runnable on empty and populated databases.
6. **Test upgrades** from previous minor on staging with production-like data volume.

### 4.2 Migration workflow (maintainers)

```text
Design schema change
    ↓
New migration: YYYY_MM_DD_HHMMSS_descriptive_name.php
    ↓
Test: migrate:fresh AND migrate from N-1 release DB snapshot
    ↓
Document in CHANGELOG + release notes if customer action needed
    ↓
Ship with version tag
```

### 4.3 Customer migration command

```bash
php artisan migrate --force
```

- Run **after** deploying new code, **before** serving traffic (or during maintenance window).
- Never use `migrate:fresh` on production — it destroys all data.
- `migrate:rollback` is for **development only** unless release notes explicitly provide safe rollback steps.

### 4.4 Seeders on upgrade

| Seeder | When to re-run |
|---|---|
| `RolePermissionSeeder` | Release notes say new permissions added |
| `SiteSettingsSeeder` | Fresh install only; not on routine upgrade |
| `DemoSeeder` | Demo hosts only; `demo:reset` preferred |

Seeders use `updateOrCreate` / idempotent patterns where applicable.

### 4.5 Data preservation guarantees

| Data class | Upgrade policy |
|---|---|
| Posts, pages, comments | **Never dropped** by routine migrations |
| Users, roles, assignments | Preserved; permission sync may add keys |
| Media files + DB rows | Preserved; paths unchanged unless major migration documents move |
| Site settings singleton | Preserved; new columns get defaults |
| Activity logs | Preserved unless major release documents retention policy |
| Sessions, cache, jobs | Ephemeral; safe to clear after upgrade |

---

## 5. Backward compatibility

### 5.1 What we keep compatible (minor/patch)

| Surface | Compatibility |
|---|---|
| Database schema | Forward-only; old data readable after migrate |
| Permission names | Existing keys remain valid |
| Post/page/comment statuses | No silent new statuses in patch/minor |
| Admin URLs (`/admin/*`) | Stable unless deprecated with notice |
| Public URLs (`/posts/{slug}`, etc.) | Stable |
| `.env` keys | Old keys continue to work; new keys optional |
| Installer lock | Upgrades retain `install.lock` |

### 5.2 What may change (major only)

- Removed routes or renamed permissions
- Column renames/drops requiring export/import
- Minimum PHP/MySQL version increase
- Changed default behavior documented as breaking

### 5.3 Compatibility testing matrix

Before each release, verify on staging:

| From → To | Test |
|---|---|
| Previous patch → current | `migrate` + smoke tests |
| Previous minor → current | Full regression subset |
| Previous major → current major | Documented migration path |

---

## 6. Breaking changes policy

### 6.1 Definition

A **breaking change** requires customers to modify configuration, code customizations, integrations, or operational procedures — or risks data/behavior change without action.

### 6.2 Requirements for breaking changes

1. **Major version bump only**
2. Document in `CHANGELOG.md` under **Breaking changes**
3. Dedicated section in release notes with:
   - What changed
   - Why
   - Before/after examples
   - Migration steps
   - Rollback limitations
4. Deprecation notice in prior **minor** release when feasible (see §12)

### 6.3 Non-breaking by default

These are **not** breaking if handled correctly:

- New optional `.env` variables with defaults
- New permissions (existing roles keep working; sync adds new keys)
- New admin menu items
- New nullable DB columns
- Security hardening that blocks previously unsafe behavior (document as **Security** not Breaking when possible)

---

## 7. Upgrade procedures

### 7.1 Standard upgrade (patch & minor)

Detailed steps: [UPGRADE.md](UPGRADE.md).

**Summary:**

```bash
# 1. Backup (mandatory — see §9)
# 2. Maintenance mode (recommended)
php artisan down

# 3. Deploy new release (git pull or tarball)
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 4. Database
php artisan migrate --force
# Optional: release-note seeder commands

# 5. Cache
php artisan config:clear && php artisan cache:clear && php artisan view:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 6. Queue workers
php artisan queue:restart

# 7. Up
php artisan up
```

### 7.2 Major upgrade

1. Read major release notes end-to-end.
2. Verify PHP/MySQL meets new minimums ([VERSIONS.md](VERSIONS.md)).
3. **Full backup** (database + `storage/app` + `.env`).
4. Upgrade on **staging** first.
5. Run documented migration scripts beyond `artisan migrate` if any.
6. Re-sync roles if permissions changed.
7. Smoke test: login, CRUD, publish, public site, scheduler, queue.
8. Production upgrade in maintenance window.

### 7.3 Post-upgrade verification

| Check | Command / action |
|---|---|
| Migrations current | `php artisan migrate:status` |
| Scheduler registered | `php artisan schedule:list` |
| Failed jobs | `php artisan queue:failed` |
| Login + dashboard | Manual |
| Public home + post | Manual |
| Installer still locked | `/install` returns 404 |

---

## 8. Rollback procedures

### 8.1 Principle

**Production rollback = restore backup + deploy previous code.**  
Do not rely on `migrate:rollback` in production unless release notes certify reversibility.

### 8.2 Rollback steps

1. Enable maintenance mode: `php artisan down`
2. **Restore MySQL database** from pre-upgrade backup
3. **Restore `storage/app`** if media changed during failed upgrade
4. Deploy previous release tag (e.g. `v1.0.0`)
5. `composer install --no-dev` and `npm ci && npm run build` for that tag
6. Clear and rebuild caches
7. `php artisan queue:restart`
8. `php artisan up`
9. Verify smoke tests

### 8.3 When rollback is insufficient

If migrations were **destructive** (major release):

- Database backup is the only safe rollback path
- Document in release notes before shipping

### 8.4 Partial failure during upgrade

| Failure point | Action |
|---|---|
| Before `migrate` | Redeploy; no DB restore needed |
| During `migrate` | Restore DB backup; fix migration; retry on staging |
| After `migrate`, before smoke OK | Restore DB + code to previous version |
| App up but defect found | Patch forward preferred over rollback for minor issues |

---

## 9. Backup requirements

### 9.1 Before every upgrade (mandatory)

| Asset | Method | Retention |
|---|---|---|
| **MySQL database** | `mysqldump` or host backup | Until next successful upgrade + 30 days |
| **`storage/app/`** | Filesystem archive | Same as database |
| **`.env`** | Secure copy (encrypted) | Same |
| **Custom code** | Git tag/commit reference | Permanent |

### 9.2 Backup validation

- Periodically **test restore** on staging (quarterly recommended).
- Verify backup includes uploaded media referenced by DB rows.

### 9.3 What backups protect

- Posts, pages, comments, users, roles, settings, activity logs
- Media files on disk
- Environment configuration

Backups do **not** replace version control for application source code.

---

## 10. Changelog format

File: [`CHANGELOG.md`](../CHANGELOG.md)

Based on [Keep a Changelog 1.1.0](https://keepachangelog.com/en/1.1.0/).

### Structure

```markdown
## [MAJOR.MINOR.PATCH] - YYYY-MM-DD

### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security

### Breaking changes   ← major releases only
### Upgrade notes      ← when customer action required
```

### Rules

1. **Newest version first**
2. Every released version has a dated section
3. Link version headers to git tag: `[1.0.0]: https://.../releases/tag/v1.0.0`
4. **Security** fixes called out explicitly
5. **Upgrade notes** for migrations, seeders, `.env` keys, cron/queue changes

---

## 11. Release notes

File pattern: `docs/RELEASE.md` (current) or `docs/releases/vX.Y.Z.md` (optional for large majors).

### Required sections

| Section | Content |
|---|---|
| **Summary** | One paragraph for customers |
| **Version & date** | SemVer + release date |
| **Upgrade priority** | Low / Medium / High (security) |
| **Requirements** | PHP, MySQL, Node if changed |
| **Upgrade steps** | Link to UPGRADE.md + release-specific commands |
| **Breaking changes** | Major only |
| **New features** | Minor/major |
| **Bug fixes** | Patch/minor |
| **Security** | All relevant releases |
| **Known issues** | If any |
| **Checksum / package** | Tarball name for source distribution |

Distribution packaging: [.release/PACKAGE_MANIFEST.md](../.release/PACKAGE_MANIFEST.md).

---

## 12. Deprecation policy

### 12.1 Timeline

| Stage | When | Action |
|---|---|---|
| **Announce** | Minor release | Document in CHANGELOG **Deprecated** + release notes |
| **Warn** | Runtime/logs/admin notice if applicable | At least one minor cycle |
| **Remove** | Next **major** release | Document in **Breaking changes** |

Minimum notice: **one minor version** before removal when feasible.

### 12.2 What can be deprecated

- Admin routes or UI modules
- Permission keys (provide mapping to replacement)
- `.env` keys (support both old and new for one minor)
- PHP version support
- Legacy APIs (if introduced in future)

### 12.3 What we avoid deprecating without major bump

- Post/page/comment status values
- Core permission semantics
- Public URL patterns customers link to externally

---

## 13. Support and release lifecycle

### 13.1 Recommended support windows

| Release type | Security fixes | Bug fixes | Feature backports |
|---|---|---|---|
| **Current major** | Yes | Yes | N/A (included in minors) |
| **Previous major** | Security only (12 months) | No | No |
| **Older majors** | Upgrade recommended | No | No |

Adjust windows per commercial license agreement.

### 13.2 Long-term support (LTS) — optional commercial tier

If offered to customers:

- Designate e.g. `2.0.x` as LTS with extended security patches
- Document separately from this file

---

## 14. Release checklist (maintainers)

Before tagging `vX.Y.Z`:

- [ ] Version bumped in `config/contentflow.php`, `composer.json`
- [ ] `CHANGELOG.md` section complete
- [ ] Release notes published
- [ ] Migrations tested from previous customer version
- [ ] `composer test` green on CI
- [ ] `npm run build` produces assets
- [ ] `.env.example` updated for new keys
- [ ] [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) reviewed
- [ ] No edited historical migrations
- [ ] Package tarball built (`.release/package.sh`) if shipping source
- [ ] Git tag `vX.Y.Z` created and pushed

After tag:

- [ ] Customer-facing upgrade summary communicated
- [ ] Demo hosts reset if seed data changed (`demo:reset`)

---

## 15. Customer data safety summary

| Question | Answer |
|---|---|
| Will upgrade delete my posts? | **No** — routine migrations preserve content tables |
| Do I need to re-run the installer? | **No** — keep `install.lock`; use upgrade procedure |
| Will media files disappear? | **No** — files in `storage/app` remain; backup anyway |
| Can I skip versions (1.0 → 1.2)? | **Yes** — run all migrations sequentially via single `migrate` |
| Is downgrade safe without backup? | **No** — always restore from backup to rollback |

---

## 16. Related documentation

| Document | Purpose |
|---|---|
| [UPGRADE.md](UPGRADE.md) | Step-by-step customer upgrade |
| [CHANGELOG.md](../CHANGELOG.md) | Version history |
| [RELEASE.md](RELEASE.md) | Current release notes |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Post-upgrade issues |
| [USER_GUIDE.md](USER_GUIDE.md) § Updates | End-user summary |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Production ops |

---

## 17. Revision history

| Date | Change |
|---|---|
| 2026-08-31 | Initial update and versioning strategy (v1.0.0 baseline) |

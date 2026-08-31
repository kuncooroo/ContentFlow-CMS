# Release Notes — ContentFlow CMS v1.0.0

| Field | Value |
|---|---|
| **Version** | 1.0.0 |
| **Release date** | 2026-08-31 |
| **Status** | Release candidate |
| **Runtime target** | Linux VPS, PHP 8.4, MySQL 8.4 |

## Summary

First MVP release of ContentFlow CMS as a source-code product: editorial CMS with admin dashboard, public site, installer, optional demo mode, and full documentation set.

Version constant: `config('contentflow.version')` → `1.0.0`

## Distribution package

Build a clean tarball on Linux (or WSL) using:

```bash
bash .release/package.sh
```

Output: `dist/contentflow-cms-1.0.0.tar.gz`

See [.release/PACKAGE_MANIFEST.md](../.release/PACKAGE_MANIFEST.md) for included/excluded paths.

**Never include in packages:** `.env`, `vendor/`, `node_modules/`, `storage/logs/*`, `.git/`, IDE caches, or local `install.lock` from dev machines.

## Pre-release verification

### Automated tests

```bash
composer test
php artisan test --filter=Security
php artisan test --filter=Regression
php artisan test --filter=Install
php artisan test --filter=Demo
```

**Requirement:** MySQL on `127.0.0.1:3306` with database `contentflow` (see `phpunit.xml`).

CI: GitHub Actions [`.github/workflows/tests.yml`](../.github/workflows/tests.yml) runs `composer test` on push/PR.

### Production asset build

```bash
npm ci
npm run build
```

Verify `public/build/` manifest exists after build.

### Security checklist

Complete [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md). Key production settings:

- `APP_DEBUG=false`
- `APP_ENV=production`
- `DEMO_MODE=false`
- `SESSION_SECURE_COOKIE=true` (HTTPS)
- Document root = `public/` only

### Secret scan (manual)

Before tagging, confirm no committed secrets:

```bash
# Example patterns — review any matches in app/, config/, docs/
rg -i "(password|secret|api_key|private_key)\s*=\s*['\"][^'\"]+['\"]" app config docs --glob '!*.md'
```

`.env.example` uses placeholders only. Demo passwords (`DemoPass123!`) are documented for demo hosts only — not shipped as defaults in `.env.example`.

### Scope verification

Confirmed **absent** from MVP:

- Payment/billing/subscription tables or UI
- Multi-tenant SaaS features
- Financial modules

Database migrations contain no payment-related tables.

## Functional smoke (manual)

| Area | Steps | Expected |
|---|---|---|
| Fresh install | `/install` wizard on empty DB | Super Admin created, lock file written |
| Login | `/login` | Dashboard access |
| Editorial | Create draft post → publish | Visible on `/blog` |
| Public | Home, post, page URLs | 200, no admin leakage |
| Comments | Submit on published post | Pending in admin queue |
| Scheduler | `php artisan content:publish-scheduled-posts` | Due scheduled posts publish |
| Queue | Reset password with `QUEUE_CONNECTION=database` + worker | Email job processed |
| Demo | `DEMO_MODE=true` + `DemoSeeder` | Banner visible, restrictions active |
| Demo reset | `php artisan demo:reset --force` | Baseline content restored |

## Backup & restore rehearsal

Documented procedure in [DEPLOYMENT.md](DEPLOYMENT.md#backups). Rehearsal checklist:

1. **Backup** — `mysqldump contentflow_cms > backup-pre-restore.sql` and copy `storage/app/`
2. **Mutate** — Create a test post titled "Restore verification delete me"
3. **Restore** — Import SQL dump; restore `storage/app/` if media paths changed
4. **Verify** — Test post from step 2 absent; site settings and users intact
5. **Record** — Date, operator, DB size, duration noted below

| Rehearsal | Date | Result | Notes |
|---|---|---|---|
| Staging/local | 2026-08-31 | Procedure documented | Execute on target VPS before customer handoff |

## Release cleanup (v1.0.0)

- Removed `/admin/smoke` development Livewire page
- Removed `inspire` scheduler dev entry
- `DEMO_MODE=false` default in `.env.example`
- Installer lock not included in distribution packages

## Documentation index

All guides: [docs/README.md](README.md)

| Guide | Path |
|---|---|
| Installation | [INSTALLATION.md](INSTALLATION.md) |
| Deployment | [DEPLOYMENT.md](DEPLOYMENT.md) |
| Upgrade | [UPGRADE.md](UPGRADE.md) |
| Troubleshooting | [TROUBLESHOOTING.md](TROUBLESHOOTING.md) |

## Tagging (maintainers)

When CI is green and manual smoke passes:

```bash
git tag -a v1.0.0 -m "ContentFlow CMS v1.0.0"
git push origin v1.0.0
```

Do **not** tag until MySQL-backed full regression passes on the release commit.

## Known limitations (v1.0.0)

- Plain-text content only (no WYSIWYG HTML)
- Single-site deployment (no multi-tenancy)
- MySQL required (SQLite not supported for production)
- Browser support: modern evergreen browsers per SRS; IE not supported

## Changelog

See [CHANGELOG.md](../CHANGELOG.md).

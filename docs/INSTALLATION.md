# Installation Guide — ContentFlow CMS

This guide covers a clean setup on a developer machine or VPS. For production web server configuration, see [DEPLOYMENT.md](DEPLOYMENT.md).

## Requirements

| Component | Minimum |
|---|---|
| PHP | 8.3+ (8.4 recommended) |
| MySQL | 8.4.x LTS |
| Composer | 2.x |
| Node.js | 20+ (22 recommended) |
| npm | 10+ |

PHP extensions: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`.

Writable directories: `storage/`, `bootstrap/cache/`. The web installer also checks that `.env` can be created or written.

## Option A — Browser installer (recommended)

Use this for source-code deployments when `storage/app/install.lock` does not exist.

1. Clone or copy the project to the server.
2. Install PHP dependencies:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

3. Ensure the web server document root points to `public/`.
4. Open **`/install`** in the browser.

The wizard steps:

| Step | What happens |
|---|---|
| 1. Requirements | PHP, extensions, writable paths |
| 2. Application | App name and URL written to `.env` |
| 3. Database | Connection test, `migrate`, seed roles/settings |
| 4. Administrator | Super Admin account created |
| 5. Site settings | Site name, timezone, locale |
| 6. Complete | Installer writes `storage/app/install.lock` |

After completion, `/install` routes return 404. Sign in at `/login`.

### Installer security notes

- Database and admin passwords are saved once and **not shown** on later steps.
- The installer is blocked when `storage/app/install.lock` exists.
- To re-run the installer intentionally, back up data first, then remove `install.lock` — only on empty or disposable databases.

## Option B — Manual setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with database credentials and `APP_URL`, then:

```bash
composer install
npm install && npm run build
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SiteSettingsSeeder
php artisan storage:link
```

Create the first Super Admin with `php artisan tinker` or the browser installer. For local QA accounts, use:

```bash
php artisan db:seed --class=TestingSeeder
```

Default factory password: `password`.

## Demo environment

For a pre-populated demo site:

```bash
# In .env
DEMO_MODE=true

php artisan db:seed --class=DemoSeeder
```

Demo credentials are documented in [README.md](../README.md#demo-mode). Reset anytime with:

```bash
php artisan demo:reset --force
```

Keep `DEMO_MODE=false` on production customer installs.

## Essential artisan commands

| Command | Purpose |
|---|---|
| `php artisan key:generate` | Create `APP_KEY` |
| `php artisan migrate --force` | Apply schema |
| `php artisan db:seed --class=RolePermissionSeeder` | Roles and permissions |
| `php artisan db:seed --class=SiteSettingsSeeder` | Site settings row |
| `php artisan storage:link` | Public media URLs |
| `php artisan serve` | Local dev server |

## Partial install recovery

| Situation | Action |
|---|---|
| Wizard failed at database step | Fix credentials/permissions; re-submit database step (no lock yet) |
| Migration failed | Fix DB issue; retry database step — success is not reported on failure |
| Wizard completed but site broken | Do **not** delete lock blindly; fix `.env`, run `php artisan migrate --force` |
| Need full reinstall | Back up DB and files; drop database or use fresh DB; remove `storage/app/install.lock`; run `/install` again |

## Verification checklist

- [ ] `/login` loads and Super Admin can sign in
- [ ] `/admin/dashboard` loads after login
- [ ] Public home `/` loads
- [ ] `storage/app/install.lock` exists (browser install path)
- [ ] `php artisan test` passes (MySQL required — see [TESTING.md](TESTING.md))

## Next steps

- [DEPLOYMENT.md](DEPLOYMENT.md) — production VPS, queue, scheduler
- [ADMIN_GUIDE.md](ADMIN_GUIDE.md) — day-to-day admin workflows
- [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) — pre-release hardening

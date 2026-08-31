# Troubleshooting — ContentFlow CMS

Common issues and fixes. Setup: [INSTALLATION.md](INSTALLATION.md). Production: [DEPLOYMENT.md](DEPLOYMENT.md).

## Installation & setup

### Browser installer returns 404

**Cause:** `storage/app/install.lock` exists from a previous install.

**Fix:** If you intentionally need a fresh install, back up data, use an empty database, remove `install.lock`, then open `/install`. Otherwise use manual migration/user setup.

### Database connection failed during install

**Checks:**

- MySQL is running and reachable from the app host
- Database exists and user has `CREATE`, `ALTER`, `INSERT` privileges
- `.env` / wizard credentials match (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)

### Migration failed in installer

The wizard reports an error and does **not** advance. Fix the underlying DB issue and retry the database step. Partial migrations may require `php artisan migrate:status` and manual DBA review.

### `storage/` or `.env` not writable

```bash
# Linux example — adjust user to your web/PHP user
chmod -R ug+rwx storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Application runtime

### 500 error on every page

1. Set `APP_DEBUG=true` temporarily **on staging only** to read the stack trace.
2. Check `storage/logs/laravel.log`.
3. Common causes: missing `APP_KEY`, DB down, cache permissions, stale config cache after `.env` change.

```bash
php artisan config:clear
php artisan cache:clear
```

### 419 Page Expired on forms

Session/CSRF mismatch. Ensure:

- `@csrf` on forms
- `SESSION_DOMAIN` and `APP_URL` match how users access the site
- Cookies work over HTTPS (`SESSION_SECURE_COOKIE=true` when using HTTPS)

### Public pages 500 but admin works

Often missing site settings seed or `SiteSettings` DB access. Run:

```bash
php artisan db:seed --class=SiteSettingsSeeder --force
```

## Database & tests

### Tests fail: `connection refused` on port 3306

MySQL is not running. Start MySQL (e.g. Laragon on Windows), ensure database `contentflow` exists per `phpunit.xml`.

```bash
php artisan test --filter=Demo
```

### `SQLSTATE[HY000] [1049] Unknown database`

Create the database:

```sql
CREATE DATABASE contentflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Match name in `.env` / `phpunit.xml`.

## Queue & mail

### Password reset emails never arrive

1. Confirm `QUEUE_CONNECTION` — if `database`, a worker must run ([QUEUE_OPERATIONS.md](QUEUE_OPERATIONS.md)).
2. Check `jobs` and `failed_jobs` tables.
3. Verify SMTP settings in `.env` (use provider docs, not committed secrets).
4. Inspect `storage/logs/laravel.log` for redacted mail errors.

### Jobs pile up in `jobs` table

Start or restart the queue worker:

```bash
php artisan queue:work --tries=3
# production: supervisorctl restart contentflow-worker:*
```

## Scheduled posts not publishing

1. Cron must run every minute: `* * * * * php artisan schedule:run`
2. Verify: `php artisan schedule:list`
3. Manually test: `php artisan content:publish-scheduled-posts`
4. Post must be `scheduled` with `publish_at` in the past

## Media uploads

### Images upload but URL 404

```bash
php artisan storage:link
```

Confirm `public/storage` symlink exists and web server serves `public/`.

### Upload rejected

Check `config/media.php` — allowed MIME types and `MEDIA_MAX_UPLOAD_KB`. Blocked extensions (php, svg, html, etc.) are intentional.

## Demo mode

### Demo banner shows on production

Set `DEMO_MODE=false` in `.env` and `php artisan config:clear`.

### App won't boot with demo enabled

`APP_ENV=production` + `DEMO_MODE=true` throws unless `DEMO_ALLOW_IN_PRODUCTION=true`. Intentional safety guard.

### `demo:reset` fails

Requires `DEMO_MODE=true`. Uses destructive wipe of users/content — run only on demo hosts.

## Security / production

See [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) for:

- `APP_DEBUG=false`
- HTTPS and `SESSION_SECURE_COOKIE`
- Document root = `public/` only

## Getting more help

1. Reproduce with `storage/logs/laravel.log` entry.
2. Run targeted tests: `php artisan test --filter=Security`
3. Consult [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) and spec docs under `docs/`.

## Walkthrough validation checklist (TASK-026)

Use when validating documentation accuracy:

- [ ] [INSTALLATION.md](INSTALLATION.md) — fresh install path documented
- [ ] [DEPLOYMENT.md](DEPLOYMENT.md) — Nginx, queue, cron, mail, backups covered
- [ ] [USER_GUIDE.md](USER_GUIDE.md) + [ADMIN_GUIDE.md](ADMIN_GUIDE.md) — core workflows described
- [ ] [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) — structure, Actions, Policies, tests
- [ ] Internal links resolve under `docs/`
- [ ] No real passwords or API keys in any doc file

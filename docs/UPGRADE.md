# Upgrade Guide — ContentFlow CMS

Steps for updating an existing installation to a newer release.

**Policy:** [UPDATE_STRATEGY.md](UPDATE_STRATEGY.md) — SemVer, migration rules, rollback, and data safety.

## Before you upgrade

1. **Back up** the MySQL database and `storage/app/` (see [DEPLOYMENT.md](DEPLOYMENT.md#backups)).
2. Note current versions: `php artisan --version`, check [VERSIONS.md](VERSIONS.md).
3. Read release notes / changelog if provided (TASK-027).
4. Test the upgrade on staging when possible.

## Standard upgrade procedure

```bash
# Maintenance mode (optional but recommended)
php artisan down

git pull origin main   # or deploy new release artifact

composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan migrate --force

php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild caches in production
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up
```

Restart queue workers after code deploy so they load new code:

```bash
sudo supervisorctl restart contentflow-worker:*
# or: php artisan queue:restart
```

## Database migrations

- Migrations are forward-only in MVP; always run `php artisan migrate --force` after pulling schema changes.
- Seeders are **not** re-run automatically on upgrade. Re-run only when release notes say so:

  ```bash
  php artisan db:seed --class=RolePermissionSeeder --force
  ```

  Idempotent seeders update roles/permissions without duplicating rows.

## Environment file changes

Compare `.env.example` with your `.env` when upgrading. New keys may be required (e.g. `DEMO_MODE`, `MEDIA_*`). Never commit real secrets.

After `.env` edits:

```bash
php artisan config:clear
php artisan config:cache
```

## Frontend assets

Always rebuild assets when `package.json` or front-end sources change:

```bash
npm ci && npm run build
```

## Installer lock

Upgrades on installed sites **keep** `storage/app/install.lock`. Do not remove it unless intentionally reinstalling ([INSTALLATION.md](INSTALLATION.md#partial-install-recovery)).

## Demo environments

After upgrade, reset demo content if release notes require fresh seed data:

```bash
php artisan demo:reset --force
```

## Verification after upgrade

```bash
php artisan test          # on CI/staging with MySQL
php artisan schedule:list
php artisan queue:failed    # should be empty or reviewed
```

Manual smoke: login, edit a post, public home page, scheduled publish cron.

## Rollback

1. Restore database backup.
2. Deploy previous release tag/commit.
3. Run `composer install` and `npm run build` for that version.
4. Clear/rebuild caches.
5. Restart queue workers.

If migrations were applied and cannot be reversed, restore DB from backup rather than using `migrate:rollback` in production unless release notes specify safe rollback steps.

## Related docs

- [DEPLOYMENT.md](DEPLOYMENT.md)
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
- [TESTING.md](TESTING.md)

# Package Manifest — ContentFlow CMS v1.0.0

## Included in distribution tarball

| Path | Purpose |
|---|---|
| `app/` | Application code |
| `bootstrap/` | Laravel bootstrap |
| `config/` | Configuration (no secrets) |
| `database/migrations/` | Schema |
| `database/seeders/` | Install/demo seeders |
| `database/factories/` | Test/seed factories |
| `public/` | Web root (includes built assets if pre-built) |
| `resources/` | Views, CSS, JS sources |
| `routes/` | Route definitions |
| `tests/` | Regression suite (optional for customer deploy) |
| `docs/` | Product documentation |
| `.env.example` | Environment template |
| `artisan` | CLI entry |
| `composer.json` / `composer.lock` | PHP dependencies lockfile |
| `package.json` / `package-lock.json` | Front-end build lockfile |
| `vite.config.js` | Vite config |
| `CHANGELOG.md` | Version history |
| `LICENSE.md` | MIT license |
| `README.md` | Project entry |

## Excluded (must not ship)

| Path | Reason |
|---|---|
| `.env` | Secrets |
| `.git/` | Version control metadata |
| `vendor/` | Run `composer install --no-dev` on target |
| `node_modules/` | Run `npm ci && npm run build` on build host |
| `storage/logs/*` | Local log files |
| `storage/app/install.lock` | Environment-specific install state |
| `storage/framework/cache/*` | Runtime cache |
| `storage/framework/sessions/*` | Session files |
| `storage/framework/views/*` | Compiled views |
| `public/hot` | Vite dev server marker |
| `.phpunit.result.cache` | Test cache |
| IDE folders (`.idea`, `.vscode`) | Dev tooling |

## Customer install flow

1. Extract tarball to server path
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci && npm run build` (if assets not pre-built)
4. Copy `.env.example` → `.env`, configure, `php artisan key:generate`
5. Open `/install` or run manual migrate/seed
6. Configure queue worker + cron per [docs/DEPLOYMENT.md](../docs/DEPLOYMENT.md)

## Pre-built assets option

For customers without Node.js on the server, build assets on CI and include `public/build/` in the tarball before running `.release/package.sh`.

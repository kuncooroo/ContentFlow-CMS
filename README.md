# ContentFlow CMS

Editorial-first Laravel modular monolith for blogs, pages, media, and site management.

**Version 1.0.0** — see [CHANGELOG.md](CHANGELOG.md) and [docs/RELEASE.md](docs/RELEASE.md).

## Stack

- Laravel 13.x
- PHP 8.4.x
- MySQL 8.4.x LTS (production target)
- Blade + Livewire + Tailwind CSS + Alpine.js

See [docs/VERSIONS.md](docs/VERSIONS.md) for verified runtime versions.

## Quick start

```bash
cp .env.example .env
php artisan key:generate

# Configure MySQL in .env, then:
php artisan migrate

composer install
npm install
npm run build

php artisan serve
```

Or use the setup script:

```bash
composer setup
```

For production-style setup, use the browser installer — see [docs/INSTALLATION.md](docs/INSTALLATION.md).

## Documentation

Full index: **[docs/README.md](docs/README.md)**

| Document | Purpose |
|---|---|
| [docs/INSTALLATION.md](docs/INSTALLATION.md) | Clean setup & browser installer |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | VPS, Nginx, queue, scheduler, mail |
| [docs/USER_GUIDE.md](docs/USER_GUIDE.md) | Public website for visitors |
| [docs/ADMIN_GUIDE.md](docs/ADMIN_GUIDE.md) | Admin workflows & roles |
| [docs/DEVELOPER_GUIDE.md](docs/DEVELOPER_GUIDE.md) | Code structure, Actions, tests |
| [docs/UPGRADE.md](docs/UPGRADE.md) | Release upgrades |
| [docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md) | Common issues |
| [docs/RELEASE.md](docs/RELEASE.md) | v1.0.0 release notes & QA checklist |
| [docs/TESTING.md](docs/TESTING.md) | Automated regression suite |
| [docs/SECURITY_CHECKLIST.md](docs/SECURITY_CHECKLIST.md) | Production hardening |
| [docs/PROJECT_STRUCTURE.md](docs/PROJECT_STRUCTURE.md) | Laravel folder layout |
| [docs/PRD.md](docs/PRD.md) | Product requirements |
| [docs/SRS.md](docs/SRS.md) | Software requirements |

## Fresh install (browser)

When `storage/app/install.lock` is absent, open `/install` to run the setup wizard. Details: [docs/INSTALLATION.md](docs/INSTALLATION.md).

## Demo mode

Set `DEMO_MODE=true` in `.env`, then seed demo content:

```bash
php artisan db:seed --class=DemoSeeder
php artisan demo:reset --force   # restore baseline later
```

Demo accounts (password `DemoPass123!`): `demo-superadmin@`, `demo-admin@`, `demo-editor@contentflow.test`. Keep `DEMO_MODE=false` in production.

## Development

```bash
composer dev    # serve + queue + logs + vite
composer test   # run PHPUnit
```

## Security

- Never commit `.env`.
- Set `APP_DEBUG=false` in production.
- Point the web server document root to `public/`.

See [docs/SECURITY_CHECKLIST.md](docs/SECURITY_CHECKLIST.md) and [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md).

## License

MIT — see [LICENSE.md](LICENSE.md).

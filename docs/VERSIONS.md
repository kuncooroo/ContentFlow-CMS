# Verified Runtime Versions

Recorded from the development environment on **2026-08-31**. Re-verify after dependency upgrades.

| Component | Verified version |
|---|---|
| PHP | 8.4.25 |
| Laravel Framework | 13.29.0 |
| Livewire | 4.4.x (see `composer.lock`) |
| Node.js | 22.20.0 |
| npm | 10.9.3 |
| Tailwind CSS | 4.x (see `package-lock.json`) |
| Vite | 7.x (see `package-lock.json`) |
| PHPUnit | 12.x (see `composer.lock`) |
| MySQL target | 8.4.x LTS (verify server separately) |
| **Product version** | **1.0.0** (see `config/contentflow.php`) |

## Verification commands

```bash
php -v
php artisan --version
node -v
npm -v
composer show livewire/livewire
```

## Notes

- PHPUnit tests use MySQL (`phpunit.xml`) because this environment has `pdo_mysql` but not `pdo_sqlite`.
- Run `php artisan migrate` before tests. Tests use `DatabaseTransactions` for isolation where needed.
- Production deployments should use MySQL per `docs/SRS.md` and `.env.example`.
- Do not invent versions in documentation—re-run the commands above when upgrading.

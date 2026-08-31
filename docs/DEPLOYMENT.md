# Deployment Guide — ContentFlow CMS

Production deployment target: **Linux VPS** with Nginx, PHP-FPM, and MySQL 8.4.x LTS.

## Pre-flight

1. Complete [INSTALLATION.md](INSTALLATION.md) or the browser installer on the server.
2. Set production environment values in `.env`:

   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.example

   SESSION_SECURE_COOKIE=true

   DEMO_MODE=false
   QUEUE_CONNECTION=database
   MAIL_MAILER=smtp
   ```

3. Run [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) before go-live.

## Web server (Nginx)

Point the vhost **document root** to the project `public/` directory only.

Example server block (adjust paths, PHP socket, and domain):

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.example;
    root /var/www/contentflow/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable HTTPS (Let's Encrypt or your CA) and redirect HTTP to HTTPS.

## PHP-FPM

- Use PHP 8.4 with required extensions (see [INSTALLATION.md](INSTALLATION.md)).
- Set reasonable `memory_limit` (256M+ recommended for admin uploads).
- Ensure `storage/` and `bootstrap/cache/` are writable by the FPM user.

## Application build on server

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

After `.env` changes, run `php artisan config:clear` (or rebuild caches).

## Queue worker

Password reset and other queued work use the `database` queue driver by default. See [QUEUE_OPERATIONS.md](QUEUE_OPERATIONS.md).

**Supervisor** example (`/etc/supervisor/conf.d/contentflow-worker.conf`):

```ini
[program:contentflow-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/contentflow/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/contentflow/storage/logs/worker.log
stopwaitsecs=3600
```

Reload Supervisor after changes:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start contentflow-worker:*
```

Inspect failures:

```bash
php artisan queue:failed
php artisan queue:retry all
```

## Scheduler (cron)

Scheduled posts publish via `content:publish-scheduled-posts` every minute. Add one cron entry for the deploy user:

```cron
* * * * * cd /var/www/contentflow && php artisan schedule:run >> /dev/null 2>&1
```

Verify:

```bash
php artisan schedule:list
```

## Mail

Configure SMTP (or your provider) in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.example
MAIL_FROM_NAME="${APP_NAME}"
```

Use real credentials only in `.env`, never in documentation or git. Test password reset after deployment.

## Media storage

```bash
php artisan storage:link
```

Uploaded files use the disk configured by `MEDIA_DISK` (default `public`). Ensure the linked `public/storage` path is web-accessible and included in backups.

## Backups

Minimum production backup set:

| Asset | Notes |
|---|---|
| MySQL database | Full dump (`mysqldump`) on a schedule |
| `storage/app/` | Uploads, install lock |
| `.env` | Store securely outside the repo |
| `storage/logs/` | Optional; rotate regularly |

Restore procedure: restore DB, restore `storage/app/`, verify `.env`, run `php artisan migrate --force` if schema version changed.

## Demo vs production

| Setting | Production customer site | Demo host |
|---|---|---|
| `DEMO_MODE` | `false` | `true` |
| `DEMO_ALLOW_IN_PRODUCTION` | unset / `false` | `true` only if demo runs on production env |
| Seeder | Installer or manual | `DemoSeeder` + periodic `demo:reset` |

Never enable demo mode on customer production unless hosting an intentional public demo.

## Post-deploy smoke test

- [ ] HTTPS loads without certificate warnings
- [ ] Login and admin dashboard
- [ ] Create/edit a draft post
- [ ] Public blog and page URLs
- [ ] Queue worker running (`supervisorctl status`)
- [ ] Cron active (`schedule:list` shows next run)
- [ ] Password reset email delivers (if SMTP enabled)

## Related docs

- [INSTALLATION.md](INSTALLATION.md)
- [QUEUE_OPERATIONS.md](QUEUE_OPERATIONS.md)
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
- [UPGRADE.md](UPGRADE.md)

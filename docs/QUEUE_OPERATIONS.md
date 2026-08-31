# Queue Operations

ContentFlow CMS queues password-reset emails and other asynchronous work on the default `database` queue connection.

## Running a queue worker

Start a worker in production (or locally when testing queued mail):

```bash
php artisan queue:work --tries=3
```

Use a process manager (Supervisor, systemd) so the worker restarts automatically.

## Inspecting failed jobs

When a queued job exhausts its retries, Laravel records it in the `failed_jobs` table:

```bash
php artisan queue:failed
php artisan queue:retry all
php artisan queue:flush
```

Mail and notification failures are also written to the application log with sensitive values redacted—never passwords, tokens, or SMTP credentials.

## Operator notes

- If the queue worker is stopped, password-reset emails remain in the `jobs` table until a worker processes them. The forgot-password screen still shows a generic success message; delivery is delayed, not lost (until jobs expire per `retry_after`).
- In local development, `QUEUE_CONNECTION=sync` sends mail immediately without a worker.
- In tests, mail uses the `array` or `log` driver; queued notifications can be asserted with `Notification::fake()`.

See [docs/DEPLOYMENT.md](DEPLOYMENT.md) for production setup details.

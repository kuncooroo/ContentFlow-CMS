# TASK-001 — Project Foundation

| Field | Value |
|---|---|
| **Task ID** | TASK-001 |
| **Title** | Project Foundation |
| **Phase** | Roadmap Phase 0 |
| **Estimate focus** | One focused foundation feature set |

---

## Objective

Initialize a stable Laravel modular-monolith application with verified runtime, MySQL connectivity, Blade + Livewire + Tailwind + Alpine baseline, queue/scheduler/logging/testing foundations, and folder conventions aligned with `docs/PROJECT_STRUCTURE.md`.

## Background

All later CMS modules depend on a bootable app, correct env handling, admin/public layout shells, and CI-ready tests. Do not implement domain CMS features here.

## Dependencies

- None (first task).
- Authority docs: `docs/SRS.md`, `docs/SYSTEM_DESIGN.md`, `docs/PROJECT_STRUCTURE.md`, `CURSOR.md`, `docs/ROADMAP.md` Phase 0.

## Files likely affected

```text
composer.json / composer.lock
package.json / lockfile
.env.example
bootstrap/
config/app.php, database.php, queue.php, session.php, cache.php, filesystems.php, logging.php
routes/web.php, admin.php, console.php
resources/views/layouts/admin.blade.php
resources/views/layouts/public.blade.php
resources/css/ / resources/js/
app/Providers/
phpunit.xml or pest.php
tests/Feature/Smoke*
docs/ note of verified versions (optional short VERSIONS.md)
```

## Database changes

- Ensure Laravel default infrastructure migrations can run: `migrations`, and prepare for later `sessions`, `cache`, `jobs`, `failed_jobs` (may enable in this task per SRS).
- No business CMS tables yet.

## Backend requirements

- Create/verify Laravel 13.x target project on PHP 8.4.x target (record **actual** installed versions; do not invent).
- Configure MySQL connection via `.env`.
- Database sessions recommended; database/file cache; database queue for MVP.
- Register route files: public `web.php`, stub `admin.php`, `console.php`.
- Storage link / public disk baseline.
- Logging baseline; scheduler entry ready (`schedule:run` documented later).
- Coding structure folders prepared per `docs/PROJECT_STRUCTURE.md` (empty `Actions/`, `Enums/`, `Livewire/Admin/`, etc. as needed—do not over-create empty noise).

## Frontend requirements

- Tailwind CSS build pipeline.
- Livewire installed and a trivial smoke component renders.
- Alpine via Livewire default (do not duplicate Alpine install unless required).
- Base admin shell (sidebar/topbar placeholders, empty content).
- Base public layout (header/footer placeholders).
- Vite (or project-standard) asset build succeeds.

## Validation rules

- N/A for domain forms.
- `.env.example` must document required keys without secrets.

## Authorization rules

- Admin routes group prepared behind `auth` middleware stub (may 302 until TASK-002).
- No permission matrix yet.

## Business rules

- One deployable Laravel app; no microservices.
- No Redis/Elasticsearch/Meilisearch required.
- No financial or SaaS scaffolding.

## Edge cases

- Missing `.env` fails clearly.
- `APP_KEY` generation documented.
- Asset build failure must not be ignored.

## Security considerations

- `.env` gitignored; never commit secrets.
- `APP_DEBUG` guidance for local vs production in `.env.example`.
- Web root must be `public/` (document).

## Testing requirements

- Smoke: app boots / homepage renders.
- Smoke: admin stub route behaves as designed (redirect or placeholder).
- Database connectivity test or migration dry-run.
- Livewire smoke component test.
- `php artisan test` (or Pest) runnable.

## Acceptance criteria

- [ ] Application boots without error.
- [ ] MySQL connection works.
- [ ] Frontend assets build.
- [ ] Blade public + admin layouts render.
- [ ] Livewire + Tailwind + Alpine smoke OK.
- [ ] Queue/session/cache config present for MVP.
- [ ] Actual versions recorded (not invented).
- [ ] Structure consistent with `docs/PROJECT_STRUCTURE.md`.

## Definition of Done

- Criteria above met; smoke tests pass; no domain CMS features merged accidentally; PR/task notes list verified PHP/Laravel/MySQL/Node versions.

# Developer Guide — ContentFlow CMS

Technical guide for contributors extending or maintaining the codebase.

## Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 13.x |
| PHP | 8.4.x (minimum 8.3) |
| Database | MySQL 8.4.x LTS |
| Admin UI | Livewire 4 + Blade + Tailwind CSS 4 |
| Public UI | Blade + Tailwind |
| Tests | PHPUnit 12 |

Verified versions: [VERSIONS.md](VERSIONS.md).

## Repository layout

See [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) for the full map. Summary:

```text
app/
  Actions/          Business use cases (preferred mutation layer)
  Http/             Controllers, middleware, form requests
  Livewire/Admin/   Admin UI components
  Models/           Eloquent models
  Policies/         Authorization rules
  Queries/          Read/query objects
  Services/         Cross-cutting services (audit, settings)
  Support/          Guards, permissions, install, demo helpers
database/
  migrations/
  seeders/
  factories/
resources/views/
  components/layouts/   admin, public, guest, install
  livewire/admin/       feature views
routes/
  web.php, admin.php, auth.php, install.php, console.php
tests/
  Feature/, Unit/, PureUnitTestCase.php
```

## Architecture conventions

```text
Livewire / Controller  →  authorize (Policy)  →  Action  →  Model / DB
```

- **Actions** encapsulate mutations, validation exceptions, transactions, and audit logging.
- **Policies** check permission constants from `PermissionNames`.
- **Queries** encapsulate list/filter/read logic for indexes.
- Avoid repository layers and speculative DTOs unless a use case justifies them.

### Adding a new admin feature

1. Migration + model (if new tables).
2. Policy with `ChecksPermissions` trait.
3. Action(s) for create/update/delete.
4. Livewire component(s) calling `$this->authorize()` then Action.
5. Routes in `routes/admin.php`.
6. Feature tests under `tests/Feature/`.

## Authorization

Permissions are string constants in `app/Support/Permissions/PermissionNames.php`. Roles sync in `RolePermissionSeeder`.

Livewire pattern:

```php
$this->authorize('update', $post);
app(UpdatePost::class)->handle(/* ... */);
```

Special guards:

- `SuperAdminProtection` — last Super Admin rules
- `DemoGuard` — demo mode restrictions ([config/demo.php](../config/demo.php))
- `CommentsGate` — public comment submission toggle

## Content status enums

- **Posts:** `draft`, `scheduled`, `published`, `archived` — transitions in `PostStatus::canTransitionTo()`
- **Pages:** `draft`, `published`, `archived` — `PageStatus::canTransitionTo()`

Scheduled publishing: `PublishScheduledPosts` action + `content:publish-scheduled-posts` command.

## Installer & demo

| Component | Location |
|---|---|
| Browser installer | `/install`, `app/Support/Install/*` |
| Install lock | `storage/app/install.lock` |
| Demo guard | `app/Support/Demo/DemoGuard.php` |
| Demo seed | `database/seeders/DemoSeeder.php` |
| Demo reset | `php artisan demo:reset` |

## Testing

Full guide: [TESTING.md](TESTING.md).

```bash
composer test
php artisan test --filter=Regression
```

- Feature tests use `DatabaseTransactions` where noted.
- `Tests\TestCase` auto-migrates and seeds roles/settings when needed.
- `Tests\LightweightTestCase` boots Laravel without DB seeding for isolated tests.

## Local development

```bash
composer setup    # first-time install
composer dev      # serve + queue + logs + vite
composer test
```

MySQL must run on `127.0.0.1:3306` with database `contentflow` (see `phpunit.xml`).

## Key artisan commands

| Command | Purpose |
|---|---|
| `php artisan migrate` | Apply migrations |
| `php artisan db:seed --class=TestingSeeder` | QA users |
| `php artisan db:seed --class=DemoSeeder` | Demo content |
| `php artisan demo:reset --force` | Reset demo baseline |
| `php artisan storage:link` | Public disk symlink |
| `php artisan queue:work` | Process queued jobs |
| `php artisan schedule:run` | Run scheduled tasks |
| `php artisan content:publish-scheduled-posts` | Publish due posts |

## Spec documents

Authoritative product/engineering specs (normalized under `docs/`):

| Document | Purpose |
|---|---|
| [PRD.md](PRD.md) | Product requirements |
| [SRS.md](SRS.md) | Software requirements |
| [SYSTEM_DESIGN.md](SYSTEM_DESIGN.md) | Architecture |
| [BUSINESS_FLOW.md](BUSINESS_FLOW.md) | Workflows and state rules |
| [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) | Code layout |

Do not document unimplemented features as shipped. Mark deferred items explicitly (see PRD/SRS out-of-scope sections).

## Security & operations

- [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md)
- [QUEUE_OPERATIONS.md](QUEUE_OPERATIONS.md)
- [DEPLOYMENT.md](DEPLOYMENT.md)

## Related task history

Implementation tasks are tracked in `.cursor/tasks/` (TASK-001 through TASK-027 roadmap).

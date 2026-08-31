# TASK-025 — Demo System

| Field | Value |
|---|---|
| **Task ID** | TASK-025 |
| **Title** | Demo System |
| **Phase** | Roadmap Phase 13 |
| **Estimate focus** | Demo mode, seed data, restrictions |

---

## Objective

Provide a commercial demo mode with seeded content/users, a visible demo banner, restricted destructive actions, and a documented reset process—without affecting production builds unless demo mode is enabled.

## Background

Prospects explore the CMS safely. Demo must not allow destroying Super Admin baseline, critical roles, or server configuration.

## Dependencies

- TASK-009–020 for seedable content.
- TASK-024 helpful for baseline roles/settings.
- TASK-004 permission matrix.

## Files likely affected

```text
config/contentflow.php or config/demo.php
app/Support/Demo/DemoGuard.php
app/Http/Middleware/EnsureNotDemoRestricted.php
database/seeders/DemoSeeder.php
resources/views/components/demo-banner.blade.php
routes/console.php (demo:reset command)
tests/Feature/Demo*
```

## Database changes

- None structural; seed data only in demo environments.

## Backend requirements

- `DEMO_MODE=true` env flag.
- DemoGuard blocks: delete last Super Admin, mutate critical system roles/permissions baseline, dangerous settings, password changes on protected accounts—as per ROADMAP.
- DemoSeeder: posts/pages/categories/tags/media/comments + Editor/Admin demo users with known passwords **only for demo env**.
- `php artisan demo:reset` (or equivalent) restores seed state.
- Middleware/Policy hooks on restricted Actions.

## Frontend requirements

- Persistent demo banner in admin (and optionally public).
- Friendly errors when action blocked.

## Validation rules

- N/A beyond blocked actions returning 403/422 with clear message.

## Authorization rules

- Demo restrictions apply in addition to normal Policies.
- Production mode: demo middleware inert.

## Business rules

- Demo visible and obvious.
- Resettable data.
- Production unaffected when flag false.
- No payment/demo billing.

## Edge cases

- Demo mode accidentally enabled in production—document risk; prefer hard fail if `APP_ENV=production` && DEMO_MODE without override.
- Partial reset failures.

## Security considerations

- Demo passwords only in demo docs/env samples—not production secrets.
- Restrict destructive server/config operations.
- Do not weaken real authorization in non-demo builds.

## Testing requirements

- Restricted actions denied in demo.
- Allowed editorial edits work.
- Reset restores expected counts/slugs.
- Production mode regression (restrictions off).

## Acceptance criteria

- [ ] Demo mode clearly indicated.
- [ ] Seeded CMS explorable.
- [ ] Prohibited destructive actions blocked.
- [ ] Reset works.
- [ ] Non-demo builds unaffected.

## Definition of Done

- Tests green; demo credentials documented only in demo section of docs (TASK-026); env sample shows DEMO_MODE=false by default.

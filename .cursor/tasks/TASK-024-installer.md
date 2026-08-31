# TASK-024 — Installer

| Field | Value |
|---|---|
| **Task ID** | TASK-024 |
| **Title** | Product Installer |
| **Phase** | Roadmap Phase 12 |
| **Estimate focus** | Fresh install wizard + lock |

---

## Objective

Provide a secure installation flow for source-code deployments: requirements check, env/DB config, migrations, Super Admin creation, initial site settings, and post-install lock so the installer cannot be re-run accidentally.

## Background

Commercial reusability requires repeatable installs without manual artisan tribal knowledge. Secrets must not be re-displayed after save.

## Dependencies

- Stable schema and seeders from prior tasks (roles, settings).
- TASK-023 preferred before calling install “done,” but installer can be built against stable migrations earlier if needed.

## Files likely affected

```text
app/Http/Controllers/Install/* or Livewire/Install/*
resources/views/install/*
routes/install.php
app/Support/Install/InstallLock.php
storage/app/install.lock (or equivalent)
database/seeders/*
tests/Feature/Install*
```

## Database changes

- Runs migrations; seeds roles/permissions + site_settings row.
- No new business domain tables beyond what product already has.

## Backend requirements

- Steps: requirements → app config → database config → migrate/seed → Super Admin → site settings → complete.
- Validate DB credentials before migrate.
- Failed migration ≠ success.
- Write install lock; block installer routes when locked.
- Create Super Admin with Super Admin role.
- Never echo secrets back in subsequent steps.

## Frontend requirements

- Simple multi-step wizard UI.
- Clear error states for failed requirements/DB.
- Success completion page with link to login.

## Validation rules

- Admin email unique/valid; password strong.
- DB host/name/user required; app URL/key rules.
- Site name/timezone required on settings step.

## Authorization rules

- Installer only available when unlocked (fresh app).
- After lock, only normal auth applies.

## Business rules

- Idempotent seed of system roles.
- One Super Admin at least after install.
- Lock prevents accidental reinstall wiping data.

## Edge cases

- Partial install crash recovery (document/re-run rules).
- Existing non-empty DB.
- Wrong permissions on `storage/` / `.env` writing.

## Security considerations

- CSRF on wizard posts.
- Restrict installer when locked.
- `.env` written safely; no world-readable secrets guidance.
- Do not log passwords.

## Testing requirements

- Fresh install happy path (as automated as feasible).
- Invalid DB credentials.
- Migration failure handling.
- Lock blocks re-entry.
- Super Admin can log in; seeds present.
- Duplicate install attempt rejected.

## Acceptance criteria

- [ ] Wizard completes fresh install.
- [ ] Super Admin + roles + settings created.
- [ ] Lock prevents re-install.
- [ ] Secrets not re-shown.
- [ ] Failures do not report false success.

## Definition of Done

- Feature tests for lock + critical steps; aligns with future `docs/INSTALLATION.md` (TASK-026).

# TASK-023 — Testing & Regression

| Field | Value |
|---|---|
| **Task ID** | TASK-023 |
| **Title** | Testing and Regression Suite |
| **Phase** | Roadmap Phase 11 |
| **Estimate focus** | Fill coverage gaps + regression gate |

---

## Objective

Complete the automated regression suite so all critical MVP acceptance areas from SRS TEST-* have reliable coverage, factories/seed helpers are stable, and CI can run the full suite.

## Background

Individual tasks already add tests; this task closes gaps, flaky tests, and cross-module regressions. Do not delete tests to make the build pass.

## Dependencies

- TASK-001–022.

## Files likely affected

```text
tests/Feature/** 
tests/Unit/**
tests/Browser/** (optional critical only)
database/factories/**
database/seeders/TestingSeeder.php (optional)
phpunit.xml / pest.php
.github/workflows/tests.yml or CI config (if present)
```

## Database changes

- None (test DB only).

## Backend requirements

- Ensure factories for all business models.
- Permission matrix regression tests.
- Scheduled publishing + visibility matrix.
- Transaction rollback cases for multi-record Actions.
- Livewire tests for critical admin flows.

## Frontend requirements

- Livewire component tests; optional Dusk/browser only for login + publish smoke if justified.

## Validation rules

- Cover core validation failures listed in SRS.

## Authorization rules

- TEST-004/005 style coverage for each protected module.

## Business rules

- Status transitions; comment visibility; menu; SEO fallback; settings; audit creation.

## Edge cases

- Flaky time-based schedule tests—freeze time.
- Parallel test isolation (RefreshDatabase).

## Security considerations

- Keep security tests from TASK-022 green.

## Testing requirements

This task is the testing phase. Minimum regression areas:

auth, users, roles, permissions, posts, pages, scheduling, categories, tags, media, comments, menus, SEO, settings, dashboard, audit, notifications, security, public visibility.

## Acceptance criteria

- [ ] Critical SRS test areas covered.
- [ ] Full suite passes locally/CI.
- [ ] No critical defects open.
- [ ] No tests removed merely to pass build.
- [ ] Factories/seed helpers documented briefly.

## Definition of Done

- `php artisan test` (or Pest) green; coverage gap list empty for critical paths; CI command documented.

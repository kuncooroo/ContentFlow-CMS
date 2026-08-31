# Testing Strategy — ContentFlow CMS

This document defines the QA strategy for ContentFlow CMS and maps it to current automated coverage.

Authoritative references:
- `docs/PRD.md`
- `docs/SRS.md`
- `docs/SYSTEM_DESIGN.md`
- `docs/BUSINESS_FLOW.md`
- `DATABASE(2).md` (database design reference currently at repository root)
- `CURSOR.md`

---

## 1) Testing goals

- Prove critical CMS workflows are correct, secure, and regression-safe.
- Enforce business rules (status transitions, role boundaries, publication visibility).
- Catch authorization, validation, transaction, and data-integrity failures early.
- Keep tests maintainable and fast enough for every PR.

---

## 2) Test types and scope

### Unit tests
- Target isolated logic without HTTP/UI concerns.
- Examples: value helpers, query helpers, listeners, domain actions with mocks/fakes.
- Current pattern: `tests/Unit/*`, `Tests\PureUnitTestCase`.

### Feature tests
- End-to-end request/response or Livewire component behavior with real policies/validation.
- Covers auth flows, admin CRUD, public pages, installer, demo mode.
- Current pattern: `tests/Feature/*`.

### Integration tests
- Verify integration across boundaries: DB + filesystem + queue/mail/events + scheduler.
- Strong focus areas:
  - media upload + persistence + storage cleanup on failure
  - scheduled publishing command + state transitions + audit
  - password reset notification dispatch + token lifecycle

### Authorization tests
- Matrix-based checks per role and per module.
- Verify both route access and action-level authorization (not only hidden UI).

### Validation tests
- Positive and negative-path tests for all state-changing endpoints.
- Include cross-field/business validation (dates, statuses, unique constraints, references).

### Database tests
- Schema invariants: indexes, FK behavior, uniqueness, constrained status transitions.
- Transaction rollback tests for multi-step operations.

### Business rule tests
- Encode PRD/BUSINESS_FLOW rules as tests (source of truth guardrails).

### Workflow tests
- Multi-step flows from actor trigger to final state:
  - installation
  - content lifecycle
  - moderation
  - user lifecycle
  - demo reset

### Financial calculation tests
- **Not applicable in MVP** (PRD scope excludes billing/e-commerce).
- Keep placeholder test namespace for future: `tests/Feature/Finance/*`.

### File upload tests
- Validate type, size, extension, metadata, and cleanup semantics.
- Include adversarial cases (double-extension, MIME mismatch, malformed image).

### Notification tests
- Verify password reset notifications and delivery-failure redaction behavior.

### API tests
- **Public API out of scope in MVP** (SRS section API).
- No public contract test suite required yet.
- If internal JSON endpoints are introduced later, add contract tests first.

### Regression tests
- Focus on previously fixed defects and brittle workflows.
- Keep narrow regression tests in `tests/Feature/Regression/*`.

---

## 3) Critical business workflows requiring automation

The following workflows must always have automated coverage:

1. Authentication lifecycle
   - login success/failure throttling
   - inactive-user rejection
   - logout session invalidation
   - password reset request/reset behavior

2. User & access-control governance
   - user create/edit/activate/deactivate
   - last active Super Admin protection
   - role assignment and permission synchronization

3. Post lifecycle
   - draft -> scheduled -> published -> archived transitions
   - author ownership boundaries
   - scheduled publish command behavior
   - public visibility gating by status/time

4. Page lifecycle
   - CRUD and status transitions
   - preview and publication visibility rules

5. Comment lifecycle
   - submission validation and pending default
   - moderation transitions (approve/reject/spam)
   - public shows approved only

6. Media lifecycle
   - upload validation/hardening
   - storage + DB consistency
   - reference-aware deletion behavior

7. Navigation and settings
   - menu update atomicity
   - site settings validation + cache invalidation

8. Installer
   - requirements/app/db/admin/settings flow
   - lock behavior
   - migration failure and duplicate install protections

9. Demo mode
   - protected actions blocked
   - reset restores baseline
   - production safety guard

10. Security controls
   - CSRF enforcement
   - XSS escaping
   - security headers
   - authorization matrix

11. Audit trail
   - sensitive actor actions generate expected audit events

---

## 4) Testing priority model

### P0 Critical (must pass before merge/release)
- Auth, authorization matrix, and inactive-user controls
- Post/page/comment public visibility and lifecycle transitions
- Super Admin protection
- Transaction rollback tests for multi-step critical actions
- Security suite (`CSRF`, `XSS`, upload hardening, session security)
- Installer lock + migration failure behavior

### P1 High
- Full CRUD validation negative paths by module
- Scheduler command behavior and idempotency
- Demo restrictions + reset reliability
- Audit event coverage for sensitive actions
- Notification behavior and failure logging

### P2 Medium
- Query behavior/performance-sensitive read logic
- Additional edge cases for optional fields and unusual locales/timezones
- Extended SEO and menu permutations

### P3 Optional
- Browser E2E smoke (Dusk/Playwright)
- Mutation testing
- Property-based fuzz tests for selected validators/parsers

---

## 5) Recommended Laravel test structure

```text
tests/
  Unit/
    Actions/
    Models/
    Queries/
    Services/
    Support/
  Feature/
    Auth/
    Authorization/
    Security/
    Admin/
      Users/
      Roles/
      Posts/
      Pages/
      Categories/
      Tags/
      Media/
      Menus/
      Settings/
      Dashboard/
      Audit/
      Seo/
      Search/
    Comments/
    Public/
    Install/
    Demo/
    Console/
    Notifications/
    Workflow/
    Regression/
  Fixtures/ (optional)
  Concerns/
  TestCase.php
  LightweightTestCase.php
  PureUnitTestCase.php
```

Conventions:
- Name tests with behavior statements: `test_author_cannot_publish_without_permission`.
- Prefer one business expectation per test method.
- Use factories and state helpers; avoid hard-coded setup duplication.
- Use `DatabaseTransactions` for feature tests unless command/process boundaries require full refresh.
- Freeze time (`Carbon::setTestNow`) for scheduling/temporal logic.

---

## 6) Execution strategy

Local:

```bash
composer test
php artisan test --filter=Security
php artisan test --filter=Regression
php artisan test tests/Feature/Auth
```

Recommended CI stages:
1. Fast lane: `Unit + Security (targeted)`
2. Full lane: all Feature/Integration
3. Release lane: full suite + regression focus + smoke commands

---

## 7) Current project coverage snapshot

Strong existing coverage:
- Auth: `tests/Feature/Auth/*`
- Authorization and role matrix: `tests/Feature/Authorization/*`
- Security: `tests/Feature/Security/*`
- Content lifecycle: posts/pages/comments tests in `tests/Feature/Admin/*` + `tests/Unit/Actions/*`
- Public visibility and SEO: `tests/Feature/Public/*`
- Installer + demo: `tests/Feature/Install/*`, `tests/Feature/Demo/*`
- Regression pack: `tests/Feature/Regression/*`

---

## 8) Missing tests identified in this project

### P0 gaps
- Database migration resilience test for media schema/index compatibility across MySQL variants.
  - Rationale: recent migration failures caused broad suite instability.

### P1 gaps
- Installer abuse/rate-limit behavior tests for all POST steps.
- Installer partial-failure recovery tests (admin created but finalize fails; retry behavior).
- Session revocation coverage:
  - verify deactivated user with existing session is blocked on next request (new behavior should be regression-tested consistently in CI).
- Upload integration edge case:
  - corrupted image file with allowed extension and forged MIME rejected.
- Demo reset failure-path test:
  - seeder failure inside transaction rolls back to pre-reset state.

### P2 gaps
- Dashboard authorization expectation test per role (explicitly document intended role access).
- Additional locale/timezone validation edge tests for installer/settings consistency.
- Cache invalidation assertions for settings/menu updates in broader workflow tests.
- Mail queue failure integration test (not only listener unit test).

### P3 optional gaps
- Browser-level E2E smoke for:
  - login -> publish post -> public visibility
  - comment submit -> moderation -> public appearance

---

## 9) Financial/API/Webhook note

- Financial calculation tests: not applicable in MVP scope.
- Public API tests: not required in MVP.
- Webhook tests: not required in MVP.

When those modules are approved, introduce dedicated suites before implementation freeze.

---

## 10) QA release gate (recommended)

Minimum release gate:
- All P0 tests pass.
- No open security HIGH/CRITICAL issues.
- Regression suite green.
- Installer and demo scenario checks pass on clean database.

Pre-release commands:

```bash
php artisan migrate:fresh --force
composer test
composer audit
```

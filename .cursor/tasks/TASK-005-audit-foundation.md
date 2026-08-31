# TASK-005 — Audit Foundation

| Field | Value |
|---|---|
| **Task ID** | TASK-005 |
| **Title** | Audit Foundation |
| **Phase** | Roadmap Phase 2/5 (logger first) |
| **Estimate focus** | Activity log persistence + logger service |

---

## Objective

Add `activity_logs` persistence and a small internal `ActivityLogger` (or `RecordActivity` Action) so later modules can record required administrative events without a heavy audit package.

## Background

MVP needs operational accountability (who did what to which subject), not compliance-grade immutable logging. Admin browse UI is TASK-019.

## Dependencies

- TASK-001, TASK-002, TASK-003 (actors are users).
- Wire into TASK-003/004 actions if already merged; otherwise provide API and backlog hooks.

## Files likely affected

```text
database/migrations/*_activity_logs*
app/Models/ActivityLog.php
app/Services/Audit/ActivityLogger.php
app/Actions/Audit/RecordActivity.php (optional alias)
tests/Unit/Services/Audit/*
tests/Feature/Audit/RecordActivityTest.php
```

## Database changes

- `activity_logs` per `docs/DATABASE.md`: actor, event, subject morph, properties JSON, `created_at` only (immutable).
- Indexes for actor/time, subject, event/time.

## Backend requirements

- `ActivityLogger::record(actor, event, subject, properties?)`.
- No updates/deletes of log rows in application code.
- Fail-soft vs fail-hard: prefer transaction-safe recording inside calling Action transactions when required by BUSINESS_FLOW; do not break primary action if product allows deferred logging—default: record in same transaction when Action already transactional.
- Event name constants or Enum-like string allowlist for known events.

## Frontend requirements

- None in this task (UI in TASK-019).

## Validation rules

- Event name required, bounded length.
- Properties must be JSON-serializable array; no secrets (passwords, tokens).

## Authorization rules

- Writing logs is internal (service), not a public endpoint.
- Reading deferred to TASK-019 permissions.

## Business rules

- Minimum events to support soon: user created/status changed, role assigned, later publish/archive, settings updated, comment moderated—implement recorder now; emit from modules as they land.
- Do not replace Laravel application/technical logs.

## Edge cases

- Null actor for system/scheduler events (nullable `actor_user_id` if DATABASE allows—follow DATABASE nullability).
- Large properties payloads—keep minimal.

## Security considerations

- Never log passwords, reset tokens, session IDs, mail credentials.
- Subject morph must not expose private file paths unnecessarily.

## Testing requirements

- Record creates immutable row.
- Properties stored correctly.
- No update path exists (feature expectation).
- Called from at least one User or Role action integration test if those Actions exist.

## Acceptance criteria

- [ ] `activity_logs` migrated.
- [ ] Logger service usable from Actions.
- [ ] No secrets in properties.
- [ ] Unit/feature tests pass.
- [ ] No full audit package added without justification.

## Definition of Done

- Service documented in code briefly; at least one real call site from Users or Roles; ready for content/settings modules to adopt.

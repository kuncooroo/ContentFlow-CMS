# TASK-012 — Scheduled Publishing

| Field | Value |
|---|---|
| **Task ID** | TASK-012 |
| **Title** | Scheduled Publishing |
| **Phase** | Roadmap Phase 4 |
| **Estimate focus** | Scheduler command + idempotent publish |

---

## Objective

Run scheduled posts publishing via Laravel Scheduler: find due Scheduled posts and publish them by reusing `PublishPost` (same business rules as manual publish).

## Background

Cron invokes `schedule:run` every minute on VPS. Do not duplicate publish rules inside the command.

## Dependencies

- TASK-010 (`PublishPost`, Scheduled status, `publish_at`).
- TASK-001 scheduler baseline.

## Files likely affected

```text
app/Console/Commands/PublishScheduledPostsCommand.php
app/Actions/Content/PublishScheduledPosts.php
routes/console.php (schedule definition)
tests/Feature/Console/PublishScheduledPosts*
```

## Database changes

- None; rely on `posts(status, publish_at)` index.

## Backend requirements

- Query: status=scheduled AND publish_at <= now (app timezone).
- For each (or chunk): call PublishPost Action.
- Idempotent: already Published posts skipped safely.
- Log failures per post without aborting entire run unreasonably (define: continue after logging).
- Schedule in `routes/console.php`: every minute command.
- Optional: pages if product schedules pages—only if DATABASE/PRD support; otherwise posts only.

## Frontend requirements

- None required; admin already shows Scheduled state from TASK-010.
- Optional dashboard count later (TASK-017).

## Validation rules

- N/A (system job). Ensure `publish_at` not null for Scheduled rows (enforced at schedule time in TASK-010).

## Authorization rules

- Runs as system/console; audit actor may be null or system user per DATABASE/TASK-005 conventions.
- Does not bypass validation of publish invariants inside Action.

## Business rules

- Same transition rules as manual publish.
- Must not publish Draft accidentally.
- Must not double-create side effects harmfully (audit may record each publish once).

## Edge cases

- Clock skew / timezone mismatches—use consistent app timezone.
- Large backlog of due posts—chunk processing.
- Action failure mid-run—other posts still processed.
- Worker not running—document operational dependency (not silent success in UI).

## Security considerations

- Command should not be web-accessible.
- No secrets in scheduler logs.

## Testing requirements

- Due scheduled post becomes Published.
- Future scheduled post untouched.
- Idempotent second run.
- Uses Action (feature test spy/mock or state assertions).
- Draft not published by command.

## Acceptance criteria

- [ ] Scheduler registers command.
- [ ] Due posts publish via shared Action.
- [ ] Idempotent behavior verified.
- [ ] Failures logged without corrupt partial post state.

## Definition of Done

- Tests pass; deployment note that cron must call `schedule:run`; overlaps documented with TASK-026.

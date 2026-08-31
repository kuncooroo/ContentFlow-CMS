# TASK-019 — Activity Log UI

| Field | Value |
|---|---|
| **Task ID** | TASK-019 |
| **Title** | Activity Log Admin UI |
| **Phase** | Roadmap Phase 5 |
| **Estimate focus** | Read-only audit trail browsing |

---

## Objective

Provide an admin UI to browse activity logs (actor, event, subject, timestamp, properties summary) for authorized users. Logs remain immutable.

## Background

Logger was added in TASK-005. This task is presentation + permissioned read access only.

## Dependencies

- TASK-005 with meaningful events from users/content/settings.
- TASK-004 permissions (e.g. `audit.view`).

## Files likely affected

```text
app/Livewire/Admin/Audit/Index.php
app/Queries/Audit/ActivityLogQuery.php
app/Policies/ActivityLogPolicy.php (viewAny)
resources/views/livewire/admin/audit/*
routes/admin.php
tests/Feature/Admin/Audit*
```

## Database changes

- None (use existing `activity_logs`).

## Backend requirements

- Paginated list; filter by event/actor/date if simple.
- Eager-load actor; present subject type/id humanely.
- No update/delete endpoints.
- Permission `audit.view` (name per seeder convention).

## Frontend requirements

- Audit index table.
- Detail drawer/modal for properties JSON (readable, not raw secrets—should already be clean).
- Empty state when no logs.

## Validation rules

- Filter inputs bounded.

## Authorization rules

- Only privileged roles view audit.
- Authors typically cannot view global audit unless permitted.

## Business rules

- Immutable; UI is read-only.
- Operational accountability, not SIEM.

## Edge cases

- Deleted actor user (show “deleted user” / null-safe).
- Large properties; unknown event types.

## Security considerations

- Authorize viewAny; do not expose logs publicly.
- Confirm properties never contain passwords/tokens (add regression assertion samples).

## Testing requirements

- Authorized list; unauthorized denied.
- Pagination; filter smoke.
- No mutation routes exist.

## Acceptance criteria

- [ ] Authorized users can browse logs.
- [ ] Actor/event/subject/time visible.
- [ ] Unauthorized access denied.
- [ ] No edit/delete capabilities.

## Definition of Done

- Livewire UI + tests; permission seeded on Admin/Super Admin.

# TASK-010 — Post Publishing Workflow

| Field | Value |
|---|---|
| **Task ID** | TASK-010 |
| **Title** | Post Publishing Workflow |
| **Phase** | Roadmap Phase 4 |
| **Estimate focus** | Publish, schedule, archive, restore |

---

## Objective

Implement explicit post status transitions: publish now, schedule, cancel schedule, archive, restore to draft, unpublish-to-draft if permitted—via Actions aligned with `docs/BUSINESS_FLOW.md`.

## Background

Status machine: Draft ↔ Scheduled → Published → Archived (and allowed restores). Authors lack publish/schedule by default.

## Dependencies

- TASK-009.
- TASK-004 permissions (`posts.publish`, `posts.schedule`, …).
- TASK-005 audit events for publish/archive.
- TASK-012 will automate due schedules using the same Publish Action.

## Files likely affected

```text
app/Actions/Content/PublishPost.php
app/Actions/Content/SchedulePost.php
app/Actions/Content/ArchivePost.php
app/Actions/Content/RestorePostToDraft.php
app/Actions/Content/UnpublishPost.php (if permitted)
app/Events/Content/PostPublished.php (only if listener exists)
app/Livewire/Admin/Posts/Edit.php (actions UI)
app/Policies/PostPolicy.php
tests/Feature/Admin/Posts/Publishing*
tests/Unit/Actions/Content/*
```

## Database changes

- None new if TASK-009 complete; ensure `status`, `publish_at` indexed per DATABASE.
- Possibly media_references rows when featuring media.

## Backend requirements

- Actions enforce valid transitions only; reject invalid ones.
- Publish now: set Published + publish_at (now) in transaction.
- Schedule: require future publish_at; status Scheduled.
- Cancel schedule → Draft (clear or keep publish_at per BUSINESS_FLOW).
- Archive from Published; restore Archived → Draft.
- Emit audit via ActivityLogger.
- Invalidate relevant caches if any.
- Preview authorized unpublished content for permitted users (route/component).

## Frontend requirements

- Explicit buttons: Publish, Schedule (datetime), Cancel schedule, Archive, Restore.
- Confirm destructive transitions.
- Disable actions lacking permission.
- Show schedule datetime validation errors.

## Validation rules

- Schedule: `publish_at` required, after now (timezone = app/site TZ when settings exist).
- Transition endpoints require existing post ID.

## Authorization rules

- `posts.publish` / `posts.schedule` / archive permissions per matrix.
- Author cannot publish/schedule by default.
- Preview limited to authorized roles.

## Business rules

- Follow BUSINESS_FLOW valid/invalid transitions strictly.
- No partial publish state (transaction).
- Scheduled must not appear on public site before due (scope on model).
- Archived excluded from normal public listing.

## Edge cases

- Schedule in the past → reject.
- Double-click publish → idempotent success.
- Publish without title/slug → already blocked by validation.
- Transition while another user edits.

## Security considerations

- Server-side transition checks; never accept arbitrary status string from client without Action.
- Preview URLs must not be publicly guessable without auth (signed URL or auth gate).

## Testing requirements

- Each valid transition; invalid transitions rejected.
- Author denied publish.
- Editor/Admin can publish.
- Draft/scheduled/published/archived visibility scopes.
- Audit row created.
- Transaction rollback simulation if feasible.

## Acceptance criteria

- [ ] All documented transitions work via Actions.
- [ ] Invalid transitions blocked.
- [ ] Permissions enforced; Author cannot publish by default.
- [ ] Audit events recorded for required actions.
- [ ] Preview works for authorized users only.

## Definition of Done

- Unit tests on Actions + feature/Livewire tests; BUSINESS_FLOW parity checklist attached in PR notes.

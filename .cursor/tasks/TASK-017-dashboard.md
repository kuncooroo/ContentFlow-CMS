# TASK-017 — Dashboard

| Field | Value |
|---|---|
| **Task ID** | TASK-017 |
| **Title** | Operational Dashboard |
| **Phase** | Roadmap Phase 7 |
| **Estimate focus** | Counts + recent/scheduled lists |

---

## Objective

Build the admin dashboard with operational metrics: draft/scheduled/published counts, pending comments, recent content, upcoming scheduled posts—permission-aware, no analytics platform.

## Background

Dashboard aggregates; it must not own business mutations. No revenue/traffic charts.

## Dependencies

- TASK-009–013 at minimum for meaningful metrics.
- TASK-004 for permission-scoped queries.

## Files likely affected

```text
app/Livewire/Admin/Dashboard/Overview.php
app/Queries/Dashboard/DashboardSummaryQuery.php
resources/views/livewire/admin/dashboard/*
routes/admin.php (dashboard home)
tests/Feature/Admin/Dashboard*
```

## Database changes

- None.

## Backend requirements

- Efficient aggregate queries (avoid N+1).
- Author sees scoped metrics (own posts) if PRD requires.
- Editor/Admin see broader operational counts per permissions.
- Upcoming scheduled ordered by `publish_at`.
- Pending comment count.

## Frontend requirements

- Simple dashboard: metric summary + tables for recent content and scheduled queue + link shortcuts.
- No decorative chart library required.
- Empty states when zero data.

## Validation rules

- N/A.

## Authorization rules

- Auth required; metrics filtered by what user can view.
- Do not expose pending comments count to roles lacking comment permissions.

## Business rules

- Counts match DB for visible scope.
- No financial/traffic analytics.

## Edge cases

- User with minimal permissions; large tables (limit recent lists); timezone display for schedules.

## Security considerations

- Permission-aware queries only; no raw enumeration of private drafts to unauthorized roles.

## Testing requirements

- Metric correctness fixtures.
- Author vs Editor scoping.
- Scheduled ordering.
- Permission-filtered pending comments.
- Basic N+1 awareness (assert query count reasonable optional).

## Acceptance criteria

- [ ] Dashboard shows accurate scoped counts.
- [ ] Recent and scheduled lists correct.
- [ ] Unauthorized data hidden.
- [ ] No analytics/revenue widgets.

## Definition of Done

- Livewire dashboard is admin home; tests cover scoping; Query object used if complexity warrants.

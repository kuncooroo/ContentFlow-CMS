# TASK-018 — Search & Filters

| Field | Value |
|---|---|
| **Task ID** | TASK-018 |
| **Title** | Admin Search and Filters |
| **Phase** | Roadmap Phase 5 |
| **Estimate focus** | List search/filter/pagination |

---

## Objective

Add MySQL/Eloquent search and filtering across major admin indexes: posts, pages, media, comments (and users if needed), with pagination and authorization-safe results.

## Background

MVP uses MySQL LIKE/indexed filters—not Elasticsearch. Search must not bypass policies.

## Dependencies

- List UIs from TASK-003, 006–011, 013, 008.
- Prefer Query objects where filters grow complex.

## Files likely affected

```text
app/Queries/Content/PostIndexQuery.php
app/Queries/Content/PageIndexQuery.php
app/Queries/Media/MediaLibraryQuery.php
app/Queries/Comments/CommentModerationQuery.php
app/Livewire/Admin/**/Index.php (wire filters)
tests/Feature/Admin/Search*
```

## Database changes

- None required if indexes from DATABASE already exist; add only if a proven filter needs it.

## Backend requirements

- Search by title/name/filename as appropriate.
- Filters: status, author, category, date ranges where PRD requires.
- Pagination always on large lists.
- Results constrained by Policy scopes (Authors see own posts).

## Frontend requirements

- Search input + filter controls on indexes.
- Clear filters control.
- Preserve filters in Livewire state; empty result state.

## Validation rules

- Bound filter enums to known statuses.
- Sanitize search string length.

## Authorization rules

- Same as underlying resource view permissions.
- Search cannot return unauthorized rows.

## Business rules

- Deterministic ordering with stable secondary key (id) for pagination.
- No external search engine.

## Edge cases

- Empty query returns default list; special LIKE characters; huge result sets paginated; combined filters yielding zero.

## Security considerations

- Parameter binding only (Eloquent); no raw concatenated SQL.
- Do not search private fields (password).

## Testing requirements

- Match by title; status filter; authorization scoping; pagination; empty results.

## Acceptance criteria

- [ ] Posts/pages/media/comments indexes support search/filter.
- [ ] Pagination works.
- [ ] Authorization respected.
- [ ] No external search dependency.

## Definition of Done

- Query objects or equivalent documented in code; feature tests pass for each major index.

# TASK-007 — Tags

| Field | Value |
|---|---|
| **Task ID** | TASK-007 |
| **Title** | Tags |
| **Phase** | Roadmap Phase 3 |
| **Estimate focus** | Tag taxonomy CRUD |

---

## Objective

Implement tags master data: list, create, edit, delete with unique slugs. Post–tag pivot may land with TASK-009.

## Background

Tags are flat labels for posts. Keep implementation parallel to categories but without hierarchical assumptions.

## Dependencies

- TASK-001, TASK-004.
- TASK-005 optional for audit.
- TASK-006 optional (patterns reuse only).

## Files likely affected

```text
database/migrations/*_tags* (optional *_post_tag*)
app/Models/Tag.php
app/Policies/TagPolicy.php
app/Livewire/Admin/Taxonomy/Tags/*
routes/admin.php
database/factories/TagFactory.php
tests/Feature/Admin/Tags*
```

## Database changes

- `tags`: name, slug unique, timestamps per DATABASE.
- `post_tag` pivot when posts wiring lands (TASK-009) if not created here.

## Backend requirements

- CRUD Livewire admin.
- Unique slug generation/override.
- Delete rules when attached to posts (block or detach per BUSINESS_FLOW—prefer explicit detach or prevent; no silent integrity break).
- Permissions `tags.*`.

## Frontend requirements

- Tags index, create/edit, delete confirm.
- Simple admin UX consistent with categories.

## Validation rules

- Name required; slug required unique URL-safe.

## Authorization rules

- Policy on all actions; UI mirrors permissions.

## Business rules

- No soft deletes.
- Slug unique among tags.

## Edge cases

- Duplicate slug; tag in use by posts; empty list.

## Security considerations

- Authorized mutations only; escaped output.

## Testing requirements

- CRUD; slug uniqueness; unauthorized deny; in-use delete behavior.

## Acceptance criteria

- [ ] Tag CRUD works.
- [ ] Unique slug enforced.
- [ ] Permissions enforced.
- [ ] Relationship-safe delete behavior defined and tested once pivot exists.

## Definition of Done

- Tests pass; permissions seeded; factories ready for post association tasks.

# TASK-006 — Categories

| Field | Value |
|---|---|
| **Task ID** | TASK-006 |
| **Title** | Categories |
| **Phase** | Roadmap Phase 3 |
| **Estimate focus** | Category taxonomy CRUD |

---

## Objective

Implement categories master data: list, create, edit, delete with unique slugs and relationship protection for posts.

## Background

Categories classify posts. Pivot `post_category` may be created here or with TASK-009; deletion must not orphan integrity rules.

## Dependencies

- TASK-001, TASK-004 (permissions/policies).
- TASK-005 optional for audit on create/update/delete.

## Files likely affected

```text
database/migrations/*_categories* (optional *_post_category*)
app/Models/Category.php
app/Policies/CategoryPolicy.php
app/Livewire/Admin/Taxonomy/Categories/*
routes/admin.php
database/factories/CategoryFactory.php
tests/Feature/Admin/Categories*
```

## Database changes

- `categories` table: name, slug unique, description optional, timestamps per DATABASE.
- Optionally `post_category` pivot if posts exist; otherwise add pivot in TASK-009 and enforce delete rules then.

## Backend requirements

- CRUD via Livewire.
- Auto-generate unique slug from name; allow manual override with uniqueness check.
- Delete: block or require reassignment if posts attached (per BUSINESS_FLOW / DATABASE RESTRICT rules).
- Policy permissions e.g. `categories.view|create|update|delete`.

## Frontend requirements

- Admin categories index + form.
- Slug field editable.
- Delete confirmation; clear error if in use.

## Validation rules

- Name required.
- Slug required, unique, URL-safe.
- Description optional max length.

## Authorization rules

- Server-side Policy on all mutations.
- UI hides unauthorized actions.

## Business rules

- Slug uniqueness global for categories.
- No soft deletes.
- Deleting category with posts must follow documented integrity behavior (prevent silent data loss).

## Edge cases

- Duplicate slug.
- Empty name.
- Concurrent slug edits.
- Category used by many posts.

## Security considerations

- Authorize every delete.
- Escape output in admin list.

## Testing requirements

- CRUD; slug uniqueness; delete protection when related posts exist (once pivot exists).
- Unauthorized denied.

## Acceptance criteria

- [ ] Categories can be created/edited/listed.
- [ ] Unique slug enforced.
- [ ] Unsafe delete prevented per rules.
- [ ] Permissions enforced.

## Definition of Done

- Tests green; seeder/factory available; permission names seeded/updated in RolePermissionSeeder.

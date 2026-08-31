# TASK-014 — Menus

| Field | Value |
|---|---|
| **Task ID** | TASK-014 |
| **Title** | Menu Builder |
| **Phase** | Roadmap Phase 3/5 |
| **Estimate focus** | Menus + ordered menu items |

---

## Objective

Implement navigation menus: menu containers and ordered menu items targeting pages, posts, categories, or custom URLs; persist order; expose data for public rendering.

## Background

Editors change site navigation without developers. Public rendering of menus is completed with TASK-020; this task delivers admin builder + persistence.

## Dependencies

- TASK-004, TASK-006, TASK-009, TASK-011 (targets).
- TASK-005 audit on structure updates.

## Files likely affected

```text
database/migrations/*_menus* *_menu_items*
app/Models/Menu.php, MenuItem.php
app/Actions/Navigation/UpdateMenuStructure.php
app/Policies/MenuPolicy.php
app/Livewire/Admin/Menus/*
tests/Feature/Admin/Menus*
```

## Database changes

- `menus` (key unique, name, …).
- `menu_items` (menu_id, label, type/target fields, position, parent if nested—follow DATABASE; MVP depth per docs).

## Backend requirements

- CRUD menu containers (at least primary/header key).
- UpdateMenuStructure Action: replace/reorder items in a transaction.
- Validate targets exist for page/post/category types; custom URL validated.
- Policy `menus.manage`.

## Frontend requirements

- Menu editor UI: add item, choose type/target, reorder (up/down or drag if simple).
- Save persists positions.
- Clear validation errors for invalid targets.

## Validation rules

- Label required; type required; target required per type.
- Custom URL: valid URL or path format per product rules.
- Position integer >= 0.

## Authorization rules

- Only authorized roles edit menus.
- Public read of published menu structure is not an admin permission issue.

## Business rules

- Order stored and stable.
- Do not point menu items at Draft content for public nav (filter at render time in TASK-020).
- Updating structure is atomic.

## Edge cases

- Empty menu; deleted target page; duplicate positions (normalize on save); max items practical limit.

## Security considerations

- Sanitize custom URLs (reject `javascript:` etc.).
- Authorize all writes.

## Testing requirements

- Persist items/order; invalid target rejected; unauthorized denied; transaction keeps consistent structure.

## Acceptance criteria

- [ ] Menus and items can be managed in admin.
- [ ] Order persists.
- [ ] Targets validated.
- [ ] Permissions enforced.
- [ ] Data shape ready for public nav.

## Definition of Done

- Action + Livewire + tests; sample primary menu seeder optional for demo later.

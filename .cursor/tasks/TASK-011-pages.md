# TASK-011 — Pages

| Field | Value |
|---|---|
| **Task ID** | TASK-011 |
| **Title** | Pages |
| **Phase** | Roadmap Phase 4 |
| **Estimate focus** | Page CRUD + publish/archive lifecycle |

---

## Objective

Implement static pages management: list, create, edit, draft/published/archived lifecycle, preview, unique slugs, optional SEO fields hook, and public-ready scopes.

## Background

Pages are separate from posts (no categories/tags requirement). Reuse Action patterns from posts where status rules match DATABASE/BUSINESS_FLOW for pages.

## Dependencies

- TASK-004, TASK-008, TASK-010 patterns (publish Actions style).
- TASK-005 audit.
- TASK-015 may complete SEO field UX if columns already on `pages`.

## Files likely affected

```text
database/migrations/*_pages*
app/Models/Page.php
app/Enums/PageStatus.php
app/Actions/Content/PublishPage.php, ArchivePage.php, ...
app/Policies/PagePolicy.php
app/Livewire/Admin/Pages/*
routes/admin.php
tests/Feature/Admin/Pages*
```

## Database changes

- `pages` table per DATABASE (title, slug unique, body, status, publish fields as specified, og/seo columns, timestamps).
- No post pivots.

## Backend requirements

- CRUD + status Actions analogous to posts where product matches.
- Unique slug; Policy permissions `pages.*`.
- Published scope for public delivery.
- Optional featured/OG media FKs with reference safety.

## Frontend requirements

- Pages index/create/edit Livewire.
- Publish/archive/restore controls per permissions.
- Preview for authorized users.

## Validation rules

- Title/slug/body rules; slug unique among pages.
- Status only via Actions.

## Authorization rules

- Permission matrix for pages; ownership if PRD assigns page authors—follow PRD (often editors/admins manage pages).

## Business rules

- Draft not public; Published public by slug route; Archived not in normal nav/listing.
- No soft deletes.

## Edge cases

- Slug collision with posts is OK (different tables/routes) unless product requires global uniqueness across types—follow DATABASE (separate).
- Delete page referenced by menu items (TASK-014)—restrict or cascade rules per DATABASE.

## Security considerations

- Authorize mutations; safe preview; XSS at render time.

## Testing requirements

- CRUD; publish/archive; draft hidden scope; permissions; slug unique.

## Acceptance criteria

- [ ] Pages CRUD works.
- [ ] Lifecycle transitions enforced.
- [ ] Draft/Archived not publicly listed; Published is.
- [ ] Permissions enforced.

## Definition of Done

- Tests green; factory/seeder helpers; menu integration ready for TASK-014.

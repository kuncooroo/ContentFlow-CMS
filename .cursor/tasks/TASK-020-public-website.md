# TASK-020 — Public Website

| Field | Value |
|---|---|
| **Task ID** | TASK-020 |
| **Title** | Public Website Content Delivery |
| **Phase** | Roadmap Phase 4/5 public surface |
| **Estimate focus** | Public routes + Blade rendering |

---

## Objective

Deliver the public website: homepage, published posts/pages, category/tag archives, navigation from menus, approved comments display, SEO meta via resolver, using site settings branding.

## Background

Public visitors never see Draft/Scheduled (before due)/Archived in normal listings. No public admin API.

## Dependencies

- TASK-010–016, TASK-014 menus, TASK-013 comments, TASK-015 SEO, TASK-016 settings.

## Files likely affected

```text
routes/web.php
app/Http/Controllers/Public/*
resources/views/public/**
resources/views/layouts/public.blade.php
resources/views/components/seo-meta.blade.php
tests/Feature/Public/*
```

## Database changes

- None.

## Backend requirements

- Controllers (or sparse Livewire) querying published scopes only.
- Post show by slug; page show by slug.
- Category/tag archive of published posts.
- Menu render helper filtering unpublished targets.
- 404 for unknown/unauthorized-unpublished slugs (do not leak drafts via different error).
- Pagination on archives/lists.

## Frontend requirements

- Public layout with logo/site name from settings.
- Post/page templates; comment list + form (TASK-013).
- Nav from primary menu.
- Responsive basic layout per UI_UX.
- Meta tags via SEO component.

## Validation rules

- Public comment validation already in TASK-013.

## Authorization rules

- Public read of published content only.
- Preview remains auth-only from admin tasks.

## Business rules

- Draft/Scheduled-before-due/Archived not in normal public listing.
- Only Approved comments.
- SEO fallbacks applied.
- Menus reflect latest saved structure.

## Edge cases

- Empty site (no posts); conflicting slugs across types (separate routes); disabled comments; missing menu.

## Security considerations

- Escape output; comments no raw HTML.
- No admin routes exposed.
- Rate limit comment submit.
- Do not disclose existence of draft via distinct errors if SRS requires uniform 404.

## Testing requirements

- Published visible; draft 404; scheduled before due 404; after publish visible.
- Archived excluded from listing.
- Approved comments only.
- Menu omits invalid targets.
- SEO tags present.

## Acceptance criteria

- [ ] Public can browse published content.
- [ ] Unpublished states hidden correctly.
- [ ] Nav, SEO, settings branding applied.
- [ ] Comments rules honored.

## Definition of Done

- Feature tests for visibility matrix; templates usable on mobile width smoke check.

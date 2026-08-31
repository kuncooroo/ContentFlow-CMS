# TASK-009 — Posts CRUD

| Field | Value |
|---|---|
| **Task ID** | TASK-009 |
| **Title** | Posts CRUD (Draft-focused) |
| **Phase** | Roadmap Phase 4 (create/edit portion) |
| **Estimate focus** | Create, edit, list posts as Draft |

---

## Objective

Implement post administration for creating and editing posts primarily in Draft: fields, slug, author, excerpt/body, featured media, category/tag relations, list UI. Publishing transitions are TASK-010.

## Background

Posts are the core editorial entity. Keep create/update simple; do not implement full state machine beyond default Draft in this task (optional save-as-draft only).

## Dependencies

- TASK-003, TASK-004, TASK-006, TASK-007, TASK-008.
- TASK-005 for create/update audit if required.

## Files likely affected

```text
database/migrations/*_posts* *_post_category* *_post_tag*
app/Models/Post.php
app/Enums/PostStatus.php
app/Policies/PostPolicy.php
app/Livewire/Admin/Posts/Index.php, Edit.php, Create.php
app/Queries/Content/PostIndexQuery.php (optional)
routes/admin.php
database/factories/PostFactory.php
tests/Feature/Admin/Posts/Crud*
```

## Database changes

- `posts` table per DATABASE (title, slug unique, body, excerpt, status default `draft`, author_id, publish_at nullable, featured_media_id, SEO columns may wait for TASK-015—add placeholders only if schema requires NOT NULL defaults).
- Pivots `post_category`, `post_tag`.

## Backend requirements

- Create/update post with validated data; default status Draft.
- Unique slug generation.
- Attach categories/tags; set featured media FK with existence checks.
- Author ownership: Authors manage own posts only (Policy).
- Eager-load list relations; prevent N+1.
- No Publish/Schedule/Archive Actions yet (stub buttons OK if disabled).

## Frontend requirements

- Posts index (pagination).
- Create/edit form: title, slug, body (rich text approach per UI_UX—safe rendering later), excerpt, categories, tags, featured image picker from media library.
- Status shown as Draft.

## Validation rules

- Title required; slug unique; body rules per PRD.
- Category/tag IDs must exist.
- featured_media_id must exist in media when set.
- author_id not arbitrarily set by Author to another user.

## Authorization rules

- `posts.view|create|update|delete` as applicable.
- Author: own posts only for update/delete.
- Editor/Admin: broader per seed.

## Business rules

- New posts start as Draft.
- Draft must not be publicly visible (enforce when public routes exist; add model scope `published()` now).
- No soft deletes; archive is TASK-010.

## Edge cases

- Duplicate slug; missing media; empty body if allowed; concurrent edits (last write wins OK for MVP).

## Security considerations

- Authorize updates; escape titles in lists; rich content storage XSS handled at render time (public TASK-020 / security TASK-022).
- Mass assignment guard.

## Testing requirements

- Create/edit draft; slug unique; relations save; Author ownership deny; unauthorized deny.
- Factory creates draft posts.

## Acceptance criteria

- [ ] Authorized users can create/edit draft posts.
- [ ] Slug unique; categories/tags/featured media attach.
- [ ] Author ownership enforced.
- [ ] List paginates without N+1 obvious failures.
- [ ] Status remains Draft without publish Actions.

## Definition of Done

- Schema + Livewire + Policy + tests; ready for TASK-010 Actions without rewrite.

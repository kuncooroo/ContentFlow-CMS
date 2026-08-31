# TASK-013 — Comments

| Field | Value |
|---|---|
| **Task ID** | TASK-013 |
| **Title** | Comments & Moderation |
| **Phase** | Roadmap Phase 5 |
| **Estimate focus** | Public submit + admin moderation |

---

## Objective

Implement comment submission on published posts and admin moderation (Pending, Approved, Spam, Rejected). Only Approved comments are public.

## Background

New comments default to Pending unless site settings define an allowed MVP alternative (default Pending). Complex approval chains are out of scope.

## Dependencies

- TASK-009/010 (published posts), TASK-004, TASK-005.
- TASK-016 for `comments_enabled` switch (gate on setting when available; default enabled until settings land).

## Files likely affected

```text
database/migrations/*_comments*
app/Models/Comment.php
app/Enums/CommentStatus.php
app/Actions/Comments/SubmitComment.php
app/Actions/Comments/ModerateComment.php
app/Policies/CommentPolicy.php
app/Livewire/Admin/Comments/*
app/Http/Controllers/Public/CommentController.php or Livewire Public
tests/Feature/Comments*
```

## Database changes

- `comments` per DATABASE: post_id, author name/email/body, status default pending, timestamps, moderator fields if specified.
- FK to posts RESTRICT on delete.

## Backend requirements

- SubmitComment on published posts only; status Pending; rate limit.
- ModerateComment transitions per BUSINESS_FLOW.
- Public query scope: Approved only.
- Respect comments_enabled when settings exist.
- Audit moderation events.

## Frontend requirements

- Public comment form on post show (when enabled).
- Admin moderation queue/list with status filters (filter polish TASK-018).
- Actions: Approve, Spam, Reject (and reverse where allowed).

## Validation rules

- Name/email/body required as per PRD; email format; body max length; honeypot/rate limit optional.
- Cannot comment on draft/scheduled/archived posts.

## Authorization rules

- Public submit: guest or auth per PRD (typically guest allowed).
- Moderation: `comments.moderate` (or equivalent) permission.
- Authors do not moderate unless permitted.

## Business rules

- Default Pending; only Approved public.
- Status transitions follow BUSINESS_FLOW.
- Spam/Rejected not public.

## Edge cases

- Comments disabled globally.
- Post unpublished after comments exist.
- Rapid duplicate submissions.
- XSS in comment body—store raw, escape on output (no arbitrary HTML).

## Security considerations

- Escape all comment output by default.
- Rate limit submit endpoint.
- Moderate authorization server-side.
- Do not leak moderator notes publicly.

## Testing requirements

- Submit → Pending; not public.
- Approve → public; Spam/Rejected not public.
- Invalid transitions; unauthorized moderate denied.
- Disabled comments rejected.

## Acceptance criteria

- [ ] Public can submit comments on published posts when enabled.
- [ ] Default Pending; only Approved visible publicly.
- [ ] Moderators can change status per rules.
- [ ] Permissions and rate limits enforced.

## Definition of Done

- Actions + UI + tests; dashboard pending count ready for TASK-017.

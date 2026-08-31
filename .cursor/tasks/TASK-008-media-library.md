# TASK-008 — Media Library

| Field | Value |
|---|---|
| **Task ID** | TASK-008 |
| **Title** | Media Library |
| **Phase** | Roadmap Phase 3 |
| **Estimate focus** | Upload, metadata, safe delete |

---

## Objective

Implement media library: upload via Laravel filesystem, store metadata, list/search basics, alt text, and deletion guards for referenced media.

## Background

Media is referenced by posts, pages, and settings. Business logic must use disk abstraction (local for MVP), never hard-coded OS paths.

## Dependencies

- TASK-001, TASK-002, TASK-004.
- TASK-005 optional audit on upload/delete.

## Files likely affected

```text
database/migrations/*_media* *_media_references*
app/Models/Media.php, MediaReference.php
app/Enums or constants for allowed MIME
app/Actions/Media/StoreUploadedMedia.php
app/Actions/Media/DeleteReferencedMedia.php
app/Policies/MediaPolicy.php
app/Livewire/Admin/Media/*
config/filesystems.php
tests/Feature/Admin/Media*
```

## Database changes

- `media` table per DATABASE (disk, path, filename, mime, size, dimensions, alt, uploader, timestamps).
- Unique `(disk, path)`.
- `media_references` for generic usages (optional now; required before unsafe deletes across owners).

## Backend requirements

- StoreUploadedMedia: validate MIME/size, store file, create DB row in transaction.
- DeleteReferencedMedia: refuse delete when referenced (featured/OG/settings/references) unless product allows forced detach—default refuse with clear error.
- No silent overwrite of existing path.
- Public disk for public media; storage:link documented.
- Policy `media.*`.

## Frontend requirements

- Media library grid/list with upload control.
- Edit alt text / metadata.
- Delete with error if referenced.
- Basic filename/search filter (full search polish in TASK-018).

## Validation rules

- File required on upload; allowlist MIME (images + agreed types from SRS/PRD).
- Max size from config.
- Alt text optional string max length.
- Reject executables / PHP/JS uploads.

## Authorization rules

- Only permitted roles upload/delete.
- Authors may be limited to own uploads if PRD requires—follow PRD ownership rules.

## Business rules

- Filesystem abstraction only.
- Referenced media cannot be silently deleted.
- Duplicate path handling: reject or generate unique path—never clobber.

## Edge cases

- Oversize file; invalid MIME; zero-byte; missing storage permissions.
- Delete while referenced by draft post.
- Partial upload failure rollback (DB + file).

## Security considerations

- MIME + extension allowlist; do not trust client Content-Type alone when possible.
- Store outside executable web paths except intended public disk.
- No SVG XSS blindly if SVG allowed—prefer disallow SVG in MVP unless reviewed.
- Authorize downloads/admin views.

## Testing requirements

- Valid upload; invalid MIME; oversize.
- Metadata/alt save.
- Delete blocked when referenced (use fake reference).
- Unauthorized denied.
- Unique path behavior.

## Acceptance criteria

- [ ] Valid files upload and appear in library.
- [ ] Invalid files rejected.
- [ ] Alt/metadata persist.
- [ ] Referenced media delete protected.
- [ ] Permissions enforced.
- [ ] No hard-coded absolute server paths in domain code.

## Definition of Done

- Actions + Policy + Livewire + tests green; `.env.example` notes `FILESYSTEM_DISK`; permissions seeded.

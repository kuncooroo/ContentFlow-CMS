# TASK-015 — SEO

| Field | Value |
|---|---|
| **Task ID** | TASK-015 |
| **Title** | SEO Metadata & Fallbacks |
| **Phase** | Roadmap Phase 5 |
| **Estimate focus** | Per-content SEO + resolver |

---

## Objective

Implement per-post/per-page SEO fields and a resolver that falls back to site default SEO (from settings when available) for title, meta description, robots, and OG image.

## Background

SEO is embedded columns on content (not a separate SEO table). Global defaults live on `site_settings` (TASK-016). This task can add content fields + resolver with temporary hardcoded defaults until settings land.

## Dependencies

- TASK-009, TASK-011.
- TASK-016 for full default source (integrate when ready).
- TASK-008 for OG media references.

## Files likely affected

```text
Migrations alter posts/pages SEO columns if not present
app/Support/Seo/SeoResolver.php
app/Livewire/Admin/Posts/Edit.php (SEO panel)
app/Livewire/Admin/Pages/Edit.php
resources/views/components/seo-meta.blade.php
tests/Unit/Support/Seo/SeoResolverTest.php
tests/Feature/Admin/Seo*
```

## Database changes

- Ensure posts/pages SEO columns per DATABASE (`meta_title`, `meta_description`, `robots_index`, `og_media_id`, etc.).
- No EAV SEO table.

## Backend requirements

- SeoResolver: content override → site defaults → sensible app fallback.
- Save SEO fields with content update authorization.
- robots_index boolean/default behavior per DATABASE.

## Frontend requirements

- SEO section on post/page edit forms.
- Admin help text for fallback behavior.
- Blade component outputting meta tags for public layouts (used in TASK-020).

## Validation rules

- Meta title/description max lengths.
- og_media_id exists when set.
- robots_index boolean.

## Authorization rules

- Same as content update permissions; no separate public write API.

## Business rules

- Empty content SEO fields inherit defaults.
- Defaults never stored as duplicated null-meaningless copies unless user sets overrides.
- Public pages use resolver output only.

## Edge cases

- All SEO fields null; missing settings row; deleted OG media FK.

## Security considerations

- Escape meta content in tags; no raw unescaped user HTML in meta.
- OG image URLs via filesystem/public URL helpers only.

## Testing requirements

- Resolver fallback chain unit tests.
- Save SEO on post/page.
- robots_index respected in output when public rendering available.

## Acceptance criteria

- [ ] Content SEO fields editable.
- [ ] Fallback resolver works without content overrides.
- [ ] Meta component ready for public layout.
- [ ] Validation + auth enforced.

## Definition of Done

- Unit tests for resolver; admin UI wired; integrates cleanly with TASK-016 defaults.

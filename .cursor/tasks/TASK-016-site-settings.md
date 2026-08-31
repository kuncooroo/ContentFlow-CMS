# TASK-016 — Site Settings

| Field | Value |
|---|---|
| **Task ID** | TASK-016 |
| **Title** | Site Settings |
| **Phase** | Roadmap Phase 9 |
| **Estimate focus** | Typed site_settings singleton |

---

## Objective

Implement the typed `site_settings` singleton (id=1): general, branding, SEO defaults, social links, comments toggle, timezone, locale—with authorized updates, cache invalidation, and audit.

## Background

Prefer one typed row over EAV key/value settings. Public site and SEO resolver consume these values.

## Dependencies

- TASK-004, TASK-005, TASK-008 (logo/favicon/default OG media).
- TASK-015 resolver integration.

## Files likely affected

```text
database/migrations/*_site_settings*
database/seeders/SiteSettingsSeeder.php
app/Models/SiteSetting.php
app/Actions/Settings/UpdateSiteSettings.php
app/Services/Settings/SiteSettings.php (read/cache)
app/Policies/SiteSettingPolicy.php
app/Livewire/Admin/Settings/*
tests/Feature/Admin/Settings*
```

## Database changes

- `site_settings` singleton per DATABASE.
- FKs to media for logo, favicon, default_og_media.
- JSON `social_links` as documented.

## Backend requirements

- Ensure row id=1 exists via seeder/installer later.
- UpdateSiteSettings Action in transaction; invalidate cache keys.
- Read service used by public layout / SEO resolver.
- Audit settings updates.
- Validate media FKs still exist.

## Frontend requirements

- Settings sections: General, Branding, SEO Defaults, Social, Content (comments enabled).
- Media pickers for branding/OG.
- Success feedback after save.

## Validation rules

- Site name required; timezone/locale required valid identifiers.
- URLs for social links validated.
- Booleans for comments_enabled / default_robots_index.

## Authorization rules

- `settings.manage` (or equivalent) only.
- Direct tampering denied.

## Business rules

- Single row only; no multi-tenant settings.
- Cache not source of truth; correct after cache clear.
- Comments module respects `comments_enabled`.

## Edge cases

- Missing singleton row—bootstrap safely.
- Invalid timezone string.
- Clearing logo (null FK).

## Security considerations

- No secrets in site_settings.
- Authorize updates; escape output when rendering site name etc.

## Testing requirements

- Authorized save; unauthorized denied.
- Cache invalidation.
- SEO defaults consumed by resolver.
- Comments flag effect.
- Audit event created.

## Acceptance criteria

- [ ] Settings editable by authorized admin.
- [ ] Persisted on `site_settings`.
- [ ] Cache invalidated; public consumers see updates.
- [ ] Audit recorded.
- [ ] Media references validated.

## Definition of Done

- Action + Livewire + tests; seeder ensures id=1; `.env` timezone vs settings timezone behavior documented briefly in code comments if non-obvious.

# Admin Guide — ContentFlow CMS

Guide for signed-in staff using the admin area at `/admin/*`. Full product manual (install through support): [USER_GUIDE.md](USER_GUIDE.md).

## Sign in

1. Go to `/login`.
2. Enter email and password.
3. After login you are redirected to **Dashboard** (`/admin/dashboard`).

Inactive accounts cannot sign in. Use **Forgot password** unless demo mode blocks reset ([README.md](../README.md#demo-mode)).

Sign out from the admin sidebar/logout action.

## Roles and permissions

Four system roles are seeded on install:

| Role | Typical use |
|---|---|
| **Super Admin** | Full access; baseline for installer-created account |
| **Administrator** | Users, roles, settings, audit, all content |
| **Editor** | Publish content, moderate comments, taxonomy, SEO — no users/settings/audit |
| **Author** | Own drafts, upload media, manage tags — limited publish rights |

### Permission matrix (high level)

| Capability | Super Admin | Administrator | Editor | Author |
|---|:---:|:---:|:---:|:---:|
| Posts (full lifecycle) | ✓ | ✓ | ✓ | Create/edit/delete own drafts |
| Schedule posts | ✓ | ✓ | ✓ | — |
| Pages (full lifecycle) | ✓ | ✓ | ✓ | — |
| Media library | ✓ | ✓ | ✓ | Upload/view |
| Comments moderation | ✓ | ✓ | ✓ | — |
| Categories & tags | ✓ | ✓ | ✓ | Tags only (Author) |
| Menus | ✓ | ✓ | — | — |
| SEO fields | ✓ | ✓ | ✓ | — |
| Users & roles | ✓ | ✓ | — | — |
| Site settings | ✓ | ✓ | — | — |
| Activity log | ✓ | ✓ | — | — |

Exact permission keys live in `RolePermissionSeeder` and `app/Support/Permissions/PermissionNames.php`.

## Dashboard

Operational summary: content counts, recent activity shortcuts, and links to common tasks.

## Posts

**Path:** Admin → Posts

### Status lifecycle

| Status | Meaning |
|---|---|
| Draft | Not public |
| Scheduled | Publishes automatically when `publish_at` is reached (requires scheduler) |
| Published | Visible on public site |
| Archived | Removed from public listings; can return to draft |

Allowed transitions match `PostStatus` in code (e.g. draft → scheduled/published; published → archived).

### Typical workflow

1. **Create** — title, slug, content, excerpt, categories, tags, featured image.
2. **Save draft** — iterate until ready.
3. **Publish** or **Schedule** — set publish date/time for scheduled posts.
4. **Preview** — admin preview route before go-live.
5. **Archive** — retire without deleting.

Scheduled posts require the server cron running `php artisan schedule:run` ([DEPLOYMENT.md](DEPLOYMENT.md)).

## Pages

**Path:** Admin → Pages

Statuses: **Draft**, **Published**, **Archived** (no scheduled state in MVP).

Use pages for static content (About, Contact). Published pages appear at `/pages/{slug}`.

## Media library

**Path:** Admin → Media

- Upload images (JPEG, PNG, GIF, WebP) within configured size limits.
- Edit alt text and metadata.
- Delete only when not referenced by posts, pages, or settings.

Run `php artisan storage:link` on new installs so public URLs work.

## Taxonomy

- **Categories** — hierarchical grouping for posts; public archive at `/categories/{slug}`.
- **Tags** — flat labels; public archive at `/tags/{slug}`.

## Comments

**Path:** Admin → Comments

Moderation actions: **Approve**, **Reject**, **Mark spam**. Pending comments do not appear on the public post until approved.

Disable new submissions via **Site settings → Comments enabled**.

## Menus

**Path:** Admin → Menus

Edit **Primary** and **Footer** menus. Item types: custom URL, page, post, category. Primary menu drives public header navigation.

## SEO

Per post/page fields: SEO title, meta description, canonical URL, robots index, Open Graph image.

Site-wide defaults are under **Site settings**. Editors with SEO permission can override per content item.

## Site settings

**Path:** Admin → Settings

Configure site name, description, logo, favicon, contact info, social links, default SEO, timezone, locale, and comments toggle.

**Demo mode:** saving settings is blocked when `DEMO_MODE=true`.

## Users

**Path:** Admin → Users

- Create users and assign one or more roles.
- **Activate / Deactivate** accounts (no hard delete in MVP).
- Edit profile and optional password change.

Protections:

- Cannot deactivate the last active Super Admin.
- Demo accounts cannot be deactivated or have passwords/roles changed in demo mode.

## Roles

**Path:** Admin → Roles

View system roles and sync permissions on non-demo installs. System roles are marked `is_system`; permission changes are blocked in demo mode.

## Activity log

**Path:** Admin → Activity log

Read-only audit trail for defined admin actions (user changes, settings, role permission changes, etc.). Requires audit permission.

## Demo mode banner

When `DEMO_MODE=true`, a yellow banner appears on admin and public layouts. Destructive baseline actions are restricted; reset with `php artisan demo:reset --force`.

## Out of scope (MVP)

- In-app notification center
- Two-factor authentication
- HTML/rich-text WYSIWYG (plain text content only)
- Workflow approval chains beyond comment moderation
- Billing or multi-tenant SaaS

## Related documentation

- [USER_GUIDE.md](USER_GUIDE.md) — commercial product manual
- [INSTALLATION.md](INSTALLATION.md) — setup
- [BUSINESS_FLOW.md](BUSINESS_FLOW.md) — detailed process specs
- [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) — production hardening

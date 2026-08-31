# Changelog

All notable changes to ContentFlow CMS are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Versioning follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-08-31

First commercial-ready MVP release.

### Added

- **Authentication** — Login, logout, password reset (queued mail), inactive user blocking
- **Users & access control** — User CRUD (activate/deactivate), roles, permission matrix, Super Admin protections
- **Content** — Posts (draft/scheduled/published/archived), pages, categories, tags, scheduled publishing command
- **Media library** — Image uploads with MIME/extension hardening
- **Comments** — Public submission with moderation queue
- **Navigation** — Primary and footer menus
- **SEO** — Per-content and site-wide metadata
- **Site settings** — Branding, contact, social links, timezone, locale, comments toggle
- **Dashboard** — Operational summary for admins
- **Activity log** — Audit trail for defined admin actions
- **Public website** — Home, blog, posts, pages, category/tag archives, custom 404
- **Notifications & UI feedback** — Flash messages, branded reset password notification
- **Security hardening** — CSRF, security headers, upload restrictions, rich content escaping, authorization matrix tests
- **Installer** — Browser wizard at `/install` with post-install lock
- **Demo mode** — `DEMO_MODE`, seeded demo content, restrictions, `demo:reset` command
- **Documentation** — Installation, deployment, user/admin/developer guides, upgrade, troubleshooting

### Security

- Rate-limited auth routes
- Policy-based authorization on admin actions
- No HTML rendering in public rich content (plain escaped text)
- Production checklist in `docs/SECURITY_CHECKLIST.md`

### Documentation

- Full doc set under `docs/` — see [docs/README.md](docs/README.md)
- Release notes: [docs/RELEASE.md](docs/RELEASE.md)

### Removed (release cleanup)

- Development-only `/admin/smoke` Livewire counter page
- `inspire` scheduler smoke entry

### Out of scope (not in v1.0.0)

- Payments, billing, subscriptions
- Multi-tenancy / SaaS
- Public member accounts
- Two-factor authentication
- WYSIWYG / HTML content editor
- In-app notification center
- Public site search UI

[1.0.0]: https://github.com/contentflow/cms/releases/tag/v1.0.0

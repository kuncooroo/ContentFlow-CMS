# ContentFlow CMS — Cursor Development Tasks

Task breakdown for MVP / Version 1.0.

Authority order: `docs/PRD.md` → `docs/SRS.md` → `docs/SYSTEM_DESIGN.md` → `docs/BUSINESS_FLOW.md` → `docs/DATABASE.md` → `docs/ROADMAP.md` → `CURSOR.md` → `docs/PROJECT_STRUCTURE.md`.

## Principles

- One focused feature per task.
- Implement only what the task lists.
- Prefer Laravel-native patterns; no speculative abstraction.
- Follow `docs/PROJECT_STRUCTURE.md` for folders and Actions/Policies placement.
- Do not implement out-of-scope PRD items (payments, multi-tenancy, page builder, etc.).

## Dependency Overview

```text
001 Foundation
 └─ 002 Authentication
     ├─ 003 Users
     │   └─ 004 Roles & Permissions
     │       ├─ 005 Audit Foundation
     │       ├─ 006 Categories
     │       ├─ 007 Tags
     │       ├─ 008 Media Library
     │       │   ├─ 009 Posts CRUD
     │       │   │   └─ 010 Post Publishing
     │       │   │       ├─ 011 Pages
     │       │   │       └─ 012 Scheduled Publishing
     │       │   ├─ 013 Comments
     │       │   ├─ 014 Menus
     │       │   ├─ 015 SEO
     │       │   └─ 016 Site Settings
     │       ├─ 017 Dashboard
     │       ├─ 018 Search & Filters
     │       └─ 019 Activity Log UI
     └─ 020 Public Website
         └─ 021 Notifications & Feedback
             └─ 022 Security Hardening
                 └─ 023 Testing & Regression
                     └─ 024 Installer
                         └─ 025 Demo System
                             └─ 026 Documentation
                                 └─ 027 Release Preparation
```

## Task Index

| ID | File | Feature |
|---|---|---|
| TASK-001 | [TASK-001-project-foundation.md](TASK-001-project-foundation.md) | Laravel/app foundation |
| TASK-002 | [TASK-002-authentication.md](TASK-002-authentication.md) | Login, logout, password reset |
| TASK-003 | [TASK-003-users.md](TASK-003-users.md) | User administration |
| TASK-004 | [TASK-004-roles-permissions.md](TASK-004-roles-permissions.md) | Roles, permissions, policies |
| TASK-005 | [TASK-005-audit-foundation.md](TASK-005-audit-foundation.md) | Activity logger service |
| TASK-006 | [TASK-006-categories.md](TASK-006-categories.md) | Categories taxonomy |
| TASK-007 | [TASK-007-tags.md](TASK-007-tags.md) | Tags taxonomy |
| TASK-008 | [TASK-008-media-library.md](TASK-008-media-library.md) | Media upload & library |
| TASK-009 | [TASK-009-posts-crud.md](TASK-009-posts-crud.md) | Posts create/edit/list (draft) |
| TASK-010 | [TASK-010-post-publishing.md](TASK-010-post-publishing.md) | Publish / schedule / archive |
| TASK-011 | [TASK-011-pages.md](TASK-011-pages.md) | Pages lifecycle |
| TASK-012 | [TASK-012-scheduled-publishing.md](TASK-012-scheduled-publishing.md) | Scheduler job |
| TASK-013 | [TASK-013-comments.md](TASK-013-comments.md) | Comments + moderation |
| TASK-014 | [TASK-014-menus.md](TASK-014-menus.md) | Menu builder |
| TASK-015 | [TASK-015-seo.md](TASK-015-seo.md) | SEO fields + fallbacks |
| TASK-016 | [TASK-016-site-settings.md](TASK-016-site-settings.md) | Site settings singleton |
| TASK-017 | [TASK-017-dashboard.md](TASK-017-dashboard.md) | Operational dashboard |
| TASK-018 | [TASK-018-search-filters.md](TASK-018-search-filters.md) | Admin search & filters |
| TASK-019 | [TASK-019-activity-log-ui.md](TASK-019-activity-log-ui.md) | Audit trail admin UI |
| TASK-020 | [TASK-020-public-website.md](TASK-020-public-website.md) | Public content delivery |
| TASK-021 | [TASK-021-notifications-feedback.md](TASK-021-notifications-feedback.md) | Flash/toast + mail polish |
| TASK-022 | [TASK-022-security-hardening.md](TASK-022-security-hardening.md) | Security review & hardening |
| TASK-023 | [TASK-023-testing-regression.md](TASK-023-testing-regression.md) | Full regression suite |
| TASK-024 | [TASK-024-installer.md](TASK-024-installer.md) | Product installer |
| TASK-025 | [TASK-025-demo-system.md](TASK-025-demo-system.md) | Demo mode & seed |
| TASK-026 | [TASK-026-documentation.md](TASK-026-documentation.md) | Product/dev docs |
| TASK-027 | [TASK-027-release-preparation.md](TASK-027-release-preparation.md) | v1.0 release packaging |

## Out of Scope (do not create tasks)

Payment, invoices, subscriptions, multi-tenancy, SaaS billing, e-commerce, page builder, AI generation, GraphQL, multilingual content editing, plugin marketplace, advanced analytics.

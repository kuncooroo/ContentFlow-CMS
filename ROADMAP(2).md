# Development Roadmap
## ContentFlow CMS

---

## Document Control

| Field | Value |
|---|---|
| Document | Technical Development Roadmap |
| Product | ContentFlow CMS |
| Scope | MVP / Version 1.0 |
| Architecture | Laravel Modular Monolith |
| Backend Target | Laravel 13.x |
| PHP Target | PHP 8.4.x |
| Database Target | MySQL 8.4.x LTS |
| Frontend | Blade + Livewire + Tailwind CSS + Alpine.js |
| Deployment | VPS |
| Status | Development Planning |
| Last Updated | 2026-08-29 |

---

# 1. Roadmap Principles

Roadmap ini disusun berdasarkan dokumen produk dan teknis ContentFlow CMS:

- `docs/PRD.md`
- `docs/SRS.md`
- `docs/SYSTEM_DESIGN.md`
- `docs/BUSINESS_FLOW.md`
- `docs/DATABASE.md`
- `docs/UI_UX.md`
- `CURSOR.md`

Prinsip utama roadmap:

1. Bangun fondasi terlebih dahulu.
2. Implementasikan modul dengan dependency paling rendah lebih awal.
3. Hindari premature abstraction.
4. Gunakan Laravel-native functionality.
5. Jangan menambahkan fitur di luar MVP.
6. Setiap phase harus memiliki acceptance criteria yang dapat diuji.
7. Setiap phase harus memiliki test minimum sebelum lanjut.
8. Refactor besar hanya dilakukan sebagai pekerjaan terpisah.
9. Security, testing, installer, demo, documentation, dan release preparation adalah bagian resmi dari development lifecycle.
10. Financial modules tidak termasuk domain ContentFlow CMS MVP.

---

# 2. Phase Dependency Overview

```text
Phase 0  Project Foundation
   ↓
Phase 1  Authentication
   ↓
Phase 2  Users / Roles / Permissions
   ↓
Phase 3  Core Master Data
   ↓
Phase 4  Primary Business Workflow
   ↓
Phase 5  Secondary Modules
   ↓
Phase 6  Financial Modules — Not Applicable / Deferred
   ↓
Phase 7  Reporting
   ↓
Phase 8  Notifications
   ↓
Phase 9  Settings
   ↓
Phase 10 Security Hardening
   ↓
Phase 11 Testing & Regression
   ↓
Phase 12 Installer
   ↓
Phase 13 Demo System
   ↓
Phase 14 Documentation
   ↓
Phase 15 Release Preparation
```

---

# Phase 0 — Project Foundation

## Objectives

Menyiapkan struktur proyek yang stabil, terverifikasi, konsisten dengan arsitektur, dan siap menjadi basis seluruh development berikutnya.

## Modules

- Laravel project initialization
- Environment configuration
- Database connection
- Frontend build pipeline
- Base Blade layout
- Tailwind setup
- Livewire setup
- Alpine.js setup
- Base admin shell
- Logging baseline
- Queue baseline
- Scheduler baseline
- Testing baseline
- Git repository conventions
- CI-ready test command structure

## Dependencies

None.

## Deliverables

- Laravel project berjalan
- PHP runtime terverifikasi
- MySQL connection terverifikasi
- `composer.json`
- `composer.lock`
- `package.json`
- frontend lock file
- `.env.example`
- base admin layout
- base public layout
- base routes
- base test suite
- base queue configuration
- base scheduler configuration
- storage link/configuration
- project coding standards applied

## Acceptance Criteria

- Application dapat boot tanpa error.
- Database connection berhasil.
- Frontend assets dapat dibuild.
- Blade page dapat dirender.
- Livewire component sederhana dapat dirender.
- Tailwind styling aktif.
- Alpine interaction sederhana bekerja.
- Test command dapat dijalankan.
- Application logs tersedia.
- `.env` tidak masuk version control.
- Actual installed versions tercatat.
- Folder/project structure konsisten dengan `SYSTEM_DESIGN.md`.

## Testing Requirements

- Smoke test homepage.
- Smoke test admin route.
- Environment configuration test.
- Database connectivity test.
- Livewire render test.
- Asset build verification.

---

# Phase 1 — Authentication

## Objectives

Menyediakan authentication flow yang aman dan sesuai Laravel conventions.

## Modules

- Login
- Logout
- Forgot Password
- Reset Password
- Session management
- Login rate limiting
- Inactive account rejection

## Dependencies

- Phase 0
- `users` schema foundation

## Deliverables

- Login page
- Logout flow
- Forgot password page
- Reset password page
- Session configuration
- Authentication middleware
- Login throttling
- Auth-related UI states
- Auth tests

## Acceptance Criteria

- User valid dapat login.
- Credential invalid ditolak.
- Inactive user ditolak.
- Session diregenerasi setelah login.
- Logout mengakhiri authenticated session.
- Reset password berjalan pada token valid.
- Token invalid/expired ditolak.
- Protected route tidak dapat diakses anonymous user.
- Auth errors tidak membocorkan informasi sensitif.

## Testing Requirements

- Valid login.
- Invalid password.
- Unknown email.
- Inactive account.
- Rate-limit behavior.
- Logout.
- Forgot password.
- Valid reset token.
- Invalid/expired reset token.
- Protected route redirect.

---

# Phase 2 — Users / Roles / Permissions

## Objectives

Membangun user administration dan role-based access control yang menjadi fondasi authorization seluruh CMS.

## Modules

- User management
- Roles
- Permissions
- Role assignment
- Permission assignment
- Policies/Gates
- User activation/deactivation

## Dependencies

- Phase 0
- Phase 1

## Deliverables

- User list
- Create user
- Edit user
- Activate/deactivate user
- Default role seeding:
  - Super Admin
  - Administrator
  - Editor
  - Author
- Permission seeding
- Role-permission management
- User-role management
- Policies/Gates baseline
- Audit event untuk user/status/role changes yang diwajibkan

## Acceptance Criteria

- User dapat dibuat dengan email unik.
- User dapat diberi role.
- Duplicate email ditolak.
- Permission diterapkan server-side.
- Unauthorized direct request ditolak.
- UI menyembunyikan unauthorized actions.
- Author tidak memiliki publish permission secara default.
- Last active Super Admin tidak dapat dinonaktifkan jika menyebabkan tidak ada active Super Admin.
- Role/permission changes berlaku pada request berikutnya.

## Testing Requirements

- User CRUD.
- Duplicate email.
- Role assignment.
- Permission assignment.
- Policy allow/deny.
- Author restrictions.
- Editor permissions.
- Admin permissions.
- Last Super Admin protection.
- Inactive user login regression.

---

# Phase 3 — Core Master Data

## Objectives

Menyediakan data pendukung utama sebelum workflow publishing dibangun penuh.

## Modules

- Categories
- Tags
- Media Library
- Basic file upload
- Media metadata
- Menus base structure

## Dependencies

- Phase 0
- Phase 2

## Deliverables

### Categories
- list
- create
- edit
- delete with relationship protection

### Tags
- list
- create
- edit
- delete

### Media
- upload
- list
- search
- metadata
- alt text
- safe deletion rules

### Menus
- menu container model
- basic menu item structure

## Acceptance Criteria

- Category dapat dibuat dan memiliki unique slug.
- Tag dapat dibuat dan memiliki unique slug.
- File valid dapat diupload.
- File invalid ditolak.
- Duplicate path tidak overwrite secara diam-diam.
- Alt text dapat disimpan.
- Media dapat dicari.
- Referenced media tidak dapat dihapus secara diam-diam.
- Menu container dapat dibuat.

## Testing Requirements

- Category CRUD.
- Category slug uniqueness.
- Tag CRUD.
- Tag slug uniqueness.
- Media upload valid.
- Invalid MIME type.
- Oversize upload.
- Media metadata.
- Media delete protection.
- Menu basic persistence.

---

# Phase 4 — Primary Business Workflow

## Objectives

Membangun workflow inti ContentFlow: membuat, mengedit, menjadwalkan, mempublikasikan, dan mengarsipkan content.

## Modules

- Posts
- Pages
- Publishing state
- Scheduling
- Preview
- Featured image
- Categories/tags relationships
- SEO per content
- Scheduled publishing job/command
- Core audit events

## Dependencies

- Phase 1
- Phase 2
- Phase 3

## Deliverables

### Posts
- post list
- create post
- edit post
- draft
- scheduled
- published
- archived
- preview
- publish now
- schedule publish
- cancel schedule
- archive
- restore to draft

### Pages
- page list
- create
- edit
- draft
- published
- archived
- preview

### Scheduled Publishing
- scheduler integration
- eligible post query
- publish action reuse
- idempotent behavior

## Acceptance Criteria

### Posts
- Draft tidak tampil publik.
- Scheduled tidak tampil sebelum waktunya.
- Published tampil publik.
- Archived tidak tampil di normal public listing.
- Slug unik.
- Author ownership rule berlaku.
- Editor/Admin dapat publish sesuai permission.
- Scheduled date harus valid.
- Scheduled publishing otomatis bekerja.

### Pages
- Draft tidak publik.
- Published dapat diakses publik.
- Archived tidak tampil di normal navigation/listing.

### Business Rules
- Status transition sesuai `BUSINESS_FLOW.md`.
- Publish tidak menghasilkan partial state.
- Audit event dibuat untuk publish/archive sesuai scope.

## Testing Requirements

- Post create/edit.
- Slug uniqueness.
- Draft visibility.
- Scheduled visibility before due.
- Scheduled visibility after due.
- Manual publish.
- Archive.
- Restore to draft.
- Author cannot publish by default.
- Category/tag relations.
- Featured media.
- Page draft/publish/archive.
- Transaction rollback on publish failure.
- Scheduled publishing idempotency.

---

# Phase 5 — Secondary Modules

## Objectives

Melengkapi CMS dengan modul penting yang mendukung workflow editorial dan website management.

## Modules

- Comments
- Comment moderation
- Menu builder
- SEO global
- Activity Log
- Search and filtering
- Media selection integration

## Dependencies

- Phase 2
- Phase 3
- Phase 4

## Deliverables

### Comments
- public comment form
- Pending by default
- moderation:
  - Approved
  - Spam
  - Rejected

### Menu Builder
- page target
- post target
- category target
- custom URL
- ordering

### SEO
- global defaults
- content fallback

### Activity
- audit list
- actor/action/subject/timestamp

### Search & Filters
- post search
- page search
- media search
- comment filtering

## Acceptance Criteria

- New comment default = Pending.
- Only Approved comments public.
- Moderator dapat mengubah status.
- Menu order tersimpan.
- Menu item target valid.
- Public navigation menggunakan menu terbaru.
- SEO fallback berjalan.
- Audit entries dapat dibaca user berwenang.
- Search dan filter tidak bypass authorization.
- Pagination aktif pada list besar.

## Testing Requirements

- Comment submit.
- Pending non-public.
- Approval public.
- Spam/rejected non-public.
- Menu target validation.
- Menu ordering.
- SEO fallback.
- Search result.
- Filter result.
- Pagination.
- Audit visibility permission.

---

# Phase 6 — Financial Modules

## Objectives

Tidak ada financial objective pada ContentFlow CMS MVP.

## Modules

**Not Applicable / Deferred**

Fitur berikut tidak termasuk domain MVP:

- payment
- invoice
- subscription billing
- refund
- revenue report
- paid membership
- e-commerce transaction

## Dependencies

None for MVP.

## Deliverables

- Explicit scope confirmation only.
- No financial database tables.
- No financial UI.
- No payment integration.

## Acceptance Criteria

- Tidak ada payment gateway.
- Tidak ada subscription table.
- Tidak ada invoice table.
- Tidak ada money/decimal business data.
- Implementasi Phase 6 tidak menunda MVP.

## Testing Requirements

No functional testing required.

Regression requirement:

- Pastikan tidak ada financial dependency yang tidak sengaja diperkenalkan.

---

# Phase 7 — Reporting

## Objectives

Menyediakan reporting operasional untuk editorial/admin tanpa membangun analytics platform.

## Modules

- Dashboard metrics
- Recent content
- Scheduled content
- Pending comments
- Filtered operational reports

## Dependencies

- Phase 4
- Phase 5

## Deliverables

- Published count
- Draft count
- Scheduled count
- Pending comment count
- Recent content table
- Upcoming scheduled posts
- Permission-aware reporting queries

## Acceptance Criteria

- Dashboard counts akurat.
- User hanya melihat data yang berhak dilihat.
- Recent content konsisten dengan database.
- Scheduled list diurutkan sesuai publish time.
- Tidak ada revenue/traffic/conversion analytics di MVP.

## Testing Requirements

- Dashboard metric correctness.
- Author dashboard scope.
- Editor dashboard scope.
- Pending comment count.
- Scheduled ordering.
- Permission-filtered reports.
- N+1 check on dashboard queries.

---

# Phase 8 — Notifications

## Objectives

Menyediakan feedback operasional dan email penting tanpa membangun notification center kompleks.

## Modules

- Toast/flash feedback
- Password reset email
- Queue-backed email
- Mail failure logging

## Dependencies

- Phase 1
- Phase 4
- Queue foundation from Phase 0

## Deliverables

- success feedback
- validation feedback
- system error feedback
- password reset mail
- queued mail baseline
- failed job handling

## Acceptance Criteria

- Save success hanya muncul setelah persistence berhasil.
- Validation error tampil jelas.
- Reset password email terkirim pada konfigurasi valid.
- Mail failure tidak membocorkan credential.
- Failed queue job dapat diinspeksi.
- Tidak ada persistent notification center pada MVP.

## Testing Requirements

- Toast success state.
- Validation error state.
- Queued mail dispatch.
- Mail failure path.
- Password reset mail.
- Failed job behavior.

---

# Phase 9 — Settings

## Objectives

Menyediakan konfigurasi site terpusat sesuai typed settings architecture.

## Modules

- General Settings
- Branding
- SEO Defaults
- Social Links
- Content Settings
- Timezone
- Locale
- Comments enable/disable

## Dependencies

- Phase 3
- Phase 5

## Deliverables

### General
- site name
- site description
- timezone
- locale
- contact information

### Branding
- logo
- favicon

### SEO
- default SEO title
- default meta description
- default OG image
- default index state

### Social
- social links

### Content
- comments enabled

## Acceptance Criteria

- Settings hanya dapat diubah authorized user.
- Data tersimpan pada `site_settings`.
- Cache settings invalidated setelah update.
- Public UI menggunakan latest settings.
- Settings update tercatat pada activity log.
- Media settings reference tetap valid.

## Testing Requirements

- Settings authorization.
- General settings save.
- Branding media save.
- SEO defaults save.
- Comments switch.
- Timezone behavior.
- Cache invalidation.
- Settings audit event.

---

# Phase 10 — Security

## Objectives

Melakukan security hardening terhadap seluruh MVP sebelum release candidate.

## Modules

- CSRF
- XSS protections
- Authorization review
- Rate limiting
- Session security
- Upload security
- Rich-content rendering review
- Error disclosure review
- Production configuration review

## Dependencies

- Phase 1–9

## Deliverables

- Security checklist
- Auth endpoint throttling
- Policy coverage review
- secure upload rules
- safe output rendering
- production `.env` guidance
- `APP_DEBUG=false` release requirement
- HTTPS deployment requirement
- web root configuration requirement
- sensitive logging review

## Acceptance Criteria

- No critical known security defect.
- Anonymous user cannot access protected routes.
- Unauthorized action rejected server-side.
- User-generated normal output escaped.
- Rich content has explicit safe rendering approach.
- Comments do not render arbitrary HTML by default.
- Upload allowlist enforced.
- Executable uploads rejected.
- Credentials do not appear in logs/docs/repository.
- Production debug disabled.

## Testing Requirements

- Authorization negative tests.
- CSRF behavior.
- Auth rate limiting.
- Upload malicious/invalid type.
- XSS-oriented output tests where applicable.
- Inactive user regression.
- Session invalidation.
- Direct unauthorized route tests.

---

# Phase 11 — Testing

## Objectives

Menyelesaikan regression suite dan memastikan seluruh acceptance criteria MVP memiliki coverage yang memadai.

## Modules

- Unit tests
- Feature tests
- Livewire tests
- Database tests
- Critical browser/E2E tests where justified
- Regression testing

## Dependencies

- Phase 1–10

## Deliverables

- Full automated test suite
- Critical business workflow tests
- Authorization coverage
- Database transaction tests
- UI component behavior tests
- Regression checklist
- Test data/factories/seed helpers

## Acceptance Criteria

- All critical tests pass.
- No critical defect open.
- No high-severity release blocker.
- Important business logic has automated tests.
- Permission matrix behavior validated.
- Scheduled publishing fully tested.
- No tests removed merely to make build pass.

## Testing Requirements

This phase is itself testing-focused.

Minimum regression areas:

- auth
- users
- roles
- permissions
- posts
- pages
- scheduling
- categories
- tags
- media
- comments
- menus
- SEO
- settings
- dashboard
- audit
- notifications
- security
- public visibility

---

# Phase 12 — Installer

## Objectives

Membuat installation experience yang memungkinkan source-code product dipasang dengan aman dan konsisten.

## Modules

- Requirement check
- Environment check
- Database setup
- Migration execution
- Initial site setup
- Super Admin creation
- Initial settings
- Installation completion lock

## Dependencies

- Phase 0–11
- Stable database schema

## Deliverables

Installer flow:

```text
Start
→ Server Requirements
→ Application Configuration
→ Database Configuration
→ Database Setup
→ Create Super Admin
→ Site Settings
→ Complete
```

## Acceptance Criteria

- Installer mendeteksi requirement yang tidak terpenuhi.
- Database configuration divalidasi.
- Migration gagal tidak menghasilkan install success.
- Super Admin berhasil dibuat.
- Default role/permission seed tersedia.
- Initial site settings tersimpan.
- Installer tidak dapat dijalankan ulang secara tidak sengaja setelah completion.
- Secret tidak ditampilkan kembali di UI.

## Testing Requirements

- Fresh install.
- Invalid DB credentials.
- Migration failure.
- Duplicate existing installation.
- Super Admin creation.
- Initial seed validation.
- Initial site settings.
- Installer lock behavior.

---

# Phase 13 — Demo System

## Objectives

Menyediakan demo komersial yang aman untuk calon pembeli tanpa memungkinkan destructive misuse.

## Modules

- Demo mode
- Demo banner
- Restricted destructive actions
- Demo accounts
- Demo seed data
- Demo reset process

## Dependencies

- Phase 4–12

## Deliverables

- Admin demo environment
- Seeded posts/pages/categories/tags/media/comments
- Editor demo user
- Administrator demo user
- Demo restriction policy
- Demo data reset process

## Acceptance Criteria

- User dapat mengeksplorasi core CMS.
- Demo mode terlihat jelas.
- User demo tidak dapat:
  - menghapus last Super Admin;
  - mengubah password penting;
  - merusak role/permission baseline;
  - melakukan destructive server configuration.
- Demo data dapat direset.
- Demo behavior tidak mempengaruhi production builds kecuali mode diaktifkan.

## Testing Requirements

- Demo restrictions.
- Demo role permissions.
- Demo content editing.
- Demo reset.
- Attempted prohibited destructive actions.
- Production mode regression.

---

# Phase 14 — Documentation

## Objectives

Menyiapkan dokumentasi produk, developer, deployment, dan end-user sebelum release.

## Modules

- User Guide
- Installation Guide
- Deployment Guide
- Admin Guide
- Developer Guide
- Upgrade Guide
- Troubleshooting
- Release Notes template

## Dependencies

- Stable Phase 0–13 behavior

## Deliverables

Recommended docs:

```text
docs/
├── PRD.md
├── SRS.md
├── SYSTEM_DESIGN.md
├── BUSINESS_FLOW.md
├── DATABASE.md
├── UI_UX.md
├── ROADMAP.md
├── INSTALLATION.md
├── DEPLOYMENT.md
├── USER_GUIDE.md
├── ADMIN_GUIDE.md
├── DEVELOPER_GUIDE.md
├── UPGRADE.md
└── TROUBLESHOOTING.md
```

## Acceptance Criteria

- Installation dapat diikuti dari dokumentasi.
- User Guide mencakup workflow utama.
- Role/permission behavior didokumentasikan.
- Deployment VPS didokumentasikan.
- Scheduler dan queue worker didokumentasikan.
- Backup requirement didokumentasikan.
- Upgrade approach didokumentasikan.
- Tidak ada credential real di docs.
- Docs sesuai current implementation.

## Testing Requirements

- Documentation walkthrough.
- Fresh install mengikuti guide.
- VPS deployment walkthrough.
- User workflow walkthrough.
- Link/path validation.

---

# Phase 15 — Release Preparation

## Objectives

Mengubah completed MVP menjadi release commercial-ready.

## Modules

- Release QA
- Production build
- Versioning
- Changelog
- License preparation
- Demo verification
- Security final review
- Backup/restore verification
- Deployment verification
- Packaging

## Dependencies

- All previous phases

## Deliverables

- Version 1.0.0 candidate
- Production-ready package
- Release notes
- Changelog
- Installation package
- License files
- Documentation package
- Demo environment
- Final test report
- Security checklist
- Backup/restore evidence
- Upgrade baseline

## Acceptance Criteria

- All MVP acceptance criteria pass.
- Full regression test passes.
- No critical defect.
- No critical known security issue.
- Production assets build successfully.
- Fresh install succeeds.
- Existing install deployment succeeds.
- Queue worker verified.
- Scheduler verified.
- Email verified.
- Backup created and restore tested.
- Demo verified.
- Documentation complete.
- Credentials/secrets excluded.
- Version number consistent.
- Product package contains no unnecessary development artifacts.

## Testing Requirements

### Functional
- Full user workflow.
- Full editorial workflow.
- Full admin workflow.

### Security
- Final authorization regression.
- Upload security.
- config disclosure check.
- production debug check.

### Installation
- clean VPS-like install validation.
- installer validation.

### Operations
- queue.
- scheduler.
- mail.
- logs.
- backup.
- restore.

### Compatibility
- verified target PHP.
- verified target MySQL.
- supported browser smoke test.

---

# 3. Recommended Release Milestones

## Milestone A — Foundation Ready

Completed:

```text
Phase 0
Phase 1
Phase 2
```

Outcome:

```text
Secure authenticated admin foundation
```

---

## Milestone B — CMS Core Ready

Completed:

```text
Phase 3
Phase 4
```

Outcome:

```text
Posts and Pages can be created and published
```

This is the first meaningful internal product milestone.

---

## Milestone C — Feature Complete MVP

Completed:

```text
Phase 5
Phase 7
Phase 8
Phase 9
```

Phase 6 remains Not Applicable.

Outcome:

```text
Complete editorial CMS behavior
```

---

## Milestone D — Release Candidate

Completed:

```text
Phase 10
Phase 11
Phase 12
```

Outcome:

```text
Secure, tested, installable product
```

---

## Milestone E — Commercial Release

Completed:

```text
Phase 13
Phase 14
Phase 15
```

Outcome:

```text
Demo-ready
Documented
Packaged
Commercially distributable
```

---

# 4. MVP Feature Freeze Rule

Once Phase 10 begins, new MVP feature requests should normally be deferred unless they are:

- critical defect fixes;
- security fixes;
- requirement gaps against PRD;
- release-blocking usability issues.

Do not add during release hardening:

- AI writing;
- page builder;
- multilingual CMS;
- SaaS billing;
- multi-tenancy;
- e-commerce;
- plugin marketplace;
- analytics platform;
- API framework;
- webhook platform.

These belong to future roadmap versions.

---

# 5. Recommended Future Roadmap After Version 1.0

## Version 1.5 — Publishing Experience

Potential:

- revisions;
- autosave;
- duplicate content;
- richer preview;
- redirect manager;
- advanced media;
- backup/export improvements.

## Version 2.0 — Agency Edition

Potential:

- theme system;
- reusable blocks;
- controlled custom fields;
- white-label capabilities;
- improved import/export;
- permission presets.

## Version 2.5 — Developer Platform

Potential:

- REST API;
- API tokens;
- webhooks;
- extension points;
- integration docs.

## Version 3.0 — SaaS Foundation

Potential:

- tenant architecture;
- site ownership;
- plans;
- subscription billing;
- quotas;
- custom domains.

Financial modules become relevant only at this stage if SaaS billing is approved.

---

# 6. Definition of Phase Complete

A phase is considered complete only when:

1. Required modules are implemented.
2. Deliverables exist.
3. Acceptance criteria pass.
4. Required automated tests pass.
5. Authorization behavior is validated.
6. Validation/error states are implemented.
7. Database changes are represented by valid migrations.
8. No unrelated architecture changes were introduced.
9. Relevant documentation is updated.
10. No critical defect remains for the phase.
11. Code follows `CURSOR.md`.
12. The implementation remains within PRD scope.

---

# 7. Final Development Sequence

Recommended implementation order:

```text
FOUNDATION
    ↓
AUTHENTICATION
    ↓
RBAC
    ↓
MASTER DATA
    ↓
CONTENT WORKFLOW
    ↓
SECONDARY CMS MODULES
    ↓
OPERATIONAL REPORTING
    ↓
NOTIFICATIONS
    ↓
SETTINGS
    ↓
SECURITY HARDENING
    ↓
FULL TESTING
    ↓
INSTALLER
    ↓
DEMO
    ↓
DOCUMENTATION
    ↓
RELEASE
```

ContentFlow CMS Version 1.0 should be considered successful when it is:

```text
Functional
Secure
Tested
Installable
Documented
Demo-ready
Commercially reusable
```

without introducing features that belong to future SaaS, e-commerce, or platform expansion.

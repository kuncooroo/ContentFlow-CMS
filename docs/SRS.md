# Software Requirements Specification (SRS)
## ContentFlow CMS

---

## Document Control

| Field | Value |
|---|---|
| Document | Software Requirements Specification |
| Product | ContentFlow CMS |
| Source of Product Requirements | `docs/PRD.md` |
| Requirement Authority | PRD is authoritative for product behavior and scope |
| Target Release | MVP / Version 1.0 |
| Deployment Model | Single-application deployment on VPS |
| Architecture Style | Laravel modular monolith |
| Document Status | Draft for Engineering |
| Last Updated | 2026-08-29 |

### Requirement Interpretation Rules

1. `docs/PRD.md` is the authoritative source for product behavior, scope, user roles, business rules, and MVP boundaries.
2. This SRS translates the PRD into software and system requirements.
3. This SRS must not expand the MVP with features explicitly listed as out of scope in the PRD.
4. Where the PRD leaves implementation choices open, this SRS prefers Laravel-native capabilities and the simplest architecture that satisfies the requirement.
5. A technical dependency must not be introduced only for architectural elegance.
6. Exact installed versions must be read from the project environment when project configuration exists.
7. Because no application project configuration is currently available, installed versions are not asserted in this document.

### Environment Inspection Status

The available project workspace was inspected before defining the stack.

Files currently available:

- `docs/PRD.md`
- source prompt document

The following application configuration was **not available** at the time of this SRS:

- `composer.json`
- `composer.lock`
- `package.json`
- lock file for frontend dependencies
- `.env`
- `.env.example`
- Docker configuration
- web server configuration
- PHP runtime output
- MySQL runtime output

Therefore:

| Component | Actual Installed Version | Requested / Recommended Target |
|---|---|---|
| Laravel | **TBD — Requires Environment Verification** | Laravel 13.x |
| PHP | **TBD — Requires Environment Verification** | PHP 8.4.x |
| MySQL | **TBD — Requires Environment Verification** | MySQL 8.4.x LTS |
| Blade | **TBD — Requires Environment Verification** | Version shipped with Laravel 13.x |
| Livewire | **TBD — Requires Environment Verification** | Livewire 4.x |
| Tailwind CSS | **TBD — Requires Environment Verification** | Tailwind CSS 4.x |
| Alpine.js | **TBD — Requires Environment Verification** | Use Alpine bundled with Livewire by default; do not install a duplicate copy |
| Asset Builder | **TBD — Requires Environment Verification** | Vite as provided by the Laravel project scaffold |
| Web Server | **TBD — Requires Environment Verification** | Nginx recommended for VPS |
| Queue Driver | **TBD — Requires Environment Verification** | Laravel database queue for MVP |
| Cache Driver | **TBD — Requires Environment Verification** | Laravel database cache or file cache; database cache preferred for predictable shared state |
| Session Driver | **TBD — Requires Environment Verification** | Laravel database session driver |
| Mail Provider | **TBD — Requires Environment Verification** | SMTP-compatible provider configured per installation |

### Compatibility Rationale

For a new ContentFlow project, the target stack above is considered suitable because:

- Laravel 13 requires PHP 8.3 or newer, so PHP 8.4.x is within the supported minimum range.
- Livewire 4 supports Laravel 13 and PHP 8.1 or newer.
- MySQL 8.4 is an LTS series intended for stable production deployments.
- Tailwind CSS 4 is the current major generation and integrates with Vite.
- Livewire includes Alpine.js, so installing Alpine separately should be avoided unless a documented customization requires manual bundling.

Exact patch versions must be selected and locked when the project is initialized.

---

# 1. System Overview

ContentFlow CMS is a server-rendered web Content Management System for creating, organizing, reviewing, scheduling, publishing, and maintaining website content through a centralized administrative interface.

The MVP supports:

- authentication;
- users;
- roles and permissions;
- posts;
- pages;
- categories;
- tags;
- media;
- comments;
- navigation menus;
- SEO metadata;
- site settings;
- dashboard;
- search and filtering;
- basic audit trail;
- scheduled publishing;
- public website content delivery.

### SYS-OV-001

The system shall expose two primary user surfaces:

1. Public Website
2. Administration Dashboard

### SYS-OV-002

The Administration Dashboard shall require authentication except for authentication recovery flows.

### SYS-OV-003

Public visitors shall only receive content that satisfies PRD publication rules.

### SYS-OV-004

The MVP shall run as one Laravel application and one primary MySQL database.

### SYS-OV-005

The MVP shall not require microservices.

### SYS-OV-006

The MVP shall not require Redis, Elasticsearch, a message broker, or object storage to function.

---

# 2. System Context

## 2.1 Actors

The system actors are:

- Public Visitor
- Author
- Editor
- Administrator
- Super Admin
- Mail Server / Email Provider
- VPS Operating Environment
- Optional external backup destination

## 2.2 Context Diagram

```text
                        ┌───────────────────┐
                        │   Public Visitor  │
                        └─────────┬─────────┘
                                  │ HTTPS
                                  ▼
┌───────────────────────────────────────────────────────┐
│                    CONTENTFLOW CMS                    │
│                                                       │
│  Public Site     Admin UI      Publishing Engine      │
│       │              │                │               │
│       └──────────────┼────────────────┘               │
│                      │                                │
│          Auth / Content / Media / SEO / RBAC          │
│                      │                                │
└──────────────┬───────┴───────────────┬────────────────┘
               │                       │
               ▼                       ▼
        ┌─────────────┐         ┌──────────────┐
        │ MySQL 8.4  │         │ File Storage │
        └─────────────┘         └──────────────┘
               │
               │ queued/scheduled work
               ▼
        ┌─────────────┐
        │ Queue Worker│
        └─────────────┘

ContentFlow ───────────────► SMTP / Email Provider
VPS Scheduler ─────────────► Laravel Scheduler
Backup Process ────────────► Backup Destination
```

### CTX-001

The web application shall communicate with the primary database through the Laravel database layer.

### CTX-002

Publicly accessible uploaded files shall be served only from an intended public storage location.

### CTX-003

Email delivery shall be delegated to a configured mail transport.

### CTX-004

The application shall not depend on a third-party SaaS service for core CMS functionality.

---

# 3. Technical Objectives

### TECH-OBJ-001 — Simplicity

Use Laravel-native facilities where they satisfy requirements without introducing material limitations.

### TECH-OBJ-002 — Predictability

The architecture shall make business operations such as publishing, scheduling, moderation, and permissions explicit and testable.

### TECH-OBJ-003 — Maintainability

UI code shall not become the primary location for business rules.

### TECH-OBJ-004 — Commercial Reusability

The codebase shall not contain customer-specific business logic in the core application.

### TECH-OBJ-005 — Production Readiness

The MVP shall support secure deployment on a standard Linux VPS with:

- web server;
- PHP runtime;
- MySQL;
- queue worker;
- scheduler;
- SSL termination.

### TECH-OBJ-006 — Controlled Extensibility

The architecture shall allow later addition of APIs, webhooks, themes, SaaS features, and external storage without requiring them in MVP.

### TECH-OBJ-007 — Upgradeability

Core framework conventions shall be preserved to reduce friction during Laravel major upgrades.

---

# 4. Functional Requirements

This section maps software behavior to the authoritative PRD.

## 4.1 Authentication

### SFR-AUTH-001
The system shall authenticate active users using unique email credentials.

### SFR-AUTH-002
The system shall reject inactive users.

### SFR-AUTH-003
The system shall provide password reset functionality.

### SFR-AUTH-004
The system shall terminate the authenticated session on logout.

## 4.2 Users

### SFR-USR-001
Authorized users shall be able to list, create, update, activate, and deactivate user accounts.

### SFR-USR-002
User email shall be unique.

### SFR-USR-003
The system shall prevent a state where no active Super Admin remains if the installation requires at least one Super Admin.

## 4.3 Roles and Permissions

### SFR-RBAC-001
The system shall support the default roles:
- Super Admin
- Administrator
- Editor
- Author

### SFR-RBAC-002
Authorization shall be permission-based even when default roles are used.

### SFR-RBAC-003
Direct requests shall be denied when authorization fails, regardless of UI visibility.

## 4.4 Posts

### SFR-POST-001
Authorized users shall be able to create and update posts.

### SFR-POST-002
Post status shall support:
- Draft
- Scheduled
- Published
- Archived

### SFR-POST-003
Scheduled posts shall not become public before their scheduled publication time.

### SFR-POST-004
Published posts shall be publicly accessible if all publication conditions are satisfied.

### SFR-POST-005
Archived posts shall not appear in normal public listings.

### SFR-POST-006
Post public slugs shall satisfy the uniqueness rules defined by the product URL namespace.

### SFR-POST-007
The system shall support preview for authorized users.

## 4.5 Pages

### SFR-PAGE-001
Authorized users shall be able to create, edit, publish, and archive pages.

### SFR-PAGE-002
Draft pages shall not be publicly discoverable through normal public routes or search.

## 4.6 Taxonomy

### SFR-TAX-001
Authorized users shall manage categories.

### SFR-TAX-002
Authorized users shall manage tags.

### SFR-TAX-003
Post relationships to categories and tags shall remain referentially valid after taxonomy changes.

## 4.7 Media

### SFR-MEDIA-001
Authorized users shall upload permitted media.

### SFR-MEDIA-002
Media shall support searchable metadata.

### SFR-MEDIA-003
Images shall support alt text.

### SFR-MEDIA-004
Deletion of referenced media shall not silently break content.

## 4.8 Comments

### SFR-COM-001
When enabled, comments shall support:
- Pending
- Approved
- Spam
- Rejected

### SFR-COM-002
Only Approved comments shall be publicly rendered.

## 4.9 Menus

### SFR-MENU-001
Authorized users shall create and modify navigation menus.

### SFR-MENU-002
Menu items shall support links to internal content and custom URLs.

### SFR-MENU-003
Menu ordering shall be persistent.

## 4.10 SEO

### SFR-SEO-001
Posts and pages shall support:
- SEO title
- meta description
- Open Graph image
- index/noindex state

### SFR-SEO-002
Global SEO defaults shall act as predictable fallbacks.

## 4.11 Settings

### SFR-SET-001
Authorized users shall manage site-level settings defined by the PRD.

## 4.12 Dashboard

### SFR-DASH-001
The dashboard shall show counts for:
- Published Posts
- Draft Posts
- Scheduled Posts
- Pending Comments

### SFR-DASH-002
Dashboard widgets shall respect user permissions.

---

# 5. Non-Functional Requirements

### SNFR-001 — Usability
An Editor shall reach Create Post from the dashboard within three navigation actions.

### SNFR-002 — Reliability
A write action shall not return a success state unless the requested change was committed.

### SNFR-003 — Data Integrity
Relational data shall not be left in an invalid state after a successful request.

### SNFR-004 — Accessibility
Primary forms shall provide labels, validation feedback, focus visibility, and understandable action names.

### SNFR-005 — Browser Experience
The administration interface shall support modern evergreen browsers covered by the selected frontend stack.

### SNFR-006 — Responsive Administration
Primary admin workflows shall remain usable on desktop and tablet viewport sizes.

### SNFR-007 — Security
No known critical security defect may remain open at production release.

### SNFR-008 — Observability
Operational failures shall create server-side logs sufficient to diagnose the failure without exposing sensitive information to the user.

---

# 6. Application Architecture

## 6.1 Architecture Style

The MVP shall use a **modular monolith**.

The application remains one deployable Laravel application.

### ARCH-001
The system shall not split modules into independently deployed services.

### ARCH-002
Modules may be organized by feature/domain while remaining inside the Laravel application.

## 6.2 Recommended Logical Layers

```text
HTTP / Livewire Interface
        │
        ▼
Authorization + Validation
        │
        ▼
Application Actions / Services
        │
        ▼
Domain Rules / Models
        │
        ▼
Eloquent / Database / Storage
```

## 6.3 Recommended Feature Areas

```text
Application
├── Authentication
├── Users
├── Access Control
├── Content
│   ├── Posts
│   └── Pages
├── Taxonomy
├── Media
├── Comments
├── Navigation
├── SEO
├── Settings
├── Dashboard
└── Audit
```

### ARCH-003
Controllers and Livewire components shall coordinate input/output but shall not become repositories for complex business logic.

### ARCH-004
Business operations that have meaningful rules or side effects shall be represented by explicit Actions or Services.

Examples include:

- Publish Post
- Schedule Post
- Archive Post
- Moderate Comment
- Update Site Settings
- Change User Status

### ARCH-005
Laravel conventions shall be preferred over custom framework abstractions.

---

# 7. Authentication Requirements

### AUTH-001
Laravel-native session authentication shall be the default authentication mechanism for the admin application.

### AUTH-002
Passwords shall be stored using Laravel-supported secure password hashing.

### AUTH-003
Password reset shall use Laravel-native password reset capabilities where possible.

### AUTH-004
Login shall regenerate the session identifier after successful authentication.

### AUTH-005
Logout shall invalidate the current session and regenerate the CSRF token.

### AUTH-006
Authentication error messages shall not disclose sensitive account state beyond product requirements.

### AUTH-007
Rate limiting shall protect repeated authentication attempts.

### AUTH-008
A disabled account shall not successfully authenticate.

### AUTH-009
MVP shall not require social login, SSO, OAuth login, or passwordless authentication.

---

# 8. Authorization Requirements

### AUTHZ-001
Authorization shall use Laravel Gates and/or Policies as the primary enforcement mechanism.

### AUTHZ-002
Authorization shall be checked server-side for protected operations.

### AUTHZ-003
UI action visibility shall follow the same permission source as server-side authorization.

### AUTHZ-004
Ownership rules shall apply to Author operations where the PRD limits actions to own content.

### AUTHZ-005
Permission names shall be stable and action-oriented.

Recommended permission vocabulary:

```text
posts.view
posts.create
posts.update
posts.delete
posts.publish
posts.schedule

pages.view
pages.create
pages.update
pages.delete
pages.publish

media.view
media.upload
media.update
media.delete

comments.view
comments.moderate

categories.manage
tags.manage

menus.manage
seo.manage
users.manage
roles.manage
settings.manage
audit.view
```

### AUTHZ-006
Permission checks shall be covered by automated tests for every protected module.

---

# 9. User Role Architecture

## 9.1 Role Model

Roles group permissions; permissions define capabilities.

```text
User
  │
  └── Role(s)
        │
        └── Permission(s)
```

### ROLE-001
The MVP shall seed the default roles from the PRD.

### ROLE-002
Default roles shall be configurable through permissions without changing product business rules.

### ROLE-003
Super Admin shall have full administrative capability.

### ROLE-004
Author shall not publish by default.

### ROLE-005
Editor shall be able to manage editorial content according to the PRD permission matrix.

### ROLE-006
Changing a user's role or permission shall affect subsequent authorized requests.

### ROLE-007
The role architecture shall not be tied to a specific customer or industry.

---

# 10. Session Management

### SESSION-001
MVP shall use Laravel session management.

### SESSION-002
Database-backed sessions are recommended for the production VPS because they provide predictable shared state and operational visibility without requiring Redis.

### SESSION-003
Session cookies shall use secure settings appropriate to HTTPS production environments.

### SESSION-004
Session lifetime shall be configurable via environment configuration.

### SESSION-005
Session data shall not contain plaintext passwords or unnecessary sensitive secrets.

### SESSION-006
Logout shall invalidate the active session.

### SESSION-007
Session storage shall be cleaned through the framework-supported mechanism.

### SESSION-008
The application shall not depend on sticky sessions in the MVP.

---

# 11. Database Requirements

## 11.1 Database Platform

Actual version:

**TBD — Requires Environment Verification**

Target for a new deployment:

**MySQL 8.4.x LTS**

## 11.2 Core Data Domains

The database shall support persistence for:

- users;
- roles;
- permissions;
- user-role relationships;
- role-permission relationships;
- posts;
- pages;
- categories;
- tags;
- content-taxonomy relationships;
- media;
- comments;
- menus;
- menu items;
- site settings;
- SEO metadata where not embedded in content records;
- sessions when database sessions are enabled;
- jobs / failed jobs when database queues are enabled;
- cache records when database cache is enabled;
- password reset tokens;
- audit/activity records.

## 11.3 Data Integrity

### DB-001
Schema changes shall be managed using Laravel migrations.

### DB-002
Foreign-key relationships shall be enforced where they improve integrity and do not conflict with explicit retention behavior.

### DB-003
Delete behavior shall be explicitly defined for each relationship.

### DB-004
Content ownership shall not be silently lost when users are deactivated or removed.

### DB-005
Public slug uniqueness shall be enforced at the database level where practical.

### DB-006
Frequently filtered columns shall be indexable based on measured query use.

Likely index candidates include:

- post status;
- publish date;
- author;
- slug;
- category relationship;
- comment status;
- created date;
- user email.

### DB-007
Database timestamps shall use one consistent application timezone strategy.

### DB-008
Migrations shall be reversible where safe.

### DB-009
Production migration operations that destroy data shall require explicit review.

---

# 12. Validation Strategy

### VAL-001
All user-controlled input shall be validated server-side.

### VAL-002
Livewire validation may provide immediate feedback, but it shall not replace server-side rule enforcement.

### VAL-003
Reusable validation rules shall be centralized when the same domain rule occurs across multiple interfaces.

### VAL-004
Validation shall distinguish:
- required fields;
- format errors;
- uniqueness conflicts;
- authorization failures;
- invalid state transitions;
- file violations.

### VAL-005
Slug validation shall enforce the product's URL uniqueness constraints.

### VAL-006
Scheduled publication time shall be validated against the configured site/application timezone and required future-time rule.

### VAL-007
File upload validation shall verify allowed type, size, and upload success.

### VAL-008
Validation messages shown to users shall be understandable and actionable.

---

# 13. Business Logic Strategy

### BL-001
Business rules from the PRD shall not be duplicated independently in multiple UI components.

### BL-002
State transitions shall have explicit rules.

Valid post states:

```text
Draft
Scheduled
Published
Archived
```

### BL-003
Transition logic shall prevent invalid states such as:
- Scheduled without a valid future publication time;
- public Draft content;
- public Pending comments;
- unauthorized publication.

### BL-004
Business operations with side effects shall execute through an application-level Action or Service.

### BL-005
Models may contain small invariant or state helper logic but shall not become large service containers.

### BL-006
Events may be emitted after meaningful business actions when another concern needs to react without coupling the action directly.

Potential events:

- PostPublished
- PostScheduled
- PostArchived
- CommentModerated
- UserStatusChanged
- SettingsUpdated

Events shall only be introduced when at least one real listener/use case exists.

---

# 14. Service Layer Strategy

### SERVICE-001
A service layer shall be used selectively, not universally.

### SERVICE-002
Simple CRUD with no meaningful business rule may use the normal Laravel request/component → model flow.

### SERVICE-003
Complex or reusable use cases shall use explicit Actions or Services.

Recommended initial Actions:

- `PublishPost`
- `SchedulePost`
- `ArchivePost`
- `ModerateComment`
- `UpdateSiteSettings`
- `ChangeUserStatus`

### SERVICE-004
Services shall not duplicate Eloquent merely to create an artificial architectural layer.

### SERVICE-005
Actions shall accept validated, authorized inputs.

### SERVICE-006
Actions that modify multiple related records shall define transaction boundaries.

---

# 15. Repository Strategy if Required

## Decision

**Repository pattern is NOT required for the MVP by default.**

### REPO-001
Eloquent shall be used directly by application services, actions, and query objects where appropriate.

### REPO-002
A repository abstraction shall only be introduced when at least one of the following becomes true:

1. the same domain persistence requires multiple interchangeable storage backends;
2. a complex persistence boundary materially improves testability or readability;
3. an external data source must be represented behind a stable interface;
4. a future API/integration requires a clear persistence abstraction that cannot be expressed cleanly through Eloquent/query objects.

### REPO-003
Repositories shall not be created one-per-model simply to wrap standard Eloquent methods.

---

# 16. Transaction Management

### TX-001
Database transactions shall be used for business operations where partial success would violate data integrity.

Examples:

- user creation plus role assignment;
- role permission updates;
- content publication plus related publication state changes;
- content deletion/reassignment workflows;
- menu structure updates involving multiple records.

### TX-002
External side effects such as email shall not be relied upon to complete inside the database transaction.

### TX-003
Queued work triggered by a transaction shall only operate on committed state.

### TX-004
Transaction scope shall be kept as short as practical.

### TX-005
A failed transaction shall not produce a success message.

---

# 17. Queue Architecture

## MVP Decision

Use Laravel's native queue system with the **database queue driver** by default for a single VPS.

Redis is not required for MVP.

### QUEUE-001
Long-running or retryable tasks shall be eligible for queue execution.

Initial queue candidates:

- transactional email;
- non-blocking notification email;
- future media optimization if added;
- future import/export generation;
- future webhook delivery.

### QUEUE-002
Core content save operations shall not be queued if the user requires immediate confirmation that data was committed.

### QUEUE-003
Production shall run at least one supervised queue worker when queued jobs are enabled.

### QUEUE-004
Failed jobs shall be persisted and inspectable.

### QUEUE-005
Queue retry policy shall be defined per job class based on idempotency and failure mode.

### QUEUE-006
Jobs shall be designed to tolerate duplicate execution where feasible.

### QUEUE-007
Queue worker deployment shall include a controlled restart/reload after application deployment.

---

# 18. Scheduled Jobs

Laravel Scheduler shall be the single scheduling entry point.

### SCHED-001
The VPS shall invoke Laravel Scheduler once per minute.

### SCHED-002
Scheduled publishing shall be processed through a scheduled command/job.

### SCHED-003
The scheduled publishing process shall:

1. locate content eligible for publication;
2. verify status;
3. verify publication time;
4. publish only eligible content;
5. avoid publishing the same content multiple times as separate business actions.

### SCHED-004
Scheduler execution shall use a mechanism that avoids overlapping runs when overlap could create inconsistent state.

### SCHED-005
Scheduler failures shall be logged.

### SCHED-006
Future scheduled housekeeping may include:
- expired session cleanup;
- old temporary file cleanup;
- retention cleanup;
- backup triggers.

Only jobs required by an implemented feature shall be enabled.

---

# 19. Cache Strategy

## MVP Decision

Use Laravel cache abstractions.

No Redis requirement for MVP.

### CACHE-001
Cache shall be used only where it improves measured or predictable read performance.

Initial cache candidates:

- global site settings;
- navigation menus;
- public taxonomy lists;
- stable SEO defaults.

### CACHE-002
Content writes shall invalidate related cached values.

### CACHE-003
Cached authorization data shall not remain stale after permission changes beyond the next request behavior required by the PRD.

### CACHE-004
Cache keys shall be namespaced by feature.

### CACHE-005
Cache shall not be the source of truth.

### CACHE-006
The application shall remain correct after cache clear.

### CACHE-007
Database cache is recommended for the initial single-VPS deployment if a shared cache store is desired without Redis.

---

# 20. Notification Architecture

## 20.1 In-App Feedback

### NOTIFY-001
Create/update/delete/moderation operations shall provide success or failure feedback in the UI.

### NOTIFY-002
Validation messages shall be separate from generic system failure messages.

### NOTIFY-003
The MVP does not require a persistent notification center unless later approved in the PRD.

## 20.2 Domain Notifications

### NOTIFY-004
Laravel Notifications may be used when one product event must support one or more delivery channels.

### NOTIFY-005
Editorial approval chains and complex real-time notifications are out of scope for MVP.

---

# 21. Email Architecture

### EMAIL-001
Laravel Mail shall be the primary application email abstraction.

### EMAIL-002
The system shall support SMTP-compatible delivery through environment configuration.

### EMAIL-003
Password reset email is mandatory for MVP.

### EMAIL-004
Email sending that is not required to complete the current HTTP request should be queueable.

### EMAIL-005
Email templates shall use product branding without hard-coded customer-specific values.

### EMAIL-006
Mail failures shall be logged without exposing mail credentials.

### EMAIL-007
Production mail credentials shall only be supplied through environment/secrets configuration.

---

# 22. File Storage

## MVP Storage Model

Local Laravel filesystem storage is sufficient for a single-VPS MVP.

### STORAGE-001
File access shall use Laravel's filesystem abstraction.

### STORAGE-002
Public media shall be stored in an intended public disk/path.

### STORAGE-003
Private/internal files, if introduced, shall not be placed in publicly served paths.

### STORAGE-004
File names shall not overwrite existing media silently.

### STORAGE-005
Metadata shall remain linked to the stored file.

### STORAGE-006
Deletion of media referenced by content shall require explicit handling.

### STORAGE-007
The storage architecture shall not assume local paths in business logic so that future S3-compatible storage can be added.

### STORAGE-008
Allowed file extensions/MIME types and maximum sizes shall be configurable/documented.

### STORAGE-009
Executable uploads shall not be allowed as normal content media.

---

# 23. Logging

### LOG-001
Application logging shall use Laravel's logging facilities.

### LOG-002
Production logs shall capture:
- unhandled exceptions;
- queue failures;
- scheduler failures;
- mail transport failures;
- storage failures;
- relevant security/authorization anomalies where appropriate.

### LOG-003
Logs shall not intentionally contain:
- plaintext passwords;
- session cookies;
- API secrets;
- database passwords;
- mail passwords.

### LOG-004
Production log rotation/retention shall prevent uncontrolled disk growth.

### LOG-005
User-facing errors shall use a correlation/request identifier where practical when deeper support diagnosis is needed.

### LOG-006
`APP_DEBUG` shall be disabled in production.

---

# 24. Activity Logging

Activity logging satisfies the PRD audit trail requirement.

### ACT-001
Activity logging shall be separate in purpose from technical application logs.

### ACT-002
MVP shall record at least:
- post publication;
- post archive;
- user creation;
- user status change;
- role/permission change;
- site settings update.

### ACT-003
Each activity record shall identify:
- actor;
- action;
- target/resource;
- timestamp.

### ACT-004
The activity log may include relevant before/after metadata when useful, but sensitive values shall not be recorded.

### ACT-005
Activity records shall not be editable by ordinary editorial roles.

### ACT-006
The MVP audit trail is operational accountability logging, not an immutable regulatory ledger.

### ACT-007
A small internal activity logging service or Laravel-native event/listener approach shall be preferred over introducing a large audit package unless the package provides clear value and is approved.

---

# 25. Error Handling

### ERR-001
Laravel's centralized exception handling shall be used for unhandled application exceptions.

### ERR-002
Expected domain failures shall return clear user-facing messages.

### ERR-003
Production responses shall not expose:
- stack traces;
- SQL;
- server paths;
- secrets;
- configuration values.

### ERR-004
HTTP status behavior shall distinguish:
- validation failure;
- unauthenticated;
- forbidden;
- not found;
- conflict where applicable;
- server error.

### ERR-005
Livewire component failures shall preserve user input where reasonably possible.

### ERR-006
Unexpected failures shall be logged.

### ERR-007
404 and 500 responses shall have product-appropriate user-facing pages.

### ERR-008
A failed upload shall not create a valid-looking media record.

---

# 26. API Requirements

## MVP Decision

A public application API is **out of scope for MVP**.

### API-001
The MVP shall not require a REST or GraphQL API for the admin interface.

### API-002
The public website shall be rendered through normal Laravel web routes and Blade/Livewire where appropriate.

### API-003
Internal JSON responses used by Livewire do not constitute a supported public API contract.

### API-004
Future API development shall use explicit versioning and authorization requirements.

### API-005
Future API endpoints shall reuse the same business actions and authorization rules rather than duplicate business logic.

### API-006
GraphQL is explicitly outside MVP per PRD.

---

# 27. Webhook Requirements if Applicable

## MVP Decision

Webhooks are **not required for MVP**.

### WEBHOOK-001
No MVP business process shall depend on outbound or inbound webhooks.

### WEBHOOK-002
Future outbound webhook delivery shall be asynchronous and retryable.

### WEBHOOK-003
Future webhook signing shall allow recipients to verify authenticity.

### WEBHOOK-004
Future webhook logs shall capture delivery status without storing sensitive secrets.

---

# 28. Search Architecture

## MVP Decision

Use MySQL/Eloquent search and filtering.

External search infrastructure is not required.

### SEARCH-ARCH-001
Post search shall support title search.

### SEARCH-ARCH-002
Post filtering shall support:
- status;
- author;
- category;
- publish period when included in the release.

### SEARCH-ARCH-003
Page search shall support title.

### SEARCH-ARCH-004
Media search shall support filename and supported metadata.

### SEARCH-ARCH-005
Comment filtering shall support moderation status.

### SEARCH-ARCH-006
Queries shall be paginated.

### SEARCH-ARCH-007
Search inputs shall be validated and safely parameterized through the database abstraction.

### SEARCH-ARCH-008
MySQL indexes shall be added when query plans/data volume demonstrate value.

### SEARCH-ARCH-009
Laravel Scout, Elasticsearch, Meilisearch, Typesense, or another external search engine shall not be introduced in MVP unless measured requirements cannot be met using MySQL.

---

# 29. Import/Export

## MVP Position

The PRD does not require full import/export functionality in MVP.

### IMP-EXP-001
Bulk migration from WordPress or another CMS is not required for MVP.

### IMP-EXP-002
If a basic export feature is selected for the MVP release, output shall contain only data the requesting user is authorized to access.

### IMP-EXP-003
Large future exports shall be generated asynchronously.

### IMP-EXP-004
Future imports shall validate the entire input structure and report rejected rows/items.

### IMP-EXP-005
Future import operations that modify many records shall support safe failure/recovery behavior.

---

# 30. Reporting Architecture

MVP reporting is operational reporting only.

### REPORT-001
Counts used on the dashboard shall be calculated from authoritative application data.

### REPORT-002
Content lists with filters serve as the primary operational report interface.

### REPORT-003
MVP shall not introduce a data warehouse or OLAP system.

### REPORT-004
MVP shall not require traffic analytics, revenue analytics, attribution, or conversion analytics.

### REPORT-005
Reporting queries shall not bypass authorization rules.

---

# 31. Security Architecture

## 31.1 Security Principles

- deny unauthorized actions server-side;
- validate all user input;
- use secure framework defaults;
- minimize dependencies;
- keep secrets out of source control;
- prevent direct public access to sensitive files;
- keep production debug disabled.

## 31.2 Requirements

### SEC-ARCH-001
All production traffic shall use HTTPS.

### SEC-ARCH-002
CSRF protection shall apply to state-changing web requests.

### SEC-ARCH-003
Passwords shall use framework-supported secure hashes.

### SEC-ARCH-004
Authorization policies/gates shall protect administrative actions.

### SEC-ARCH-005
User-generated rich content shall be handled in a way that prevents unauthorized script execution.

### SEC-ARCH-006
File uploads shall enforce allowlisted content rules.

### SEC-ARCH-007
Laravel/Composer/npm dependencies shall be reviewed for known security issues before release.

### SEC-ARCH-008
Production shall run with `APP_DEBUG=false`.

### SEC-ARCH-009
The web server document root shall point to Laravel's `public` directory.

### SEC-ARCH-010
`.env`, source configuration, storage internals, and vendor files shall not be directly served by the web server.

### SEC-ARCH-011
Authentication endpoints shall be rate limited.

### SEC-ARCH-012
Sensitive configuration shall be environment-specific.

### SEC-ARCH-013
Security-sensitive admin actions shall be represented in activity logs where required by the PRD.

### SEC-ARCH-014
MVP shall avoid third-party packages that duplicate robust Laravel-native security features without a concrete requirement.

---

# 32. Backup Requirements

Backup is an operational deployment requirement even though advanced backup management UI is not part of MVP.

### BACKUP-001
Production shall back up the MySQL database.

### BACKUP-002
Production shall back up user-uploaded files.

### BACKUP-003
Backups shall be stored separately from the active application data when operationally possible.

### BACKUP-004
At least one restoration procedure shall be documented and tested before commercial production release.

### BACKUP-005
Backup retention shall be documented per installation.

### BACKUP-006
Backups containing sensitive data shall be access-controlled.

### BACKUP-007
Application source code does not require runtime backup if safely stored in version control and release artifacts, but environment-specific secrets must have an independent secure recovery process.

### BACKUP-008
A UI-based backup manager is not required in MVP.

---

# 33. Testing Requirements

## 33.1 Test Levels

The project shall use:

- Unit Tests where isolated business logic provides value;
- Feature Tests for HTTP/application workflows;
- Livewire component tests for interactive admin behaviors;
- Database tests for persistence/business constraints;
- targeted browser/end-to-end tests for critical flows.

## 33.2 Mandatory Test Areas

### TEST-001
Authentication success/failure.

### TEST-002
Inactive-user login rejection.

### TEST-003
Password reset flow.

### TEST-004
Permission enforcement for each protected module.

### TEST-005
Author ownership restrictions.

### TEST-006
Post status transitions.

### TEST-007
Scheduled publication before and after publication time.

### TEST-008
Draft non-visibility on public routes.

### TEST-009
Published content visibility.

### TEST-010
Archived content listing behavior.

### TEST-011
Comment moderation visibility.

### TEST-012
Media type/size validation.

### TEST-013
Menu persistence and public rendering.

### TEST-014
SEO fallback behavior.

### TEST-015
Search/filter behavior.

### TEST-016
Core validation errors.

### TEST-017
Critical transaction rollback cases.

### TEST-018
Activity log creation for required actions.

## 33.3 Release Quality Gate

### TEST-019
All critical tests shall pass before production release.

### TEST-020
No open critical severity defect is permitted.

### TEST-021
No known critical security vulnerability is permitted.

### TEST-022
Regression tests shall be run after framework or major dependency upgrades.

---

# 34. Deployment Requirements

## 34.1 Deployment Target

Primary deployment:

**Linux VPS**

Actual server specification:

**TBD — Requires Environment Verification**

## 34.2 Required Runtime Services

A production VPS shall provide:

- web server;
- PHP-FPM or supported PHP application serving model;
- PHP 8.4.x target;
- MySQL 8.4.x target;
- Composer for release/build process as appropriate;
- Node.js/npm only where asset build occurs on-server;
- queue worker process;
- scheduler invocation;
- TLS/SSL;
- log rotation;
- backup mechanism.

## 34.3 Recommended Web Server

Nginx is recommended for the first production target.

### DEPLOY-001
Web server root shall point to `public/`.

### DEPLOY-002
`storage` and `bootstrap/cache` shall be writable by the application process as required.

### DEPLOY-003
Production deployment shall run Laravel optimization/cache commands appropriate to the release.

### DEPLOY-004
Long-running queue workers shall be reloaded after deployment.

### DEPLOY-005
Database migrations shall be executed in a controlled deployment step.

### DEPLOY-006
A failed deployment shall have a documented rollback strategy.

### DEPLOY-007
Production source code shall not include developer-only debug configuration.

### DEPLOY-008
The application health endpoint shall be available for operational checks.

### DEPLOY-009
Only required ports shall be publicly exposed.

---

# 35. Environment Configuration

### ENV-001
Environment-specific values shall not be hard-coded in application logic.

### ENV-002
The `.env` file shall not be committed with real production secrets.

### ENV-003
The project shall provide an `.env.example` containing non-secret configuration keys required to start the application.

### ENV-004
Required configuration categories shall include:

- application name;
- environment;
- application URL;
- application key;
- debug mode;
- timezone/locale;
- database;
- session;
- cache;
- queue;
- filesystem;
- mail;
- logging.

### ENV-005
Production configuration shall be validated before release/deployment.

### ENV-006
After project initialization, exact technical versions shall be recorded from:
- `composer.lock`;
- `package-lock.json`/equivalent;
- `php -v`;
- MySQL server version;
- deployment runtime.

### ENV-007
This SRS must be updated if verified installed major versions differ from the target stack.

---

# 36. Performance Requirements

The authoritative PRD defines these product targets.

### PERF-ARCH-001
Normal admin pages shall target initial usable response ≤ 2.5 seconds under the agreed MVP test environment.

### PERF-ARCH-002
Admin search against the MVP test dataset shall target ≤ 2 seconds.

### PERF-ARCH-003
Normal post save operations shall return visible success/failure within ≤ 2 seconds, excluding large file uploads.

### PERF-ARCH-004
Lists shall use pagination.

### PERF-ARCH-005
The application shall avoid N+1 query patterns in common list/detail workflows.

### PERF-ARCH-006
Public content rendering shall not wait for non-essential queued work.

### PERF-ARCH-007
Production deployment shall enable framework optimization appropriate to Laravel 13.

### PERF-ARCH-008
Performance changes shall be based on measurement before introducing external infrastructure.

### PERF-ARCH-009
Redis, full-page caching, CDN, Octane, and external search are optimization options, not MVP dependencies.

---

# 37. Scalability Requirements

## MVP Scale Model

The initial target is a single VPS serving a small-to-medium content website.

### SCALE-001
The application shall be stateless enough at the HTTP layer that future horizontal scaling is possible after session/cache/storage decisions are adapted.

### SCALE-002
Business logic shall not rely on process-local memory as the system of record.

### SCALE-003
Queued workload shall be separable from web request processing.

### SCALE-004
Uploaded storage shall use Laravel filesystem abstraction to allow future migration to object storage.

### SCALE-005
Search shall begin with MySQL and be replaceable later without changing core product behavior.

### SCALE-006
The architecture shall permit future Redis adoption for cache/session/queue if traffic justifies it.

### SCALE-007
The architecture shall not introduce multi-tenancy until the SaaS roadmap requires it.

### SCALE-008
No premature distributed-system architecture shall be created for hypothetical future scale.

---

# 38. Maintainability Requirements

### MAINT-001
The application shall follow standard Laravel project conventions unless deviation has documented value.

### MAINT-002
Business logic shall be named around business operations rather than generic utility classes.

### MAINT-003
Dead code and speculative abstractions shall not be added to the MVP.

### MAINT-004
Third-party packages shall be introduced only when they:
1. solve a real requirement;
2. are actively maintained;
3. are compatible with the project's Laravel/PHP versions;
4. reduce more complexity than they add.

### MAINT-005
Code style shall be automatically enforceable using Laravel/PHP ecosystem tooling selected by the project.

### MAINT-006
Database migrations shall represent schema evolution.

### MAINT-007
Critical application behavior shall be protected by automated regression tests.

### MAINT-008
Configuration defaults shall be documented.

### MAINT-009
Feature-specific query logic may be extracted into query objects when list/filter complexity grows, instead of bloating controllers/components.

### MAINT-010
New architecture layers shall require a demonstrated use case.

---

# 39. Upgrade Strategy

## 39.1 Dependency Policy

### UPGRADE-001
Exact installed versions shall be locked by Composer and frontend lock files.

### UPGRADE-002
Patch/minor updates shall be evaluated regularly for bug and security fixes.

### UPGRADE-003
Major Laravel upgrades shall be performed intentionally using the official upgrade guide.

### UPGRADE-004
Production upgrades shall be validated in a non-production environment first.

### UPGRADE-005
Automated tests shall run before and after framework/dependency upgrades.

### UPGRADE-006
Database backup shall be completed before migrations that may alter production data materially.

### UPGRADE-007
Custom code shall avoid framework-internal APIs when stable public Laravel APIs exist.

## 39.2 Product Versioning

Recommended product release model:

```text
1.0.x  Patch / bug / security fixes
1.x    Backward-compatible product improvements where practical
2.0    Major product/architecture capabilities with documented upgrade path
```

## 39.3 Laravel Upgrade Principle

ContentFlow shall not fork Laravel framework behavior.

The product should remain close to documented Laravel conventions so annual major upgrades remain manageable.

## 39.4 Database Upgrade Principle

MySQL 8.4 LTS is preferred for stability in the initial product line. Database major-version upgrades require compatibility verification, backup, migration rehearsal, and rollback planning.

---

# 40. Technical Constraints

### CONSTRAINT-001
The authoritative MVP scope is defined by `docs/PRD.md`.

### CONSTRAINT-002
Backend target is Laravel 13.x, subject to environment verification.

### CONSTRAINT-003
PHP target is 8.4.x, subject to environment verification.

### CONSTRAINT-004
Database target is MySQL 8.4.x LTS, subject to environment verification.

### CONSTRAINT-005
Frontend stack is:
- Blade
- Livewire
- Tailwind CSS
- Alpine.js

### CONSTRAINT-006
Deployment target is VPS.

### CONSTRAINT-007
The application shall remain a modular monolith for MVP.

### CONSTRAINT-008
Laravel-native functionality shall be preferred.

### CONSTRAINT-009
Repository pattern shall not be mandatory.

### CONSTRAINT-010
Microservices shall not be used.

### CONSTRAINT-011
Redis shall not be a required dependency for MVP.

### CONSTRAINT-012
External search services shall not be required for MVP.

### CONSTRAINT-013
Public API and webhooks shall not be required for MVP.

### CONSTRAINT-014
Multi-tenancy and SaaS billing shall not be implemented in MVP.

### CONSTRAINT-015
AI content generation shall not be implemented in MVP.

### CONSTRAINT-016
Visual page builder shall not be implemented in MVP.

### CONSTRAINT-017
E-commerce functionality shall not be implemented in MVP.

### CONSTRAINT-018
Complex editorial approval workflows shall not be implemented in MVP.

### CONSTRAINT-019
Technical architecture shall prioritize:
1. simplicity;
2. correctness;
3. security;
4. maintainability;
5. commercial reusability;
6. measured scalability.

---

# Technical Decision Summary

| Area | MVP Decision |
|---|---|
| Architecture | Modular monolith |
| Backend | Laravel 13.x target; installed version TBD |
| PHP | PHP 8.4.x target; installed version TBD |
| Database | MySQL 8.4.x LTS target; installed version TBD |
| UI | Blade + Livewire |
| Styling | Tailwind CSS |
| Client Interactions | Alpine.js bundled with Livewire where possible |
| Auth | Laravel session authentication |
| Authorization | Policies / Gates + role/permission model |
| ORM | Eloquent |
| Repository Pattern | No, unless a concrete need emerges |
| Service Layer | Selective Actions/Services |
| Transactions | Laravel database transactions for multi-record invariants |
| Queue | Laravel database queue |
| Scheduler | Laravel Scheduler |
| Cache | Laravel cache; database/file initially |
| Sessions | Database recommended |
| Search | MySQL/Eloquent |
| Files | Laravel filesystem, local public storage initially |
| Email | Laravel Mail + SMTP |
| API | Not required in MVP |
| Webhooks | Not required in MVP |
| Activity Log | Lightweight operational audit trail |
| Deployment | Linux VPS + Nginx recommended |
| Scaling | Vertical first; horizontal-ready boundaries without distributed-system complexity |

---

# Requirement Traceability Summary

This SRS preserves the following authoritative PRD behaviors:

| PRD Capability | SRS Coverage |
|---|---|
| Authentication | Sections 4, 7, 10, 31 |
| Users | Sections 4, 8, 9, 11 |
| Roles & Permissions | Sections 4, 8, 9 |
| Posts | Sections 4, 13, 16, 18 |
| Pages | Sections 4, 11 |
| Categories & Tags | Sections 4, 11, 28 |
| Media Library | Sections 4, 22, 31 |
| Comments | Sections 4, 13 |
| Menus | Sections 4, 19 |
| SEO | Sections 4, 19 |
| Settings | Sections 4, 19 |
| Dashboard | Sections 4, 30 |
| Search & Filters | Section 28 |
| Audit Trail | Sections 24, 31 |
| Scheduled Publishing | Sections 13, 17, 18 |
| Notifications | Sections 20, 21 |
| Import/Export | Section 29 |
| Security | Sections 7, 8, 10, 22, 31 |
| Performance | Section 36 |
| Deployment | Sections 32, 34, 35 |
| Future API/Webhooks | Sections 26, 27 |
| MVP simplicity | Sections 6, 15, 37, 40 |

---

# Environment Verification Checklist

When the actual Laravel project is created or made available, the following verification shall be performed before treating the stack as final:

- [ ] Read `composer.json`
- [ ] Read `composer.lock`
- [ ] Run/read `php -v`
- [ ] Verify `php artisan --version`
- [ ] Verify MySQL server version
- [ ] Read `package.json`
- [ ] Read frontend lock file
- [ ] Verify Livewire installed version
- [ ] Verify Tailwind installed version
- [ ] Verify whether Alpine is bundled or separately installed
- [ ] Verify Vite version
- [ ] Verify session driver
- [ ] Verify cache driver
- [ ] Verify queue driver
- [ ] Verify filesystem disk
- [ ] Verify mail driver
- [ ] Verify server OS/web server
- [ ] Update this SRS if actual versions differ from target versions

Until that verification is completed, all installed version fields remain:

**TBD — Requires Environment Verification**

---

# External Compatibility References

These references are informational and are not substitutes for inspecting the actual project environment.

- Laravel 13 Deployment / Server Requirements: https://laravel.com/docs/13.x/deployment
- Laravel 13 Documentation: https://laravel.com/docs/13.x
- Livewire 4 Installation: https://livewire.laravel.com/docs/4.x/installation
- Tailwind CSS Installation: https://tailwindcss.com/docs/installation
- MySQL 8.4 LTS Release Model: https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html

---

# Final Engineering Principle

> ContentFlow CMS must solve the product requirements using the smallest maintainable Laravel architecture that is secure, testable, production-ready, and reusable across commercial installations.

A new abstraction, service, package, infrastructure component, or distributed-system pattern shall only be introduced when an implemented requirement demonstrates the need.

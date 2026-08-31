# Project Structure
## ContentFlow CMS

---

## Document Control

| Field | Value |
|---|---|
| Document | Project Structure |
| Product | ContentFlow CMS |
| Sources | `PRD`, `SRS`, `SYSTEM_DESIGN`, `DATABASE`, `BUSINESS_FLOW`, `CURSOR.md` |
| Architecture | Laravel modular monolith |
| Scope | MVP / Version 1.0 |
| Status | Recommended structure for engineering |
| Last Updated | 2026-08-29 |

### Purpose

This document defines the recommended Laravel directory layout and where each application concern belongs.

It prefers:

- Laravel conventions;
- feature-oriented namespaces inside standard folders;
- selective Actions for meaningful use cases;
- no package-per-module split;
- no generic repository layer;
- no speculative DTOs/Services/Traits.

---

# 1. Architecture Choice

ContentFlow CMS is **one Laravel application** with **logical modules**, not microservices and not Composer packages per domain.

```text
UI coordinates.
Validation validates.
Policies authorize.
Actions / Services execute business use cases.
Models represent persisted domain state.
Eloquent persists data.
Events decouple real side effects.
Jobs handle asynchronous work.
```

### What we intentionally avoid in MVP

| Avoid | Why |
|---|---|
| Microservices / separate API app | Out of scope; increases ops cost |
| One repository per model | Ceremony without MVP benefit |
| Fat “god” Services wrapping every CRUD | Hides Eloquent without adding rules |
| Module packages (`packages/content`, etc.) | Premature before real reuse |
| Speculative DTO layers | Livewire / validated arrays are enough initially |
| Soft-delete everywhere | Status fields already model lifecycle |

---

# 2. Logical Modules

Keep behavior separated by domain. Namespaces mirror these modules; folders stay Laravel-standard.

```text
Authentication   Login, logout, password recovery
Users            Account state, profile, activation / deactivation
AccessControl    Roles, permissions, role assignment
Content          Posts, pages, publishing lifecycle
Taxonomy         Categories, tags
Media            Uploads, metadata, reference checks
Comments         Submission + moderation
Navigation       Menus, menu items
SEO              Per-content metadata + fallbacks (with Settings)
Settings         Site-wide configuration singleton
Dashboard        Operational summaries / shortcuts (read-mostly)
Audit            Activity logs for defined admin actions
```

### Ownership examples

| Belongs here | Does not belong here |
|---|---|
| Content owns post status transitions | Content does not own credentials |
| Media owns file metadata + delete guards | Media does not own publish rules |
| Comments owns moderation states | Comments does not own post SEO |
| Dashboard aggregates counts | Dashboard does not mutate domain state |
| Audit records business events | Audit is not application error logging |

---

# 3. Recommended Directory Tree

Feature namespaces live under the normal Laravel roots. Do **not** invent a custom `app/Modules/*` package tree unless a future need forces it.

```text
app/
├── Actions/                      # Meaningful use cases only
│   ├── AccessControl/
│   ├── Audit/
│   ├── Comments/
│   ├── Content/
│   ├── Media/
│   ├── Navigation/
│   ├── Settings/
│   ├── Taxonomy/
│   └── Users/
├── Console/
│   └── Commands/                 # Scheduler entrypoints (thin)
├── DTOs/                         # Optional; only when payload reuse is real
│   └── ...
├── Enums/
│   ├── CommentStatus.php
│   ├── PostStatus.php
│   ├── PageStatus.php
│   └── UserStatus.php
├── Events/
│   ├── Comments/
│   ├── Content/
│   ├── Settings/
│   └── Users/
├── Exceptions/                   # Domain/business exceptions if needed
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                # Thin; prefer Livewire for admin UI
│   │   ├── Auth/
│   │   └── Public/
│   ├── Middleware/
│   └── Requests/                 # Form Requests (controller flows)
│       ├── Admin/
│       ├── Auth/
│       └── Public/
├── Jobs/
│   ├── Content/
│   ├── Media/
│   └── Notifications/
├── Listeners/
│   ├── Audit/
│   ├── Content/
│   └── Notifications/
├── Livewire/
│   ├── Admin/
│   │   ├── Comments/
│   │   ├── Dashboard/
│   │   ├── Media/
│   │   ├── Menus/
│   │   ├── Pages/
│   │   ├── Posts/
│   │   ├── Roles/
│   │   ├── Settings/
│   │   ├── Taxonomy/
│   │   └── Users/
│   ├── Forms/                    # Livewire Form objects (reusable validation)
│   │   └── Admin/
│   └── Public/                   # Sparse; only when interactivity is needed
├── Models/
│   ├── Category.php
│   ├── Comment.php
│   ├── Media.php
│   ├── MediaReference.php
│   ├── Menu.php
│   ├── MenuItem.php
│   ├── Page.php
│   ├── Permission.php
│   ├── Post.php
│   ├── Role.php
│   ├── SiteSetting.php
│   ├── Tag.php
│   ├── User.php
│   └── ActivityLog.php
├── Notifications/
│   └── Auth/
├── Policies/
│   ├── CommentPolicy.php
│   ├── MediaPolicy.php
│   ├── MenuPolicy.php
│   ├── PagePolicy.php
│   ├── PostPolicy.php
│   ├── RolePolicy.php
│   ├── SiteSettingPolicy.php
│   └── UserPolicy.php
├── Providers/
├── Queries/                      # Optional read models for complex listings
│   ├── Comments/
│   ├── Content/
│   ├── Dashboard/
│   └── Media/
├── Services/                     # Rare; broader orchestration / integrations
│   ├── Audit/
│   └── Settings/
├── Support/                      # Small shared helpers (prefer classes over globals)
│   └── ...
└── View/
    └── Components/               # Blade class components when needed

bootstrap/
config/
database/
├── factories/
├── migrations/
└── seeders/
lang/                             # Prefer over scattered hard-coded strings
public/
resources/
├── css/
├── js/
├── views/
│   ├── admin/
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── posts/
│   │   ├── pages/
│   │   ├── media/
│   │   ├── comments/
│   │   ├── users/
│   │   ├── roles/
│   │   ├── categories/
│   │   ├── tags/
│   │   ├── menus/
│   │   ├── settings/
│   │   ├── seo/
│   │   ├── dashboard/
│   │   └── audit/
│   ├── auth/
│   ├── components/               # Shared Blade partials / anonymous components
│   ├── emails/
│   ├── layouts/
│   │   ├── admin.blade.php
│   │   └── public.blade.php
│   ├── livewire/                 # Livewire view templates
│   │   ├── admin/
│   │   └── public/
│   └── public/
│       ├── home/
│       ├── posts/
│       ├── pages/
│       ├── categories/
│       ├── tags/
│       └── partials/
routes/
├── web.php                       # Public routes
├── admin.php                     # Admin routes (auth + permission middleware)
├── auth.php                      # Login / password reset
└── console.php
storage/
tests/
├── Feature/
│   ├── Admin/
│   ├── Auth/
│   ├── Public/
│   └── Livewire/
├── Unit/
│   ├── Actions/
│   ├── Enums/
│   └── Models/
└── Browser/                      # Optional critical E2E only
```

---

# 4. Where Each Concern Belongs

## 4.1 Models — `app/Models`

**Belong here**

- Eloquent mappings for business tables (`posts`, `pages`, `users`, …);
- relationships;
- casts (including backed Enums);
- query scopes;
- small state helpers (`isPublished()`, `isEditableBy(User $user)`).

**Do not put here**

- multi-step workflows (publish + audit + cache);
- mail / notifications;
- HTTP / Livewire concerns;
- filesystem orchestration beyond trivial accessors.

**MVP model list (aligned with DATABASE)**

`User`, `Role`, `Permission`, `Post`, `Page`, `Category`, `Tag`, `Media`, `MediaReference`, `Comment`, `Menu`, `MenuItem`, `SiteSetting`, `ActivityLog`

Keep models flat under `app/Models`. Subfolders are optional later if the count becomes noisy; they are not required for MVP.

---

## 4.2 Controllers — `app/Http/Controllers`

**Role:** thin HTTP coordinators.

Prefer Livewire for the admin dashboard. Use controllers mainly for:

- public website pages (`Public\PostController`, `Public\PageController`, …);
- authentication pages if not fully Livewire;
- simple redirects / downloads;
- any non-interactive admin endpoints.

**Typical flow**

```text
Route → Controller → Form Request → authorize() / Policy → Action (if needed) → View / Redirect
```

**Do not** implement publishing, moderation, role assignment, or menu rebuilds inside controllers.

---

## 4.3 Services — `app/Services` (selective)

Use a Service only when:

- several related operations share cohesive domain behavior; or
- an integration needs a stable boundary; or
- orchestration is broader than one Action.

**Good MVP candidates**

| Service | Why |
|---|---|
| `Audit\RecordActivity` or `ActivityLogger` | Shared audit write path used by many Actions |
| `Settings\SiteSettings` | Singleton read/update + cache invalidation |

**Avoid**

```text
PostService::create()
PostService::update()
PostService::delete()
```

if those methods only wrap Eloquent.

**Rule of thumb:** prefer a named Action for one use case; promote to a Service only when reuse/orchestration demands it.

---

## 4.4 Actions — `app/Actions/{Module}`

Actions are the primary home for meaningful business use cases.

**Create an Action when the operation has**

- workflow / state rules;
- multi-record integrity;
- side effects (events, cache busting, audit);
- reuse from Livewire, scheduler, or future API.

**Recommended initial Actions**

```text
Content/
  PublishPost
  SchedulePost
  UnpublishPost          # if product allows Published → Draft
  ArchivePost
  RestorePostToDraft
  PublishPage
  SchedulePage
  ArchivePage
  PublishScheduledPosts  # used by scheduler/command

Comments/
  SubmitComment
  ModerateComment

Media/
  StoreUploadedMedia
  DeleteReferencedMedia  # enforces reference checks

Navigation/
  UpdateMenuStructure

Settings/
  UpdateSiteSettings

Users/
  ChangeUserStatus
  CreateUserWithRole     # if create + role must be atomic

AccessControl/
  AssignRole
  SyncRolePermissions
```

**Action rules**

1. One meaningful use case per class.
2. Accept validated, authorized input (arrays, Models, or small DTOs).
3. Own transaction boundaries when partial writes would be invalid.
4. Emit Events only when a real Listener exists.
5. Contain no Blade / Livewire / HTTP response code.

**Simple CRUD** (create draft post with no special rules beyond validation + policy) may stay in Livewire → Model without inventing `CreatePost` solely for ceremony.

---

## 4.5 DTOs — `app/DTOs` (optional)

**Default: do not introduce DTOs.**

Validated Livewire Form properties, Form Request `validated()` arrays, and Eloquent models are enough for MVP.

**Introduce a DTO when**

- the same structured payload is passed across Actions / Jobs / Commands; or
- an Action signature becomes an unreadable bag of parameters; or
- a future API must share an explicit input shape.

Example (only if needed later):

```text
App\DTOs\Content\PublishPostData
App\DTOs\Settings\SiteSettingsData
```

Prefer `readonly` PHP classes over large DTO libraries.

---

## 4.6 Enums — `app/Enums`

Use backed string Enums for status fields that DATABASE / BUSINESS_FLOW define as closed sets.

| Enum | Values |
|---|---|
| `PostStatus` | `draft`, `scheduled`, `published`, `archived` |
| `PageStatus` | same pattern as posts (per DATABASE) |
| `CommentStatus` | `pending`, `approved`, `spam`, `rejected` |
| `UserStatus` | `active`, `inactive` |

Cast Enums on Models. Transition legality lives in Actions / Policies, not only in the Enum.

Do not Enum every string column (slugs, titles, permission names as free-form stable strings stay strings unless a closed set is product-defined).

---

## 4.7 Policies — `app/Policies`

**Belong here**

- permission checks (`posts.publish`, `settings.manage`, …);
- ownership rules (Author may edit own posts only);
- resource-level authorization for Models.

Register policies conventionally. Call them from Livewire / Controllers via `authorize()` / `$this->authorize()`.

**Do not** put workflow transitions or DB transactions in Policies.

UI visibility must use the same permission source as Policies (Gates / permission helpers), never a parallel hard-coded role list in Blade.

---

## 4.8 Form Requests — `app/Http/Requests`

**Use for controller-based HTTP flows**

- login / password reset;
- public comment submission (if controller-based);
- any classic form POST that is not Livewire.

**Livewire**

- simple rules: component `$rules` / `#[Validate]`;
- complex reusable forms: `app/Livewire/Forms/Admin/...`;
- do **not** force Form Requests into Livewire just for symmetry.

Shared rule objects / Rule classes may live under `app/Rules` when uniqueness or domain checks repeat.

---

## 4.9 Jobs — `app/Jobs`

Queue non-blocking work. MVP uses the **database queue**.

**Good Jobs**

| Job | Trigger |
|---|---|
| Send queued mail / notification | password reset, optional editorial mail |
| Heavy media post-processing | only if upload path needs async work |
| Batch cache warm / rebuild | rare; prefer targeted invalidation |

**Do not** put core publish rules only inside a Job. Scheduler / Jobs should call the same Actions used by the UI (`PublishPost`, `PublishScheduledPosts`).

Jobs must tolerate duplicate execution where feasible and must run against committed DB state.

---

## 4.10 Events — `app/Events`

Introduce an Event only when at least one Listener (or queued side effect) needs it.

**Candidates from SRS**

```text
PostPublished
PostScheduled
PostArchived
CommentModerated
UserStatusChanged
SettingsUpdated
```

Events carry identifiers / Models needed by listeners. They are not a substitute for Actions.

---

## 4.11 Listeners — `app/Listeners`

**Belong here**

- audit recording reactions (if not called directly from Actions);
- cache invalidation after settings/content changes;
- notification dispatch after domain events.

Keep listeners focused. Prefer calling a small Service (`ActivityLogger`) from either Action or Listener—pick one consistent pattern and stick to it.

---

## 4.12 Notifications — `app/Notifications`

Laravel Notifications for multi-channel or queued user messaging.

**MVP**

- password reset (Mail / Notification via Laravel auth);
- optional queued mail if product requires it.

**Not required in MVP**

- persistent in-app notification center;
- editorial approval chains;
- real-time push.

In-request UX feedback uses Livewire flash / toast, not the Notification system.

---

## 4.13 Repositories — generally **not used**

Per ADR-005 / SRS REPO-*:

- Eloquent is used directly from Actions, Queries, and Livewire.
- Do **not** create `PostRepository`, `UserRepository`, etc. by default.

**Revisit only if**

1. interchangeable storage backends appear; or
2. an external data source needs a stable interface; or
3. a persistence boundary materially improves testability beyond Query objects.

If introduced later, place under `app/Repositories/{Module}` and keep the surface small.

---

## 4.14 Traits — use sparingly

Prefer composition and clear base classes over Trait sprawl.

**Acceptable**

- tiny Eloquent concerns shared by 2–3 models (`HasSlug` only if truly shared and stable);
- Livewire concerns that Laravel / the project already use.

**Avoid**

- `HandlesEverything` Traits;
- moving Action logic into Traits to “reuse” UI and domain code together.

Default location if needed: `app/Models/Concerns` or `app/Livewire/Concerns`.

---

## 4.15 Helpers — `app/Support` (prefer classes)

Avoid global `helpers.php` unless a tiny pure function is used widely (e.g. permission check wrapper already standardized).

Prefer:

```text
App\Support\Slug\SlugGenerator
App\Support\Seo\SeoResolver
App\Support\Permissions\PermissionNames
```

SEO fallback resolution (content override → site defaults) belongs in a small Support/Service class, not in Blade.

---

## 4.16 Livewire Components — `app/Livewire`

Primary admin UI technology.

```text
Livewire/Admin/Posts/Index
Livewire/Admin/Posts/Edit
Livewire/Admin/Comments/ModerationTable
Livewire/Admin/Media/Library
Livewire/Admin/Menus/Editor
Livewire/Admin/Settings/General
Livewire/Admin/Dashboard/Overview
```

**Livewire may**

- bind form state;
- validate input;
- authorize;
- call Actions;
- manage filters / pagination UI;
- emit browser events for toasts.

**Livewire must not**

- own publishing state machines;
- duplicate Policy rules in ad-hoc `if ($role === ...)`;
- perform multi-table writes without an Action when integrity matters.

Views live under `resources/views/livewire/...` matching component namespaces.

---

## 4.17 Views — `resources/views`

| Area | Path |
|---|---|
| Admin chrome | `resources/views/admin/layouts`, `admin/components` |
| Public site | `resources/views/public/...` |
| Auth | `resources/views/auth/...` |
| Livewire templates | `resources/views/livewire/admin|public/...` |
| Mail | `resources/views/emails/...` |

Blade renders presentation. Business rules stay in Actions / Policies.

Public SEO tags should call a resolver (Support/Service), not re-implement fallbacks in every template.

---

## 4.18 Query Objects — `app/Queries` (optional)

Introduce when listing / filter / dashboard SQL becomes hard to read in Livewire.

```text
Queries/Content/PostIndexQuery
Queries/Media/MediaLibraryQuery
Queries/Comments/CommentModerationQuery
Queries/Dashboard/DashboardSummaryQuery
```

Query objects encapsulate **reads** only. Writes stay in Actions.

Do not create a Query for every `Model::query()`.

---

## 4.19 Tests — `tests`

Mirror modules, not framework internals.

```text
tests/Feature/Auth/...
tests/Feature/Admin/Posts/...
tests/Feature/Admin/Comments/...
tests/Feature/Public/PostVisibilityTest.php
tests/Feature/Livewire/Admin/...
tests/Unit/Actions/Content/PublishPostTest.php
tests/Unit/Enums/...
```

**Mandatory coverage themes (SRS)**

- auth success/failure + inactive rejection;
- password reset;
- permission + Author ownership;
- post status transitions + scheduled publish;
- draft hidden / published visible / archived listing behavior;
- comment moderation visibility;
- media validation;
- menu public render;
- SEO fallback;
- search/filter;
- activity log for required actions;
- critical transaction rollbacks.

Use Pest or PHPUnit—whichever the initialized project standardizes on. Do not add a second framework.

---

# 5. Request Flow (Canonical)

## Admin mutation (Livewire)

```text
Browser
  → Livewire component
  → authorize (Policy / Gate)
  → validate (component or Livewire Form)
  → Action / Service
      → DB transaction if needed
      → Models / Eloquent
      → Event (optional)
  → Listener / Job / Notification (side effects)
  → UI feedback (flash / re-render)
```

## Public read

```text
Browser
  → Route
  → Public Controller (or simple Livewire)
  → Eloquent / Query object (published scope only)
  → Blade view + SEO resolver
```

## Scheduled publishing

```text
Cron → schedule:run
  → Console Command (thin)
  → PublishScheduledPosts Action
  → same rules as manual publish
```

---

# 6. Routes Layout

```text
routes/web.php      Public content, homepage, taxonomy archives
routes/auth.php     Login, logout, password reset
routes/admin.php    Dashboard + admin Livewire full-page components
routes/console.php  Scheduler command registration
```

Admin routes:

- `auth` middleware;
- permission / role middleware as designed;
- stable names: `admin.posts.index`, `admin.settings.general`, …

Do not put business logic in route closures beyond trivial redirects.

---

# 7. Database & Artisan Layout

```text
database/migrations/     Schema only; no business behavior
database/seeders/        Roles, permissions, demo/admin bootstrap
database/factories/      Test data builders aligned to Enums/statuses
```

Factories and seeders must respect valid status values and FK rules from DATABASE.

---

# 8. Naming Conventions (quick reference)

| Kind | Pattern | Example |
|---|---|---|
| Model | singular StudlyCase | `Post` |
| Action | verb + noun | `PublishPost` |
| Policy | Model + Policy | `PostPolicy` |
| Enum | noun + Status/Type | `PostStatus` |
| Query | Model + purpose | `PostIndexQuery` |
| Livewire | StudlyCase by screen | `Admin\Posts\Edit` |
| Permission | `resource.action` | `posts.publish` |
| Route | `area.resource.action` | `admin.posts.edit` |
| Job | verb phrase | `SendPasswordResetEmail` |
| Event | past tense domain fact | `PostPublished` |

---

# 9. Decision Guide (when adding a class)

Ask in order:

1. **Is this UI state?** → Livewire / Blade.
2. **Is this input shape?** → Form Request or Livewire Form.
3. **Is this “may this user?”** → Policy / Gate.
4. **Is this a meaningful business operation?** → Action.
5. **Is this a complex reusable read?** → Query object.
6. **Is this async / non-blocking?** → Job (+ Notification if messaging).
7. **Does another concern need to react?** → Event + Listener.
8. **Is this persisted state?** → Model (+ Enum cast if closed set).
9. **Would a repository only wrap Eloquent?** → **Do not create it.**
10. **Would a DTO only wrap one validated array used once?** → **Do not create it.**

---

# 10. Growth Path (without premature abstraction)

| Stage | Structure change |
|---|---|
| MVP | Flat Models; Actions by module; Livewire Admin; optional Queries |
| Larger admin lists | Add Query objects aggressively for reads |
| First external API | Reuse Actions; thin API Controllers; still no microservices |
| Real multi-storage need | Consider targeted Repositories / filesystem disks |
| Multi-product reuse | Consider extracting a package **after** boundaries are proven |

---

# 11. Summary Map

| Concern | Location | Default stance |
|---|---|---|
| Models | `app/Models` | Always |
| Controllers | `app/Http/Controllers` | Thin; public + auth first |
| Services | `app/Services` | Rare orchestration |
| Actions | `app/Actions/{Module}` | Selective use cases |
| DTOs | `app/DTOs` | Only when reuse demands |
| Enums | `app/Enums` | Status closed sets |
| Policies | `app/Policies` | Always for protected resources |
| Form Requests | `app/Http/Requests` | Controller flows |
| Jobs | `app/Jobs` | Async side effects |
| Events | `app/Events` | When listeners exist |
| Listeners | `app/Listeners` | Side-effect reactions |
| Notifications | `app/Notifications` | Mail / multi-channel |
| Repositories | — | **Not by default** |
| Traits | `*/Concerns` | Sparingly |
| Helpers | `app/Support` | Small focused classes |
| Livewire | `app/Livewire` | Primary admin UI |
| Views | `resources/views` | Presentation only |
| Queries | `app/Queries` | Complex reads |
| Tests | `tests/Feature|Unit|...` | Required for critical flows |

---

# 12. Source Alignment

This structure implements:

- **PRD** — editorial CMS feature set and commercial reusability without SaaS premature design;
- **SRS** — modular monolith, selective Actions, no default repositories, Livewire + Blade, testable workflows;
- **SYSTEM_DESIGN** — ADR-001..006 folder guidance and module map;
- **DATABASE** — model inventory and status Enums;
- **BUSINESS_FLOW** — Action candidates for status transitions and moderation;
- **CURSOR.md** — Laravel-native first, thin UI, selective Actions, no speculative abstraction.

When implementation conflicts with this document, follow the authority order in `CURSOR.md` and update this file if an intentional architecture decision changes.

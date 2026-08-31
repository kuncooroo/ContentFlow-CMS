# CURSOR.md
## Permanent AI Development Instructions — ContentFlow CMS

> This file defines permanent engineering rules for AI coding assistants working on ContentFlow CMS.
> Read this file **before changing any project file**.

---

# 1. Project Identity

**Project:** ContentFlow CMS

ContentFlow CMS is a commercial Laravel-based Content Management System focused on:

- blog/content publishing;
- posts and pages;
- categories and tags;
- media management;
- comments and moderation;
- SEO;
- users, roles, and permissions;
- menus;
- site settings;
- operational dashboard;
- scheduled publishing;
- audit/activity logging.

The product is designed to be:

- simple;
- editorial-first;
- maintainable;
- commercially reusable;
- source-code friendly;
- SaaS-ready in the future without premature SaaS architecture.

---

# 2. Authoritative Project Documents

Before implementing a feature, read the relevant project documents.

Authority order:

1. `docs/PRD.md`
   - authoritative for product behavior;
   - MVP scope;
   - user roles;
   - business rules;
   - acceptance criteria.

2. `docs/SRS.md`
   - authoritative for software/technical requirements;
   - runtime behavior;
   - system constraints.

3. `docs/SYSTEM_DESIGN.md`
   - authoritative for architecture;
   - module boundaries;
   - request flow;
   - deployment architecture.

4. `docs/BUSINESS_FLOW.md`
   - authoritative for workflow;
   - state transitions;
   - actor/precondition/error flow;
   - audit events.

5. `docs/DATABASE.md`
   - authoritative for intended database design;
   - relationships;
   - constraints;
   - indexes;
   - status storage.

6. `docs/UI_UX.md`
   - authoritative for interface patterns;
   - navigation;
   - CRUD standards;
   - interaction states;
   - responsive behavior.

7. Existing source code
   - authoritative for current implementation patterns **only when it does not conflict with the documents above**.

## Conflict Rule

If existing code conflicts with documented business behavior:

- do not silently preserve the incorrect behavior;
- do not silently rewrite the business rule;
- identify the conflict;
- follow the highest-authority applicable document;
- implement the smallest compliant change;
- update technical documentation if an architecture decision legitimately changes.

Never change a business rule merely because another implementation would be easier.

---

# 3. Technical Stack

Target stack:

| Component | Target |
|---|---|
| Backend | Laravel 13.x |
| PHP | PHP 8.4.x target |
| Database | MySQL 8.4.x LTS |
| Server Rendering | Blade |
| Reactive UI | Livewire |
| Styling | Tailwind CSS |
| Client UI Behavior | Alpine.js |
| Deployment | Linux VPS |
| Architecture | Laravel Modular Monolith |
| Queue | Laravel database queue for MVP |
| Scheduler | Laravel Scheduler |
| Storage | Laravel Filesystem |
| Sessions | Database-backed recommended |
| Cache | Laravel cache; database/file initially |

## Runtime Version Rule

The actual installed PHP/framework/frontend versions must be verified from the project environment.

Before assuming versions, inspect:

```text
composer.json
composer.lock
package.json
frontend lock file
php -v
php artisan --version
database server version
```

Until verified:

```text
Actual PHP Version: TBD — Requires Environment Verification
```

Do not invent an installed version.

Do not upgrade dependencies merely because a newer version exists.

---

# 4. Core Architecture Rules

ContentFlow CMS is a **modular monolith**.

Mandatory:

- one Laravel application;
- one primary MySQL database;
- clear logical module boundaries;
- Laravel-native features first;
- no microservices for MVP;
- no unnecessary infrastructure.

Do not introduce:

- microservices;
- service mesh;
- Kafka/RabbitMQ/event streaming;
- Kubernetes;
- Redis as a mandatory dependency;
- Elasticsearch/Meilisearch unless measured requirements justify it;
- generic repository layers around every model;
- multi-tenancy before approved SaaS requirements;
- speculative plugin frameworks.

## Architectural Principle

```text
UI coordinates.
Validation validates.
Policies authorize.
Actions/Services execute business use cases.
Models represent persisted domain state.
Eloquent persists data.
Events decouple actual side effects.
Jobs handle asynchronous work.
```

---

# 5. AI Assistant — Mandatory Pre-Change Analysis

Before editing code, the AI must inspect the existing implementation.

At minimum:

1. Read `CURSOR.md`.
2. Read the relevant documents under `docs/`.
3. Inspect `composer.json` and `composer.lock` if available.
4. Inspect `package.json` and frontend lock file if available.
5. Locate existing files implementing the same or adjacent feature.
6. Inspect:
   - routes;
   - models;
   - policies;
   - actions/services;
   - Livewire components;
   - validation;
   - migrations;
   - tests.
7. Search for all call sites before changing a public class/method/property.
8. Check existing naming and folder conventions.
9. Check current tests covering the behavior.
10. Check whether a suitable abstraction already exists.

Do not create a new class because you failed to search for an existing one.

Do not assume a file does not exist without searching the repository.

---

# 6. AI Assistant — Change Discipline

For every requested change:

- make the smallest coherent change that satisfies the request;
- preserve existing architecture;
- preserve backward compatibility whenever possible;
- avoid unrelated formatting or refactoring;
- avoid renaming unrelated classes;
- avoid moving files unless required;
- do not alter public behavior not requested by the task;
- do not silently change defaults;
- do not silently broaden permissions;
- do not silently alter status transitions;
- do not silently change database semantics.

## Forbidden AI Behavior

Never:

- rewrite a large module to implement a small feature;
- replace a working Laravel-native feature with a package without justification;
- create duplicate helpers/services/components;
- modify unrelated code "for cleanliness";
- introduce speculative abstractions;
- add a new dependency without explaining the necessity;
- alter production migrations;
- remove tests to make a build pass;
- weaken validation to make a test pass;
- weaken authorization to make a feature easier;
- expose secrets during debugging;
- fabricate configuration values.

---

# 7. Laravel Conventions

Follow Laravel conventions unless the existing project intentionally documents a different rule.

Prefer:

- Eloquent models;
- route model binding;
- Form Requests for complex controller validation;
- Livewire validation/Form objects where appropriate for Livewire interactions;
- Policies/Gates for authorization;
- Laravel events/listeners;
- Laravel jobs/queues;
- Laravel notifications/mail;
- Laravel filesystem;
- Laravel cache;
- Laravel scheduler;
- Laravel logging;
- Laravel transactions.

Do not recreate framework functionality without a concrete need.

---

# 8. PHP Standards

Use modern PHP compatible with the verified project runtime.

Mandatory:

- PSR-style code formatting;
- strict, readable type usage;
- explicit return types where practical;
- explicit parameter types where practical;
- constructor property promotion where it improves clarity;
- enums/value objects only when they solve a real domain need;
- avoid clever metaprogramming;
- avoid hidden global state.

## Null Handling

Do not use nullable types casually.

Use null only when null has an explicit business meaning.

## Boolean Naming

Boolean variables/methods should read clearly:

```php
$isPublished
$canPublish
$hasPermission
shouldNotify()
```

Avoid:

```php
$flag
$statusBool
$check
```

## Comments

Do not comment obvious code.

Add comments for:

- non-obvious domain reasoning;
- important compatibility constraints;
- unusual technical decisions.

Prefer self-documenting names over comments.

---

# 9. Naming Conventions

## Classes

Use `StudlyCase`.

Examples:

```text
Post
PublishPost
PostPolicy
PostIndexQuery
SendPasswordResetNotification
PublishScheduledPosts
```

## Methods / Variables

Use `camelCase`.

## Database Tables

Use plural `snake_case`.

Examples:

```text
users
posts
post_tag
activity_logs
```

## Database Columns

Use `snake_case`.

Examples:

```text
author_id
publish_at
featured_media_id
created_at
```

## Routes

Use stable, descriptive route names.

Examples:

```text
admin.posts.index
admin.posts.create
admin.posts.edit
admin.settings.general
```

## Permissions

Use stable action-based names consistent with project documentation.

Examples:

```text
posts.view
posts.create
posts.update
posts.delete
posts.publish
posts.schedule
settings.manage
```

Do not invent multiple naming patterns for the same concept.

---

# 10. Domain Module Rules

Primary logical modules:

```text
Authentication
Users
Access Control
Content
Taxonomy
Media
Comments
Navigation
SEO
Settings
Dashboard
Audit
```

Keep module behavior logically separated.

Example:

- Content must not directly implement credential management.
- Media must not own post publishing behavior.
- Dashboard must query business data, not become the source of business logic.
- Audit logging must not replace application logging.

Do not package each module as a separate Composer package in MVP.

---

# 11. Models

Models should represent persisted domain entities and relationships.

Models may contain:

- relationships;
- scopes;
- casts;
- small state helpers;
- simple invariant-related methods.

Models should not become giant service containers.

Avoid:

- network calls from models;
- mail sending from model mutators;
- complicated multi-step workflows directly in models;
- hidden side effects on normal getters/setters.

## Relationship Rules

Use explicit Eloquent relationships.

Prevent N+1 queries.

Use eager loading when rendering lists or nested relations.

Example:

```text
Posts list needs author + category
→ eager load required relations
```

Do not eager-load large relation graphs by default everywhere.

Load only what the use case requires.

## Mass Assignment

Never mass-assign raw request data.

Use:

- validated data;
- explicitly allowed attributes;
- existing project mass-assignment convention.

Do not set unrestricted mass assignment merely for convenience.

---

# 12. Controllers

Controllers must remain thin.

A controller may:

1. receive a request;
2. authorize;
3. use validated input;
4. call an Action/Service;
5. return a response.

Controllers must not directly contain complex:

- publishing workflows;
- permission rules;
- transaction orchestration;
- notification orchestration;
- multi-record updates;
- domain state machines.

Bad:

```text
Controller
→ validate
→ update five models
→ send email
→ update cache
→ log audit
→ decide workflow state
```

Preferred:

```text
Controller
→ Form Request
→ Policy
→ Application Action
→ Response
```

---

# 13. Actions and Services

Use Actions/Services **selectively**.

Do not create a service for trivial CRUD merely to add a layer.

Good Action candidates:

```text
PublishPost
SchedulePost
ArchivePost
ModerateComment
ChangeUserStatus
UpdateSiteSettings
UpdateMenuStructure
AssignRole
```

## Action Rules

An Action should:

- represent one meaningful use case;
- have a clear name;
- receive validated/authorized input;
- define transaction boundaries when needed;
- return a clear result;
- avoid UI concerns.

## Service Rules

Use a Service when:

- several related operations share cohesive domain behavior;
- an integration requires a reusable boundary;
- orchestration is broader than one single Action.

Do not create generic classes like:

```text
PostService::create()
PostService::update()
PostService::delete()
```

if they simply wrap Eloquent without adding meaningful behavior.

---

# 14. Repository Strategy

Repositories are **not required by default**.

Do not create one repository per Eloquent model.

Use Eloquent directly through:

- Actions;
- Services;
- Query objects;
- Livewire components for simple reads.

Introduce a repository only when there is a concrete reason, such as:

- interchangeable persistence implementations;
- external data source boundary;
- highly complex persistence contract;
- a proven testability/architecture need.

A repository must solve a real problem.

---

# 15. Form Requests and Validation

Never trust client-side validation.

Server-side validation is mandatory.

## Controllers

Use Form Requests for:

- complex validation;
- reusable validation;
- validation involving multiple fields;
- authorization attached to a request where appropriate.

## Livewire

For Livewire:

- simple validation may live in the component;
- complex reusable form behavior should use the project's Livewire Form pattern or shared validation rules;
- do not duplicate the same complex domain rules in multiple Livewire components.

Form Requests are for normal HTTP/controller request flows; do not force a Form Request into Livewire if it conflicts with Livewire's intended architecture.

## Validation Rules

Validation must cover:

- required fields;
- data type;
- length;
- format;
- uniqueness;
- valid relation IDs;
- allowed status values;
- upload type/size;
- cross-field rules;
- scheduled time rules.

Validation does not replace authorization.

---

# 16. Policies and Gates

Use Policies/Gates for authorization.

Mandatory:

- all protected resource actions are authorized server-side;
- ownership rules are server-side;
- role/permission checks are not implemented only in Blade/Livewire UI;
- direct URLs/actions must remain protected.

The UI may hide unauthorized actions, but backend authorization is still mandatory.

## Example

Author:

```text
may edit own post
may not publish by default
may not edit another author's post
```

Editor:

```text
may manage editorial content according to assigned permissions
```

Never widen access simply because the UI does not show a button.

---

# 17. Routes

Use Laravel route conventions.

Prefer:

- route groups;
- route names;
- middleware groups;
- route model binding.

Admin routes should be clearly separated.

Conceptually:

```text
/admin/*
```

Protected routes must use authentication middleware.

Authorization remains Policy/Gate responsibility.

## Route Rules

- use RESTful naming when it fits;
- do not create duplicate URLs for the same action;
- avoid business logic in route closures;
- route closures are acceptable only for genuinely trivial behavior;
- do not expose internal IDs unnecessarily when a documented slug route exists.

---

# 18. Blade Templates

Blade is responsible for rendering, not business decisions.

Blade may:

- render authorized actions;
- display validated state;
- compose reusable components;
- escape output.

Blade must not:

- execute multi-step business logic;
- query large datasets;
- define permission rules independently;
- perform writes.

## Escaping

Escape user-generated output by default.

Use raw output only when:

- the content is intentionally trusted/sanitized rich content;
- the rendering path is documented;
- XSS handling is explicit.

Never use raw rendering simply to "make HTML work."

---

# 19. Livewire Components

Livewire components coordinate interactive UI.

Use Livewire for:

- search;
- filters;
- pagination;
- CRUD interaction;
- moderation;
- media selection;
- status actions;
- settings forms;
- modal coordination.

## Livewire Rules

- keep business logic out of large component methods;
- call Actions/Services for meaningful workflows;
- authorize server-side;
- validate server-side;
- display loading state;
- prevent double submission;
- preserve useful UI state;
- paginate large result sets.

Do not turn one component into an entire application module.

Split components by cohesive UI responsibility.

---

# 20. Alpine.js

Use Alpine.js for light client-side UI behavior.

Good uses:

- dropdowns;
- drawer state;
- modal state where no server state is required;
- collapsible sections;
- copy-to-clipboard;
- tabs.

Do not duplicate authoritative business state in Alpine.js.

Laravel/Livewire remains authoritative.

---

# 21. Tailwind CSS

Use existing project design tokens/components.

Mandatory:

- reuse shared UI components;
- keep spacing/type/radius patterns consistent;
- do not introduce arbitrary visual systems per module;
- avoid giant repeated utility strings when a reusable component is appropriate;
- follow `docs/UI_UX.md`.

Do not redesign unrelated pages while implementing one feature.

---

# 22. Jobs

Use Jobs for asynchronous/retryable work.

MVP examples:

- non-blocking email;
- future exports;
- future webhook delivery;
- future media processing.

Do not queue business operations where the user needs immediate committed confirmation, such as:

- Save Post;
- Publish Post status change;
- permission update;
- settings update.

## Job Rules

- use stable scalar/model identifiers in payloads;
- avoid serializing unnecessary large object graphs;
- design idempotently where practical;
- define retry behavior consciously;
- log terminal failures;
- dispatch after transaction commit when job depends on committed state.

Do not add a queue job merely to move simple code elsewhere.

---

# 23. Events

Use Events only when there is a real decoupling need.

Good candidates:

```text
PostPublished
PostArchived
CommentModerated
UserStatusChanged
SettingsUpdated
```

Do not create events with no listeners/use cases "for future flexibility."

Events are not substitutes for Actions.

---

# 24. Listeners

Listeners should perform a specific side effect or reaction.

Examples:

- create activity log;
- invalidate a relevant cache;
- dispatch non-critical notification.

Listener rules:

- keep listeners focused;
- do not create hidden critical business workflows across many listeners;
- critical state transitions remain explicit in Actions;
- queued listeners must tolerate retry.

---

# 25. Notifications

Prefer Laravel Notifications when one application event may have one or more channels.

MVP:

- password reset;
- simple email notification when explicitly required.

The persistent notification-center feature is not part of the MVP unless the PRD changes.

Never introduce a notification table just because Laravel supports database notifications.

---

# 26. Email

Use Laravel Mail / Notifications.

Mandatory:

- configuration comes from environment;
- never commit SMTP credentials;
- do not hard-code provider credentials;
- queue non-blocking emails when appropriate;
- log delivery failures without leaking secrets.

Email failure must not incorrectly roll back an already committed business operation unless email delivery is explicitly defined as part of that transaction's success criteria.

---

# 27. Database Migrations

Migrations define schema evolution.

Mandatory rules:

- use descriptive migration names;
- create new migrations for schema changes;
- **never modify an existing production migration**;
- do not delete a production migration;
- review destructive operations carefully;
- define foreign key behavior deliberately;
- add indexes for demonstrated query patterns;
- keep migrations deterministic.

## Production Migration Rule

Once a migration has been deployed to a production environment:

```text
DO NOT EDIT IT.
```

Create a new migration.

For a brand-new unreleased project, editing an unexecuted/unshared migration may only be done if the repository's established workflow explicitly allows it. The safe default is still a new migration after a schema has been shared.

Do not rewrite migration history to hide changes.

---

# 28. Database Transactions

Use transactions for multi-step critical operations.

Mandatory examples:

- create user + assign roles;
- role/permission changes;
- publish post + required audit state;
- schedule post;
- comment moderation with moderator metadata;
- site settings + audit;
- multi-item menu save/reorder.

## Transaction Rules

- keep transactions short;
- do not wait for SMTP inside a transaction;
- do not perform slow external HTTP calls inside a transaction;
- dispatch dependent Jobs after commit;
- failure must roll back all critical related writes;
- never show success before commit succeeds.

---

# 29. Database Query Rules

Prevent N+1 queries.

Mandatory:

- inspect relationships used by each list/view;
- eager load when necessary;
- use `with()`, `withCount()`, aggregate queries, or dedicated queries appropriately;
- paginate large data sets;
- select only necessary columns for heavy queries when meaningful;
- do not retrieve all records and filter them in PHP when SQL can filter them safely.

Do not prematurely optimize every query.

Measure when complexity increases.

---

# 30. Search and Filtering

MVP search uses MySQL/Eloquent.

Do not introduce an external search engine unless:

- actual requirements exceed MySQL;
- performance/relevance is measured;
- architecture change is justified and documented.

Search/filter rules:

- validate input;
- apply authorization scope;
- use pagination;
- preserve filter state;
- index common filters;
- avoid wildcard-heavy query designs without review.

---

# 31. API Rules

Public REST/GraphQL API is out of MVP scope.

Do not create a public API merely for architectural fashion.

If future API requirements are approved:

```text
API Controller
→ Validation
→ Authorization
→ Existing Action/Service
→ Domain
```

Never duplicate business logic for API endpoints.

GraphQL is not part of MVP.

---

# 32. Webhooks

Webhooks are not part of MVP.

Do not add webhook tables, signing logic, or delivery workers until the product requirement exists.

Future outbound webhooks must:

- be signed;
- be queued;
- retry safely;
- have delivery logs;
- avoid duplicating business logic.

---

# 33. File Uploads

All uploads are untrusted.

Mandatory:

- validate upload authorization;
- validate MIME/type;
- validate size;
- use allowlists;
- use Laravel Filesystem;
- do not trust original filename for storage path;
- prevent silent overwrites;
- do not allow executable uploads as normal media;
- handle partial/failed upload cleanup;
- preserve media metadata integrity.

Do not store binary media payloads in MySQL.

## Media Deletion

Before delete:

- check direct media references;
- check generic media references;
- apply documented business rule;
- never silently break published content.

---

# 34. Security Rules

Security is mandatory, not optional polish.

Always:

- use CSRF protection;
- authorize protected actions;
- validate input;
- escape output;
- use framework password hashing;
- rate-limit sensitive endpoints;
- use HTTPS in production;
- use secure session/cookie settings;
- keep `APP_DEBUG=false` in production;
- point web root to `public/`;
- keep `.env` inaccessible from web;
- validate rich-text rendering strategy;
- use least-privilege DB credentials.

Never:

- expose secrets;
- log passwords;
- log session cookie values;
- print environment credentials;
- commit `.env`;
- trust hidden fields as authorization;
- trust client-provided role/permission IDs without authorization;
- interpolate raw user input into SQL;
- disable security middleware simply to fix an issue.

---

# 35. Sensitive Configuration

Sensitive configuration belongs in environment/secrets management.

Examples:

```text
APP_KEY
DB_PASSWORD
MAIL_PASSWORD
API secrets
storage credentials
```

Never:

- commit production secrets;
- hard-code secrets in PHP/Blade/JS;
- place secrets in `site_settings`;
- expose secrets in logs/errors;
- paste real secrets into documentation.

`.env.example` must contain keys only, with safe example values.

---

# 36. Status and Business Rule Integrity

Use the documented states exactly.

## Post

```text
draft
scheduled
published
archived
```

Do not invent statuses such as:

```text
pending
review
deleted
trash
```

unless the PRD is updated.

## Comment

```text
pending
approved
spam
rejected
```

Only approved is public.

## User

```text
active
inactive
```

Inactive users cannot authenticate.

## Rule

Never silently add a new status to solve an implementation shortcut.

State transitions must follow `docs/BUSINESS_FLOW.md`.

---

# 37. Logging

Use Laravel logging for technical/operational logs.

Log:

- unexpected exceptions;
- queue failures;
- scheduler failures;
- storage failures;
- mail failures;
- relevant operational security events.

Do not log secrets.

Use appropriate log levels.

Avoid excessive debug logs in production paths.

Technical logs and activity/audit logs are separate concerns.

---

# 38. Activity / Audit Logging

Activity logs answer:

```text
Who performed what action, on which subject, and when?
```

Important events include those documented in project files, such as:

- user creation;
- user deactivation/reactivation;
- role/permission changes;
- post publishing;
- post archiving;
- settings updates.

Audit properties must never contain sensitive credentials.

Do not treat the audit table as a general debugging log.

---

# 39. Error Handling

Expected user/domain errors should be handled cleanly.

Categories:

- validation error;
- authentication error;
- authorization error;
- not found;
- invalid state transition;
- storage failure;
- queue/mail failure;
- unexpected exception.

Mandatory:

- do not expose stack traces in production;
- return understandable user messages;
- preserve user input where practical;
- log unexpected errors;
- use correct HTTP status behavior;
- never report success when persistence failed.

Do not catch broad exceptions only to suppress them.

Catch an exception when you can:

- handle it;
- translate it;
- retry it;
- add meaningful context.

---

# 40. Testing

Write tests for important business behavior.

Mandatory coverage includes:

- authentication;
- inactive-user login rejection;
- password reset;
- authorization;
- Author ownership restrictions;
- publishing;
- scheduled publishing;
- draft non-public visibility;
- archived visibility;
- comment moderation;
- media upload validation;
- menu persistence;
- SEO fallback;
- search/filter behavior;
- transaction rollback for critical operations;
- required audit events.

## Test Framework

Use the test framework already present in the project.

Do not add a second testing framework unnecessarily.

## Test Type Selection

Use:

- unit tests for isolated domain logic;
- feature tests for application flows;
- Livewire tests for interactive components;
- browser/end-to-end tests for critical flows where they provide meaningful coverage.

## Test Quality

Tests must verify behavior, not implementation trivia.

Do not delete or weaken a test just because new code fails it unless the business rule has explicitly changed.

---

# 41. Testing Before Completing a Change

Before considering a task complete:

1. run relevant targeted tests;
2. run related module tests;
3. run broader suite when the change touches shared behavior;
4. run formatter/linter used by the project;
5. inspect for N+1/query regressions where relevant;
6. verify authorization paths;
7. verify validation failure paths;
8. verify no accidental documentation/business-rule drift.

If a tool/test cannot run, state the limitation rather than pretending it passed.

---

# 42. Performance

Follow documented product targets.

General rules:

- paginate tables;
- avoid N+1 queries;
- eager load intentionally;
- cache stable settings/menus where useful;
- keep public rendering free of unnecessary synchronous work;
- queue non-critical slow work;
- avoid loading entire tables into memory;
- inspect actual query patterns before adding infrastructure.

Do not add Redis, Octane, CDN logic, or dedicated search solely as speculative optimization.

---

# 43. Cache

Cache is an optimization, never source of truth.

Suitable candidates:

- site settings;
- menus;
- stable SEO defaults;
- stable taxonomy lookup.

Mandatory:

- invalidate cache on relevant write;
- application remains correct after cache clear;
- permission changes must not be incorrectly stale.

Do not cache everything.

---

# 44. Scheduled Tasks

Use Laravel Scheduler.

Scheduled publishing must reuse the same business Action as manual publishing where appropriate.

Do not duplicate publishing rules in a scheduler command.

Scheduled job rules:

- re-check eligibility;
- avoid overlap when harmful;
- be safe on repeated execution;
- log failures;
- never mark an item successful if state update failed.

---

# 45. Notifications and UI Feedback

For user-initiated actions:

- success message only after persistence succeeds;
- validation errors inline;
- system errors clearly differentiated;
- no misleading "Saved" before commit.

Follow `docs/UI_UX.md` for:

- toast behavior;
- empty states;
- loading states;
- confirmations;
- error states.

---

# 46. CRUD Standards

Follow consistent CRUD conventions.

List page:

```text
Header
Search / Filters
Create Action
Paginated Table/List
```

Create/Edit:

```text
Page Header
Form
Validation
Primary Save Action
Secondary/Destructive Actions
```

Do not design each CRUD screen as an unrelated bespoke interface.

---

# 47. Documentation

Update documentation when behavior or architecture legitimately changes.

Examples:

- product behavior → PRD/Business Flow;
- technical requirement → SRS;
- architecture → System Design;
- schema → Database;
- interaction architecture → UI/UX;
- permanent engineering rule → CURSOR.md.

Do not update docs to retroactively justify an accidental implementation.

The implementation should comply with the approved design, or the design change should be explicit.

---

# 48. Dependency / Package Rules

Never introduce a package without justification.

Before adding a package:

1. verify Laravel-native functionality cannot adequately solve the requirement;
2. inspect maintenance status;
3. inspect Laravel/PHP compatibility;
4. inspect security/reputation;
5. assess long-term upgrade impact;
6. document why dependency complexity is justified.

Do not add a package merely to save a few lines of straightforward Laravel code.

Do not replace an existing dependency without a concrete problem.

Keep dependency count low.

---

# 49. Git Discipline

Keep changes reviewable.

Mandatory:

- small focused commits;
- one logical concern per commit where practical;
- meaningful commit messages;
- no credentials;
- no `.env`;
- no `vendor/`;
- no `node_modules/`;
- do not regenerate lock files without dependency change;
- do not include unrelated formatter churn;
- do not force-push shared branches without explicit workflow authorization;
- do not rewrite published migration history.

Suggested commit style:

```text
feat(posts): add scheduled publishing validation
fix(media): prevent deletion of referenced assets
test(auth): cover inactive user login
docs(system): update publishing architecture
```

Follow the repository's existing commit convention if one exists.

---

# 50. Backward Compatibility

Maintain backward compatibility whenever possible.

Before changing:

- a public route;
- a method signature;
- a database column;
- a permission name;
- a status value;
- a configuration key;
- a Blade/Livewire component interface;

search for all usages.

If a breaking change is unavoidable:

- justify it;
- update all call sites;
- provide a migration/compatibility path when applicable;
- update documentation;
- add regression tests.

Never break a stable interface casually.

---

# 51. Refactoring Rules

Refactor only when:

- required for the current task;
- needed to remove a concrete defect;
- needed to make the requested behavior testable/maintainable.

Do not perform a broad cleanup while implementing a small feature.

Bad:

```text
Task: add comment filter
Change: redesign all controllers and rename 30 files
```

Good:

```text
Task: add comment filter
Change: extend existing Comment query/filter pattern + tests
```

Large refactors should be separate deliberate work.

---

# 52. Production Safety

Before modifying behavior that affects production data:

- inspect current schema;
- inspect migration history;
- inspect data relationships;
- identify destructive risk;
- add migration rather than editing deployed migration;
- ensure rollback/recovery strategy when relevant.

Never:

- truncate production data;
- drop columns/tables casually;
- change status semantics without data migration;
- change unique constraints without considering existing records.

---

# 53. Environment Configuration Rules

Environment-specific values must not be embedded in source code.

Use configuration files backed by environment variables.

Read configuration via Laravel configuration APIs.

Do not call `env()` throughout application business code.

Use `env()` only in configuration files according to Laravel conventions.

Examples:

```text
config/app.php
config/database.php
config/mail.php
config/filesystems.php
```

---

# 54. Database Schema Rules Specific to ContentFlow

Follow `docs/DATABASE.md`.

Key rules:

- posts and pages remain separate tables;
- no default soft deletes;
- use explicit status fields;
- settings use typed `site_settings`;
- money tables do not exist in MVP;
- tenant tables do not exist in MVP;
- binary media remains outside MySQL;
- direct media FKs are used for first-class media roles;
- generic media references are limited to justified embedded usage;
- activity logs are separate from technical logs.

Do not normalize or denormalize the schema differently without a documented architecture change.

---

# 55. UI/UX Rules Specific to ContentFlow

Follow `docs/UI_UX.md`.

Mandatory:

- clean professional SaaS visual hierarchy;
- no excessive charts;
- no generic admin-template clutter;
- clear primary action;
- permission-aware navigation;
- consistent status labels;
- responsive admin layout;
- accessible forms;
- keyboard-accessible controls;
- role-specific sidebar visibility;
- explicit destructive confirmations.

Do not add visual complexity merely to demonstrate functionality.

---

# 56. Business Rules That Must Not Be Silently Changed

Examples:

- Author cannot publish by default.
- Draft is not public.
- Scheduled content is not public before due time.
- Only Approved comments are public.
- Inactive users cannot authenticate.
- At least one active Super Admin must remain.
- Referenced media cannot be silently deleted.
- Archived is a content lifecycle state, not soft delete.
- SaaS billing is not MVP.
- Multi-tenancy is not MVP.
- Page builder is not MVP.
- E-commerce is not MVP.

If a feature request contradicts these, identify the conflict before changing the system design.

---

# 57. Recommended Implementation Pattern

For a meaningful state-changing workflow:

```text
Route / Livewire action
        ↓
Authentication Middleware
        ↓
Policy / Gate
        ↓
Validation
        ↓
Application Action
        ↓
Database Transaction if required
        ↓
Eloquent Models
        ↓
Commit
        ↓
Cache invalidation
        ↓
Audit / Event / Queue side effect
        ↓
User feedback
```

Do not skip layers that are necessary for correctness.

Do not add layers that provide no value.

---

# 58. Query Object Rule

A Query Object may be introduced for complex reusable read behavior.

Examples:

```text
PostIndexQuery
DashboardSummaryQuery
CommentModerationQuery
MediaLibraryQuery
```

Do not create query classes for trivial:

```text
Post::find($id)
```

Query objects should solve real readability/reuse complexity.

---

# 59. Rich Content / XSS Rule

CMS rich content is a high-risk boundary.

Requirements:

- sanitize or otherwise explicitly trust only content authored through approved editorial paths;
- escape normal user-generated strings;
- comments are treated as untrusted;
- never render comments with raw HTML by default;
- never use raw Blade syntax around arbitrary request data.

If a rich-text editor is introduced, its storage/rendering security model must be explicitly reviewed.

---

# 60. File and Folder Creation Rules for AI

Before creating a new file:

1. search for an existing equivalent;
2. inspect current folder organization;
3. follow current namespace;
4. avoid duplicate concepts;
5. choose the smallest appropriate abstraction.

Before moving/removing a file:

1. search all imports/usages;
2. inspect routes/config/service provider registrations;
3. inspect tests;
4. update all dependent references.

Never remove a file simply because it appears unused from one local view.

---

# 61. AI Assistant — Required Completion Review

After code changes, the AI must review the diff conceptually.

Ask:

- Did I change only what was requested?
- Did I preserve documented business rules?
- Did I use existing project architecture?
- Did I add unnecessary abstraction?
- Did I add a package?
- Did I accidentally broaden authorization?
- Did I validate server-side?
- Did I introduce an N+1 query?
- Did I paginate large data?
- Did I expose sensitive information?
- Did I change a production migration?
- Did I update required tests?
- Did architecture/schema behavior change enough to require docs updates?
- Is backward compatibility preserved?

Do not mark work complete until this review is satisfied.

---

# 62. AI Assistant — When Existing Code Is Incomplete

If an expected class/module is not implemented yet:

- do not invent a parallel architecture;
- follow `docs/SYSTEM_DESIGN.md`;
- create the smallest structure consistent with Laravel conventions;
- do not build future modules not needed by the task;
- do not add speculative extension hooks.

Example:

If `PublishPost` does not exist and the requested feature requires publishing rules:

```text
Create PublishPost action
Reuse it from current UI
Add tests
```

Do not create:

```text
PublishingRepository
PublishingManager
PublishingFactory
PublishingPipeline
PublishingProvider
```

without concrete need.

---

# 63. AI Assistant — Handling Ambiguity

When implementation detail is ambiguous:

1. search the codebase;
2. check authoritative docs;
3. follow existing established pattern;
4. choose the smallest Laravel-native solution.

Do not invent hidden requirements.

When two technical approaches are both valid:

Prefer the option with:

1. fewer dependencies;
2. fewer abstractions;
3. better Laravel convention alignment;
4. simpler tests;
5. easier upgrade path;
6. lower operational complexity.

---

# 64. Definition of Engineering Done

A feature is not complete until:

- behavior matches PRD;
- workflow matches Business Flow;
- architecture matches System Design;
- database usage matches Database Design;
- UI follows UI/UX standards;
- authorization is enforced;
- validation is server-side;
- important paths have tests;
- no known critical defect remains;
- relevant tests pass;
- documentation is updated when required;
- change contains no unrelated refactor;
- no credentials/sensitive config are exposed.

---

# 65. Final Engineering Directive

ContentFlow CMS must remain:

```text
Simple
Laravel-native
Explicit
Secure
Testable
Maintainable
Commercially reusable
```

The default answer to unnecessary architectural complexity is:

```text
Do not add it yet.
```

Implement only what current product requirements need.

Prefer:

```text
Laravel convention
→ clear business Action
→ Policy
→ validated input
→ Eloquent
→ transaction when necessary
→ tests
```

over speculative architecture.

**Never sacrifice correctness, authorization, security, or documented business behavior for implementation speed.**

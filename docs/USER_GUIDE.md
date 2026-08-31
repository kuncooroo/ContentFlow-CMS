# ContentFlow CMS — User Guide

**Product:** ContentFlow CMS  
**Version:** 1.0.0  
**Audience:** Site owners, editors, administrators, and technical operators  

This guide explains how to install, configure, and use ContentFlow CMS day to day. You do not need to be a developer to follow most sections. Technical operators will find deeper deployment detail in linked documents.

> **Screenshot placeholder**  
> Throughout this guide, look for boxes labeled **[Screenshot]**. Replace them with your own product screenshots when preparing customer-facing documentation packages.

---

## Table of contents

1. [Introduction](#1-introduction)
2. [Key features](#2-key-features)
3. [System requirements](#3-system-requirements)
4. [Installation](#4-installation)
5. [Initial configuration](#5-initial-configuration)
6. [Admin setup](#6-admin-setup)
7. [User management](#7-user-management)
8. [Roles and permissions](#8-roles-and-permissions)
9. [Module guides](#9-module-guides)
10. [Business workflows](#10-business-workflows)
11. [Notifications](#11-notifications)
12. [Reports](#12-reports)
13. [File management](#13-file-management)
14. [Email configuration](#14-email-configuration)
15. [Cron configuration](#15-cron-configuration)
16. [Queue configuration](#16-queue-configuration)
17. [Backup](#17-backup)
18. [Updates](#18-updates)
19. [Troubleshooting](#19-troubleshooting)
20. [FAQ](#20-faq)
21. [Security recommendations](#21-security-recommendations)
22. [Support information](#22-support-information)

---

## 1. Introduction

ContentFlow CMS is a **commercial Laravel-based content management system** for editorial websites. It helps teams publish blog posts and pages, organize content with categories and tags, manage media, moderate comments, control access with roles, and operate a public website from a clean admin panel.

### Who this product is for

| Audience | How they use ContentFlow |
|---|---|
| **Site owners** | Oversee branding, users, and site settings |
| **Editors** | Publish posts and pages, moderate comments |
| **Authors** | Draft content and upload media |
| **Administrators** | Manage users, roles, menus, and configuration |
| **Developers / ops** | Install, deploy, schedule jobs, and maintain backups |

### What ContentFlow is not (MVP)

- Not an e-commerce platform
- Not a multi-tenant SaaS product
- Not a visual page builder
- Not a marketing automation suite

For product scope, see [PRD.md](PRD.md).

### Related documentation

| Document | Use when |
|---|---|
| [INSTALLATION.md](INSTALLATION.md) | Detailed setup options |
| [ADMIN_GUIDE.md](ADMIN_GUIDE.md) | Concise staff reference for admin screens |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Production VPS, Nginx, HTTPS |
| [DEMO_MODE.md](DEMO_MODE.md) | Public demo sandbox rules |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Problem diagnosis |

---

## 2. Key features

| Feature | Description |
|---|---|
| **Posts** | Draft, schedule, publish, and archive blog posts |
| **Pages** | Static pages (About, Contact, etc.) |
| **Categories & tags** | Organize posts and power public archives |
| **Media library** | Upload and reuse images with alt text |
| **Comments** | Public submission with admin moderation |
| **Menus** | Primary and footer navigation |
| **SEO fields** | Titles, descriptions, robots, Open Graph image |
| **Users & roles** | Super Admin, Administrator, Editor, Author |
| **Site settings** | Branding, contact, timezone, locale, comments toggle |
| **Dashboard** | Operational counts and recent content |
| **Activity log** | Audit trail for sensitive admin actions |
| **Public website** | Home, blog, posts, pages, category/tag archives |
| **Installer** | Browser wizard for first-time source-code setup |
| **Demo mode** | Optional sales sandbox with protected accounts |

---

## 3. System requirements

### Server

| Component | Minimum | Recommended |
|---|---|---|
| PHP | 8.3+ | 8.4.x |
| MySQL | 8.0+ | 8.4.x LTS |
| Composer | 2.x | 2.x |
| Node.js | 20+ | 22.x |
| npm | 10+ | 10+ |
| Web server | Nginx or Apache | Nginx + PHP-FPM |
| OS | Linux VPS (production) | Ubuntu LTS |

### Required PHP extensions

`pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`

### Writable directories

- `storage/`
- `bootstrap/cache/`

### Client browsers

Modern evergreen browsers (Chrome, Firefox, Edge, Safari). Admin UI uses Livewire; JavaScript must be enabled.

Verified versions: [VERSIONS.md](VERSIONS.md).

---

## 4. Installation

You can install ContentFlow in two ways.

### Option A — Browser installer (recommended)

1. Copy the product source code to your server.
2. Install dependencies:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

3. Point the web server document root to the `public/` folder.
4. Open **`https://your-domain.example/install`** in a browser.

**[Screenshot: Installer — Requirements step]**  
_Show the checklist of PHP version, extensions, and writable folders._

5. Complete each wizard step:

| Step | What you enter |
|---|---|
| Requirements | Confirm all checks pass |
| Application | Site/app name and public URL |
| Database | Host, port, database name, username, password |
| Administrator | First Super Admin name, email, password |
| Settings | Site name, timezone, locale |
| Complete | Confirmation and link to login |

6. After success, the installer creates `storage/app/install.lock`. Visiting `/install` again returns **404** (by design).

**[Screenshot: Installer — Complete page with Login button]**

Full installer design: [INSTALLER.md](INSTALLER.md). Operator details: [INSTALLATION.md](INSTALLATION.md).

### Option B — Manual installation

For experienced operators:

```bash
cp .env.example .env
php artisan key:generate
# Edit .env with database and APP_URL
composer install
npm install && npm run build
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SiteSettingsSeeder
php artisan storage:link
```

Then create the first Super Admin via the installer (if unlocked) or your preferred admin bootstrap method.

### After install checklist

- [ ] Sign in at `/login`
- [ ] Dashboard loads at `/admin/dashboard`
- [ ] Public home page loads at `/`
- [ ] `php artisan storage:link` has been run (media URLs)
- [ ] Scheduler and queue are configured for production (sections 15–16)

---

## 5. Initial configuration

After first login as Super Admin:

### 5.1 Site settings

1. Go to **Admin → Settings**.
2. Set **Site name**, description, logo, and favicon.
3. Set **Timezone** and **Locale**.
4. Enable or disable **Comments**.
5. Configure contact details and default SEO fields.
6. Click **Save**.

**[Screenshot: Site settings form]**

### 5.2 Branding and public appearance

| Setting | Effect |
|---|---|
| Site name / logo | Header branding |
| Favicon | Browser tab icon |
| Default SEO title / description | Fallbacks when content has no SEO override |

### 5.3 Menus

1. Go to **Admin → Menus**.
2. Edit the **Primary** menu (header navigation).
3. Add items: custom URL, page, post, or category.
4. Save the structure.

**[Screenshot: Menu editor with ordered items]**

### 5.4 Storage link

If uploaded images do not appear on the public site:

```bash
php artisan storage:link
```

---

## 6. Admin setup

### Sign in

1. Open `/login`.
2. Enter email and password.
3. You are redirected to **Dashboard**.

**[Screenshot: Login page]**

### Sign out

Use the logout control in the admin layout.

### Dashboard

**Path:** `/admin/dashboard`

Shows operational summaries such as post/page counts, pending comments (when permitted), and recent content.

**[Screenshot: Admin dashboard]**

### Admin navigation

Typical sidebar modules (visibility depends on your role):

- Dashboard  
- Posts, Pages  
- Media  
- Categories, Tags  
- Comments  
- Menus  
- Settings  
- Users, Roles  
- Activity log  

Inactive accounts cannot sign in. Password reset is available via **Forgot password** unless demo mode is enabled ([DEMO_MODE.md](DEMO_MODE.md)).

---

## 7. User management

**Path:** Admin → Users  
**Who can access:** Super Admin, Administrator

### Create a user

1. Click **Create user**.
2. Enter name, email, and temporary password.
3. Choose status (**Active** or **Inactive**).
4. Assign one or more roles.
5. Save.

**[Screenshot: Create user form]**

### Edit a user

1. Open the user from the list.
2. Update name, email, or password (optional).
3. Adjust roles if permitted.
4. Save.

### Activate or deactivate

From the users list:

- **Deactivate** — blocks login immediately (session is invalidated).
- **Activate** — restores login ability.

**Rules:**

- You cannot deactivate your own account.
- You cannot deactivate the **last active Super Admin**.

### Search and filters

Use search and status filters on the users list to find accounts quickly.

---

## 8. Roles and permissions

### Default roles

| Role | Typical responsibilities |
|---|---|
| **Super Admin** | Full access; first installer account |
| **Administrator** | Users, roles, settings, audit, all content |
| **Editor** | Publish content, moderate comments, taxonomy, SEO — no users/settings/audit |
| **Author** | Own drafts, media upload, tags — limited publish rights |

### Permission overview

| Capability | Super Admin | Administrator | Editor | Author |
|---|:---:|:---:|:---:|:---:|
| Posts (full lifecycle) | ✓ | ✓ | ✓ | Own drafts |
| Schedule posts | ✓ | ✓ | ✓ | — |
| Pages | ✓ | ✓ | ✓ | — |
| Media | ✓ | ✓ | ✓ | Upload/view |
| Comment moderation | ✓ | ✓ | ✓ | — |
| Categories | ✓ | ✓ | ✓ | — |
| Tags | ✓ | ✓ | ✓ | ✓ |
| Menus | ✓ | ✓ | — | — |
| SEO fields | ✓ | ✓ | ✓ | — |
| Users & roles | ✓ | ✓ | — | — |
| Site settings | ✓ | ✓ | — | — |
| Activity log | ✓ | ✓ | — | — |

### Managing roles

**Path:** Admin → Roles

1. Open a role.
2. Review or sync permissions (system roles may be restricted in demo mode).
3. Save.

**[Screenshot: Role permission matrix]**

Exact permission keys: `RolePermissionSeeder` and developer docs. Day-to-day guidance: [ADMIN_GUIDE.md](ADMIN_GUIDE.md#roles-and-permissions).

---

## 9. Module guides

### 9.1 Posts

**Path:** Admin → Posts

**Statuses:** Draft → Scheduled → Published → Archived

#### Create and publish a post

1. Click **Create post**.
2. Enter title, content, excerpt.
3. Assign categories and tags.
4. Optional: featured image from the media library.
5. Save as **Draft**.
6. When ready, **Publish** or **Schedule** a future date/time.
7. Use **Preview** before go-live if needed.

**[Screenshot: Post editor]**  
**[Screenshot: Post list with status badges]**

Public URL: `/posts/{slug}`

### 9.2 Pages

**Path:** Admin → Pages

Statuses: **Draft**, **Published**, **Archived** (no scheduling in MVP).

1. Create a page with title and content.
2. Publish when ready.
3. Link it from **Menus** so visitors can find it.

Public URL: `/pages/{slug}`

**[Screenshot: Page editor]**

### 9.3 Categories and tags

| Module | Path | Public archive |
|---|---|---|
| Categories | Admin → Categories | `/categories/{slug}` |
| Tags | Admin → Tags | `/tags/{slug}` |

Create items with a clear name; the system manages unique slugs.

### 9.4 Comments

**Path:** Admin → Comments

1. Review **Pending** comments.
2. Choose **Approve**, **Reject**, or **Mark spam**.
3. Only **Approved** comments appear on the public post.

**[Screenshot: Comment moderation list]**

Disable new comments globally: **Settings → Comments enabled**.

### 9.5 Menus

See [Initial configuration — Menus](#53-menus).

### 9.6 SEO

On each post or page, set:

- SEO title  
- Meta description  
- Canonical URL (optional)  
- Robots index flag  
- Open Graph image  

Site-wide defaults live under **Settings**.

### 9.7 Activity log

**Path:** Admin → Activity log

Read-only history of sensitive actions (user changes, settings updates, role changes, etc.). Use filters by event, actor, and date.

**[Screenshot: Activity log table]**

### 9.8 Public website (visitor experience)

Visitors can:

| Action | Where |
|---|---|
| Browse recent posts | `/` and `/blog` |
| Read a post | `/posts/{slug}` |
| Read a page | `/pages/{slug}` |
| Browse by category/tag | `/categories/{slug}`, `/tags/{slug}` |
| Leave a comment | On published posts when comments are enabled |

There is **no public registration** in MVP. Comments start as Pending until moderated.

**[Screenshot: Public post page with comments form]**

---

## 10. Business workflows

### Post publishing workflow

```text
Author/Editor creates draft
        ↓
Content reviewed and edited
        ↓
Publish now ──or── Schedule for later
        ↓
Public site shows post (when published / due)
        ↓
Optional: Archive (hide from public)
```

Scheduled posts require the server **cron scheduler** (section 15).

### Comment moderation workflow

```text
Visitor submits comment
        ↓
Status = Pending
        ↓
Editor/Admin approves, rejects, or marks spam
        ↓
Only Approved comments are public
```

### User lifecycle workflow

```text
Admin creates user + roles
        ↓
User signs in (must be Active)
        ↓
Optional: Deactivate (blocks login)
        ↓
Optional: Reactivate
```

Detailed process specs: [BUSINESS_FLOW.md](BUSINESS_FLOW.md).

---

## 11. Notifications

### What is included (MVP)

| Notification | Channel | Notes |
|---|---|---|
| Password reset | Email | Queued when queue worker is running |

### What is not included (MVP)

- In-app notification center  
- Editorial approval emails  
- Comment notification emails  

Password reset emails require correct [Email configuration](#14-email-configuration) and a running [queue worker](#16-queue-configuration) when `QUEUE_CONNECTION=database`.

Flash messages (success/error) appear in admin after save actions.

---

## 12. Reports

ContentFlow MVP provides **operational summaries**, not business intelligence.

| Report surface | Location | Contents |
|---|---|---|
| Dashboard counts | Admin → Dashboard | Posts/pages by status, pending comments |
| Activity log | Admin → Activity log | Who did what, when |
| Content lists | Module index pages | Filterable tables with search |

**Not included:** traffic analytics, revenue reports, conversion funnels, CSV export suite.

---

## 13. File management

### Media library

**Path:** Admin → Media

#### Upload an image

1. Open Media.
2. Choose a file (JPEG, PNG, GIF, or WebP).
3. Stay within the configured size limit (default **5 MB**).
4. After upload, edit **alt text** as needed.

**[Screenshot: Media library grid]**

#### Use media in content

Select images as featured images or SEO/Open Graph images from post/page forms and settings.

#### Delete media

Deletion is blocked when the file is still referenced by posts, pages, or site settings. Remove references first.

### Storage tips

- Always run `php artisan storage:link` after install.
- Keep backups of `storage/app` with your database backups.
- Do not upload executable files — the library allows images only.

---

## 14. Email configuration

Email is configured in the server `.env` file (not in the admin UI).

### Typical production SMTP settings

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.example
MAIL_PORT=587
MAIL_USERNAME=your-user
MAIL_PASSWORD=your-secret
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.example
MAIL_FROM_NAME="${APP_NAME}"
```

### Local / demo recommendation

```env
MAIL_MAILER=log
```

Messages are written to the application log instead of being sent.

### After changing mail settings

```bash
php artisan config:clear
# or rebuild caches in production
php artisan config:cache
```

Test by requesting a password reset for an **active** account (not available in demo mode).

---

## 15. Cron configuration

ContentFlow uses Laravel’s scheduler for **scheduled post publishing**.

### What cron runs

On the server, run every minute:

```bash
* * * * * cd /var/www/contentflow && php artisan schedule:run >> /dev/null 2>&1
```

Adjust the path to your installation.

### Why it matters

| Feature | Without cron |
|---|---|
| Scheduled posts | Stay “Scheduled” and never go live automatically |

Production setup details: [DEPLOYMENT.md](DEPLOYMENT.md).

**[Screenshot: Example hosting panel cron job entry]** _(optional)_

---

## 16. Queue configuration

### Default

```env
QUEUE_CONNECTION=database
```

Password reset notifications are queued. Without a worker, emails may not send until a worker processes jobs.

### Start a worker (development)

```bash
php artisan queue:work
```

### Production

Use Supervisor (or equivalent) to keep `queue:work` running. Example and failed-job handling: [QUEUE_OPERATIONS.md](QUEUE_OPERATIONS.md) and [DEPLOYMENT.md](DEPLOYMENT.md).

### Sync mode (simple local only)

```env
QUEUE_CONNECTION=sync
```

Jobs run immediately in the web request. Fine for local demos; not recommended for production.

---

## 17. Backup

Back up regularly before updates or major content changes.

### What to back up

| Item | Location / method |
|---|---|
| Database | MySQL dump (`mysqldump` or host panel) |
| Uploaded media | `storage/app` (especially `public/` media) |
| Environment | `.env` (store securely — never in public git) |
| Custom code changes | Your deployment repository |

### Example database dump

```bash
mysqldump -u USER -p DATABASE_NAME > contentflow-backup-$(date +%F).sql
```

### Restore overview

1. Restore the database dump.
2. Restore `storage/app` files.
3. Restore `.env` if needed.
4. Run `php artisan storage:link` if the symlink is missing.
5. Clear caches: `php artisan config:clear && php artisan cache:clear`

Store backups encrypted and off-server when possible.

---

## 18. Updates

Follow [UPGRADE.md](UPGRADE.md) for version upgrades. High-level process:

1. **Back up** database and files.
2. Put the site in maintenance mode (optional): `php artisan down`.
3. Deploy the new source release.
4. Install dependencies:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```

5. Run migrations:

   ```bash
   php artisan migrate --force
   ```

6. Rebuild caches:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

7. Restart queue workers.
8. Bring the site up: `php artisan up`.
9. Smoke-test login, dashboard, publish, and public pages.

**Do not** remove `storage/app/install.lock` during upgrades.

Release notes: [RELEASE.md](RELEASE.md), [CHANGELOG.md](../CHANGELOG.md).

---

## 19. Troubleshooting

| Problem | What to try |
|---|---|
| Installer returns 404 | Installer is locked — intended after install. See [INSTALLATION.md](INSTALLATION.md). |
| Cannot sign in | Check Active status; verify password; clear browser cookies |
| 419 Page Expired | CSRF/session issue — match `APP_URL`, use HTTPS cookies in production |
| Images missing | Run `php artisan storage:link` |
| Scheduled posts not publishing | Verify cron is running `schedule:run` |
| Password reset email not arriving | Check SMTP, queue worker, and `MAIL_MAILER` |
| 500 error | Check `storage/logs/laravel.log`; ensure `APP_DEBUG=false` in production |
| Demo banner / blocked actions | `DEMO_MODE=true` — see [DEMO_MODE.md](DEMO_MODE.md) |

Expanded diagnostics: [TROUBLESHOOTING.md](TROUBLESHOOTING.md).

---

## 20. FAQ

**Q: Can visitors create accounts?**  
A: No. Public registration is not part of MVP. Staff accounts are created by Administrators.

**Q: Why don’t my comments appear?**  
A: New comments are Pending until an Editor or Administrator approves them.

**Q: Can Authors publish posts?**  
A: By default, Authors manage their own drafts. Publishing and scheduling require higher roles/permissions.

**Q: Is there a WYSIWYG HTML editor?**  
A: Not in MVP. Content is stored and displayed as plain escaped text for security.

**Q: Can I re-run the installer?**  
A: Only intentionally after backup, on a fresh/empty database, after removing `storage/app/install.lock`.

**Q: Where do I change the site logo?**  
A: Admin → Settings → logo media.

**Q: Does ContentFlow include analytics?**  
A: No built-in traffic analytics in MVP. Use an external analytics tool if needed.

**Q: What is demo mode?**  
A: A sales/evaluation sandbox with protected accounts and a reset command. Keep it off on customer production sites.

---

## 21. Security recommendations

| Recommendation | Why |
|---|---|
| Set `APP_DEBUG=false` in production | Hides stack traces and internals |
| Use HTTPS and `SESSION_SECURE_COOKIE=true` | Protects session cookies |
| Keep `DEMO_MODE=false` on customer sites | Avoids shared demo credentials |
| Confirm `storage/app/install.lock` exists after install | Closes the installer surface |
| Point document root to `public/` only | Blocks access to `.env` and source |
| Use strong Super Admin passwords | Protects full-access accounts |
| Deactivate unused staff accounts | Reduces attack surface |
| Keep Composer dependencies updated | Review with `composer audit` |
| Restrict SSH and database access | Ops-level least privilege |
| Back up regularly | Recovery from accidents or incidents |

Pre-release checklist: [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md).  
Audit findings: [SECURITY_AUDIT.md](SECURITY_AUDIT.md).

---

## 22. Support information

### Self-service resources

| Resource | Location |
|---|---|
| Installation | [INSTALLATION.md](INSTALLATION.md) |
| Deployment | [DEPLOYMENT.md](DEPLOYMENT.md) |
| Troubleshooting | [TROUBLESHOOTING.md](TROUBLESHOOTING.md) |
| Admin quick reference | [ADMIN_GUIDE.md](ADMIN_GUIDE.md) |
| Demo architecture | [DEMO_MODE.md](DEMO_MODE.md) |
| Changelog | [CHANGELOG.md](../CHANGELOG.md) |

### Commercial support

> **[Placeholder: Support contact]**  
> Replace with your company support email, ticket portal, SLA hours, and license terms.  
> Example: `support@yourcompany.example` · Portal: `https://support.yourcompany.example`

When requesting support, include:

1. ContentFlow version (see [CHANGELOG.md](../CHANGELOG.md) / `config/contentflow.php`)
2. PHP and MySQL versions
3. Steps to reproduce
4. Relevant log excerpts from `storage/logs/laravel.log` (**redact passwords and secrets**)

### License

See [LICENSE.md](../LICENSE.md) for license terms applicable to your distribution.

---

## Document control

| Field | Value |
|---|---|
| Document | User Guide |
| Product | ContentFlow CMS 1.0.0 |
| Last updated | 2026-08-31 |
| Maintainer | Product documentation |

_End of User Guide_

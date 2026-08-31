# Commercial Release Guide — ContentFlow CMS

Product management playbook for selling, packaging, and supporting ContentFlow CMS as a **commercial source-code product**.

| Field | Value |
|---|---|
| **Product** | ContentFlow CMS |
| **Version** | 1.0.0 |
| **Distribution model** | One-time purchase / license + optional support subscription |
| **Runtime** | Self-hosted (Linux VPS) |
| **Related docs** | [RELEASE.md](RELEASE.md), [RELEASE_AUDIT.md](RELEASE_AUDIT.md), [UPDATE_STRATEGY.md](UPDATE_STRATEGY.md), [USER_GUIDE.md](USER_GUIDE.md) |

---

## 1. Product name

**ContentFlow CMS**

- **Short name:** ContentFlow
- **Package slug:** `contentflow-cms`
- **Composer name:** `contentflow/cms`
- **Version constant:** `config('contentflow.version')` → `1.0.0`
- **Tag line (internal):** *Editorial-first Laravel CMS for agencies and content teams.*

Use **ContentFlow CMS** on invoices, licenses, and marketing pages. Use **ContentFlow** in conversational copy after first mention.

---

## 2. Product positioning

### Positioning statement

> **ContentFlow CMS** is a self-hosted, editorial-first Laravel CMS for freelancers, digital agencies, and content teams who need a **clean publishing workflow** and **predictable permissions** — without the complexity of WordPress plugins, page builders, or SaaS lock-in.

### Category

- **Primary:** Source-code CMS (PHP / Laravel)
- **Secondary:** Agency reusable website foundation
- **Not:** SaaS platform, page builder, e-commerce stack, headless API product

### Value proposition (buyer language)

| Pain | ContentFlow answer |
|---|---|
| “Every client site rebuilds the same CMS features” | Complete MVP editorial stack in one codebase |
| “WordPress is heavy and plugin-dependent” | Opinionated, minimal scope, Laravel maintainability |
| “Generic admin kits still need months of CMS work” | End-to-end product: public site + admin + installer + docs |
| “Buyers can’t evaluate before purchase” | Live demo mode with role-based sandbox |
| “Upgrades break client data” | SemVer, forward migrations, documented upgrade path |

### Pricing tiers (recommended structure)

Define at go-to-market; suggested framing:

| Tier | Buyer | Includes |
|---|---|---|
| **Standard License** | Single end-client project | Source download, docs, 12 months updates |
| **Agency License** | Unlimited client projects for one agency | Same + priority support option |
| **Extended License** | Product resale / white-label SaaS prep | Legal review required; not included in MVP default |

Document exact pricing, refund policy, and license count on the sales page — not in the repository.

---

## 3. Target buyers

### Primary buyers (decision makers)

| Persona | Why they buy | Success metric |
|---|---|---|
| **Freelance Laravel developer** | Reusable CMS base for client blogs and company sites | First client site live in days, not weeks |
| **Digital agency tech lead** | Standardized handover to editors; predictable stack | Repeatable deploy + training with USER_GUIDE |
| **Small business owner (technical)** | Own their stack; no SaaS subscription | Independent content updates without developer |
| **Startup content team** | Fast editorial ops on VPS they control | Publish first article within 10 minutes of setup |

### Secondary buyers (influencers)

| Persona | Role in purchase |
|---|---|
| **Editor / marketing manager** | Evaluates admin UX via demo |
| **DevOps / sysadmin** | Validates install, backup, queue, cron docs |
| **Procurement / legal** | Reviews license terms and support SLA |

### Anti-personas (do not target for v1.0)

- Teams needing **multi-tenant SaaS**, billing, or subscriptions
- Merchants needing **e-commerce / payments**
- Marketing teams requiring **visual drag-and-drop page building**
- Organizations requiring **multilingual content editing** out of the box

Redirect these to roadmap or custom services — do not oversell MVP scope.

---

## 4. Unique selling points (USPs)

Lead with these on the marketing page and sales conversations:

1. **Editorial-first, not feature-bloated** — Draft → schedule → publish workflow is the product center, not an afterthought.
2. **Complete product, not a starter kit** — Posts, pages, media, comments, menus, SEO, roles, public site, installer, and demo mode ship together.
3. **Agency-repeatable** — Same codebase for blog, company site, portal, or publication; hand off with documentation.
4. **Permission clarity** — Default roles (Super Admin, Administrator, Editor, Author) with policy-enforced boundaries and test coverage.
5. **Self-hosted ownership** — Customer owns code and data on their VPS; no platform lock-in.
6. **Commercial documentation package** — USER_GUIDE, ADMIN_GUIDE, DEPLOYMENT, UPGRADE, TROUBLESHOOTING included.
7. **Safe evaluation** — Demo mode with resettable sandbox and protected system accounts.
8. **Upgrade-safe data model** — SemVer and forward-only migrations designed to preserve content across updates.
9. **Modern Laravel stack** — Laravel 13, Livewire 4, Tailwind 4 — maintainable for PHP teams.
10. **Security-conscious MVP** — CSRF, policy authorization, upload hardening, inactive-user session revocation, security audit documented.

---

## 5. Feature list

### Included in v1.0.0 (ship on marketing page)

**Content & publishing**

- Posts — draft, scheduled, published, archived
- Pages — static content with SEO
- Categories and tags with public archive pages
- Featured images and excerpt support
- Post/page preview before publish
- Scheduled publishing (cron + artisan command)

**Media & engagement**

- Media library — upload, search, alt text, reuse
- Comments — public submission, pending default, moderation (approve / reject / spam)

**Site structure & discovery**

- Primary and footer menu manager
- Admin search and filters (posts, pages, media, comments)
- Public website — home, blog, posts, pages, category/tag archives, custom 404

**Administration**

- User management — create, edit, activate/deactivate
- Roles and permissions — configurable permission matrix
- Site settings — branding, logo, favicon, contact, social links, timezone, locale
- SEO — global defaults + per-content overrides (title, description, robots, OG image)
- Dashboard — status counts, recent content, scheduled queue
- Activity log — audit trail for sensitive admin actions

**Operations**

- Browser installer (`/install`) with post-install lock
- Demo mode — sandbox, banner, restrictions, `demo:reset`
- Password reset (queued email)
- Queue + scheduler documentation for production

**Documentation & release**

- Full `docs/` set, CHANGELOG, LICENSE, packaging script

### Explicitly not included (state on marketing page to reduce refunds)

- E-commerce, payments, subscriptions
- Multi-tenancy / SaaS tenant management
- Visual page builder / WYSIWYG HTML editor (plain escaped text content)
- Public site search UI
- Public REST/GraphQL API
- Two-factor authentication
- Multilingual content
- Built-in analytics dashboard
- CSV/content export/import
- Native mobile apps

See [CHANGELOG.md](../CHANGELOG.md) and [PRD.md](PRD.md) for authoritative scope.

---

## 6. Minimum server requirements

Publish these on the sales page and in [INSTALLATION.md](INSTALLATION.md).

### Production minimum (single site, MVP traffic)

| Component | Minimum | Recommended |
|---|---|---|
| **OS** | Linux (Ubuntu 22.04+ or Debian 12+) | Ubuntu 24.04 LTS |
| **Web server** | Nginx 1.18+ | Nginx + HTTPS (Let’s Encrypt) |
| **PHP** | 8.3+ | 8.4.x |
| **PHP extensions** | `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo` | Same |
| **Database** | MySQL 8.4.x LTS | MySQL 8.4.x on same VPS or managed DB |
| **Memory** | 1 GB RAM | 2 GB+ RAM |
| **Disk** | 10 GB | 20 GB+ (media growth) |
| **CPU** | 1 vCPU | 2 vCPU for admin + queue worker |

### Build / install host (can differ from production)

| Component | Version |
|---|---|
| **Composer** | 2.x |
| **Node.js** | 20+ (22 recommended) |
| **npm** | 10+ |

**Note:** Customers may build front-end assets on CI and deploy pre-built `public/build/` so Node.js is not required on the production server. See [§ Release ZIP structure](#12-release-zip-structure).

### Not supported for production

- SQLite (local smoke tests only)
- Shared hosting without SSH / cron / queue worker (document as unsupported or best-effort)
- Internet Explorer

---

## 7. Installation experience

### Buyer promise

> “Deploy on your VPS, run the installer, and sign in as Super Admin — no manual SQL or undocumented tribal steps.”

### Standard customer journey

```text
Purchase → Download ZIP → Extract to /var/www/contentflow
    → composer install --no-dev
    → (optional) npm ci && npm run build  OR use pre-built assets
    → Point Nginx document root to public/
    → Open /install
    → Complete 6-step wizard
    → Sign in at /login
    → Configure queue + cron (DEPLOYMENT.md)
    → Go live
```

### Installer steps (customer-facing summary)

| Step | Customer sees |
|---|---|
| 1. Requirements | PHP version, extensions, writable paths |
| 2. Application | Site name and URL |
| 3. Database | MySQL connection test + migrations |
| 4. Administrator | Super Admin account (password not re-shown) |
| 5. Site settings | Timezone, locale, site name |
| 6. Complete | Success + lock; `/install` permanently closed |

### Post-install checklist (include in welcome email)

- [ ] `php artisan storage:link` (until TASK-028-C automates this)
- [ ] Configure SMTP for password reset
- [ ] Start queue worker (Supervisor)
- [ ] Add cron for scheduler (scheduled posts)
- [ ] Set `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true` on HTTPS
- [ ] Verify `storage/app/install.lock` exists
- [ ] Run backup procedure once before go-live

### Installation positioning vs competitors

| ContentFlow | Typical generic Laravel kit |
|---|---|
| Browser wizard + lock | README + artisan commands only |
| Idempotent role/settings seed | Manual seeder documentation |
| Commercial INSTALLATION + DEPLOYMENT guides | Sparse or dev-only notes |

---

## 8. Demo strategy

### Goals

1. Let prospects **experience** admin UX and publishing flow before purchase.
2. Protect baseline data — demo resets cleanly.
3. Never conflate demo host with customer production patterns.

### Demo host architecture

| Element | Recommendation |
|---|---|
| **URL** | `demo.contentflowcms.com` (dedicated subdomain) |
| **Environment** | `APP_ENV=production`, `DEMO_MODE=true`, `DEMO_ALLOW_IN_PRODUCTION=true` |
| **Reset** | Cron: `php artisan demo:reset --force` every 6–24 hours |
| **Banner** | Persistent “Demo environment” on all pages |
| **Data** | `php artisan db:seed --class=DemoSeeder` |

### Demo accounts (public — safe to publish on marketing page)

Password for all demo accounts: **`DemoPass123!`**

| Role | Email | Use in demo script |
|---|---|---|
| Super Admin | `demo-superadmin@contentflow.test` | Full system access |
| Administrator | `demo-admin@contentflow.test` | Users, settings, menus |
| Editor | `demo-editor@contentflow.test` | Publish, moderate, taxonomy |

**Do not** use these credentials on customer production sites.

### Demo script (3-minute evaluation path)

1. Open public home — show blog, pages, navigation.
2. Log in as **Editor** — create draft post, preview, schedule.
3. Log in as **Administrator** — moderate a pending comment, edit menu item.
4. Show **Media**, **SEO** fields, **Dashboard** counts.
5. Mention demo resets — destructive changes are temporary.

### Demo limitations to disclose

- Plain-text content (no rich HTML editor)
- Demo restricts password reset, user creation, and site settings changes
- Sample media may be limited until DemoSeeder includes media assets (roadmap)

### Future enhancement

- Installer opt-in “Seed demo content” for agency staging (TASK-028-B)

---

## 9. Screenshot requirements

Screenshots are **required** before customer-facing documentation and marketplace listing. Replace `[Screenshot]` placeholders in [USER_GUIDE.md](USER_GUIDE.md).

### Minimum screenshot set (16 images)

| # | Screen | Filename convention | Notes |
|---|---|---|---|
| 1 | Public home | `01-public-home.png` | Show blog listing, header nav |
| 2 | Public post detail | `02-public-post.png` | Author, date, comments section |
| 3 | Login | `03-login.png` | Clean auth layout |
| 4 | Dashboard | `04-admin-dashboard.png` | Counts + recent + scheduled widgets |
| 5 | Posts list | `05-posts-index.png` | Filters visible |
| 6 | Post editor | `06-post-edit.png` | Title, content, category, SEO panel |
| 7 | Post preview | `07-post-preview.png` | Preview before publish |
| 8 | Pages list | `08-pages-index.png` | |
| 9 | Media library | `09-media-library.png` | Grid + upload |
| 10 | Comments moderation | `10-comments.png` | Pending queue |
| 11 | Menu editor | `11-menus.png` | Reorder items |
| 12 | Categories / tags | `12-taxonomy.png` | |
| 13 | Users list | `13-users.png` | Roles visible |
| 14 | Roles / permissions | `14-roles.png` | Permission groups |
| 15 | Site settings | `15-settings.png` | Branding section |
| 16 | Installer complete | `16-install-complete.png` | Success step |

### Capture standards

| Rule | Value |
|---|---|
| **Resolution** | 1440×900 minimum (admin); 1280×800 acceptable |
| **Browser** | Chrome or Firefox, clean profile, no extensions visible |
| **Data** | DemoSeeder content or neutral “Acme Corp” branding — no real customer data |
| **Format** | PNG (lossless); WebP derivatives optional for web |
| **Annotations** | Optional: numbered callouts for USER_GUIDE PDF export |
| **Dark mode** | Not required for v1.0 (product is light theme only) |

### Storage layout (repository or asset pack)

```text
marketing/
  screenshots/
    v1.0.0/
      01-public-home.png
      ...
  social/
    og-image.png          # 1200×630
    twitter-card.png      # 1200×675
```

Do not commit large binary assets to git if using external CDN or marketplace hosting — document path in release checklist.

---

## 10. Promo video requirements

### Recommended deliverables

| Asset | Length | Purpose |
|---|---|---|
| **Hero promo** | 60–90 seconds | Marketing page header, YouTube ads |
| **Feature walkthrough** | 3–5 minutes | Deep evaluation for technical buyers |
| **Install quickstart** | 2–3 minutes | Post-purchase onboarding |

### Hero promo script outline (60s)

1. **Hook (0–10s):** “Stop rebuilding CMS basics for every client project.”
2. **Problem (10–20s):** Scattered content ops, unclear permissions, plugin sprawl (WordPress contrast — no direct trademark attack).
3. **Demo (20–50s):** Fast cuts — dashboard → create post → publish → public page → roles.
4. **Close (50–60s):** Self-hosted Laravel CMS, full source, documentation included, link to live demo.

### Production specs

| Spec | Requirement |
|---|---|
| **Resolution** | 1920×1080 (1080p); export 4K master if available |
| **Frame rate** | 30 fps |
| **Audio** | Voiceover + subtle background music (licensed) |
| **Captions** | English SRT required (accessibility + muted autoplay) |
| **Branding** | ContentFlow logo, consistent with site colors (slate/neutral admin UI) |
| **Recording** | OBS or ScreenFlow; demo host only — no localhost |
| **Privacy** | No real emails, API keys, or `.env` visible |

### Hosting

- YouTube (unlisted until launch, then public)
- Embedded on marketing page via privacy-enhanced embed
- Optional: short GIF loops for feature sections (≤ 15s, ≤ 5 MB)

---

## 11. Documentation requirements

### Included in every customer package

| Document | Path | Audience |
|---|---|---|
| User Guide | [USER_GUIDE.md](USER_GUIDE.md) | Owners, editors, ops |
| Admin Guide | [ADMIN_GUIDE.md](ADMIN_GUIDE.md) | Staff quick reference |
| Installation | [INSTALLATION.md](INSTALLATION.md) | Install |
| Deployment | [DEPLOYMENT.md](DEPLOYMENT.md) | Production ops |
| Upgrade | [UPGRADE.md](UPGRADE.md) | Version updates |
| Update policy | [UPDATE_STRATEGY.md](UPDATE_STRATEGY.md) | Maintainers |
| Troubleshooting | [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Support |
| Security checklist | [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) | Pre-go-live |
| Developer Guide | [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) | Customization |
| Changelog | [CHANGELOG.md](../CHANGELOG.md) | All |
| Release notes | [RELEASE.md](RELEASE.md) | v1.0.0 specifics |

### Documentation quality gates (before commercial launch)

- [ ] All `[Screenshot]` placeholders replaced or linked in PDF export
- [ ] Demo credentials match `DemoSeeder` and marketing page
- [ ] Version numbers consistent (`1.0.0` in USER_GUIDE, CHANGELOG, config)
- [ ] Out-of-scope features listed in USER_GUIDE §1 and sales page
- [ ] LICENSE terms match actual legal offer (see [§ Licensing](#14-licensing-considerations))
- [ ] No committed secrets in docs (SMTP examples use placeholders)

### Optional customer deliverables (upsell / agency tier)

- PDF export of USER_GUIDE with screenshots
- 1-page “Editor quick start” laminated/card PDF
- Video install walkthrough (§10)

---

## 12. Support policy

Recommended policy for commercial source-code products — **customize before publishing**.

### Standard license support

| Channel | Included | Response target |
|---|---|---|
| **Documentation** | Yes — self-service | Immediate |
| **Email support** | 90 days from purchase | 2 business days |
| **Bug fixes (patch releases)** | 12 months updates | Per UPDATE_STRATEGY |
| **Installation assistance** | Best-effort email | 2 business days |
| **Custom development** | Not included | Paid engagement |
| **Phone / Slack** | Not included | Agency tier optional |

### What support covers

- Clarification of documented behavior
- Defects reproducible on supported stack (PHP 8.3+, MySQL 8.4, Linux)
- Security issues — responsible disclosure, patch priority

### What support does not cover

- Server provisioning, Nginx/SSL misconfiguration beyond DEPLOYMENT.md
- Third-party plugins or customer code modifications
- Recovery without backups
- Issues on unsupported stacks (SQLite production, missing cron/queue)
- Training content creation for end-client editors (agency responsibility)

### Severity SLAs (optional paid support tier)

| Severity | Definition | Target response |
|---|---|---|
| **S1 — Critical** | Production site down, data loss risk | 24 hours |
| **S2 — High** | Major feature broken, no workaround | 2 business days |
| **S3 — Medium** | Partial issue with workaround | 5 business days |
| **S4 — Low** | Question, cosmetic, feature request | Best effort |

### Support intake template (auto-reply)

Collect: license key / purchase email, version (`config/contentflow.version`), PHP/MySQL versions, error message (no `.env` contents), steps to reproduce, whether `APP_DEBUG=false` in production.

---

## 13. Update policy

Customer-facing summary — full detail in [UPDATE_STRATEGY.md](UPDATE_STRATEGY.md).

| Topic | Policy |
|---|---|
| **Versioning** | Semantic Versioning (SemVer) |
| **Update delivery** | Download new release ZIP or git tag access (per license) |
| **Included period** | 12 months from purchase (recommended) |
| **Patch releases** | Bug + security fixes; backward compatible |
| **Minor releases** | New features; backward compatible migrations |
| **Major releases** | Breaking changes; migration guide required |
| **Customer data** | Preserved across patch/minor upgrades when following UPGRADE.md |
| **Rollback** | Restore database + `storage/app` backup; redeploy previous version tag |
| **Renewal** | Optional annual update subscription after included period |

### Customer upgrade steps (one paragraph for FAQ)

Back up database and `storage/app/`, deploy new files, run `composer install --no-dev`, rebuild assets if needed, run `php artisan migrate --force`, clear caches, smoke-test admin and public site. Never run `migrate:fresh` on production.

---

## 14. Licensing considerations

### Current repository state

The project ships with **[MIT License](../LICENSE.md)** in `LICENSE.md` and `composer.json`. MIT allows recipients to use, modify, and **redistribute** the software with minimal restrictions.

### Commercial implication

**MIT is usually wrong for a paid source-code product** unless you deliberately use open-core or services revenue. For a traditional commercial CMS sale, buyers expect:

- Per-project or per-developer license limits
- No public redistribution of source
- Clear distinction between permitted client work and competing product resale

### Recommended licensing models (choose one before launch)

| Model | Description | Best for |
|---|---|---|
| **A. Proprietary EULA** | Replace MIT in customer packages; repo stays private | Classic CodeCanyon / direct sales |
| **B. Dual license** | MIT for OSS community edition; commercial license for paid features | Open-core growth |
| **C. Agency EULA + MIT core** | MIT base + paid “pro” modules | Ecosystem play (later) |

### Minimum EULA clauses (legal review required)

1. **Grant** — non-exclusive, non-transferable license to use source for defined scope
2. **Restrictions** — no public source redistribution, no competing CMS product resale
3. **Single vs multi-project** — Standard vs Agency tier
4. **Support and updates** — term and renewal
5. **Warranty disclaimer** — “AS IS” aligned with software industry norm
6. **Liability cap**
7. **Termination** — breach and refund policy reference

### Action items before first sale

- [ ] Engage legal counsel for EULA + refund policy
- [ ] Remove or dual-label MIT in **customer-facing** tarball if proprietary
- [ ] Add `LICENSE.txt` or `EULA.pdf` to release ZIP
- [ ] License verification mechanism (purchase email, Gumroad/Lemon Squeezy key, or manual)

**Do not** ship contradictory terms (MIT in package + “all rights reserved” on sales page).

---

## 15. Product package structure

Logical structure of what the customer receives:

```text
ContentFlow CMS v1.0.0
├── Source code (app, config, database, resources, routes, public)
├── Database migrations & seeders
├── Built front-end assets (public/build/) — recommended pre-built
├── Documentation (docs/)
├── Tests (tests/) — optional for deploy; useful for agencies
├── Release metadata (CHANGELOG, LICENSE/EULA, README)
└── Environment template (.env.example)
```

### Customer responsibilities (not in package)

- Linux VPS and MySQL database
- `.env` with secrets (never in ZIP)
- `vendor/` via `composer install`
- Queue worker + cron configuration
- SMTP credentials
- SSL certificate

---

## 16. Release ZIP structure

Built via [`.release/package.sh`](../.release/package.sh) → `dist/contentflow-cms-1.0.0.tar.gz`

### Archive contents

```text
contentflow-cms-1.0.0.tar.gz
├── app/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── docs/                          # Full commercial documentation
├── public/
│   └── build/                     # Present if pre-built on CI
├── resources/
├── routes/
├── tests/
├── .release/
│   └── PACKAGE_MANIFEST.md
├── .env.example
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── vite.config.js
├── CHANGELOG.md
├── LICENSE.md                     # Replace with EULA when proprietary
└── README.md
```

### Excluded from archive (never ship)

| Path | Reason |
|---|---|
| `.env` | Secrets |
| `.git/` | VCS metadata |
| `vendor/` | Customer runs Composer |
| `node_modules/` | Customer/build CI runs npm |
| `storage/app/install.lock` | Environment-specific |
| `storage/logs/*` | Dev logs |
| IDE folders | Dev tooling |

### Marketplace ZIP variant (optional)

Some marketplaces require `.zip` instead of `.tar.gz`:

```bash
cd dist && tar -xzf contentflow-cms-1.0.0.tar.gz
zip -r contentflow-cms-1.0.0.zip contentflow-cms-1.0.0/
```

Include a **`READ_ME_FIRST.txt`** at ZIP root:

```text
ContentFlow CMS v1.0.0
Start: docs/INSTALLATION.md
Support: support@your-domain.example
License: see LICENSE.txt
```

---

## 17. Demo credentials

**For marketing page and demo host only.**

| Field | Value |
|---|---|
| **Demo URL** | `https://demo.contentflowcms.com` (configure your domain) |
| **Password (all accounts)** | `DemoPass123!` |

| Role | Email |
|---|---|
| Super Admin | `demo-superadmin@contentflow.test` |
| Administrator | `demo-admin@contentflow.test` |
| Editor | `demo-editor@contentflow.test` |

**Author role:** not seeded by default — mention as roadmap or add to DemoSeeder if needed for sales script.

Security notes for public listing:

- Demo blocks password reset and destructive settings changes
- Reset cron restores baseline
- Never reuse demo password on production

---

## 18. Marketing page structure

Recommended single-page layout (sections in order):

```text
┌─────────────────────────────────────────────────────────┐
│  HERO: Headline + subhead + CTA (Buy / View Demo)       │
│  [Hero promo video or animated screenshot carousel]      │
├─────────────────────────────────────────────────────────┤
│  TRUST: Laravel · Self-hosted · Full source · Docs       │
├─────────────────────────────────────────────────────────┤
│  PROBLEM → SOLUTION: 3 columns (agency / editor / ops)   │
├─────────────────────────────────────────────────────────┤
│  FEATURE GRID: 8–12 icons with short labels              │
├─────────────────────────────────────────────────────────┤
│  SCREENSHOTS: Tabbed Admin | Public | Installer          │
├─────────────────────────────────────────────────────────┤
│  LIVE DEMO: Embedded credentials + “Open demo” button    │
├─────────────────────────────────────────────────────────┤
│  TECH SPECS: PHP, MySQL, Laravel versions                │
├─────────────────────────────────────────────────────────┤
│  WHAT'S INCLUDED: Source, docs, updates, support term    │
├─────────────────────────────────────────────────────────┤
│  COMPARISON: ContentFlow vs generic alternatives       │
├─────────────────────────────────────────────────────────┤
│  PRICING: Standard / Agency tiers + FAQ link           │
├─────────────────────────────────────────────────────────┤
│  ROADMAP TEASER: v1.5 revisions, v2 agency edition      │
├─────────────────────────────────────────────────────────┤
│  FAQ: Accordion (see §19)                                │
├─────────────────────────────────────────────────────────┤
│  FOOTER: Changelog, license, contact, refund policy    │
└─────────────────────────────────────────────────────────┘
```

### Primary CTAs

1. **View live demo** (low friction — top of page)
2. **Purchase / Get license** (above fold, repeated after features)
3. **Documentation preview** (link to sanitized USER_GUIDE PDF)

### SEO targets

- Laravel CMS source code
- Self-hosted blog CMS PHP
- Agency reusable CMS Laravel
- Editorial CMS self hosted

---

## 19. FAQ

### Product

**Q: Is ContentFlow a WordPress replacement?**  
A: For editorial blogs and company websites, yes — if you want Laravel maintainability and a fixed scope without plugins. It is not a plugin ecosystem and does not replicate every WordPress extension.

**Q: Do I need to pay monthly?**  
A: The product is self-hosted source code. You pay for the license (and optional support renewal). Hosting (VPS) and email are your ongoing costs.

**Q: Can I use this for multiple client projects?**  
A: Depends on license tier — Standard typically one end project; Agency tier unlimited client projects for one agency. See license terms at purchase.

**Q: Is there a WYSIWYG / HTML editor?**  
A: Not in v1.0. Content is plain text with escaped output for security. Rich editing is planned for a future release.

**Q: Can I run a SaaS with this?**  
A: Not out of the box. Multi-tenancy and billing are out of MVP scope. Extended license and custom architecture required.

### Technical

**Q: What stack is required?**  
A: Linux VPS, PHP 8.3+, MySQL 8.4, Nginx, Composer. See [§ Minimum server requirements](#6-minimum-server-requirements).

**Q: Does it include an installer?**  
A: Yes — browser wizard at `/install` for first-time setup.

**Q: How do scheduled posts work?**  
A: Cron runs Laravel scheduler every minute; command publishes due posts. See DEPLOYMENT.md.

**Q: Can I deploy without Node.js on the server?**  
A: Yes, if you deploy pre-built assets in `public/build/` (recommended in release tarball).

**Q: Is SQLite supported?**  
A: Local development smoke tests only. Production requires MySQL.

### Licensing & support

**Q: Can I modify the source code?**  
A: Yes — you receive full source. License restricts redistribution/resale as a competing product, not internal modification.

**Q: How long are updates included?**  
A: Recommended 12 months from purchase; then optional renewal.

**Q: What if I need installation help?**  
A: Standard support covers documented procedures via email for the support term. Hands-on server work is a paid service.

---

## 20. Pre-sale questions

Use this checklist in sales chat or qualification form:

### Fit qualification

1. What type of website are you building (blog, company site, portal)?
2. Do you require e-commerce, memberships, or multi-language content?
3. Who will host the site (your VPS, client VPS, managed Laravel host)?
4. How many sites do you plan to deploy under this license?
5. Do you need a visual page builder or is structured editorial content sufficient?

**Disqualify early if:** e-commerce, SaaS multi-tenant, page builder required, shared hosting without SSH/cron.

### Technical qualification

6. Are you comfortable with Laravel, or do you have a PHP developer?
7. Can you run MySQL 8.4 and configure Nginx + SSL?
8. Will you configure queue worker and cron for scheduled posts and mail?

### Commercial qualification

9. Is this for a single client project or agency reuse?
10. Do you need invoice / PO procurement process?
11. What is your expected go-live date?
12. Do you require extended support or white-label terms?

### Red flags (pause sale)

- Expectation of “install on any cheap shared host with one click”
- Requirement for 100% WordPress plugin parity
- Intent to resell as competing CMS product without extended license
- No technical owner and refusal of hosted setup service

---

## 21. Post-sale support workflow

```text
┌──────────────┐     ┌─────────────────┐     ┌──────────────────┐
│ Purchase     │────▶│ Delivery email  │────▶│ Customer portal  │
│ confirmed    │     │ ZIP + license   │     │ or download link │
└──────────────┘     └─────────────────┘     └────────┬─────────┘
                                                        │
                        ┌───────────────────────────────┘
                        ▼
              ┌─────────────────────┐
              │ READ_ME_FIRST +     │
              │ INSTALLATION.md     │
              └──────────┬──────────┘
                         │
         ┌───────────────┼───────────────┐
         ▼               ▼               ▼
   Install OK      Install issue    Feature question
         │               │               │
         ▼               ▼               ▼
   DEPLOYMENT.md   Support ticket    USER_GUIDE /
   smoke test      + TROUBLESHOOTING  ADMIN_GUIDE
         │               │               │
         ▼               ▼               ▼
   Go-live          Bug? → GitHub/     Answered /
   optional         internal issue    closed
   survey           tracker
```

### Delivery email template (outline)

**Subject:** Your ContentFlow CMS v1.0.0 download

**Body:**

1. Thank you + license tier summary
2. Secure download link (expiring)
3. License key / purchase record
4. Links: INSTALLATION.md, DEPLOYMENT.md, USER_GUIDE.md
5. Demo credentials (for training, not production)
6. Support email + expected response time
7. Update policy summary + changelog URL
8. Refund policy link (if applicable)

### Ticket triage (support team)

| Step | Action |
|---|---|
| 1 | Verify active license and support term |
| 2 | Request version, PHP/MySQL, sanitized error description |
| 3 | Classify: install / config / defect / customization / feature request |
| 4 | Defect → reproduce on reference stack; log internal issue |
| 5 | Config → point to doc section; close if resolved |
| 6 | Customization → quote or decline per policy |
| 7 | Security report → private channel, patch priority per UPDATE_STRATEGY |

### Success milestones (reduce churn)

| Day | Touchpoint |
|---|---|
| 0 | Delivery email + READ_ME_FIRST |
| 3 | Optional: “Did install complete?” auto-email |
| 14 | Link to backup section (USER_GUIDE §17) |
| 30 | Request testimonial / case study (if go-live) |
| 11 mo | Update renewal reminder |

---

## 22. Competitive differentiation

### vs WordPress

| Dimension | WordPress | ContentFlow CMS |
|---|---|---|
| Stack | PHP procedural + plugins | Laravel 13 + structured app |
| Scope | Infinite plugins | Opinionated editorial MVP |
| Security surface | Plugins, themes, legacy APIs | Smaller codebase, policy auth |
| Agency reuse | Theme/plugin variance | Same codebase, same docs |
| Hosting | Ubiquitous | VPS + MySQL (technical buyer) |
| **Sell when** | Client wants plugin ecosystem | Client wants maintainable custom stack |

### vs Filament / Laravel admin panels

| Dimension | Admin panel kit | ContentFlow CMS |
|---|---|---|
| Product type | UI framework | Complete CMS product |
| Public site | You build it | Included |
| Editorial workflows | You design them | Pre-built |
| Installer + demo | No | Yes |
| Time to client handover | Weeks–months | Days |
| **Sell when** | Buyer wants infinite flexibility | Buyer wants shipped CMS behavior |

### vs Headless (Strapi, Directus) + Next.js

| Dimension | Headless | ContentFlow CMS |
|---|---|---|
| Architecture | API + separate front-end | Monolith — simpler ops |
| Buyer skills | JS + API + hosting × 2 | PHP Laravel team |
| Cost | Higher build complexity | Lower total build for classic sites |
| **Sell when** | Omnichannel API-first | Traditional website + admin |

### vs CodeCanyon generic Laravel CMS scripts

| Dimension | Generic scripts | ContentFlow CMS |
|---|---|---|
| Documentation | Often minimal | Full commercial doc set |
| Tests | Often absent | ~350 automated tests |
| Security audit | Rare | SECURITY_AUDIT.md published |
| Upgrade policy | Unclear | UPDATE_STRATEGY.md SemVer |
| Demo mode | Rare | Built-in resettable sandbox |
| **Sell when** | Buyer compares on price only | Buyer compares on risk and TCO |

### Positioning sentence for sales

> “ContentFlow is the CMS you would have built after your third client blog — already documented, tested, and ready to deploy on Laravel.”

---

## 23. Commercial launch checklist

Before opening sales:

- [ ] [RELEASE_AUDIT.md](RELEASE_AUDIT.md) gates closed (green tests, backup evidenced)
- [ ] EULA finalized; MIT replaced or dual-licensed in customer package
- [ ] Pricing page live with refund policy
- [ ] Demo host running with cron reset
- [ ] 16 screenshots captured (§9)
- [ ] Hero promo video published (§10)
- [ ] USER_GUIDE screenshots replaced
- [ ] Payment + delivery automation tested end-to-end
- [ ] Support inbox + ticket template ready
- [ ] `dist/contentflow-cms-1.0.0.tar.gz` built and checksum recorded

---

## Document history

| Date | Version | Change |
|---|---|---|
| 2026-08-31 | 1.0 | Initial commercial release guide |

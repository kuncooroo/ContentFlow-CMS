# Performance Audit — ContentFlow CMS

| Field | Value |
|---|---|
| **Audit date** | 2026-08-31 |
| **Scope** | MVP v1.0.0 application layer (Laravel + MySQL) |
| **Method** | Static code review of queries, migrations, caching, queues, Livewire payloads |
| **References** | `docs/SYSTEM_DESIGN.md`, `DATABASE(2).md`, `docs/PRD.md`, `CURSOR.md` |
| **Related** | [TESTING.md](TESTING.md), [SECURITY_AUDIT.md](SECURITY_AUDIT.md) |

---

## Executive summary

ContentFlow CMS is **well structured for MVP-scale production** (roughly &lt;5k posts, &lt;50 users, single VPS). Admin lists use pagination, most relationship access is eager-loaded, and database indexes align with documented access patterns for posts, comments, menus, and activity logs.

The largest realistic performance risks are **read amplification**, not missing fundamentals:

1. **Uncached primary navigation** queried on every public page.
2. **LONGTEXT over-fetch** on public listings and menu resolution.
3. **Repeated role lookups** via `User::hasRole()` without preloaded roles.
4. **Admin search** using leading-wildcard `LIKE` patterns that will degrade as content grows.

**Do not optimize prematurely.** Redis, Octane, CDN, and dedicated search engines are not warranted until measured bottlenecks appear. Fix the high-impact, low-risk items below first.

---

## Assessment by category

| Category | Grade | Notes |
|---|---|---|
| N+1 queries (list views) | **A-** | `with()` used correctly on admin/public lists |
| N+1 queries (auth/roles) | **C** | `hasRole()` can hit DB repeatedly per request |
| Indexes | **B+** | Core content tables indexed; `users.status` missing |
| Pagination | **A** | All primary admin indexes paginate |
| Eager loading discipline | **B** | Some unused relations loaded |
| Dashboard aggregation | **B** | Good `GROUP BY`; 6+ queries per load |
| Caching | **C+** | Site settings cached; menus not cached |
| Queue usage | **B-** | Appropriate for MVP; only mail queued |
| File uploads | **A-** | Bounded size, streamed storage, MIME checks |
| API payload size | **N/A** | No public API |
| Livewire payload size | **C** | Full post/page body in component state |

---

## Findings

Each finding includes severity, component, realistic production impact, and recommended fix. Severity reflects impact at **typical CMS scale**, not hypothetical hyperscale.

---

### PERF-001 — Primary menu queried on every public page (uncached)

| Field | Detail |
|---|---|
| **Severity** | **High** |
| **Affected component** | `AppServiceProvider` (view composer), `MenuNavigationQuery` |
| **Problem** | Every public layout render loads menu + items + related page/post/category models from DB. `docs/SYSTEM_DESIGN.md` describes menu caching; it is not implemented. |
| **Production impact** | 2–4+ queries per public page view. Noticeable under traffic (blog listing + post detail + archives all pay this cost). |
| **Recommended fix** | Cache resolved nav items: `Cache::remember('menu:primary', ttl, fn () => ...)`. Invalidate in `UpdateMenuStructure` on save. |
| **When to implement** | Before production launch or when public traffic exceeds low hundreds of daily visitors. |
| **Status** | Open |

```35:42:app/Providers/AppServiceProvider.php
        View::composer('components.layouts.public', function ($view): void {
            $settings = app(SiteSettings::class)->get();
            $settings->loadMissing(['logoMedia', 'faviconMedia']);

            $navItems = app(MenuNavigationQuery::class)
                ->publicItemsForMenu('primary')
                ->filter(fn (array $item): bool => $item['is_publicly_visible'] && filled($item['url']))
                ->values();
```

---

### PERF-002 — Public post listings load full `content` LONGTEXT

| Field | Detail |
|---|---|
| **Severity** | **High** |
| **Affected component** | `PublicPostIndexQuery` |
| **Problem** | `Post::query()->...->get()` / `paginate()` selects all columns. List cards only use title, excerpt, author, publish_at. |
| **Production impact** | Transfers large blobs over MySQL wire and into PHP memory on home, archive, category, and tag pages. Grows linearly with post body size (10k posts × 20 KB body ≈ 200 MB per full-table read if ever unbounded; paginated reads still waste ~12 rows × body size per page). |
| **Recommended fix** | Add explicit column selection in `baseQuery()`: `select(['id','title','slug','excerpt','author_id','publish_at','status'])` (plus any scope-required columns). |
| **When to implement** | Now — low risk, clear win. |
| **Status** | Open |

```43:49:app/Queries/Content/PublicPostIndexQuery.php
    private function baseQuery(): Builder
    {
        return Post::query()
            ->publiclyVisible()
            ->with(['author'])
            ->orderByDesc('publish_at')
            ->orderByDesc('id');
    }
```

Post cards use only metadata:

```1:13:resources/views/public/partials/post-card.blade.php
<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm text-slate-500">{{ $post->author->name }}</p>
    ...
    @if ($post->excerpt)
        <p class="mt-2 text-slate-600">{{ $post->excerpt }}</p>
    @endif
```

---

### PERF-003 — Menu navigation eager-loads full Page/Post models

| Field | Detail |
|---|---|
| **Severity** | **High** |
| **Affected component** | `MenuNavigationQuery` |
| **Problem** | Eager load uses full models (`page`, `post`, `category`) including LONGTEXT `content` when only slug/status/visibility fields are needed for URL resolution. |
| **Production impact** | Amplifies PERF-001 on every public request. |
| **Recommended fix** | Constrain eager loads: `with(['page:id,slug,status', 'post:id,slug,status,publish_at', 'category:id,slug'])`. Combine with PERF-001 caching. |
| **Status** | Open |

---

### PERF-004 — Repeated `hasRole()` database queries

| Field | Detail |
|---|---|
| **Severity** | **Medium** |
| **Affected component** | `User::hasRole()`, `PostIndexQuery`, `PostPolicy`, `DashboardSummaryQuery` |
| **Problem** | When roles are not loaded, each `hasRole()` call runs a separate `EXISTS` query. Ownership checks call it up to 4 times per invocation. |
| **Production impact** | Authors see 8–16 extra queries on posts index and dashboard. Adds ~50–150 ms under load. Editors/admins hit fewer but still redundant calls. |
| **Recommended fix** | Eager-load auth user roles once per admin request (middleware: `Auth::user()?->load('roles')`). Refactor `requiresOwnership()` to check loaded roles in one pass. |
| **When to implement** | When admin latency is measured &gt;2.5 s or Author-role usage is heavy. |
| **Status** | Open |

```50:56:app/Models/User.php
    public function hasRole(string $roleName): bool
    {
        if (! $this->relationLoaded('roles')) {
            return $this->roles()->where('name', $roleName)->exists();
        }

        return $this->roles->contains('name', $roleName);
    }
```

---

### PERF-005 — Missing index on `users.status`

| Field | Detail |
|---|---|
| **Severity** | **Medium** |
| **Affected component** | `database/migrations/2026_08_31_120000_add_status_to_users_table.php` |
| **Problem** | Design doc specifies `INDEX (status)`; migration adds column only. |
| **Production impact** | Low at MVP user counts (&lt;500). User list filters and login credential checks scan more rows as staff accounts grow. |
| **Recommended fix** | Add migration: `$table->index('status')`. |
| **Status** | Open |

---

### PERF-006 — Admin search uses non-sargable `LIKE '%term%'`

| Field | Detail |
|---|---|
| **Severity** | **Medium** (at scale) |
| **Affected component** | `PostIndexQuery`, `PageIndexQuery`, `MediaLibraryQuery`, `CommentModerationQuery`, `UserIndexQuery` |
| **Problem** | `SearchTerm::likePattern()` produces leading wildcards. Existing B-tree indexes on title/slug/name cannot be used. |
| **Production impact** | Acceptable below ~10k rows per table. Search latency grows to hundreds of ms–seconds on large datasets. Comment search with `orWhereHas('post')` is worst case. |
| **Recommended fix** | **Do not fix prematurely.** Monitor search latency. If needed: MySQL `FULLTEXT` on title/excerpt, or dedicated search (Scout/Meilisearch) per PRD growth path. |
| **Status** | Acceptable for MVP; monitor |

---

### PERF-007 — Dashboard runs 6+ sequential queries per load

| Field | Detail |
|---|---|
| **Severity** | **Medium** |
| **Affected component** | `DashboardSummaryQuery` |
| **Problem** | Separate queries for post counts, page counts, pending comments, recent posts/pages, scheduled posts. Status aggregation uses efficient `GROUP BY`. |
| **Production impact** | ~30–80 ms for admins at moderate scale. Admin-only page; acceptable for MVP. |
| **Recommended fix** | Optional short-TTL cache keyed by user role/scope (`dashboard:summary:{userId}`, 30–60 s). Only if dashboard load time becomes a complaint. |
| **Status** | Acceptable; optional cache later |

Count aggregation is already efficient:

```41:44:app/Queries/Dashboard/DashboardSummaryQuery.php
        $counts = $this->scopedPostQuery($user)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
```

---

### PERF-008 — Over-eager loading on admin indexes

| Field | Detail |
|---|---|
| **Severity** | **Low** |
| **Affected component** | `PostIndexQuery`, `PageIndexQuery`, `CommentModerationQuery` |
| **Problem** | Relations loaded but not displayed: `tags`, `featuredMedia` (posts index), `ogMedia` (pages index), `moderator` (comments index). Dashboard recent lists load `author` but view does not show author name. |
| **Production impact** | 1–3 extra queries per admin list page; small constant overhead. |
| **Recommended fix** | Trim `with([...])` to match view usage. Low priority cleanup. |
| **Status** | Open |

---

### PERF-009 — Filter dropdowns re-query on every Livewire render

| Field | Detail |
|---|---|
| **Severity** | **Low** |
| **Affected component** | `Posts/Index`, `Pages/Index` Livewire components |
| **Problem** | `Category::orderBy('name')->get()` and author dropdown subqueries run on every debounced search/filter update. |
| **Production impact** | 2 extra queries per keystroke-driven re-render. Fine for small taxonomies. |
| **Recommended fix** | Cache taxonomy lists (5–15 min TTL) or load dropdowns once in `mount()` when lists are stable. |
| **Status** | Acceptable for MVP |

---

### PERF-010 — Comments index runs redundant `pendingCount()`

| Field | Detail |
|---|---|
| **Severity** | **Low** |
| **Affected component** | `Comments/Index` Livewire, `CommentModerationQuery` |
| **Problem** | `pendingCount()` executes on every render, including when already filtered to pending. |
| **Production impact** | One extra `COUNT(*)` per Livewire update. |
| **Recommended fix** | Skip count when filter is `pending`; or cache count with invalidation on moderation actions. |
| **Status** | Open (minor) |

---

### PERF-011 — Audit log actor filter loads all distinct actors

| Field | Detail |
|---|---|
| **Severity** | **Medium** (long-running sites) |
| **Affected component** | `ActivityLogQuery::actorIdsWithLogs()`, `Audit/Index` Livewire |
| **Problem** | Unbounded `DISTINCT actor_user_id` scan on every audit index render. |
| **Production impact** | Negligible early; grows with audit log volume (100k+ rows). |
| **Recommended fix** | Limit to recent actors (e.g. last 90 days) or cache actor ID list with invalidation on new audit entries. |
| **Status** | Open |

---

### PERF-012 — `Schema::hasTable()` before every site settings read

| Field | Detail |
|---|---|
| **Severity** | **Low** |
| **Affected component** | `SiteSettings::get()` |
| **Problem** | Metadata query runs even when settings are cache-hit. |
| **Production impact** | One information_schema/table check per request using settings (public layout). |
| **Recommended fix** | Move table-existence guard inside cache miss path only, or resolve once at boot after install. |
| **Status** | Open |

---

### PERF-013 — Livewire editors hold full LONGTEXT content in component state

| Field | Detail |
|---|---|
| **Severity** | **Medium** |
| **Affected component** | `Posts/Edit`, `Pages/Edit` Livewire components |
| **Problem** | Full `content` field is a public Livewire property; serialized on every admin interaction round-trip. |
| **Production impact** | Large POST payloads and memory use for long articles (50 KB+). Affects editor UX latency, not public visitors. |
| **Recommended fix** | Defer/lazy hydration for content field, or split editor into save-on-blur pattern. Implement when editors report slowness on long posts. |
| **Status** | Monitor |

---

### PERF-014 — Scheduled publishing runs synchronously in scheduler

| Field | Detail |
|---|---|
| **Severity** | **Low** |
| **Affected component** | `PublishScheduledPostsCommand`, `routes/console.php` |
| **Problem** | Cron command publishes due posts inline; uses chunking but no queue job. |
| **Production impact** | Fine for MVP publish volume. Scheduler overlap risk if many posts due simultaneously. |
| **Recommended fix** | Dispatch queue job when due post count regularly exceeds ~50/minute. Not needed yet. |
| **Status** | Acceptable for MVP |

---

### PERF-015 — Media upload is synchronous (bounded)

| Field | Detail |
|---|---|
| **Severity** | **Low** |
| **Affected component** | `StoreUploadedMedia`, `config/media.php` |
| **Problem** | Upload, MIME validation, and disk write happen in HTTP request. Max 5120 KB; images only. |
| **Production impact** | Acceptable for MVP admin usage. `storeAs()` streams to disk; memory bounded. |
| **Recommended fix** | Queue only if max upload size increases significantly or image processing (thumbnails) is added. |
| **Status** | Acceptable |

Minor note: `getimagesize()` is called twice (validation + dimensions) — reuse first result if optimizing.

---

### PERF-016 — Database cache + database sessions + database queue

| Field | Detail |
|---|---|
| **Severity** | **Low** (info) |
| **Affected component** | `.env.example`, `config/cache.php`, `config/session.php`, `config/queue.php` |
| **Problem** | All three default to MySQL-backed drivers on a single VPS. |
| **Production impact** | Extra MySQL traffic; fine for MVP. Becomes a bottleneck under high concurrent admin + queue workers. |
| **Recommended fix** | **Do not change by default.** Consider Redis or file cache if MySQL CPU/connections become constrained. |
| **Status** | Acceptable for MVP |

---

### PERF-017 — Site settings caching (positive)

| Field | Detail |
|---|---|
| **Severity** | N/A (strength) |
| **Affected component** | `SiteSettings` |
| **Notes** | `Cache::rememberForever` with invalidation on update/install/demo reset. Correct pattern per `SYSTEM_DESIGN.md`. |

---

### PERF-018 — Pagination on admin lists (positive)

| Field | Detail |
|---|---|
| **Severity** | N/A (strength) |
| **Affected component** | All primary `*IndexQuery` classes |
| **Notes** | Posts/pages/users/media/comments use `paginate(15–20)`. No unbounded admin table loads. |

---

## N+1 query summary

| Area | Risk | Mitigation in place |
|---|---|---|
| Post/page/comment admin lists | Low | `with(['author', ...])` |
| Public post detail | Low | `$post->load(['author', 'approvedComments'])` |
| Menu items | Medium | Eager load present but over-fetches columns |
| Auth role checks | **High** | None global; `hasRole()` hits DB |
| Policy `@can` in loops | Low | Permissions loaded once via `loadMissing` |
| Audit log list | Low | `with('actor')` |

---

## Index coverage summary

### Present and aligned with design

- `posts`: `(status, publish_at)`, `(author_id, status)`, `(status, created_at)`, unique `slug`
- `pages`: `(status, created_at)`, `(author_id, status)`, unique `slug`
- `comments`: `(status, created_at)`, `(post_id, status, created_at)`
- `menus` / `menu_items`: `(menu_id, position)`, FK indexes
- `activity_logs`: actor, subject, event, created_at indexes
- `media`: `(uploaded_by_user_id, created_at)`, prefix unique on `(disk, path)`

### Missing or deferred

| Index | Priority | Rationale |
|---|---|---|
| `users.status` | Medium | Login filter + admin user list |
| Fulltext on searchable text columns | Low | Only if LIKE search degrades |
| `posts.updated_at` | Low | Admin sort only; monitor first |

---

## Caching opportunities (prioritized)

| Candidate | Priority | Invalidation trigger | Premature? |
|---|---|---|---|
| Primary menu (`menu:primary`) | **P0** | Menu save | No |
| Taxonomy dropdowns (categories/tags) | P2 | Category/tag CRUD | Slightly |
| Dashboard summary | P3 | Post/page/comment changes | Yes for MVP |
| Public post list fragments | P3 | Publish/archive | Yes until traffic measured |
| Site settings | Done | Settings update | — |

**Rule:** Cache is optimization, never source of truth (`CURSOR.md` §43). Always invalidate on write.

---

## Queue candidates

| Operation | Current | Queue candidate? | Recommendation |
|---|---|---|---|
| Password reset email | Queued | — | Keep |
| Scheduled publish | Sync cron | Later | Keep sync for MVP |
| Media upload | Sync | No | Keep sync (5 MB cap) |
| Comment submission | Sync | No | Keep sync |
| Demo reset | Sync CLI | No | Demo-only |
| Install migrate/seed | Sync HTTP | No | One-time setup |
| Bulk export (future) | N/A | Yes | When feature ships |

---

## Memory and payload notes

| Surface | Concern | MVP impact |
|---|---|---|
| Public listings | LONGTEXT over-fetch | **Fix recommended** |
| Livewire post/page edit | Full body in state | Monitor for large articles |
| Media upload | Bounded 5 MB | OK |
| Post detail + comments | Loads all approved comments | OK for typical posts; paginate comments if threads grow large |
| API JSON responses | N/A | No public API |

---

## Slow reports / operational queries

No dedicated reporting module in MVP. Closest operational reads:

| Query | Risk | Mitigation |
|---|---|---|
| Dashboard counts | Low | Already aggregated |
| Audit log browse | Medium at scale | Paginated; fix actor dropdown scan |
| Admin search | Medium at scale | Accept MVP LIKE; plan search upgrade |
| Activity log export | N/A | Not implemented |

---

## Recommended optimization roadmap

### Phase 1 — Do now (high impact, low risk)

1. **PERF-002** — Select only list columns in `PublicPostIndexQuery`.
2. **PERF-003** — Constrain menu eager-load columns.
3. **PERF-001** — Cache primary menu with invalidation on menu update.

### Phase 2 — Before growth pain (medium impact)

4. **PERF-004** — Preload auth user roles in admin middleware.
5. **PERF-005** — Add `users.status` index migration.
6. **PERF-008** — Remove unused eager loads on admin indexes.
7. **PERF-011** — Bound audit actor dropdown query.

### Phase 3 — Measure first, then optimize

8. Admin search upgrade (FULLTEXT or external search) if p95 search &gt; 2 s.
9. Dashboard summary cache if admin home feels slow.
10. Livewire content field lazy loading if editors report latency.
11. Queue scheduled publishing if due volume spikes.
12. Redis cache layer if MySQL connection/CPU saturates.

### Explicitly defer

- Laravel Octane / FrankenPHP
- CDN for admin assets
- Elasticsearch / Meilisearch (without measurement)
- Microservices or read replicas
- Parallel PHPUnit (infra concern, not runtime)

---

## Verification and monitoring

### Baseline commands

```bash
# Query-focused tests
php artisan test tests/Unit/Queries
php artisan test tests/Feature/Admin/Dashboard
php artisan test tests/Feature/Public

# Full regression
composer test
```

### Production signals to watch

| Metric | Warning threshold | Action |
|---|---|---|
| Public page p95 TTFB | &gt; 2.5 s | Check menu cache, LONGTEXT selects |
| Admin search p95 | &gt; 2 s | Evaluate FULLTEXT/search engine |
| MySQL slow query log | Repeated `LIKE '%...%'` | Index/search strategy review |
| Scheduler duration | &gt; 30 s | Queue scheduled publish |
| Livewire request size | &gt; 500 KB | Lazy content hydration |

### Optional dev profiling

```bash
# Enable query log temporarily in local/staging
# Use Laravel Debugbar or Telescope (dev only) to confirm query counts per page
```

---

## Revision history

| Date | Change |
|---|---|
| 2026-08-31 | Initial performance audit |

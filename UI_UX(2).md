# UI/UX Architecture
## ContentFlow CMS

---

## Document Control

| Field | Value |
|---|---|
| Document | UI/UX Architecture |
| Product | ContentFlow CMS |
| Product Type | Commercial Content Management System |
| Primary Users | Blogger, Content Writer, Editor, Website Administrator, Digital Marketing Team, Company/Organization Content Team, Media/Publication Manager |
| Frontend | Blade + Livewire + Tailwind CSS + Alpine.js |
| UX Direction | Clean, editorial-first, professional SaaS |
| Scope | MVP / Version 1.0 |
| Status | Design specification |
| Last Updated | 2026-08-29 |

---

# 1. UX Principles

ContentFlow CMS should feel like a focused SaaS product rather than a generic admin template.

## 1.1 Clarity Over Density

The interface should prioritize:

- clear page purpose;
- obvious primary actions;
- readable content;
- predictable navigation;
- restrained information density.

Avoid:

- excessive cards;
- unnecessary charts;
- dense icon-only controls;
- decorative gradients;
- excessive badges;
- multiple competing primary buttons.

## 1.2 Editorial First

The admin experience must optimize the workflows that matter most:

```text
Write
→ Review
→ Schedule
→ Publish
→ Maintain
```

Content actions should always be easier to reach than administrative configuration.

## 1.3 Progressive Disclosure

Show advanced options only when needed.

Examples:

- SEO fields inside a collapsible section;
- advanced filters inside a Filter panel;
- destructive actions inside an overflow menu;
- media metadata shown when a media item is selected.

## 1.4 Predictable Patterns

All modules should use the same interaction patterns for:

- page headers;
- form validation;
- search;
- filters;
- tables;
- save actions;
- destructive actions;
- empty states;
- loading states;
- pagination.

## 1.5 Permission-Aware UI

Users should see only actions they are allowed to perform.

Examples:

- Author does not see Publish if not permitted.
- Editor does not see user-management modules unless permitted.
- Settings navigation is hidden for users without settings access.

Hiding UI does not replace backend authorization.

## 1.6 Low Cognitive Load

The interface should use:

- simple labels;
- one primary CTA per page;
- limited color accents;
- consistent iconography;
- sufficient whitespace;
- grouped settings;
- concise helper text.

## 1.7 Content Safety

The interface must make risky actions deliberate.

Examples:

- publishing;
- archiving;
- deleting media;
- deactivating users;
- role/permission changes.

## 1.8 Professional SaaS Appearance

The visual style should feel:

- calm;
- modern;
- trustworthy;
- neutral;
- spacious;
- polished.

Not:

- flashy;
- game-like;
- overly colorful;
- dashboard-heavy.

---

# 2. Application Layout

## 2.1 Desktop Admin Shell

Recommended structure:

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Top Bar                                                              │
├──────────────────┬───────────────────────────────────────────────────┤
│                  │                                                   │
│ Sidebar          │ Main Content                                      │
│                  │                                                   │
│                  │                                                   │
│                  │                                                   │
└──────────────────┴───────────────────────────────────────────────────┘
```

### Desktop Regions

**Sidebar**
- product navigation;
- current section indication;
- role-aware visibility.

**Top Bar**
- global search trigger;
- quick create;
- notifications if later enabled;
- profile menu.

**Main Content**
- page header;
- breadcrumbs if necessary;
- page actions;
- content area.

## 2.2 Main Content Width

For list/dashboard pages:

```text
max-width: wide / fluid
```

For forms/settings:

```text
max-width: medium
```

Avoid full-width form fields spanning very large desktop monitors.

## 2.3 Page Header Pattern

Each page should follow:

```text
Page Title
Short contextual description

[Secondary Action] [Primary Action]
```

Example:

```text
Posts
Manage drafts, scheduled posts, and published articles.

[Filters] [Create Post]
```

---

# 3. Sidebar Structure

The default admin sidebar should remain short and product-focused.

## 3.1 Recommended Sidebar

```text
ContentFlow

Overview
  Dashboard

Content
  Posts
  Pages
  Categories
  Tags
  Media

Engagement
  Comments

Website
  Menus
  SEO

Administration
  Users
  Roles & Permissions
  Activity Log
  Settings
```

## 3.2 Visibility by Role

### Author

```text
Dashboard
Posts
Media
Profile
```

Optional Tags visibility if permission exists.

### Editor

```text
Dashboard
Posts
Pages
Categories
Tags
Media
Comments
SEO
Profile
```

### Administrator

All operational modules except protected Super Admin-only actions.

### Super Admin

Full sidebar.

## 3.3 Sidebar Rules

- no more than 2 navigation hierarchy levels in MVP;
- sidebar groups should be collapsible only if necessary;
- active page must be visually obvious;
- labels must always accompany icons;
- do not rely on icon meaning alone;
- no marketing links inside operational navigation.

---

# 4. Top Navigation

Recommended top bar:

```text
[Sidebar Toggle]         [Global Search]      [+ Create]   [Profile]
```

## 4.1 Global Search

A search trigger may open a command-style search overlay in future versions.

MVP may use a simple search entry point that routes to the relevant module.

## 4.2 Quick Create

For authorized editorial users:

```text
+ Create
  Post
  Page
```

Do not show options the user cannot create.

## 4.3 Profile Menu

Recommended:

```text
Profile
Account Settings
View Site
Logout
```

Future:

```text
Theme preference
Help
Keyboard shortcuts
```

## 4.4 Top Bar Rules

- remain visually light;
- avoid showing multiple counters;
- no duplicate module navigation from the sidebar;
- profile image/avatar should be optional.

---

# 5. Mobile Navigation

The administration interface is primarily desktop/tablet optimized, but must remain usable on mobile.

## 5.1 Mobile Structure

```text
Top Bar
[Menu] ContentFlow                 [Profile]

Main Content
```

The sidebar becomes an off-canvas drawer.

## 5.2 Mobile Navigation Behavior

- hamburger opens sidebar drawer;
- drawer traps focus while open;
- Esc closes drawer where keyboard is available;
- tapping navigation closes drawer;
- destructive/admin actions remain accessible but not crowded into top bar.

## 5.3 Mobile Bottom Navigation

Do **not** use a permanent five-item bottom navigation for admin MVP.

Reason:

- CMS has too many sections;
- permission-based modules vary;
- bottom navigation would create inconsistent information architecture.

---

# 6. Page Hierarchy

## 6.1 Admin Hierarchy

```text
Admin
├── Dashboard
├── Posts
│   ├── List
│   ├── Create
│   ├── Edit
│   └── Preview
├── Pages
│   ├── List
│   ├── Create
│   ├── Edit
│   └── Preview
├── Categories
├── Tags
├── Media
│   ├── Library
│   └── Media Detail
├── Comments
├── Menus
├── SEO
├── Users
│   ├── List
│   ├── Create
│   └── Edit
├── Roles & Permissions
├── Activity Log
├── Settings
│   ├── General
│   ├── Branding
│   ├── SEO
│   ├── Social
│   └── Content
└── Profile
```

---

# 7. Sitemap

## 7.1 Public Website Sitemap

```text
Home
├── Blog / Posts
│   ├── Post Detail
│   ├── Category Archive
│   └── Tag Archive
├── Pages
│   └── Page Detail
├── Search
├── Contact / Company Pages
└── System Pages
    ├── 404
    └── 500
```

## 7.2 Administration Sitemap

```text
/login
/forgot-password
/reset-password

/admin
├── dashboard
├── posts
├── pages
├── categories
├── tags
├── media
├── comments
├── menus
├── seo
├── users
├── roles
├── activity
├── settings
└── profile
```

Actual URLs may differ but navigation semantics should remain consistent.

---

# 8. Dashboard Layout

The dashboard should summarize work, not attempt to become an analytics platform.

## 8.1 Recommended Desktop Layout

```text
Dashboard
Good afternoon, [Name].

┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ Published   │ │ Drafts      │ │ Scheduled   │ │ Comments    │
│ 124         │ │ 12          │ │ 4           │ │ 8 Pending   │
└─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘

Recent Content
────────────────────────────────────────────────────────────────────
Title                         Status        Author       Updated
...

Scheduled
────────────────────────────────────────────────────────────────────
Title                         Publish Time
...
```

## 8.2 Dashboard Rules

- maximum four summary metrics in MVP;
- no decorative charts;
- metrics are clickable where useful;
- show shortcuts relevant to user permission;
- show only data user can access;
- use empty-state guidance when no content exists.

## 8.3 Author Dashboard

Reduced version:

```text
My Drafts
My Recent Posts
Create Post
```

---

# 9. Module Navigation

## 9.1 Posts

Top-level tabs/filters may be:

```text
All
Draft
Scheduled
Published
Archived
```

Tabs represent status shortcuts, not separate pages.

## 9.2 Comments

```text
Pending
Approved
Spam
Rejected
```

## 9.3 Users

```text
All
Active
Inactive
```

## 9.4 Settings

Use left-side or top-tab subnavigation:

```text
General
Branding
SEO
Social
Content
```

Avoid long one-page settings screens.

---

# 10. Form Patterns

## 10.1 Standard Form Layout

```text
Page Header

Main Form Column                 Context Column
────────────────────            ──────────────────
Primary Fields                  Status
                                Author
                                Publish
                                Featured Image
                                Category/Tags

Expandable Sections
SEO
Advanced
```

## 10.2 Form Rules

- labels always visible;
- helper text only where needed;
- required fields clearly indicated;
- errors displayed directly under affected fields;
- preserve entered values on validation failure;
- avoid placeholder-only labels;
- one primary submit button;
- Save action text should match context.

Examples:

```text
Save Draft
Publish
Schedule
Save Changes
Create User
```

## 10.3 Post Form

Recommended order:

```text
Title
Slug
Excerpt
Content Editor

Featured Image
Category
Tags

Publishing
  Status
  Publish Date/Time

SEO
  SEO Title
  Meta Description
  Canonical URL
  Social Image
  Index / Noindex
```

## 10.4 Sticky Save Area

On long edit screens, a compact sticky action bar may contain:

```text
[Preview] [Save Draft] [Publish]
```

Only if it does not obscure content.

---

# 11. Table Patterns

Tables are used for dense operational records.

## 11.1 Standard Table

```text
[Search........................] [Filter]                  [Create]

☐ Title              Status       Author       Updated        Actions
──────────────────────────────────────────────────────────────────────
  Example Post       Draft        Andi         2 min ago      •••
```

## 11.2 Table Rules

- left-align text;
- use status badges sparingly;
- actions should be limited;
- primary row click opens detail/edit where appropriate;
- overflow menu for secondary/destructive actions;
- pagination required;
- never hide essential actions only on hover;
- table header remains simple.

## 11.3 Bulk Actions

MVP should avoid bulk actions unless a clear workflow requires them.

Future possible:

```text
Archive
Change status
Delete
```

Do not add bulk controls simply because tables normally have them.

---

# 12. Filter Patterns

Filters should support the exact product requirements.

## 12.1 Posts Filters

```text
Status
Author
Category
Publish Date Range
```

## 12.2 Comments Filters

```text
Status
Post
Date
```

## 12.3 Filter UI

Desktop:

```text
[Search] [Status ▼] [Author ▼] [Category ▼] [More Filters]
```

Mobile:

```text
[Search]
[Filters]
```

opens a filter drawer/sheet.

## 12.4 Filter Rules

- active filters must be visible;
- show clear Reset All;
- filter state should persist during pagination;
- do not apply surprising defaults;
- empty result state should indicate filters are active.

---

# 13. Search Patterns

## 13.1 Local Module Search

Primary search pattern:

```text
Search posts...
Search pages...
Search media...
```

Search should be contextual.

## 13.2 Search Feedback

While typing:

- use reasonable debounce for Livewire search;
- show lightweight loading feedback;
- keep prior results until replacement results arrive where possible.

## 13.3 No Results

Example:

```text
No posts found for "Laravel 13".

Try a different keyword or clear your filters.
```

## 13.4 Future Global Search

May search:

- Posts
- Pages
- Media

Do not include users/settings by default unless permission and use case justify it.

---

# 14. Modal Usage

Modals should be used selectively.

## Good Modal Use

- confirmation;
- small quick form;
- media selection;
- change password;
- quick status action.

## Bad Modal Use

Do not place:

- full post editor;
- complex user form;
- roles/permissions matrix;
- settings page;
- long-form content.

## Modal Rules

- title describes action;
- clear primary/secondary buttons;
- Esc closes non-destructive modal;
- focus moves into modal;
- focus returns to trigger after close;
- destructive confirmation uses explicit language.

---

# 15. Toast / Notification Behavior

Use toast messages for completed operations.

Examples:

```text
Post saved.
Post published.
Comment approved.
Settings updated.
```

## 15.1 Toast Rules

- appear in consistent top-right desktop location;
- mobile may use top-center/full-width compact placement;
- disappear automatically for success;
- errors remain long enough to read;
- never rely on color alone;
- do not stack excessive duplicate toasts.

## 15.2 Inline Errors

Validation errors should be inline, not toast-only.

Toast may summarize:

```text
Please fix 3 fields before saving.
```

---

# 16. Empty States

Every data-driven module needs an intentional empty state.

## 16.1 First-Use Empty State

Example Posts:

```text
No posts yet

Create your first article and start publishing content.

[Create Post]
```

## 16.2 Filtered Empty State

```text
No scheduled posts

There are no posts matching the current filters.

[Clear Filters]
```

## 16.3 Permission-Aware Empty State

Do not show a Create CTA if the user cannot create.

---

# 17. Loading States

Livewire interactions must visibly acknowledge latency.

## 17.1 Recommended Loading Patterns

- button spinner for save;
- skeleton rows for initial data load when needed;
- subtle table loading overlay during filter/search;
- disabled primary action during duplicate submission risk.

## 17.2 Rules

- avoid full-screen loaders for small updates;
- preserve layout size to reduce jumping;
- do not make user guess whether Save was triggered;
- prevent repeated destructive submissions.

---

# 18. Error States

Error design should distinguish user-correctable problems from system failures.

## 18.1 Validation Error

```text
Title is required.
```

Displayed under the relevant field.

## 18.2 Permission Error

```text
You do not have permission to perform this action.
```

## 18.3 Not Found

```text
This content could not be found.

It may have been removed or the URL may be incorrect.
```

## 18.4 Server Failure

```text
Something went wrong.

Your changes may not have been saved. Please try again.
```

Do not expose technical stack traces.

## 18.5 Upload Error

Examples:

```text
This file type is not supported.
The file exceeds the 10 MB limit.
Upload failed. Please try again.
```

---

# 19. Confirmation Dialogs

Confirmations are required for actions that are difficult to reverse or affect public behavior.

## Required Confirmation Examples

- Archive published post
- Delete media
- Deactivate user
- Remove permission
- Delete category with existing relationships
- Delete menu item if destructive
- Publish if product wants an explicit final step

## Dialog Pattern

```text
Archive this post?

The post will no longer appear in normal public listings.

[Cancel] [Archive Post]
```

Avoid generic:

```text
Are you sure?
```

---

# 20. Responsive Behavior

## 20.1 Breakpoint Strategy

Follow Tailwind's responsive model rather than creating a custom breakpoint system without need.

## 20.2 Desktop

- persistent sidebar;
- multi-column forms where useful;
- tables use normal table layout.

## 20.3 Tablet

- collapsible sidebar;
- two-column form may collapse partially;
- maintain visible primary actions.

## 20.4 Mobile

- off-canvas navigation;
- cards/list rows may replace wide tables;
- horizontal overflow is avoided for core workflows;
- form fields stack;
- top actions collapse into overflow where necessary.

## 20.5 Table Mobile Strategy

Priority columns become mobile rows:

```text
Post Title
Status · Author
Updated 10 minutes ago

[Edit] [More]
```

Do not force desktop table columns into tiny mobile widths.

---

# 21. Accessibility Requirements

ContentFlow should target WCAG 2.2 AA-aligned interaction quality for primary workflows.

## 21.1 Required Practices

- semantic headings;
- proper form labels;
- keyboard reachable controls;
- visible focus indicator;
- sufficient contrast;
- meaningful link/button text;
- `aria-*` only where native HTML is insufficient;
- error messages associated with fields;
- status not communicated through color alone;
- alt text support for content images;
- modal focus trapping;
- skip-to-content support for admin shell.

## 21.2 Status Badges

Use:

```text
● Draft
● Scheduled
● Published
● Archived
```

with text, not color-only dots.

## 21.3 Icon Buttons

Every icon-only button must have an accessible name.

---

# 22. Keyboard Navigation

## 22.1 Required Keyboard Support

Users must be able to:

- Tab through primary navigation;
- activate buttons with keyboard;
- navigate form fields;
- submit forms;
- close dialogs with Esc where safe;
- navigate modal controls;
- access table actions.

## 22.2 Optional Shortcuts

Future:

```text
C P   Create Post
/     Focus Search
G D   Go to Dashboard
G P   Go to Posts
```

Do not implement shortcuts in MVP unless discoverability and conflict handling are designed properly.

---

# 23. Design Consistency Rules

## 23.1 Typography

Use a neutral sans-serif.

Recommended hierarchy:

```text
Page Title      24–30px
Section Title   18–20px
Body            14–16px
Caption         12–14px
```

## 23.2 Color

Use one primary brand/accent color.

Neutral tones should dominate:

- background;
- borders;
- text;
- table surfaces.

Status colors are reserved for semantic states.

## 23.3 Spacing

Use Tailwind spacing scale consistently.

Avoid arbitrary per-page spacing.

## 23.4 Radius

Use one or two radius levels only.

Example:

```text
cards: rounded-lg
buttons/inputs: rounded-md
```

## 23.5 Shadows

Use subtle shadows only for:

- modal;
- dropdown;
- floating menu.

Do not put strong shadows on every card.

## 23.6 Iconography

Use one icon set consistently.

Do not mix icon styles.

---

# 24. CRUD Page Standards

All CRUD modules should follow one standard.

## 24.1 Index

```text
Title
Description

Search / Filters                         Create

Table / List

Pagination
```

## 24.2 Create

```text
Breadcrumb / Back
Create [Entity]

Form

[Cancel] [Create]
```

## 24.3 Edit

```text
Breadcrumb / Back
Edit [Entity]

Form

[Cancel] [Save Changes]
```

## 24.4 Delete

Prefer:
- archive/deactivate where lifecycle exists;
- explicit delete only when product behavior requires it.

## 24.5 Consistency

The primary action stays in the same visual area across modules.

---

# 25. Detail Page Standards

Not every entity needs a separate detail page.

## Use Detail Pages For

- Media
- User
- Audit Event if more metadata is needed

## Edit-First Modules

Posts and Pages may open directly into edit because editing is the dominant task.

## Detail Pattern

```text
Entity Name
Status / Metadata

Primary Information
Related Information
Activity / Metadata

Actions
```

Avoid duplicating information already available in Edit without clear benefit.

---

# 26. Settings Pages

Settings should use grouped navigation.

## 26.1 General

- Site Name
- Site Description
- Timezone
- Locale
- Contact Information

## 26.2 Branding

- Logo
- Favicon

## 26.3 SEO

- Default SEO Title
- Default Meta Description
- Default OG Image
- Default Index Setting

## 26.4 Social

- Social Links

## 26.5 Content

- Comments Enabled
- Other approved content-level defaults

## 26.6 Settings Rules

- Save per section;
- unsaved changes warning where practical;
- no "Save All Everything" mega form;
- dangerous/system-critical settings should be separated visually.

---

# 27. Profile Pages

Profile is user-owned account configuration.

## Sections

```text
Profile Information
  Name
  Email

Password
  Current Password
  New Password
  Confirm Password
```

Future optional:

```text
Avatar
UI preferences
Two-factor authentication
```

## Rules

- profile actions must not expose role-management controls;
- role assignment belongs to Users/Administration;
- password fields never prefill existing password.

---

# 28. Authentication Pages

Authentication should be visually simple.

## 28.1 Login

```text
ContentFlow

Welcome back

Email
Password

[Sign In]

Forgot password?
```

## 28.2 Forgot Password

```text
Reset your password

Email

[Send Reset Link]

Back to login
```

## 28.3 Reset Password

```text
Create a new password

New Password
Confirm Password

[Reset Password]
```

## 28.4 Auth Layout Rules

- centered narrow card or split layout;
- no marketing carousel;
- no excessive illustration;
- strong focus on task completion;
- consistent brand identity.

---

# 29. Public Pages

The public website should remain themeable without changing CMS administration patterns.

## 29.1 Core Public Templates

### Home

May include:

- site hero/title;
- recent posts;
- selected categories;
- static page blocks.

Do not force a visual page builder.

### Blog Index

```text
Page Title
Optional intro
Search / category navigation
Post list/grid
Pagination
```

### Post Detail

```text
Title
Author / Publish Date
Featured Image
Content
Tags
Comments
```

### Page Detail

```text
Title
Content
```

### Category / Tag

```text
Taxonomy Name
Description
Matching Posts
Pagination
```

### Public Search

```text
Search Query
Result Count
Results
Pagination
```

## 29.2 Public Design Rules

- readable typography;
- strong content hierarchy;
- mobile-first;
- SEO-friendly structure;
- no admin UI visual leakage;
- theme can evolve independently of admin shell.

---

# 30. Demo Mode Considerations

Commercial source-code products often benefit from a public demo.

Demo mode must protect the application from destructive misuse.

## 30.1 Demo Mode Goals

Allow prospective buyers to evaluate:

- dashboard;
- content editing;
- navigation;
- media;
- comments;
- settings UI.

## 30.2 Restricted Demo Actions

Recommended restrictions:

- no password changes;
- no Super Admin deletion/deactivation;
- no destructive role/permission removal;
- no permanent media deletion;
- no changing mail/server-sensitive settings;
- no destructive database actions.

## 30.3 Demo Banner

Show a subtle banner:

```text
Demo Mode
Some destructive actions are disabled.
```

Do not show repeated warning popups.

## 30.4 Demo Data Reset

A demo environment may periodically reset seeded content outside the user-facing application workflow.

This operational reset is separate from production product behavior.

## 30.5 Demo Account Roles

Potential demo accounts:

```text
Editor Demo
Administrator Demo
```

Avoid publishing real credentials in production documentation outside the dedicated demo environment.

---

# 31. Status Visual System

A consistent status language is required across the CMS.

## Posts

```text
Draft
Scheduled
Published
Archived
```

## Comments

```text
Pending
Approved
Spam
Rejected
```

## Users

```text
Active
Inactive
```

## Rules

- use badge + text;
- use the same label everywhere;
- do not rename `Scheduled` to `Queue` on another screen;
- do not use ambiguous colors without labels.

---

# 32. Primary Action Hierarchy

Use one strong primary action.

Example Post Edit:

```text
Primary: Publish
Secondary: Save Draft
Tertiary: Preview
Overflow: Archive
```

For an existing Published post:

```text
Primary: Save Changes
Secondary: Preview
Overflow: Archive / Return to Draft
```

Do not show Publish, Schedule, Save Draft, Preview, Delete, Duplicate, Archive, SEO, and More all as equal buttons.

---

# 33. Livewire Interaction Guidelines

Because ContentFlow uses Livewire:

## Use Livewire For

- search;
- filters;
- pagination;
- validation feedback;
- modal state;
- media selection;
- comment moderation;
- table refresh;
- settings saves;
- post status actions.

## Avoid Overusing Livewire For

- purely static content;
- interaction that can remain standard Blade/link behavior.

## UX Rules

- every network-triggering control has loading feedback;
- disable duplicate save action while request is pending;
- maintain scroll/focus context after small updates;
- avoid large page flashes;
- preserve filter state.

---

# 34. Alpine.js Interaction Guidelines

Use Alpine.js only for light client-side behavior.

Appropriate:

- dropdown;
- sidebar drawer;
- collapsible section;
- tabs;
- modal visibility where Livewire state is unnecessary;
- copy-to-clipboard;
- local UI state.

Do not duplicate application data/business state in Alpine when Livewire/Laravel is authoritative.

---

# 35. Tailwind CSS Usage Rules

- create reusable Blade/Livewire components for recurring UI patterns;
- avoid copying long utility strings across dozens of screens;
- use design tokens/theme configuration;
- avoid arbitrary values unless justified;
- establish consistent button/input/card/table styles.

Recommended reusable components:

```text
Button
Input
Textarea
Select
Checkbox
Badge
Alert
Modal
Dropdown
Table
Pagination
EmptyState
PageHeader
FormSection
Toast
Tabs
SidebarItem
```

---

# 36. Recommended Screen Inventory

## Authentication
- Login
- Forgot Password
- Reset Password

## Dashboard
- Dashboard

## Posts
- Post List
- Create Post
- Edit Post
- Preview Post

## Pages
- Page List
- Create Page
- Edit Page
- Preview Page

## Taxonomy
- Categories
- Tags

## Media
- Media Library
- Media Detail / Edit Metadata

## Comments
- Comment Moderation List

## Navigation
- Menu List / Menu Builder

## SEO
- Global SEO Settings

## Administration
- User List
- Create User
- Edit User
- Roles & Permissions
- Activity Log
- Settings

## Account
- Profile
- Password

## Public
- Home
- Blog Index
- Post Detail
- Page Detail
- Category
- Tag
- Search
- 404
- 500

---

# 37. UX Acceptance Checklist

Before a screen is considered UX-complete:

- [ ] page purpose is obvious;
- [ ] primary action is obvious;
- [ ] permission-aware actions are correct;
- [ ] empty state exists;
- [ ] loading state exists where asynchronous;
- [ ] validation state exists;
- [ ] system error state exists;
- [ ] destructive action has appropriate confirmation;
- [ ] keyboard navigation works;
- [ ] focus state is visible;
- [ ] mobile/tablet layout is usable;
- [ ] labels and status terms match product vocabulary;
- [ ] no unnecessary visual component was added;
- [ ] page follows shared CRUD/layout pattern;
- [ ] Livewire interaction provides feedback;
- [ ] user cannot mistake an unsaved operation for success.

---

# 38. Final UI/UX Direction

ContentFlow CMS should visually communicate:

```text
Simple
Professional
Editorial
Reliable
Focused
Modern
```

The intended admin experience is closer to a calm modern SaaS workspace than a traditional overloaded CMS dashboard.

The design should consistently prioritize:

```text
Content first
Clear hierarchy
Minimal navigation depth
Predictable actions
Permission-aware UI
Strong feedback
Safe destructive flows
Responsive usability
Accessible interaction
```

The product should never add visual complexity merely to appear "feature rich."

The preferred experience is:

> Open ContentFlow, immediately understand what needs attention, create or manage content with minimal friction, and complete publishing tasks without navigating through unnecessary interface layers.

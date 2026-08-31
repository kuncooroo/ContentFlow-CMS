# Product Requirement Document (PRD)
## ContentFlow CMS

---

## 1. Document Information

| Field | Value |
|---|---|
| Document | Product Requirement Document |
| Product Name | ContentFlow CMS |
| Product Type | Web-based Content Management System |
| Business Domain | Content Management System (CMS) / Blogging / Digital Publishing |
| Target Platform | Web |
| Primary Technology Constraint | Laravel + MySQL |
| Document Status | Draft for Product Development |
| Initial Release | MVP / Version 1.0 |
| Primary Audience | Product Manager, UI/UX Designer, Software Architect, Developer, QA, Product Owner |
| Commercial Direction | Source-code product first, with future SaaS potential |
| Language | Indonesian |
| Last Updated | 2026-08-29 |

### 1.1 Purpose of This Document

Dokumen ini mendefinisikan kebutuhan produk ContentFlow CMS dari sudut pandang perilaku produk, pengguna, proses bisnis, kualitas, keamanan, dan acceptance criteria.

Dokumen ini tidak mendefinisikan detail implementasi kode, framework internals, struktur tabel database, atau keputusan teknis tingkat rendah.

---

## 2. Product Overview

ContentFlow CMS adalah aplikasi web untuk membuat, mengelola, menjadwalkan, meninjau, dan mempublikasikan konten website melalui dashboard admin yang terpusat.

Produk ditujukan untuk:

- blog pribadi;
- portal informasi;
- website perusahaan;
- website organisasi;
- website institusi pendidikan;
- website publikasi;
- website klien yang dikelola freelancer atau digital agency.

Produk harus menyediakan pengalaman penggunaan yang sederhana bagi pengguna nonteknis sekaligus memberikan struktur pengelolaan konten yang cukup profesional untuk tim editorial.

Fungsi inti meliputi:

- authentication;
- user management;
- roles and permissions;
- posts;
- pages;
- categories;
- tags;
- media library;
- comments;
- navigation menus;
- SEO;
- site settings;
- dashboard;
- search and filtering;
- activity tracking dasar;
- publishing workflow.

---

## 3. Background

Pengelolaan konten website sering dilakukan melalui salah satu dari dua pendekatan:

1. menggunakan CMS yang sangat fleksibel tetapi dapat menjadi terlalu kompleks untuk kebutuhan sederhana; atau
2. membangun website custom yang menyebabkan fitur CMS dasar seperti user management, posts, media, SEO, categories, tags, dan publishing workflow dibuat ulang pada setiap proyek.

Kondisi tersebut menimbulkan masalah berupa:

- ketergantungan terhadap developer;
- proses publikasi lambat;
- UX admin yang tidak konsisten;
- pengelolaan SEO yang tersebar;
- kesulitan mengelola media;
- tidak adanya pembagian peran yang jelas;
- risiko kesalahan publikasi;
- tingginya biaya pengembangan ulang fitur CMS dasar.

ContentFlow CMS dirancang sebagai CMS yang opinionated, sederhana, modern, dan dapat digunakan secara berulang untuk berbagai kebutuhan website berbasis konten.

---

## 4. Problem Statement

Target pengguna membutuhkan sistem yang memungkinkan pengelolaan konten website secara mandiri tanpa harus bergantung pada developer untuk pekerjaan editorial sehari-hari.

Masalah utama yang harus diselesaikan:

1. pengguna tidak memiliki satu dashboard terpusat untuk mengelola konten;
2. pembuatan dan perubahan konten sering membutuhkan bantuan teknis;
3. draft dan konten terjadwal tidak mudah dipantau;
4. aset media tersebar dan sulit dicari kembali;
5. pengaturan SEO per konten sering tidak konsisten;
6. tanggung jawab editor, author, dan administrator tidak terpisah dengan jelas;
7. komentar membutuhkan moderasi;
8. struktur navigasi website sulit diubah tanpa developer;
9. pengguna membutuhkan pengalaman CMS yang cukup sederhana tetapi tetap mendukung operasional website profesional.

---

## 5. Product Vision

> Menjadi CMS berbasis Laravel yang sederhana, modern, editorial-first, dan mudah digunakan untuk membangun serta mengelola website berbasis konten tanpa kompleksitas yang tidak diperlukan.

ContentFlow CMS harus dikenal karena:

- clean user experience;
- publishing workflow yang jelas;
- pengelolaan konten yang cepat;
- konfigurasi yang mudah;
- hak akses yang terkontrol;
- pengalaman yang sesuai untuk blogger, perusahaan, dan agency;
- maintainability sebagai produk komersial yang dapat dijual berulang.

---

## 6. Product Objectives

### OBJ-001 — Independent Content Management

Pengguna berwenang harus dapat membuat, mengubah, menjadwalkan, dan mempublikasikan konten tanpa bantuan developer.

**Success condition:** seluruh operasi editorial utama tersedia melalui UI admin.

### OBJ-002 — Centralized Content Operations

Semua operasi konten utama harus dapat diakses melalui satu dashboard administrasi.

**Success condition:** post, page, media, comments, taxonomy, menus, users, SEO, dan settings dapat dikelola dari area admin.

### OBJ-003 — Clear Publishing Workflow

Status konten harus mudah dipahami dan dikontrol.

**Success condition:** post memiliki minimal status Draft, Scheduled, Published, dan Archived.

### OBJ-004 — Controlled Access

Setiap role hanya dapat melakukan tindakan sesuai permission yang diberikan.

**Success condition:** tindakan yang tidak diizinkan harus ditolak dan tidak muncul sebagai action yang dapat dijalankan.

### OBJ-005 — Commercial Reusability

Produk harus memiliki feature set yang cukup generik untuk digunakan pada lebih dari satu jenis website konten.

**Success condition:** produk tidak bergantung pada satu industri, organisasi, atau struktur konten spesifik.

### OBJ-006 — MVP Simplicity

Versi MVP harus berfokus pada CMS inti dan tidak berubah menjadi page builder, e-commerce platform, CRM, atau marketing automation platform.

---

## 7. Success Metrics

Metrics berikut digunakan sebagai target awal produk.

| ID | Metric | Target MVP |
|---|---|---:|
| MET-001 | First successful article publication after setup | ≤ 10 menit untuk user yang sudah login dan memahami konteks dasar CMS |
| MET-002 | Successful post creation flow | ≥ 95% pada acceptance testing |
| MET-003 | Successful scheduled publishing flow | 100% untuk test case yang valid |
| MET-004 | Unauthorized action prevention | 100% pada permission test |
| MET-005 | CRUD completion for core modules | 100% acceptance criteria |
| MET-006 | Public page availability after publish | 100% untuk content valid |
| MET-007 | Admin search success for known content | ≥ 95% pada test dataset |
| MET-008 | Broken core navigation links | 0 pada release candidate |
| MET-009 | Critical security defects | 0 pada production release |
| MET-010 | Critical data-loss defects | 0 pada production release |
| MET-011 | Responsive usability on supported viewports | 100% pada QA checklist |
| MET-012 | Accessibility for primary admin actions | Semua action utama dapat dikenali, difokuskan, dan dijalankan tanpa ambiguity |

---

## 8. Target Users

### Primary Users

- Blogger
- Content Writer
- Editor
- Website Administrator
- Digital Marketing Team
- Company Content Administrator
- Organization Administrator
- Media / Publication Manager

### Secondary Users

- Freelancer
- Web Developer
- Digital Agency
- Small Business Owner
- Startup Team
- Educational Institution Administrator

---

## 9. User Personas

### 9.1 Blogger — "Independent Publisher"

**Goal:** menulis dan mempublikasikan artikel dengan cepat.

**Needs:**
- editor konten;
- kategori dan tag;
- featured image;
- SEO;
- scheduling;
- comment moderation;
- simple dashboard.

**Frustrations:**
- CMS terlalu penuh fitur;
- terlalu banyak konfigurasi;
- workflow publikasi tidak jelas.

### 9.2 Content Writer — "Author"

**Goal:** membuat draft berkualitas tanpa mengubah area administrasi lain.

**Needs:**
- create/edit own posts;
- upload media;
- save drafts;
- preview content;
- submit content untuk dipublikasikan oleh user berwenang.

### 9.3 Editor — "Publishing Gatekeeper"

**Goal:** menjaga kualitas dan konsistensi publikasi.

**Needs:**
- melihat draft;
- mengubah artikel;
- menjadwalkan atau mempublikasikan;
- mengelola taxonomy;
- moderasi komentar.

### 9.4 Administrator — "Site Operator"

**Goal:** mengelola operasional website secara menyeluruh.

**Needs:**
- users;
- roles;
- permissions;
- menus;
- settings;
- content;
- SEO;
- media;
- comments.

### 9.5 Digital Agency — "Reusable Site Builder"

**Goal:** menggunakan ContentFlow sebagai basis website klien.

**Needs:**
- predictable CMS behavior;
- konfigurasi branding;
- reusable core;
- role management;
- clear content structure;
- easy handover ke client.

---

## 10. User Pain Points

| ID | Pain Point | Product Response |
|---|---|---|
| PAIN-001 | Bergantung pada developer untuk update konten | Self-service CMS admin |
| PAIN-002 | Sulit menemukan draft atau scheduled content | Dashboard dan filtering berdasarkan status |
| PAIN-003 | Pengelolaan gambar tidak terpusat | Media Library |
| PAIN-004 | SEO tidak konsisten | SEO fields per content + global defaults |
| PAIN-005 | Hak akses terlalu luas | Role and permission control |
| PAIN-006 | Komentar spam atau tidak relevan | Moderation states |
| PAIN-007 | Navigasi sulit diubah | Menu Manager |
| PAIN-008 | Sulit mencari artikel lama | Search + filters |
| PAIN-009 | Risiko publish tidak sengaja | Explicit publishing status and confirmation behavior |
| PAIN-010 | CMS terlalu kompleks | Opinionated MVP scope dan simple information architecture |

---

## 11. User Roles

MVP harus memiliki role default berikut.

### 11.1 Super Admin

Memiliki akses penuh ke seluruh sistem.

### 11.2 Administrator

Mengelola operasional website termasuk content, users, menus, comments, SEO, dan settings sesuai permission.

### 11.3 Editor

Mengelola konten editorial dan komentar, termasuk publish apabila permission diberikan.

### 11.4 Author

Membuat dan mengelola konten miliknya sendiri, tanpa akses administrasi global.

### 11.5 Public Visitor

Mengakses halaman publik dan, apabila komentar diaktifkan, dapat mengirim komentar sesuai kebijakan website.

Role default harus dapat dibedakan melalui permission.

---

## 12. Product Scope

### In Scope

- user authentication;
- user management;
- roles;
- permissions;
- posts;
- pages;
- categories;
- tags;
- media library;
- comments;
- navigation menu management;
- SEO metadata;
- site settings;
- dashboard;
- basic search;
- filters;
- basic audit trail;
- basic notifications;
- public content rendering;
- scheduled publishing;
- content archive state;
- basic localization readiness.

---

## 13. MVP Scope

MVP ContentFlow CMS harus berisi:

1. Authentication
2. Users
3. Roles & Permissions
4. Posts
5. Pages
6. Categories
7. Tags
8. Media Library
9. Comments
10. Menu Manager
11. SEO
12. Site Settings
13. Dashboard
14. Search & Filters
15. Basic Activity/Audit Trail
16. Scheduled Publishing
17. Public Website Content Delivery

### MVP Principle

Sebuah fitur hanya termasuk MVP apabila secara langsung mendukung:

- pembuatan konten;
- pengorganisasian konten;
- publikasi;
- discovery;
- moderation;
- akses;
- konfigurasi dasar website.

---

## 14. Out of Scope

Fitur berikut tidak termasuk MVP:

- multi-tenancy;
- SaaS subscription billing;
- e-commerce;
- shopping cart;
- payment gateway;
- marketplace;
- plugin marketplace;
- theme marketplace;
- visual drag-and-drop page builder;
- CRM;
- email marketing platform;
- newsletter campaign engine;
- paid membership;
- paywall;
- advertising management;
- native mobile application;
- AI content generation;
- multi-site;
- advanced approval workflow;
- complex custom content-type builder;
- GraphQL;
- advanced analytics platform;
- multilingual content editing;
- real-time collaborative editing;
- social network features.

Fitur out-of-scope tidak boleh menunda rilis MVP.

---

## 15. Functional Requirements

### 15.1 Authentication

**FR-AUTH-001**  
Sistem harus menyediakan halaman login yang menerima credential pengguna.

**Acceptance test:** user dengan credential valid berhasil masuk; credential tidak valid ditolak.

**FR-AUTH-002**  
Sistem harus menyediakan fungsi logout.

**Acceptance test:** setelah logout, user tidak dapat mengakses halaman terproteksi tanpa login kembali.

**FR-AUTH-003**  
Sistem harus menyediakan forgot password dan reset password.

**Acceptance test:** user yang memiliki akun aktif dapat memulai proses reset password dan menggunakan password baru setelah proses berhasil.

**FR-AUTH-004**  
Akun nonaktif tidak boleh dapat login.

### 15.2 User Management

**FR-USR-001**  
User berwenang harus dapat melihat daftar pengguna.

**FR-USR-002**  
User berwenang harus dapat membuat pengguna baru dengan nama, email, role, dan status.

**FR-USR-003**  
User berwenang harus dapat mengubah data pengguna.

**FR-USR-004**  
User berwenang harus dapat mengaktifkan atau menonaktifkan pengguna.

**FR-USR-005**  
Email user harus unik.

**FR-USR-006**  
Sistem tidak boleh mengizinkan penghapusan atau perubahan yang menyebabkan tidak adanya akun Super Admin aktif, apabila hanya terdapat satu Super Admin.

### 15.3 Roles and Permissions

**FR-RBAC-001**  
Setiap user harus memiliki minimal satu role aktif.

**FR-RBAC-002**  
Role harus menentukan sekumpulan permission.

**FR-RBAC-003**  
Sistem harus memeriksa permission sebelum menjalankan action terproteksi.

**FR-RBAC-004**  
UI tidak boleh menampilkan action terproteksi sebagai action aktif kepada user tanpa permission.

**FR-RBAC-005**  
Akses langsung ke URL/action terproteksi tetap harus ditolak meskipun user mencoba melewati UI.

### 15.4 Posts

**FR-POST-001**  
User berwenang harus dapat membuat post.

**FR-POST-002**  
Post minimal memiliki:
- title;
- slug;
- content;
- status;
- author;
- publish date;
- created date;
- updated date.

**FR-POST-003**  
Post dapat memiliki:
- excerpt;
- featured image;
- category;
- multiple tags;
- SEO metadata.

**FR-POST-004**  
Status post MVP harus mencakup:
- Draft;
- Scheduled;
- Published;
- Archived.

**FR-POST-005**  
Post Draft tidak boleh tampil pada public website.

**FR-POST-006**  
Post Scheduled tidak boleh tampil sebelum waktu publish.

**FR-POST-007**  
Post Published harus tampil pada public website jika tidak memiliki kondisi lain yang menghalangi publikasi.

**FR-POST-008**  
Post Archived tidak boleh tampil dalam listing publik normal.

**FR-POST-009**  
User dengan permission publish harus dapat mempublikasikan post.

**FR-POST-010**  
User dengan permission schedule harus dapat menjadwalkan post pada waktu di masa depan.

**FR-POST-011**  
Sistem harus menolak tanggal scheduled publish yang tidak valid.

**FR-POST-012**  
Slug post harus unik dalam ruang URL post yang sama.

**FR-POST-013**  
Sistem harus menyediakan preview sebelum publish.

### 15.5 Pages

**FR-PAGE-001**  
User berwenang harus dapat membuat, membaca, mengubah, dan mengarsipkan page.

**FR-PAGE-002**  
Page minimal memiliki title, slug, content, status, dan SEO metadata.

**FR-PAGE-003**  
Page Published harus dapat diakses melalui URL publik.

**FR-PAGE-004**  
Page Draft tidak boleh dapat ditemukan oleh visitor normal.

### 15.6 Categories

**FR-CAT-001**  
User berwenang harus dapat membuat, mengubah, dan menghapus category jika category tidak sedang dilindungi oleh aturan bisnis.

**FR-CAT-002**  
Category minimal memiliki name dan slug.

**FR-CAT-003**  
Category slug harus unik.

**FR-CAT-004**  
Sistem harus mencegah kehilangan relasi konten secara diam-diam ketika category dihapus.

### 15.7 Tags

**FR-TAG-001**  
User berwenang harus dapat membuat, mengubah, dan menghapus tag.

**FR-TAG-002**  
Satu post dapat memiliki nol atau lebih tag.

**FR-TAG-003**  
Tag slug harus unik.

### 15.8 Media Library

**FR-MEDIA-001**  
User berwenang harus dapat mengunggah file yang memenuhi aturan file upload.

**FR-MEDIA-002**  
User harus dapat melihat media dalam media library.

**FR-MEDIA-003**  
User harus dapat mencari media berdasarkan nama file atau metadata yang didukung.

**FR-MEDIA-004**  
Gambar harus dapat memiliki alt text.

**FR-MEDIA-005**  
User berwenang harus dapat memilih media sebagai featured image.

**FR-MEDIA-006**  
Penghapusan media yang masih digunakan harus menampilkan warning dan tidak boleh menyebabkan broken content secara diam-diam.

### 15.9 Comments

**FR-COM-001**  
Apabila komentar diaktifkan, visitor harus dapat mengirim komentar pada content yang mengizinkan komentar.

**FR-COM-002**  
Komentar baru harus masuk ke status Pending secara default, kecuali site setting menyatakan aturan lain yang diizinkan MVP.

**FR-COM-003**  
Status komentar harus mencakup:
- Pending;
- Approved;
- Spam;
- Rejected.

**FR-COM-004**  
Hanya komentar Approved yang boleh tampil untuk visitor.

**FR-COM-005**  
User berwenang harus dapat mengubah status komentar.

### 15.10 Menu Manager

**FR-MENU-001**  
User berwenang harus dapat membuat minimal satu menu navigasi.

**FR-MENU-002**  
Menu item dapat mengarah ke page, post, category, atau custom URL.

**FR-MENU-003**  
User harus dapat mengubah urutan menu item.

**FR-MENU-004**  
Perubahan menu yang disimpan harus tercermin pada area publik yang menggunakan menu tersebut.

### 15.11 SEO

**FR-SEO-001**  
Post dan page harus dapat memiliki SEO title.

**FR-SEO-002**  
Post dan page harus dapat memiliki meta description.

**FR-SEO-003**  
Post dan page harus dapat memiliki Open Graph image.

**FR-SEO-004**  
Post dan page harus mendukung index/noindex control.

**FR-SEO-005**  
Sistem harus memiliki global default untuk site title, site description, dan default social image.

**FR-SEO-006**  
Apabila field SEO spesifik kosong, sistem harus menggunakan nilai fallback yang dapat diprediksi dari content/global settings.

### 15.12 Site Settings

**FR-SET-001**  
User berwenang harus dapat mengubah:
- site name;
- site description;
- logo;
- favicon;
- contact information;
- default SEO settings;
- social links;
- basic content settings.

**FR-SET-002**  
Perubahan yang berhasil disimpan harus digunakan oleh bagian website terkait.

### 15.13 Dashboard

**FR-DASH-001**  
Dashboard harus menampilkan ringkasan jumlah Published, Draft, Scheduled, dan Pending Comments.

**FR-DASH-002**  
Dashboard harus menampilkan recent content.

**FR-DASH-003**  
Dashboard harus menampilkan scheduled content terdekat.

**FR-DASH-004**  
Dashboard hanya boleh menampilkan informasi yang boleh dilihat user berdasarkan permission.

---

## 16. Non-Functional Requirements

### NFR-001 — Usability

User dengan role Editor harus dapat menemukan action create post dari dashboard dalam maksimal 3 langkah navigasi.

### NFR-002 — Responsiveness

Semua fitur administrasi utama harus dapat digunakan pada viewport desktop dan tablet yang didukung produk.

### NFR-003 — Consistency

Action dengan makna yang sama harus menggunakan label dan behavior yang konsisten antar modul.

### NFR-004 — Reliability

Operasi create/update/delete tidak boleh dianggap berhasil oleh UI apabila data tidak tersimpan.

### NFR-005 — Data Integrity

Sistem tidak boleh meninggalkan data relasional inti dalam kondisi invalid setelah operasi yang berhasil.

### NFR-006 — Accessibility

Form utama harus memiliki label yang dapat dipahami dan error message harus terkait dengan field yang bermasalah.

### NFR-007 — Maintainable Product Behavior

Fitur MVP harus memiliki perilaku terdefinisi dan acceptance criteria yang dapat dites tanpa bergantung pada pengetahuan developer tertentu.

---

## 17. User Stories

### Content Creation

**US-001**  
Sebagai Author, saya ingin membuat draft post agar dapat menyiapkan artikel tanpa langsung mempublikasikannya.

**Acceptance criteria:**
- Author dapat membuka create post.
- Author dapat menyimpan title dan content.
- Status dapat disimpan sebagai Draft.
- Draft tidak tampil di public website.

### Scheduling

**US-002**  
Sebagai Editor, saya ingin menjadwalkan post agar konten dapat terbit otomatis pada waktu yang ditentukan.

**Acceptance criteria:**
- Editor dapat memilih Scheduled.
- Editor wajib menentukan waktu publish di masa depan.
- Post belum tampil sebelum waktu tersebut.
- Post tampil setelah kondisi publish terpenuhi.

### Media

**US-003**  
Sebagai Content Writer, saya ingin memilih gambar dari Media Library agar aset dapat digunakan kembali.

### Moderation

**US-004**  
Sebagai Editor, saya ingin menyetujui komentar agar hanya komentar yang layak tampil.

### SEO

**US-005**  
Sebagai Digital Marketing user, saya ingin mengatur metadata SEO agar setiap halaman dapat memiliki informasi pencarian yang sesuai.

### Access Control

**US-006**  
Sebagai Administrator, saya ingin membatasi Author agar hanya mengelola post miliknya sendiri.

### Site Navigation

**US-007**  
Sebagai Administrator, saya ingin mengubah menu website tanpa bantuan developer.

### Search

**US-008**  
Sebagai Editor, saya ingin mencari post berdasarkan keyword agar dapat menemukan konten lama dengan cepat.

---

## 18. User Journeys

### 18.1 Create and Publish Post

```text
Login
→ Dashboard
→ Posts
→ Create Post
→ Add Title
→ Add Content
→ Select Category/Tags
→ Select Featured Image
→ Configure SEO
→ Preview
→ Publish
→ Public Content Available
```

**Journey success:** post Published dapat diakses dari public website.

### 18.2 Schedule Post

```text
Login
→ Create/Edit Post
→ Select Scheduled
→ Choose Future Date/Time
→ Save
→ Post Listed as Scheduled
→ Publish Time Reached
→ Post Becomes Public
```

### 18.3 Moderate Comment

```text
Login
→ Dashboard / Comments
→ Open Pending Comment
→ Review
→ Approve / Reject / Spam
→ Save
→ Public Visibility Updated
```

### 18.4 Manage Navigation

```text
Login
→ Menus
→ Select Menu
→ Add/Edit/Reorder Items
→ Save
→ Verify Public Navigation
```

---

## 19. Business Rules

**BR-001**  
Content hanya dianggap publik apabila memenuhi status publik dan aturan waktu publikasi.

**BR-002**  
Draft tidak boleh ditampilkan kepada public visitor melalui listing, search normal, atau URL normal.

**BR-003**  
Scheduled content hanya menjadi publik setelah waktu publish tercapai.

**BR-004**  
Archived content tidak tampil pada listing publik normal.

**BR-005**  
Slug yang digunakan untuk public URL harus unik dalam namespace yang relevan.

**BR-006**  
User nonaktif tidak dapat login.

**BR-007**  
Author tidak boleh mengubah content milik user lain kecuali diberikan permission terkait.

**BR-008**  
Hanya comment Approved yang ditampilkan untuk public visitor.

**BR-009**  
System settings hanya boleh diubah oleh user dengan permission settings.

**BR-010**  
Penghapusan object yang masih direferensikan harus ditolak atau memerlukan tindakan eksplisit yang mencegah data rusak.

**BR-011**  
Perubahan permission harus berlaku pada request berikutnya setelah perubahan disimpan.

**BR-012**  
Aksi sensitif harus dapat dicatat pada audit trail sesuai cakupan MVP.

---

## 20. Permissions Matrix

Legend:

- ✅ Allowed by default
- ⚠️ Conditional / own content only
- ❌ Not allowed by default

| Capability | Super Admin | Administrator | Editor | Author |
|---|:---:|:---:|:---:|:---:|
| View dashboard | ✅ | ✅ | ✅ | ✅ |
| Manage users | ✅ | ✅ | ❌ | ❌ |
| Manage roles | ✅ | ⚠️ | ❌ | ❌ |
| Manage permissions | ✅ | ⚠️ | ❌ | ❌ |
| Create post | ✅ | ✅ | ✅ | ✅ |
| Edit own post | ✅ | ✅ | ✅ | ✅ |
| Edit others' post | ✅ | ✅ | ✅ | ❌ |
| Delete own post | ✅ | ✅ | ✅ | ⚠️ |
| Delete others' post | ✅ | ✅ | ⚠️ | ❌ |
| Publish post | ✅ | ✅ | ✅ | ❌ |
| Schedule post | ✅ | ✅ | ✅ | ❌ |
| Manage pages | ✅ | ✅ | ✅ | ❌ |
| Manage categories | ✅ | ✅ | ✅ | ❌ |
| Manage tags | ✅ | ✅ | ✅ | ⚠️ |
| Upload media | ✅ | ✅ | ✅ | ✅ |
| Delete media | ✅ | ✅ | ⚠️ | ⚠️ |
| Moderate comments | ✅ | ✅ | ✅ | ❌ |
| Manage menus | ✅ | ✅ | ❌ | ❌ |
| Manage SEO settings | ✅ | ✅ | ⚠️ | ❌ |
| Manage site settings | ✅ | ✅ | ❌ | ❌ |
| View audit trail | ✅ | ✅ | ⚠️ | ❌ |

Matrix default dapat dikonfigurasi melalui permission selama tetap memenuhi business rules produk.

---

## 21. Main Modules

### 21.1 Authentication
Login, logout, reset password.

### 21.2 User Management
Users, status, role assignment.

### 21.3 Access Control
Roles and permissions.

### 21.4 Content
Posts and Pages.

### 21.5 Taxonomy
Categories and Tags.

### 21.6 Media
Asset upload, browsing, metadata, reuse.

### 21.7 Comments
Submission and moderation.

### 21.8 Navigation
Menu management.

### 21.9 SEO
Per-content and global metadata.

### 21.10 Settings
General site configuration.

### 21.11 Dashboard
Operational overview.

### 21.12 Audit Trail
Record of selected administrative actions.

---

## 22. Dashboard Requirements

**DASH-001**  
Dashboard harus menampilkan:
- Published Posts count;
- Draft Posts count;
- Scheduled Posts count;
- Pending Comments count.

**DASH-002**  
Dashboard harus menampilkan maksimal sejumlah recent content yang ditentukan produk, dengan link ke detail/edit.

**DASH-003**  
Dashboard harus menampilkan scheduled content yang paling dekat waktu publish-nya.

**DASH-004**  
Dashboard harus memiliki shortcut minimal ke Create Post dan Media Library untuk user yang memiliki permission.

**DASH-005**  
Widget yang tidak relevan terhadap permission user tidak boleh ditampilkan.

**DASH-006**  
Dashboard MVP tidak memerlukan chart dekoratif atau analytics kompleks.

---

## 23. Reporting Requirements

Reporting MVP harus bersifat operasional, bukan business intelligence.

**REP-001**  
User berwenang harus dapat melihat total content berdasarkan status.

**REP-002**  
User berwenang harus dapat memfilter post berdasarkan status, author, category, dan periode.

**REP-003**  
User berwenang harus dapat melihat jumlah pending comments.

**REP-004**  
MVP tidak wajib menyediakan traffic analytics, conversion analytics, revenue analytics, atau attribution reports.

---

## 24. Notification Requirements

### In-App Notifications

**NOTIF-001**  
Sistem harus memberikan feedback keberhasilan setelah operasi create/update/delete/moderation yang berhasil.

**NOTIF-002**  
Sistem harus memberikan feedback gagal ketika operasi tidak dapat diselesaikan.

**NOTIF-003**  
Pesan error tidak boleh menyatakan operasi berhasil apabila data tidak tersimpan.

### Email Notifications

MVP email notification bersifat minimal.

**NOTIF-004**  
Reset password harus dapat menggunakan email notification.

**NOTIF-005**  
Notification editorial kompleks seperti approval chain tidak termasuk MVP.

---

## 25. Search and Filtering Requirements

**SEARCH-001**  
Post list harus dapat dicari minimal berdasarkan title.

**SEARCH-002**  
Post list harus dapat difilter berdasarkan:
- status;
- author;
- category;
- published date range jika tersedia pada MVP.

**SEARCH-003**  
Page list harus dapat dicari berdasarkan title.

**SEARCH-004**  
Media Library harus dapat dicari berdasarkan filename atau metadata utama.

**SEARCH-005**  
Comments harus dapat difilter berdasarkan moderation status.

**SEARCH-006**  
Filter aktif harus terlihat oleh user.

**SEARCH-007**  
User harus dapat menghapus/reset filter.

**SEARCH-008**  
Empty result harus menampilkan empty-state yang jelas, bukan error.

---

## 26. Import/Export Requirements

MVP hanya memerlukan kebutuhan minimal.

**IMP-001**  
Bulk import content dari CMS eksternal tidak wajib pada MVP.

**EXP-001**  
User berwenang harus dapat mengekspor data operasional dasar apabila fitur export disertakan pada release MVP.

**EXP-002**  
Apabila export belum disertakan pada MVP final, product documentation harus menyatakannya secara eksplisit.

### Future Direction

Future version dapat menyediakan:

- content export;
- media export;
- WordPress import;
- CSV import/export;
- portable backup package.

---

## 27. File Upload Requirements

**FILE-001**  
Sistem harus memiliki allowlist jenis file yang dapat diunggah.

**FILE-002**  
File yang tidak diizinkan harus ditolak dengan pesan yang dapat dipahami.

**FILE-003**  
Sistem harus memiliki batas ukuran file yang dikonfigurasi pada level produk.

**FILE-004**  
Nama file yang sama tidak boleh menyebabkan overwrite diam-diam.

**FILE-005**  
Upload gagal tidak boleh menghasilkan media record yang terlihat sebagai file valid.

**FILE-006**  
Image upload harus mendukung minimal format gambar web umum yang dinyatakan dalam product documentation.

**FILE-007**  
User tanpa upload permission tidak boleh mengunggah media.

**FILE-008**  
Alt text harus tersedia untuk image.

---

## 28. Audit Trail Requirements

**AUDIT-001**  
MVP harus mencatat minimal:
- user login success jika dipilih sebagai activity scope;
- post publish;
- post archive;
- user creation;
- user status change;
- role/permission change;
- site settings update.

**AUDIT-002**  
Audit entry minimal harus menyimpan:
- actor;
- action;
- object/resource;
- timestamp.

**AUDIT-003**  
Audit trail tidak boleh dapat diubah oleh role yang tidak memiliki permission khusus.

**AUDIT-004**  
Audit trail MVP ditujukan untuk accountability operasional, bukan compliance-grade immutable logging.

---

## 29. Security Requirements

**SEC-001**  
Semua halaman admin yang membutuhkan login harus menolak user yang belum terautentikasi.

**SEC-002**  
Authorization harus dievaluasi pada setiap action yang dilindungi.

**SEC-003**  
Credential sensitif tidak boleh ditampilkan kembali dalam UI setelah disimpan.

**SEC-004**  
Password tidak boleh ditampilkan dalam bentuk plaintext.

**SEC-005**  
Form sensitif harus memiliki perlindungan terhadap request yang tidak sah sesuai baseline keamanan web modern.

**SEC-006**  
Input user harus divalidasi.

**SEC-007**  
Konten yang dapat memuat markup harus diperlakukan dengan aturan keamanan yang mencegah execution yang tidak diizinkan.

**SEC-008**  
File upload harus divalidasi berdasarkan aturan file.

**SEC-009**  
User nonaktif harus kehilangan akses login.

**SEC-010**  
Session user yang telah logout tidak boleh dapat digunakan kembali untuk mengakses halaman terproteksi.

**SEC-011**  
Release production tidak boleh memiliki critical security issue yang diketahui dan belum ditangani.

---

## 30. Localization Requirements

MVP tidak wajib mendukung multilingual content.

Namun produk harus memenuhi:

**LOC-001**  
Teks UI utama tidak boleh bergantung pada konten hard-coded yang membuat perubahan bahasa mustahil secara produk.

**LOC-002**  
Format tanggal dan waktu harus konsisten.

**LOC-003**  
Timezone website harus dapat ditentukan melalui setting atau konfigurasi produk yang terdokumentasi.

**LOC-004**  
Scheduled publishing harus menggunakan timezone yang konsisten dan ditampilkan secara jelas kepada user.

### Future

- multi-language admin UI;
- multilingual content;
- locale-specific URLs;
- translation workflow.

---

## 31. Performance Requirements

Target berikut berlaku untuk environment produksi yang memenuhi baseline deployment yang direkomendasikan produk.

**PERF-001**  
Halaman admin biasa harus memberikan initial usable response dalam target ≤ 2.5 detik pada kondisi normal dan dataset MVP.

**PERF-002**  
Search admin terhadap dataset MVP harus menampilkan hasil dalam target ≤ 2 detik pada kondisi normal.

**PERF-003**  
Penyimpanan post harus memberikan hasil success/failure yang dapat dilihat user dalam target ≤ 2 detik, tidak termasuk upload file besar.

**PERF-004**  
Public content page harus tetap usable tanpa menunggu proses non-esensial.

**PERF-005**  
List besar harus mendukung pagination.

**PERF-006**  
Satu halaman admin tidak boleh mencoba menampilkan seluruh dataset tanpa pagination apabila jumlah data melebihi batas page size.

---

## 32. Data Retention Requirements

**RET-001**  
Published content harus dipertahankan sampai user berwenang menghapus atau mengarsipkannya sesuai kebijakan produk.

**RET-002**  
Audit trail harus memiliki retention period yang dapat didokumentasikan.

**RET-003**  
MVP tidak boleh melakukan auto-delete terhadap content utama tanpa aturan produk yang eksplisit.

**RET-004**  
Penghapusan user tidak boleh menyebabkan attribution content hilang tanpa behavior yang terdefinisi.

**RET-005**  
Jika user harus dihapus, content ownership harus ditangani melalui aturan yang eksplisit seperti reassign, preserve attribution, atau blocking deletion.

**RET-006**  
Data retention untuk backup belum menjadi fitur product-level wajib pada MVP kecuali dimasukkan ke release scope final.

---

## 33. Error Handling Requirements

**ERR-001**  
Validation error harus muncul dekat dengan field terkait atau dalam summary yang jelas.

**ERR-002**  
Error message kepada end user tidak boleh menampilkan stack trace atau informasi internal sensitif.

**ERR-003**  
Not Found harus menampilkan halaman 404 yang dapat dipahami.

**ERR-004**  
Unauthorized/Forbidden harus menghasilkan response yang membedakan akses ditolak dari resource tidak ditemukan apabila aman dilakukan.

**ERR-005**  
Upload gagal harus menunjukkan alasan umum yang actionable apabila diketahui, misalnya jenis file atau ukuran.

**ERR-006**  
Save gagal harus mempertahankan input user sejauh memungkinkan agar user tidak kehilangan seluruh pekerjaan.

**ERR-007**  
Scheduled date invalid harus ditolak sebelum content dianggap berhasil dijadwalkan.

**ERR-008**  
Duplicate slug harus menghasilkan error yang jelas atau mekanisme resolusi yang predictable.

---

## 34. Acceptance Criteria

Produk MVP dapat diterima apabila seluruh kondisi berikut terpenuhi.

### Authentication
- User valid dapat login.
- User invalid ditolak.
- User nonaktif ditolak.
- Reset password berhasil pada skenario valid.
- Logout mengakhiri akses session.

### Roles and Permissions
- Role default tersedia.
- Permission bekerja pada UI dan direct request.
- Author tidak dapat publish tanpa permission.
- User tanpa settings permission tidak dapat mengubah settings.

### Content
- Post dapat dibuat sebagai Draft.
- Draft tidak publik.
- Post dapat dijadwalkan.
- Scheduled post tidak publik sebelum waktunya.
- Published post publik.
- Archived post tidak tampil pada listing normal.
- Page dapat dibuat dan dipublikasikan.
- Duplicate slug ditangani.

### Taxonomy
- Category dapat dikelola.
- Tag dapat dikelola.
- Relasi post-category/tag bekerja.

### Media
- File valid dapat diunggah.
- File invalid ditolak.
- Media dapat dipilih untuk content.
- Alt text dapat disimpan.

### Comments
- Comment dapat masuk Pending.
- Approved tampil publik.
- Rejected/Spam tidak tampil.

### Menus
- Admin dapat mengubah menu.
- Perubahan tampil di public website.

### SEO
- SEO title dan meta description dapat disimpan.
- Global defaults tersedia.
- Content-specific SEO dapat override global fallback.

### Dashboard
- Summary status akurat.
- Recent content tersedia.
- Scheduled content tersedia.

### Search
- Search dapat menemukan known record.
- Filter status bekerja.
- Empty result state benar.

### Security
- Protected page tidak dapat diakses anonymous user.
- Unauthorized actions ditolak.
- No critical known security defect.

---

## 35. Definition of Done

Sebuah feature dianggap Done hanya jika:

1. requirement produk telah dipenuhi;
2. acceptance criteria terkait telah lulus;
3. permission telah diuji;
4. empty state tersedia;
5. validation state tersedia;
6. error state tersedia;
7. success feedback tersedia;
8. responsive behavior telah diverifikasi;
9. tidak ada critical defect;
10. tidak ada high-severity defect yang memblokir user flow;
11. dokumentasi user-facing yang diperlukan tersedia;
12. fitur dapat digunakan tanpa pengetahuan internal developer;
13. QA regression untuk core workflow telah lulus;
14. behavior konsisten dengan business rules;
15. perubahan tidak merusak workflow utama ContentFlow.

---

## 36. Product Risks

### RISK-001 — Scope Creep

**Risk:** produk berkembang menjadi page builder, e-commerce, CRM, newsletter, dan SaaS sekaligus.

**Mitigation:** fitur di luar MVP harus masuk backlog roadmap, bukan release blocker.

### RISK-002 — Generic Market Positioning

**Risk:** ContentFlow terlihat seperti CMS Laravel lain tanpa alasan kuat untuk dibeli.

**Mitigation:** fokus pada clean UX, editorial-first workflow, simplicity, dan agency/developer reusability.

### RISK-003 — Permission Complexity

**Risk:** permission terlalu granular sehingga UX membingungkan.

**Mitigation:** sediakan default roles yang masuk akal dan permission naming yang konsisten.

### RISK-004 — Content Loss

**Risk:** user kehilangan draft karena validation/error.

**Mitigation:** preserve input dan future autosave/revisions.

### RISK-005 — Media Bloat

**Risk:** media library menjadi sulit digunakan pada dataset besar.

**Mitigation:** pagination, search, metadata, dan future folder/collection capability.

### RISK-006 — SEO Misconfiguration

**Risk:** user menghasilkan metadata yang tidak konsisten.

**Mitigation:** global fallback dan sensible defaults.

### RISK-007 — Product Maintenance

**Risk:** source-code product membutuhkan update dan support berkelanjutan.

**Mitigation:** scope terkontrol, release policy, dokumentasi, dan backward-compatible product behavior bila memungkinkan.

### RISK-008 — SaaS Premature Complexity

**Risk:** multi-tenancy dan billing terlalu cepat dimasukkan.

**Mitigation:** SaaS bukan bagian MVP.

---

## 37. Future Development

Future capability candidates:

### Editorial
- revisions;
- autosave;
- editorial notes;
- content approval;
- content calendar;
- duplicate post;
- scheduled unpublish.

### Media
- image optimization;
- WebP/AVIF conversion;
- external storage;
- folders/collections;
- focal point;
- advanced metadata.

### SEO
- redirects;
- broken link detection;
- advanced sitemap settings;
- structured data;
- social preview.

### Agency
- white label;
- theme settings;
- reusable content blocks;
- import/export;
- starter site presets.

### Developer Platform
- REST API;
- API tokens;
- webhooks;
- extension hooks;
- developer documentation.

### SaaS
- multi-tenancy;
- plans;
- subscriptions;
- custom domains;
- storage quotas;
- tenant management;
- usage tracking.

### Ecosystem
- premium themes;
- extensions;
- integrations;
- marketplace;
- AI-assisted tools only when they create real user value.

---

## 38. Version Roadmap

### Version 1.0 — Core CMS MVP

**Goal:** produk pertama yang lengkap dan dapat digunakan untuk production-grade content websites.

Includes:

- Authentication
- Users
- Roles & Permissions
- Posts
- Pages
- Categories
- Tags
- Media Library
- Comments
- Menus
- SEO
- Settings
- Dashboard
- Search & Filters
- Basic Audit Trail
- Scheduled Publishing
- Public Website Content Delivery

**Exit criteria:**
- seluruh acceptance criteria MVP lulus;
- zero critical defects;
- permission matrix tervalidasi;
- core content workflow stabil.

---

### Version 1.5 — Publishing Experience

Planned:

- revisions;
- autosave;
- duplicate content;
- improved preview;
- advanced media management;
- redirects;
- improved activity log;
- backup/export foundation.

---

### Version 2.0 — Agency Edition

Planned:

- theme system;
- theme configuration;
- reusable content blocks;
- custom fields with controlled scope;
- white-label options;
- stronger import/export;
- advanced permission presets.

---

### Version 2.5 — Developer Platform

Planned:

- REST API;
- API tokens;
- webhooks;
- documented extension points;
- integration capabilities.

---

### Version 3.0 — SaaS Foundation

Planned:

- tenant model;
- plan management;
- subscription capability;
- quota controls;
- custom domains;
- per-tenant storage and configuration;
- SaaS administration.

---

### Version 4.0 — ContentFlow Cloud

Planned product direction:

```text
Create Account
→ Create Site
→ Select Theme
→ Configure Branding
→ Connect Domain
→ Publish Content
```

---

## Final Product Principle

ContentFlow CMS harus tetap mengikuti prinsip berikut:

> Build the smallest commercially useful CMS that provides a clean publishing experience, clear permissions, predictable behavior, and reusable value for individuals, businesses, and agencies.

Setiap feature baru harus menjawab minimal salah satu pertanyaan berikut:

1. Apakah fitur mempercepat pengelolaan konten?
2. Apakah fitur meningkatkan kualitas publikasi?
3. Apakah fitur mengurangi ketergantungan kepada developer?
4. Apakah fitur meningkatkan keamanan atau kontrol?
5. Apakah fitur meningkatkan repeatability sebagai produk komersial?

Jika jawabannya tidak, fitur tersebut tidak boleh menjadi prioritas MVP.

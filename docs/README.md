# Documentation Index — ContentFlow CMS

Central index for product, operations, and engineering documentation (TASK-026).

## Getting started

| Document | Audience | Description |
|---|---|---|
| [../README.md](../README.md) | Everyone | Project entry, quick start |
| [INSTALLATION.md](INSTALLATION.md) | Ops / Developer | Clean setup, browser installer, recovery |
| [INSTALLER.md](INSTALLER.md) | Engineers | Installer system design and security model |
| [DEMO_MODE.md](DEMO_MODE.md) | Product / Ops | Public demo architecture and restrictions |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Ops | VPS, Nginx, queue, scheduler, mail, backups |
| [UPGRADE.md](UPGRADE.md) | Ops / Developer | Release upgrades |
| [UPDATE_STRATEGY.md](UPDATE_STRATEGY.md) | Maintainers | SemVer, migrations, rollback policy |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Everyone | Common problems |

## Product & usage

| Document | Audience | Description |
|---|---|---|
| [USER_GUIDE.md](USER_GUIDE.md) | Owners / Editors / Ops | Commercial product user manual |
| [ADMIN_GUIDE.md](ADMIN_GUIDE.md) | Staff | Admin workflows and role matrix |
| [PRD.md](PRD.md) | Product | Product requirements (authoritative scope) |
| [BUSINESS_FLOW.md](BUSINESS_FLOW.md) | Product / QA | Business workflows and state rules |

## Engineering

| Document | Audience | Description |
|---|---|---|
| [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) | Developers | Code conventions, testing, commands |
| [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) | Developers | Directory layout |
| [SYSTEM_DESIGN.md](SYSTEM_DESIGN.md) | Architects | System design |
| [SRS.md](SRS.md) | Engineering | Software requirements |
| [TESTING.md](TESTING.md) | Developers / QA | Automated test suite |
| [PERFORMANCE_AUDIT.md](PERFORMANCE_AUDIT.md) | Developers / Ops | Performance findings and optimization roadmap |
| [VERSIONS.md](VERSIONS.md) | Everyone | Verified runtime versions |

## Security & operations

| Document | Audience | Description |
|---|---|---|
| [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) | Ops / Security | Pre-release hardening |
| [SECURITY_AUDIT.md](SECURITY_AUDIT.md) | Ops / Security | Full security audit findings |
| [RELEASE.md](RELEASE.md) | Maintainers | v1.0.0 release notes & QA |
| [QUEUE_OPERATIONS.md](QUEUE_OPERATIONS.md) | Ops | Queue worker and failed jobs |

## Environment reference

Key `.env` flags (defaults in `.env.example`):

| Variable | Purpose |
|---|---|
| `APP_ENV` | `local`, `production`, etc. |
| `APP_DEBUG` | Must be `false` in production |
| `APP_URL` | Canonical site URL |
| `DB_*` | MySQL connection |
| `QUEUE_CONNECTION` | `database` or `sync` |
| `MEDIA_DISK` | Media storage disk |
| `DEMO_MODE` | Enable demo restrictions and banner |
| `DEMO_ALLOW_IN_PRODUCTION` | Override production demo safety guard |

Never commit real credentials. Use `.env` only on each host.

## Out of scope (MVP)

Documented as **not included** in current release:

- SaaS multi-tenancy and billing
- Public member accounts
- Two-factor authentication
- Rich HTML/WYSIWYG content (plain escaped text only)
- In-app notification center
- Public site search UI

See [PRD.md](PRD.md) for full scope boundaries.

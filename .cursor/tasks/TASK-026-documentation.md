# TASK-026 — Documentation

| Field | Value |
|---|---|
| **Task ID** | TASK-026 |
| **Title** | Product and Technical Documentation |
| **Phase** | Roadmap Phase 14 |
| **Estimate focus** | Install, deploy, user, admin, developer docs |

---

## Objective

Produce release documentation that matches the implemented MVP: installation, deployment (VPS, queue, scheduler), user/admin guides, developer guide, upgrade/troubleshooting notes, and ensure authoritative specs live under `docs/` with consistent names.

## Background

ROADMAP lists required guides. Existing draft specs may still use alternate filenames at repo root—normalize to `docs/*.md` without inventing conflicting business rules.

## Dependencies

- Behavior stable through TASK-024/025 preferred.
- Do not document unbuilt features as shipped.

## Files likely affected

```text
docs/INSTALLATION.md
docs/DEPLOYMENT.md
docs/USER_GUIDE.md
docs/ADMIN_GUIDE.md
docs/DEVELOPER_GUIDE.md
docs/UPGRADE.md
docs/TROUBLESHOOTING.md
docs/PRD.md, SRS.md, ... (normalize/copy from drafts if needed)
README.md (project entry)
```

## Database changes

- None.

## Backend requirements

- Document artisan commands: migrate, seed, queue worker, schedule cron, storage:link, demo:reset, installer lock.
- Document permission roles matrix at high level.
- Document env keys without real secrets.

## Frontend requirements

- Screenshots optional; describe primary admin/public flows accurately.

## Validation rules

- N/A.

## Authorization rules

- Document which roles can do what (from seeder truth).

## Business rules

- Docs must match BUSINESS_FLOW status transitions actually implemented.
- Out-of-scope features explicitly marked not included.

## Edge cases

- Broken internal doc links—validate paths.
- Version numbers consistent with release task.

## Security considerations

- No real credentials, API keys, or customer data in docs.
- Production hardening notes (`APP_DEBUG`, HTTPS, webroot).

## Testing requirements

- Manual walkthrough: fresh install via INSTALLATION.md.
- Link/path validation checklist.
- Deployment guide dry-run notes.

## Acceptance criteria

- [ ] Installation guide works for clean setup.
- [ ] Deployment covers Nginx/VPS, queue, scheduler, mail, backups.
- [ ] User/Admin guides cover core workflows.
- [ ] Developer guide covers structure/Actions/Policies/tests.
- [ ] No secrets in docs.
- [ ] Docs match current implementation.

## Definition of Done

- Docs merged under `docs/`; README points to them; walkthrough checklist attached to task completion notes.

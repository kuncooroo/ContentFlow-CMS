# TASK-027 — Release Preparation

| Field | Value |
|---|---|
| **Task ID** | TASK-027 |
| **Title** | Release Preparation (v1.0) |
| **Phase** | Roadmap Phase 15 |
| **Estimate focus** | Package commercial-ready MVP |

---

## Objective

Prepare ContentFlow CMS Version 1.0.0 release candidate: versioning, changelog, license, packaging, final QA, security/production verification, backup/restore evidence, and exclusion of development artifacts/secrets.

## Background

Final gate before commercial distribution of the source-code product. Financial modules remain out of scope.

## Dependencies

- TASK-001–026 complete (or explicitly waived with documented residual risk—prefer complete).

## Files likely affected

```text
CHANGELOG.md
LICENSE / LICENSE.md
VERSION or config version constant
composer.json version metadata if used
.release/ or dist packaging scripts (optional)
.github/ or build scripts
.env.example (final review)
```

## Database changes

- None for release itself; verify migrations are final and ordered.

## Backend requirements

- Tag version 1.0.0 consistently across docs/package.
- Production build of frontend assets.
- Verify queue worker, scheduler, mail on staging-like VPS.
- Backup + restore rehearsal documented.
- Remove dump routes, debug pages, leftover demo credentials from default package.
- Ensure installer lock + DEMO_MODE default safe.

## Frontend requirements

- Production Vite/asset build succeeds.
- No broken admin/public assets.

## Validation rules

- N/A.

## Authorization rules

- Final authorization regression from TASK-022/023 passes.

## Business rules

- All MVP PRD acceptance criteria pass.
- Out-of-scope features absent (no payment tables/UI).

## Edge cases

- Package includes `node_modules` or `.env` by mistake—exclude.
- Windows vs Linux path issues in docs only—runtime target is Linux VPS.

## Security considerations

- Final secret scan of repo/package.
- `APP_DEBUG` false in production guidance.
- No critical known vulnerabilities.
- Upload/authz regressions green.

## Testing requirements

- Full regression suite green.
- Fresh install + upgrade-from-clean path.
- Functional smoke: editorial workflow + public visibility.
- Ops: queue, scheduler, mail, logs, backup/restore.
- Browser smoke on supported browsers if required by SRS.

## Acceptance criteria

- [ ] v1.0.0 tagged and changelog written.
- [ ] License present.
- [ ] Full regression + security checks pass.
- [ ] Fresh install succeeds; demo verified if shipped.
- [ ] Package contains no secrets or unnecessary dev artifacts.
- [ ] Documentation complete and consistent.
- [ ] Backup/restore evidenced.
- [ ] No financial/SaaS accidental inclusions.

## Definition of Done

- Release candidate approved with test report + security checklist + package artifact list; ready for distribution per commercial process.

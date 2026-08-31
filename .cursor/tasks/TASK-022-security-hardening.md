# TASK-022 — Security Hardening

| Field | Value |
|---|---|
| **Task ID** | TASK-022 |
| **Title** | Security Hardening |
| **Phase** | Roadmap Phase 10 |
| **Estimate focus** | Review + fix security gaps |

---

## Objective

Perform MVP security hardening: CSRF, XSS-safe output, authorization coverage, auth rate limits, session security, upload allowlists, rich-content rendering policy, error disclosure, and production config guidance.

## Background

This is a hardening pass across existing modules—not a new product feature. Fix gaps found; do not add unrelated features.

## Dependencies

- TASK-001–021 functionally present (or harden what exists and list blockers).

## Files likely affected

```text
app/Policies/* (coverage fixes)
app/Actions/Media/* (upload rules)
resources/views/** (escaping / {!! !!} audit)
config/session.php
bootstrap/app.php rate limiters
docs/SECURITY_CHECKLIST.md (short checklist output)
tests/Feature/Security/*
```

## Database changes

- None expected.

## Backend requirements

- Verify Policy coverage on every protected Livewire/controller action.
- Auth throttling confirmed.
- Upload allowlist + executable rejection confirmed.
- Define rich content rendering approach (e.g. HTML Purifier / restricted subset / plain escaped)—explicit and tested.
- Comments escaped by default.
- Production requirements: `APP_DEBUG=false`, HTTPS, `public/` webroot.

## Frontend requirements

- Fix any unescaped outputs found.
- Ensure unauthorized actions not only hidden but unusable.

## Validation rules

- Strengthen upload/content validation gaps discovered.

## Authorization rules

- Negative tests for each major module.
- Inactive user regression.

## Business rules

- Do not weaken validation/authorization to pass tests.

## Edge cases

- `{!! $html !!}` usages inventoried.
- SVG/script uploads.
- Session fixation already handled by login regeneration—retest.

## Security considerations

- This task’s entire scope.
- Secrets not in repo/logs/docs.
- CSRF on state changes.

## Testing requirements

- Unauthorized route/action matrix samples.
- CSRF smoke where applicable.
- Auth rate limit.
- Malicious/invalid upload rejected.
- XSS-oriented output tests for comments/titles.
- Inactive login; session invalidation after logout.

## Acceptance criteria

- [ ] No known critical security defects open.
- [ ] Checklist completed and stored under `docs/` or `.cursor/`.
- [ ] Upload + output + authz hardening verified by tests.
- [ ] Production debug/HTTPS/webroot guidance documented.

## Definition of Done

- Security checklist signed off in task notes; failing tests fixed properly; no credential leaks.

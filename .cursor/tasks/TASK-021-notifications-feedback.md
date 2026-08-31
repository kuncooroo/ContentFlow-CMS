# TASK-021 — Notifications & Feedback

| Field | Value |
|---|---|
| **Task ID** | TASK-021 |
| **Title** | Notifications and UI Feedback |
| **Phase** | Roadmap Phase 8 |
| **Estimate focus** | Flash/toast + mail polish |

---

## Objective

Standardize operational UI feedback (success, validation, system error) and harden email delivery for password reset (and any queued mail) including failure logging—without building an in-app notification center.

## Background

MVP notifications are minimal. Editorial approval notification chains are out of scope.

## Dependencies

- TASK-002 (password reset mail), TASK-001 queue.
- Admin Livewire screens from prior tasks.

## Files likely affected

```text
resources/views/components/flash*.blade.php or layout toasts
resources/views/emails/*
app/Notifications/* or Mailables
config/mail.php, queue.php
tests/Feature/Notifications*
tests/Feature/UiFeedback* (optional)
```

## Database changes

- Ensure `jobs` / `failed_jobs` usable.

## Backend requirements

- Consistent flash/event pattern for Livewire success after successful persistence only.
- Queue password reset / mail where not required inline.
- Log mail failures without credentials.
- Failed jobs inspectable via `queue:failed`.
- No persistent notification center tables/UI.

## Frontend requirements

- Toast/flash regions in admin + auth layouts.
- Validation errors distinct from system errors.
- Email templates branded via settings/app name—no customer-specific hardcoding.

## Validation rules

- N/A beyond existing forms.

## Authorization rules

- N/A.

## Business rules

- Success message only after DB success.
- Do not email on every editorial action in MVP unless PRD requires (default: reset password only).

## Edge cases

- Mail driver `log`/`array` in testing.
- Queue worker down—document UX (job pending).
- Duplicate toast on Livewire re-render.

## Security considerations

- No secrets in mail logs.
- Password reset links expire; HTTPS in production docs.

## Testing requirements

- Flash on successful save sample.
- Validation error display sample.
- Password reset notification asserted mailed/queued.
- Mail failure path logged safely.

## Acceptance criteria

- [ ] Consistent success/validation/error feedback in admin.
- [ ] Reset password email works with valid mail config.
- [ ] Failures do not leak credentials.
- [ ] No notification center feature added.

## Definition of Done

- Shared flash component used by key screens; mail tests green; queue failure documented for ops (TASK-026).

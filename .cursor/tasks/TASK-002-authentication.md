# TASK-002 — Authentication

| Field | Value |
|---|---|
| **Task ID** | TASK-002 |
| **Title** | Authentication |
| **Phase** | Roadmap Phase 1 |
| **Estimate focus** | Login, logout, password reset |

---

## Objective

Provide secure Laravel session authentication for the admin application: login, logout, forgot/reset password, session regeneration, rate limiting, and inactive-account rejection.

## Background

Admin CMS access starts here. Social login, SSO, OAuth, and passwordless are out of scope.

## Dependencies

- TASK-001 completed.
- Minimal `users` table available (Laravel default + `status` column if introduced now; full user admin is TASK-003). Prefer adding `users.status` (`active`/`inactive`) here or in TASK-003 before inactive-login tests—if deferred, document stub and complete inactive rejection in TASK-003 with regression here.

## Files likely affected

```text
database/migrations/*_users_table.php (status if needed)
app/Models/User.php
app/Enums/UserStatus.php (if status introduced)
app/Http/Controllers/Auth/*
app/Http/Requests/Auth/*
resources/views/auth/*
routes/auth.php
config/auth.php, session.php
tests/Feature/Auth/*
```

## Database changes

- Ensure `users` supports authentication fields per `docs/DATABASE.md`.
- `password_reset_tokens` table.
- `sessions` table if database sessions enabled.
- Optional: `users.status` with default `active`.

## Backend requirements

- Laravel session auth (no custom auth framework).
- Login regenerates session ID.
- Logout invalidates session + regenerates CSRF token.
- Password hashing via Laravel hasher.
- Password reset via Laravel-native flow + queued/sync mailable as configured.
- Throttle login attempts.
- Reject inactive users (AUTH-008 / FR-AUTH-004).
- Protected admin routes require auth.

## Frontend requirements

- Login page (email, password, remember optional per product).
- Forgot password + reset password pages.
- Clear validation / auth error UI without leaking account existence beyond product rules.
- Redirect intended URL after login when safe.

## Validation rules

- Email required, valid format.
- Password required on login.
- Reset: strong password rules per Laravel/project policy; token required.

## Authorization rules

- Guests only for login/reset routes.
- Authenticated users redirected away from login.
- All `admin.*` routes require authentication.

## Business rules

- Only active users may authenticate.
- Reset email only for recoverable accounts per product rules (do not leak inactive/unknown distinctions beyond SRS).
- No social/SSO.

## Edge cases

- Unknown email / wrong password → generic failure messaging.
- Expired/invalid reset token rejected.
- Concurrent session behavior follows Laravel session config.
- Already-authenticated visit to login.

## Security considerations

- Rate limit auth endpoints.
- CSRF on all state-changing forms.
- No password/token in logs.
- Session cookie secure flags for production guidance.
- Timing/information disclosure minimized per SRS.

## Testing requirements

- Valid login; invalid password; unknown email.
- Inactive account rejected.
- Rate-limit behavior.
- Logout blocks protected routes.
- Forgot password; valid reset; invalid/expired token.
- Protected route redirect to login.

## Acceptance criteria

- [ ] Valid user can log in and reach admin shell.
- [ ] Invalid credentials rejected.
- [ ] Inactive user cannot log in (when status exists).
- [ ] Session regenerated on login; logout ends session.
- [ ] Password reset works with valid token.
- [ ] Anonymous users cannot access protected admin routes.
- [ ] Auth errors do not expose sensitive account state beyond requirements.

## Definition of Done

- All acceptance criteria and auth tests pass; mail reset verified in local/log driver; no SSO packages added.

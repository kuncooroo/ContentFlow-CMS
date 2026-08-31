# TASK-003 — Users

| Field | Value |
|---|---|
| **Task ID** | TASK-003 |
| **Title** | User Administration |
| **Phase** | Roadmap Phase 2 (users portion) |
| **Estimate focus** | User CRUD + activation |

---

## Objective

Build admin user management: list, create, edit, activate/deactivate users, with unique email and status lifecycle. Role assignment UI may be stubbed until TASK-004 if roles tables are not ready—prefer wiring role attach only after TASK-004, or implement create-with-role in TASK-004.

## Background

Users are the identity backbone for authorship, moderation, and audit. Soft deletes are not used; inactive status replaces deletion for accounts.

## Dependencies

- TASK-001, TASK-002.
- TASK-004 for full role assignment (this task may create users without roles or with a placeholder if seed roles exist).
- TASK-005 for audit events on create/status change (call logger when available; otherwise leave TODO hook).

## Files likely affected

```text
database/migrations/*_users*
app/Models/User.php
app/Enums/UserStatus.php
app/Actions/Users/ChangeUserStatus.php
app/Actions/Users/CreateUser.php (if non-trivial)
app/Policies/UserPolicy.php
app/Livewire/Admin/Users/*
resources/views/livewire/admin/users/*
routes/admin.php
database/factories/UserFactory.php
tests/Feature/Admin/Users/*
tests/Feature/Livewire/Admin/Users*
```

## Database changes

- Finalize `users` per `docs/DATABASE.md`: name, email unique, password, status, timestamps.
- No soft deletes.

## Backend requirements

- Admin list with pagination.
- Create user (name, email, password, status).
- Edit user profile fields.
- Activate / deactivate via `ChangeUserStatus` Action.
- Policies for view/create/update/change-status.
- Never mass-assign raw request data.
- Password hashed on create/update when provided.

## Frontend requirements

- Livewire admin Users index (search stub OK; full search in TASK-018).
- Create / edit forms.
- Status badge + activate/deactivate actions with confirm for deactivate.
- Unauthorized actions hidden in UI and denied server-side.

## Validation rules

- Name required.
- Email required, unique, valid.
- Password required on create; optional on edit with confirmation rules.
- Status in `active|inactive`.

## Authorization rules

- Only users with user-management permissions (seeded in TASK-004) may manage users.
- Until permissions exist, restrict to authenticated admin bootstrap user and replace with policies in TASK-004.
- Users cannot escalate privileges beyond what TASK-004 allows.

## Business rules

- Deactivating a user does not delete authored content.
- Inactive users cannot log in (regression with TASK-002).
- Last Super Admin protection belongs primarily in TASK-004; do not allow deactivating the only remaining Super Admin once roles exist.

## Edge cases

- Duplicate email.
- Deactivate self (define: forbid or force re-login—prefer forbid self-deactivate if it locks out last admin).
- Empty user list / first bootstrap user.

## Security considerations

- Password never returned to UI after save.
- Authorization on every mutation.
- Do not expose other users’ password hashes.

## Testing requirements

- Create/edit user; duplicate email rejected.
- Activate/deactivate; inactive login regression.
- Unauthorized access denied.
- Validation errors.

## Acceptance criteria

- [ ] Authorized admin can list/create/edit users.
- [ ] Email uniqueness enforced.
- [ ] Status active/inactive works end-to-end with login.
- [ ] UI + server deny unauthorized actions.
- [ ] No soft-delete column introduced.

## Definition of Done

- Criteria + tests pass; UserPolicy in place or clearly deferred hooks for TASK-004 permissions; aligns with DATABASE users table.

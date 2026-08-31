# TASK-004 — Roles & Permissions

| Field | Value |
|---|---|
| **Task ID** | TASK-004 |
| **Title** | Roles and Permissions |
| **Phase** | Roadmap Phase 2 (access control) |
| **Estimate focus** | RBAC seeding, assignment, policies |

---

## Objective

Implement role-based access control: roles, permissions, pivots, seeding of default roles (Super Admin, Administrator, Editor, Author), role/permission management UI, user–role assignment, and baseline Policies/Gates for protected modules.

## Background

Authorization must be server-side. Author has no publish permission by default. UI visibility must use the same permission source as Policies.

## Dependencies

- TASK-001, TASK-002, TASK-003.
- TASK-005 recommended for auditing role/permission/user-role changes.

## Files likely affected

```text
database/migrations/*_roles* *_permissions* *_role_user* *_permission_role*
app/Models/Role.php, Permission.php
app/Actions/AccessControl/AssignRole.php
app/Actions/AccessControl/SyncRolePermissions.php
app/Policies/*.php (baseline)
app/Providers/AuthServiceProvider.php or AppServiceProvider gates
app/Livewire/Admin/Roles/*
database/seeders/RolePermissionSeeder.php
tests/Feature/Admin/Roles/*
tests/Feature/Authorization/*
```

## Database changes

- `roles`, `permissions`, `role_user`, `permission_role` per `docs/DATABASE.md`.
- Unique `roles.name`, `permissions.name`.
- `roles.is_system` flag for built-in roles.

## Backend requirements

- Seed permissions using stable names (`posts.view`, `posts.create`, `posts.update`, `posts.delete`, `posts.publish`, `posts.schedule`, `settings.manage`, etc.—full matrix from PRD/SRS).
- Seed four default roles with correct permission sets.
- Super Admin: full access (via all permissions or explicit gate).
- AssignRole / SyncRolePermissions Actions with transactions.
- Policies for User, and stubs/hooks for Post/Page/Media/Comment/Menu/Settings as modules land.
- Last active Super Admin cannot be removed/deactivated if it would leave zero active Super Admins.

## Frontend requirements

- Roles list; edit role permissions (system roles protected from destructive delete).
- User edit: assign roles.
- Hide unauthorized nav items using permission checks.

## Validation rules

- Role name unique, required.
- Permission sync only accepts known permission IDs/names.
- Cannot delete system roles required by product.

## Authorization rules

- Only privileged admins manage roles/permissions.
- Direct HTTP/Livewire attempts without permission denied.
- Author cannot receive publish by default seed (tests must lock this).

## Business rules

- Permission changes apply on subsequent requests (session/cache freshness per SRS).
- Do not invent parallel role checks in Blade (`if ($user->email === ...)`).
- Ownership rules for Author content enforced later in content Policies but permission names reserved now.

## Edge cases

- Removing last Super Admin role from last Super Admin user → blocked.
- Empty permission set on custom role.
- Concurrent permission edits.

## Security considerations

- Never trust client-provided “is admin” flags.
- Permission names are server-defined allowlists.
- Audit sensitive role changes when TASK-005 exists.

## Testing requirements

- Seed roles/permissions.
- Assign role; sync permissions.
- Policy allow/deny matrix samples (Author vs Editor vs Admin).
- Last Super Admin protection.
- Unauthorized role UI/API denied.

## Acceptance criteria

- [ ] Default roles and permissions seeded.
- [ ] Users can be assigned roles.
- [ ] Server-side permission enforcement works.
- [ ] UI hides unauthorized actions.
- [ ] Author lacks publish by default.
- [ ] Last Super Admin protected.

## Definition of Done

- Seeder idempotent; tests cover matrix samples; Policies registered; docs permission names match CURSOR examples.

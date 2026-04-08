# User And Role Lifecycle

This document explains how users enter the RGC system, how roles are assigned, and how scope boundaries work.

## Summary

- Public self-registration creates a `member` account only.
- Higher roles are assigned later by an authorized administrator.
- The main source of role assignment is `super_admin`.
- Access is controlled by both `role rank` and `location scope`.

## Current Roles

The system currently uses these normalized roles:

1. `super_admin`
2. `regional_admin`
3. `district_admin`
4. `branch_admin`
5. `bishop`
6. `pastor`
7. `accountant`
8. `member`

Role rank is defined in `App\Models\User::SYSTEM_ROLE_RANK`.

## How A New User Enters The System

### 1. Public registration

When someone registers from the public registration page:

- they choose `region`
- then `district`
- then `branch`
- the account is created as `member`
- the selected branch is stored as the user's church location

This flow is handled by:

- `app/Http/Controllers/AuthController.php`
- `app/Http/Requests/RegisterUserRequest.php`

### 2. First login

After registration, the user logs in with their own email and password.

At this stage they are still a `member` unless an authorized admin changes the role later.

## How Roles Are Assigned

### Default rule

Self-registration does not create leaders or officers.

Every public registration becomes:

- `role = member`
- Spatie role = `member`

### Promotion or reassignment

An authorized administrator can later:

- create a user directly
- promote a `member` into a leadership role
- move a user back to a lower role
- change the user's region, district, or branch scope

The main management flows are handled by:

- `app/Http/Controllers/UserManagementController.php`
- `app/Http/Controllers/Api/UserController.php`
- `app/Policies/UserPolicy.php`

## Who Can Create Or Promote Users

### `super_admin`

Main authority for:

- creating users
- assigning any supported role
- managing users across the whole system

### `regional_admin`

Can manage users only inside the assigned region and only within allowed hierarchy boundaries.

### `district_admin`

Can manage users only inside the assigned district and only within allowed hierarchy boundaries.

### `branch_admin`

Can manage users only inside the assigned branch and only within allowed hierarchy boundaries.

## Role Scope

The system does not rely on role names alone.

Each account is also limited by location scope:

- `region_id`
- `district_id`
- `branch_id`
- `church_id`

Scope helpers live in:

- `app/Models/User.php`

Important methods:

- `canManageRegion()`
- `canManageDistrict()`
- `canManageBranch()`
- `outranks()`
- `canAssignSystemRole()`

## What Each Role Is For

### `super_admin`

Use for:

- full system setup
- branch creation
- user governance
- top-level administration

### `regional_admin`

Use for:

- region-wide coordination
- oversight of districts and branches within one region

### `district_admin`

Use for:

- district-wide coordination
- branch visibility within one district

### `branch_admin`

Use for:

- branch operations
- local user administration
- announcements
- events
- offerings
- expenses

### `accountant`

Use for:

- offerings
- expenses
- branch finance activity

### `pastor`

Use for:

- branch communication
- general branch workspace visibility

### `bishop`

Use for:

- branch communication
- branch workspace visibility

### `member`

Use for:

- standard member access
- announcements
- branch chat
- giving-related member flows

## Do Roles Interfere With Each Other

The intended design is no.

The system separates access using:

- role rank
- location scope
- policies
- controller query filters
- request validation

Examples:

- a lower-ranked admin should not manage a higher-ranked admin
- a district-scoped account should not manage another district
- a branch-scoped account should not control another branch

## Password Notes

Passwords are not stored in plain text.

That means:

- the system does not keep a readable password list for all roles
- each user account can have a different password
- a role is not the same thing as a login account

### Known seeded local super admin

The local seeder creates a super admin using:

- email from `RGC_SUPER_ADMIN_EMAIL`
- password from `RGC_SUPER_ADMIN_PASSWORD`

If no password is provided and the app is in `local` or `testing`, the seeder falls back to:

- `ChangeMe123!`

Seeder file:

- `database/seeders/RgcSuperAdminSeeder.php`

## Recommended Real-World Flow

1. A person registers and becomes `member`.
2. They log in with their own account.
3. If they need leadership access, `super_admin` assigns the correct role.
4. The assigned role is limited to the correct region, district, or branch.
5. The user operates only within that allowed scope.

## Best Practice

- Keep `super_admin` accounts very few.
- Give each person the smallest role that is enough for the job.
- Do not use shared passwords.
- Do not promote ordinary users unless there is a real operational reason.
- Review role assignments regularly.

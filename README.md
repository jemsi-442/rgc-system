# RGC - Redeemed Gospel Church Inc. Tanzania Management Platform

National church governance platform for Tanzania Mainland and Zanzibar.
Built with Laravel 12, Vite, Tailwind CSS, and Spatie Permission.

## Overview

This codebase has already been refactored away from the original Trashed project and now runs as an RGC application with:

- public homepage at `/`
- web authentication for dashboard users
- Bearer-token API authentication for external clients
- hierarchical governance: `super_admin -> regional_admin -> district_admin -> branch_admin -> officers -> member`
- strict role and scope enforcement
- Tanzania master data for regions and districts
- branch-scoped announcements, events, offerings, expenses, and chat
- Snippe-hosted offering payment sessions with webhook reconciliation

## Active Runtime Surface

The current active application surface is intentionally focused on the RGC governance modules.

Active controllers:
- `AuthController`
- `PublicController`
- `DashboardController`
- `BranchController`
- `BranchMessageController`
- `AnnouncementController`
- `EventController`
- `OfferingController`
- `OfferingPaymentController`
- `ExpenseController`
- `HomeSliderController`
- `Api\AuthController`
- `Api\LocationController`
- `Api\UserController`

Active models:
- `User`
- `Region`
- `District`
- `Branch` (`churches` table)
- `Announcement`
- `Event`
- `Offering`
- `OfferingPayment`
- `Expense`
- `BranchMessage`
- `HomeSlider` (`slides` table)

Active seed flow:
- `TanzaniaRegionDistrictSeeder`
- `RgcRolePermissionSeeder`
- `RgcSuperAdminSeeder`

## Legacy Archive

Old repo-era modules that are no longer part of the active RGC runtime were moved to:

- `legacy-archive/`

This archive exists only for reference and manual recovery.
It is not part of the active route surface, active seeding flow, or current runtime architecture.

Archived items include:
- old member portal modules
- old income and request modules
- old pastoral service and jumuiya modules
- old exports
- old unused seeders
- old controllers and models not used by the current RGC platform

## Governance and Roles

Current normalized system roles:
- `super_admin`
- `regional_admin`
- `district_admin`
- `branch_admin`
- `bishop`
- `pastor`
- `accountant`
- `member`

Role enforcement uses:
- `spatie/laravel-permission`
- Laravel policies
- scoped controller queries
- request validation on region, district, and branch hierarchy

Related admin documentation:
- `docs/user-role-lifecycle.md`
- `docs/railway-deploy.md`

## Core Data Model

Primary master and governance tables:
- `regions`
- `districts`
- `churches`
- `users`
- `branch_messages`
- `announcements`
- `events`
- `offerings`
- `offering_payments`
- `expenses`
- `slides`
- Spatie permission tables: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

Supporting Laravel runtime tables:
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `password_reset_tokens`

## Tanzania Master Data

Seeded canonical region set:
- 26 Mainland regions
- Zanzibar regions
- Pemba regions

Current canonical total:
- `31` regions

Dependent dropdown APIs:
- `GET /api/regions`
- `GET /api/districts?region_id=`
- `GET /api/branches?district_id=`

## Authentication

### Web

Used for browser login, registration, dashboard, and branch-scoped web modules.

Routes:
- `GET /login`
- `POST /login`
- `GET /register`
- `POST /register`
- `POST /logout`
- `GET /dashboard`

### API

Public:
- `POST /api/auth/login`
- `GET /api/regions`
- `GET /api/districts?region_id=`
- `GET /api/branches?district_id=`

Bearer token protected:
- `POST /api/auth/logout`
- `GET /api/me`
- `GET /api/users`
- `POST /api/users`
- `GET /api/users/{id}`
- `PUT/PATCH /api/users/{id}`
- `DELETE /api/users/{id}`

API token notes:
- bearer tokens are single-token-per-user and expire automatically
- default expiry is `1440` minutes (`AUTH_API_TOKEN_EXPIRE_MINUTES`)
- password changes, account deactivation, and logout revoke the current API token immediately

## Branch and Registration Rules

Branch creation requires:
- region
- district
- branch name
- branch type

Public registration requires:
- region selection
- district selection
- branch selection

Validation enforced by the app:
- district must belong to selected region
- branch must belong to selected district
- scoped users cannot create records outside their hierarchy


## Snippe Payments

The offerings module now supports Snippe hosted payment sessions.

Admin flow:
- create a payment link from `/offerings/create`
- share the hosted checkout link with the giver
- let Snippe call the webhook after payment
- the system creates the final offering record automatically when the payment is confirmed
- completed receipts are downloaded through short-lived signed links instead of permanent public URLs
- public status pages mask payer email and phone details and send `no-store` / `noindex` headers

Routes:
- `POST /offerings/payments`
- `POST /offerings/payments/{payment}/sync`
- `GET /giving/{publicReference}`
- `POST /api/payments/snippe/webhook`

Required `.env` values:
- `SNIPPE_BASE_URL=https://api.snippe.sh`
- `SNIPPE_API_KEY=`
- `SNIPPE_WEBHOOK_SECRET=`
- `SNIPPE_TIMEOUT=15`

Security note:
- keep Snippe credentials only in `.env`
- do not commit live keys or webhook secrets into the repository
- if credentials were previously shared outside `.env`, rotate them before production


## SMTP Mail Setup

The current mail features already support:
- donor receipt emails after completed payments
- branch payment alert emails for branch leaders

To send emails to real inboxes, configure SMTP in `.env`.

Recommended production values:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_FROM_ADDRESS=noreply@rgc.or.tz
MAIL_FROM_NAME="RGC Platform"
```

Notes:
- `MAIL_MAILER=log` keeps emails in local logs only and does not deliver to real inboxes.
- the current `.env` in local development is still set to `log`
- change `MAIL_FROM_ADDRESS` to a real domain address you control before production

After updating SMTP credentials, clear config cache:

```bash
php artisan optimize:clear
```

Send a test email:

```bash
php artisan mail:test your-email@example.com
```

If delivery fails, verify:
- SMTP host and port
- username and password
- TLS requirement of the provider
- sender address/domain verification at your provider
## Theme

RGC brand colors:
- Yellow: `#FFD700`
- Red: `#C00000`
- Black: `#000000`
- White: `#FFFFFF`

## Local Setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Set database credentials

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rgc_db
DB_USERNAME=root
DB_PASSWORD=
```

Important defaults already expected by the app:
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`

That means migrations must be run before using the web app.

### 4. Run database setup

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Build assets

```bash
npm run build
```

### 6. Start development server

```bash
php artisan serve
```

## Bootstrap Super Admin

The default seeding flow creates only the initial `super_admin` account.
Regional, district, and branch admins should be created later by `super_admin` from the dashboard after the right branch hierarchy already exists.

For non-testing environments, set these before the first seed:

```env
RGC_SUPER_ADMIN_EMAIL=your-admin-email@example.com
RGC_SUPER_ADMIN_PASSWORD=use-a-strong-random-password
```

Notes:
- The repository intentionally does not display admin credentials in the browser or README.
- After the first successful sign-in, rotate the bootstrap password and create other leadership accounts through the user management screen.

## Testing and QA

### Test Database

Automated tests now default to in-memory SQLite through [`phpunit.xml`](/home/jaykali/rgc-system/phpunit.xml), so the suite can run without a local MySQL service or tracked credentials.

For MySQL parity checks in a local environment, override the test database variables at runtime instead of committing them into the repository.

### Run the Test Suite

```bash
php artisan test
```

### Current Automated Coverage

The current suite covers:
- API login, `/api/me`, and logout token revocation
- User API scope enforcement and privilege escalation blocking
- public registration hierarchy validation
- super admin branch creation hierarchy validation
- dashboard scope for member and regional admin views
- branch chat isolation
- announcements, offerings, and expenses branch scoping
- Snippe offering payment session creation and webhook confirmation
- update/delete authorization for announcements, offerings, expenses, and users
- assistant management, feedback ownership, and interaction pruning
- production ops checks, media cleanup, signed public receipts, and upload validation hardening

### Manual QA Checklist

Recommended smoke checks after major changes:
- open `/` and confirm the public homepage renders
- open `/login` and `/register` and verify hierarchy dropdowns load correctly
- log in as `super_admin` and confirm `/dashboard` renders without errors
- create a branch with a valid region/district pair and verify invalid pairs are rejected
- register a member and confirm branch, district, and region are stored correctly
- verify `/api/auth/login` returns a bearer token
- verify `/api/me` works with that token
- verify `/api/users` respects role and governance scope
- create a Snippe payment link and confirm the public status page masks payer contact details
- confirm the receipt PDF link only works as a signed URL and expires after its short access window
- verify branch chat rejects more than 5 total attachments in one message
- verify `svg` files are rejected for announcement and homepage slider images

## Production Deployment Checklist

### Server Requirements

Recommended baseline:
- Linux server with PHP `8.2+`
- MySQL or MariaDB
- Nginx or Apache
- Composer
- Node.js and npm for asset builds
- process manager for queue workers such as `systemd` or `supervisor`

### Production Environment Values

Before go-live, set these in `.env`:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-real-domain`
- `SESSION_SECURE_COOKIE=true`
- real `DB_*` credentials
- real `MAIL_*` credentials
- `LOG_LEVEL=info` or stricter

### First Deploy

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Web Server

Nginx:
- point the document root to `public/`
- send PHP requests to PHP-FPM
- deny direct access outside `public/`

Apache:
- point the virtual host `DocumentRoot` to `public/`
- ensure `mod_rewrite` is enabled
- allow Laravel's `public/.htaccess` rules to work

### Filesystem and Runtime

Make sure the web server user can write to:
- `storage/`
- `bootstrap/cache/`

If uploads or homepage slider images are used, confirm the public symlink exists:

```bash
php artisan storage:link
```

### Queue Worker

This application uses the database queue by default. Run a persistent worker in production:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Recommended approach:
- run the queue worker under `systemd` or `supervisor`
- restart the worker after each deploy

### Post-Deploy Verification

After deployment, verify:
- `/` returns the public homepage
- `/login` and `/register` render
- `php artisan migrate:status` shows all migrations as ran
- `php artisan route:list --except-vendor` completes without errors
- `php artisan ops:health-check` exits successfully
- `php artisan storage:link` has been run if `FILESYSTEM_DISK=public`
- `php artisan test` still passes in the deployment pipeline or staging environment
- queue workers are running
- the seeded super admin can log in and land on `/dashboard`

### Railway Deployment

This repository now includes Railway-ready files:
- `nixpacks.toml`
- `Procfile`
- `scripts/railway-release.sh`
- `scripts/railway-start.sh`
- `scripts/railway-worker.sh`
- `railway.env.example`

Recommended Railway layout:
- one `web` service from this repo
- one `worker` service from this repo
- one `scheduler` service from this repo
- one MySQL service attached to the project

Recommended Railway environment values:
- start from [railway.env.example](/home/jaykali/rgc-system/railway.env.example)
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-railway-domain`
- `APP_KEY=base64:...`
- real `DB_*` values from the Railway MySQL service
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`
- `FILESYSTEM_DISK=public` when you attach a persistent Railway volume for uploads
- or move later to `FILESYSTEM_DISK=s3` with an S3-compatible provider if you want external object storage
- real `MAIL_*` values
- real `SNIPPE_*` values
- `LOG_CHANNEL=stack`
- `LOG_STACK=stderr`

Railway first-deploy sequence:
1. Deploy the repo and let Nixpacks run Composer and Vite build steps.
2. Set `APP_KEY` before opening the app publicly.
3. Attach a persistent volume and mount it to `/app/storage` before opening uploads to real users.
4. Run `bash scripts/railway-release.sh`.
5. Run `php artisan db:seed --force` on the first deploy only.
6. Keep the `worker` service running with `bash scripts/railway-worker.sh`.
7. Keep the `scheduler` service running with `bash scripts/railway-scheduler.sh`.
8. Confirm `SESSION_DRIVER`, `CACHE_STORE`, and `QUEUE_CONNECTION` all point to durable database-backed drivers.
9. Run `php artisan ops:health-check` and clear any reported failures before going live.

`php artisan ops:health-check` now flags common production mistakes such as `MAIL_MAILER=log`, a missing `public/storage` link when using the `public` disk, invalid token expiry, and missing database-backed runtime tables.

Signed-link note:
- keep `APP_URL` correct in every environment because public receipt links are signed against that URL
- if the public domain changes, clear config cache and generate new links from the app

Persistent upload note:
- Railway containers are ephemeral by default.
- This app stores slider images, announcement images, and branch chat attachments on the `public` disk.
- For production, attach a persistent volume before relying on local/public uploads.
- If you stay on ephemeral local storage without a volume, uploaded files may disappear after redeploys or restarts.

Upload storage decision:
- Current recommendation for this project: `FILESYSTEM_DISK=public` with a persistent Railway volume mounted at `/app/storage`.
- Later, if you want external object storage, switch to `FILESYSTEM_DISK=s3` with an S3-compatible provider such as Cloudflare R2.
- The current no-card path is the persistent volume route, because slides, announcement images, and branch chat attachments are user-facing content.

Branch chat realtime note:
- branch chat now uses Server-Sent Events on `/messages/stream`
- Railway should handle this normally
- if you later place another proxy/CDN in front, make sure buffering is disabled for that route
- branch chat send, feed, and stream routes are rate-limited, so repeated reconnect loops, aggressive polling, or spam posting should be tested carefully behind any extra proxy layer

Upload safety note:
- public-served slider and announcement images are limited to `jpg`, `jpeg`, `png`, `webp`, and `gif`
- `svg` is intentionally rejected for these public image surfaces
- branch chat attachment names are sanitized before storage and attachment metadata uses server-side MIME detection

Maintenance scheduler note:
- run Laravel scheduler continuously in production so `announcements:archive-expired`, `assistant:prune-interactions`, and `media:prune-orphans` execute automatically
- if your platform does not provide a scheduler service, trigger `php artisan schedule:run` every minute from cron or an equivalent platform job

Backup and restore note:
- keep regular database backups outside the app server
- if you use `FILESYSTEM_DISK=public`, back up the persistent upload volume together with the database
- test one restore path in staging before relying on backups for production recovery
- `php artisan ops:backup-checklist` gives a config-aware summary of the database, uploads, secrets, and restore steps that must be covered

### Notes

- Keep `rgc_db` for the application runtime, and let automated tests use the isolated SQLite testing config unless you intentionally override it for MySQL parity checks.
- The app now has scheduled housekeeping commands for expired announcements, assistant interaction pruning, and orphaned public upload cleanup.
- assistant interaction analytics keep role context plus hashed request fingerprints for authenticated users, but they no longer store raw IP or raw user-agent values from public traffic.

## Useful Commands

```bash
php artisan route:list --except-vendor
php artisan migrate:status
php artisan optimize:clear
composer dump-autoload
php artisan test
php artisan ops:health-check
php artisan ops:backup-checklist
php artisan assistant:prune-interactions --dry-run
php artisan media:prune-orphans --dry-run
```

## Current Project Notes

- The runtime has been reconciled with the current database naming used by the application.
- `Branch` uses the `churches` table and `HomeSlider` uses the `slides` table.
- The active authorization surface is protected by middleware, policies, scoped queries, and regression tests.
- Public receipts now rely on short-lived signed links instead of permanent open URLs.
- Public payment status pages mask payer contact details and are marked non-cacheable / non-indexable.
## Progressive Web App

The platform can be installed on supported phones, tablets, and desktops as a PWA.

- `public/manifest.webmanifest` defines the install metadata, icons, colors, and standalone display mode.
- `public/sw.js` registers the app shell cache and serves `public/offline.html` as a fallback when navigation fails offline.
- `resources/views/layouts/app.blade.php` includes the install button and install banner shell.
- `resources/js/app.js` handles the browser `beforeinstallprompt` flow and iPhone/iPad "Add to Home Screen" guidance.

Deployment notes:

- Use HTTPS in production or browsers will suppress installability.
- Keep `APP_URL` accurate so manifest, icons, and other public assets resolve correctly.
- After deploy, open the site once online so the service worker can cache the shell before offline use.

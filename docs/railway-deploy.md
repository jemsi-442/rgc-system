# Railway Deployment Guide

This guide explains how to deploy the RGC system to Railway using a Railway-provided domain first, then move to a custom domain later if needed.

## Recommended Railway Layout

Use one Railway project with:

1. one `web` service from this repository
2. one `worker` service from this repository
3. one `scheduler` service from this repository
4. one MySQL service attached to the same project

## Files Already Prepared For Railway

The repository already includes Railway-oriented startup files:

- `Procfile`
- `nixpacks.toml`
- `scripts/railway-start.sh`
- `scripts/railway-worker.sh`
- `scripts/railway-scheduler.sh`
- `scripts/railway-release.sh`
- `railway.env.example`

## Step 1: Create The Railway Project

1. Push this codebase to GitHub.
2. In Railway, create a new project.
3. Choose `Deploy from GitHub Repo`.
4. Select this repository.

Railway should detect the app and build it using Nixpacks.

## Step 2: Add The Database

Inside the same Railway project:

1. add a `MySQL` service
2. note the database credentials provided by Railway

These values will be used in the app service variables.

## Step 3: Configure The Web Service

The main app service should use:

- build from this repository
- start command from `nixpacks.toml`
- web startup script: `bash scripts/railway-start.sh`

The script already:

- creates storage framework directories
- refreshes `public/storage` link
- starts Laravel on the Railway port

## Step 4: Add Environment Variables

Open the `Variables` tab for the web service.

Start from:

- `railway.env.example`

Paste it into the Railway `RAW Editor`, then replace placeholders with real values.

### Required variables

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-app.up.railway.app`
- `APP_KEY=base64:...`
- `DB_CONNECTION=mysql`
- `DB_HOST=...`
- `DB_PORT=3306`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`
- `RGC_SUPER_ADMIN_EMAIL=...`
- `RGC_SUPER_ADMIN_PASSWORD=...`
- real `MAIL_*` values
- real `SNIPPE_*` values

### Important runtime values

- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`
- `QUEUE_FAILED_DRIVER=database-uuids`
- `FILESYSTEM_DISK=public`

## Step 5: Generate APP_KEY

Generate an application key locally:

```bash
php artisan key:generate --show
```

Copy the generated value into:

- `APP_KEY`

## Step 6: Generate A Railway Public Domain

Railway does not assign a public domain automatically.

For the web service:

1. open service settings
2. go to `Networking`
3. find `Public Networking`
4. click `Generate Domain`

This will give you a live HTTPS URL such as:

- `https://your-app.up.railway.app`

Then set:

- `APP_URL=https://your-app.up.railway.app`

This is enough for:

- public access
- signed links
- Snippe webhook callbacks
- PWA installation

You do not need the church's final custom domain yet.

## Step 7: Attach Persistent Storage

If you keep:

- `FILESYSTEM_DISK=public`

then attach a persistent Railway volume to the web service and mount it at:

- `/app/storage`

This is important so uploads survive redeploys.

Without persistent storage, public uploads may disappear after redeployment.

## Step 8: Run Release Steps

After variables and storage are ready, run:

```bash
bash scripts/railway-release.sh
```

This script already does:

- `php artisan migrate --force --no-interaction`
- `php artisan optimize:clear --no-interaction`
- `php artisan config:cache --no-interaction`
- `php artisan route:cache --no-interaction`
- `php artisan view:cache --no-interaction`

## Step 9: Seed First-Time Data

On the first deployment only, run:

```bash
php artisan db:seed --force
```

This prepares:

- Tanzania locations
- roles and permissions
- super admin account

## Step 10: Create Worker And Scheduler Services

### Worker service

Create another Railway service from the same repository and use:

```bash
bash scripts/railway-worker.sh
```

This runs:

```bash
php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=3
```

### Scheduler service

Create another Railway service from the same repository and use:

```bash
bash scripts/railway-scheduler.sh
```

This runs:

```bash
php artisan schedule:work
```

## Step 11: Run Health Check

Before going live, run:

```bash
php artisan ops:health-check
```

Also useful:

```bash
php artisan ops:backup-checklist
```

## Step 12: Smoke Test The Main Flows

After deployment, test:

1. homepage
2. login
3. registration
4. region -> district -> branch selection
5. dashboard
6. announcements
7. branch chat
8. giving and payment prompt
9. Snippe webhook callback
10. receipt PDF
11. assistant
12. PWA install

## Known Production-Critical Checks

Make sure these are correct:

- `APP_URL` must use the live Railway HTTPS domain
- database credentials must work
- `MAIL_MAILER` must point to real SMTP, not `log`
- queue worker must be running
- scheduler must be running
- storage must persist
- Snippe webhook values must be real

## Moving Later To A Custom Domain Or Another Host

When you later move to a church-owned domain or another provider such as Rodline:

1. point the new domain correctly
2. update `APP_URL`
3. update Snippe webhook URL if needed
4. clear and rebuild config cache
5. re-test signed links, payments, and PWA install

The Railway deployment can be used as your real staging or temporary live environment until that move is approved.

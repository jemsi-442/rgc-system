# RGC Tanzania Management System

RGC Tanzania Management System is a digital platform for Redeemed Gospel Church Inc. Tanzania. It helps the church manage leadership structure, branches, members, announcements, events, offerings, expenses, and internal communication from one place.

The system is built for church operations across national, regional, district, branch, leadership, and member levels.

## What the System Does

- Manages regions, districts, and church branches
- Registers and manages users based on their church roles
- Shares announcements with the right branch or leadership scope
- Tracks church events and branch activities
- Records offerings and online payment confirmations
- Manages branch expenses
- Supports branch-level chat and attachments
- Shows each user a dashboard that matches their role
- Provides a public homepage with church information and PWA support

## Technology Stack

- Laravel 12
- MySQL / MariaDB
- Vite
- Tailwind CSS
- Spatie Laravel Permission
- Snippe payment integration

## User Roles

The system supports these main roles:

- `super_admin`
- `regional_admin`
- `district_admin`
- `branch_admin`
- `bishop`
- `pastor`
- `accountant`
- `member`

Each role only sees and manages the information it is allowed to access. For example, a branch leader cannot manage another branch outside their assigned scope.

## Main Modules

### Homepage

The homepage introduces RGC Tanzania, shows key church information, recent updates, and links for login or registration.

### Dashboard

The dashboard changes based on the logged-in user. It can show summaries for branches, members, events, offerings, payments, announcements, and other church activities.

### Branches

Church branches are organized using:

- Region
- District
- Branch

This keeps church records connected to the correct leadership and location.

### Announcements and Events

Leaders can publish announcements and events for the right branch or scope. Members see updates that are relevant to their church branch.

### Offerings and Payments

The offerings module supports manual records and Snippe hosted checkout. When a payment is confirmed, the system creates the final offering record automatically.

### Branch Chat

Branch chat supports internal communication for each branch. Messages and attachments stay within the correct branch scope.

## Requirements

For local development, install:

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL or MariaDB

## Local Setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Create the environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Set database credentials

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rgc_db
DB_USERNAME=root
DB_PASSWORD=
```

The app also expects database-backed sessions, cache, and queues:

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 4. Run migrations and seeders

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Build frontend assets

```bash
npm run build
```

### 6. Start the development server

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

## First Super Admin

The seeders create the first `super_admin` account. Before seeding production, set a safe email and password in `.env`:

```env
RGC_SUPER_ADMIN_EMAIL=admin@example.com
RGC_SUPER_ADMIN_PASSWORD=strong-password-here
```

After the first login, change the bootstrap password and create the other leadership accounts from the dashboard.

## API

Public endpoints:

- `POST /api/auth/login`
- `GET /api/regions`
- `GET /api/districts?region_id=`
- `GET /api/branches?district_id=`

Bearer-token protected endpoints:

- `POST /api/auth/logout`
- `GET /api/me`
- `GET /api/users`
- `POST /api/users`
- `GET /api/users/{id}`
- `PUT/PATCH /api/users/{id}`
- `DELETE /api/users/{id}`

API token expiry is controlled by:

```env
AUTH_API_TOKEN_EXPIRE_MINUTES=1440
```

## Snippe Payments

Set these values in `.env` before using hosted payments:

```env
SNIPPE_BASE_URL=https://api.snippe.sh
SNIPPE_API_KEY=
SNIPPE_WEBHOOK_SECRET=
SNIPPE_TIMEOUT=15
```

Payment routes:

- `POST /offerings/payments`
- `POST /offerings/payments/{payment}/sync`
- `GET /giving/{publicReference}`
- `POST /api/payments/snippe/webhook`

Do not commit live API keys or webhook secrets to the repository.

## Email Setup

To send real emails, configure SMTP in `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_FROM_ADDRESS=noreply@rgc.or.tz
MAIL_FROM_NAME="RGC Tanzania"
```

After changing mail settings:

```bash
php artisan optimize:clear
```

Send a test email:

```bash
php artisan mail:test your-email@example.com
```

## Progressive Web App

The system can be installed on supported phones, tablets, and desktops as a PWA.

- `public/manifest.webmanifest` contains install metadata
- `public/sw.js` handles the service worker
- `public/offline.html` is used when the app cannot reach the network

Use HTTPS in production so browsers allow installation.

## Testing

Run tests:

```bash
php artisan test
```

Recommended smoke checks after major changes:

- Open `/` and confirm the homepage looks correct
- Open `/login` and `/register`
- Confirm region, district, and branch dropdowns load
- Log in as `super_admin` and open `/dashboard`
- Create a branch with a valid region and district
- Register a member and confirm the branch is saved correctly
- Create a Snippe payment link and confirm the status page works
- Confirm receipt links use signed URLs
- Test branch chat and attachments

## Production Deployment

Production requirements:

- PHP 8.2+
- MySQL or MariaDB
- Composer
- Node.js and npm
- Nginx or Apache
- A queue worker through `systemd` or `supervisor`

Important production `.env` values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
SESSION_SECURE_COOKIE=true
LOG_LEVEL=info
```

Deployment commands:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run the queue worker:

```bash
php artisan queue:work --tries=3 --timeout=90
```

## Railway Deployment

The repository includes Railway deployment files:

- `nixpacks.toml`
- `Procfile`
- `scripts/railway-release.sh`
- `scripts/railway-start.sh`
- `scripts/railway-worker.sh`
- `scripts/railway-scheduler.sh`
- `railway.env.example`

Recommended Railway services:

- `web`
- `worker`
- `scheduler`
- MySQL service

After setting environment variables, run:

```bash
bash scripts/railway-release.sh
php artisan db:seed --force
php artisan ops:health-check
```

For slider images, announcement images, and chat attachments, use a persistent Railway volume or external storage such as S3/R2.

## Useful Commands

```bash
php artisan route:list --except-vendor
php artisan migrate:status
php artisan optimize:clear
composer dump-autoload
npm run build
php artisan test
php artisan ops:health-check
php artisan ops:backup-checklist
php artisan assistant:prune-interactions --dry-run
php artisan media:prune-orphans --dry-run
```

## Security Notes

- Do not commit `.env`
- Do not commit live API keys
- Use HTTPS in production
- Change the bootstrap password after the first login
- Keep the queue worker and scheduler running in production
- Back up the database and uploaded files regularly

## Brand Colors

- Yellow: `#FFD700`
- Red: `#C00000`
- Black: `#000000`
- White: `#FFFFFF`

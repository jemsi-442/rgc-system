# Security Checklist

This checklist helps the team review the RGC system before calling it production-strong.

## Secrets

- Keep `.env` out of Git and avoid copying secrets into docs, tickets, or chat.
- Rotate any secret that has ever been shared outside the server environment.
- Review and protect:
  - `APP_KEY`
  - `DB_PASSWORD`
  - `MAIL_PASSWORD`
  - `SNIPPE_API_KEY`
  - `SNIPPE_WEBHOOK_SECRET`
- Make sure Railway variables are correct for `web`, `worker`, and `scheduler`.

## Authentication

- Change all test, seed, or temporary passwords before live use.
- Keep `APP_DEBUG=false` in production.
- Keep login and registration rate limits enabled.
- Re-test login, logout, and PWA login flow after major deploys.
- Plan multi-factor authentication for `super_admin` accounts when possible.

## Roles and Scope

- Confirm admin routes are protected by `auth`, role middleware, and policies.
- Re-test role boundaries for:
  - `super_admin`
  - `regional_admin`
  - `district_admin`
  - `branch_admin`
  - `accountant`
  - `member`
- Confirm users cannot view or manage records outside their region, district, or branch scope.

## Uploads and Media

- Keep public images server-re-encoded before storage.
- Keep branch chat attachments on private storage and serve them through controllers.
- Do not move non-image chat attachments back to public storage.
- Keep `X-Content-Type-Options: nosniff` on protected media responses.
- Keep `Cache-Control: private, no-store` on protected media responses.
- Make sure the hosting layer does not allow script execution inside upload or storage paths.

## Browser and Session Security

- Use `APP_URL` with `https` only.
- Keep `SESSION_SECURE_COOKIE=true` in production.
- Confirm CSRF and session flow after each deploy, especially in the installed app.
- Prefer extra browser headers at the proxy or hosting layer:
  - `Referrer-Policy`
  - `Content-Security-Policy`
  - `X-Frame-Options`

## Database

- Do not expose the database directly to the public internet unless strictly controlled.
- Prefer a dedicated production database user instead of `root`.
- Review backup and restore steps before launch.
- Confirm runtime tables exist:
  - `sessions`
  - `cache`
  - `cache_locks`
  - `jobs`
  - `failed_jobs`

## Queues and Scheduler

- Keep worker and scheduler processes alive in production.
- Review failed jobs regularly.
- Avoid writing secrets or full payment payloads to logs.

## Payments

- Verify Snippe webhook signature validation in live deployment.
- Review payment status pages to ensure they do not expose unnecessary payer details.
- Re-test duplicate payment and webhook replay behavior before full launch.
- Rotate Snippe credentials immediately if they were ever shared outside the server environment.

## Dependencies

- Keep Laravel 12 on the latest safe patch release.
- Run dependency audits regularly:
  - `composer audit`
  - `npm audit`
- Review packages that affect:
  - uploads
  - authentication
  - payments
  - PDF and export flows

## Logging and Monitoring

- Review logs for login failures, webhook failures, and upload failures.
- Keep sensitive tokens, passwords, and secrets out of logs.
- Add a simple incident response note for the team:
  - who checks logs
  - who rotates secrets
  - who verifies payments and queues

## Recovery

- Be ready to restore:
  - code
  - environment variables
  - database
  - uploaded files
- Test the restore path at least once before relying on it.

## Go-Live Review

- Rotate any exposed or test credentials.
- Re-run smoke tests for:
  - login and logout
  - registration
  - role access
  - uploads
  - branch chat
  - announcements
  - giving and payment flow
  - receipts
- Review the Railway deployment checklist in `docs/railway-deploy.md`.


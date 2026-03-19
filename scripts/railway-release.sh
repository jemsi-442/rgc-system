#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/framework/{cache,sessions,views} storage/app/public bootstrap/cache
php artisan storage:link --force >/dev/null 2>&1 || true

php artisan migrate --force --no-interaction
php artisan optimize:clear --no-interaction
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

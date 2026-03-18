#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
php artisan storage:link --force >/dev/null 2>&1 || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"

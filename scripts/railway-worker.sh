#!/usr/bin/env bash
set -euo pipefail

exec php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=3

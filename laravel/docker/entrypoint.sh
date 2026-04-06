#!/bin/sh
set -e

# Render (and similar) expose Postgres as DATABASE_URL; Laravel reads DB_URL.
export DB_URL="${DB_URL:-$DATABASE_URL}"
export DB_CONNECTION="${DB_CONNECTION:-pgsql}"

if [ -z "$APP_KEY" ]; then
  echo "error: set APP_KEY in the hosting dashboard (run locally: php artisan key:generate --show)" >&2
  exit 1
fi

php artisan config:clear

php artisan migrate --force

php artisan storage:link 2>/dev/null || true

php artisan optimize

exec /usr/bin/supervisord -n

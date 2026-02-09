#!/bin/sh
set -e

# Ensure required envs exist even if the hosting provider doesn't inject them.
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_DATABASE="${DB_DATABASE:-/app/database/database.sqlite}"

# Ensure SQLite database exists for first boot.
if [ "${DB_CONNECTION}" = "sqlite" ]; then
  mkdir -p /app/database
  touch "${DB_DATABASE}"
fi

php artisan storage:link || true
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

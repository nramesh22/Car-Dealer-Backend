#!/bin/sh
set -e

# Ensure SQLite database exists for first boot.
if [ "${DB_CONNECTION}" = "sqlite" ]; then
  mkdir -p /app/database
  if [ -n "${DB_DATABASE}" ]; then
    touch "${DB_DATABASE}"
  else
    touch /app/database/database.sqlite
  fi
fi

php artisan storage:link || true
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

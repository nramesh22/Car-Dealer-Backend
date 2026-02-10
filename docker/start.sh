#!/bin/sh
set -e

# Ensure required envs exist even if the hosting provider doesn't inject them.
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_DATABASE="${DB_DATABASE:-/app/database/database.sqlite}"
export FILAMENT_ADMIN_NAME="${FILAMENT_ADMIN_NAME:-Admin}"

# Ensure SQLite database exists for first boot.
if [ "${DB_CONNECTION}" = "sqlite" ]; then
  mkdir -p /app/database
  touch "${DB_DATABASE}"
fi

php artisan storage:link || true
php artisan migrate --force

# Create a Filament admin user when credentials are provided via env.
if [ -n "${FILAMENT_ADMIN_EMAIL}" ] && [ -n "${FILAMENT_ADMIN_PASSWORD}" ]; then
  php -r '
  require __DIR__ . "/vendor/autoload.php";
  $app = require __DIR__ . "/bootstrap/app.php";
  $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
  $email = getenv("FILAMENT_ADMIN_EMAIL");
  $name = getenv("FILAMENT_ADMIN_NAME") ?: "Admin";
  $password = getenv("FILAMENT_ADMIN_PASSWORD");
  App\\Models\\User::updateOrCreate(
    ["email" => $email],
    [
      "name" => $name,
      "password" => Illuminate\\Support\\Facades\\Hash::make($password),
    ]
  );
  '
fi

php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

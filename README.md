## Car Dealer Backend

Laravel 10 + Filament admin backend for the Dealer Car Inventory POC. Provides the admin UI, API endpoints, and media storage.

## Requirements

- PHP 8.2+
- Composer
- MySQL (local dev) or SQLite (Render deploy)

## Local setup

1. Install PHP dependencies:

```
composer install
```

2. Copy environment config:

```
cp .env.example .env
```

3. Generate app key:

```
php artisan key:generate
```

4. Configure database in `.env` and run migrations:

```
php artisan migrate
php artisan storage:link
```

5. Create a Filament admin user:

```
php artisan make:filament-user
```

6. Start the backend:

```
php artisan serve
```

Admin panel: `http://localhost:8000/admin`

## API endpoints

- `GET /api/cars`
- `GET /api/cars/{slug}`
- `POST /api/admin/cars`
- `PUT /api/admin/cars/{id}`
- `DELETE /api/admin/cars/{id}`

## Filament Cars CMS

The Cars resource supports create, edit, delete, and media uploads (featured image and video).

## Render deployment (SQLite)

This repo includes a Docker-based Render setup.

1. Create a new Render service from the repo (Render detects the blueprint).
2. Update `APP_URL` in `render.yaml` after Render assigns the service URL.
3. Deploy and verify:

```
https://<service>.onrender.com/api/cars
```

The Docker start script initializes the SQLite file, runs migrations, and creates the storage symlink on boot.

# Academic Tracker

Academic Tracker is a full-stack school management system split into two applications in one repository.

- `frontend/`: React 19 + Vite single-page application.
- `laravel/`: Laravel 12 backend serving session-authenticated JSON endpoints under `/api/`.

## What The System Covers

The current codebase includes:

- staff and student dashboards
- student, parent, teacher, and class management
- class activities and per-student activity logging
- admin settings for users, grades, subjects, terms, and activity types
- finance modules for fee types, fee structures, student fees, payments, arrears, and payment plans
- collaboration modules for group chat, direct messages, and class-group sync

## Requirements

- Node.js 20+
- npm 10+
- PHP 8.2+
- Composer 2+

## Project Layout

```text
.
|-- frontend/
|-- laravel/
`-- .github/workflows/
```

## Runtime Layout

- Frontend dev server: `http://localhost:3000`
- Backend dev server: `http://localhost:8000`
- Frontend proxies `/api/*` requests to the Laravel backend during development.
- Authentication is session-based and uses a Django-style `csrftoken` cookie plus `X-CSRFToken` header handling.

## Local Development

### Frontend

From `frontend/`:

```bash
npm ci
npm run dev
```

Production build:

```bash
npm run build
```

### Laravel Backend

From `laravel/`:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

PowerShell equivalent for copying the env file:

```powershell
Copy-Item .env.example .env
```

The default database configuration is SQLite and resolves to `laravel/database/database.sqlite` when `DB_DATABASE` is not set.

### Laravel Concurrent Dev Mode

From `laravel/`:

```bash
composer dev
```

This starts the Laravel server, queue listener, log tailing via Pail, and Vite dev mode together.

### Tests And Builds

From `laravel/`:

```bash
composer test
npm run build
```

## Seeded Defaults

The backend seeder creates:

- roles: `admin`, `student`, `teacher`, `parent`
- default admin: `admin@example.com` / `12345`
- grades: `Grade 1` to `Grade 3`, `Form 1` to `Form 3`
- subjects: `Art`, `English`, `Mathematics`, `Shona`
- term: `2024 Term 1`
- activity types: `Homework`, `Attendance`, `Study Material`
- finance fee types: `Tuition`, `Registration`, `Laboratory`, `Sports`, `Library`

## CI

GitHub Actions runs CI on pushes and pull requests to `main`.

- Frontend job: installs dependencies and runs the Vite production build.
- Laravel job: installs Composer and npm dependencies, prepares `.env`, generates an app key, runs `composer test`, and builds Laravel assets.
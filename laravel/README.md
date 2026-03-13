# Academic Tracker Laravel Backend

This folder contains the Laravel 12 backend for Academic Tracker. The app exposes JSON endpoints under `/api/`, uses session-backed authentication, and supports the academic, finance, and collaboration modules consumed by the React frontend.

## Backend Scope

The backend currently includes:

- authentication and password reset
- staff and student dashboards
- student, parent, teacher, and class management
- activity creation, activity logging, and student assignment views
- admin settings for users, grades, subjects, terms, and activity types
- finance APIs for fee types, fee structures, student fees, payments, arrears, special fees, and payment plans
- collaboration APIs for inboxes, groups, direct messages, membership management, and class-group sync

## Routing Model

- JSON API endpoints are defined in `routes/web.php` under the `/api/` prefix.
- The root `/` route returns a backend health-style JSON message.
- Protected endpoints use the standard Laravel `auth` middleware plus profile-role middleware where needed.

## Run

From this folder:

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

The default database connection is SQLite and resolves to `database/database.sqlite` unless `DB_DATABASE` is overridden.

## Useful Commands

```bash
composer dev
composer test
npm run build
```

- `composer dev` runs the app server, queue listener, Pail log tailing, and Vite together.
- `composer test` runs the PHPUnit suite with SQLite in memory.
- `npm run build` compiles Laravel frontend assets.

## Auth And Frontend Integration

- Session auth is used for protected routes.
- Middleware mirrors Laravel's CSRF token into a Django-style `csrftoken` cookie and accepts `X-CSRFToken` headers.
- API CORS headers allow credentialed requests from `http://localhost:3000`.
- The React frontend talks to this backend through `/api/*` and sends cookies with requests.

## Authorization Notes

- Staff dashboard, class, parent, student, and activity routes are restricted to `admin` and `teacher` profiles where appropriate.
- Settings and teacher-management routes are admin-only.
- Student portal routes are student-only.
- Finance endpoints are currently authenticated-only rather than role-gated, which is preserved by the test suite.

## Seeded Defaults

The default seeder creates:

- roles: `admin`, `student`, `teacher`, `parent`
- default admin user: `admin@example.com` / `12345`
- grades: `Grade 1`, `Grade 2`, `Grade 3`, `Form 1`, `Form 2`, `Form 3`
- subjects: `Art`, `English`, `Mathematics`, `Shona`
- term: `2024 Term 1`
- activity types: `Homework`, `Attendance`, `Study Material`
- fee types: `Tuition`, `Registration`, `Laboratory`, `Sports`, `Library`

## Test Coverage Snapshot

The current feature smoke tests cover:

- seeded admin login and `/api/auth/me/`
- finance dashboard access for authenticated students
- the current student activity-history behavior
- collaboration message sync behavior

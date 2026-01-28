# Academic Tracker Backend

A comprehensive academic tracking system built with Laravel. This application serves as the backend for managing students, grades, subjects, and terms.

## Features

- **Admin Dashboard**: Manage users, roles, and system settings.
- **Student Dashboard**: Students can view their marks, assignments, and academic progress.
- **Grade Management**: Track student performance across different subjects and terms.
- **Role-Based Access Control**: Secure access for Admins, Teachers, and Students.

## Getting Started

Follow these steps to set up the project locally.

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/jaggerjack61/academic-tracker-back.git
   cd academic-tracker-back
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure the environment**
   Copy the example environment file and configure your database settings.
   ```bash
   cp .env.example .env
   ```
   Open `.env` and update the `DB_` variables to match your local MySQL configuration.

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations and seed the database**
   This step is crucial to set up the default admin account.
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the application**
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`.

## Default Credentials

The database seeder creates a default Admin account for you to log in with.

- **Email:** `admin@example.com`
- **Password:** `12345`

> [!NOTE]
> These credentials are defined in `database/seeders/UserSeeder.php`. It is recommended to change the password after your first login.

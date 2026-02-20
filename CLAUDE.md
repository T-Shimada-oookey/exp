# CLAUDE.md

This file provides guidance for AI assistants working on this codebase.

## Project Overview

This is a **Laravel 12** application ("exp") used as a learning/experimentation project. It demonstrates Laravel patterns including interfaces, dependency injection, enums, and the MVC architecture. The app runs in a Dockerized environment (PHP-FPM + Nginx + MySQL).

## Repository Structure

```
exp/
├── compose.yml           # Docker Compose (app, web, db, mail services)
├── developTest.js        # Standalone JS debugging utility (not part of Laravel)
├── docker/
│   ├── app/              # PHP 8.3-FPM container + Node.js 20 + Composer
│   ├── db/               # MySQL container (Asia/Tokyo timezone)
│   └── web/              # Nginx 1.26-alpine container
├── .github/workflows/
│   ├── laravel-CI-Level-0.yml  # CI: runs on PRs/pushes to `develop`
│   └── manual.yml              # Manual workflow dispatch
└── src/                  # Laravel application root (mounted to /app in containers)
    ├── app/
    │   ├── Contracts/    # Interfaces (DiscountCalculatorInterface)
    │   ├── Enums/        # PHP enums (PayBackPoints)
    │   ├── Http/Controllers/trial/  # Feature controllers
    │   ├── Models/       # Eloquent models (User, PayManagements)
    │   ├── Providers/    # AppServiceProvider (IoC bindings)
    │   └── Services/     # Concrete service implementations
    ├── database/migrations/
    ├── resources/views/  # Blade templates
    ├── routes/web.php
    └── tests/            # PHPUnit tests (Unit + Feature)
```

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.3 |
| Framework | Laravel 12 |
| Web Server | Nginx 1.26 |
| PHP Server | PHP-FPM 8.3 |
| Database | MySQL (Docker) |
| Frontend Build | Vite + Node.js 20 |
| Testing | PHPUnit 11 |
| Code Style | Laravel Pint |
| Mail (dev) | MailHog |

## Development Environment Setup

All development runs inside Docker containers. The `src/` directory is bind-mounted to `/app` inside the containers.

### Starting the environment

```bash
# From the repository root (where compose.yml lives)
docker compose up -d

# Access the app at http://localhost:80
# MailHog UI at http://localhost:8025
# Vite dev server at http://localhost:5173
```

### Running commands inside the app container

```bash
docker compose exec app bash

# Then inside the container:
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
```

### Local development without Docker (SQLite)

The `.env.example` defaults to SQLite. To run locally:

```bash
cd src/
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev   # starts server + queue + logs + vite concurrently
```

## Running Tests

Tests live in `src/tests/`. Run from inside the `src/` directory or app container:

```bash
# Run all tests
php artisan test

# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature

# Via PHPUnit directly
./vendor/bin/phpunit
```

**Note:** `phpunit.xml` has SQLite in-memory config commented out. Tests may require a running database or adjusting the env. In CI, `php artisan about` is used as a smoke test rather than running the full test suite.

## Code Style

This project uses **Laravel Pint** for PHP formatting (PSR-12 compatible):

```bash
# From src/ directory
./vendor/bin/pint

# Check only (no changes)
./vendor/bin/pint --test
```

## Key Application Concepts

### Dependency Injection via Interfaces

The project demonstrates Laravel's IoC container with the discount calculator pattern:

- **Interface**: `app/Contracts/DiscountCalculatorInterface.php` — defines `calculateDiscount(float $amount): float`
- **Implementation**: `app/Services/DefaultDiscountCalculator.php` — 5% discount under 1000, 10% at/above 1000
- **Binding**: `app/Providers/AppServiceProvider.php` registers the binding:
  ```php
  $this->app->bind(DiscountCalculatorInterface::class, DefaultDiscountCalculator::class);
  ```
- **Consumer**: `app/Http/Controllers/trial/InterfaceController.php` receives it via constructor injection

To add a new discount strategy: implement `DiscountCalculatorInterface` in a new service class, then update the binding in `AppServiceProvider`.

### Member Rank System

- **Enum**: `app/Enums/PayBackPoints.php` — `Bronze`/`Silver`/`Gold` backed by string `'0'`/`'1'`/`'2'`
- **Point rates**: Bronze=1%, Silver=5%, Gold=10% (via `MembersRankPoint()` method)
- **Database**: `member_rank` column on `users` table (tinyInteger), cast to `PayBackPoints` enum in the `User` model
- Migration: `database/migrations/2025_06_03_013734_update_users.php`

### Routes

Defined in `src/routes/web.php`:

```
GET  /                          → welcome view
GET  /trial/interface           → InterfaceController@index  (shows member ranks + discount form)
POST /trial/interface           → InterfaceController@store  (returns discount amount)
```

Named route prefix: `trial.interface.*` (e.g., `route('trial.interface.store')`)

## Database

### Docker credentials (from `compose.yml`)

```
Host:     db (container name)
Port:     3306
Database: database
User:     user
Password: password
Root PW:  password
```

### Migrations (in order)

| Migration | Description |
|-----------|-------------|
| `0001_01_01_000000_create_users_table` | Standard Laravel users table |
| `0001_01_01_000001_create_cache_table` | Cache storage |
| `0001_01_01_000002_create_jobs_table` | Queue jobs |
| `2025_03_03_022754_create_orders_table` | Orders table (stub, columns TBD) |
| `2025_06_03_013414_create_pay_managements_table` | Pay managements table (stub) |
| `2025_06_03_013734_update_users` | Adds `member_rank` tinyInteger to users |

```bash
# Run all migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Fresh with seeds
php artisan migrate:fresh --seed
```

## CI/CD

**File:** `.github/workflows/laravel-CI-Level-0.yml`

- **Triggers:** PRs to `develop` branch, or direct pushes to `develop`
- **Steps:** checkout → PHP 8.3 setup → Composer install (cached) → copy `.env` → generate app key → `php artisan about`
- **Note:** The CI does not currently run PHPUnit tests; it verifies the app bootstraps correctly.

**Branching convention:** Feature work goes on `feature/v{version}/{description}` branches and merges into `develop`.

## Docker Container Details

| Service | Image | Ports | Notes |
|---------|-------|-------|-------|
| `app` | `php:8.3-fpm` (custom) | 5173 | PHP-FPM + Node.js 20 + Composer 2.7 |
| `web` | `nginx:1.26-alpine` (custom) | 80 | Proxies to `app` PHP-FPM |
| `db` | MySQL (custom) | 3306 | Persistent volume `exp-volume` |
| `mail` | `mailhog/mailhog` | 8025 (UI), 1025 (SMTP) | Mail stored in `maildir` volume |

## Conventions

- **Namespacing:** `App\` maps to `src/app/`, `Tests\` maps to `src/tests/`
- **Contracts** (interfaces) go in `app/Contracts/`
- **Service classes** (implementations) go in `app/Services/`
- **Enums** go in `app/Enums/`
- **Controller subgroups** are organized in subdirectories under `app/Http/Controllers/` (e.g., `trial/`)
- **Views** match controller grouping under `resources/views/` (e.g., `trial/interface.blade.php`)
- **Route groups** use `prefix` and `as` to namespace routes (e.g., prefix `trial`, as `trial.`)
- **Japanese comments** are common in this codebase; this is intentional

## Miscellaneous

- `developTest.js` in the repo root is a standalone Node.js debugging script demonstrating VSCode debugger usage. It is not part of the Laravel application.
- `.vscode/launch.json` is committed for shared debug configurations.
- Timezone is set to `Asia/Tokyo` in the PHP container and MySQL.

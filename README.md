# Course Platform

A **Laravel 12** web application for selling and managing online courses. It includes a public student-facing site, email **OTP** verification, checkout with **demo**, **Stripe**, or **PayPal** payments, course ratings and reviews, and an **Filament 5** admin panel.

---

## Features

### Public site and students

- Home page with categories and courses.
- Course search (`/search`).
- **Arabic / English** locale switching (session + `lang/ar.json` translations).
- Registration and login (based on **Laravel Breeze** with project-specific changes).
- **Email OTP** verification before accessing protected routes (`verified.otp` middleware).
- Profile (update details, password, delete account).
- **My courses** page after purchase.
- Text reviews and star ratings with authorization policies.
- **Checkout and enrollment**: discount codes, payment methods:
  - **demo**: instant completion for local development and tests (on by default in `local`; see `config/payments.php`).
  - **Stripe Checkout**.
  - **PayPal** (via `srmklive/paypal`).

### Admin panel (Filament)

- URL: **`/admin`** (Filament login).
- Only users with role **`admin`** (`User::ROLE_ADMIN`) can access the panel.
- CRUD-style management for users, categories, courses, discount codes, enrollments, and reviews.
- Dashboard widgets: overview stats, new users chart, revenue chart.

### Quality and tests

- Feature tests for authentication, OTP, profile, search, and checkout flows (`tests/`).

---

## Requirements

| Tool | Notes |
|------|--------|
| PHP | ^8.2 |
| Composer | Recent version |
| Node.js + npm | For Vite and frontend assets |
| Database | **MySQL** (recommended) or **SQLite** for quick local trials |

---

## Quick install

From the project root:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env` (MySQL or SQLite; see comments in `.env.example`).

```bash
php artisan migrate
# Optional: rich demo data (categories, courses, payments, enrollments)
php artisan db:seed
npm install
npm run build
php artisan serve
```

You can also use the Composer script (creates `.env` if missing, generates key, migrates, builds assets):

```bash
composer run setup
```

---

## Daily development

Run the app server, Vite, queue worker, and logs together:

```bash
composer run dev
```

Or manually:

```bash
php artisan serve
npm run dev
```

In local development, the **demo** payment option is usually available on checkout without Stripe/PayPal keys. See `config/payments.php` and the environment variables below.

---

## Demo accounts after `db:seed`

| Role | Email | Password (factory default) |
|------|-------|------------------------------|
| Admin (Filament + same user record) | `admin@example.com` | `password` |
| Sample student | `student@example.com` | `password` |

> Change passwords immediately in any non-local environment.

---

## Important environment variables

Copy from `.env.example` and adjust for your environment:

| Variable | Description |
|----------|-------------|
| `APP_URL` | Application URL (important for Stripe/PayPal return URLs) |
| `DB_*` | MySQL settings, or switch to SQLite as documented in `.env.example` |
| `MAIL_*` | Outbound mail (required for OTP in production; often `log` locally) |
| `PAYMENT_GATEWAY` | `demo`, `stripe`, or `paypal` |
| `PAYMENT_DEMO_ENABLED` | Show or hide the demo payment option (defaults depend on `APP_ENV`) |
| `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET` | Stripe integration |
| `PAYPAL_MODE`, `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET` (and sandbox/live variants per package docs) | PayPal integration |

The `.env` file is **not** committed (see `.gitignore`). Share configuration with your team via `.env.example` only.

---

## Tests

```bash
composer test
```

or:

```bash
php artisan test
```

---

## Project layout (summary)

```
app/
  Filament/          # Filament resources and admin UI
  Http/Controllers/  # Public site, auth, courses, checkout, search
  Http/Middleware/   # Locale, OTP verification gate
  Models/            # User, Course, Category, Enrollment, Payment, ...
  Services/          # OTP, course pricing, payment gateways
config/
  payments.php       # Payment gateway and demo settings
database/
  migrations/        # Schema
  seeders/           # DatabaseSeeder
resources/
  views/             # Blade (public, auth, courses, checkout)
routes/
  web.php            # Web routes (public + authenticated)
  auth.php           # Breeze-style auth routes
```

---

## Notable routes

| Path | Description |
|------|-------------|
| `/` | Home |
| `/search` | Search |
| `/categories/{category}` | Category |
| `/courses/{course}` | Course details |
| `/login`, `/register` | Authentication |
| `/verify-email-otp` | OTP entry after registration |
| `/my-courses` | Purchased courses (after verification) |
| `/courses/{course}/checkout` | Payment and enrollment |
| `/admin` | Filament admin (admins only) |

---

## License

This project builds on Laravel and third-party packages; see `composer.json` for package licenses. The default Laravel application skeleton is **MIT** unless stated otherwise.

---

## Contributing / coursework notes

Document database changes with migrations, run tests before submission, and never commit payment secrets or a real `.env` to a public repository.

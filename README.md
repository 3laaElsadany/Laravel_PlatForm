# Course Platform

A **Laravel 12** web application for an online course marketplace and learning platform. This repository implements a full-stack website with:

- a public student-facing course catalog,
- registration/login with email **OTP verification**,
- checkout and enrollment flows,
- ratings and reviews,
- and a **Filament 5** admin panel for managing courses, categories, users, payments, and reviews.

---

## Project details

- Project type: Laravel web application
- Main purpose: course marketplace + learning management for students and admins
- Languages: PHP, Blade, JavaScript
- Admin UI: Filament 5
- Payment options: demo, Stripe, PayPal
- Localization: Arabic and English
- Auth: Laravel Breeze with custom OTP middleware

---

## Core features

### Student experience

- Browse categories and course listings
- Search courses by keyword
- Arabic / English locale switching
- Register and log in with email/password
- Complete email **OTP verification** before accessing protected content
- Update profile, password, and delete account
- View enrolled courses on **My Courses** page
- Submit reviews and star ratings for completed courses
- Checkout with discount codes and multiple payment gateways

### Payments and enrollment

- Demo payment gateway for local development and testing
- Stripe Checkout integration
- PayPal integration using `srmklive/paypal`
- Enrollment records created after successful checkout
- Payment history and statuses stored in the database

### Admin panel

- Accessible via **`/admin`**
- Restricted to users with role **`admin`** (`User::ROLE_ADMIN`)
- Manage users, categories, courses, discount codes, enrollments, and reviews
- Filament dashboard widgets for stats, revenue, and recent activity

### Testing and quality

- Automated feature tests under `tests/` for authentication, OTP verification, search, checkout, and user flows
- Uses Laravel best practices for middleware, policies, and service classes

---

## Requirements

| Tool          | Notes                                                     |
| ------------- | --------------------------------------------------------- |
| PHP           | ^8.2                                                      |
| Composer      | Latest stable version                                     |
| Node.js + npm | For compiling frontend assets with Vite                   |
| Database      | MySQL recommended, SQLite supported for quick local setup |
| Mail driver   | `log` for local OTP testing, real SMTP for production     |

---

## Setup instructions

From the project root:

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Configure your database and mail settings in `.env`.

Run migrations and optional demo seed data:

```bash
php artisan migrate
php artisan db:seed
```

Install frontend dependencies and build assets:

```bash
npm install
npm run build
```

Run the application locally:

```bash
php artisan serve
```

Optional one-step setup script:

```bash
composer run setup
```

---

## Development commands

For local development with automatic asset rebuilding:

```bash
composer run dev
```

Or manually:

```bash
php artisan serve
npm run dev
```

---

## Demo accounts

After running `php artisan db:seed`, use these example accounts:

| Role    | Email                 | Password   |
| ------- | --------------------- | ---------- |
| Admin   | `admin@example.com`   | `password` |
| Student | `student@example.com` | `password` |

> تأكد من تغيير كلمات المرور قبل النشر أو التسليم.

---

## Environment variables

Important variables to configure in `.env`:

| Variable                                                                                     | Description                                             |
| -------------------------------------------------------------------------------------------- | ------------------------------------------------------- |
| `APP_URL`                                                                                    | Application URL used by redirects and payment callbacks |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`           | Database connection settings                            |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION` | Email configuration for OTP messages                    |
| `PAYMENT_GATEWAY`                                                                            | `demo`, `stripe`, or `paypal`                           |
| `PAYMENT_DEMO_ENABLED`                                                                       | Enable the demo gateway in local mode                   |
| `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`                                       | Stripe integration settings                             |
| `PAYPAL_MODE`, `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`                                           | PayPal integration settings                             |

---

## Testing

Run the test suite with either command:

```bash
composer test
```

or:

```bash
php artisan test
```

---

## Project structure

```
app/
  Filament/          # Filament resources and admin UI
  Http/Controllers/  # Controllers for web, auth, courses, checkout
  Http/Middleware/   # Locale switching, OTP verification, auth checks
  Models/            # User, Course, Category, Enrollment, Payment, Review, ...
  Services/          # Business logic for OTP, pricing, payments, and enrollment
config/
  payments.php       # Payment gateway and demo settings
database/
  migrations/        # Database schema migrations
  seeders/           # Database seeding logic
resources/
  views/             # Blade templates for public pages and auth
routes/
  web.php            # Main web routes
  auth.php           # Auth routes and guards
```

---

## Useful routes

| Path                         | Description              |
| ---------------------------- | ------------------------ |
| `/`                          | Home page                |
| `/search`                    | Search courses           |
| `/categories/{category}`     | View courses by category |
| `/courses/{course}`          | Course details           |
| `/login`                     | Login page               |
| `/register`                  | Registration page        |
| `/verify-email-otp`          | OTP verification page    |
| `/my-courses`                | Enrolled courses         |
| `/courses/{course}/checkout` | Checkout page            |
| `/admin`                     | Filament admin panel     |

---

## License

This project is based on Laravel and third-party packages. See `composer.json` for package licensing details. The default Laravel skeleton uses the **MIT** license.

---

## Notes

- Do not commit your real `.env` file or payment secrets.
- Use migrations and seeders to document database changes.
- Run tests before submitting or deploying the project.

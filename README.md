# DVC API

Backend for the Delta V Creative website. It receives contact-form leads, emails them to the admin and has a Filament admin panel for working through them.

- **Stack:** Laravel 13, PHP 8.5, Filament 5. It's hosted on Laravel Vapor (`php-8.5:al2023`) at `api.deltavcreative.com`.
- **Lead API:** `POST /api/lead` takes `name`, `email`, `phone` and `message`. In production it only accepts requests whose `Origin` is `deltavcreative.com`.
- **Admin:** `/admin`. Only the user whose email matches `ADMIN_EMAIL` can sign in.

## Local setup

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
npm ci && npm run build
php artisan test
```

To create an admin user, set `ADMIN_EMAIL` in `.env`, then run `php artisan make:filament-user` with that same email.

## CI / deployment

`.github/workflows/ci.yml` runs Pint and the test suite on every pull request and on every push to `master`. When a push to `master` passes, it deploys the `production` Vapor environment with `vapor deploy`.

It needs this repository secret: **`VAPOR_API_TOKEN`** (create one at vapor.laravel.com → Team Settings → API).

The deploy job runs in a GitHub `production` environment, so you can add a required reviewer there to gate deploys.

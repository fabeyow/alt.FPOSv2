<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## alt.FPOS database setup

Configure your database in `.env`, then run:

```sh
php artisan migrate --seed
php artisan serve
```

The default connection is SQLite (`database/database.sqlite`). Create that file if it does not exist. After registering and logging in, open `/customers` and `/users` to see five sample accounts in each table. Repeating the seeder does not duplicate sample records or overwrite edited accounts.

The schema follows [TFA2](https://github.com/fabeyow/alt.FPOS/commit/61c80d0f5ddec766db20c191dec303b3f93824fc): customers have `full_name`, `email`, nullable `phone`, and `created_at`; users have unique `username`, `full_name`, `role` (default `Staff`), and `created_at`. Neither model writes `updated_at`. The existing table layout and styling are retained; the users table displays username instead of email to match TFA2.

The historical users migration upgrades Laravel's starter table, preserving IDs and names and assigning existing rows a `user_{id}` username. It removes the original authentication fields. The new authentication migration adds nullable unique email, password, email verification, and remember-token fields while preserving existing POS records. Neither model writes `updated_at`. Rolling back the new migration removes those credentials; it does not delete accounts.

Run `php artisan test` to verify database-backed pages, seeding, constraints, and migration upgrade/rollback behavior using an isolated in-memory SQLite database.

## Account forms and avatars

Run `composer install`, enable PHP's GD extension (`extension=gd` in the loaded `php.ini`), then run:

```sh
php artisan migrate
php artisan storage:link
```

Set `APP_URL` in `.env` to the application's public URL so avatar links resolve correctly. PHP's `upload_max_filesize` must allow at least 2MB, and `post_max_size` must be larger to allow multipart form overhead.

Use `/customers/create` and `/users/create`, or the Create buttons on the listing pages. Each row has an Edit link. Forms retain submitted values and show validation errors. Customer names and valid emails are required; usernames must be unique and user names are required. Editing a user allows their existing username.

User edit pages accept image uploads up to 2MB. [Intervention Image v3](https://image.intervention.io/v3) with GD crops and re-encodes uploads into 256 × 256 JPEG thumbnails. Confirm this package meets your instructor's approved package list. Only the generated `avatars/*.jpg` path is stored in `users.avatar`; files use Laravel's public storage disk. A successful replacement removes the previous avatar, and submitting without a new image preserves it. Users without an avatar display a bundled placeholder.

Run `php artisan test` with GD enabled to verify the forms, validation, thumbnail dimensions, replacement, and listing display.

## Resource routes and authentication

[Laravel Breeze](https://github.com/laravel/breeze) provides the Blade login, registration, logout, password reset, and profile screens. Install dependencies and prepare the application:

```sh
composer install
npm install
npm run build
php artisan migrate
php artisan serve
```

Visit `/register` and provide a unique username, full name, unique email, and confirmed password (at least eight characters). Registration signs you in with the default `Staff` role. Later, sign in at `/login` using your email and password. Use Log Out in the navigation to end the session. The dashboard links to both account lists.

Existing seeded accounts and records created through the POS user form retain their original fields and have no login credentials. Register a new account with a different username to sign in; no shared password is assigned to existing records. Password reset email requires a configured mail transport in `.env`; the default log mailer writes messages locally.

Each controller uses the conventional `index`, `create`, `store`, `edit`, and `update` methods. Each resource has one `Route::resource(...)->only(...)` declaration covering those existing actions. List routes are named `customers.index` and `users.index`; forms use the conventional `.create`, `.store`, `.edit`, and `.update` names. All ten resource routes use Laravel's `auth` middleware. Home and About remain public. Every registered user can manage both resources; role-based permissions are outside this activity.

Verify access control manually:

1. Log out, then visit `/customers` or `/users`. Each redirects to `/login`.
2. Log in with your registered account. Laravel returns you to the requested page, which displays normally.
3. Open the Create and Edit pages and save a record to verify authenticated form access.
4. Log out and revisit a protected page to confirm access is blocked again.

Run `php artisan test` for the automated checks. `AccountAccessTest` covers guest redirects for every resource action (including both PUT and PATCH), blocked guest writes, login return paths, authenticated listings, logout, registration validation, password hashing, and preservation of existing accounts during migration. Existing account tests now authenticate before accessing protected pages. Tests use an isolated in-memory SQLite database and do not require a running Vite server. With CLI GD disabled but installed, run `php -d extension=gd vendor/phpunit/phpunit/phpunit`.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

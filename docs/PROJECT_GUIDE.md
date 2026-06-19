# Kimmex Project Guide

This guide explains how the whole project is organized, how to run it locally, how the frontend build works, and how to deploy it on normal hosting or old cPanel hosting without Node.js or terminal access.

## Project Overview

Kimmex is a Laravel 13 corporate website with a Filament 5 admin panel.

- Public website: Blade views in `resources/views`
- Admin panel: Filament resources in `app/Filament`
- Backend models and business data: `app/Models`
- Forms and media controllers: `app/Http/Controllers`
- Frontend source assets: `resources/css/app.css` and `resources/js/app.js`
- Compiled frontend assets: `public/build`
- Static public files: `public/images`, `public/docs`, `public/logo.png`
- Database schema: `database/migrations`
- Seed data: `database/seeders`

## Main Features

- Public pages for home, about, services, projects, news, careers, documents, privacy, and contact
- Filament admin panel for managing content
- Multilingual content using English and Khmer translations
- Dynamic organization profile, theme, footer, and brand settings
- Contact form and career application form
- Public document library
- Project categories, project images, services, partners, testimonials, employees, departments, and org chart data
- SEO routes for `robots.txt` and `sitemap.xml`

## Important Folders

```text
app/Filament                  Admin panel resources, pages, widgets, forms, tables
app/Http/Controllers          Public form and media controllers
app/Models                    Eloquent models
app/Providers                 Shared app boot logic and view data
app/Support                   Helper classes
database/migrations           Database structure
database/seeders              Initial/demo data
public                        Web-accessible files
resources/css/app.css         Tailwind v4 and design system CSS
resources/js/app.js           Alpine.js frontend entry
resources/views               Blade templates and components
routes/web.php                Public website routes
scripts/prepare-cpanel-zip.sh Deployment packaging helper
docs                          Project documentation
```

## Local Development Requirements

Use these locally on your computer:

- PHP 8.3 or newer
- Composer 2
- Node.js and npm
- MySQL/MariaDB or SQLite for local testing

Install dependencies:

```bash
composer install
npm install
```

Create local environment:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

Start development:

```bash
composer run dev
```

This starts the queue listener and Vite dev server together.

If you only need Vite:

```bash
npm run dev
```

## Frontend Build

The frontend source files are:

```text
resources/css/app.css
resources/js/app.js
```

The production build command is:

```bash
npm run build
```

That creates compiled files in:

```text
public/build
```

Laravel uses `@vite(['resources/css/app.css', 'resources/js/app.js'])` in the main layout. In production, Laravel reads `public/build/manifest.json` and loads the compiled CSS/JS.

## Important Vite Rule

Do not upload this file to production:

```text
public/hot
```

`public/hot` is created only by `npm run dev`. If it exists on production, Laravel may try to load CSS/JS from a local dev server such as:

```text
http://127.0.0.1:5173
```

That will break the frontend on cPanel or live hosting.

## When To Rebuild Frontend

Run `npm run build` when you change:

- `resources/css/app.css`
- `resources/js/app.js`
- Tailwind class usage that must be included in compiled CSS
- Vite config
- frontend dependencies in `package.json`

You usually do not need to rebuild when you only change:

- Blade HTML text
- PHP controller logic
- database data
- Filament form/table definitions

For old hosting, always rebuild locally and upload the new `public/build` folder.

## Public Routes

Core public routes are in `routes/web.php`.

```text
/                       Home
/about                  About
/services               Service listing
/services/{slug}        Service detail
/projects               Project listing
/projects/{slug}        Project detail
/news                   News listing
/news/{slug}            News detail
/careers                Career listing
/careers/{slug}         Job detail
/documents              Public document library
/documents/{slug}       Document detail
/contact                Contact page
/privacy-policy         Privacy page
/lang/en                Switch to English
/lang/km                Switch to Khmer
/robots.txt             SEO robots file
/sitemap.xml            SEO sitemap
```

## Admin Panel

The admin panel is powered by Filament.

Common admin code locations:

```text
app/Filament/Resources
app/Filament/Pages
app/Filament/Widgets
resources/views/filament
```

Typical managed content includes:

- Projects and project categories
- Services
- News articles
- Documents and document categories
- Careers and job applications
- Inquiries
- Employees, departments, and org chart
- Partners
- Testimonials
- System settings
- Users
- Activity logs

## Environment Files

Local development uses:

```text
.env
```

Base examples:

```text
.env.example
.env.cpanel.example
```

Never commit the real `.env` file.

For production, important values are:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
QUEUE_CONNECTION=sync
CACHE_STORE=database
SESSION_DRIVER=database
FILESYSTEM_DISK=public
PUBLIC_UPLOADS_DISK=public
```

For old shared hosting, `QUEUE_CONNECTION=sync` is usually the safest choice because there may be no queue worker.

## Storage And Uploads

The project can use public local storage or Cloudflare R2.

For cPanel/simple hosting without terminal access, use direct public uploads:

```env
FILESYSTEM_DISK=cpanel_public
PUBLIC_UPLOADS_DISK=cpanel_public
```

This stores admin uploads in:

```text
public/uploads
```

That avoids Laravel's `php artisan storage:link` requirement, which is painful on cPanel accounts without SSH or Terminal.

For VPS/SSH hosting, you can still use the normal Laravel public disk:

```env
FILESYSTEM_DISK=public
PUBLIC_UPLOADS_DISK=public
```

Then run:

```bash
php artisan storage:link
```

## Database

For local development:

```bash
php artisan migrate
php artisan db:seed
```

For production:

```bash
php artisan migrate --force
```

If cPanel has no terminal, export/import the database through phpMyAdmin.

Recommended production flow:

1. Create MySQL database and user in cPanel.
2. Import the SQL dump in phpMyAdmin.
3. Set the same database credentials in `.env`.
4. If you add new migrations later, run them locally/staging and import the updated database, or use hosting support/cron/SSH if available.

## Deployment With Terminal Access

On a server with SSH/terminal:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Make sure the web document root points to:

```text
public
```

## Deployment To Old cPanel Without Node.js Or Terminal

This project can run on old cPanel hosting without Node.js if you prepare everything locally first.

### Prepare Locally

From your computer:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Make sure these exist before upload:

```text
vendor
public/build
.env
```

Make sure this does not exist before upload:

```text
public/hot
```

### Upload Layout Option A: Best Setup

If cPanel lets you choose the domain document root, upload the project and point the domain to:

```text
/home/CPANEL_USER/kimmex_app/public
```

This is the cleanest Laravel setup.

### Upload Layout Option B: Fixed `public_html`

If cPanel forces the domain to use `public_html`:

```text
/home/CPANEL_USER/kimmex_app       Laravel app files
/home/CPANEL_USER/public_html      Contents of kimmex_app/public
```

Then edit `public_html/index.php` so it loads Laravel from the app folder:

```php
require __DIR__.'/../kimmex_app/vendor/autoload.php';
$app = require_once __DIR__.'/../kimmex_app/bootstrap/app.php';
```

### Files To Upload

Upload these:

```text
app
bootstrap
config
database
public
resources
routes
storage
vendor
artisan
composer.json
composer.lock
package.json
package-lock.json
.env
```

Do not upload:

```text
node_modules
public/hot
.git
.env.example as the real production env
tests
```

You may upload `tests` if you want a full source backup, but it is not needed to run the live site.

## Packaging Helper

There is an existing helper:

```bash
bash scripts/prepare-cpanel-zip.sh
```

It builds frontend assets, prepares Composer dependencies, removes local-only files, and writes:

```text
deploy/kimmex-app.zip
deploy/kimmex-public-html.zip
```

Use `kimmex-app.zip` for the Laravel app folder. Use `kimmex-public-html.zip` only when cPanel forces the website to run from `public_html`; it contains the public files with `index.php` already pointed at `../kimmex_app`.

Use this when you want a repeatable cPanel package. See also:

```text
docs/CPANEL_DEPLOYMENT.md
```

## Less Painful Upload Workflow

For old cPanel, avoid uploading thousands of files one by one.

Recommended first upload:

1. Run `bash scripts/prepare-cpanel-zip.sh` locally.
2. Upload `deploy/kimmex-app.zip` to your account root, not inside `public_html`.
3. Extract it so you have:

```text
/home/CPANEL_USER/kimmex_app
```

4. If your domain document root can point to `kimmex_app/public`, stop there and point the domain to that folder.
5. If your host forces `public_html`, upload `deploy/kimmex-public-html.zip` into `public_html` and extract it there.
6. Create `.env` on the server from `.env.cpanel.example`.
7. Import database with phpMyAdmin.

For normal content/admin updates, do not upload code. Use the Filament admin panel.

For frontend-only CSS/JS updates:

1. Run `npm run build` locally.
2. Upload only `public/build` to the server.
3. Make sure `public/hot` is not on the server.

For Blade/PHP code updates:

1. Upload only changed files when possible.
2. If many files changed, rerun `bash scripts/prepare-cpanel-zip.sh` and upload the zip again.
3. Avoid replacing `.env` and uploaded storage files.

## Update Workflow For cPanel

For a Blade-only text/layout change:

1. Edit files locally.
2. Upload changed Blade/PHP files.
3. If caching is enabled and no terminal exists, clear Laravel cache by deleting cached files from `bootstrap/cache` carefully, or use hosting tools if available.

For CSS/JS changes:

1. Edit locally.
2. Run `npm run build`.
3. Upload changed source files if you keep source on server.
4. Upload the whole `public/build` folder.
5. Confirm `public/hot` is not on the server.

For database/content changes:

1. Prefer using the Filament admin panel.
2. For schema changes, add a new migration.
3. On no-terminal hosting, apply schema changes through phpMyAdmin or deploy an updated database dump.

## Verification Checklist

After deployment, check:

- Home page loads CSS and JS correctly
- Browser dev tools show no missing `public/build` asset
- `public/hot` is absent
- `/admin` loads
- Login works
- Images load from `public/images` or storage
- Contact form submits
- Career application submits with file upload
- `/sitemap.xml` loads
- `/robots.txt` loads
- Language switcher works for English and Khmer
- Mobile menu opens and contact info is correct

## Common Problems

### Frontend has no styling

Likely causes:

- `public/build` was not uploaded
- `public/build/manifest.json` is missing
- `public/hot` exists and points to a dev server
- File permissions prevent reading assets

### 500 error after upload

Likely causes:

- Wrong PHP version
- Missing `vendor`
- Wrong `.env`
- Missing `APP_KEY`
- Database credentials are wrong
- `storage` or `bootstrap/cache` is not writable

### Uploads do not display

Likely causes:

- `storage:link` was not created
- `FILESYSTEM_DISK` or `PUBLIC_UPLOADS_DISK` is wrong
- Uploaded files are not in the expected public storage path

### Contact form does not send email

Likely causes:

- Mail settings are wrong in `.env`
- Hosting blocks SMTP port
- `MAIL_FROM_ADDRESS` does not match the domain mailbox

## Safe Defaults For Shared Hosting

Use these production defaults unless there is a reason to change:

```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=sync
CACHE_STORE=database
SESSION_DRIVER=database
FILESYSTEM_DISK=cpanel_public
PUBLIC_UPLOADS_DISK=cpanel_public
LOG_LEVEL=error
```

## Developer Commands

```bash
npm run build
php artisan view:cache
php artisan view:clear
php artisan route:list
php artisan migrate
php artisan test
```

Use `npm run build` and `php artisan view:cache` before shipping frontend changes.

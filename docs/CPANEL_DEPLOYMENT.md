# cPanel Manual Deployment

## Server Requirements

- PHP 8.3
- MySQL or MariaDB
- PHP extensions: `ctype`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `intl`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `session`, `simplexml`, `tokenizer`, `xml`, `xmlreader`, `xmlwriter`, `zip`
- Composer 2 on the server, or upload the generated package that already includes `vendor`

## Build The Zip

From the project root:

```bash
bash scripts/prepare-cpanel-zip.sh
```

The zip is written to `deploy/kimmex-cpanel.zip`.

## Upload Layout

Recommended cPanel layout:

```text
home/CPANEL_USER/kimmex_app   Laravel project files from the zip
home/CPANEL_USER/public_html  contents of kimmex_app/public
```

If cPanel lets you set the domain document root, point it to:

```text
/home/CPANEL_USER/kimmex_app/public
```

If cPanel does not let you change the document root, keep the Laravel app outside `public_html`, copy everything inside `public/` into `public_html`, then update `public_html/index.php` paths from `../vendor/autoload.php` and `../bootstrap/app.php` to:

```php
require __DIR__.'/../kimmex_app/vendor/autoload.php';
$app = require_once __DIR__.'/../kimmex_app/bootstrap/app.php';
```

## Environment

1. Copy `.env.cpanel.example` to `.env` on the server.
2. Set `APP_URL`, database credentials, and mail credentials.
3. Generate or paste a production app key:

```bash
php artisan key:generate --force
```

## Database

Create a MySQL database and user in cPanel, then import `kimmex_backup.sql` if this is the production data dump. After import, run:

```bash
php artisan migrate --force
```

## Final Commands

Run these from the Laravel app directory on the server:

```bash
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

For shared hosting without a queue worker, keep `QUEUE_CONNECTION=sync`.

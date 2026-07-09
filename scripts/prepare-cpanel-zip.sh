#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
APP_BUILD_DIR="${ROOT_DIR}/deploy/build/kimmex_app"
PUBLIC_BUILD_DIR="${ROOT_DIR}/deploy/build/public_html"
APP_ZIP_PATH="${ROOT_DIR}/deploy/kimmex-app.zip"
PUBLIC_ZIP_PATH="${ROOT_DIR}/deploy/kimmex-public-html.zip"

cd "$ROOT_DIR"

rm -rf "${ROOT_DIR}/deploy/build" "$APP_ZIP_PATH" "$PUBLIC_ZIP_PATH"
mkdir -p "$APP_BUILD_DIR" "$PUBLIC_BUILD_DIR"

npm run build

rsync -a \
  --exclude='.git' \
  --exclude='.agents' \
  --exclude='.codex' \
  --exclude='.env' \
  --exclude='.env.backup' \
  --exclude='.env.production' \
  --exclude='.phpunit.result.cache' \
  --exclude='node_modules' \
  --exclude='deploy' \
  --exclude='docs' \
  --exclude='public/hot' \
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/testing/*' \
  --exclude='storage/framework/views/*' \
  --exclude='storage/logs/*' \
  --exclude='tests' \
  "$ROOT_DIR/" "$APP_BUILD_DIR/"

cd "$APP_BUILD_DIR"
composer install --no-dev --optimize-autoloader --no-interaction

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache
touch storage/framework/cache/data/.gitkeep storage/framework/sessions/.gitkeep storage/framework/testing/.gitkeep storage/framework/views/.gitkeep storage/logs/.gitkeep
mkdir -p public/uploads
touch public/uploads/.gitkeep

rsync -a "${APP_BUILD_DIR}/public/" "$PUBLIC_BUILD_DIR/"

php -r '
$path = $argv[1];
$contents = file_get_contents($path);
$contents = str_replace("__DIR__.\047/../vendor/autoload.php\047", "__DIR__.\047/../kimmex_app/vendor/autoload.php\047", $contents);
$contents = str_replace("__DIR__.\047/../bootstrap/app.php\047", "__DIR__.\047/../kimmex_app/bootstrap/app.php\047", $contents);
file_put_contents($path, $contents);
' "${PUBLIC_BUILD_DIR}/index.php"

cd "${ROOT_DIR}/deploy/build"
zip -qr "$APP_ZIP_PATH" kimmex_app
cd "$PUBLIC_BUILD_DIR"
zip -qr "$PUBLIC_ZIP_PATH" .

printf 'Created %s\n' "$APP_ZIP_PATH"
printf 'Created %s\n' "$PUBLIC_ZIP_PATH"

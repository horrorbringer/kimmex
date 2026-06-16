#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BUILD_DIR="${ROOT_DIR}/deploy/build/kimmex_app"
ZIP_PATH="${ROOT_DIR}/deploy/kimmex-cpanel.zip"

cd "$ROOT_DIR"

rm -rf "${ROOT_DIR}/deploy/build" "$ZIP_PATH"
mkdir -p "$BUILD_DIR"

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
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/testing/*' \
  --exclude='storage/framework/views/*' \
  --exclude='storage/logs/*' \
  --exclude='tests' \
  "$ROOT_DIR/" "$BUILD_DIR/"

cd "$BUILD_DIR"
composer install --no-dev --optimize-autoloader --no-interaction

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache
touch storage/framework/cache/data/.gitkeep storage/framework/sessions/.gitkeep storage/framework/testing/.gitkeep storage/framework/views/.gitkeep storage/logs/.gitkeep

cd "${ROOT_DIR}/deploy/build"
zip -qr "$ZIP_PATH" kimmex_app

printf 'Created %s\n' "$ZIP_PATH"

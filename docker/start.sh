#!/bin/sh

set -eu

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chmod -R 775 storage bootstrap/cache || true

exec php artisan serve --host 0.0.0.0 --port "${PORT:-10000}"

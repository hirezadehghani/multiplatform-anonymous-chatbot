#!/bin/sh
set -e

mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan config:clear
php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true

exec "$@"
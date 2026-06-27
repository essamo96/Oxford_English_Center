#!/usr/bin/env bash
# Run this once on the server, from the project root (public_html/),
# after every move/copy of the project to a new path or domain.
set -e

cd "$(dirname "$0")"

echo "Ensuring storage directories exist..."
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/framework/testing \
         storage/logs

echo "Fixing permissions..."
chmod -R 775 storage bootstrap/cache

echo "Clearing stale caches (may contain paths from the previous location)..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Re-linking public/storage..."
rm -f public/storage
php artisan storage:link

echo "Done. Check storage/logs/laravel.log if the site still errors."

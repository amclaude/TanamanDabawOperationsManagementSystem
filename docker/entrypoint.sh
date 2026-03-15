#!/bin/sh
set -e

cd /var/www/html

# No .env file needed - Laravel will use Render's environment variables directly
php artisan config:clear
php artisan key:generate --force 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan migrate --force

exec supervisord -c /etc/supervisord.conf
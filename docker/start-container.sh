#!/usr/bin/env sh
set -e

: "${PORT:=8080}"

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

# Ensure Vite does not try to use a local dev server URL in production
rm -f public/hot

php artisan config:cache || true
php artisan route:cache || true

# Run migrations only when explicitly enabled
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "RUN_MIGRATIONS=true, running migrations..."
    php artisan migrate --force --no-interaction || echo "Migration failed, but continuing startup so the container stays healthy."
else
    echo "RUN_MIGRATIONS is not true, skipping migrations."
fi

php-fpm -D
exec nginx -g 'daemon off;'

#!/usr/bin/env sh
set -e

: "${PORT:=8080}"

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

# In production, environment variables are passed directly; generate APP_KEY if needed
if [ -z "$APP_KEY" ] && [ ! -f .env ]; then
    php artisan key:generate --force --no-interaction || true
fi

php artisan config:cache || true
php artisan route:cache || true

# Run migrations in production if flag is set or if APP_ENV is production
if [ "${RUN_MIGRATIONS:-false}" = "true" ] || [ "$APP_ENV" = "production" ]; then
    php artisan migrate --force --no-interaction || true
fi

php-fpm -D
exec nginx -g 'daemon off;'

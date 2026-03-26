#!/usr/bin/env sh
set -e

: "${PORT:=8080}"

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ -f .env ]; then
    APP_KEY_VALUE=$(grep '^APP_KEY=' .env | cut -d '=' -f2- || true)
    if [ -z "$APP_KEY_VALUE" ]; then
        php artisan key:generate --force || true
    fi
fi

php artisan config:cache || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force || true
fi

php-fpm -D
exec nginx -g 'daemon off;'

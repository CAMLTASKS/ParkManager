#!/bin/sh
set -e

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
rm -rf storage/app/print-jobs

if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
    echo "Esperando MySQL en ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
    until php -r "new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" >/dev/null 2>&1; do
        sleep 2
    done
fi

php artisan optimize:clear >/dev/null 2>&1 || true
php artisan storage:link >/dev/null 2>&1 || true
php artisan migrate --force

if [ "${DOCKER_SEED_ON_START:-true}" = "true" ]; then
    php artisan db:seed --class=DockerSeeder --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"

#!/bin/sh

set -e

cd /var/www/html

echo "Instaliranje PHP zavisnosti..."

composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

chmod -R 777 storage
chmod -R 777 bootstrap/cache

if [ ! -f .env ]; then
    cp .env.example .env
fi

if grep -q "^APP_KEY=$" .env; then
    php artisan key:generate --force
fi

php artisan config:clear

echo "Laravel aplikacija se pokreće na portu 8000..."

exec php artisan serve --host=0.0.0.0 --port=8000

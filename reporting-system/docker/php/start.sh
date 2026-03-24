#!/bin/bash

# 1. Wait for MySQL to be available
echo "--- Waiting for MySQL to be ready... ---"
until mysqladmin ping -h"mysql" -u"reporting_user" -p"reporting_pass" --silent --skip-ssl; do
  echo "Waiting for database connection..."
  sleep 2
done
echo "--- MySQL is up! ---"

# 2. Setup Backend Dependencies (if missing)
cd /var/www/html/backend
if [ ! -d "vendor" ]; then
    echo "--- Installing composer dependencies... ---"
    composer install --no-interaction --prefer-dist
fi

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# 3. Setup Laravel Identity & Database
echo "--- Running Database Migrations ---"
php artisan key:generate --no-interaction --force
php artisan migrate --force --seed

# 4. Setup Frontend
echo "--- Starting Frontend (Vite)... ---"
cd /var/www/html/frontend
if [ ! -d "node_modules" ]; then
    echo "--- Installing frontend dependencies... ---"
    npm install
fi

# Run Vite in the background
npm run dev -- --host & 

# 5. Starting Backend (PHP + Nginx)
echo "--- Starting PHP-FPM and Nginx... ---"
php-fpm -D
sleep 1
nginx -g "daemon off;"
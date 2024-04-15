#!/bin/bash
set -e

echo "Deployment started..."

# Pull the latest version of the app
git pull
echo "New changes copied to server !"

echo "Installing Dependencies..."
composer install --optimize-autoloader --no-dev

echo "Some Artisan Commands..."

php artisan optimize
php artisan storage:link

php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment Finished!"

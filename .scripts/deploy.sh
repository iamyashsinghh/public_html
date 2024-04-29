#!/bin/bash
set -e

echo "Deployment started..."

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

echo "Deployment Finished!"

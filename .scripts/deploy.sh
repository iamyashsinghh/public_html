#!/bin/bash
set -e

echo "Deployment started..."

# Pull the latest version of the app
git pull
echo "New changes copied to server !"

echo "Installing Dependencies..."
composer i --yes

echo "hello"
pm2 reload app_name/id

echo "Deployment Finished!"

#!/usr/bin/env bash
# Exit on error
set -o errexit

# 1. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 2. Install Node dependencies & Build Frontend (Vue/Inertia)
npm install
npm run build

# 3. Cache Laravel Configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run Migrations (Optional pero reco para automatic)
php artisan migrate --force
php artisan db:seed --force
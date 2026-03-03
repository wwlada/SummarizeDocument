#!/bin/bash

set -e

git pull

docker compose exec laravel.CV npm ci
docker compose exec laravel.CV npm run build

docker compose exec laravel.CV php artisan migrate --force
docker compose exec laravel.CV php artisan optimize
docker compose exec laravel.CV php artisan queue:restart

echo "Deploy completed successfully."

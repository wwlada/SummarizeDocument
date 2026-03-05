#!/bin/bash
set -e

cd /var/www/apps/AiSummaryApp

OLD_COMMIT=$(git rev-parse HEAD)

git pull

NEW_COMMIT=$(git rev-parse HEAD)

if [ "$OLD_COMMIT" != "$NEW_COMMIT" ]; then
  if git diff --name-only "$OLD_COMMIT" "$NEW_COMMIT" | grep -qE 'package.json|package-lock.json|vite.config.js|postcss.config|tailwind.config|resources/(js|css|sass|views)/'; then
    echo "Frontend changes detected. Rebuilding assets..."
    docker compose exec laravel_ai_summary_app npm ci
    docker compose exec laravel_ai_summary_app npm run build
  else
    echo "No frontend changes detected. Skipping asset build."
  fi
else
  echo "Already up to date. Skipping asset build."
fi

docker compose exec laravel_ai_summary_app php artisan migrate --force
docker compose exec laravel_ai_summary_app php artisan optimize:clear
docker compose exec laravel_ai_summary_app php artisan optimize
docker compose exec laravel_ai_summary_app php artisan queue:restart

echo "Deploy ai_summary_app completed successfully."

#!/usr/bin/env sh
set -eu

export APP_IMAGE="${APP_IMAGE:-gitlab-laravel-app:latest}"

if ! docker compose version >/dev/null 2>&1; then
  apk add --no-cache docker-cli-compose
fi

if [ -n "${CI_REGISTRY:-}" ] && [ -n "${CI_REGISTRY_USER:-}" ] && [ -n "${CI_REGISTRY_PASSWORD:-}" ]; then
  docker login -u "${CI_REGISTRY_USER}" -p "${CI_REGISTRY_PASSWORD}" "${CI_REGISTRY}"
fi

docker compose -f docker-compose.yml -f docker-compose.prod.yml pull
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --no-build --remove-orphans --wait
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T app php artisan optimize

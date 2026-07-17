#!/bin/bash
set -euo pipefail

if [ -z "${APP_ENV:-}" ]; then
  APP_ENV=prod
fi

if [ "$APP_ENV" = "prod" ]; then
  echo "Clearing dependency cache (env=$APP_ENV)..."
  if ! php bin/console.php cache:clear:dependencies; then
      echo "Cache clearing failed! Stopping container." >&2
      exit 1
  fi
else
  echo "Dependency cache clear skipped (env=$APP_ENV)"
fi

exec php-fpm

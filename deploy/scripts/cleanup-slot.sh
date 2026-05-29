#!/bin/bash
set -euo pipefail

SLOT="${1:?Usage: cleanup-slot.sh blue|green}"

if [ "$SLOT" != "blue" ] && [ "$SLOT" != "green" ]; then
    echo "--- ERROR: Slot must be 'blue' or 'green', got '$SLOT'"
    exit 1
fi

DEPLOY_DIR="${DEPLOY_DIR:-/opt/mkd-bot}"
ENV_FILE="$DEPLOY_DIR/.env"
COMPOSE_APP_SRC="$DEPLOY_DIR/docker-compose.app.yml"
COMPOSE_APP_RENDERED="$DEPLOY_DIR/docker-compose.app.${SLOT}.yml"
PROJECT_NAME="mkd-bot-${SLOT}"

# Нельзя удалять активный слот — трафик уйдёт в никуда
ACTIVE_SLOT=$(cat "$DEPLOY_DIR/.active-slot" 2>/dev/null || echo "unknown")
if [ "$SLOT" = "$ACTIVE_SLOT" ]; then
    echo "--- ERROR: Cannot cleanup ACTIVE slot '$SLOT'!"
    exit 1
fi

echo "=== Cleaning up $SLOT slot (passive) ==="

# Подставляем переменные шаблона в compose-файл
export SLOT="$SLOT"
export SLOT_ROLE=passive
export CI_REGISTRY_IMAGE="${CI_REGISTRY_IMAGE:-mkd-bot}"
export APP_VERSION="${APP_VERSION:-latest}"
envsubst '$SLOT $SLOT_ROLE $CI_REGISTRY_IMAGE $APP_VERSION $DEPLOY_DIR' < "$COMPOSE_APP_SRC" > "$COMPOSE_APP_RENDERED"

# Останавливаем и удаляем контейнеры слота
if [ -f "$ENV_FILE" ]; then
    docker compose --env-file "$ENV_FILE" -p "$PROJECT_NAME" -f "$COMPOSE_APP_RENDERED" down
else
    docker compose -p "$PROJECT_NAME" -f "$COMPOSE_APP_RENDERED" down
fi

# Удаляем неиспользуемые образы проекта
docker image prune -f --filter "label=project=mkd-bot"

echo "+++ $SLOT slot cleaned up"

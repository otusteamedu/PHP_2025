#!/bin/bash
set -euo pipefail

DEPLOY_DIR="${DEPLOY_DIR:-/opt/mkd-bot}"
STATE_FILE="$DEPLOY_DIR/.active-slot"

# 1. State-файл — самый быстрый и надёжный способ
if [ -f "$STATE_FILE" ]; then
    cat "$STATE_FILE"
    exit 0
fi

# 2. Конфиг switch-nginx — если state-файл потерян
CONF_FILE="$DEPLOY_DIR/deploy/switch-nginx/default.conf"
if [ -f "$CONF_FILE" ]; then
    SLOT=$(grep -oP '(blue|green)-webserver' "$CONF_FILE" | head -1)
    if [ -n "$SLOT" ]; then
        echo "$SLOT"
        exit 0
    fi
fi

# 3. По запущенным контейнерам — проверяем SLOT_ROLE, чтобы не спутать с passive
if docker ps --format '{{.Names}}' | grep -q '^blue-app$'; then
    ROLE=$(docker exec blue-app printenv SLOT_ROLE 2>/dev/null || true)
    if [ "$ROLE" = "active" ]; then
        echo "blue"
        exit 0
    fi
fi

if docker ps --format '{{.Names}}' | grep -q '^green-app$'; then
    ROLE=$(docker exec green-app printenv SLOT_ROLE 2>/dev/null || true)
    if [ "$ROLE" = "active" ]; then
        echo "green"
        exit 0
    fi
fi

echo "ERROR: Cannot determine active slot" >&2
exit 1

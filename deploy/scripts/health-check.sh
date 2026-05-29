#!/bin/bash
set -euo pipefail

SLOT="${1:?Usage: health-check.sh blue|green}"

if [ "$SLOT" != "blue" ] && [ "$SLOT" != "green" ]; then
    echo "--- ERROR: Slot must be 'blue' or 'green', got '$SLOT'"
    exit 1
fi

MAX_RETRIES=${2:-10}
RETRY_INTERVAL=${3:-3}

echo "Health-checking $SLOT slot..."

for i in $(seq 1 $MAX_RETRIES); do
    # Запрос изнутри Docker-сети: app -> webserver по имени сервиса
    HTTP_CODE=$(docker exec ${SLOT}-app curl -s -o /dev/null -w "%{http_code}" --max-time 5 "http://${SLOT}-webserver/ready" 2>/dev/null || echo "000")

    if [ "$HTTP_CODE" = "200" ]; then
        # Проверяем тело ответа, чтобы убедиться в реальной готовности
        BODY=$(docker exec ${SLOT}-app curl -s --max-time 5 "http://${SLOT}-webserver/ready" 2>/dev/null || echo "")

        if echo "$BODY" | grep -q '"status":"ok"'; then
            echo "+++ $SLOT slot is HEALTHY (attempt $i/$MAX_RETRIES)"
            exit 0
        fi
    fi

    echo "!!! Attempt $i/$MAX_RETRIES: $SLOT slot returned HTTP $HTTP_CODE, retrying in ${RETRY_INTERVAL}s..."
    sleep $RETRY_INTERVAL
done

echo "--- $SLOT slot is UNHEALTHY after $MAX_RETRIES attempts"
exit 1

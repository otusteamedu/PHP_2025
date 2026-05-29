#!/bin/bash
set -euo pipefail

DEPLOY_DIR="${DEPLOY_DIR:-/opt/mkd-bot}"
ACTIVE_SLOT=$(cat "$DEPLOY_DIR/.active-slot" 2>/dev/null || echo "unknown")

if [ "$ACTIVE_SLOT" = "blue" ]; then
    ROLLBACK_SLOT="green"
elif [ "$ACTIVE_SLOT" = "green" ]; then
    ROLLBACK_SLOT="blue"
else
    echo "--- ERROR: Cannot determine active slot for rollback"
    exit 1
fi

echo "=== EMERGENCY ROLLBACK: $ACTIVE_SLOT -> $ROLLBACK_SLOT ==="

# Старый слот должен быть жив, чтобы на него можно было откатиться
if ! docker ps --format '{{.Names}}' | grep -q "^${ROLLBACK_SLOT}-app$"; then
    echo "--- ERROR: $ROLLBACK_SLOT slot is not running! Cannot rollback."
    exit 1
fi

# Проверяем здоровье неактивного слота перед переключением
if ! bash "$DEPLOY_DIR/deploy/scripts/health-check.sh" "$ROLLBACK_SLOT" 3 3; then
    echo "WARNING: Inactive slot ${ROLLBACK_SLOT} failed health check!"
    echo "Rolling back to an unhealthy slot may cause service disruption."

    if [ -t 0 ]; then
        # Интерактивный режим — спрашиваем оператора
        read -p "Continue rollback? [y/N] " CONFIRM
        if [ "$CONFIRM" != "y" ] && [ "$CONFIRM" != "Y" ]; then
            echo "Rollback cancelled."
            exit 1
        fi
    else
        # CI/CD — автоматически прерываем, чтобы не сломать прод ещё сильнее
        echo "--- Non-interactive terminal: rollback aborted due to failed health check."
        exit 1
    fi
fi

# Переключаем трафик на старый слот
bash "$DEPLOY_DIR/deploy/scripts/switch-slot.sh" "$ROLLBACK_SLOT"

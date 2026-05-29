#!/bin/bash
set -e

# Права на логи — www-data должен иметь доступ на запись
for dir in /data/logs /var/log/app; do
    if [ -d "$dir" ]; then
        chown -R www-data:www-data "$dir"
        chmod -R 775 "$dir"
        # Исправляем права файлов, созданных от root
        find "$dir" -type f -exec chown www-data:www-data {} \; 2>/dev/null || true
        find "$dir" -type f -exec chmod 664 {} \; 2>/dev/null || true
    fi
done

# Управление режимом Telegram:
# - longpoll — consumer-telegram-longpoll включён через supervisor
# - webhook (или не задан) — consumer-telegram-longpoll отключён
# consumer-telegram-forward (дублирование Max->Telegram) работает всегда
LONGPOLL_CONF="/etc/supervisor/conf.d/consumer-telegram-longpoll.conf"
LONGPOLL_DISABLED_CONF="/etc/supervisor/conf.d.disabled/consumer-telegram-longpoll.conf"
TELEGRAM_MODE="${TELEGRAM_MODE:-longpoll}"

if [ "$TELEGRAM_MODE" = "longpoll" ]; then
    echo "TELEGRAM_MODE=longpoll — consumer-telegram-longpoll включён"
    # Восстановить из disabled, если был перемещён ранее
    if [ -f "$LONGPOLL_DISABLED_CONF" ]; then
        mv "$LONGPOLL_DISABLED_CONF" "$LONGPOLL_CONF"
        echo "Восстановлен: consumer-telegram-longpoll.conf ← conf.d.disabled/"
    fi
else
    echo "TELEGRAM_MODE=${TELEGRAM_MODE:-webhook} — consumer-telegram-longpoll отключён"
    if [ -f "$LONGPOLL_CONF" ]; then
        mkdir -p /etc/supervisor/conf.d.disabled
        mv "$LONGPOLL_CONF" "$LONGPOLL_DISABLED_CONF"
        echo "Отключён: consumer-telegram-longpoll.conf -> conf.d.disabled/"
    fi
fi

# Управление консьюмерами и cron по SLOT_ROLE:
# passive (или не задан) — только PHP-FPM (HTTP + health-check), консьюмеры и cron отключены
# active — все консьюмеры и cron работают
#
# Конфиги перемещаются в backup-директорию вместо удаления,
# чтобы корректно восстанавливаться при docker restart / --force-recreate
SLOT_ROLE="${SLOT_ROLE:-passive}"
SUPERVISOR_CONF_DIR="/etc/supervisor/conf.d"
SUPERVISOR_DISABLED_DIR="/etc/supervisor/conf.d.disabled"
CONSUMER_CONFS="consumer-fallback.conf consumer-news-delivery.conf consumer-rag-query.conf consumer-telegram-forward.conf consumer-telegram-longpoll.conf deliver-news-cron.conf"

mkdir -p "$SUPERVISOR_DISABLED_DIR"

if [ "$SLOT_ROLE" = "passive" ]; then
    echo "SLOT_ROLE=passive - консьюмеры и cron отключены, работает только PHP-FPM"
    for conf in $CONSUMER_CONFS; do
        if [ -f "$SUPERVISOR_CONF_DIR/$conf" ]; then
            mv "$SUPERVISOR_CONF_DIR/$conf" "$SUPERVISOR_DISABLED_DIR/$conf"
            echo "Отключён: $conf -> $SUPERVISOR_DISABLED_DIR/$conf"
        fi
    done
else
    echo "SLOT_ROLE=active - консьюмеры и cron включены"
    for conf in $CONSUMER_CONFS; do
        if [ -f "$SUPERVISOR_DISABLED_DIR/$conf" ]; then
            mv "$SUPERVISOR_DISABLED_DIR/$conf" "$SUPERVISOR_CONF_DIR/$conf"
            echo "Восстановлен: $conf <-- $SUPERVISOR_DISABLED_DIR/$conf"
        fi
    done
fi

exec "$@"

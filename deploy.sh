#!/bin/bash
set -e

# Настройки
SERVER="root@192.168.0.1"
SERVER_IP="192.168.0.1"
APP_DIR="/var/www/myapp"
REPO="https://github.com/gnvs/otus_test_repos.git"
LOG_FILE="$APP_DIR/deploy.log"

# Новый релиз
RELEASE_DIR="$APP_DIR/releases/$(date +%Y%m%d_%H%M%S)"

echo "Старт..."

# Проверка подключения к серверу
echo "Проверяем подключение к серверу..."
if ! ssh $SERVER "echo ok" &>/dev/null; then
    echo "Ошибка: сервер $SERVER недоступен"
    exit 1
fi
echo "Подключение успешно установлено"

# Один SSH-коннект, внутри которого всё выполняется
ssh $SERVER bash << EOF
    set -e
    
    # Подготовка
    mkdir -p $APP_DIR/releases
    mkdir -p $APP_DIR/shared
    
    # Клонируем код
    echo "Клонируем код из репозитория..."
    git clone $REPO $RELEASE_DIR
    
    # Проверяем на composer.json и устанавливаем зависимости composer
    if [ -f "$RELEASE_DIR/composer.json" ]; then
        echo "Устанавливаем зависимости Composer..."
        cd $RELEASE_DIR && composer install --no-dev --optimize-autoloader
    fi
    
    # Переключаем симлинк
    echo "Переключаем симлинк..."
    ln -snf $RELEASE_DIR $APP_DIR/current
    
    # Перезапускаем PHP-FPM
    echo "Перезапускаем PHP-FPM..."
    systemctl reload php8.1-fpm || systemctl reload php8.3-fpm || true
    
    # Очищаем старые релизы (оставляем последние 5)
    echo "Очищаем старые релизы..."
    cd $APP_DIR/releases && ls -1 | sort -r | tail -n +6 | xargs rm -rf 2>/dev/null || true
    
    # Логирование
    echo "\$(date): Деплой $RELEASE_DIR завершён" >> $LOG_FILE
    
    echo "Деплой завершён!"
EOF

echo "Приложение доступно: http://$SERVER_IP"

# Инфо:
# - Проверка подключения к серверу перед деплоем
# - Копируем код с GitHub на сервер
# - Переключаем симлинк, чтобы сайт обновлялся без остановки
# - Перезапуск PHP-FPM для применения изменений
# - Очистка старых релизов (храним последние 5)
# - Логирование всех деплоев в файл
#
# Запуск:
# chmod +x deploy.sh
# ./deploy.sh
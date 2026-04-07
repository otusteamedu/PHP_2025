#!/bin/bash
set -e

# Настройки
SERVER="root@192.168.0.1"
SERVER_IP="192.168.0.1"
APP_DIR="/var/www/myapp"
REPO="https://github.com/gnvs/otus_test_repos.git"

# Новый релиз
RELEASE_DIR="$APP_DIR/releases/$(date +%Y%m%d_%H%M%S)"

echo "Старт..."

# Один SSH-коннект, внутри которого всё выполняется
ssh $SERVER bash << EOF
    set -e
    
    # Подготовка
    mkdir -p $APP_DIR/releases
    mkdir -p $APP_DIR/shared
    
    # Клонируем код
    echo "Клонируем код из репозитория..."
    git clone $REPO $RELEASE_DIR
    
    # Проверяем на composer.json и устанавливаем зависимости сomposer
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
    
    echo "Деплой завершён!"
EOF

echo "Приложение доступно: http://$SERVER_IP"

# Инфо:
# Копируем код с GitHub на сервер
# Переключаем симлинк, чтобы сайт обновлялся без остановки
# Чистит старые релизы
# 
# Запуск:
# chmod +x deploy.sh
# ./deploy.sh

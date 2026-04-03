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

# Подготовка
ssh $SERVER "mkdir -p $APP_DIR/releases"
ssh $SERVER "mkdir -p $APP_DIR/shared"

# Клонируем код
echo "Клонируем код из репозитория..."
ssh $SERVER "git clone $REPO $RELEASE_DIR"

# Переключаем симлинк
echo "Переключаем..."
ssh $SERVER "ln -snf $RELEASE_DIR $APP_DIR/current"

# Очищаем старые релизы
echo "Очищаем старые релизы..."
ssh $SERVER "cd $APP_DIR/releases && ls -1 | sort -r | tail -n +6 | xargs rm -rf 2>/dev/null || true"

echo "Деплой завершён!"
echo "Приложение доступно: http://$SERVER_IP"

# Инфо:
# Копируем код с GitHub на сервер
# Переключаем симлинк, чтобы сайт обновлялся без остановки
# Чистит старые релизы
# 
# Запуск:
# chmod +x deploy.sh
# ./deploy.sh

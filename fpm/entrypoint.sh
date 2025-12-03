#!/bin/sh
set -e

export COMPOSER_ALLOW_SUPERUSER=1

APP_DIR="/data/mysite.local"
COMPOSER_JSON="$APP_DIR/composer.json"
VENDOR_DIR="$APP_DIR/vendor"

if [ ! -f "$COMPOSER_JSON" ]; then
  echo "composer.json не найден — пропускаю установку зависимостей."
  exec php-fpm
fi

echo "composer.json найден в $APP_DIR"

# Если vendor отсутствует — обычная установка
if [ ! -d "$VENDOR_DIR" ]; then
  echo "vendor не найден — выполняю composer install..."
  cd "$APP_DIR"
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  # vendor есть: проверяем, не новее ли composer.json
  JSON_MTIME=$(stat -c %Y "$COMPOSER_JSON" 2>/dev/null || stat -f %m "$COMPOSER_JSON")
  # Берём любой файл из vendor как эталон времени (composer/autoload_classmap.php чаще всего есть)
  BASE_VENDOR_FILE="$VENDOR_DIR/autoload.php"
  [ ! -f "$BASE_VENDOR_FILE" ] && BASE_VENDOR_FILE="$VENDOR_DIR/composer/autoload_classmap.php"

  if [ -f "$BASE_VENDOR_FILE" ]; then
    VENDOR_MTIME=$(stat -c %Y "$BASE_VENDOR_FILE" 2>/dev/null || stat -f %m "$BASE_VENDOR_FILE")
  else
    # если эталон не найден, считаем что нужно обновить автозагрузку
    VENDOR_MTIME=0
  fi

  if [ "$JSON_MTIME" -gt "$VENDOR_MTIME" ]; then
    echo "composer.json новее чем vendor — выполняю composer dump-autoload -o..."
    cd "$APP_DIR"
    composer dump-autoload -o
  else
    echo "vendor актуален — пропускаю dump-autoload."
  fi
fi

exec php-fpm
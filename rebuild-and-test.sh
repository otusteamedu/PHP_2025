#!/bin/bash

echo "=== Полная пересборка и тестирование ==="

# 1. Останавливаем всё
echo "1. Остановка контейнеров..."
docker-compose down

# 2. Проверяем и генерируем SSL
echo "2. Проверка SSL сертификатов..."
if [ ! -f "ssl/nginx-selfsigned.crt" ] || [ ! -f "ssl/nginx-selfsigned.key" ]; then
    echo "Генерация SSL сертификатов..."
    mkdir -p ssl
    cd ssl
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout nginx-selfsigned.key \
        -out nginx-selfsigned.crt \
        -subj "/C=RU/ST=Moscow/L=Moscow/O=Company/CN=localhost" \
        -addext "subjectAltName=DNS:localhost,DNS:127.0.0.1,IP:127.0.0.1"
    cd ..
    echo "SSL сертификаты созданы"
else
    echo "SSL сертификаты уже существуют"
fi

# 3. Проверяем права
echo "3. Установка прав на SSL файлы..."
chmod 644 ssl/nginx-selfsigned.crt
chmod 600 ssl/nginx-selfsigned.key

# 4. Проверяем конфигурацию Nginx
echo "4. Проверка конфигурации Nginx..."
docker run --rm -v $(pwd)/docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf:ro \
    -v $(pwd)/ssl:/etc/nginx/ssl:ro nginx:alpine nginx -t

# 5. Сборка
echo "5. Сборка образов..."
docker-compose build

# 6. Запуск
echo "6. Запуск контейнеров..."
docker-compose up -d

# 7. Ожидание запуска
echo "7. Ожидание запуска сервисов..."
sleep 5

# 8. Проверка
echo "8. Проверка контейнеров..."
docker-compose ps

# 9. Тестирование
echo "9. Тестирование подключения..."
echo "=== Тест HTTP (порт 80) ==="
curl -s http://localhost || echo "HTTP test failed"

echo -e "\n=== Тест HTTPS (порт 443) ==="
curl -k -s https://localhost:443 || echo "HTTPS test failed"

echo -e "\n=== Тест SSL-информации ==="
curl -k -s https://localhost:443/ssl-test.php | jq . 2>/dev/null || \
    curl -k -s https://localhost:443/ssl-test.php

# 10. Логи
echo -e "\n=== Последние логи Nginx ==="
docker-compose logs nginx --tail=20

echo -e "\n=== Проверка SSL файлов в контейнере ==="
docker-compose exec nginx ls -la /etc/nginx/ssl/

echo -e "\n=== Готово! ==="
echo "URL для тестирования:"
echo "  HTTP:  http://localhost"
echo "  HTTPS: https://localhost:443"
echo "  SSL тест: https://localhost:443/ssl-test.php"
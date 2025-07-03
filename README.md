# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Домашнее задание по лекции 30 Проектирование API

Реализовано приложение на SlimPHP. API очередь сделана через Raddis, статус сохраняется в sqlite. 

Инструкции по запуску

# 1. Запуск Docker
cd docker/
docker-compose up -d --build

# 2. Инициализация базы (один раз из корня проекта)
вместо `docker_app_name` подставить имя контейнера, c которым запустилось приложение
`docker exec -it docker_app_name php init_db.php`
API запускается самостоятельно, так как в контейнере уже запущен PHP с сервером на порт 8080

# 3. Запуск воркера для приема API запросов
docker exec -it docker-app-1 php worker.php

# 4. Документация API
Файл: `swagger/openapi.yaml` — совместим с Swagger UI.
Так же по `http://localhost:8081` поднимается Swagger web UI

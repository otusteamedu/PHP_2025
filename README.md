# 🧹 Домашнее задание №5 — Валидация скобок и балансировка нагрузки

## 📦 Описание проекта

Учебный проект для отработки практических навыков с Docker, PHP-FPM, Nginx и Redis.
Приложение принимает строку из скобок через POST-запрос и определяет, корректна ли она с точки зрения вложенности.
Сервис построен с балансировкой нагрузки, многими PHP-FPM контейнерами и общим Redis-хранилищем сессий.

## 🧱 Стек технологий

* PHP 8.x (FPM)
* Nginx
* Docker / Docker Compose
* Redis
* Composer (autoload)

## 📁 Структура проекта

```
project-root/
├── docker/
│   ├── nginx/                 # Конфигурация nginx-балансировщика
│   │   └── nginx.conf
│   ├── php/                   # Dockerfile и конфиги PHP-FPM
│   │   └── Dockerfile
│   ├── app1/                  # Первый php+nginx бекенд
│   │   ├── nginx.conf
│   │   └── Dockerfile
│   ├── app2/                  # Второй php+nginx бекенд
│   │   ├── nginx.conf
│   │   └── Dockerfile
│   └── redis/                 # (опционально, если будешь использовать собственный конфиг)
├── public/                    # Корневая директория, расшариваемая для PHP и Nginx
│   └── index.php              # Обработка POST-запроса и валидация строки
├── src/                       # Дополнительная логика, классы, автозагрузка
│   └── Validator.php
├── .gitignore
├── composer.json
├── docker-compose.yml         # Собирает все сервисы: балансировщик, php-бэкенды, redis
└── README.md
```

## 🚀 Как запустить (будет дополнено)

```bash
# Запуск контейнеров
docker compose up -d

# Остановка контейнеров
docker compose down
```

## 🧪 Примеры запросов

```bash
# Пример запроса с корректной строкой
curl -X POST http://localhost/ -d "string=(()())"

# Пример запроса с некорректной строкой
curl -X POST http://localhost/ -d "string=)(()"
```

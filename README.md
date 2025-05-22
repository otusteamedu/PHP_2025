# 🧹 Домашнее задание №5 — Валидация скобок и балансировка нагрузки

## 📦 Описание проекта

Учебный проект для отработки практических навыков в работе с Docker, PHP-FPM, Nginx, Redis и фронтендом на Vue.js. Основная задача — реализовать веб-сервис, принимающий строку из скобок через POST-запрос, проверяющий её корректность с точки зрения вложенности и симметрии, а также организовать балансировку нагрузки между несколькими backend-контейнерами.

Проект построен на микросервисной архитектуре с разделением на:

* балансировщик (nginx upstream)
* два backend-а (nginx + php-fpm)
* redis-хранилище для сессий
* фронтенд-приложение (Vue + Vite)

## 🧱 Стек технологий

* PHP 8.3 (FPM)
* Nginx (статичный сервер и балансировщик)
* Redis
* Docker / Docker Compose
* Composer (PSR-4 autoload)
* Vue 3 + Vite

## 📁 Структура проекта

```
otus-php-hw04/
├── balancer/                      # Конфигурация балансировщика
│   └── nginx.conf
├── docker/                        # Dockerfile'ы и конфиги
│   ├── balancer/
│   │   └── balancer.Dockerfile
│   ├── nginx/
│   │   ├── nginx.Dockerfile
│   │   └── conf.d/
│   │       └── default.conf
│   ├── php/
│       ├── php.Dockerfile
│       ├── php.ini
│       ├── conf.d/
│       │   └── session.redis.ini
│       ├── www-fpm1.conf
│       └── www-fpm2.conf
├── frontend/                      # Vue frontend
│   ├── src/
│   │   ├── assets/
│   │   └── utils/bracketGenerator.js
│   ├── App.vue
│   ├── main.js
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
├── nginx/                         # Статичные nginx-конфиги backend
│   ├── nginx.conf
│   └── conf.d/
│       └── default.conf
├── public/                        # Веб-доступный PHP
│   └── index.php
├── src/                           # PHP-классы (автозагрузка)
│   └── Validator.php
├── vendor/                        # Composer зависимости
├── .gitignore
├── composer.json
├── docker-compose.yml
├── docker-compose.dev.yml
├── docker-compose.prod.yml
├── Makefile
└── README.md
```

## ⚙️ Как запустить (dev-режим)

```bash
# Сборка и запуск всех сервисов
make dev-build

# Остановка
make stop
```

Альтернативно напрямую:

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up --build -d
```

## 📨 Пример запроса (CLI)

```bash
# Корректная строка
curl -X POST http://localhost/api/validate -d "string=(()())"

# Некорректная строка
curl -X POST http://localhost/api/validate -d "string=)(()"
```

## 🔍 Валидация строки

Валидация реализована в `src/Validator.php` и учитывает:

* пустые строки (ошибка)
* вложенность и правильную последовательность `()`
* запрет любых символов кроме `(` и `)`

### Пример кода:

```php
Validator::validate('(()())'); // true
Validator::validate('((())');  // false
Validator::validate(')(');     // false
```

## 🧠 Важные файлы и их назначение

### PHP:

* `php.ini` — базовая настройка окружения
* `session.redis.ini` — настройка хранения сессий в Redis
* `www-fpm1.conf`, `www-fpm2.conf` — конфиги php-fpm для разных инстансов

### Nginx-балансировщик:

```nginx
upstream backend_upstream {
    server nginx-backend1:80;
    server nginx-backend2:80;
}

location /api/ {
    proxy_pass http://backend_upstream;
    ...
}
```

### Vue frontend (упрощённый запуск)

```bash
cd frontend
npm install
npm run dev
```

## 🧪 Генерация скобочных строк (frontend)

Фронтенд использует генератор строк (валидных/невалидных) из `bracketGenerator.js` и визуализирует результат проверки через REST API.

---

## ✅ Выполненные требования задания:

* [x] Docker-контейнеры: nginx, php-fpm, redis ✅
* [x] POST-запрос `/api/validate` ✅
* [x] Корректная валидация вложенных скобок ✅
* [x] Балансировка между backend-ами через nginx upstream ✅
* [x] Redis-сессии ✅
* [x] Разделение dev и prod окружений ✅
* [x] Фронтенд-интерфейс на Vue ✅

---

## 📮 Автор

**Vladimir Matkovskii** — [vlavlamat@icloud.com](mailto:vlavlamat@icloud.com)
